<?php

namespace App\Http\Controllers;

use DB;
use App\Banks;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BankController extends Controller
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

        if (request()->ajax()) {
            $banks = Banks::All();
            return Datatables::of($banks)
                ->addColumn(
                    'action',
                    '@can("Bank.update")
                    <button data-href="{{action(\'BankController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_bank_button btn-edt"><i class="glyphicon glyphicon-edit"></i></button>
                        &nbsp;
                    @endcan
                    @can("Bank.delete")
                        <button data-href="{{action(\'BankController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_bank_button btn-dlt"><i class="glyphicon glyphicon-trash"></i></button>
                    @endcan'
                )
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('bank.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $banks = Banks::All();
        return view('bank.create')->with(compact('banks'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $input = $request->only(['name']);
            $input['created_by'] = $request->session()->get('user.id');

            $bank = Banks::create($input);
            DB::commit();
            $output = ['success' => true,
                            'data' => $bank,
                            'msg' => __("bank.added_success")
                        ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $bank = Banks::findOrFail($id);
        return view('bank.edit')->with(compact('bank'));
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
        if (request()->ajax()) {
            try {
                DB::beginTransaction();
                $input = $request->only(['name']);

                $bank = Banks::findOrFail($id);
                $bank->name = $input['name'];

                $bank->save();
                DB::commit();
                $output = ['success' => true,
                            'msg' => 'Bank Updated Successfully'
                            ];
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (request()->ajax()) {
            try {
                DB::beginTransaction();
                Banks::where('id', $id)->delete();
                DB::commit();
                $output = ['success' => true, 'msg' => 'Bank deleted Successfully'];
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
            }
            return $output;
        }
    }

}
