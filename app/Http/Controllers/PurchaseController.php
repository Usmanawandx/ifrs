<?php

namespace App\Http\Controllers;

use App\AccountTransaction;
use App\Account;
use App\Business;
use App\Brands;
use App\TransactionPayment;
use App\BusinessLocation;
use App\Contact;
use App\purchasetype;
use App\TermsConditions;
use App\CustomerGroup;
use App\Product;
use App\PurchaseLine;
use App\TransactionSellLine;
use App\TaxRate;
use App\store;
use App\Transaction;
use App\DelTransaction;
use App\User;
use App\Unit;
use App\Type;
use App\Utils\BusinessUtil;
use App\Transporter;
use App\Vehicle;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\VariationLocationDetails;
use App\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Activitylog\Models\Activity;

class PurchaseController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $transactionUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;

        $this->dummyPaymentLine = ['method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'bank_account_number' => '',
        'is_return' => 0, 'transaction_no' => ''];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('purchase.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $purchases = $this->transactionUtil->getListPurchases($business_id);

            // $permitted_locations = auth()->user()->permitted_locations();
            // if ($permitted_locations != 'all') {
            //     $purchases->whereIn('transactions.location_id', $permitted_locations);
            // }

            if (!empty(request()->supplier_id)) {
                $purchases->where('contacts.id', request()->supplier_id);
            }
            if (!empty(request()->location_id)) {
                $purchases->where('transactions.location_id', request()->location_id);
            }
            if (!empty(request()->input('payment_status')) && request()->input('payment_status') != 'overdue') {
                $purchases->where('transactions.payment_status', request()->input('payment_status'));
            } elseif (request()->input('payment_status') == 'overdue') {
                $purchases->whereIn('transactions.payment_status', ['due', 'partial'])
                    ->whereNotNull('transactions.pay_term_number')
                    ->whereNotNull('transactions.pay_term_type')
                    ->whereRaw("IF(transactions.pay_term_type='days', DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number DAY) < CURDATE(), DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number MONTH) < CURDATE())");
            }


            if (!empty(request()->status)) {
                $purchases->where('transactions.status', request()->status);
            }
            if (!empty(request()->purchase_category)) {
             
                $purchases->where('transactions.purchase_category', request()->purchase_category);
            }
            if (!empty(request()->transporter_name)) {
          
                $purchases->where('transactions.transporter_name', request()->transporter_name);
            }

            if (!empty(request()->vehicle_number)) {
             
                $purchases->where('transactions.vehicle_no', request()->vehicle_number);
            }
            

            if (!empty(request()->sales_man)) {
             
                $purchases->where('transactions.sales_man', request()->sales_man);
            }
            
            if (!empty(request()->purchase_type)) {
                $purchases->where('transactions.purchase_type', request()->purchase_type);
            }
            
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $purchases->whereDate('transactions.transaction_date', '>=', $start)
                            ->whereDate('transactions.transaction_date', '<=', $end);
            }

            if (!auth()->user()->can('purchase.view') && auth()->user()->can('view_own_purchase')) {
                $purchases->where('transactions.created_by', request()->session()->get('user.id'));
            }

            return Datatables::of($purchases)
                ->addColumn('action', function ($row) {
                    $html="";
                     if (auth()->user()->can("purchase.print")) {
                        $html .= '<a href="#" data-href="' . action('PurchaseController@show', [$row->id]) . '" class="btn-modal btn btn-xs btn-info btn-vew" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i></a>';
                    }
                    if (auth()->user()->can("purchase.update")) {
                        $html .= '<a href="' . action('PurchaseController@edit', [$row->id]) . '" class="btn btn-xs btn-primary btn-edt"><i class="fas fa-edit"></i></a>';
                    }
                    if (auth()->user()->can("purchase.delete")) {
                        $html .= '<a href="' . action('PurchaseController@destroy', [$row->id]) . '" class="delete-purchase btn btn-xs btn-danger btn-dlt"><i class="fas fa-trash"></i></a>';
                    }
                    
                    // $html .= '<div class="btn-group">
                    //         <button type="button" class="btn btn-info dropdown-toggle btn-xs btn-edt" 
                    //             data-toggle="dropdown" aria-expanded="false"><span class="fa fas fa-solid fa-list"></span><span class="sr-only">Toggle Dropdown
                    //             </span>
                    //         </button>
                    //         <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                   
                    // if (auth()->user()->can("purchase.view")) {
                    //     $html .= '<li><a href="#" class="print-invoice btn-modal" data-href="' . action('PurchaseController@show', ['id' => $row->id, 'isprint' => true]) . '"  data-container=".view_modal"><i class="fas fa-print" aria-hidden="true"></i>'. __("messages.print") .'</a></li>';
                    // }
                   

                    // $html .= '<li><a href="' . action('LabelsController@show') . '?purchase_id=' . $row->id . '" data-toggle="tooltip" title="' . __('lang_v1.label_help') . '"><i class="fas fa-barcode"></i>' . __('barcode.labels') . '</a></li>';

                    // if (auth()->user()->can("purchase.view") && !empty($row->document)) {
                    //     $document_name = !empty(explode("_", $row->document, 2)[1]) ? explode("_", $row->document, 2)[1] : $row->document ;
                    //     $html .= '<li><a href="' . url('uploads/documents/' . $row->document) .'" download="' . $document_name . '"><i class="fas fa-download" aria-hidden="true"></i>' . __("purchase.download_document") . '</a></li>';
                    //     if (isFileImage($document_name)) {
                    //         $html .= '<li><a href="#" data-href="' . url('uploads/documents/' . $row->document) .'" class="view_uploaded_document"><i class="fas fa-image" aria-hidden="true"></i>' . __("lang_v1.view_document") . '</a></li>';
                    //     }
                    // }
                                        
                    // if (auth()->user()->can("purchase.create")) {
                    //     $html .= '<li class="divider"></li>';
                    //     if ($row->payment_status != 'paid' && auth()->user()->can("purchase.payments")) {
                    //         $html .= '<li><a href="' . action('TransactionPaymentController@addPayment', [$row->id]) . '" class="add_payment_modal"><i class="fas fa-money-bill-alt" aria-hidden="true"></i>' . __("purchase.add_payment") . '</a></li>';
                    //     }
                    //     $html .= '<li><a href="' . action('TransactionPaymentController@show', [$row->id]) .
                    //     '" class="view_payment_modal"><i class="fas fa-money-bill-alt" aria-hidden="true" ></i>' . __("purchase.view_payments") . '</a></li>';
                    // }

                    // if (auth()->user()->can("purchase.update")) {
                    //     $html .= '<li><a href="' . action('PurchaseReturnController@add', [$row->id]) .
                    //     '"><i class="fas fa-undo" aria-hidden="true" ></i>' . __("lang_v1.purchase_return") . '</a></li>';
                    // }

                    // if (auth()->user()->can("purchase.update") || auth()->user()->can("purchase.update_status")) {
                    //     $html .= '<li><a href="#" data-purchase_id="' . $row->id .
                    //     '" data-status="' . $row->status . '" class="update_status"><i class="fas fa-edit" aria-hidden="true" ></i>' . __("lang_v1.update_status") . '</a></li>';
                    // }

                    // if ($row->status == 'ordered') {
                    //     $html .= '<li><a href="#" data-href="' . action('NotificationController@getTemplate', ["transaction_id" => $row->id,"template_for" => "new_order"]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-envelope" aria-hidden="true"></i> ' . __("lang_v1.new_order_notification") . '</a></li>';
                    // } elseif ($row->status == 'received') {
                    //     $html .= '<li><a href="#" data-href="' . action('NotificationController@getTemplate', ["transaction_id" => $row->id,"template_for" => "items_received"]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-envelope" aria-hidden="true"></i> ' . __("lang_v1.item_received_notification") . '</a></li>';
                    // } elseif ($row->status == 'pending') {
                    //     $html .= '<li><a href="#" data-href="' . action('NotificationController@getTemplate', ["transaction_id" => $row->id,"template_for" => "items_pending"]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-envelope" aria-hidden="true"></i> ' . __("lang_v1.item_pending_notification") . '</a></li>';
                    // }

                    // $html .=  '</ul></div>';
                    return $html;
                })
                ->removeColumn('id')
                ->editColumn('ref_no', function ($row) {
                    return !empty($row->return_exists) ? $row->ref_no . ' <small class="label bg-red label-round no-print" title="' . __('lang_v1.some_qty_returned') .'"><i class="fas fa-undo"></i></small>' : $row->ref_no;
                })
                ->editColumn(
                    'final_total',
                    '<span class="final_total" data-orig-value="{{$final_total}}">@format_currency($final_total)</span>'
                )
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$name}}')
                ->editColumn(
                    'status',
                    '<a href="#" @if(auth()->user()->can("purchase.update") || auth()->user()->can("purchase.update_status")) class="update_status no-print" data-purchase_id="{{$id}}" data-status="{{$status}}" @endif><span class="label @transaction_status($status) status-label" data-status-name="{{__(\'lang_v1.\' . $status)}}" data-orig-value="{{$status}}">{{__(\'lang_v1.\' . $status)}}
                        </span></a>'
                )
                ->editColumn(
                    'payment_status',
                    function ($row) {
                        $payment_status = Transaction::getPaymentStatus($row);
                        return (string) view('sell.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->id, 'for_purchase' => true]);
                    }
                )
                ->addColumn('payment_due', function ($row) {
                    $due = $row->final_total - $row->amount_paid;
                    $due_html = '<strong>' . __('lang_v1.purchase') .':</strong> <span class="payment_due" data-orig-value="' . $due . '">' . $this->transactionUtil->num_f($due, true) . '</span>';

                    if (!empty($row->return_exists)) {
                        $return_due = $row->amount_return - $row->return_paid;
                        $due_html .= '<br><strong>' . __('lang_v1.purchase_return') .':</strong> <a href="' . action("TransactionPaymentController@show", [$row->return_transaction_id]) . '" class="view_purchase_return_payment_modal"><span class="purchase_return" data-orig-value="' . $return_due . '">' . $this->transactionUtil->num_f($return_due, true) . '</span></a>';
                    }
                    return $due_html;
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("purchase.view")) {
                            return  action('PurchaseController@show', [$row->id]) ;
                        } else {
                            return '';
                        }
                    }])
                ->rawColumns(['final_total', 'action', 'payment_due', 'payment_status', 'status', 'ref_no', 'name'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id, false);
        $orderStatuses = $this->productUtil->orderStatuses();
        
        $product_type = Type::orderBy("name",'asc')->where('is_milling','0')->get();
        $purchase_type = purchasetype::orderBy("Type",'asc')->get();
        
        
        $t_no = Transaction::where(['type' => "purchase",'delete_status'=>1])->where('ref_no', 'not like', "%-ovr%")->max("ref_no");
        if(empty($t_no)){
            $t_no = 1;
        }else{
            $break_no = explode("-",$t_no);
            $t_no = end($break_no);
        }
        
        
        
        
        // transaction no for pm
        $pm_no = Transaction::where('type', 'purchase')
        ->where('ref_no', 'not like', "%-ovr%")
        ->where('ref_no', 'like', '%GRN-PM%')
        ->select('ref_no', DB::raw("substring_index(substring_index(ref_no,'-',-1),',',-1) as max_no"))->get()
        ->max('max_no');
        if(empty($pm_no)){
            $pm_no = 1;
        }else{
            $break_no = explode("-",$pm_no);
            $pm_no = end($break_no)+1;
        }
        // transaction no for rmp
        $rmp_no = Transaction::where('type', 'purchase')
        ->where('ref_no', 'not like', "%-ovr%")
        ->where('ref_no', 'like', '%GRN-RMP%')
        ->select('ref_no', DB::raw("substring_index(substring_index(ref_no,'-',-1),',',-1) as max_no"))->get()
        ->max('max_no');
        if(empty($rmp_no)){
            $rmp_no = 1;
        }else{
            $break_no = explode("-",$rmp_no);
            $rmp_no = end($break_no)+1;
        }

        $transporter= Contact::where('type','Transporter')->get();
        $vehicles=vehicle::All();
        $sales_man=Contact::where('type','Agent')->get();
        return view('purchase.index')
            ->with(compact('business_locations','t_no','suppliers', 'orderStatuses','pm_no','rmp_no','product_type','purchase_type','transporter','vehicles','sales_man'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $taxes = TaxRate::where('business_id', $business_id)
                        ->ExcludeForTaxGroup()
                        ->get();
        $orderStatuses = $this->productUtil->orderStatuses();
        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $bl_attributes = $business_locations['attributes'];
        $business_locations = $business_locations['locations'];
        $prefix=Business::first();
        $grn=$prefix->ref_no_prefixes['purchase'];
        
        $t_no = Transaction::where(['type' => "purchase",'delete_status'=>1])->where('ref_no', 'not like', "%-ovr%")->max("ref_no");
        if(empty($t_no)){
            $unni = 1;
        }else{
            $break_no = explode("-",$t_no);
            $unni = end($break_no)+1;
        }
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        $P_order = Transaction::where('type',"purchase_order")->where('delete_status',1)->where('po_id',0)->get();


        $default_purchase_status = null;
        if (request()->session()->get('business.enable_purchase_status') != 1) {
            $default_purchase_status = 'received';
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $customer_groups = CustomerGroup::forDropdown($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        $payment_line = $this->dummyPaymentLine;
        $payment_types = $this->productUtil->payment_types(null, true, $business_id);

        //Accounts
        $accounts = $this->moduleUtil->accountsDropdown($business_id, true);
        $default_id  = DB::table('default_account')->where('form_type','Purchase_invoice')->where('field_type','salesman')->first()->account_id ?? 0;
        $default_sales_man=Account::where('id',$default_id)->first()->contact_id ?? 0;

        $common_settings = !empty(session('business.common_settings')) ? session('business.common_settings') : [];
        $p_type = Type::orderBy("name",'asc')->get();
        $T_C = TermsConditions::All();
         $product = Product::leftJoin(
            'variations',
            'products.id',
            '=',
            'variations.product_id'
        )
           ->where('business_id', $business_id)
            ->whereNull('variations.deleted_at')
            
            ->select(
                'products.id as product_id',
                'products.name',
                'products.type',
                // 'products.sku as sku',
                'variations.id as variation_id',
                'variations.name as variation',
                'variations.sub_sku as sub_sku'
            )
            ->groupBy('variation_id')->pluck('name', 'product_id');
            
        $store=Store::pluck('name','name');    
        $supplier=Contact::pluck('supplier_business_name','id');
        $transporter = Contact::where('type','Transporter')->get();
        $sale_man=Contact::where('type','Agent')->get();
        $purchase_category = purchasetype::orderBy("Type",'asc')->get();
        $brands=Brands::All();
        
        // transaction no for pm
        $pm_no = Transaction::where('type', 'Purchase')
        ->where('ref_no', 'not like', "%-ovr%")
        ->where('ref_no', 'like', '%GRN-PM%')
        ->max("ref_no");
        if(empty($pm_no)){
            $pm_no = 1;
        }else{
            $break_no = explode("-",$pm_no);
            $pm_no = end($break_no)+1;
        }
        // transaction no for rmp
        $rmp_no = Transaction::where('type', 'Purchase')
        ->where('ref_no', 'not like', "%-ovr%")
        ->where('ref_no', 'like', '%GRN-RMP%')
        ->max("ref_no");
        if(empty($rmp_no)){
            $rmp_no = 1;
        }else{
            $break_no = explode("-",$rmp_no);
            $rmp_no = end($break_no)+1;
        }
        
        return view('purchase.create')
            ->with(compact('taxes','grn','sale_man','transporter','unni','T_C','p_type','P_order','orderStatuses','supplier','store','product','business_locations', 'currency_details', 'default_purchase_status', 'customer_groups', 'types', 'shortcuts', 'payment_line', 'payment_types', 'accounts', 'bl_attributes', 'common_settings','purchase_category'
            ,'pm_no','rmp_no','brands','default_sales_man'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
         $request->validate([
                'ref_no'=> 'required|unique:transactions',
            ]);
        
        
        if (!auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            $transaction_data = $request->only([ 'ref_no', 'status', 'contact_id', 'transaction_date','posting_date','tandc_type','tandc_title','purchase_type','total_before_tax', 'location_id','discount_type', 'discount_amount','tax_id', 'tax_amount', 'shipping_details', 'shipping_charges', 'final_total', 'additional_notes', 'exchange_rate', 'pay_term_number', 'pay_term_type','vehicle_no','transporter_name','factory_weight','sales_man','purchase_category']);
            $exchange_rate = $transaction_data['exchange_rate'];
            $user_id = $request->session()->get('user.id');
            $enable_product_editing = $request->session()->get('business.enable_editing_product_from_purchase');
            //Update business exchange rate.
            Business::update_business($business_id, ['p_exchange_rate' => ($transaction_data['exchange_rate'])]);

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            //unformat input values
            $transaction_data['total_before_tax'] = $this->productUtil->num_uf($transaction_data['total_before_tax'], $currency_details)*$exchange_rate;

            // If discount type is fixed them multiply by exchange rate, else don't
            if ($transaction_data['discount_type'] == 'fixed') {
                $transaction_data['discount_amount'] = $this->productUtil->num_uf($transaction_data['discount_amount'], $currency_details)*$exchange_rate;
            } elseif ($transaction_data['discount_type'] == 'percentage') {
                $transaction_data['discount_amount'] = $this->productUtil->num_uf($transaction_data['discount_amount'], $currency_details);
            } else {
                $transaction_data['discount_amount'] = 0;
            }

            $transaction_data['tax_amount'] = $this->productUtil->num_uf($transaction_data['tax_amount'], $currency_details)*$exchange_rate;
            $transaction_data['shipping_charges'] = $this->productUtil->num_uf($transaction_data['shipping_charges'], $currency_details)*$exchange_rate;
            $transaction_data['final_total'] = $this->productUtil->num_uf($transaction_data['final_total'], $currency_details)*$exchange_rate;

            $transaction_data['business_id'] = $business_id;
            $transaction_data['created_by'] = $user_id;
            $transaction_data['type'] = 'purchase';
            $transaction_data['payment_status'] = 'due';
            $transaction_data['transaction_date'] = $this->productUtil->uf_date($transaction_data['transaction_date'], false);

            //upload document
            $transaction_data['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');
            DB::beginTransaction();

            //Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount($transaction_data['type']);
            //Generate reference number
            $transaction_data['ref_no'] =$request->prefix."".$transaction_data['ref_no'];
            $transaction_data['pay_type'] =$request->input('Pay_type');
            $transaction_data['purchase_order_ids']=(int)$request->input('purchase_order_ids');

            
            $transaction_data['gross_weight']  = $request->input('total_gross__weight');
            $transaction_data['net_weight']    = $request->input('total_net__weight');

            $transaction = Transaction::create($transaction_data);
            
            $purchase_lines = [];
            $purchases = $request->input('purchases');
           
        
            $this->productUtil->createOrUpdatePurchaseLines($transaction, $purchases, $currency_details, $enable_product_editing);
            
            //Add Purchase payments
            $this->transactionUtil->createOrUpdatePaymentLines($transaction, $request->input('payment'));

            //update payment status
            $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);
      
            //Adjust stock over selling if found
            $this->productUtil->adjustStockOverSelling($transaction);

            $this->transactionUtil->activityLog($transaction, 'added');
            
            if($transaction_data['purchase_order_ids'] != 0){
           
                $purchase_order=Transaction::where('id',$transaction_data['purchase_order_ids'])->where('type','purchase_order')->first();
                $purchase_order->po_id=$transaction_data['purchase_order_ids'];
                $purchase_order->save();
                 
            }
           
            DB::commit();
            
            $output = ['success' => 1,
                            'msg' => __('purchase.purchase_add_success')
                        ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __($e->getMessage()),
                            'details' => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                        ];
        }
        if ($request->input('submit_type') == 'save_n_add_another') {
            return redirect()->action(
                'PurchaseController@create'
            )->with('status', $output);
        }
        // dd($output);
        return redirect('purchases')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) 
    {

        $business_id = request()->session()->get('user.business_id');
        $taxes = TaxRate::where('business_id', $business_id)
                            ->pluck('name', 'id');
        $purchase = Transaction::where('business_id', $business_id)
                                ->where('id', $id)
                                ->with(
                                    'contact',
                                    'purchase_lines',
                                    'purchase_lines.product',
                                    'purchase_lines.product.unit',
                                    'purchase_lines.variations',
                                    'purchase_lines.variations.product_variation',
                                    'purchase_lines.sub_unit',
                                    'location',
                                    'payment_lines',
                                    'tax'
                                )
                                ->firstOrFail();

        foreach ($purchase->purchase_lines as $key => $value) {
            if (!empty($value->sub_unit_id)) {
                $formated_purchase_line = $this->productUtil->changePurchaseLineUnit($value, $business_id);
                $purchase->purchase_lines[$key] = $formated_purchase_line;
            }
        }
        
        $payment_methods = $this->productUtil->payment_types($purchase->location_id, true);

        $purchase_taxes = [];
        if (!empty($purchase->tax)) {
            if ($purchase->tax->is_tax_group) {
                $purchase_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($purchase->tax, $purchase->tax_amount));
            } else {
                $purchase_taxes[$purchase->tax->name] = $purchase->tax_amount;
            }
        }

        //Purchase orders
        $purchase_order_nos = '';
        $purchase_order_dates = '';
        // if (!empty($purchase->purchase_order_ids)) {
        //     $purchase_orders = Transaction::find($purchase->purchase_order_ids);
        //     $purchase_order_nos = implode(', ', $purchase_orders->pluck('ref_no')->toArray());
        //     dd($purchase_order_nos);
        //     $order_dates = [];
        //     foreach ($purchase_orders as $purchase_order) {
        //         $order_dates[] = $this->transactionUtil->format_date($purchase_order->transaction_date, true);
        //     }
        //     $purchase_order_dates = implode(', ', $order_dates);
        // }

        $activities = Activity::forSubject($purchase)
           ->with(['causer', 'subject'])
           ->latest()
           ->get();

        $statuses = $this->productUtil->orderStatuses();

        return view('purchase.show')
                ->with(compact('taxes', 'purchase', 'payment_methods', 'purchase_taxes', 'activities', 'statuses', 'purchase_order_nos', 'purchase_order_dates'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('purchase.update')) {
            abort(403, 'Unauthorized action.');
        }
        $page = 0 ;
        if($_GET){
            $page = $_GET['delete'];
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse(action('PurchaseController@index'));
        }

        //Check if the transaction can be edited or not.
        $edit_days = request()->session()->get('business.transaction_edit_days');
        if (!$this->transactionUtil->canBeEdited($id, $edit_days)) {
            return back()
                ->with('status', ['success' => 0,
                    'msg' => __('messages.transaction_edit_not_allowed', ['days' => $edit_days])]);
        }

        //Check if return exist then not allowed
        if ($this->transactionUtil->isReturnExist($id)) {
            return back()->with('status', ['success' => 0,
                    'msg' => __('lang_v1.return_exist')]);
        }

        $business = Business::find($business_id);

        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $taxes = TaxRate::where('business_id', $business_id)
                            ->ExcludeForTaxGroup()
                            ->get();
                            $store=store::All();
        $purchase = Transaction::where('business_id', $business_id)
                    ->where('id', $id)
                    ->with(
                        'contact',
                        'purchase_lines',
                        'purchase_lines.product',
                        'purchase_lines.product.unit',
                        //'purchase_lines.product.unit.sub_units',
                        'purchase_lines.variations',
                        'purchase_lines.variations.product_variation',
                        'location',
                        'purchase_lines.sub_unit',
                        'purchase_lines.purchase_order_line'
                    )
                    ->first();
        
        foreach ($purchase->purchase_lines as $key => $value) {
            if (!empty($value->sub_unit_id)) {
                $formated_purchase_line = $this->productUtil->changePurchaseLineUnit($value, $business_id);
                $purchase->purchase_lines[$key] = $formated_purchase_line;
            }
        }
        
        $orderStatuses = $this->productUtil->orderStatuses();

        $business_locations = BusinessLocation::forDropdown($business_id);

        $default_purchase_status = null;
        if (request()->session()->get('business.enable_purchase_status') != 1) {
            $default_purchase_status = 'received';
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $customer_groups = CustomerGroup::forDropdown($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);
        $transporter = Contact::where('type','Transporter')->get();
        $vehicles = Vehicle::where('id',$purchase->vehicle_no)->get();
        $p_type = Type::orderBy("name",'asc')->get();
        $T_C = TermsConditions::All();

        $common_settings = !empty(session('business.common_settings')) ? session('business.common_settings') : [];

        $purchase_orders = Transaction::where('business_id', $business_id)
        ->where('type', 'purchase_order')
        ->where('id',$purchase->purchase_order_ids)
        ->get();
        
        $product = Product::leftJoin(
            'variations',
            'products.id',
            '=',
            'variations.product_id'
        )
           ->where('business_id', $business_id)
            ->whereNull('variations.deleted_at')
            
            ->select(
                'products.id as product_id',
                'products.name',
                'products.type',
                // 'products.sku as sku',
                'variations.id as variation_id',
                'variations.name as variation',
                'variations.sub_sku as sub_sku'
            )
            ->groupBy('variation_id')->get();
        
        $supplier=Contact::All();
        $purchase_category = purchasetype::orderBy("Type",'asc')->get();
        $sale_man =Contact::where('type','Agent')->get();
        $brand=Brands::All();

        return view('purchase.edit')
            ->with(compact(
                'taxes',
                'sale_man',
                'supplier',
                'product',
                'transporter',
                'vehicles',
                'purchase',
                'store',
                'T_C',
                'p_type',
                'orderStatuses',
                'business_locations',
                'business',
                'currency_details',
                'default_purchase_status',
                'customer_groups',
                'types',
                'shortcuts',
                'purchase_orders',
                'common_settings',
                'page',
                'purchase_category',
                'brand'
            ));
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
        if (!auth()->user()->can('purchase.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $transaction = Transaction::findOrFail($id);

            //Validate document size
            $request->validate([
                'document' => 'file|max:'. (config('constants.document_size_limit') / 1000)
            ]);

            $transaction = Transaction::findOrFail($id);
            $before_status = $transaction->status;
            $business_id = request()->session()->get('user.business_id');
            $enable_product_editing = $request->session()->get('business.enable_editing_product_from_purchase');

            $transaction_before = $transaction->replicate();

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            $update_data = $request->only([ 'ref_no', 'status', 'contact_id',
                            'transaction_date','posting_date','tandc_type','tandc_title','purchase_type','total_before_tax',
                            'discount_type', 'discount_amount', 'tax_id',
                            'tax_amount', 'shipping_details',
                            'shipping_charges', 'final_total',
                             'additional_notes', 'exchange_rate', 'pay_term_number', 'pay_term_type','vehicle_no','transporter_name','factory_weight','purchase_order_ids','sales_man','pay_type','purchase_category']);

            $exchange_rate = $update_data['exchange_rate'];

            //Reverse exchage rate and save
            //$update_data['exchange_rate'] = number_format(1 / $update_data['exchange_rate'], 2);

            $update_data['transaction_date'] = $this->productUtil->uf_date($update_data['transaction_date'], false);

            //unformat input values
            $update_data['total_before_tax'] = $this->productUtil->num_uf($update_data['total_before_tax'], $currency_details) * $exchange_rate;

            // If discount type is fixed them multiply by exchange rate, else don't
            if ($update_data['discount_type'] == 'fixed') {
                $update_data['discount_amount'] = $this->productUtil->num_uf($update_data['discount_amount'], $currency_details) * $exchange_rate;
            } elseif ($update_data['discount_type'] == 'percentage') {
                $update_data['discount_amount'] = $this->productUtil->num_uf($update_data['discount_amount'], $currency_details);
            } else {
                $update_data['discount_amount'] = 0;
            }

            $update_data['tax_amount'] = $this->productUtil->num_uf($update_data['tax_amount'], $currency_details) * $exchange_rate;
            $update_data['shipping_charges'] = $this->productUtil->num_uf($update_data['shipping_charges'], $currency_details) * $exchange_rate;
            $update_data['final_total'] = $this->productUtil->num_uf($update_data['final_total'], $currency_details) * $exchange_rate;
            //unformat input values ends

            //upload document
            $document_name = $this->transactionUtil->uploadFile($request, 'document', 'documents');
            if (!empty($document_name)) {
                $update_data['document'] = $document_name;
            }

            $purchase_order_ids = $transaction->purchase_order_ids ?? [];

            $update_data['gross_weight']  = $request->input('total_gross__weight');
            $update_data['net_weight']    = $request->input('total_net__weight');
            DB::beginTransaction();

            //update transaction
            $transaction->update($update_data);

            //Update transaction payment status
            $payment_status = $this->transactionUtil->updatePaymentStatus($transaction->id);
            $transaction->payment_status = $payment_status;

            $purchases = $request->input('purchases');

            $delete_purchase_lines = $this->productUtil->createOrUpdatePurchaseLines($transaction, $purchases, $currency_details, $enable_product_editing, $before_status);

            //Update mapping of purchase & Sell.
            $this->transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($before_status, $transaction, $delete_purchase_lines);

            //Adjust stock over selling if found
            $this->productUtil->adjustStockOverSelling($transaction);

            $new_purchase_order_ids = $transaction->purchase_order_ids ;
         
            $this->transactionUtil->activityLog($transaction, 'edited', $transaction_before);

            DB::commit();

            $output = ['success' => 1,
                            'msg' => __('purchase.purchase_update_success')
                        ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => $e->getLine()
                        ];
            return back()->with('status', $output);
        }

        return redirect('purchases')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('purchase.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
            
                DB::beginTransaction();
                $transaction = Transaction::where('id',$id)->get()->toArray();
                DB::table('stock_history')->where('ref_no', $transaction[0]['ref_no'])->delete(); 
                $transaction[0]['transaction_id'] = $id;
                DelTransaction::create($transaction[0]);
                if($transaction[0]['type'] == "purchase"){
                    Transaction::where('id', $transaction[0]['purchase_order_ids'])->update(['po_id' => 0]);
                }

                $transaction = Transaction::with('purchase_lines')->findOrFail($id);   

                foreach($transaction->purchase_lines as $key => $value){
                    $quantity_variation_details = VariationLocationDetails::where("product_id",$value->product_id)->firstOrFail();
                    $quantity_variation_details->qty_available -= $value->quantity;
                    $quantity_variation_details->save();
                }
           
                Transaction::where('id',$id)->delete();

                DB::commit();
                
                $output = ['success' => true,
                            'msg' => __('lang_v1.purchase_delete_success')
                        ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => $e->getMessage()
                        ];
        }

        return $output;
    }
    
    
    /**
     * Retrieves supliers list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSuppliers()
    {
        if (request()->ajax()) {
            $term = request()->q;
            if (empty($term)) {
                return json_encode([]);
            }

            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $query = Contact::where('business_id', $business_id)
                            ->active();

            $selected_contacts = User::isSelectedContacts($user_id);
            if ($selected_contacts) {
                $query->join('user_contact_access AS uca', 'contacts.id', 'uca.contact_id')
                ->where('uca.user_id', $user_id);
            }
            $suppliers = $query->where(function ($query) use ($term) {
                $query->where('name', 'like', '%' . $term .'%')
                                ->orWhere('supplier_business_name', 'like', '%' . $term .'%')
                                ->orWhere('contacts.contact_id', 'like', '%' . $term .'%');
            })
                        ->select(
                            'contacts.id', 
                            'name as business_name', 
                            'supplier_business_name as text', 
                            'contacts.mobile',
                            'contacts.address_line_1',
                            'contacts.address_line_2',
                            'contacts.city',
                            'contacts.state',
                            'contacts.country',
                            'contacts.zip_code',
                            'contact_id', 
                            'contacts.pay_term_type', 
                            'contacts.pay_term_number', 
                            'contacts.balance'
                        )
                        ->onlySuppliers()
                        ->get();
            return json_encode($suppliers);
        }
    }

    /**
     * Retrieves products list.
     *
     * @return \Illuminate\Http\Response
     */
  
        public function getProducts()
        {
            if (request()->ajax()) {

                $term = request()->term;
                $result = Product::with('unit','brand')->where('id',$term)->get();
               
                $maxTransactionId = DB::table('transactions')->select(DB::raw('MAX(id) as max_id'))->where('type', 'sale_invoice')->first()->max_id ?? 0;
                $last_sale_price = TransactionSellLine::where('product_id', $term)->where('transaction_id', $maxTransactionId)->first()->unit_price ?? 0;

                $result[0]->last_sale_price = (!empty($last_sale_price)) ? $last_sale_price : '' ;
                $unit_purchase=unit::where('id',$result[0]->unit->base_unit_id)->first();
                $result[0]->shipping_custom_field_1=(!empty($unit_purchase->actual_name))?$unit_purchase->actual_name:'none';
                $avialable=VariationLocationDetails::where('product_id',$result[0]->id)->first();
                $result[0]->alert_quantity =$avialable->qty_available;
                
                //for get transporter & Contractor rate start
                $transporter_id                 = request()->transporter;
                $contractor_id                  = request()->contractor;
                $prd_contractor_id              = request()->prd_contractor;
                $product                        = Product::where('id',$term)->first();
                $subCat_id                      = $product->sub_category_id;
                
                $result[0]->transporter_rate    = 0;
                $result[0]->contractor_rate     = 0;
                $result[0]->prd_contractor      = 0;
                if($subCat_id > 0){
                    $result[0]->transporter_rate   = DB::table('vehicle_rate')->where(['vehicle_id' => $transporter_id, 'child_id' => $subCat_id])
                                                    ->where('date', function ($query) use ($transporter_id, $subCat_id) {
                                                        $query->select(DB::raw('MAX(date)'))
                                                            ->from('vehicle_rate')
                                                            ->where('vehicle_id', $transporter_id)
                                                            ->where('child_id', $subCat_id);
                                                    })
                                                    ->value('rate') ?? 0;


                    $result[0]->contractor_rate   = DB::table('contractor_rate')->where(['contractor_id' => $contractor_id, 'child_id' => $subCat_id])
                                                    ->where('date', function ($query) use ($contractor_id, $subCat_id) {
                                                        $query->select(DB::raw('MAX(date)'))
                                                            ->from('contractor_rate')
                                                            ->where('contractor_id', $contractor_id)
                                                            ->where('child_id', $subCat_id);
                                                    })
                                                    ->value('rate') ?? 0;
                    
                    $result[0]->prd_contractor   = DB::table('contractor_rate')->where(['contractor_id' => $prd_contractor_id, 'child_id' => $subCat_id])
                                                    ->where('date', function ($query) use ($prd_contractor_id, $subCat_id) {
                                                        $query->select(DB::raw('MAX(date)'))
                                                            ->from('contractor_rate')
                                                            ->where('contractor_id', $prd_contractor_id)
                                                            ->where('child_id', $subCat_id);
                                                    })
                                                    ->value('rate') ?? 0;

                }


                // if($child_cat->count() > 0){
                //     // $transporter_rate           = DB::table('vehicle_rate')->where('vehicle_id', $transporter_id)->where('child_id', $child_cat[0]->id)->get();
                //     $transporterr               = DB::table('vehicle_rate')->where('vehicle_id', $transporter_id)->first();
                //     $transporter_rate           = [];
                //     if(!empty($transporterr->id)){
                //         $transporter_rate           = DB::table('vehicle_rate')->where('parent_id', $transporterr->id)->where('child_id', $child_cat[0]->id)->get();
                //     }
                //     $contractor_rate            = DB::table('contractor_rate')->where('contractor_id', $contractor_id)->where('child_id', $child_cat[0]->id)->get();
                    
                //     if (is_array($transporter_rate) && count($transporter_rate) > 0) {
                //         $result[0]->transporter_rate = $transporter_rate[0]->rate;
                //     } else {
                //         // Handle the case when $transporter_rate is not an array or is empty
                //     }
                //     if($contractor_rate->count() > 0){
                //         $result[0]->contractor_rate    = $contractor_rate[0]->rate;
                //     }
                // }
                //for get transporter & Contractor rate end
                
                return json_encode($result);
            }
        }

    
    public function getProductss()
    {
        if (request()->ajax()) {
            $term = request()->term;

            $check_enable_stock = true;
            if (isset(request()->check_enable_stock)) {
                $check_enable_stock = filter_var(request()->check_enable_stock, FILTER_VALIDATE_BOOLEAN);
            }

            $only_variations = false;
            if (isset(request()->only_variations)) {
                $only_variations = filter_var(request()->only_variations, FILTER_VALIDATE_BOOLEAN);
            }

            if (empty($term)) {
                return json_encode([]);
            }

            $business_id = request()->session()->get('user.business_id');
            $q = Product::leftJoin(
                'variations',
                'products.id',
                '=',
                'variations.product_id'
            )
                ->where(function ($query) use ($term) {
                    $query->where('products.name', 'like', '%' . $term .'%');
                    $query->orWhere('sku', 'like', '%' . $term .'%');
                    $query->orWhere('sub_sku', 'like', '%' . $term .'%');
                })
                ->active()
                ->where('business_id', $business_id)
                ->whereNull('variations.deleted_at')
                ->select(
                    'products.id as product_id',
                    'products.name',
                    'products.type',
                    // 'products.sku as sku',
                    'variations.id as variation_id',
                    'variations.name as variation',
                    'variations.sub_sku as sub_sku'
                )
                ->groupBy('variation_id');

            if ($check_enable_stock) {
                $q->where('enable_stock', 1);
            }
            if (!empty(request()->location_id)) {
                $q->ForLocation(request()->location_id);
            }
            $products = $q->get();
                
            $products_array = [];
            foreach ($products as $product) {
                $products_array[$product->product_id]['name'] = $product->name;
                $products_array[$product->product_id]['sku'] = $product->sub_sku;
                $products_array[$product->product_id]['type'] = $product->type;
                $products_array[$product->product_id]['variations'][]
                = [
                        'variation_id' => $product->variation_id,
                        'variation_name' => $product->variation,
                        'sub_sku' => $product->sub_sku
                        ];
            }

            $result = [];
            $i = 1;
            $no_of_records = $products->count();
            if (!empty($products_array)) {
                foreach ($products_array as $key => $value) {
                    if ($no_of_records > 1 && $value['type'] != 'single' && !$only_variations) {
                        $result[] = [ 'id' => $i,
                                    'text' => $value['name'] . ' - ' . $value['sku'],
                                    'variation_id' => 0,
                                    'product_id' => $key
                                ];
                    }
                    $name = $value['name'];
                    foreach ($value['variations'] as $variation) {
                        $text = $name;
                        if ($value['type'] == 'variable') {
                            $text = $text . ' (' . $variation['variation_name'] . ')';
                        }
                        $i++;
                        $result[] = [ 'id' => $i,
                                            'text' => $text . ' - ' . $variation['sub_sku'],
                                            'product_id' => $key ,
                                            'variation_id' => $variation['variation_id'],
                                        ];
                    }
                    $i++;
                }
            }
            
            return json_encode($result);
        }
    }
    
    /**
     * Retrieves products list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPurchaseEntryRow(Request $request)
    {
        if (request()->ajax()) {
            $product_id = $request->input('product_id');
            $variation_id = $request->input('variation_id');
            $business_id = request()->session()->get('user.business_id');
            $location_id = $request->input('location_id');
            $is_purchase_order = $request->has('is_purchase_order');

            $hide_tax = 'hide';
            if ($request->session()->get('business.enable_inline_tax') == 1) {
                $hide_tax = '';
            }

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
            

            if (!empty($product_id)) {
                $row_count = $request->input('row_count');
                $product = Product::where('id', $product_id)
                                    ->with(['unit'])
                                    ->first();
                
                $sub_units = $this->productUtil->getSubUnits($business_id, $product->unit->id, false, $product_id);

                $query = Variation::where('product_id', $product_id)
                                ->with([
                                    'product_variation', 
                                    'variation_location_details' => function ($q) use ($location_id) {
                                        $q->where('location_id', $location_id);
                                    }
                                ]);
                if ($variation_id !== '0') {
                    $query->where('id', $variation_id);
                }

                $variations =  $query->get();


                $business_id = request()->session()->get('user.business_id');

                $business_locations = BusinessLocation::where('business_id',$business_id)->get();
                $store=store::All();

                // dd($store);

                $taxes = TaxRate::where('business_id', $business_id)
                            ->ExcludeForTaxGroup()
                            ->get();

                return view('purchase.partials.purchase_entry_row')
                    ->with(compact(
                        'product',
                        'store',
                        'business_locations',
                        'variations',
                        'row_count',
                        'variation_id',
                        'taxes',
                        'currency_details',
                        'hide_tax',
                        'sub_units',
                        'is_purchase_order'
                    ));
            }
        }
    }


    public function po_entries(Request $request)
    {
   
        if (request()->ajax()) {
        
            $product_id = $request->input('product_id');
            $variation_id = $request->input('variation_id');
            $business_id = request()->session()->get('user.business_id');
            $location_id = $request->input('location_id');
            $is_purchase_order = $request->has('is_purchase_order');
            $hide_tax = 'hide';
            if ($request->session()->get('business.enable_inline_tax') == 1) {
                $hide_tax = '';
            }

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
            

            if (!empty($product_id)) {
                $row_count = $request->input('row_count');
                $product = Product::where('id', $product_id)
                                    ->with(['unit'])
                                    ->first();
                
                $sub_units = $this->productUtil->getSubUnits($business_id, $product->unit->id, false, $product_id);

                $query = Variation::where('product_id', $product_id)
                                ->with([
                                    'product_variation', 
                                    'variation_location_details' => function ($q) use ($location_id) {
                                        $q->where('location_id', $location_id);
                                    }
                                ]);
                if ($variation_id !== '0') {
                    $query->where('id', $variation_id);
                }

                $variations =  $query->get();


                $business_id = request()->session()->get('user.business_id');

                $business_locations = BusinessLocation::where('business_id',$business_id)->get();
                $store=store::All();

                // dd($store);

                $taxes = TaxRate::where('business_id', $business_id)
                            ->ExcludeForTaxGroup()
                            ->get();

                return view('purchase.partials.po_entries')
                    ->with(compact(
                        'product',
                        'store',
                        'business_locations',
                        'variations',
                        'row_count',
                        'variation_id',
                        'taxes',
                        'currency_details',
                        'hide_tax',
                        'sub_units',
                        'is_purchase_order'
                    ));
            }
        }
    }

    public function getPurchaseEntryRowReq(Request $request)
    {
        if (request()->ajax()) {
            // dd("");
            $product_id = $request->input('product_id');
            $variation_id = $request->input('variation_id');
            $business_id = request()->session()->get('user.business_id');
            $location_id = $request->input('location_id');
            $is_purchase_order = $request->has('is_purchase_order');

            $hide_tax = 'hide';
            if ($request->session()->get('business.enable_inline_tax') == 1) {
                $hide_tax = '';
            }

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
            

            if (!empty($product_id)) {
                $row_count = $request->input('row_count');
                $product = Product::where('id', $product_id)
                                    ->with(['unit'])
                                    ->first();
                
                $sub_units = $this->productUtil->getSubUnits($business_id, $product->unit->id, false, $product_id);

                $query = Variation::where('product_id', $product_id)
                                ->with([
                                    'product_variation', 
                                    'variation_location_details' => function ($q) use ($location_id) {
                                        $q->where('location_id', $location_id);
                                    }
                                ]);
                if ($variation_id !== '0') {
                    $query->where('id', $variation_id);
                }

                $variations =  $query->get();


                $business_id = request()->session()->get('user.business_id');

                $business_locations = BusinessLocation::where('business_id',$business_id)->get();
                $store=store::All();

                // dd($store);

                $taxes = TaxRate::where('business_id', $business_id)
                            ->ExcludeForTaxGroup()
                            ->get();

                return view('purchase.partials.purchase_entry_row_req')
                    ->with(compact(
                        'product',
                        'store',
                        'business_locations',
                        'variations',
                        'row_count',
                        'variation_id',
                        'taxes',
                        'currency_details',
                        'hide_tax',
                        'sub_units',
                        'is_purchase_order'
                    ));
            }
        }
    }


    public function getPurchaseOrderLines($purchase_order_id)
    {
        $business_id = request()->session()->get('user.business_id');
        
        $purchase_order = Transaction::where('business_id', $business_id)
                        ->where('type', 'purchase_order')
                        ->with(['purchase_lines', 'purchase_lines.variations', 
                            'purchase_lines.product', 'purchase_lines.product.unit', 'purchase_lines.variations.product_variation' ])
                        ->findOrFail($purchase_order_id);
        
        $taxes = TaxRate::where('business_id', $business_id)
                            ->ExcludeForTaxGroup()
                            ->get();

        $store=store::All();
   

        $sub_units_array = [];
        foreach ($purchase_order->purchase_lines as $pl) {
            $sub_units_array[$pl->id] = $this->productUtil->getSubUnits($business_id, $pl->product->unit->id, false, $pl->product_id);
        }
        $hide_tax = request()->session()->get('business.enable_inline_tax') == 1 ? '' : 'hide';
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        $row_count = request()->input('row_count');
        $product_name=Product::All();
        $brand=Brands::All();

        $html =  view('purchase.partials.purchase_order_lines')
                ->with(compact(
                    'purchase_order',
                    'product_name',
                    'taxes',
                    'store',
                    'hide_tax',
                    'currency_details',
                    'row_count',
                    'sub_units_array',
                    'brand'
                ))->render();

        return [
            'html' => $html,
            'po' => $purchase_order
        ];

    }

    public function convert_grn_to_pi($id)
    {
   
        if (!auth()->user()->can('purchase_order.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $delete = "convert_grn_to_pi";
        $business = Business::find($business_id);

        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $taxes = TaxRate::where('business_id', $business_id)
                            ->ExcludeForTaxGroup()
                            ->get();

        $query = Transaction::where('business_id', $business_id)
                    ->where('id', $id)
                    ->with(
                        'contact',
                        'purchase_lines',
                        'purchase_lines.product',
                        'purchase_lines.product.unit',
                        //'purchase_lines.product.unit.sub_units',
                        'purchase_lines.variations',
                        'purchase_lines.variations.product_variation',
                        'location',
                        'purchase_lines.sub_unit'
                    );

        if (!auth()->user()->can('purchase_order.view_all') && auth()->user()->can('purchase_order.view_own')) {
            $query->where('transactions.created_by', request()->session()->get('user.id'));
        }
        
        $purchase =  $query->first();
        
        $purchase->type = "Purchase_invoice";
        $purchase->grn_ref_no = $purchase->ref_no;
        $purchase->purchase_order_ids = $id;

        $purchase_lines = [];
        foreach ($purchase->purchase_lines as $key => $value) {
            if (!empty($value->sub_unit_id)) {
                $formated_purchase_line = $this->productUtil->changePurchaseLineUnit($value, $business_id);
                $purchase_lines[$key] = $formated_purchase_line;
            }
            $purchase_lines[$key] = $value;
        }
        
       DB::beginTransaction();
        
        $type_prefix = DB::table('purchase_type')->where('id', $purchase->purchase_category)->first();
        $prefix = 'PI-' . $type_prefix->prefix;
        
        $t_no = DB::table('transactions')
        ->where('ref_no', 'like', '%'. $prefix .'%')
        ->select('ref_no', DB::raw("substring_index(substring_index(ref_no,'-',-1),',',-1) as max_no"))->get()
        ->max('max_no');
        if(empty($t_no)){
            $trans_no_ = 1;
        }else{
            $break_no = explode("-",$t_no);
            $trans_no_ = end($break_no)+1;
        }
        $trans_no_ = str_pad($trans_no_, 4, '0', STR_PAD_LEFT);
        $purchase->ref_no = $prefix.'-'.$trans_no_;

            unset($purchase->purchase_lines);
            unset($purchase->location);
            unset($purchase->contact);            
            $transaction = Transaction::create($purchase->toArray());
            $transaction_prq = Transaction::where("id",$id)->first();
            $transaction_prq->po_id = $transaction->id ;
            $transaction_prq->save();
            foreach($purchase_lines as $key_line => $value_line){

                unset($value_line->product);
                unset($value_line->sub_unit);
                unset($value_line->variations);
           
                $value_line->transaction_id=$transaction->id;
                $purchasePO_lines = PurchaseLine::create($value_line->toArray());                
            }
            DB::commit();            
            $output = ['success' => 1,
                            'msg' => __('Convert  GRN TO PI Successfully')
                        ];
         return redirect()->route('purchase-order.edit',[$transaction->id ,'delete' => $delete]);
    }

    public function get_prq($purchase_order_id)
    {
        
        // dd($purchase_order_id);
        
        $business_id = request()->session()->get('user.business_id');
        $store=store::All();
        $purchase_order = Transaction::where('business_id', $business_id)
                        ->where('type', 'Purchase Requisition')
                        ->with(['purchase_lines', 'purchase_lines.variations', 
                            'purchase_lines.product', 'purchase_lines.product.unit', 'purchase_lines.variations.product_variation' ])
                        ->findOrfail($purchase_order_id);

        $taxes = TaxRate::where('business_id', $business_id)
                            ->ExcludeForTaxGroup()
                            ->get();

        $sub_units_array = [];
        foreach ($purchase_order->purchase_lines as $pl) {
            $sub_units_array[$pl->id] = $this->productUtil->getSubUnits($business_id, $pl->product->unit->id, false, $pl->product_id);
        }
        $hide_tax = request()->session()->get('business.enable_inline_tax') == 1 ? '' : 'hide';
        $brand=Brands::All();
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        $row_count = request()->input('row_count');
        $product_name=Product::where('product_type',$purchase_order->purchase_type)->get();
        $html =  view('purchase.partials.purchase_order_lines')
                ->with(compact(
                    'purchase_order',
                    'taxes',
                    'product_name',
                    'store',
                    'hide_tax',
                    'currency_details',
                    'row_count',
                    'sub_units_array',
                    'brand'
                ))->render();
    
        return [
            'html' => $html,
            'po' => $purchase_order
        ];

    }
    
      public function get_grn($purchase_order_id)
    {
        $business_id = request()->session()->get('user.business_id');
        $store=store::All();
        $purchase_order = Transaction::where('business_id', $business_id)
                        ->where('type', 'purchase')
                        ->with(['purchase_lines', 'purchase_lines.variations', 
                            'purchase_lines.product', 'purchase_lines.product.unit', 'purchase_lines.variations.product_variation' ])
                        ->findOrfail($purchase_order_id);

        $taxes = TaxRate::where('business_id', $business_id)
                            ->ExcludeForTaxGroup()
                            ->get();
        $sub_units_array = [];
        foreach ($purchase_order->purchase_lines as $pl) {
            $sub_units_array[$pl->id] = $this->productUtil->getSubUnits($business_id, $pl->product->unit->id, false, $pl->product_id);
        }
        $hide_tax = request()->session()->get('business.enable_inline_tax') == 1 ? '' : 'hide';
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        $row_count = request()->input('row_count');
        $product_name=Product::where('product_type',$purchase_order->purchase_type)->get();
        $brand=Brands::All();
        $html =  view('purchase.partials.purchase_order_lines')
                ->with(compact(
                    'purchase_order',
                    'taxes',
                    'product_name',
                    'store',
                    'hide_tax',
                    'currency_details',
                    'row_count',
                    'sub_units_array',
                    'brand'
                ))->render();
    
        return [
            'html' => $html,
            'po' => $purchase_order
        ];

    }

 public function get_delivery_note($purchase_order_id)
    {
        
        
        $business_id = request()->session()->get('user.business_id');
        $store=store::All();
        $delivery_note = Transaction::where('business_id', $business_id)
                        ->whereIn('type', ['delivery_note', 'sales_order'])
                        ->with(['purchase_lines', 'purchase_lines.variations', 
                            'purchase_lines.product', 'purchase_lines.product.unit', 'purchase_lines.variations.product_variation' ])
                        ->findOrfail($purchase_order_id);
        // $c_i = $delivery_note->contact_id ;
        // $contact_i

        $taxes = TaxRate::where('business_id', $business_id)
                            ->ExcludeForTaxGroup()
                            ->get();


        $sub_units_array = [];
        foreach ($delivery_note->purchase_lines as $pl) {
            $sub_units_array[$pl->id] = $this->productUtil->getSubUnits($business_id, $pl->product->unit->id, false, $pl->product_id);
        }
        $hide_tax = request()->session()->get('business.enable_inline_tax') == 1 ? '' : 'hide';
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        $row_count = request()->input('row_count');
        $product=Product::All();
        $brands = Brands::All();
        $further_tax = TaxRate::where('type','further_tax')->get();
        $html =  view('purchase.partials.delivery_order_lines')
                ->with(compact(
                    'further_tax',
                    'brands',
                    'delivery_note',
                    'taxes',
                    'product',
                    'store',
                    'hide_tax',
                    'currency_details',
                    'row_count',
                    'sub_units_array'
                ))->render();
    
        return [
            'html' => $html,
            'po' => $delivery_note
        ];

    }
    

 



    
    /**
     * Checks if ref_number and supplier combination already exists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkRefNumber(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $contact_id = $request->input('contact_id');
        $ref_no = $request->input('ref_no');
        $purchase_id = $request->input('purchase_id');

        $count = 0;
        if (!empty($contact_id) && !empty($ref_no)) {
            //check in transactions table
            $query = Transaction::where('business_id', $business_id)
                            ->where('ref_no', $ref_no)
                            ->where('contact_id', $contact_id);
            if (!empty($purchase_id)) {
                $query->where('id', '!=', $purchase_id);
            }
            $count = $query->count();
        }
        if ($count == 0) {
            echo "true";
            exit;
        } else {
            echo "false";
            exit;
        }
    }

    /**
     * Checks if ref_number and supplier combination already exists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function printInvoice($id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $taxes = TaxRate::where('business_id', $business_id)
                                ->pluck('name', 'id');
            $purchase = Transaction::where('business_id', $business_id)
                                    ->where('id', $id)
                                    ->with(
                                        'contact',
                                        'purchase_lines',
                                        'purchase_lines.product',
                                        'purchase_lines.variations',
                                        'purchase_lines.variations.product_variation',
                                        'location',
                                        'payment_lines'
                                    )
                                    ->first();
            $payment_methods = $this->productUtil->payment_types(null, false, $business_id);

            //Purchase orders
            $purchase_order_nos = '';
            $purchase_order_dates = '';
            if (!empty($purchase->purchase_order_ids)) {
                $purchase_orders = Transaction::find($purchase->purchase_order_ids);

                $purchase_order_nos = implode(', ', $purchase_orders->pluck('ref_no')->toArray());
                $order_dates = [];
                foreach ($purchase_orders as $purchase_order) {
                    $order_dates[] = $this->transactionUtil->format_date($purchase_order->transaction_date, true);
                }
                $purchase_order_dates = implode(', ', $order_dates);
            }

            $output = ['success' => 1, 'receipt' => [], 'print_title' => $purchase->ref_no];
            $output['receipt']['html_content'] = view('purchase.partials.show_details', compact('taxes', 'purchase', 'payment_methods', 'purchase_order_nos', 'purchase_order_dates'))->render();
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];
        }

        return $output;
    }
    
    
    
    //Purchase Requisition
    
    
    
    

    /**
     * Update purchase status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request)
    {
        if (!auth()->user()->can('purchase.update') && !auth()->user()->can('purchase.update_status')) {
            abort(403, 'Unauthorized action.');
        }
        //Check if the transaction can be edited or not.
        $edit_days = request()->session()->get('business.transaction_edit_days');
        if (!$this->transactionUtil->canBeEdited($request->input('purchase_id'), $edit_days)) {
            return ['success' => 0,
                    'msg' => __('messages.transaction_edit_not_allowed', ['days' => $edit_days])];
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            $transaction = Transaction::where('business_id', $business_id)
                                ->where('type', 'purchase')
                                ->with(['purchase_lines'])
                                ->findOrFail($request->input('purchase_id'));

            $before_status = $transaction->status;
            

            $update_data['status'] = $request->input('status');


            DB::beginTransaction();

            //update transaction
            $transaction->update($update_data);

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
            foreach ($transaction->purchase_lines as $purchase_line) {
                $this->productUtil->updateProductStock($before_status, $transaction, $purchase_line->product_id, $purchase_line->variation_id, $purchase_line->quantity, $purchase_line->quantity, $currency_details);
            }

            //Update mapping of purchase & Sell.
            $this->transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($before_status, $transaction, null);

            //Adjust stock over selling if found
            $this->productUtil->adjustStockOverSelling($transaction);

            DB::commit();

            $output = ['success' => 1,
                            'msg' => __('purchase.purchase_update_success')
                        ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => $e->getMessage()
                        ];
        }

        return $output;
    }
    
    
    public function get_type_product($id)
    {
    $product=Product::where('product_type',[$id])->orderBy("name",'asc')->get();
    return json_encode($product);
    }
    
    // for dropdown of product types
    public function get_product_types($id)
    {
        $product_type=Type::where('purchase_type',$id)->orderBy("name",'asc')->get();
        return json_encode($product_type);
    }
    
    
    
}
