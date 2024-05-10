<?php

namespace App\Http\Controllers;

use App\Account;
use DB;
use App\Banks;
use App\PostDatedCheque;
use App\PostDatedChequeLine;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PostDateChequeController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $commonQuery = function($query) {
            return $query->leftJoin('post_dated_cheque_lines', 'post_dated_cheque_lines.post_dated_cheque_id', '=', 'post_dated_cheques.id')
                ->join('banks', 'banks.id', '=', 'post_dated_cheque_lines.bank_id')
                ->join('accounts', 'accounts.id', '=', 'post_dated_cheque_lines.account_id')
                ->select(
                    'post_dated_cheques.id',
                    'post_dated_cheque_lines.id as line_id',
                    'ref_no',
                    'post_dated_cheques.date',
                    'banks.name as bank',
                    'accounts.name as account',
                    'post_dated_cheque_lines.status',
                    'post_dated_cheque_lines.cheque_no',
                    'post_dated_cheque_lines.cheque_date',
                    'post_dated_cheque_lines.amount',
                    'post_dated_cheques.remarks'
                );
        };
        $cheque_payment = $commonQuery(PostDatedCheque::where('type', 'payment'))->get();
        $cheque_received = $commonQuery(PostDatedCheque::where('type', 'received'))->get();
        return view('post_date_cheque.index')->with(compact('cheque_received','cheque_payment'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $ref_no = PostDatedCheque::select('ref_no', 
                    DB::raw("substring_index(substring_index(ref_no,'-',-1),',',-1) as max_no"))->get()
                    ->max('max_no');
        if(empty($ref_no)){
            $ref_no_ = 1;
        }else{
            $break_no = explode("-",$ref_no);
            $ref_no_ = end($break_no)+1;
        }
        $ref_no_ = 'CH-'.$ref_no_; 
        $banks = Banks::whereNull('deleted_at')->get();
        $accounts = Account::whereNull('deleted_at')->where('is_closed', 0)->get();
        return view('post_date_cheque.create')->with(compact('banks','accounts', 'ref_no_'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            $input = $request->only(['ref_no','date','type','remarks','final_total']);

            $post_dated = PostDatedCheque::create($input);
            $this->createOrUpdateCheques($post_dated->id, $request->cheque);
            DB::commit();
            $output = ['success' => true,
                            'msg' => 'cheque added Successfully'
                        ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                        ];
        }

        return redirect()->action('PostDateChequeController@index')->with('status', $output);
    }


    public function createOrUpdateCheques($post_date_id, $cheques)
    {
        // Delete existing records related to the doctor_sale_id
        PostDatedChequeLine::where('post_dated_cheque_id', $post_date_id)->delete();
    
        // Prepare data for bulk insertion
        $bulkInsertData = [];
    
        foreach ($cheques as $data) {
            if (empty($data['bank_id'])) {
                continue;
            }

            $PostDatedChequeLine = [
                'post_dated_cheque_id' => $post_date_id,
                'bank_id'       => $data['bank_id'],
                'account_id'    => $data['account_id'],
                'cheque_no'     => $data['cheque_no'],
                'amount'        => $data['amount'] ?? null,
                'cheque_date'   => $data['date'] ?? null,
            ];

            $bulkInsertData[] = $PostDatedChequeLine;
        }
    
        // Bulk insert the data
        PostDatedChequeLine::insert($bulkInsertData);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post_date_cheque = PostDatedCheque::findOrFail($id);
        $post_date_cheque_lines = PostDatedChequeLine::where('post_dated_cheque_id',$id)->with(['bank','account'])->get();
        $banks = Banks::whereNull('deleted_at')->get();
        $accounts = Account::whereNull('deleted_at')->where('is_closed', 0)->get();
        return view('post_date_cheque.show')->with(compact('post_date_cheque','post_date_cheque_lines','banks','accounts'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post_date_cheque = PostDatedCheque::findOrFail($id);
        $post_date_cheque_lines = PostDatedChequeLine::where('post_dated_cheque_id',$id)->get();
        $banks = Banks::whereNull('deleted_at')->get();
        $accounts = Account::whereNull('deleted_at')->where('is_closed', 0)->get();
        return view('post_date_cheque.edit')->with(compact('post_date_cheque','post_date_cheque_lines','banks','accounts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            $input = $request->only(['ref_no','date','type','remarks','final_total']);

            $post_dated = PostDatedCheque::find($id)->update($input);
            $this->createOrUpdateCheques($id, $request->cheque);
            DB::commit();
            $output = ['success' => true,
                            'msg' => 'cheque updated Successfully'
                        ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                        ];
        }

        return redirect()->action('PostDateChequeController@index')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            // PostDatedCheque::where('id', $id)->delete();
            PostDatedChequeLine::where('id', $id)->delete();
            DB::commit();
            $output = ['success' => true, 'msg' => 'Cheque deleted Successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }
        return redirect()->action('PostDateChequeController@index')->with('status', $output);
    }

}
