<?php

namespace App\Http\Controllers;

use App\Account;
use App\AccountTransaction;
use App\AccountType;
use App\PostDatedChequeLine;
use App\PostDatedCheque;
use App\TaxRate;
use App\Invoice_setting;
use App\Business;
use App\Contact;
use App\Type;
use App\Receipt;
use App\Receipt_detail;
use App\Transaction;
use Spatie\Permission\Models\Role;
use App\TransactionPayment;
use App\Utils\Util;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\Facades\DataTables;
use App\Media;
use App\BusinessLocation;
use App\Utils\ModuleUtil;

class AccountController extends Controller
{
    protected $commonUtil;
    protected $moduleUtil;

    protected $debit_amount  = 0;
    protected $credit_amount = 0;
    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {

        $is_admin = $this->moduleUtil->is_admin(auth()->user());

        $transaction_acc = DB::table('assign_coa_role')->where('role_id', auth()->user()->roles[0]->id)->get()->toArray();
        $transactionIds = array_map(function($item) {
            return $item->transaction_acc_id;
        }, $transaction_acc);
        
        
        $business_id = session()->get('user.business_id');
        if (request()->ajax()) {
            $accounts = Account::leftjoin('account_transactions as AT', 'AT.account_id', '=', 'accounts.id')
            ->leftjoin(
                'account_types as ats',
                'accounts.account_type_id',
                '=',
                'ats.id'
            )
            ->leftjoin(
                'account_types as pat',
                'ats.parent_account_type_id',
                '=',
                'pat.id'
            )
            ->leftJoin('users AS u', 'accounts.created_by', '=', 'u.id')
                                ->where('accounts.business_id', $business_id)
                                ->select(['accounts.name', 'accounts.account_number', 'accounts.note', 'accounts.id', 'accounts.account_type_id',
                                    'ats.name as account_type_name',
                                    'pat.name as parent_account_type_name',
                                    'accounts.account_details',
                                    'accounts.is_closed', 
                                    'accounts.contact_id', 
                                    'accounts.user_id', 
                                        // DB::raw("SUM( IF(AT.type='credit', amount, -1*amount) ) as balance"),
                                        'AT.type',
                                        DB::raw('SUM(IF(AT.type = "debit", AT.amount, 0)) - SUM(IF(AT.type = "credit", AT.amount, 0)) AS balance'),
                                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by")
                                    ]);

            //check account permissions basaed on location
            $permitted_locations = auth()->user()->permitted_locations();
            $account_ids = [];
            if ($permitted_locations != 'all') {

                $locations = BusinessLocation::where('business_id', $business_id)
                                ->whereIn('id', $permitted_locations)
                                ->get();

                foreach ($locations as $location) {
                    if (!empty($location->default_payment_accounts)) {
                        $default_payment_accounts = json_decode($location->default_payment_accounts, true);
                        foreach ($default_payment_accounts as $key => $account) {
                            if (!empty($account['is_enabled']) && !empty($account['account'])) {
                                $account_ids[] = $account['account'];
                            }
                        }
                    }
                }

                $account_ids = array_unique($account_ids);
            }

            if (!$this->moduleUtil->is_admin(auth()->user(), $business_id) && $permitted_locations != 'all') {
                $accounts->whereIn('accounts.id', $account_ids);
            }
            
            if(!auth()->user()->can('account.access_all')){
                if (!$this->moduleUtil->is_admin(auth()->user(), $business_id)) {
                    $accounts->whereNotIn('accounts.id',$transactionIds);
                }
            }
            
            
            $is_closed = request()->input('account_status') == 'closed' ? 1 : 0;
            $accounts->where('accounts.is_closed', $is_closed)
                ->whereNull('AT.deleted_at')
                ->groupBy('accounts.id');

            return DataTables::of($accounts)
                            ->addColumn('checkbox', '<input type="checkbox" value="{{$id}}" data-url="{{action(\'AccountController@close\',[$id])}}" class="coa_check">')
                            // ->addColumn(
                            //     'action',
                            //     '<button data-href="{{action(\'AccountController@edit\',[$id])}}" data-container=".account_model" class="btn btn-xs btn-primary btn-modal btn-edt"><i class="glyphicon glyphicon-edit"></i></button>
                            //     <a href="{{action(\'AccountController@show\',[$id])}}" class="btn btn-warning btn-xs acc_book btn-vew" data-id="{{ $id }}" target="_blank"><i class="fa fa-book"></i></a>&nbsp;
                                
                            //     <a href="{{action(\'AccountController@detail_show\',[$id])}}" class="btn btn-info btn-xs btn-vew" target="_blank"><i class="fa fa-book"></i></a>&nbsp;
                                
                            //     @if($is_closed == 0)
                            //     <button data-href="{{action(\'AccountController@getFundTransfer\',[$id])}}" class="btn btn-xs btn-info btn-modal hide" data-container=".view_modal"><i class="fa fa-exchange"></i> @lang("account.fund_transfer")</button>

                            //     <button data-href="{{action(\'AccountController@getDeposit\',[$id])}}" class="btn btn-xs btn-success btn-modal hide" data-container=".view_modal"><i class="fas fa-money-bill-alt"></i> @lang("account.deposit")</button>

                            //     <button data-url="{{action(\'AccountController@close\',[$id])}}" class="btn btn-xs btn-danger hide close_account"><i class="fa fa-power-off"></i> @lang("messages.close")</button>
                            //     @elseif($is_closed == 1)
                            //         <button data-url="{{action(\'AccountController@activate\',[$id])}}" class="btn hide btn-xs btn-success activate_account"><i class="fa fa-power-off"></i> @lang("messages.activate")</button>
                            //     @endif'
                            // )
                            ->addColumn('action', function ($row) use($is_admin) {
                                 $actionHtml = '';
                                if(auth()->user()->can('accounts.delete')){
                                    
                                if ($row->user_id == null && $row->contact_id == null) {
                                    $actionHtml .= '<button data-url="' . action('AccountController@destroy', [$row->id]) . '" class="btn btn-xs btn-danger delete_account btn-vew"><i class="fa fa-trash"></i></button>';
                                }
                                
                                }
                                if(auth()->user()->can('accounts.edit')){
                                
                                $actionHtml .= '<button data-href="' . action('AccountController@edit', [$row->id]) . '" data-container=".account_model" class="btn btn-xs btn-primary btn-modal btn-edt"><i class="glyphicon glyphicon-edit"></i></button>';
                                
                                }
                                $actionHtml .= '<a href="' . action('AccountController@show', [$row->id]) . '" class="btn btn-warning btn-xs acc_book btn-vew" data-id="' . $row->id . '" target="_blank"><i class="fa fa-book"></i></a>&nbsp;';
                                
                                // $actionHtml .= '<a href="' . action('AccountController@detail_show', [$row->id]) . '" class="btn btn-info btn-xs btn-vew" target="_blank"><i class="fa fa-book"></i></a>&nbsp;';
                                
                                if ($row->is_closed == 0) {
                                    $actionHtml .= '<button data-href="' . action('AccountController@getFundTransfer', [$row->id]) . '" class="btn btn-xs btn-info btn-modal hide" data-container=".view_modal"><i class="fa fa-exchange"></i> @lang("account.fund_transfer")</button>';
                                    $actionHtml .= '<button data-href="' . action('AccountController@getDeposit', [$row->id]) . '" class="btn btn-xs btn-success btn-modal hide" data-container=".view_modal"><i class="fas fa-money-bill-alt"></i> @lang("account.deposit")</button>';
                                    $actionHtml .= '<button data-url="' . action('AccountController@close', [$row->id]) . '" class="btn btn-xs btn-danger hide close_account"><i class="fa fa-power-off"></i> @lang("messages.close")</button>';
                                } elseif ($row->is_closed == 1) {
                                    $actionHtml .= '<button data-url="' . action('AccountController@activate', [$row->id]) . '" class="btn hide btn-xs btn-success activate_account"><i class="fa fa-power-off"></i> @lang("messages.activate") - ' . $row->type . '</button>';
                                }
                            
                                return $actionHtml;
                            })
                            
                            ->editColumn('name', function ($row) {
                                if ($row->is_closed == 1) {
                                    return $row->name . ' <small class="label pull-right bg-red no-print">' . __("account.closed") . '</small><span class="print_section">(' . __("account.closed") . ')</span>';
                                } else {
                                    return $row->name;
                                }
                            })
                            ->editColumn('balance', function ($row) {
                                // if($row->type == 'credit'){
                                //     $balanceText = 'Cr: '.number_format($row->balance, 2);
                                // }else if($row->type == 'debit'){
                                //     $balanceText = 'Dr: '.number_format($row->balance, 2);
                                // }else{
                                //     $balanceText = '';
                                // }
                                // $balanceText = str_replace('-', '', $balanceText);
                                if($row->balance <  0){
                                    $balanceText = 'Cr: '.number_format($row->balance, 2);
                                }else if($row->balance > 0){
                                    $balanceText = 'Dr: '.number_format($row->balance, 2);
                                }else{
                                    $balanceText = '';
                                }
                                $balanceText = str_replace('-', '', $balanceText);
                                
                                $formattedBalance = '<span>' . $balanceText . '</span>';
                                return $formattedBalance;
                            })
                            ->editColumn('account_type', function ($row) {
                                $account_type = '';
                                if (!empty($row->account_type->parent_account)) {
                                    $account_type .= $row->account_type->parent_account->name . ' - ';
                                }
                                if (!empty($row->account_type)) {
                                    $account_type .= $row->account_type->name;
                                }
                                return $account_type;
                            })
                            ->editColumn('parent_account_type_name', function ($row) {
                                $parent_account_type_name = empty($row->parent_account_type_name) ? $row->account_type_name : $row->parent_account_type_name;
                                return $parent_account_type_name;
                            })
                            ->editColumn('account_type_name', function ($row) {
                                $account_type_name = empty($row->parent_account_type_name) ? '' : $row->account_type_name;
                                return $account_type_name;
                            })
                            ->editColumn('account_details', function($row) {
                                $html = '';
                                if (!empty($row->account_details)) {
                                    foreach ($row->account_details as $account_detail) {
                                        if (!empty($account_detail['label']) && !empty($account_detail['value'])) {
                                            $html .= $account_detail['label'] . " : ".$account_detail['value'] ."<br>";
                                        }
                                    }
                                }
                                return $html;
                            })
                            ->removeColumn('id')
                            ->removeColumn('is_closed')
                            ->rawColumns(['action', 'balance', 'name', 'account_details','checkbox'])
                            ->make(true);
        }

        $not_linked_payments = TransactionPayment::leftjoin(
            'transactions as T',
            'transaction_payments.transaction_id',
            '=',
            'T.id'
        )
                                    ->whereNull('transaction_payments.parent_id')
                                    ->where('method', '!=', 'advance')
                                    ->where('transaction_payments.business_id', $business_id)
                                    ->whereNull('account_id')
                                    ->count();

        // $capital_account_count = Account::where('business_id', $business_id)
        //                             ->NotClosed()
        //                             ->where('account_type', 'capital')
        //                             ->count();

        $account_types = AccountType::where('business_id', $business_id)
                                     ->whereNull('parent_account_type_id')
                                     ->with(['sub_types'])
                                     ->get();

        return view('account.index')
                ->with(compact('not_linked_payments', 'account_types'));
    }

    
    
    
    public function assign_coa(){
        $transaction_acc    = Account::All();
        $acc_types          = AccountType::All();
        $roles              = Role::All();
        return view('account.assign_coa')->with(compact('transaction_acc','acc_types', 'roles'));
    }
    
    public function assign_coa_list(){
        $assign_coa_list = DB::table('assign_coa_role')->leftjoin('roles', 'roles.id', '=', 'assign_coa_role.role_id')->select('assign_coa_role.*','roles.name')->groupby('role_id')->get();
        return view('account.assign_coa_list')->with(compact('assign_coa_list'));
    }
    
    public function assign_coa_edit($role_id){
        $assign_coa         = DB::table('assign_coa_role')->leftjoin('roles', 'roles.id', '=', 'assign_coa_role.role_id')->where('role_id', $role_id)->select('assign_coa_role.*','roles.name')->get();
        foreach($assign_coa as $key => $val){
             $account  = Account::where('id', $val->transaction_acc_id)->get();
            foreach($account as $key2 => $val2){
                $assign_coa[$key]->control_account = $account[$key2]['account_type_id'];
            }
        }
        // dd($assign_coa);
        $transaction_acc    = Account::All();
        $acc_types          = AccountType::All();
        $roles              = Role::All();
        return view('account.assign_coa_edit')->with(compact('transaction_acc','acc_types', 'roles', 'assign_coa'));
    }
    
    public function get_transaction_acc(Request $request){
        $acc_type        = $request->input('acc_type');
        $transaction_acc = Account::whereIn('account_type_id', $acc_type)->get();
        return response()->json($transaction_acc);
    }
    
    public function assign_coa_store(Request $request){
        try {
            $transaction_acc = $request->input('transaction_acc');
            $acc_type = $request->input('acc_type');
            $role = $request->input('role');
            
            DB::beginTransaction();
            $data = [];
            foreach ($transaction_acc as $key => $val) {
                $data[] = [
                    'role_id' => $role,
                    'transaction_acc_id' => $val,
                ]; 
            }
        
            DB::table('assign_coa_role')->insert($data);
            DB::commit();
            
            $output = ['success' => true, 'msg' => 'Success'];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }

        return redirect()->action('AccountController@assign_coa_list')->with($output);
    }
    
    public function assign_coa_update(Request $request){
        try {
            $transaction_acc = $request->input('transaction_acc');
            $acc_type = $request->input('acc_type');
            $role = $request->input('role');
            $role_id = $request->input('role_id');
            
            DB::beginTransaction();
            DB::table('assign_coa_role')->where('role_id',$role_id)->delete();
            
            $data = [];
            foreach ($transaction_acc as $key => $val) {
                $data[] = [
                    'role_id' => $role,
                    'transaction_acc_id' => $val,
                ]; 
            }
        
            DB::table('assign_coa_role')->insert($data);
            
            DB::commit();
            $output = ['success' => true, 'msg' => 'Success'];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }

        return redirect()->action('AccountController@assign_coa_list')->with($output);
    }
    
    public function assign_coa_delete($role_id){
        try {
            DB::beginTransaction();
            DB::table('assign_coa_role')->where('role_id',$role_id)->delete();
            
            DB::commit();
            $output = ['success' => true, 'msg' => 'Success'];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }
        return redirect()->action('AccountController@assign_coa_list')->with($output);
    }
    
    
    
    public function print_pr($id){
        
        $invoice=AccountTransaction::whereIn('reff_no',[$id])
        ->leftJoin('accounts', 'accounts.id', '=', 'account_transactions.account_id')
        ->select('account_transactions.*', 'accounts.name as acc_name')
        ->get();
        
        $invoice_total= DB::table('account_transactions')
                    ->whereNotNull('reff_no')
                    ->where('reff_no',$id)
                    ->whereNull('deleted_at')
                    ->select('*', DB::raw('sum(amount) as total'))
                    ->groupBy('reff_no')
                    ->get();
                    
        // dd($invoice);
        return view('account.invoice_print')->with(compact('invoice','invoice_total'));
    }
    
    
    public function print_voucher($id){
        
        $invoice=AccountTransaction::whereIn('reff_no',[$id])->get(); 
        $invoice_total= AccountTransaction::
                whereNotNull('reff_no') 
                ->where('reff_no',$id)
                ->select('*', DB::raw('sum(amount) as total'))
                ->groupBy('reff_no')
                ->get();
                
        $payment_voucer_total = AccountTransaction::
                whereNotNull('reff_no') 
                ->where('reff_no',$id)
                ->where('type',"credit")
                ->select('*', DB::raw('sum(amount) as total'))
                ->groupBy('reff_no')
                ->get();
                
        $reciept_voucer_total = AccountTransaction::
                whereNotNull('reff_no') 
                ->where('reff_no',$id)
                ->where('type',"credit")
                ->select('*', DB::raw('sum(amount) as total'))
                ->groupBy('reff_no')
                ->get();
             
        // dd($payment_voucer_total[0]->account->name);
                 
        return view('account.invoice_print_voucher')->with(compact('invoice','invoice_total',"payment_voucer_total","reciept_voucer_total"));
    }
    
    
    public function voucher_listing(){
        $start = request()->start;
        $end = request()->end;

        $voucher_list = AccountTransaction::
                    leftJoin('accounts', 'accounts.id', '=', 'account_transactions.account_id')
                    ->whereNull('account_transactions.deleted_at') 
                    ->whereNotNull('account_transactions.reff_no')
                    ->where('account_transactions.reff_no', '!=', '')
                    ->when(isset($start, $end), function($query) use ($start, $end){
                        $query->whereBetween('account_transactions.operation_date', [$start, $end]);
                    })
                    ->select('account_transactions.*','accounts.name as account_name')
                    ->orderby('account_transactions.operation_date','asc')
                    ->get();

        if(request()->ajax()){
            return view('account.listing_voucher_partial')->with(compact('voucher_list'));
        }else{
            return view('account.listing_voucher')->with(compact('voucher_list'));
        }
    }
    
    
        public function account_book_create(Request $request)
    {

      try {

        DB::beginTransaction();

        $user_id = $request->session()->get('user.id');
        $amount = 0;
        $head= $request->input('debit_head');
    
          foreach($head as $key=>$h){
            for ($i = 0; $i < 2; $i++) {
                if($i==0){
                    $data = new AccountTransaction;
                    $data->amount = $request->input('amount')[$key];
                    $data->account_id =  $request->input('debit_head')[$key];
                    $data->type  = "debit";
                    $data->sub_type = 'account_book';
                    $data->operation_date = $request->input('date');
                    $data->reff_no=$request->input('v_no');
                    $data->description=$request->input('description')[$key];
                    $data->document=$request->input('document')[$key];
                    if(!empty($request->input('document')[$key])){
                        $this->commonUtil->realize_cheque($request->input('document')[$key]);
                    }
                    if (!empty($request->file('attachment')[$key]) && $request->file('attachment')[$key] != null) 
                     {
                       
                        $file=$request->file('attachment')[$key];
                        $path       = "bank_book/";
                        // $year_folder  = $path . date("Y");
                        // $month_folder = $year_folder . '/' . date("m");
                        $image_name_2 = time() . '.' . $file->getClientOriginalName();

                        $file->move(public_path($path), $image_name_2);
                        $data->attachment=$image_name_2;
                    }else{
                        $data->attachment="";
                    }
               
                    $data->note=$request->input('remarks');
                    $data->created_by =$user_id;
                    $data->save();                        

                }elseif($i=1){ 
                    $data = new AccountTransaction;
                    $data->amount = $request->input('amount')[$key];
                    $data->account_id =  $request->input('credit_head')[$key];
                    $data->type  = "credit";
                    $data->sub_type = 'account_book';
                    $data->operation_date = $request->input('date');
                    $data->reff_no=$request->input('v_no');
                    $data->description=$request->input('description')[$key];
                    $data->document=$request->input('document')[$key];
                    if(!empty($request->input('document')[$key])){
                        $this->commonUtil->realize_cheque($request->input('document')[$key]);
                    }
                    
                    if (!empty($request->file('attachment')[$key]) && $request->file('attachment')[$key] != null) 
                    {
                       
                        $file=$request->file('attachment')[$key];
                        $path       = "bank_book/";
                        // $year_folder  = $path . date("Y");
                        // $month_folder = $year_folder . '/' . date("m");
                        $image_name_2 = time() . '.' . $file->getClientOriginalName();
                        $data->attachment=$image_name_2;
                    }else{
                        $data->attachment="";
                    }
                   
                    $data->note=$request->input('remarks');
                    $data->created_by =$user_id;
                    $data->save();      
                }
            }
        }
        DB::commit();

        $output = ['success' => true,
                            'msg' => __('Created  Successfully')
                ];
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
        
        $output = ['success' => false,
                        'msg' => $e->getMessage()
                    ];
    }
       return redirect()->action('AccountController@account_book_list')->with('status',$output); 
    }

    
    public function account_book()
    {
        $business_id = session()->get('user.business_id');
        $not_linked_payments = TransactionPayment::leftjoin(
            'transactions as T',
            'transaction_payments.transaction_id',
            '=',
            'T.id'
        );
        $voucher_no = 0;
        $voucher_no_result = DB::table('account_transactions')
                 ->whereNull('deleted_at')
                 ->where('reff_no', 'like', '%AB-%')
                 ->select('reff_no', DB::raw("substring_index(substring_index(reff_no,'-',-1),',',-1) as max_no"))->get()
                 ->max('max_no');
        $int_val = (int) isset($voucher_no_result) ? $voucher_no_result : '0';
        $voucher_no = $int_val+1;
        
        
        $account_types = AccountType::where('business_id', $business_id)
                                     ->whereNull('parent_account_type_id')
                                     ->with(['sub_types'])
                                     ->get();
        $payment_account = DB::table('accounts')
            ->whereNull('deleted_at')
            ->where('account_number', 'like', '%B%')
            ->orWhere('account_number', 'like', '%C%')
            ->orderBy('name','ASC')
            ->get();
        $other_accounts = DB::table('accounts')
        ->whereNull('deleted_at')
        ->where(function ($query) {
            $query->where('account_number', 'not like', '%B%')
                ->orWhere('account_number', 'not like', '%C%');
        })
        ->orderBy('name', 'ASC')
        ->get();
            
        $cheques = PostDatedChequeLine::where('status', 0)->get();
        return view('account.account_book')
                ->with(compact('cheques','not_linked_payments', 'account_types','payment_account','other_accounts','voucher_no'));
    }
    
    
    
    public function account_book_list(){
        $list = AccountTransaction::
                where('reff_no','LIKE', '%AB-%')
                ->groupBy('reff_no')
                ->orderBy('operation_date','DESC')
                ->select('reff_no','operation_date','note', \DB::raw('SUM(CASE WHEN type = "debit" THEN amount ELSE 0 END) as total_debit_amount'))
                ->get();
        return view('account.account_book_list')->with(compact('list'));
    }
    
    public function account_book_delete($reff_no){

    try {

        AccountTransaction::where('reff_no',$reff_no)->delete(); 
      
    DB::commit();

    $output = ['success' => true,
        'msg' => __('Deleted Successfully')
        ];

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
        
        $output = ['success' => false,
                        'msg' => $e->getMessage()
                    ];
    }

    return redirect()->action('AccountController@account_book_list')->with('status',$output); 
    }
    
    //  account book end
    
public function get_code($id)
{
    $business_id = session()->get('user.business_id');
    $transaction_acc = DB::table('assign_coa_role')->where('role_id', auth()->user()->roles[0]->id)->get()->toArray();
    $transactionIds = array_map(function($item) {
        return $item->transaction_acc_id;
    }, $transaction_acc);

    $accountQuery = Account::leftJoin(
            'account_transactions as AT',
            'AT.account_id',
            '=',
            'accounts.id'
        )
        ->whereNull('AT.deleted_at')
        ->where('accounts.business_id', $business_id)
        ->where('accounts.id', $id)
        ->select('accounts.*', DB::raw("SUM(IF(AT.type='credit', AT.amount, -1 * AT.amount)) as balance"));

        // Apply additional condition if user doesn't have access to all accounts
        if (!auth()->user()->can('account.access_all')) {
            if (!$this->moduleUtil->is_admin(auth()->user(), $business_id)) {
                $accountQuery->whereNotIn('accounts.id', $transactionIds);
            }
        }

    // Fetch the account details
    $account = $accountQuery->first();

    // Check if the account exists
    if (!$account) {
        return response()->json(['error' => 'Account not found'], 404);
    }

    // Return the account details
    return response()->json($account);
}

    
    
    public function edit_voucher($id = null)
    {
        // dd($id);
        
    }
    
    // GENERAL VOUCHER 
    public function general_voucher_list()
    {
    
        $list = AccountTransaction::where('reff_no','LIKE', '%J-%')->groupby('reff_no')->orderBy('operation_date','DESC')->get();
        return view('account.general_voucher_list')->with(compact('list'));
    }
    
    public function general_voucher()
    {
      
        $business_id = session()->get('user.business_id');
        $not_linked_payments = TransactionPayment::leftjoin(
            'transactions as T',
            'transaction_payments.transaction_id',
            '=',
            'T.id'
        );
        
        $voucher_no = 0;
        $voucher_no_result = DB::table('account_transactions')
                 ->whereNull('deleted_at')
                 ->where('reff_no', 'like', '%J-%')
                 ->select('reff_no', DB::raw("substring_index(substring_index(reff_no,'-',-1),',',-1) as max_no"))->get()
                 ->max('max_no');
        $int_val = (int) isset($voucher_no_result) ? $voucher_no_result : '0';
        $voucher_no = $int_val+1;
        
        $account_types = AccountType::where('business_id', $business_id)
                                     ->whereNull('parent_account_type_id')
                                     ->with(['sub_types'])
                                     ->get();
                                     
        $payment_account = DB::table('accounts')
        ->whereNull('deleted_at')
        ->get();
            
        $other_accounts = DB::table('accounts')
            ->whereNull('deleted_at')
            ->where('is_closed','0')
            ->get();
        
        return view('account.general_voucher')
                ->with(compact('not_linked_payments', 'account_types','payment_account','other_accounts','voucher_no'));
            
    }

    public function general_voucher_create(Request $request)
    {
        try {
        $user_id = $request->session()->get('user.id');
        $amount = 0;
            
        DB::beginTransaction();
        $get_row = $request->input('debit_head');               
        if (is_array($get_row)) {
            for ($i = 0; $i < sizeof($get_row); $i++) {
                $data = new AccountTransaction;
                $data->amount = ($request->input('debit')[$i] > 0 ? $request->input('debit')[$i] : ($request->input('credit')[$i] > 0 ? $request->input('credit')[$i] : 0));
                $data->account_id =  $request->input('debit_head')[$i];
                $data->type  = ($request->input('debit')[$i] > 0 ? "debit" : ($request->input('credit')[$i] > 0 ? "credit" : "error"));;
                $data->sub_type = 'general_voucher';
                $data->document =  $request->input('doc_num')[$i];
                $data->description =  $request->input('desc')[$i];
                $data->operation_date = $request->input('date');
                $data->reff_no=$request->input('v_no');
                $data->note=$request->input('remarks');
                $data->created_by =$user_id;
                
                // for file upload
                if($i == 0){
                    if(!empty($request->file('attachment')) || $request->file('attachment') != null)
                    {
                        $file=$request->file('attachment');
                        $path       = "journal_voucher/";
                        $image_name_2 = time() . '.' . $file->getClientOriginalName();
                        $file->move(public_path($path), $image_name_2);
                        $data->attachment=$image_name_2;
                    }else{
                        $data->attachment="";
                    }
                }else{
                    $data->attachment="";
                }
                
                $data->save();                        
            }
        }
           
        DB::commit();
            
        $output = ['success' => 1,
                        'msg' => 'Created Successfull'
                    ];
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
        
        $output = ['success' => 0,
                        'msg' => $e->getMessage()
                    ];
    }
    return redirect()->action('AccountController@general_voucher_list')->with('status',$output);
    }

    public function jv_edit($reff_no){
        
        $jv = AccountTransaction::where('reff_no',$reff_no)->get();
        $business_id            = session()->get('user.business_id');
        $not_linked_payments    = TransactionPayment::leftjoin(
            'transactions as T',
            'transaction_payments.transaction_id',
            '=',
            'T.id'
        );
        $account_types      = AccountType::where('business_id', $business_id)->whereNull('parent_account_type_id')->with(['sub_types'])->get();
        $payment_account    = DB::table('accounts')->whereNull('deleted_at')->get();
        $other_accounts     = DB::table('accounts')->whereNull('deleted_at')->get();
            
        return view('account.jv_edit')
                ->with(compact('not_linked_payments', 'account_types','payment_account','other_accounts','jv'));
    }

    public function jv_update(Request $request){

        try {

        DB::beginTransaction();

        $jv = AccountTransaction::where('reff_no',$request->input('v_no'))->delete();


        $user_id = $request->session()->get('user.id');
        $amount = 0;
        $get_row = $request->input('debit_head');               
        if (is_array($get_row)) {
            for ($i = 0; $i < sizeof($get_row); $i++) {
                $data = new AccountTransaction;
                $data->amount = ($request->input('debit')[$i] > 0 ? $request->input('debit')[$i] : ($request->input('credit')[$i] > 0 ? $request->input('credit')[$i] : 0));
                $data->account_id =  $request->input('debit_head')[$i];
                $data->type  = ($request->input('debit')[$i] > 0 ? "debit" : ($request->input('credit')[$i] > 0 ? "credit" : "error"));;
                $data->sub_type ='general_voucher';
                $data->document =  $request->input('doc_num')[$i];
                $data->description =  $request->input('desc')[$i];
                $data->operation_date = $request->input('date');
                $data->reff_no=$request->input('v_no');
                $data->note=$request->input('remarks');
                $data->created_by =$user_id;
                $data->save();                        
            }
        }
        DB::commit();

        $output = ['success' => 1,
                        'msg' => __('Updated Successfull')
                    ];
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
        
        $output = ['success' => 0,
                        'msg' => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                    ];
     
    }
        
    return redirect()->action('AccountController@general_voucher_list')->with('status', $output);
    }

    public function jv_delete($reff_no){
        
        try {


        $jv = AccountTransaction::where('reff_no',$reff_no)->delete();
        DB::commit();

        $output = ['success' => true,
                            'msg' => __('Deleted Successfully')
                ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => $e->getMessage()
                        ];
        }

        return redirect()->action('AccountController@general_voucher_list')->with('status',$output);
        // return $output;
    }


    // Payment Voucher || debit Voucher
    public function debit_voucher_list()
    {
        
         if (!auth()->user()->can('account.payment_vouchers')) {
            abort(403, 'Unauthorized action.');
        }
        
        if(request('type') == 'cash_payment_voucher'){
            $list = AccountTransaction::selectRaw('*, SUM(CASE WHEN type = "credit" THEN amount ELSE 0 END) as total_amount')->where('reff_no','LIKE', '%CPV-%')->groupby('reff_no')->orderBy('operation_date','DESC')->get();
        }else{
            $list = AccountTransaction::selectRaw('*, SUM(CASE WHEN type = "credit" THEN amount ELSE 0 END) as total_amount')->where('reff_no','LIKE', '%D-%')->where('type','credit')->groupby('reff_no')->orderBy('operation_date','DESC')->get();
        }
        return view('account.debit_voucher_list')->with(compact('list'));
    }

    public function debit_voucher(){
        if (!auth()->user()->can('account.payment_vouchers')) {
            abort(403, 'Unauthorized action.');
        }
        
        $voucher_no = 0;
        if(request('type') == 'cash_payment_voucher'){
            $voucher_no_result = DB::table('account_transactions')
                 ->whereNull('deleted_at')
                 ->where('reff_no', 'like', '%CPV-%')
                 ->select('reff_no', DB::raw("substring_index(substring_index(reff_no,'-',-1),',',-1) as max_no"))->get()
                 ->max('max_no');
        }else{
                $voucher_no_result = DB::table('account_transactions')
                 ->whereNull('deleted_at')
                 ->where('reff_no', 'like', '%D-%')
                 ->select('reff_no', DB::raw("substring_index(substring_index(reff_no,'-',-1),',',-1) as max_no"))->get()
                 ->max('max_no');
        }   
                 
        $int_val = (int) isset($voucher_no_result) ? $voucher_no_result : '0';
        $voucher_no = $int_val+1;
        
        $business_id = session()->get('user.business_id');
        $not_linked_payments = TransactionPayment::leftjoin(
            'transactions as T',
            'transaction_payments.transaction_id',
            '=',
            'T.id'
        );
        $account_types = AccountType::where('business_id', $business_id)
                                     ->whereNull('parent_account_type_id')
                                     ->with(['sub_types'])
                                     ->get();
                                     
        $payment_account = DB::table('accounts')
        // ->whereIn('account_type_id',[13,12])
        ->whereNull('deleted_at')
        ->where("is_closed",0)->get();
            
        $other_accounts = DB::table('accounts')
            ->whereNull('deleted_at')
            ->where("is_closed",0)->get();
                
        return view('account.debit_voucher')
                ->with(compact('not_linked_payments', 'account_types','payment_account','other_accounts','voucher_no'));
            
    }

    public function debit_voucher_create(Request $request){
        
        if (!auth()->user()->can('account.payment_vouchers')) {
            abort(403, 'Unauthorized action.');
        }
        
    try {

        DB::beginTransaction();

        $user_id = $request->session()->get('user.id');
        $amount = 0;
        $sub_type = 'credit_voucher';
        
        if (!empty($request->input('type'))) {
            $sub_type = $request->input('type');
        }
        
        $image_name_2 = ''; // Initialize $image_name_2 as an empty string
        
        if (!empty($request->file('attachment')) && $request->file('attachment') != null) {
            // Handle single file upload
            $file = $request->file('attachment');
            $path = "payment_voucher/";
            $image_name_2 = time() . '.' . $file->getClientOriginalName();
            $file->move(public_path($path), $image_name_2);
        } else {
            $image_name_2 = ''; // Ensure $image_name_2 is an empty string if no file is provided
        }
        
            foreach($request->input('debit_head') as $key => $value){
                
                    $amount += $request->input('txtAmount')[$key];
                    $credit_data = [
                        'amount' => $_POST['txtAmount'][$key],
                        'account_id' => $value,
                        'type' => 'debit',
                        'reff_no' => $request->input('v_no'),
                        'note' => $request->input('remarks'),
                        // 'sub_type' => 'debit_voucher',
                        'operation_date' => $request->input('date'),
                        'document' =>  $request->input('doc_num')[$key],
                        'description' =>  $request->input('desc')[$key],
                        'created_by' => $user_id,
                    ];
                    if(!empty($request->input('doc_num')[$key])){
                        $this->commonUtil->realize_cheque($request->input('doc_num')[$key]);
                    }
                    
                // for attachment
                if (!empty($request->file('attachment_credit')[$key]) && $request->file('attachment_credit')[$key] !=null) {
                            $file = $request->file('attachment_credit')[$key];
                            $path = "payment_voucher/";
                            $image_name = time() . '.' . $file->getClientOriginalName();
               
                            $file->move(public_path($path), $image_name);
                            $credit_data['attachment'] = $image_name;
   
                    // $ob_transaction_data['attachment'] = implode(',', $attachments);
                } else {
                    // $ob_transaction_data['attachment'] = '';
                    $credit_data['attachment']="";
                }
                
                  
                AccountTransaction::createAccountTransaction($credit_data);
                
                // credit
                $ob_transaction_data = [
                    'amount' => $_POST['txtAmount'][$key],
                    'account_id' => $request->input('credit_head'),
                    'type' => 'credit',
                    'reff_no' => $request->input('v_no'),
                    'note' => $request->input('remarks'),
                    // 'sub_type' => 'debit_voucher',
                    'operation_date' => $request->input('date'),
                    'document' =>  $request->input('doc_num')[$key],
                    'description' =>  $request->input('desc')[$key],
                    'created_by' => $user_id,
                    'attachment' => $image_name_2
                ];
                AccountTransaction::createAccountTransaction($ob_transaction_data);
            }


            DB::commit();

        $output = ['success' => true,
              'msg' => __('Created Successfully')
        ];
        } catch (\Exception $e) {
        DB::rollBack();
        \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                    
        $output = ['success' => false,
            'msg' => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
         ];
    }
            
        if(!empty($request->input('type'))){
            return redirect()->action('AccountController@debit_voucher_list', ['type' => $request->input('type')])->with('status',$output);
        }else{
            return redirect()->action('AccountController@debit_voucher_list')->with('status',$output);  
        }
    }

    public function dv_edit($reff_no){
        
        $debit_entries = AccountTransaction::where('reff_no',$reff_no)->where('type', 'debit')->get();
        $credit_entries = AccountTransaction::where('reff_no',$reff_no)->where('type','credit')->first();
        
        $business_id            = session()->get('user.business_id');
        $not_linked_payments    = TransactionPayment::leftjoin(
            'transactions as T',
            'transaction_payments.transaction_id',
            '=',
            'T.id'
        );
        $account_types      = AccountType::where('business_id', $business_id)->whereNull('parent_account_type_id')->with(['sub_types'])->get();
        $payment_account    = DB::table('accounts')
        ->whereNull('deleted_at')
        ->get();
        $other_accounts     = DB::table('accounts')
        ->whereNull('deleted_at')
        ->get();
        
        return view('account.dv_edit')
                ->with(compact('not_linked_payments', 'account_types','payment_account','other_accounts','credit_entries','debit_entries'));
    }

    public function dv_update(Request $request){

    try {

    DB::beginTransaction();

    $dv = AccountTransaction::where('reff_no',$request->input('v_no'))->delete();
        $dv = AccountTransaction::where('attachment',$request->input('v_no'))->delete();
        $user_id = $request->session()->get('user.id');
        $amount = 0;
        $sub_type = 'credit_voucher';
        if(!empty($request->input('type'))){
            $sub_type = $request->input('type');
        }
        if (!empty($request->file('attachment')) && $request->file('attachment') != null) {
            $file = $request->file('attachment');
            $path = "payment_voucher/";
            $image_name_2 = time() . '.' . $file->getClientOriginalName();
            $file->move(public_path($path), $image_name_2);
        } else {
            $image_name_2 = $request->input('attachment_one'); // Ensure $image_name_2 is an empty string if no file is provided
        }
        foreach($request->input('debit_head') as $key => $value){
            
                $amount += $request->input('txtAmount')[$key];
                $credit_data = [
                    'amount' => $_POST['txtAmount'][$key],
                    'account_id' => $value,
                    'type' => 'debit',
                    'reff_no' => $request->input('v_no'),
                    'note' => $request->input('remarks'),
                    'document' =>  $request->input('doc_num')[$key],
                    'description' =>  $request->input('desc')[$key],
                    // 'sub_type' => 'debit_voucher',
                    'operation_date' => $request->input('date'),
                    'created_by' => $user_id
                ];
                if(!empty($request->input('doc_num')[$key])){
                    $this->commonUtil->realize_cheque($request->input('doc_num')[$key]);
                }
                
                if (!empty($request->file('attachment_credit')[$key]) && $request->file('attachment_credit')[$key] !=null) {
                    $file = $request->file('attachment_credit')[$key];
                    $path = "payment_voucher/";
                    $image_name = time() . '.' . $file->getClientOriginalName();
       
                    $file->move(public_path($path), $image_name);
                    $credit_data['attachment'] = $image_name;

                    } else {
                    
                        $credit_data['attachment'] = $request->input('attachment_cred_hidden')[$key];
                     
                    }
                AccountTransaction::createAccountTransaction($credit_data);
                
                $ob_transaction_data = [
                    'amount' => $_POST['txtAmount'][$key],
                    'account_id' => $request->input('credit_head'),
                    'type' => 'credit',
                    'reff_no' => $request->input('v_no'),
                    'note' => $request->input('remarks'),
                    'document' =>  $request->input('doc_num')[$key],
                    'description' =>  $request->input('desc')[$key],
                    // 'sub_type' => 'debit_voucher',
                    'operation_date' => $request->input('date'),
                    'created_by' => $user_id,
                    'attachment' => $image_name_2
                ];
                AccountTransaction::createAccountTransaction($ob_transaction_data);
        }

     DB::commit();

        $output = ['success' => true,
                'msg' => __('Updated Successfully')
        ];
        } catch (\Exception $e) {
        DB::rollBack();
        \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                        
        $output = ['success' => false,
        'msg' => $e->getMessage()
        ];
    }
        if(!empty($request->input('type'))){
            return redirect()->action('AccountController@debit_voucher_list', ['type' => $request->input('type')])->with('status',$output);
        }else{
            return redirect()->action('AccountController@debit_voucher_list')->with('status',$output);
        }

    }

    public function dv_delete($reff_no)
    {
        try{


        $dv = AccountTransaction::where('reff_no',$reff_no)->delete();

            
        DB::commit();

    $output = ['success' => true,
          'msg' => __('Created Successfully')
    ];
    } catch (\Exception $e) {
    DB::rollBack();
    \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                
     $output = ['success' => false,
        'msg' => $e->getMessage()
      ];
    }
        return redirect()->action('AccountController@debit_voucher_list');
    }



    // Recipt Voucher || Credit voucher
    public function credit_voucher_list()
    {
        
        if (!auth()->user()->can('account.receiept_vouchers')) {
            abort(403, 'Unauthorized action.');
        }


        if(request('type') == 'cash_received_voucher'){
            $list = AccountTransaction::selectRaw('*, SUM(CASE WHEN type = "debit" THEN amount ELSE 0 END) as total_amount')->where('reff_no','LIKE', '%CRV-%')->groupby('reff_no')->orderBy('operation_date','DESC')->get();
        }else{
            $list = AccountTransaction::selectRaw('*, SUM(CASE WHEN type = "debit" THEN amount ELSE 0 END) as total_amount')->where('reff_no','LIKE', '%R-%')->groupby('reff_no')->orderBy('operation_date','DESC')->get();
        }
        return view('account.credit_voucher_list')->with(compact('list'));
    }
    
    public function credit_voucher(){
        $business_id = session()->get('user.business_id');
        $not_linked_payments = TransactionPayment::leftjoin(
            'transactions as T',
            'transaction_payments.transaction_id',
            '=',
            'T.id'
        );
        
        
        $voucher_no = 0;
        
        if(request('type') == 'cash_received_voucher'){
            $voucher_no_result = DB::table('account_transactions')
                 ->whereNull('deleted_at')
                 ->where('reff_no', 'like', '%CRV-%')
                 ->select('reff_no', DB::raw("substring_index(substring_index(reff_no,'-',-1),',',-1) as max_no"))->get()
                 ->max('max_no');
        }else{
            $voucher_no_result = DB::table('account_transactions')
                 ->whereNull('deleted_at')
                 ->where('reff_no', 'like', '%R-%')
                 ->select('reff_no', DB::raw("substring_index(substring_index(reff_no,'-',-1),',',-1) as max_no"))->get()
                 ->max('max_no');
        }
                 
                 
        $int_val = (int) isset($voucher_no_result) ? $voucher_no_result : '0';
        $voucher_no = $int_val+1;
        
        $account_types = AccountType::where('business_id', $business_id)
                                     ->whereNull('parent_account_type_id')
                                     ->with(['sub_types'])
                                     ->get();
                                     
        $payment_account = DB::table('accounts')
        // ->whereIn('account_type_id',[13,12])
        ->whereNull('deleted_at')
        ->where("is_closed",0)->get();
            
        $other_accounts = DB::table('accounts')
        // ->whereIn('account_type_id',[13,12])
        ->whereNull('deleted_at')
        ->where("is_closed",0)->get();
                
        return view('account.credit_voucher')
                ->with(compact('not_linked_payments', 'account_types','payment_account','other_accounts','voucher_no'));
            
    }

    public function credit_voucher_create(Request $request)
    {

        try {
    
            DB::beginTransaction();
            $user_id = $request->session()->get('user.id');
            $amount = 0;
            $sub_type = 'credit_voucher';
            if(!empty($request->input('type'))){
                $sub_type = $request->input('type');
            }

            if (!empty($request->file('attachment')) && $request->file('attachment')->isValid()) {
                $file = $request->file('attachment');
                $path = "reciept_voucher/";
            
                // Generate a unique filename
                $image_name_2 = time() . '_' . str_random(8) . '.' . $file->getClientOriginalName();
            
                try {
                    // Move the file to the desired directory
                    $file->move(public_path($path), $image_name_2);
            
                    // Optionally, you can store $image_name_2 in the database or use it for further processing
                } catch (\Exception $e) {
                    // Handle the exception (e.g., log, display an error message, etc.)
                    echo "Error uploading file: " . $e->getMessage();
                }
            } else {
                // Set a default value or handle the case where no attachment is provided
                $image_name_2 = null;
            }
            
            
            foreach($request->input('debit_head') as $key => $value){
                // debit
                    $amount += $request->input('txtAmount')[$key];
                          // for attachment
        
                    $credit_data = [
                        'amount' => $_POST['txtAmount'][$key],
                        'account_id' => $request->input('debit_head')[$key],
                        'type' => 'credit',
                        'reff_no' => $request->input('v_no'),
                        'sub_type' => $sub_type,
                        'note' => $request->input('remarks'),
                        'operation_date' => $request->input('receipt_date'),
                        'document' =>  $request->input('doc_num')[$key],
                        'description' =>  $request->input('desc')[$key],
                        'created_by' => $user_id,
                        

                    ];
                    if(!empty($request->input('doc_num')[$key])){
                        $this->commonUtil->realize_cheque($request->input('doc_num')[$key]);
                    }
                    
                    if (!empty($request->file('attachment_child')[$key]) && $request->file('attachment_child')[$key] != null) 
                    {
                    
                      $file       =$request->file('attachment_child')[$key];
                      $path       = "reciept_voucher/";
                      $image_name = time() . '.' . $file->getClientOriginalName();
                    
                      $file->move(public_path($path), $image_name);
                      $credit_data['attachment']=$image_name;
                    
                
                    }else{
                      $credit_data['attachment']="";
                    }

                 AccountTransaction::createAccountTransaction($credit_data);
                    

                    // debit
                    $debit_data = [
                        'amount' => $_POST['txtAmount'][$key],
                        'account_id' => $request->input('credit_head'),
                        'type' => 'debit',
                        'reff_no' => $request->input('v_no'),
                        'sub_type' => $sub_type,
                        'operation_date' => $request->input('receipt_date'),
                        'note' => $request->input('remarks'),
                        'document' =>  $request->input('doc_num')[$key],
                        'description' =>  $request->input('desc')[$key],
                        'created_by' => $user_id,
                        'attachment' =>$image_name_2
                    ];
                    
                   
                    
                   
                   $account_transaction= AccountTransaction::createAccountTransaction($debit_data);
    
   
            }
    
        DB::commit();
    
        $output = ['success' => true,
              'msg' => __('Created Successfully')
        ];
        } catch (\Exception $e) {
        DB::rollBack();
        \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                    
         $output = ['success' => false,
            'msg' => $e->getMessage()
          ];
        }
            
            if(!empty($request->input('type'))){
                return redirect()->action('AccountController@credit_voucher_list', ['type' => $request->input('type')])->with('status',$output);
            }else{
                return redirect()->action('AccountController@credit_voucher_list')->with('status',$output); 
            }
        }
 
    public function cv_edit($reff_no){
        $cv = AccountTransaction::where('reff_no',$reff_no)->where('type', 'credit')->get();
        $business_id            = session()->get('user.business_id');
        $debit_acc_id = AccountTransaction::where('reff_no',$reff_no)->where('type','debit')->first();
        $not_linked_payments    = TransactionPayment::leftjoin(
            'transactions as T',
            'transaction_payments.transaction_id',
            '=',
            'T.id'
        );
        $account_types      = AccountType::where('business_id', $business_id)->whereNull('parent_account_type_id')->with(['sub_types'])->get();
        
        $payment_account = DB::table('accounts')
        // ->whereIn('account_type_id',[13,12])
        ->where("is_closed",0)->get();

        $other_accounts  = DB::table('accounts')
        ->whereNull('deleted_at')
        ->where("is_closed",0)->get();

        return view('account.cv_edit')
                ->with(compact('not_linked_payments', 'account_types','payment_account','other_accounts','cv','debit_acc_id'));
    }

  public function cv_update(Request $request) {
        try {
            DB::beginTransaction();
    
            // Use forceDelete to permanently delete records
            $cv = AccountTransaction::where('reff_no', $request->input('v_no'))->forceDelete();
    
            $user_id = $request->session()->get('user.id');
            $amount = 0;
            $sub_type = 'credit_voucher';
    
            if (!empty($request->input('type'))) {
                $sub_type = $request->input('type');
            }
    
            if (!empty($request->file('attachment')) && $request->file('attachment') != null) {
                $file = $request->file('attachment');
                $path = "reciept_voucher/";
                $image_name_2 = time() . '.' . $file->getClientOriginalName();
                $file->move(public_path($path), $image_name_2);
            } else {
                $image_name_2 = $request->input('attachment_one'); // Ensure $image_name_2 is an empty string if no file is provided
            }
    
            foreach ($request->input('debit_head') as $key => $value) {
                $amount += $request->input('txtAmount')[$key];
    
                $ob_transaction_data = [
                    'amount' => $request->input('txtAmount')[$key],
                    'account_id' => $value,
                    'type' => 'credit', // Corrected 'credit' instead of 'debit'
                    'reff_no' => $request->input('v_no'),
                    'note' => $request->input('remarks'),
                    'sub_type' => $sub_type,
                    'operation_date' => $request->input('date'),
                    'document' => $request->input('doc_num')[$key],
                    'description' => $request->input('desc')[$key],
                    'created_by' => $user_id,
                ];
                if(!empty($request->input('doc_num')[$key])){
                    $this->commonUtil->realize_cheque($request->input('doc_num')[$key]);
                }
                   
                if (!empty($request->file('attachment_credit')[$key]) && $request->file('attachment_credit')[$key] != null) {
                    $file = $request->file('attachment_credit')[$key];
                    $path = "reciept_voucher/";
                    $image_name = time() . '.' . $file->getClientOriginalName();
    
                    $file->move(public_path($path), $image_name);
                    $ob_transaction_data['attachment'] = $image_name;
                } else {
                    $ob_transaction_data['attachment'] = $request->input('attachment_cred_hidden')[$key];
                }
    
                AccountTransaction::createAccountTransaction($ob_transaction_data);
    
                // Assuming you want to create a debit transaction for each credit transaction
                $ob_transaction_data = [
                    'amount' => $request->input('txtAmount')[$key],
                    'account_id' => $request->input('credit_head'),
                    'type' => 'debit', // Corrected 'debit' instead of 'credit'
                    'reff_no' => $request->input('v_no'),
                    'note' => $request->input('remarks'),
                    'sub_type' => $sub_type,
                    'operation_date' => $request->input('date'),
                    'document' => $request->input('doc_num')[$key],
                    'description' => $request->input('desc')[$key],
                    'created_by' => $user_id,
                    'attachment' => $image_name_2,
                ];
    
                AccountTransaction::createAccountTransaction($ob_transaction_data);
            }
    
            DB::commit();
    
            $output = [
                'success' => true,
                'msg' => __('Updated Successfully'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
    
            $output = [
                'success' => false,
                'msg' => $e->getMessage(),
            ];
        }
    
        if (!empty($request->input('type'))) {
            return redirect()->action('AccountController@credit_voucher_list', ['type' => $request->input('type')])->with('status', $output);
        } else {
            return redirect()->action('AccountController@credit_voucher_list')->with('status', $output);
        }
    }
    public function cv_delete($reff_no)
    {
    try {
            
        DB::beginTransaction();

        $cv = AccountTransaction::where('reff_no', $reff_no)->forceDelete();
      

        DB::commit();

        $output = ['success' => true,
              'msg' => __('Deleted Successfully')
    ];
    } catch (\Exception $e) {
    DB::rollBack();
    \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                    
    $output = ['success' => false,
       'msg' => $e->getMessage()
     ];
}
     return redirect()->action('AccountController@credit_voucher_list')->with('status',$output);


}
    
   public function edit_bank_book($id)
    {
        $AccountTransaction=AccountTransaction::where('reff_no',$id)->where('type','debit')->get();
        $Account_credit=AccountTransaction::where('reff_no',$id)->where('type','credit')->get();
   
        
        $other_accounts = DB::table('accounts')
        ->whereNull('deleted_at')
        ->where(function ($query) {
            $query->where('account_number', 'not like', '%B%')
                ->orWhere('account_number', 'not like', '%C%');
        })
        ->orderBy('name', 'ASC')
        ->get();

        $cheques = PostDatedChequeLine::where('status', 0)->get();
        return view('account.edit_account_book')
        ->with(compact('AccountTransaction','other_accounts','Account_credit', 'cheques'));
    }

    public function account_book_update(Request $request)
    {
      try {

        DB::beginTransaction();
        
        $AccountTransaction = AccountTransaction::where('reff_no',$request->input('v_no'))->forceDelete();
        $user_id = $request->session()->get('user.id');
        $amount = 0;
        $head= $request->input('debit_head');
    
          foreach($head as $key=>$h){
              
            for ($i = 0; $i < 2; $i++) {
                if($i==0){
                    
                    $data = new AccountTransaction;
                    $data->amount = $request->input('amount')[$key];
                    $data->account_id =  $request->input('debit_head')[$key];
                    $data->type  = "debit";
                    $data->sub_type = 'account_book';
                    $data->reff_no=$request->input('v_no');
                    $data->operation_date = $request->input('date');
                    $data->description=$request->input('description')[$key];
                    $data->document=$request->input('document')[$key]; 
                    if(!empty($request->input('document')[$key])){
                        $this->commonUtil->realize_cheque($request->input('document')[$key]);
                    }

                    if (!empty($request->file('attachment')[$key]) && $request->file('attachment')[$key] != null) 
                    {
                        $file=$request->file('attachment')[$key];
                        
                        $path       = "bank_book/";
                        // $year_folder  = $path . date("Y");
                        // $month_folder = $year_folder . '/' . date("m");
                        $image_name_2 = time() . '.' . $file->getClientOriginalName();
                        $file->move(public_path($path), $image_name_2);

                        $data->attachment=$image_name_2;
                      
                 
                    }else{
                       
                        $data->attachment=$request->input('attachments')[$key];
                    }
                    $data->note=$request->input('remarks');
                    $data->created_by =$user_id;
                    $data->save();                        
         
                }elseif($i=1){
                    $data = new AccountTransaction;
                    $data->amount = $request->input('amount')[$key];
                    $data->account_id =  $request->input('credit_head')[$key];
                    $data->type  = "credit";
                    $data->sub_type = 'account_book';
                    $data->reff_no=$request->input('v_no');
                    $data->operation_date = $request->input('date');
                    $data->description=$request->input('description')[$key];
                    $data->document=$request->input('document')[$key];
                    if(!empty($request->input('document')[$key])){
                        $this->commonUtil->realize_cheque($request->input('document')[$key]);
                    }
                   
                     if (!empty($request->file('attachment')[$key]) && $request->file('attachment')[$key] != null) 
                     {
                       
           
                        $file=$request->file('attachment')[$key];
                  
                        $path       = "bank_book/";
                        $year_folder  = $path . date("Y");
                        $month_folder = $year_folder . '/' . date("m");
                        $image_name_2 = time() . '.' . $file->getClientOriginalName();
                        $data->attachment=$image_name_2;
                          
                 
                    }else{
                   
                        $data->attachment=$request->input('attachments')[$key];
                    }
                    
                    $data->note=$request->input('remarks');
                    $data->created_by =$user_id;
                 
                    $data->save(); 

                }
            }


            


        }

        DB::commit();

        $output = ['success' => true,
                'msg' => __('Updated Successfully')
            ];
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
        
        $output = ['success' => false,
                        'msg' => $e->getMessage()
                    ];
    }


       return redirect()->action('AccountController@account_book_list')->with('status',$output); 
    }
    


    // 

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
       if (!auth()->user()->can('accounts.access')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = session()->get('user.business_id');
        // $account_types = AccountType::where('business_id', $business_id)
        //                             //  ->whereNull('parent_account_type_id')
        //                              ->with(['sub_types'])
        //                              ->get();
                                     
        $account_types = DB::table('account_types as a')
                            ->leftJoin('account_types as b', 'a.id', '=', 'b.parent_account_type_id')
                            ->select('a.*')
                            ->whereNull('b.id')
                            ->get();
                            
        return view('account.create')
                ->with(compact('account_types'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        
        $date=Business::first();
        $currentYear = date('Y'); 
        $selectedMonth = str_pad($date->fy_start_month, 2, '0', STR_PAD_LEFT);


        if (request()->ajax()) {
            try {

                $is_allow=$request->input('is_allow');
                $input                     = $request->only(['name', 'account_number', 'note', 'account_type_id', 'account_details']);
                $business_id               = $request->session()->get('user.business_id');
                $user_id                   = $request->session()->get('user.id');
                $input['business_id']      = $business_id;
                $input['created_by']       = $user_id;
                $input['is_allow_customer']= $is_allow;
               
                $account = Account::create($input);

                //Opening Balance
                $opening_bal = $request->input('opening_balance');
                if (!empty($opening_bal)) {
                    $ob_transaction_data = [
                        'amount' =>$this->commonUtil->num_uf($opening_bal),
                        'account_id' => $account->id,
                        // 'type' => $type,
                        'type' => $request->input('type'),
                        'sub_type' => 'opening_balance',
                        'operation_date' => \Carbon::parse(session()->get("financial_year.start"))->subDay()->toDateString(),
                        'created_by' => $user_id
                    ];

                    AccountTransaction::createAccountTransaction($ob_transaction_data);
                }

                

                if($is_allow == 1)
                { 

                    $contact =new Contact;
                    $contact->business_id=$business_id;
                    $contact->type="customer";
                    $contact->supplier_business_name=$request->input('name');
                    $contact->account_head=$request->input('account_type_id');
                    $contact->is_allow=$is_allow;
                    $contact->contact_id=$request->input('account_number');
                    $contact->save();

                    $account = Account::where('id',$account->id)->first();
                    $account->contact_id=$contact->id;
                    $account->save();

                }
            

             
                
                $output = ['success' => true,
                            'msg' => __("account.account_created_success")
                        ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                    
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                            ];
            }

            return $output;
        }
    }

    /**
     * Show the specified resource.
     * @return Response
     */
     
    public function show($id){
       
        $business_id = 1;

        
        $date=Business::first();
        $currentYear = date('Y'); 
        $selectedMonth = str_pad($date->fy_start_month, 2, '0', STR_PAD_LEFT);


        if (request()->ajax()) {
            $start_date = request()->input('start_date');
            $end_date = request()->input('end_date');

            $before_bal_query = AccountTransaction::join(
                'accounts as A',
                'account_transactions.account_id',
                '=',
                'A.id'
            )
                    ->where('A.business_id', $business_id)
                    ->where('A.id', $id)
                    ->select([
                        DB::raw('SUM(IF(account_transactions.type="credit", account_transactions.amount, -1 * account_transactions.amount)) as prev_bal')])
                    ->where('account_transactions.operation_date', '<', $start_date)
                    ->whereNull('account_transactions.deleted_at');
            if (!empty(request()->input('type'))) {
                $before_bal_query->where('account_transactions.type', request()->input('type'));
            }
            $bal_before_start_date = $before_bal_query->first()->prev_bal;

            $accounts = AccountTransaction::join(
                'accounts as A',
                'account_transactions.account_id',
                '=',
                'A.id'
            )
            ->leftJoin('transaction_payments AS tp', 'account_transactions.transaction_payment_id', '=', 'tp.id')
            ->leftJoin('users AS u', 'account_transactions.created_by', '=', 'u.id')
            ->leftJoin('contacts AS c', 'tp.payment_for', '=', 'c.id')
                            ->where('A.business_id', $business_id)
                            ->where('A.id', $id)
                            ->with(['transaction', 'transaction.contact', 'transfer_transaction', 'media', 'transfer_transaction.media'])
                            ->select(['account_transactions.type', 'account_transactions.amount', 'operation_date',
                                'sub_type', 'transfer_transaction_id',
                                'A.id as account_id',
                                'account_transactions.transaction_id',
                                'account_transactions.id',
                                'account_transactions.note',
                                'account_transactions.document as document_no',
                                'account_transactions.description',
                                'account_transactions.against_id',
                                'tp.is_advance',
                                'tp.payment_ref_no',
                                'c.name as payment_for',
                                'account_transactions.reff_no as ref_no',
                                DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by"),
                                // DB::raw("CASE WHEN sub_type = 'opening_balance' THEN 0 ELSE 1 END as custom_order")
                                ])
                            //  ->groupBy('account_transactions.id')
                            //  ->orderBy('custom_order', 'asc')
                             ->orderBy('account_transactions.operation_date', 'asc')
                             ->orderByRaw("CASE
                                    WHEN account_transactions.reff_no LIKE 'sell-%' THEN 1
                                    WHEN account_transactions.reff_no LIKE 'sell-return-%' THEN 2
                                    WHEN account_transactions.reff_no LIKE 'purchase-%' THEN 3
                                    WHEN account_transactions.reff_no LIKE 'purchase-return-%' THEN 4
                                    WHEN account_transactions.reff_no LIKE 'CRV-%' THEN 5
                                    WHEN account_transactions.reff_no LIKE 'CPV-%' THEN 6
                                    WHEN account_transactions.reff_no LIKE 'AB-%' THEN 7
                                    WHEN account_transactions.reff_no LIKE 'J-%' THEN 8
                                    ELSE 8
                                END");
                            
            if (!empty(request()->input('type'))) {
                $accounts->where('account_transactions.type', request()->input('type'));
            }
            
            if (!empty($start_date) && !empty($end_date)) {
                $accounts->whereDate('operation_date', '>=', $start_date)
                         ->whereDate('operation_date', '<=', $end_date);
            }
            
            // $accounts->where(function ($query) {
                // $query->where('account_transactions.sub_type', '!=', 'opening_balance');
            // });
            
            $accounts = $accounts->get();
            
            // if(request()->input('is_filter')){
                // $financial_year_start = date("$currentYear-$selectedMonth-01");
                $financial_year_start = \Carbon::parse(session()->get("financial_year.start"))->subDay()->toDateString();
                $opening = AccountTransaction::selectRaw('*')
                        ->where('account_id', $id)
                        ->when($start_date != $financial_year_start, function ($query) use ($start_date) {
                            return $query->whereDate('operation_date', '<', $start_date);
                        }, function ($query) use ($start_date) {
                            return $query->whereDate('operation_date', '<=', $start_date)->where('sub_type','opening_balance');
                        })
                        ->whereNull('deleted_at')
                        ->orderBy('account_transactions.operation_date', 'desc')
                        // ->groupBy('account_id')
                        ->get();
            // }
            // For getting opening balance row
           
            
            // For getting only opening balance 
            $opening_balance = AccountTransaction::selectRaw('(SUM(CASE WHEN type = "debit" THEN amount ELSE 0 END) - SUM(CASE WHEN type = "credit" THEN amount ELSE 0 END)) AS opening_balance')
                    ->where('account_id', $id)
                    ->when($start_date != $financial_year_start, function ($query) use ($start_date) {
                        return $query->whereDate('operation_date', '<', $start_date);
                    }, function ($query) use ($start_date) {
                        return $query->whereDate('operation_date', '<=', $start_date)->where('sub_type','opening_balance');
                    })
                    ->whereNull('deleted_at')
                    ->orderBy('account_transactions.id', 'desc')
                    ->groupBy('account_id')
                    ->first();
            // dd($opening_balance);

            // For pending cheques
            $pendingCheques = PostDatedCheque::leftjoin('post_dated_cheque_lines','post_dated_cheque_lines.post_dated_cheque_id','=','post_dated_cheques.id')
            ->join('banks', 'banks.id','=','post_dated_cheque_lines.bank_id')
            ->join('accounts', 'accounts.id','=','post_dated_cheque_lines.account_id')
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
            )->where(['post_dated_cheque_lines.account_id' => $id, 'status' => 0])->get();
            
            return view('account.show_partial')->with(compact('accounts','opening','opening_balance', 'pendingCheques'));
        }
        
        $account = Account::where('business_id', $business_id)
                        ->with(['account_type', 'account_type.parent_account'])
                        ->findOrFail($id);

        return view('account.show')->with(compact('account'));
    }
     
     
    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('accounts.edit')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $account = Account::where('business_id', $business_id)->find($id);
            
            $acc_transaction = AccountTransaction::where('account_id',$account->id)->where('sub_type','opening_balance')->first();
            $account_types = AccountType::where('business_id', $business_id)->with(['sub_types'])->get();
           
            return view('account.edit')
                ->with(compact('account', 'account_types', 'acc_transaction'));
        }
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        
        
        $date=Business::first();
        $currentYear = date('Y'); 
        $selectedMonth = str_pad($date->fy_start_month, 2, '0', STR_PAD_LEFT);


        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'account_number', 'note', 'account_type_id', 'account_details']);

                $is_allow=$request->input('is_allow');
                $input['is_allow_customer']= $is_allow;
                $business_id = request()->session()->get('user.business_id');
                $account = Account::where('business_id', $business_id)
                            ->findOrFail($id);
                $account->name = $input['name'];
                $account->account_number = $input['account_number'];
                $account->note = $input['note'] ?? '';
                $account->account_type_id = $input['account_type_id'];
                $account->account_details = $input['account_details'] ?? '';
                $account->save();
                
                //Opening Balance
                $user_id = $request->session()->get('user.id');
                $opening_bal = $request->input('opening_balance');
                $acc_transaction = AccountTransaction::where('account_id',$account->id)->where('sub_type','opening_balance')->first();

                if(isset($acc_transaction)){
                      $acc_transaction->type    = $request->input('type');
                      $acc_transaction->amount  = $opening_bal;
                       $acc_transaction->operation_date= \Carbon::parse(session()->get("financial_year.start"))->subDay()->toDateString();
                      $acc_transaction->save();
                }else{ 
                   $ob_transaction_data = [
                        'amount' =>$this->commonUtil->num_uf($opening_bal),
                        'account_id' => $account->id,
                        'type' => $request->input('type'),
                        'sub_type' => 'opening_balance',
                        'operation_date' => \Carbon::parse(session()->get("financial_year.start"))->subDay()->toDateString(),
                        'created_by' => $user_id
                    ];
                    AccountTransaction::createAccountTransaction($ob_transaction_data);
                }
                
           
               if ($is_allow == 1) {
                    $contact = Contact::where('id', $account->contact_id)->first();
                    if ($contact) {
                        $contact->business_id = $business_id;
                        $contact->supplier_business_name = $request->input('name');
                        $contact->account_head = $request->input('account_type_id');
                        // $contact->is_allow=$is_allow;
                        $contact->contact_id = $request->input('account_number');
                        $contact->save();
                    } else {
                        $contact = new Contact;
                        $contact->business_id = $business_id;
                        $contact->type = "customer";
                        $contact->supplier_business_name = $request->input('name');
                        $contact->account_head = $request->input('account_type_id');
                        $contact->is_allow = $is_allow;
                        $contact->contact_id = $request->input('account_number');
                        $contact->deleted_at = null;
                        $contact->save();

                        $accounts = Account::where('id', $account->id)->first();
                        $accounts->contact_id = $contact->id;
                        $accounts->is_allow_customer = 1;
                        $accounts->save();
                    }
                } else {
                    $contact = Contact::where('id', $account->contact_id)->first();
                    if ($contact && $contact->is_allow == 1) {
                        // $contact->is_allow= 0;
                        $transaction = Transaction::where('contact_id', $account->contact_id);
                        if($transaction->count() == 0){
                            $contact->deleted_at = now();
                            $contact->save();
                            $account = Account::where('id', $account->id)->first();
                            $account->contact_id = null;
                            $account->is_allow_customer = 0;
                            $account->save();
                        }else{
                            throw new \Exception("Transaction exist!...");
                        }
                    }
                }

                
                $output = ['success' => true,
                                'msg' => __("account.account_updated_success")
                                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            // 'msg' => __("messages.something_went_wrong")
                            'msg' => $e->getMessage()
                        ];
            }
            
            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroyAccountTransaction($id)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $account_transaction = AccountTransaction::findOrFail($id);
                
                if (in_array($account_transaction->sub_type, ['fund_transfer', 'deposit'])) {
                    //Delete transfer transaction for fund transfer
                    if (!empty($account_transaction->transfer_transaction_id)) {
                        $transfer_transaction = AccountTransaction::findOrFail($account_transaction->transfer_transaction_id);
                        $transfer_transaction->delete();
                    }
                    $account_transaction->delete();
                }

                $output = ['success' => true,
                            'msg' => __("lang_v1.deleted_success")
                            ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }

    /**
     * Closes the specified account.
     * @return Response
     */
    public function close($id)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        
        if (request()->ajax()) {
            try {
                $business_id = session()->get('user.business_id');
            
                $account = Account::where('business_id', $business_id)
                                                    ->findOrFail($id);
                $account->is_closed = 1;
                $account->save();

                $output = ['success' => true,
                                    'msg' => __("account.account_closed_success")
                                    ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }
            
            return $output;
        }
    }

    public function destroy($id){
        $is_admin = $this->moduleUtil->is_admin(auth()->user());
        if (!$is_admin) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            try {
                $check = AccountTransaction::where('account_id', $id)->count();
                if($check == 0){
                    $account = Account::where('id', $id)->delete();
                    $output = ['success' => true, 'msg' => 'Account deleted successfully.'];
                }else{
                    $output = ['success' => false, 'msg' => 'Transaction Exist of this account'];
                }

            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
            }
            
            return $output;
        }
    }

    public function closeAll($ids = null)
    {
        if (request()->ajax()) {
            $idsArray = explode(',',$ids);
            try {
                $business_id = session()->get('user.business_id');
                $account = Account::where('business_id', $business_id)
                                    ->whereIn('id', $idsArray)
                                    ->update(['is_closed' => 1]);
                $output = ['success' => true, 'msg' => __("account.account_closed_success")];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
            }
            
            return $output;
        }
    }

    public function ActiveAll($ids)
    {
        if (request()->ajax()) {
            $idsArray = explode(',',$ids);
            try {
                $business_id = session()->get('user.business_id');
                $account = Account::where('business_id', $business_id)
                                    ->whereIn('id', $idsArray)
                                    ->update(['is_closed' => 0]);
                $output = ['success' => true, 'msg' => __("account.account_closed_success")];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
            }
            
            return $output;
        }
    }


    /**
     * Shows form to transfer fund.
     * @param  int $id
     * @return Response
     */
    public function getFundTransfer($id)
    {
       
        
        if (request()->ajax()) {
            $business_id = 1;
            
            $from_account = Account::where('business_id', $business_id)
                            ->NotClosed()
                            ->find($id);

            $to_accounts = Account::where('business_id', $business_id)
                            ->where('id', '!=', $id)
                            ->NotClosed()
                            ->pluck('name', 'id');

            return view('account.transfer')
                ->with(compact('from_account', 'to_accounts'));
        }
    }

    /**
     * Transfers fund from one account to another.
     * @return Response
     */
    public function postFundTransfer(Request $request)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            $business_id = session()->get('user.business_id');

            $amount = $this->commonUtil->num_uf($request->input('amount'));
            $from = $request->input('from_account');
            $to = $request->input('to_account');
            $note = $request->input('note');
            if (!empty($amount)) {
                $debit_data = [
                    'amount' => $amount,
                    'account_id' => $from,
                    'type' => 'debit',
                    'sub_type' => 'fund_transfer',
                    'created_by' => session()->get('user.id'),
                    'note' => $note,
                    'transfer_account_id' => $to,
                    'operation_date' => $this->commonUtil->uf_date($request->input('operation_date'), true),
                ];

                DB::beginTransaction();
                $debit = AccountTransaction::createAccountTransaction($debit_data);

                $credit_data = [
                        'amount' => $amount,
                        'account_id' => $to,
                        'type' => 'credit',
                        'sub_type' => 'fund_transfer',
                        'created_by' => session()->get('user.id'),
                        'note' => $note,
                        'transfer_account_id' => $from,
                        'transfer_transaction_id' => $debit->id,
                        'operation_date' => $this->commonUtil->uf_date($request->input('operation_date'), true),
                    ];

                $credit = AccountTransaction::createAccountTransaction($credit_data);

                $debit->transfer_transaction_id = $credit->id;
                $debit->save();

                Media::uploadMedia($business_id, $debit, $request, 'document');

                DB::commit();
            }
            
            $output = ['success' => true,
                                'msg' => __("account.fund_transfered_success")
                                ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
        
            $output = ['success' => false,
                        'msg' => __("messages.something_went_wrong")
                    ];
        }

        return redirect()->action('AccountController@index')->with('status', $output);
    }

    /**
     * Shows deposit form.
     * @param  int $id
     * @return Response
     */
    public function getDeposit($id)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        
        if (request()->ajax()) {
            $business_id = session()->get('user.business_id');
            
            $account = Account::where('business_id', $business_id)
                            ->NotClosed()
                            ->find($id);

            $from_accounts = Account::where('business_id', $business_id)
                            ->where('id', '!=', $id)
                            // ->where('account_type', 'capital')
                            ->NotClosed()
                            ->pluck('name', 'id');

            return view('account.deposit')
                ->with(compact('account', 'account', 'from_accounts'));
        }
    }

    /**
     * Deposits amount.
     * @param  Request $request
     * @return json
     */
    public function postDeposit(Request $request)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = session()->get('user.business_id');

            $amount = $this->commonUtil->num_uf($request->input('amount'));
            $account_id = $request->input('account_id');
            $note = $request->input('note');

            $account = Account::where('business_id', $business_id)
                            ->findOrFail($account_id);

            if (!empty($amount)) {
                $credit_data = [
                    'amount' => $amount,
                    'account_id' => $account_id,
                    'type' => 'credit',
                    'sub_type' => 'deposit',
                    'operation_date' => $this->commonUtil->uf_date($request->input('operation_date'), true),
                    'created_by' => session()->get('user.id'),
                    'note' => $note
                ];
                $credit = AccountTransaction::createAccountTransaction($credit_data);

                $from_account = $request->input('from_account');
                if (!empty($from_account)) {
                    $debit_data = $credit_data;
                    $debit_data['type'] = 'debit';
                    $debit_data['account_id'] = $from_account;
                    $debit_data['transfer_transaction_id'] = $credit->id;

                    $debit = AccountTransaction::createAccountTransaction($debit_data);

                    $credit->transfer_transaction_id = $debit->id;

                    $credit->save();
                }
            }
            
            $output = ['success' => true,
                                'msg' => __("account.deposited_successfully")
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
     * Calculates account current balance.
     * @param  int $id
     * @return json
     */
    public function getAccountBalance($id)
    {
        if (!auth()->user()->can('accounts.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = session()->get('user.business_id');
        $account = Account::leftjoin(
            'account_transactions as AT',
            'AT.account_id',
            '=',
            'accounts.id'
        )
            ->whereNull('AT.deleted_at')
            ->where('accounts.business_id', $business_id)
            ->where('accounts.id', $id) 
            // ->select('accounts.*', DB::raw("SUM( IF(AT.type='credit', amount, -1 * amount) ) as balance"))
            // ->select('accounts.*','AT.type', DB::raw("amount as balance"))
            ->select('accounts.*','AT.type', DB::raw('SUM(IF(AT.type = "debit", AT.amount, 0)) - SUM(IF(AT.type = "credit", AT.amount, 0)) AS balance'))
            ->first();
  
        return $account;
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function cashFlow()
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $accounts = AccountTransaction::join(
                'accounts as A',
                'account_transactions.account_id',
                '=',
                'A.id'
                )
                ->leftjoin(
                    'transaction_payments as TP',
                    'account_transactions.transaction_payment_id',
                    '=',
                    'TP.id'
                )
                ->where('A.business_id', $business_id)
                ->with(['transaction', 'transaction.contact', 'transfer_transaction'])
                ->select(['type', 'account_transactions.amount', 'operation_date',
                    'sub_type', 'transfer_transaction_id',
                    'account_transactions.transaction_id',
                    'account_transactions.id',
                    'A.name as account_name',
                    'TP.payment_ref_no as payment_ref_no',
                    'account_transactions.account_id'
                    ])
                 ->groupBy('account_transactions.id')
                 ->orderBy('account_transactions.operation_date', 'desc');
            if (!empty(request()->input('type'))) {
                $accounts->where('type', request()->input('type'));
            }

            $permitted_locations = auth()->user()->permitted_locations();
            $account_ids = [];
            if ($permitted_locations != 'all') {
                $locations = BusinessLocation::where('business_id', $business_id)
                                ->whereIn('id', $permitted_locations)
                                ->get();

                foreach ($locations as $location) {
                    if (!empty($location->default_payment_accounts)) {
                        $default_payment_accounts = json_decode($location->default_payment_accounts, true);
                        foreach ($default_payment_accounts as $key => $account) {
                            if (!empty($account['is_enabled']) && !empty($account['account'])) {
                                $account_ids[] = $account['account'];
                            }
                        }
                    }
                }

                $account_ids = array_unique($account_ids);
            }

            if ($permitted_locations != 'all') {
                $accounts->whereIn('A.id', $account_ids);
            }

            $location_id = request()->input('location_id');
            if (!empty($location_id)) {
                $location = BusinessLocation::find($location_id);
                if (!empty($location->default_payment_accounts)) {
                    $default_payment_accounts = json_decode($location->default_payment_accounts, true);
                    $account_ids = [];
                    foreach ($default_payment_accounts as $key => $account) {
                        if (!empty($account['is_enabled']) && !empty($account['account'])) {
                            $account_ids[] = $account['account'];
                        }
                    }

                    $accounts->whereIn('A.id', $account_ids);
                }
            }

            if (!empty(request()->input('account_id'))) {
                $accounts->where('A.id', request()->input('account_id'));
            }

            $start_date = request()->input('start_date');
            $end_date = request()->input('end_date');
            
            if (!empty($start_date) && !empty($end_date)) {
                $accounts->whereBetween(DB::raw('date(operation_date)'), [$start_date, $end_date]);
            }

            return DataTables::of($accounts)
                ->addColumn('debit', '@if($type == "debit")@format_currency($amount)@endif')
                ->addColumn('credit', '@if($type == "credit")@format_currency($amount)@endif')
                ->addColumn('balance', function ($row) {      
                    $balance = AccountTransaction::where('account_id', 
                                        $row->account_id)
                                    ->where('operation_date', '<=', $row->operation_date)
                                    ->whereNull('deleted_at')
                                    ->select(DB::raw("SUM(IF(type='credit', amount, -1 * amount)) as balance"))
                                    ->first()->balance;

                    return $this->commonUtil->num_f($balance, true);
                })
                ->editColumn('operation_date', function ($row) {
                    return $this->commonUtil->format_date($row->operation_date, true);
                })
                ->editColumn('sub_type', function ($row) {
                    return $this->__getPaymentDetails($row);
                })
                ->removeColumn('id')
                ->rawColumns(['credit', 'debit', 'balance', 'sub_type'])
                ->make(true);
        }
        $accounts = Account::forDropdown($business_id, false);

        $business_locations = BusinessLocation::forDropdown($business_id, true);
                            
        return view('account.cash_flow')
                 ->with(compact('accounts', 'business_locations'));
    }

    public function __getPaymentDetails($row)
    {
        
        $details = '';
        if (!empty($row->sub_type)) {
            // $details = __('account.' . $row->sub_type);
            if (in_array($row->sub_type, ['fund_transfer', 'deposit']) && !empty($row->transfer_transaction)) {
                if ($row->type == 'credit') {
                    $details .= ' ( ' . __('account.from') .': ' . $row->transfer_transaction->account->name . ')';
                } else {
                    $details .= ' ( ' . __('account.to') .': ' . $row->transfer_transaction->account->name . ')';
                }
            }
        } else {
            if (!empty($row->transaction->type)) {
                if ($row->transaction->type == 'purchase') {
                    $details = 
                     '</b> <a href="#" data-href="' . action("PurchaseController@show", [$row->transaction->id]) . '" class="btn-modal" data-container=".view_modal">' . $row->transaction->ref_no . '</a>';
                }elseif ($row->transaction->type == 'expense') {
                    $details = __('lang_v1.expense') . '<br><b>' . __('purchase.ref_no') . ':</b>' . $row->transaction->ref_no;
                } elseif ($row->transaction->type == 'sell') {
                    $details = 
                     '</b> <a href="#" data-href="' . action("SellController@show", [$row->transaction->id]) . '" class="btn-modal" data-container=".view_modal">' . $row->transaction->ref_no . '</a>';
                } elseif ($row->transaction->type == 'sale_invoice') {
                    $details = 
                     '</b> <a href="#" data-href="' . action("SellController@show", [$row->transaction->id]) . '" class="btn-modal" data-container=".view_modal">' . $row->transaction->ref_no . '</a>';
                }
                elseif ($row->transaction->type == 'Purchase_invoice') {
                    $details = 
                     '</b> <a href="#" data-href="' . action("PurchaseOrderController@show", [$row->transaction->id]) . '" class="btn-modal" data-container=".view_modal">' . $row->transaction->ref_no . '</a>';
                }elseif ($row->transaction->type == 'sale_return_invoice') {
                    $details = 
                     '</b> <a href="#" data-href="' . action("SellController@show", [$row->transaction->id]) . '" class="btn-modal" data-container=".view_modal">' . $row->transaction->ref_no . '</a>';
                }elseif ($row->transaction->type == 'purchase_return') {
                    $details = 
                     '</b> <a href="#" data-href="' . action("PurchaseReturnController@show", [$row->transaction->id]) . '" class="btn-modal" data-container=".view_modal">' . $row->transaction->ref_no . '</a>';
                }
            }
        }

        if (!empty($row->payment_ref_no)) {
            if (!empty($details)) {
                $details .= '<br/>';
            }

            $details .= '<b>' . __('lang_v1.pay_reference_no') . ':</b> ' . $row->payment_ref_no;
        }
        if (!empty($row->payment_for)) {
            if (!empty($details)) {
                $details .= '<br/>';
            }

            $details .= '<b>' . __('account.payment_for') . ':</b> ' . $row->payment_for;
        }

        if ($row->is_advance == 1) {
            $details .= '<br>(' . __('lang_v1.advance_payment') . ')';
        }

        return $details;
    }

    /**
     * activate the specified account.
     * @return Response
     */
    public function activate($id)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        
        if (request()->ajax()) {
            try {
                $business_id = session()->get('user.business_id');
            
                $account = Account::where('business_id', $business_id)
                                ->findOrFail($id);

                $account->is_closed = 0;
                $account->save();

                $output = ['success' => true,
                        'msg' => __("lang_v1.success")
                        ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }
            
            return $output;
        }
    }
    
    
    
    public function account_number($id){
        $getchild = DB::table('account_types')->where('parent_account_type_id', $id)->get();
        $parent = DB::table('account_types')->where('id', $id)->first();
        if (count($getchild) > 0) {
                $child_max_code = DB::table('account_types')
                    ->where('parent_account_type_id', $id)
                    ->max('code');
                $code = ($child_max_code + 1) ;
        }else{
            
            $code = $parent->code . '001';
        }
        echo $code;
    }
    
    
    // public function transaction_account_number($id){
    //     $getparent = DB::table('account_types')->where('id', $id)->get();
    //     if (count($getparent) > 0) {
    //             $child_max_code = DB::table('accounts')
    //                 ->where('account_type_id', $id)
    //                 // ->where('is_closed', 0)
    //                 // ->whereNull('deleted_at')
    //                 ->max('account_number');
    //             $code = ($child_max_code + 1) ;
    //     }else{
    //         $code = $getparent[0]->code . '0001';
    //     }
    //     echo $code;
    // }
    
    
    public function transaction_account_number($id){
        
        $accountType = AccountType::findOrFail($id);
        $accountTypeCode = $accountType->sub_types()->max('code'); 
        $accountCode = $accountType->accounts()->where('is_closed', 0)->whereNull('deleted_at')->max('account_number');
        $newCode = max($accountTypeCode,$accountCode);
        if (!$newCode) {
            return $accountType->code . '0001';
        }
        echo ($newCode + 1);
    }
    
    public function get_newCode($id){
        $accountType = AccountType::findOrFail($id);
        $accountTypeCode = $accountType->sub_types()->max('code'); 
        $accountCode = $accountType->accounts()->where('is_closed', 0)->whereNull('deleted_at')->max('account_number');
        $newCode = max($accountTypeCode,$accountCode);
        if (!$newCode) {
            return $accountType->code . '01';
        }
        echo ($newCode + 1);
    }
    
    public function detail_show($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $date = Business::first();
        $currentYear = date('Y');
        $selectedMonth = str_pad($date->fy_start_month, 2, '0', STR_PAD_LEFT);


        if (request()->ajax()) {
            $start_date = request()->input('start_date');
            $end_date = request()->input('end_date');

            $before_bal_query = AccountTransaction::join(
                'accounts as A',
                'account_transactions.account_id',
                '=',
                'A.id'
            )
                ->where('A.business_id', $business_id)
                ->where('A.id', $id)
                ->select([
                    DB::raw('SUM(IF(account_transactions.type="credit", account_transactions.amount, -1 * account_transactions.amount)) as prev_bal')
                ])
                ->where('account_transactions.operation_date', '<', $start_date)
                ->whereNull('account_transactions.deleted_at');
            if (!empty(request()->input('type'))) {
                $before_bal_query->where('account_transactions.type', request()->input('type'));
            }
            $bal_before_start_date = $before_bal_query->first()->prev_bal;

            $accounts = AccountTransaction::join(
                'accounts as A',
                'account_transactions.account_id',
                '=',
                'A.id'
            )
                ->leftJoin('transaction_payments AS tp', 'account_transactions.transaction_payment_id', '=', 'tp.id')
                ->leftJoin('users AS u', 'account_transactions.created_by', '=', 'u.id')
                ->leftJoin('contacts AS c', 'tp.payment_for', '=', 'c.id')
                ->where('A.business_id', $business_id)
                ->where('A.id', $id)
                ->with(['transaction', 
                'transaction.sell_lines.product', 'transaction.purchase_lines.product',
                'transaction.sell_lines.line_tax', 'transaction.sell_lines.further_taxs',
                'transaction.purchase_lines.line_tax', 'transaction.purchase_lines.further_taxs',
                'transaction.contact', 'transfer_transaction', 'media', 'transfer_transaction.media'])
                ->select([
                    'account_transactions.type', 'account_transactions.amount', 'operation_date',
                    'sub_type', 'transfer_transaction_id',
                    'A.id as account_id',
                    'A.contact_id as contact_id',
                    'account_transactions.transaction_id',
                    'account_transactions.id',
                    'account_transactions.note',
                    'account_transactions.document as document_no',
                    'account_transactions.description',
                    'account_transactions.against_id',
                    'tp.is_advance',
                    'tp.payment_ref_no',
                    'c.name as payment_for',
                    'account_transactions.reff_no as ref_no',
                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by"),
                ])
                ->orderBy('account_transactions.operation_date', 'asc');

            if (!empty(request()->input('type'))) {
                $accounts->where('account_transactions.type', request()->input('type'));
            }

            if (!empty($start_date) && !empty($end_date)) {
                $accounts->whereDate('operation_date', '>=', $start_date)
                    ->whereDate('operation_date', '<=', $end_date);
            }

            $accounts = $accounts->get();
            
            $financial_year_start = \Carbon::parse(session()->get("financial_year.start"))->subDay()->toDateString();
            $opening = AccountTransaction::selectRaw('*')
                ->where('account_id', $id)
                ->when($start_date != $financial_year_start, function ($query) use ($start_date) {
                    return $query->whereDate('operation_date', '<', $start_date);
                }, function ($query) use ($start_date) {
                    return $query->whereDate('operation_date', '<=', $start_date)->where('sub_type', 'opening_balance');
                })
                ->whereNull('deleted_at')
                ->orderBy('account_transactions.operation_date', 'desc')
                ->get();

            // For getting only opening balance 
            $opening_balance = AccountTransaction::selectRaw('(SUM(CASE WHEN type = "debit" THEN amount ELSE 0 END) - SUM(CASE WHEN type = "credit" THEN amount ELSE 0 END)) AS opening_balance')
            ->where('account_id', $id)
            ->when($start_date != $financial_year_start, function ($query) use ($start_date) {
                return $query->whereDate('operation_date', '<', $start_date);
            }, function ($query) use ($start_date) {
                return $query->whereDate('operation_date', '<=', $start_date)->where('sub_type', 'opening_balance');
            })
                ->whereNull('deleted_at')
                ->orderBy('account_transactions.id', 'desc')
                ->groupBy('account_id')
                ->first();

            return view('account.detail_ledger_partial')->with(compact('accounts', 'opening', 'opening_balance'));
        }

        $account = Account::where('business_id', $business_id)
            ->with(['account_type', 'account_type.parent_account'])
            ->findOrFail($id);

        return view('account.detail_ledger')->with(compact('account'));
    }
    
    
    public function default_account(){ 
        $accounts         = Account::where('is_closed',0)->get(); 
        $contractors      = Contact::where('type','contracter')->get(); 
        $product_types    = Type::All();  
        $sale_invoice     = DB::table('default_account')->where('form_type','sale_invoice')->get();
        $sale_return      = DB::table('default_account')->where('form_type','sale_return_invoice')->get();
        $purcase_invoice  = DB::table('default_account')->where('form_type','Purchase_invoice')->get();
        $purcase_return   = DB::table('default_account')->where('form_type','purchase_return')->get();
        $tank             = DB::table('default_account')->where('form_type','tank')->get();
        $invoice_setting  = DB::table('invoice_setting')->first();
        $profitLoss       = DB::table('default_account')->where('form_type','profit&loss')->get();
        $balance_sheet    = DB::table('default_account')->where('form_type','balance_sheet')->get();
        $accountType      = DB::table('account_types as a')
                            ->leftJoin('account_types as b', 'a.id', '=', 'b.parent_account_type_id')
                            ->select('a.*')->whereNull('b.id')->get(); 

        return view('account.default_account')->with(compact('balance_sheet','accountType','accounts','tank','product_types','contractors','sale_invoice','sale_return','purcase_invoice','purcase_return','invoice_setting','profitLoss'));
    }
    
    public function default_acc_store(Request $request){
        
        try{
            DB::beginTransaction();
            DB::table('default_account')->where('form_type', $request->form_type)->delete();
            $account_id =  $request->account_id;
            $form_type  =  $request->form_type;
            $field_type =  $request->field;
            if($form_type == "profit&loss" || $form_type == "balance_sheet"){
                foreach($account_id as $key => $val){
                    foreach($val as $key2 => $val2){
                        $data = [
                            'account_id' => $val2,
                            'form_type'  => $form_type,
                            'field_type' => $key
                            ];
                        DB::table('default_account')->insert($data);
                    }
                }
            }else{
                foreach($account_id as $key => $val){
                    $data = [
                        'account_id' => $val,
                        'form_type'  => $form_type,
                        'field_type' => $field_type[$key]
                        ];
                    DB::table('default_account')->insert($data);
                }
            }
            
            DB::commit();
            
            $output = ['success' => true, 'msg' => "Success" ];
        } catch (\Exception $e) {
            
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            dd("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['error' => false, 'msg' => __("messages.something_went_wrong") ];
        }
        return redirect()->action('AccountController@default_account')->with($output);
    }
     public function defaultInvoice(Request $request)
     {
    try{
        DB::beginTransaction();
        $id      = $request->is_id;
        $address = $request->is_address;
        $logo    = $request->is_logo;
        $company = $request->is_Company_name;

   
        $invoice = Invoice_setting::where('id',$id)->first();
        $invoice->address= $address ??0;
        $invoice->logo= $logo ?? 0;
        $invoice->company= $company ?? 0;
        $invoice->save();
            
        DB::commit();
            
        $output = ['success' => true, 'msg' => "Success" ];
    } catch (\Exception $e) {
        
        DB::rollBack();
        \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
    
        $output = ['error' => false, 'msg' => __("messages.something_went_wrong") ];
    }
    return redirect()->action('AccountController@default_account')->with($output);

     }

     public function get_rcpt_total($transaction_id) {
        $total = DB::table('receipt_details')
                    ->where('transaction_id', $transaction_id)
                    ->sum('receipts');
    
        return $total;
    }

    
    
    public function get_sale_invoices(Request $request,$id)
    {

    $param=  $request->parameter;
    $data= Transaction::where('contact_id', $id)
    ->leftJoin('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
    ->leftJoin('tax_rates as sale_tax', 'transaction_sell_lines.tax_id', '=', 'sale_tax.id')
    ->leftJoin('tax_rates as further', 'transaction_sell_lines.further_tax', '=', 'further.id')
    ->leftJoin('receipt_details', function ($join) {
    
        $join->on(DB::raw('transactions.ref_no'), DB::raw('receipt_details.ref_no'));
    })
    ->groupBy('transactions.ref_no')
    ->whereIn('transactions.type', ['sale_invoice', 'purchase_invoice', 'purchase_return', 'sale_return'])
    ->select('transactions.*','transactions.id as t_id','receipt_details.receipt_id as pay_id','receipt_details.receipts as aging_amount','receipt_details.voucher_no as voucher_no','receipt_details.ref_no as receipt_ref','transaction_sell_lines.*', 'sale_tax.amount as sale_tax_amount', 'further.amount as further_tax_amount',
        DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price * (sale_tax.amount / 100)) as total_tax'),
        DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price * (further.amount / 100)) as total_further_tax'),
        DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price * (sale_tax.amount / 100)) + SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price * (further.amount / 100)) as total_combine_tax'),
        DB::raw('transactions.final_total as final_total')
    )->get();    


    $Tax=TaxRate::All();

    $transaction = []; 
 
        foreach ($data as $transactions) {
          
            $invamount = $this->get_rcpt_total($transactions->t_id);
            // dd($param);
         
            if ($transactions->aging_amount == $invamount AND $transactions->aging_amount < $invamount)
             {
              continue;
             }
            else
             {
              array_push($transaction, $transactions);
             }
        }


    return view('account.invoice_details',compact('transaction','Tax','param'));
    
    }
    
     public function invoice_check()
    {
       
        $contact=Contact::where('type',['customer','supplier'])->get();
        $Account_head=Account::get();
   
        return view('receipt_form.index',compact('contact','Account_head'));
        
    }
    
    public function save_invoice(Request $request)
    {
        
        $receipt=new Receipt;
        $receipt->customer_id=$request->customer_supplier_customer_id;
        $receipt->receipt_date=$request->receipt_date;
        $receipt->amount=$request->amount;
        $receipt->account_id=$request->account_head;
        $receipt->payment_type=$request->payment_type;
        $receipt->cheque_no=$request->cheque_no;
        $receipt->cheque_date=$request->cheque_date;

        $receipt->save();
     
     return redirect()->back();

        
    }
    
    public function ShowReceipts()
    {
   
      $data = Receipt::select('receipts.*', 'contacts.*')
    ->join('contacts', 'receipts.customer_id', '=', 'contacts.id')
    ->get();
      return view('receipt_form.show',compact('data'));
    }
    
    public function Aging_add(Request $request)
    {

    //    try{

        $get_row = $request->input('ref');

        $existing_receipt_detail = Receipt_detail::where('ref_no', $get_row)->delete();

                if (is_array($get_row)) {
                for ($i = 0; $i < sizeof($get_row); $i++)
                {

                    if($request->receipts[$i] == 0)
                    {
                    $existing_receipt_detail = Receipt_detail::where('receipt_id', $request->id_voucher)
                    ->where('ref_no', $get_row[$i])
                    ->delete();
                    }

                    if (!empty($request->receipts[$i]) && $request->receipts[$i] > 0 )
                    {
            
                   
                        // Create new record
                        $receipt_detail = new Receipt_detail;
                        $receipt_detail->receipt_id = $request->id_voucher;
                        $receipt_detail->ref_no = $get_row[$i];
                        $receipt_detail->advance_payments = $request->advance_payment;
                        $receipt_detail->final = $request->total[$i];
                        $receipt_detail->transaction_date = $request->date[$i];
                        $receipt_detail->receipts = $request->receipts[$i];
                        $receipt_detail->voucher_no = $request->voucher_no[$i];
                        $receipt_detail->transaction_id = $request->transaction_id[$i];
                        $receipt_detail->save();
                   
                    }
                    }
                }
                $output = ['success' => true, 'msg' => "Success" ];
            // } catch (\Exception $e) {
            
            //     DB::rollBack();
            //     \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            //     $output = ['error' => false, 'msg' => __("messages.something_went_wrong") ];
            // }
                return redirect()->back()->with('message',$output);
                
    }
    
}
