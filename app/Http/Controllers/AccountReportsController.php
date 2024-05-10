<?php

namespace App\Http\Controllers;

use App\Account;
use App\AccountType;
use App\Business;
use App\AccountTransaction;
use App\TransactionPayment;
use App\Utils\TransactionUtil;
use App\Utils\BusinessUtil;
use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\BusinessLocation;

class AccountReportsController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $transactionUtil;
    protected $businessUtil;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, BusinessUtil $businessUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function balanceSheet()
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());
        if ( !$is_admin && !auth()->user()->hasAnyPermission(['account.balance_sheet']) ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = session()->get('user.business_id');
        if (request()->ajax()) {
            $end_date = !empty(request()->input('end_date')) ? $this->transactionUtil->uf_date(request()->input('end_date')) : \Carbon::now()->format('Y-m-d');
            $location_id = !empty(request()->input('location_id')) ? request()->input('location_id') : null;

            $purchase_details = $this->transactionUtil->getPurchaseTotals(
                $business_id,
                null,
                $end_date,
                $location_id
            );
            $sell_details = $this->transactionUtil->getSellTotals(
                $business_id,
                null,
                $end_date,
                $location_id
            );

            $transaction_types = ['sell_return'];

            $sell_return_details = $this->transactionUtil->getTransactionTotals(
                $business_id,
                $transaction_types,
                null,
                $end_date,
                $location_id
            );

            $account_details = $this->getAccountBalance($business_id, $end_date, 'others', $location_id);
            // $capital_account_details = $this->getAccountBalance($business_id, $end_date, 'capital');

            //Get Closing stock
            $closing_stock = $this->transactionUtil->getOpeningClosingStock(
                $business_id,
                $end_date,
                $location_id
            );

            $output = [
                'supplier_due' => $purchase_details['purchase_due'],
                'customer_due' => $sell_details['invoice_due'] - $sell_return_details['total_sell_return_inc_tax'],
                'account_balances' => $account_details,
                'closing_stock' => $closing_stock,
                'capital_account_details' => null
            ];

            return $output;
        }

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('account_reports.balance_sheet')->with(compact('business_locations'));
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    // public function trialBalance()
    // {
    //     $is_admin = $this->businessUtil->is_admin(auth()->user());
    //     if ( !$is_admin && !auth()->user()->hasAnyPermission(['account.trial_balance']) ) {
    //         abort(403, 'Unauthorized action.');
    //     }

    //     $business_id = session()->get('user.business_id');

    //     if (request()->ajax()) {
    //         $end_date = !empty(request()->input('end_date')) ? $this->transactionUtil->uf_date(request()->input('end_date')) : \Carbon::now()->format('Y-m-d');
    //          $location_id = !empty(request()->input('location_id')) ? request()->input('location_id') : null;

    //         $purchase_details = $this->transactionUtil->getPurchaseTotals(
    //             $business_id,
    //             null,
    //             $end_date,
    //             $location_id
    //         );
    //         $sell_details = $this->transactionUtil->getSellTotals(
    //             $business_id,
    //             null,
    //             $end_date,
    //             $location_id
    //         );

    //         $account_details = $this->getAccountBalance($business_id, $end_date, 'others', $location_id);

    //         // $capital_account_details = $this->getAccountBalance($business_id, $end_date, 'capital');

    //         $output = [
    //             'supplier_due' => $purchase_details['purchase_due'],
    //             'customer_due' => $sell_details['invoice_due'],
    //             'account_balances' => $account_details,
    //             'capital_account_details' => null
    //         ];

    //         return $output;
    //     }

    //     $business_locations = BusinessLocation::forDropdown($business_id, true);

    //     return view('account_reports.trial_balance')->with(compact('business_locations'));
    // }
    
    public function trialBalance()
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());
        if ( !$is_admin && !auth()->user()->hasAnyPermission(['account.trial_balance']) ) {
            abort(403, 'Unauthorized action.');
        }
        
        $accounts = Account::All()->pluck('name','id');
        return view('account_reports.trial_balance')->with(compact('accounts'));
    }
    
    
    public function trial_balacnce_data(){
        $data = [
            'account_id' => request()->input('account_id'),
            'start_date' => request()->input('start_date'),
            'end_date'   => request()->input('end_date'),
            'is_zero'    => request()->input('is_zero')
        ];
        
        $parent_data = $this->get_parent($data);
        $parent_data_recursive = $this->get_parent_data_recursive($data, $parent_data);

        // dd($parent_data_recursive);
        return view('account_reports.trial_balance_data')->with(compact('parent_data_recursive'));
    }
    
    
    
    public function get_parent_data_recursive($data, $parent_data){
        
        foreach ($parent_data as $key => $val) {
            $sub_data = $this->get_sub_account($data, $val['id']);
            $parent_data[$key]['sub_account'] = $sub_data;
            
            $transaction_account = $this->get_transaction_account($data, $val['id']);
            $parent_data[$key]['transaction_account'] = $transaction_account;
            
  
            if (!empty($sub_data)) {
                $sub_data_recursive = $this->get_parent_data_recursive($data, $sub_data);
                $parent_data[$key]['sub_account'] = $sub_data_recursive;
            }
    
        }
        return $parent_data;
    }
    
    
    public function get_parent($data){
        
        $date_filter = " AND account_transactions.operation_date BETWEEN '{$data['start_date']}' AND '{$data['end_date']}' ";
        
            $parent = DB::select("
                SELECT
                    at.name,
                    at.id,
                    (
                        SELECT SUM(atx.amount)
                        FROM account_transactions AS atx
                        WHERE atx.account_id IN (
                            SELECT acc.id
                            FROM accounts AS acc
                            WHERE acc.account_type_id = at.id OR acc.account_type_id IN (
                                SELECT child.id
                                FROM account_types AS child
                                WHERE child.parent_account_type_id = at.id
                            )
                        )
                        AND atx.type = 'debit'
                        AND atx.deleted_at IS NULL
                    ) AS Debit,
                    (
                        SELECT SUM(atx.amount)
                        FROM account_transactions AS atx
                        WHERE atx.account_id IN (
                            SELECT acc.id
                            FROM accounts AS acc
                            WHERE acc.account_type_id = at.id OR acc.account_type_id IN (
                                SELECT child.id
                                FROM account_types AS child
                                WHERE child.parent_account_type_id = at.id
                            )
                        )
                        AND atx.type = 'credit'
                        AND atx.deleted_at IS NULL
                    ) AS Credit,
                    (
                        SELECT SUM(
                            CASE
                                WHEN atx.type = 'debit' THEN atx.amount
                                WHEN atx.type = 'credit' THEN -atx.amount
                                ELSE 0
                            END
                        )
                        FROM account_transactions AS atx
                        WHERE atx.account_id IN (
                            SELECT acc.id
                            FROM accounts AS acc
                            WHERE acc.account_type_id = at.id OR acc.account_type_id IN (
                                SELECT child.id
                                FROM account_types AS child
                                WHERE child.parent_account_type_id = at.id
                            )
                        )
                        AND atx.deleted_at IS NULL
                    ) AS balance
                FROM account_types AS at
                WHERE at.parent_account_type_id IS NULL;

            ");
        return json_decode(json_encode($parent), true);
    }
    
    public function get_sub_account($data, $parent_id){
        $date_filter = " AND account_transactions.operation_date BETWEEN '{$data['start_date']}' AND '{$data['end_date']}' ";
      $zero = "";
        if($data["is_zero"] == 0){
            $zero        = " AND (
                        SELECT SUM(IF(account_transactions.type = 'debit', account_transactions.amount, 0)) 
                        - SUM(IF(account_transactions.type = 'credit', account_transactions.amount, 0))
                        FROM account_transactions
                         WHERE account_transactions.account_id IN (
                            SELECT accounts.id
                            FROM accounts
                            WHERE accounts.account_type_id = account_types.id
                            $date_filter
                        )
                        AND account_transactions.deleted_at IS NULL
                      
                    ) != 0";
        }
        
        $sub_account = DB::select("
                SELECT
                    account_types.name,
                    account_types.id,
                    (
                    SELECT SUM(account_transactions.amount)
                        FROM account_transactions
                        WHERE account_transactions.account_id IN (
                            SELECT accounts.id
                            FROM accounts
                            WHERE accounts.account_type_id = account_types.id
                            $date_filter
                        )
                        AND account_transactions.type = 'debit'
                        AND account_transactions.deleted_at IS NULL
                    ) AS Debit,
                    (
                        SELECT SUM(account_transactions.amount)
                        FROM account_transactions
                        WHERE account_transactions.account_id IN (
                            SELECT accounts.id
                            FROM accounts
                            WHERE accounts.account_type_id = account_types.id
                            $date_filter
                        )
                        AND account_transactions.type = 'credit'
                        AND account_transactions.deleted_at IS NULL
                    ) AS Credit,
                    
                    (
                        SELECT SUM(IF(account_transactions.type = 'debit', account_transactions.amount, 0)) 
                        - SUM(IF(account_transactions.type = 'credit', account_transactions.amount, 0))
                        FROM account_transactions
                         WHERE account_transactions.account_id IN (
                            SELECT accounts.id
                            FROM accounts
                            WHERE accounts.account_type_id = account_types.id
                            $date_filter
                        )
                        AND account_transactions.deleted_at IS NULL
                    ) AS balance
                    
                FROM account_types
                WHERE parent_account_type_id = $parent_id $zero ;
            ");
            
        return json_decode(json_encode($sub_account), true);
    }
    
    
    
    
    
    public function get_transaction_account($data, $parent_id){
        $date_filter = " AND account_transactions.operation_date BETWEEN '{$data['start_date']}' AND '{$data['end_date']}' ";
        $zero = "";
        if($data["is_zero"] == 0){
            $zero        = "AND (
                        SELECT SUM(IF(account_transactions.type = 'debit', account_transactions.amount, 0)) 
                        - SUM(IF(account_transactions.type = 'credit', account_transactions.amount, 0))
                        FROM account_transactions
                         WHERE account_transactions.account_id = accounts.id
                        AND account_transactions.deleted_at IS NULL
                        $date_filter
                    ) != 0";
        }
        $acc_transaction = DB::select("
                SELECT
                    accounts.name,
                    accounts.id,
                    (
                    SELECT SUM(account_transactions.amount)
                        FROM account_transactions
                        WHERE account_transactions.account_id = accounts.id
                        AND account_transactions.type = 'debit'
                        AND account_transactions.deleted_at IS NULL
                        $date_filter
                        
                    ) AS Debit,
                    (
                        SELECT SUM(account_transactions.amount)
                        FROM account_transactions
                        WHERE account_transactions.account_id = accounts.id
                        AND account_transactions.type = 'credit'
                        AND account_transactions.deleted_at IS NULL
                        $date_filter
                    ) AS Credit,
                    
                    (
                        SELECT SUM(IF(account_transactions.type = 'debit', account_transactions.amount, 0)) 
                        - SUM(IF(account_transactions.type = 'credit', account_transactions.amount, 0))
                        FROM account_transactions
                         WHERE account_transactions.account_id = accounts.id
                        AND account_transactions.deleted_at IS NULL
                        $date_filter
                    ) AS balance
                    
                FROM accounts
                WHERE account_type_id = $parent_id $zero;
            ");
            
        return json_decode(json_encode($acc_transaction), true);
    }
    
    
    





















    // public function trial_balacnce_data(){   
    //     $data = [
    //         'account_id' => request()->input('account_id'),
    //         'start_date' => request()->input('start_date'),
    //         'end_date'   => request()->input('end_date')
    //     ];
        
    //     $parent_data = $this->get_parent($data);
        
    //   if (isset($data['account_id'])) {
    //         $filtered_data = $this->filter_data_by_account_ids($parent_data, $data['account_id']);
    //         $parent_data_recursive = $this->get_parent_data_recursive($data, $filtered_data);
    //     } else {
    //         $parent_data_recursive = $this->get_parent_data_recursive($data, $parent_data);
    //     }
        
    //     return view('account_reports.trial_balance_data')->with(compact('parent_data_recursive'));
    // }
    
    // public function get_parent($data)
    // {
    //     $date_filter = " AND account_transactions.operation_date BETWEEN '{$data['start_date']}' AND '{$data['end_date']}' ";
    
    //     $parent = DB::select("
    //         SELECT
    //             account_types.name,
    //             account_types.id,
    //             (
    //                 SELECT SUM(account_transactions.amount)
    //                 FROM account_transactions
    //                 WHERE account_transactions.account_id IN (
    //                     SELECT accounts.id
    //                     FROM accounts
    //                     WHERE accounts.account_type_id = account_types.id
    //                     $date_filter
    //                 )
    //                 AND account_transactions.type = 'debit'
    //             ) AS Debit,
    //             (
    //                 SELECT SUM(account_transactions.amount)
    //                 FROM account_transactions
    //                 WHERE account_transactions.account_id IN (
    //                     SELECT accounts.id
    //                     FROM accounts
    //                     WHERE accounts.account_type_id = account_types.id
    //                     $date_filter
    //                 )
    //                 AND account_transactions.type = 'credit'
    //             ) AS Credit
    //         FROM account_types
    //         WHERE parent_account_type_id IS NULL;
    //     ");
    
    //     return json_decode(json_encode($parent), true);
    // }
    // public function get_sub_account($data, $parent_id)
    // {
    //     $date_filter = " AND account_transactions.operation_date BETWEEN '{$data['start_date']}' AND '{$data['end_date']}' ";
    
    //     $sub_account = DB::select("
    //         SELECT
    //             sub_accounts.name,
    //             sub_accounts.id,
    //             (
    //                 SELECT SUM(account_transactions.amount)
    //                 FROM account_transactions
    //                 WHERE account_transactions.account_id = sub_accounts.id
    //                 AND account_transactions.type = 'debit'
    //                 $date_filter
    //             ) AS Debit,
    //             (
    //                 SELECT SUM(account_transactions.amount)
    //                 FROM account_transactions
    //                 WHERE account_transactions.account_id = sub_accounts.id
    //                 AND account_transactions.type = 'credit'
    //                 $date_filter
    //             ) AS Credit
    //         FROM accounts AS sub_accounts
    //         WHERE sub_accounts.parent_id = $parent_id;
    //     ");
    
    //     return json_decode(json_encode($sub_account), true);
    // }


    // public function get_parent_data_recursive($data, $parent_data)
    // {
    //     foreach ($parent_data as $key => $val) {
    //         $sub_data = $this->get_sub_account($data, $val['id']);
    //         $parent_data[$key]['sub_account'] = $sub_data;
    
    //         if (!empty($sub_data)) {
    //             $sub_data_recursive = $this->get_parent_data_recursive($data, $sub_data);
    //             $parent_data[$key]['sub_account'] = $sub_data_recursive;
    //         }
    //     }
    
    //     return $parent_data;
    // }

    
    // public function filter_data_by_account_ids($parent_data, $account_ids){
    //     $filtered_data = [];
    
    //     foreach ($parent_data as $parent) {
    //         $sub_data = $parent['sub_account'] ?? [];
    //         if (!empty($sub_data)) {
    //             $filtered_sub_data = $this->filter_data_by_account_ids($sub_data, $account_ids);
    //             if (!empty($filtered_sub_data)) {
    //                 $parent['sub_account'] = $filtered_sub_data;
    //                 $filtered_data[] = $parent;
    //             }
    //         }
    
    //         if (in_array($parent['id'], $account_ids)) {
    //             $filtered_data[] = $parent;
    //         }
    //     }
    
    //     return $filtered_data;
    // }













    /**
     * Retrives account balances.
     * @return Obj
     */
    private function getAccountBalance($business_id, $end_date, $account_type = 'others', $location_id = null)
    {
        $query = Account::leftjoin(
            'account_transactions as AT',
            'AT.account_id',
            '=',
            'accounts.id'
        )
                                // ->NotClosed()
                                ->whereNull('AT.deleted_at')
                                ->where('business_id', $business_id)
                                ->whereDate('AT.operation_date', '<=', $end_date);

        // if ($account_type == 'others') {
        //    $query->NotCapital();
        // } elseif ($account_type == 'capital') {
        //     $query->where('account_type', 'capital');
        // }

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
            $query->whereIn('accounts.id', $account_ids);
        }

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

                $query->whereIn('accounts.id', $account_ids);
            }
        }

        $account_details = $query->select(['name',
                                        DB::raw("SUM( IF(AT.type='credit', amount, -1*amount) ) as balance")])
                                ->groupBy('accounts.id')
                                ->get()
                                ->pluck('balance', 'name');

        return $account_details;
    }

    /**
     * Displays payment account report.
     * @return Response
     */
    public function paymentAccountReport()
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = session()->get('user.business_id');

        if (request()->ajax()) {
            $query = TransactionPayment::leftjoin(
                'transactions as T',
                'transaction_payments.transaction_id',
                '=',
                'T.id'
            )
                                    ->leftjoin('accounts as A', 'transaction_payments.account_id', '=', 'A.id')
                                    ->where('transaction_payments.business_id', $business_id)
                                    ->whereNull('transaction_payments.parent_id')
                                    ->where('transaction_payments.method', '!=', 'advance')
                                    ->select([
                                        'paid_on',
                                        'payment_ref_no',
                                        'T.ref_no',
                                        'T.invoice_no',
                                        'T.type',
                                        'T.id as transaction_id',
                                        'A.name as account_name',
                                        'A.account_number',
                                        'transaction_payments.id as payment_id',
                                        'transaction_payments.account_id'
                                    ]);

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('T.location_id', $permitted_locations);
            }

            $start_date = !empty(request()->input('start_date')) ? request()->input('start_date') : '';
            $end_date = !empty(request()->input('end_date')) ? request()->input('end_date') : '';

            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(paid_on)'), [$start_date, $end_date]);
            }

            $account_id = !empty(request()->input('account_id')) ? request()->input('account_id') : '';

            if ($account_id == 'none') {
                $query->whereNull('account_id');
            } elseif (!empty($account_id)) {
                $query->where('account_id', $account_id);
            }

            return DataTables::of($query)
                    ->editColumn('paid_on', function ($row) {
                        return $this->transactionUtil->format_date($row->paid_on, true);
                    })
                    ->addColumn('action', function ($row) {
                        $action = '<button type="button" class="btn btn-info 
                        btn-xs btn-modal"
                        data-container=".view_modal" 
                        data-href="' . action('AccountReportsController@getLinkAccount', [$row->payment_id]). '">' . __('account.link_account') .'</button>';
                        
                        return $action;
                    })
                    ->addColumn('account', function ($row) {
                        $account = '';
                        if (!empty($row->account_id)) {
                            $account = $row->account_name . ' - ' . $row->account_number;
                        }
                        return $account;
                    })
                    ->addColumn('transaction_number', function ($row) {
                        $html = $row->ref_no;
                        if ($row->type == 'sell') {
                            $html = '<button type="button" class="btn btn-link btn-modal"
                                    data-href="' . action('SellController@show', [$row->transaction_id]) .'" data-container=".view_modal">' . $row->invoice_no . '</button>';
                        } elseif ($row->type == 'purchase') {
                            $html = '<button type="button" class="btn btn-link btn-modal"
                                    data-href="' . action('PurchaseController@show', [$row->transaction_id]) .'" data-container=".view_modal">' . $row->ref_no . '</button>';
                        }
                        return $html;
                    })
                    ->editColumn('type', function ($row) {
                        $type = $row->type;
                        if ($row->type == 'sell') {
                            $type = __('sale.sale');
                        } elseif ($row->type == 'purchase') {
                            $type = __('lang_v1.purchase');
                        } elseif ($row->type == 'expense') {
                            $type = __('lang_v1.expense');
                        }
                        return $type;
                    })
                    ->filterColumn('account', function ($query, $keyword) {
                        $query->where('A.name', 'like', ["%{$keyword}%"])
                            ->orWhere('account_number', 'like', ["%{$keyword}%"]);
                    })
                    ->filterColumn('transaction_number', function ($query, $keyword) {
                        $query->where('T.invoice_no', 'like', ["%{$keyword}%"])
                            ->orWhere('T.ref_no', 'like', ["%{$keyword}%"]);
                    })
                    ->rawColumns(['action', 'transaction_number'])
                    ->make(true);
        }

        $accounts = Account::forDropdown($business_id, false);
        $accounts = ['' => __('messages.all'), 'none' => __('lang_v1.none')] + $accounts;
        
        return view('account_reports.payment_account_report')
                ->with(compact('accounts'));
    }

    /**
     * Shows form to link account with a payment.
     * @return Response
     */
    public function getLinkAccount($id)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = session()->get('user.business_id');
        if (request()->ajax()) {
            $payment = TransactionPayment::where('business_id', $business_id)->findOrFail($id);
            $accounts = Account::forDropdown($business_id, false);

            return view('account_reports.link_account_modal')
                ->with(compact('accounts', 'payment'));
        }
    }

    /**
     * Links account with a payment.
     * @param  Request $request
     * @return Response
     */
    public function postLinkAccount(Request $request)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = session()->get('user.business_id');
            if (request()->ajax()) {
                $payment_id = $request->input('transaction_payment_id');
                $account_id = $request->input('account_id');

                $payment = TransactionPayment::with(['transaction'])->where('business_id', $business_id)->findOrFail($payment_id);
                $payment->account_id = $account_id;
                $payment->save();

                $payment_type = !empty($payment->transaction->type) ? $payment->transaction->type : null;
                if (empty($payment_type)) {
                    $child_payment = TransactionPayment::where('parent_id', $payment->id)->first();
                    $payment_type = !empty($child_payment->transaction->type) ? $child_payment->transaction->type : null;
                }

                AccountTransaction::updateAccountTransaction($payment, $payment_type);
            }
            $output = ['success' => true,
                            'msg' => __("account.account_linked_success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                
            $output = ['success' => false,
                        'msg' => __("messages.something_went_wrong")
                        ];
        }

        return $output;
    }
    
    //  public function Transaction_trial_balanc_show()
    // {
        

    //       return view('account_reports.transaction_trial')
    //             ->with(compact('acc_transaction'));
          
        
    // }
    
    
    function getSubAccountTypes($parentAccountTypeId = null, $opening_date = null, $formattedDate = null, $acc_type_id = null) {
        $datefilter ='';
        if($opening_date && $formattedDate){
            $datefilter = " AND DATE(account_transactions.operation_date) Between '$opening_date' AND '$formattedDate'";
        }

        if($acc_type_id == null){
            $subAccountTypes = DB::table('account_types')
            ->select('id', 'name', 'parent_account_type_id', 'code',
            DB::raw("(SELECT IFNULL(SUM(account_transactions.amount), 0) FROM account_transactions WHERE account_transactions.account_id In (SELECT id FROM accounts WHERE account_type_id = account_types.id) AND account_transactions.type = 'debit' AND account_transactions.deleted_at IS NULL $datefilter) - 
            (SELECT IFNULL(SUM(account_transactions.amount), 0) FROM account_transactions WHERE account_transactions.account_id In (SELECT id FROM accounts WHERE account_type_id = account_types.id) AND account_transactions.type = 'credit' AND account_transactions.deleted_at IS NULL $datefilter) as acc_type_balance"))
            ->where('parent_account_type_id', $parentAccountTypeId)
            ->get();
        }else{
            $subAccountTypes = DB::table('account_types')
            ->select('id', 'name', 'parent_account_type_id', 'code')
            ->where('parent_account_type_id', $parentAccountTypeId)
            ->get();
        }
    
        foreach ($subAccountTypes as $subAccountType) {
            $subAccountType->subAccountTypes = $this->getSubAccountTypes($subAccountType->id, $opening_date, $formattedDate, $acc_type_id);
            
            // Add accounts to subAccountType
            $subAccountType->accounts = DB::table('accounts')
                ->select(
                    'accounts.name',
                    'accounts.id',
                    'accounts.id as account_id',
                    DB::raw("(SELECT IFNULL(SUM(account_transactions.amount), 0) FROM account_transactions WHERE account_transactions.account_id = accounts.id AND account_transactions.type = 'debit' AND account_transactions.deleted_at IS NULL $datefilter) - 
                        (SELECT IFNULL(SUM(account_transactions.amount), 0) FROM account_transactions WHERE account_transactions.account_id = accounts.id AND account_transactions.type = 'credit' AND account_transactions.deleted_at IS NULL $datefilter) as balance")
                )
                ->where('account_type_id', $subAccountType->id)
                ->when($acc_type_id, function($query) use ($acc_type_id){
                    return $query->where('account_type_id', $acc_type_id);
                })
                ->where('is_closed', 0)
                ->whereNull('deleted_at')
                ->orderBy('account_number', 'ASC')
                ->get();
                
        }
    
        return $subAccountTypes;
    }
    
    public function Transaction_trial_balance()
    {
        
        
        $accountTypes = $this->getSubAccountTypes();
        // dd($accountTypes);
        
         $acc_transaction = DB::table('accounts')
         ->join('account_types', 'accounts.account_type_id', '=', 'account_types.id')
        ->select('accounts.name', 'accounts.id', 'account_types.name as account_type_name','account_types.id as account_type_id',    'accounts.id as account_id',
            DB::raw("(SELECT IFNULL(SUM(account_transactions.amount), 0) FROM account_transactions WHERE account_transactions.account_id = accounts.id AND account_transactions.type = 'credit' AND account_transactions.deleted_at IS NULL) as credit"),
            DB::raw("(SELECT IFNULL(SUM(account_transactions.amount), 0) FROM account_transactions WHERE account_transactions.account_id = accounts.id AND account_transactions.type = 'debit' AND account_transactions.deleted_at IS NULL ) as debit"),
            DB::raw("(SELECT IFNULL(SUM(account_transactions.amount), 0) FROM account_transactions WHERE account_transactions.account_id = accounts.id AND account_transactions.type = 'debit' AND account_transactions.deleted_at IS NULL ) - 
                (SELECT IFNULL(SUM(account_transactions.amount), 0) FROM account_transactions WHERE account_transactions.account_id = accounts.id AND account_transactions.type = 'credit' AND account_transactions.deleted_at IS NULL) as balance")
        )->where('is_closed',0)->whereNull('deleted_at')->orderBy('account_number','ASC')
        ->get();
        
        
        $groupedAccounts = $acc_transaction->groupBy('account_type_id');
        
        
        $account_types = DB::table('account_types as a')
        ->leftJoin('account_types as b', 'a.id', '=', 'b.parent_account_type_id')
        ->select('a.*')
        ->whereNull('b.id')
        ->get();

        return view('account_reports.transaction_trial')
              ->with(compact('groupedAccounts', 'account_types','accountTypes'));
    }
    public function date_wise(Request $request)
    {
    $acc_type_id = $request->accounttype;
    $dates=Business::first();
    $currentYear = date('Y'); 
    $selectedMonth = str_pad($dates->fy_start_month, 2, '0', STR_PAD_LEFT);
    $opening_date= \Carbon::parse(session()->get("financial_year.start"))->subDay()->toDateString();

    $date = $request->input('date');
    $carbonDate = \Carbon\Carbon::createFromFormat('d-m-Y', $date);
    $formattedDate = $carbonDate->format('Y-m-d');

    $accountTypes = $this->getSubAccountTypes(null, $opening_date, $formattedDate, $acc_type_id);
    
    $acc_transaction = DB::table('accounts')
    ->join('account_types', 'accounts.account_type_id', '=', 'account_types.id')
        ->select('accounts.name', 'accounts.id','account_types.name as account_type_name','account_types.id as account_type_id',    'accounts.id as account_id',
            DB::raw("(SELECT IFNULL(SUM(account_transactions.amount), 0) FROM account_transactions WHERE account_transactions.account_id = accounts.id AND account_transactions.type = 'credit' AND account_transactions.deleted_at IS NULL AND DATE(account_transactions.operation_date) Between '$opening_date' AND '$formattedDate') as credit"),
            DB::raw("(SELECT IFNULL(SUM(account_transactions.amount), 0) FROM account_transactions WHERE account_transactions.account_id = accounts.id AND account_transactions.type = 'debit' AND account_transactions.deleted_at IS NULL AND DATE(account_transactions.operation_date) Between '$opening_date' AND '$formattedDate') as debit"),
            DB::raw("(SELECT IFNULL(SUM(account_transactions.amount), 0) FROM account_transactions WHERE account_transactions.account_id = accounts.id AND account_transactions.type = 'debit' AND account_transactions.deleted_at IS NULL AND DATE(account_transactions.operation_date) Between '$opening_date' AND '$formattedDate') - 
                (SELECT IFNULL(SUM(account_transactions.amount), 0) FROM account_transactions WHERE account_transactions.account_id = accounts.id AND account_transactions.type = 'credit' AND account_transactions.deleted_at IS NULL AND DATE(account_transactions.operation_date) Between '$opening_date' AND '$formattedDate') as balance")
        )->where('is_closed',0);
        if($acc_type_id){
            $acc_transaction->where('account_type_id',$acc_type_id);
        }
      
        $acc_transaction = $acc_transaction->orderBy('id','ASC')
        ->get();
          $groupedAccounts = $acc_transaction->groupBy('account_type_id');

    $view = view('account_reports.transaction_partial', compact('acc_transaction','groupedAccounts', 'accountTypes'))->render();

    return response()->json(['html' => $view]);
    }

}
