<?php

namespace App\Http\Controllers;

use App\Business;
use App\BusinessLocation;
use App\Brands;
use App\Contact;
use App\Vehicle;
use App\store;
use App\CustomerGroup;
use App\PurchaseLine;
use App\TaxRate;
use App\Transaction;
use App\DelTransaction;
use App\Product;
use App\User;
use App\Type;
use App\Utils\BusinessUtil;
use App\purchasetype;
use App\TermsConditions;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Activitylog\Models\Activity;
use \NumberFormatter;
use App\Media;
use App\Account; 
use App\AccountType; 

class PurchaseOrderController extends Controller
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

        $this->purchaseOrderStatuses = [
            'ordered' => [
                'label' => __('lang_v1.ordered'),
                'class' => 'bg-info'
            ],
            'Requested' => [
                'label' => __('Requested'),
                'class' => 'bg-info'
            ],
            'partial' => [
                'label' => __('lang_v1.partial'),
                'class' => 'bg-yellow'
            ],
            'completed' => [
                'label' => __('restaurant.completed'),
                'class' => 'bg-green'
            ]
        ];

        $this->shipping_status_colors = [
            'ordered' => 'bg-yellow',
            'packed' => 'bg-info',
            'shipped' => 'bg-navy',
            'delivered' => 'bg-green',
            'cancelled' => 'bg-red',
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	if (!auth()->user()->can('purchase_order.view_all') && !auth()->user()->can('purchase_order.view_own')) {
            abort(403, 'Unauthorized action.');
        }

        $is_admin = $this->businessUtil->is_admin(auth()->user());
        $shipping_statuses = $this->transactionUtil->shipping_statuses();
    	$business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $purchase_orders = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                    ->join(
                        'business_locations AS BS',
                        'transactions.location_id',
                        '=',
                        'BS.id'
                    )
                    ->leftJoin('purchase_lines as pl', 'transactions.id', '=', 'pl.transaction_id')
                    ->leftJoin('transactions as tr_n', 'transactions.purchase_order_ids', '=', 'tr_n.id')
                    ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
                    ->leftJoin('type as f', 'transactions.purchase_type', '=', 'f.id')
                    ->leftJoin('purchase_type as ptype', 'transactions.purchase_category', '=', 'ptype.id')
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'purchase_order')
                    ->where('transactions.delete_status',1)

                    ->select(
                        'transactions.id',
                        'transactions.document',
                        'transactions.transaction_date',
                        'transactions.ref_no',
                        'transactions.status',
                        'contacts.name',
                        'contacts.supplier_business_name',
                        'transactions.final_total',
                        'f.name as product_type',
                        'BS.name as location_name',
                        'transactions.pay_term_number',
                        'ptype.Type as purchase_type',
                        'transactions.pay_term_type',
                        'transactions.shipping_status',
                         'tr_n.ref_no as purchase_ord',
                        DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by"),
                        DB::raw('SUM(pl.quantity - pl.po_quantity_purchased) as po_qty_remaining')
                    )
                    ->groupBy('transactions.id')->orderby('transactions.id', 'DESC');

            // $permitted_locations = auth()->user()->permitted_locations();
            // if ($permitted_locations != 'all') {
            //     $purchase_orders->whereIn('transactions.location_id', $permitted_locations);
            // }

            if (!empty(request()->supplier_id)) {
                $purchase_orders->where('contacts.id', request()->supplier_id);
            }
            if (!empty(request()->location_id)) {
                $purchase_orders->where('transactions.location_id', request()->location_id);
            }

            if (!empty(request()->status)) {
                $purchase_orders->where('transactions.status', request()->status);
            }
            if (!empty(request()->purchase_category)) {
             
                $purchase_orders->where('transactions.purchase_category', request()->purchase_category);
            }
            
            if (!empty(request()->purchase_type)) {
                $purchase_orders->where('transactions.purchase_type', request()->purchase_type);
            }

            if (!empty(request()->from_dashboard)) {
                $purchase_orders->where('transactions.status', '!=', 'completed')
                    ->orHavingRaw('po_qty_remaining > 0');
            }

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $purchase_orders->whereDate('transactions.transaction_date', '>=', $start)
                            ->whereDate('transactions.transaction_date', '<=', $end);
            }

            if (!auth()->user()->can('purchase_order.view_all') && auth()->user()->can('purchase_order.view_own')) {
                $purchase_orders->where('transactions.created_by', request()->session()->get('user.id'));
            }

            if (!empty(request()->input('shipping_status'))) {
                $purchase_orders->where('transactions.shipping_status', request()->input('shipping_status'));
            }

            return Datatables::of($purchase_orders)
                ->addColumn('action', function ($row) use ($is_admin) {
                    $html="";
                    if (auth()->user()->can("purchase_order.print")) {   
                    $html .= '<a href="#" data-href="' . action('PurchaseOrderController@show', [$row->id]) . '" class="btn-modal btn btn-xs btn-info btn-vew" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i></a>';
                    }
                    if (auth()->user()->can("purchase_order.update")) {
                        $html .= '<a href="' . action('PurchaseOrderController@edit', [$row->id]) . '" class="btn btn-xs btn-primary btn-edt"><i class="fas fa-edit"></i></a>';
                    }
                    if (auth()->user()->can("purchase_order.delete")) {
                        $html .= '<a href="' . action('PurchaseOrderController@destroy', [$row->id]) . '" class="delete-purchase-order btn btn-xs btn-danger btn-dlt"><i class="fas fa-trash"></i></a>';
                    }
                    
                    // $html .= '<div class="btn-group">
                    //         <button type="button" class="btn btn-info dropdown-toggle btn-xs btn-edt" 
                    //             data-toggle="dropdown" aria-expanded="false"><span class="fa fas fa-solid fa-list"></span><span class="sr-only">Toggle Dropdown
                    //             </span>
                    //         </button>
                    //         <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                    // if (auth()->user()->can("purchase_order.view_all") || auth()->user()->can("purchase_order.view_own")) {

                    //     $html .= '<li><a href="#" class="print-invoice" data-href="' . action('PurchaseController@printInvoice', [$row->id]) . '"><i class="fas fa-print" aria-hidden="true"></i>'. __("messages.print") .'</a></li>';
                    // }
                    // if (config('constants.enable_download_pdf') && (auth()->user()->can("purchase_order.view_all") || auth()->user()->can("purchase_order.view_own"))) {
                    //     $html .= '<li><a href="' . route('purchaseOrder.downloadPdf', [$row->id]) . '" target="_blank"><i class="fas fa-print" aria-hidden="true"></i> ' . __("lang_v1.download_pdf") . '</a></li>';
                    // }
                    

                    // if ($is_admin || auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping']) ) {
                    //     $html .= '<li><a href="#" data-href="' . action('SellController@editShipping', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-truck" aria-hidden="true"></i>' . __("lang_v1.edit_shipping") . '</a></li>';
                    // }

                    // if ((auth()->user()->can("purchase_order.view_all") || auth()->user()->can("purchase_order.view_own")) && !empty($row->document)) {
                    //     $document_name = !empty(explode("_", $row->document, 2)[1]) ? explode("_", $row->document, 2)[1] : $row->document ;
                    //     $html .= '<li><a href="' . url('uploads/documents/' . $row->document) .'" download="' . $document_name . '"><i class="fas fa-download" aria-hidden="true"></i>' . __("purchase.download_document") . '</a></li>';
                    //     if (isFileImage($document_name)) {
                    //         $html .= '<li><a href="#" data-href="' . url('uploads/documents/' . $row->document) .'" class="view_uploaded_document"><i class="fas fa-image" aria-hidden="true"></i>' . __("lang_v1.view_document") . '</a></li>';
                    //     }
                    // }
                                        
                    // $html .=  '</ul></div>';
                    return $html;
                })
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    '<span class="final_total" data-orig-value="{{$final_total}}">@format_currency($final_total)</span>'
                )
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('po_qty_remaining', '{{@format_quantity($po_qty_remaining)}}')
                ->editColumn('name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$name}}')
                ->editColumn('status', function($row)use($is_admin){
                    $status = '';
                    $order_statuses = $this->purchaseOrderStatuses;
                    if (array_key_exists($row->status, $order_statuses)) {
                        if ($is_admin && $row->status != 'completed') {
                            $status = '<span class="edit-po-status label ' . $order_statuses[$row->status]['class']
                            . '" data-href="'.action("PurchaseOrderController@getEditPurchaseOrderStatus", ['id' => $row->id]).'">' . $order_statuses[$row->status]['label'] . '</span>';
                        } else {
                            $status = '<span class="label ' . $order_statuses[$row->status]['class']
                            . '" >' . $order_statuses[$row->status]['label'] . '</span>';
                        }
                    }

                    return $status;
                })
                ->editColumn('shipping_status', function ($row) use ($shipping_statuses) {
                    $status_color = !empty($this->shipping_status_colors[$row->shipping_status]) ? $this->shipping_status_colors[$row->shipping_status] : 'bg-gray';
                    $status = !empty($row->shipping_status) ? '<a href="#" class="btn-modal" data-href="' . action('SellController@editShipping', [$row->id]) . '" data-container=".view_modal"><span class="label ' . $status_color .'">' . $shipping_statuses[$row->shipping_status] . '</span></a>' : '';
                     
                    return $status;
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        return  action('PurchaseOrderController@show', [$row->id]) ;
                    }])
                ->rawColumns(['final_total', 'action', 'ref_no', 'name', 'status', 'shipping_status'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id, false);
        $purchaseOrderStatuses = [];
        foreach ($this->purchaseOrderStatuses as $key => $value) {
            $purchaseOrderStatuses[$key] = $value['label'];
        }
        
        $t_no = Transaction::where('type', 'purchase_order')->where('ref_no', 'not like', "%-ovr%")
        ->select("id")
        ->orderBy("id",'desc')->take(1)->count();
        $product_type = Type::orderBy("name",'asc')->where('is_milling','0')->get();
        $purchase_type = purchasetype::orderBy("Type",'asc')->get();
        
        
        return view('purchase_order.index')->with(compact('business_locations', 'suppliers', 'purchaseOrderStatuses', 'shipping_statuses','t_no','product_type','purchase_type'));
    }

    public function index_requision()
    {
          if (!auth()->user()->can('purchase.purchase_req.view') ) {
            abort(403, 'Unauthorized action.');
        }


        $is_admin = $this->businessUtil->is_admin(auth()->user());
        $shipping_statuses = $this->transactionUtil->shipping_statuses();
    	$business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $purchase_orders = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                    ->join(
                        'business_locations AS BS',
                        'transactions.location_id',
                        '=',
                        'BS.id'
                    )
                    ->leftJoin('purchase_lines as pl', 'transactions.id', '=', 'pl.transaction_id')
                    ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
                    ->leftJoin('type as f', 'transactions.purchase_type', '=', 'f.id')
                    ->leftJoin('purchase_type as ptype', 'transactions.purchase_category', '=', 'ptype.id')
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'Purchase Requisition')
                    ->where('transactions.delete_status', 1)
                    ->select(
                        'transactions.id',
                        'transactions.document',
                        DB::raw('DATE_FORMAT(transactions.transaction_date, "%d-%m-%Y") as transaction_date'),
                        DB::raw('DATE_FORMAT(transactions.expected_date, "%d-%m-%Y") as expected_date'),
                        'transactions.ref_no',
                        'transactions.status',
                        'contacts.name',
                        'f.name as product_type',
                        'ptype.Type as purchase_type',
                        'contacts.supplier_business_name',
                        'transactions.final_total',
                        'BS.name as location_name',
                        'transactions.pay_term_number',
                        'transactions.pay_term_type',
                        'transactions.shipping_status',
                        DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by"),
                        DB::raw('SUM(pl.quantity - pl.po_quantity_purchased) as po_qty_remaining')
                        // DB::raw('SUM(pl.quantity - pl.po_quantity_purchased) as po_qty_remaining != 0 ')
                    )
                    ->groupBy('transactions.id')->orderby('transactions.id', 'desc');
                    // ->having('po_qty_remaining', '!=', 0);

            // $permitted_locations = auth()->user()->permitted_locations();
            // if ($permitted_locations != 'all') {
            //     $purchase_orders->whereIn('transactions.location_id', $permitted_locations);
            // }

            if (!empty(request()->supplier_id)) {
                $purchase_orders->where('contacts.id', request()->supplier_id);
            }
            if (!empty(request()->location_id)) {
                $purchase_orders->where('transactions.location_id', request()->location_id);
            }

            if (!empty(request()->status)) {
                $purchase_orders->where('transactions.status', request()->status);
            }

            if (!empty(request()->purchase_category)) {
             
                $purchase_orders->where('transactions.purchase_category', request()->purchase_category);
            }
            
            if (!empty(request()->purchase_type)) {
                $purchase_orders->where('transactions.purchase_type', request()->purchase_type);
            }

            if (!empty(request()->from_dashboard)) {
                $purchase_orders->where('transactions.status', '!=', 'completed')
                    ->orHavingRaw('po_qty_remaining > 0');
            }

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $purchase_orders->whereDate('transactions.transaction_date', '>=', $start)
                            ->whereDate('transactions.transaction_date', '<=', $end);
            }

            if (!auth()->user()->can('purchase_order.view_all') && auth()->user()->can('purchase_order.view_own')) {
                $purchase_orders->where('transactions.created_by', request()->session()->get('user.id'));
            }

            if (!empty(request()->input('shipping_status'))) {
                $purchase_orders->where('transactions.shipping_status', request()->input('shipping_status'));
            }

            return Datatables::of($purchase_orders)
                ->addColumn('created_at', function ($row) {
                    return optional($row->created_at)->format('d/m/Y'); // Format created_at date
                })
                ->addColumn('action', function ($row) use ($is_admin) {
                     $html ="";
                    if (auth()->user()->can("purchase.purchase_req.print")) {
                    $html .= '<a href="#" data-href="' . action('PurchaseOrderController@show', [$row->id]) . '" class="btn-modal btn btn-xs btn-info btn-vew" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i></a>';
                    }
                    if (auth()->user()->can("purchase.purchase_req.edit")) {
                        $html .= '<a href="' . action('PurchaseOrderController@edit_req', [$row->id]) . '" class="btn btn-xs btn-primary btn-edt"><i class="fas fa-edit"></i></a>';
                    }
                    if (auth()->user()->can("purchase.purchase_req.delete")) {
                        $html .= '<a href="' . action('PurchaseOrderController@destroy', [$row->id]) . '" class="delete-purchase-order btn btn-xs btn-danger btn-dlt"><i class="fas fa-trash"></i></a>';
                    }

                    // $html .= '<div class="btn-group">
                    //         <button type="button" class="btn btn-info dropdown-toggle btn-xs btn-edt" 
                    //             data-toggle="dropdown" aria-expanded="false"><span class="fa fas fa-solid fa-list"></span><span class="sr-only">Toggle Dropdown
                    //             </span>
                    //         </button>
                    //         <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                    // if (auth()->user()->can("purchase_order.view_all") || auth()->user()->can("purchase_order.view_own")) { 

                    //     $html .= '<li><a href="#" class="print-invoice btn-modal" data-href="' . action('PurchaseOrderController@show', ['id' => $row->id, 'isprint' => true]) . '"  data-container=".view_modal"><i class="fas fa-print" aria-hidden="true"></i>'. __("messages.print") .'</a></li>';
                    // }
                    // if (config('constants.enable_download_pdf') && (auth()->user()->can("purchase_order.view_all") || auth()->user()->can("purchase_order.view_own"))) {
                    //     $html .= '<li><a href="' . route('purchaseOrder.downloadPdf', [$row->id]) . '" target="_blank"><i class="fas fa-print" aria-hidden="true"></i> ' . __("lang_v1.download_pdf") . '</a></li>';
                    // }
               

                    // if ($is_admin || auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping']) ) {
                    //     $html .= '<li><a href="#" data-href="' . action('SellController@editShipping', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-truck" aria-hidden="true"></i>' . __("lang_v1.edit_shipping") . '</a></li>';
                    // }

                    // if ((auth()->user()->can("purchase_order.view_all") || auth()->user()->can("purchase_order.view_own")) && !empty($row->document)) {
                    //     $document_name = !empty(explode("_", $row->document, 2)[1]) ? explode("_", $row->document, 2)[1] : $row->document ;
                    //     $html .= '<li><a href="' . url('uploads/documents/' . $row->document) .'" download="' . $document_name . '"><i class="fas fa-download" aria-hidden="true"></i>' . __("purchase.download_document") . '</a></li>';
                    //     if (isFileImage($document_name)) {
                    //         $html .= '<li><a href="#" data-href="' . url('uploads/documents/' . $row->document) .'" class="view_uploaded_document"><i class="fas fa-image" aria-hidden="true"></i>' . __("lang_v1.view_document") . '</a></li>';
                    //     }
                    // }
                                        
                    // $html .=  '</ul></div>';
                    return $html;
                })
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    '<span class="final_total" data-orig-value="{{$final_total}}">@format_currency($final_total)</span>'
                )
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('po_qty_remaining', '{{@format_quantity($po_qty_remaining)}}')
                ->editColumn('name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$name}}')
                ->editColumn('status', function($row)use($is_admin){
                    $status = '';
                    $order_statuses = $this->purchaseOrderStatuses;
                    if (array_key_exists($row->status, $order_statuses)) {
                        if ($is_admin && $row->status != 'completed') {
                            $status = '<span class="edit-po-status label ' . $order_statuses[$row->status]['class']
                            . '" data-href="'.action("PurchaseOrderController@getEditPurchaseOrderStatus", ['id' => $row->id]).'">' . $order_statuses[$row->status]['label'] . '</span>';
                        } else {
                            $status = '<span class="label ' . $order_statuses[$row->status]['class']
                            . '" >' . $order_statuses[$row->status]['label'] . '</span>';
                        }
                    }

                    return $status;
                })
                ->editColumn('shipping_status', function ($row) use ($shipping_statuses) {
                    $status_color = !empty($this->shipping_status_colors[$row->shipping_status]) ? $this->shipping_status_colors[$row->shipping_status] : 'bg-gray';
                    $status = !empty($row->shipping_status) ? '<a href="#" class="btn-modal" data-href="' . action('SellController@editShipping', [$row->id]) . '" data-container=".view_modal"><span class="label ' . $status_color .'">' . $shipping_statuses[$row->shipping_status] . '</span></a>' : '';
                     
                    return $status;
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        return  action('PurchaseOrderController@show', [$row->id]) ;
                    }])
                ->rawColumns(['final_total', 'action', 'ref_no', 'name', 'status', 'shipping_status'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id, false);
        $purchaseOrderStatuses = [];
        foreach ($this->purchaseOrderStatuses as $key => $value) {
            $purchaseOrderStatuses[$key] = $value['label'];
        }
        $product_type = Type::orderBy("name",'asc')->where('is_milling','0')->get();
        $purchase_type = purchasetype::orderBy("Type",'asc')->get();
        
        $t_no = Transaction::where('type', 'Purchase Requisition')
        ->where('ref_no', 'not like', "%-ovr%")
        ->select('ref_no', DB::raw("substring_index(substring_index(ref_no,'-',-1),',',-1) as max_no"))->get()
        ->max('max_no');
        if(empty($t_no)){
            $t_no = 1;
        }else{
            $break_no = explode("-",$t_no);
            $t_no = end($break_no)+1;
        }

        return view('purchase_requision.index')->with(compact('business_locations', 'suppliers', 'purchaseOrderStatuses', 'shipping_statuses','t_no','product_type','purchase_type'));
    }


    // 

    public function index_invoice()
    {

         if (!auth()->user()->can('purchase_invoice.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $is_admin = $this->businessUtil->is_admin(auth()->user());
        $shipping_statuses = $this->transactionUtil->shipping_statuses();
    	$business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $purchase_orders = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                    ->join(
                        'business_locations AS BS',
                        'transactions.location_id',
                        '=',
                        'BS.id'
                    )
                    ->leftJoin('purchase_lines as pl', 'transactions.id', '=', 'pl.transaction_id')
                     ->leftJoin('transactions as pro_no', 'transactions.purchase_order_ids', '=', 'pro_no.id')
                    ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
                    ->leftJoin('type as f', 'transactions.purchase_type', '=', 'f.id')
                    ->leftJoin('purchase_type as ptype', 'transactions.purchase_category', '=', 'ptype.id')
                    ->leftJoin('accounts as account', 'transactions.transaction_account', '=', 'account.id')
                    ->leftJoin('contacts as sales_m', 'transactions.sales_man', '=', 'sales_m.id')
                    ->leftJoin('contacts as trans', 'transactions.transporter_name', '=', 'trans.id')
                    ->leftJoin('vehicle AS vl','transactions.vehicle_no','=','vl.id')
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'Purchase_invoice')
                    ->where('transactions.delete_status', 1)
                    ->select(
                        'transactions.id',
                        'transactions.final_total as invoice_total',
                        'transactions.document',
                        'account.name as transaction_account',
                        'transactions.transaction_date',
                        'transactions.ref_no',
                        'trans.supplier_business_name as tname',
                        'vl.vhicle_number as vehicle',
                        'transactions.status',
                        'sales_m.supplier_business_name as sales_man',
                        'contacts.name',
                        'f.name as product_type',
                        'contacts.supplier_business_name' ,
                        'transactions.final_total',
                        'ptype.Type as purchase_type',
                        'BS.name as location_name',
                        'transactions.pay_term_number',
                        'transactions.pay_term_type',
                        'transactions.shipping_status',
                        'pro_no.ref_no as pro_no',
                        
                        DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by"),
                        DB::raw('SUM(pl.quantity - pl.po_quantity_purchased) as po_qty_remaining')
                    )
                    ->groupBy('transactions.id')->orderby('transactions.id', 'desc');

            // $permitted_locations = auth()->user()->permitted_locations();
            // if ($permitted_locations != 'all') {
            //     $purchase_orders->whereIn('transactions.location_id', $permitted_locations);
            // }

            if (!empty(request()->supplier_id)) {
                $purchase_orders->where('contacts.id', request()->supplier_id);
            }
            if (!empty(request()->location_id)) {
                $purchase_orders->where('transactions.location_id', request()->location_id);
            }

            if (!empty(request()->status)) {
                $purchase_orders->where('transactions.status', request()->status);
            }

            if (!empty(request()->from_dashboard)) {
                $purchase_orders->where('transactions.status', '!=', 'completed')
                    ->orHavingRaw('po_qty_remaining > 0');
            }

            if (!empty(request()->purchase_category)) {
             
                $purchase_orders->where('transactions.purchase_category', request()->purchase_category);
            }
            

            if (!empty(request()->sales_man)) {
             
                $purchase_orders->where('transactions.sales_man', request()->sales_man);
            }

            if (!empty(request()->transaction_accounts)) {
             
                $purchase_orders->where('transactions.transaction_account', request()->transaction_accounts);
            }

            if (!empty(request()->transporter_name)) {
          
                $purchase_orders->where('transactions.transporter_name', request()->transporter_name);
            }

            if (!empty(request()->vehicle_number)) {
             
                $purchase_orders->where('transactions.vehicle_no', request()->vehicle_number);
            }
            
            if (!empty(request()->purchase_type)) {
                $purchase_orders->where('transactions.purchase_type', request()->purchase_type);
            }
            

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $purchase_orders->whereDate('transactions.transaction_date', '>=', $start)
                            ->whereDate('transactions.transaction_date', '<=', $end);
            }

            if (!auth()->user()->can('purchase_order.view_all') && auth()->user()->can('purchase_order.view_own')) {
                $purchase_orders->where('transactions.created_by', request()->session()->get('user.id'));
            }

            if (!empty(request()->input('shipping_status'))) {
                $purchase_orders->where('transactions.shipping_status', request()->input('shipping_status'));
            }

            return Datatables::of($purchase_orders)
                ->addColumn('action', function ($row) use ($is_admin) {


                      $html ='';
                      if (auth()->user()->can("purchase_invoice.print")) {
                     $html .= '<a href="#" data-href="' . action('PurchaseOrderController@show', [$row->id]) . '" class="btn-modal btn btn-xs btn-info btn-vew" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i></a>';
                      }
                    if (auth()->user()->can("purchase_invoice.edit")) {
                        $html .= '<a href="' . action('PurchaseOrderController@edit_invoice', [$row->id]) . '" class="btn btn-xs btn-primary btn-edt"><i class="fas fa-edit"></i></a>';
                    }
                    if (auth()->user()->can("purchase_invoice.delete")) {
                        $html .= '<a href="' . action('PurchaseOrderController@destroy', [$row->id]) . '" class="delete-purchase-order btn btn-xs btn-danger btn-dlt"><i class="fas fa-trash"></i></a>';
                    }


                   
                    return $html;
                })
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    '<span class="final_total" data-orig-value="{{$final_total}}">@format_currency($final_total)</span>'
                )
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('po_qty_remaining', '{{@format_quantity($po_qty_remaining)}}')
                ->editColumn('name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$name}}')
                ->editColumn('status', function($row)use($is_admin){
                    $status = '';
                    $order_statuses = $this->purchaseOrderStatuses;
                    if (array_key_exists($row->status, $order_statuses)) {
                        if ($is_admin && $row->status != 'completed') {
                            $status = '<span class="edit-po-status label ' . $order_statuses[$row->status]['class']
                            . '" data-href="'.action("PurchaseOrderController@getEditPurchaseOrderStatus", ['id' => $row->id]).'">' . $order_statuses[$row->status]['label'] . '</span>';
                        } else {
                            $status = '<span class="label ' . $order_statuses[$row->status]['class']
                            . '" >' . $order_statuses[$row->status]['label'] . '</span>';
                        }
                    }

                    return $status;
                })
                ->editColumn('shipping_status', function ($row) use ($shipping_statuses) {
                    $status_color = !empty($this->shipping_status_colors[$row->shipping_status]) ? $this->shipping_status_colors[$row->shipping_status] : 'bg-gray';
                    $status = !empty($row->shipping_status) ? '<a href="#" class="btn-modal" data-href="' . action('SellController@editShipping', [$row->id]) . '" data-container=".view_modal"><span class="label ' . $status_color .'">' . $shipping_statuses[$row->shipping_status] . '</span></a>' : '';
                     
                    return $status;
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        return  action('PurchaseOrderController@show', [$row->id]) ;
                    }])
                ->rawColumns(['final_total', 'action', 'ref_no', 'name', 'status', 'shipping_status'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id, false);
        $purchaseOrderStatuses = [];
        foreach ($this->purchaseOrderStatuses as $key => $value) {
            $purchaseOrderStatuses[$key] = $value['label'];
        }
        
     
        //   $t_no = Transaction::where(['type' => "Purchase_invoice",'delete_status'=>1])->where('ref_no', 'not like', "%-ovr%")->max("ref_no");
        // if(empty($t_no)){ 
        //     $t_no = 1;
        // }else{
        //     $break_no = explode("-",$t_no);
        //     $t_no = end($break_no)+1;
        // }
        
        $t_no = Transaction::where('type', 'Purchase_invoice')->where('ref_no', 'not like', "%-ovr%")->select("id")->orderBy("id",'desc')->take(1)->count();
        if(empty($t_no)){
            $unni = 1;
        }else{
            $break_no = explode("-",$t_no);
            $unni = end($break_no)+1;
        }
        
        // transaction no for pm
        $pm_no = Transaction::where('type', 'Purchase_invoice')
        ->where('ref_no', 'not like', "%-ovr%")
        ->where('ref_no', 'like', '%PI-PM%')
        ->select('ref_no', DB::raw("substring_index(substring_index(ref_no,'-',-1),',',-1) as max_no"))->get()
        ->max('max_no');
        if(empty($pm_no)){
            $pm_no = 1;
        }else{
            $break_no = explode("-",$pm_no);
            $pm_no = end($break_no)+1;
        }
        // transaction no for rmp
        $rmp_no = Transaction::where('type', 'Purchase_invoice')
        ->where('ref_no', 'not like', "%-ovr%")
        ->where('ref_no', 'like', '%PI-RMP%')
        ->select('ref_no', DB::raw("substring_index(substring_index(ref_no,'-',-1),',',-1) as max_no"))->get()
        ->max('max_no');
        if(empty($rmp_no)){
            $rmp_no = 1;
        }else{
            $break_no = explode("-",$rmp_no);
            $rmp_no = end($break_no)+1;
        }
        $product_type = Type::orderBy("name",'asc')->where('is_milling','0')->get();
        $purchase_type = purchasetype::orderBy("Type",'asc')->get();
        $transporter= Contact::where('type','Transporter')->get();
        $vehicles=vehicle::All();
        $sales_man=Contact::where('type','Agent')->get();
        $transaction_accounts=Account::where('is_closed','0')->get();
        
        return view('purchase_order_invoice.index')->with(compact('business_locations','t_no','pm_no','rmp_no','suppliers', 'purchaseOrderStatuses', 'shipping_statuses','product_type','purchase_type','transporter','vehicles','transaction_accounts','sales_man'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    	if (!auth()->user()->can('purchase_order.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $taxes = TaxRate::where('business_id', $business_id)
                        ->ExcludeForTaxGroup()
                        ->get();

        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $bl_attributes = $business_locations['attributes'];
        $business_locations = $business_locations['locations'];

        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        
        $t_no = Transaction::where('type', 'purchase_order')->where('ref_no', 'not like', "%-ovr%")
        ->select("id")
        ->orderBy("id",'desc')->take(1)->count();
        $prefix=Business::first();
        $PO_pre=$prefix->ref_no_prefixes['purchase_order'];

        $brand=Brands::All();

        if($t_no== 0){

          $unni=1;
        }else{
            $tr_no=$t_no+1;
            $unni = $tr_no;
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
        $p_type = Type::orderBy("name",'asc')->get();
        $T_C = TermsConditions::All();

        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);
        $default_id  = DB::table('default_account')->where('form_type','Purchase_invoice')->where('field_type','salesman')->first()->account_id ?? 0;
        $default_sales_man=Account::where('id',$default_id)->first()->contact_id ?? 0;

        $Prq = Transaction::with('contact')->where('type',"Purchase Requisition")
        ->Join('purchase_lines as pl', 'transactions.id', '=', 'pl.transaction_id')
        ->select('transactions.ref_no', 'transactions.id',DB::raw('SUM(pl.quantity - pl.po_quantity_purchased) as po_qty_remaining'))
        ->where('delete_status',1)
        ->where('purchase_order_ids',0)
        ->groupBy('transactions.id')
        ->get()
        ->pluck('ref_no', 'id');

        
        // dd($Prq);
        
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
            
        $store=Store::pluck('name', 'name');
        
        $supplier=Contact::orderBy('supplier_business_name', 'ASC')->pluck('supplier_business_name','id');

        $sale_man=Contact::where('type','Agent')->get();
        
        $shipping_statuses = $this->transactionUtil->shipping_statuses();
        $purchase_category = purchasetype::orderBy("Type",'asc')->get();
        $tax_rate = TaxRate::All();
        return view('purchase_order.create')
            ->with(compact('tax_rate','taxes','unni','sale_man','PO_pre','T_C','p_type','Prq','supplier','store','product','business_locations', 'currency_details', 'customer_groups', 'types', 'shortcuts', 'bl_attributes', 'shipping_statuses','purchase_category','brand','default_sales_man'));
    }
    
    
    public function get_term($id){
        $title = TermsConditions::find($id);
	    return response()->json($title);
    }
    

    public function create_requision()
    {
    
        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $taxes = TaxRate::where('business_id', $business_id)
                        ->ExcludeForTaxGroup()
                        ->get();

        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $bl_attributes = $business_locations['attributes'];
        $business_locations = $business_locations['locations'];
        
        $prefix=Business::first();
        $pr=$prefix->ref_no_prefixes['purchase_requision'];

        $brand=Brands::All();
        
        $t_no = Transaction::where('type', 'Purchase Requisition')
        ->where('ref_no', 'not like', "%-ovr%")
        ->select('ref_no', DB::raw("substring_index(substring_index(ref_no,'-',-1),',',-1) as max_no"))->get()
        ->max('max_no');
        if(empty($t_no)){
            $unni = 1;
        }else{
            $break_no = explode("-",$t_no);
            $unni = end($break_no)+1;
        }
        
        $sale_man=Contact::where('type','Agent')->get();
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

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
        $p_type = Type::orderBy("name",'asc')->get();
        $purchase_category = purchasetype::orderBy("Type",'asc')->get();
        $T_C = TermsConditions::All();

        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        $shipping_statuses = $this->transactionUtil->shipping_statuses();
        
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
            
        $store=Store::pluck('name', 'name');
        $supplier=Contact::pluck('supplier_business_name','id');

        return view('purchase_requision.create')
            ->with(compact('taxes','unni','sale_man','pr','T_C','p_type','supplier','store','product','business_locations',
            'currency_details', 'customer_groups', 'types', 'shortcuts', 'bl_attributes', 'shipping_statuses',
            'purchase_category','brand'));
    }

    public function convert_pr_to_po($id)
    {
        
        if (!auth()->user()->can('purchase_order.update')) {
            abort(403, 'Unauthorized action.');
        }
        $delete = "convert_pr_to_po";
        $business_id = request()->session()->get('user.business_id');

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
        
        
        
        $purchase->type = "purchase_order";
        $purchase->status = "received";
         $purchase->purchase_order_ids = $id ;
        
  
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
        $prefix = 'PO-' . $type_prefix->prefix;
        
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
            
            // dd($transaction);
            
            $transaction_prq = Transaction::where("id",$id)->first();
            $transaction_prq->purchase_order_ids = $transaction->id ;
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
                            'msg' => __('Convert  PR TO PO Successfully')
                        ];
                         return redirect()->route('purchase-order.edit',[$transaction->id , 'delete' => $delete ]);
    }
    
    public function convert_po_to_grn($id){
        if (!auth()->user()->can('purchase_order.update')) {
            abort(403, 'Unauthorized action.');
        }
        $delete = "convert_po_to_grn";

        $business_id = request()->session()->get('user.business_id');

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
        
        
        $purchase->type = "purchase";
        $purchase->po_ref_no = $purchase->ref_no;
        $purchase->custom_field_4="convert";
        $purchase->purchase_order_ids=$id;
        
        // dd($purchase);
        

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
            $prefix = 'GRN-' . $type_prefix->prefix;
            
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
                            'msg' => __('Convert  PO TO GRN Successfully')
                        ];
                                                return redirect()->route('purchases.edit',[$transaction->id ,'delete' => $delete]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('purchase_order.create')) {
            abort(403, 'Unauthorized action.');
        }
           $request->validate([
                'ref_no'=> 'required|unique:transactions',
            ]);
      try {
            $business_id = $request->session()->get('user.business_id');

            $transaction_data = $request->only([ 'ref_no', 'contact_id', 'transaction_date','posting_date','delivery_date','tandc_type','tandc_title','purchase_type',
            'total_before_tax', 'location_id','discount_type', 'discount_amount','tax_id', 'tax_amount', 'shipping_details', 'shipping_charges', 'final_total', 
            'additional_notes', 'exchange_rate', 'pay_term_number', 'pay_term_type', 'shipping_address', 'shipping_status', 'delivered_to','sales_man','pay_type',
            'purchase_category']);

            $exchange_rate = $transaction_data['exchange_rate'];

            //Reverse exchange rate and save it.
            //$transaction_data['exchange_rate'] = $transaction_data['exchange_rate'];

            //TODO: Check for "Undefined index: total_before_tax" issue
            //Adding temporary fix by validating
            $request->validate([
                'contact_id' => 'required',
                'transaction_date' => 'required',
                'posting_date' => 'required',
                'delivery_date' => 'required',
                'total_before_tax' => 'required',
                'location_id' => 'required',
                'final_total' => 'required',
                'document' => 'file|max:'. (config('constants.document_size_limit') / 1000)
            ]);

            $user_id = $request->session()->get('user.id');
            $enable_product_editing = $request->session()->get('business.enable_editing_product_from_purchase');

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
            $transaction_data['type'] = 'purchase_order';
            $transaction_data['status'] = 'ordered';
            $transaction_data['transaction_date'] = $this->productUtil->uf_date($transaction_data['transaction_date'], false);
            $transaction_data['purchase_order_ids']=(int)$request->input('purchase_order_ids');

            //upload document
            $transaction_data['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');
            
            $transaction_data['pay_type'] =$request->input('Pay_type');
            DB::beginTransaction();
          
            //Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount($transaction_data['type']);
            //Generate reference number
            $transaction_data['ref_no'] = $request->prefix."".$transaction_data['ref_no'];

              
            $transaction_data['gross_weight']  = $request->input('total_gross__weight');
            $transaction_data['net_weight']    = $request->input('total_net__weight');
            //   dd($transaction_data);
            $transaction = Transaction::create($transaction_data);
            
          
            if($transaction_data['purchase_order_ids']!=0)
            {
            $purchase_requisition=Transaction::where('id',$transaction_data['purchase_order_ids'])->where('type','Purchase Requisition')->first();
            $purchase_requisition->purchase_order_ids=$transaction_data['purchase_order_ids'];
            $purchase_requisition->save();
            }
            //Upload Shipping documents
            Media::uploadMedia($business_id, $transaction, $request, 'shipping_documents', false, 'shipping_document');
            
            $purchase_lines = [];
            $purchases = $request->input('purchases');

            $this->productUtil->createOrUpdatePurchaseLines($transaction, $purchases, $currency_details, $enable_product_editing);

            $this->transactionUtil->activityLog($transaction, 'added');
            
            DB::commit();
            
            $output = ['success' => 1,
                            'msg' => __('lang_v1.added_success')
                        ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => $e->getMessage()
                        ];
        }

        
        
        if ($request->input('submit_type') == 'save_n_add_another') {
            return redirect()->action(
                'PurchaseOrderController@create'
            )->with('status', $output);
        }

        return redirect()->action('PurchaseOrderController@index')->with('status', $output);
    }

    public function store_requision(Request $request)
    {
        
        $request->validate([
         'ref_no'=> 'required|unique:transactions',
        ]);

        try {
            $business_id = $request->session()->get('user.business_id');

            //Check if subscribed or not
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse(action('PurchaseController@index'));
            }

            $transaction_data = $request->only([ 'ref_no', 'contact_id', 'transaction_date','posting_date','delivery_date','expected_date','tandc_type','tandc_title','purchase_type','total_before_tax', 'location_id','discount_type', 'discount_amount','tax_id', 'tax_amount', 'shipping_details', 'shipping_charges', 'final_total', 'additional_notes', 'exchange_rate', 'pay_term_number', 'pay_term_type', 'shipping_address', 'shipping_status', 'delivered_to','sales_man']);
            $exchange_rate = $transaction_data['exchange_rate'];

          
            //Reverse exchange rate and save it.
            //$transaction_data['exchange_rate'] = $transaction_data['exchange_rate'];

            //TODO: Check for "Undefined index: total_before_tax" issue
            //Adding temporary fix by validating
            $request->validate([
                'posting_date' => 'required',
                'total_before_tax' => 'required',
                'location_id' => 'required',
                'final_total' => 'required',
                'document' => 'file|max:'. (config('constants.document_size_limit') / 1000)
            ]);

            $user_id = $request->session()->get('user.id');
            $enable_product_editing = $request->session()->get('business.enable_editing_product_from_purchase');

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
            $transaction_data['type'] = 'Purchase Requisition';
            $transaction_data['status'] = 'Requested';
            
            $transaction_data['transaction_date'] = $this->productUtil->uf_date($transaction_data['transaction_date'], false);
            // dd("Sa");

            
            $transaction_data['gross_weight']  = $request->input('total_gross__weight');
            $transaction_data['net_weight']    = $request->input('total_net__weight');

            //upload document
            $transaction_data['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');
            
            DB::beginTransaction();

            //Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount($transaction_data['type']);
            $transaction_data['ref_no'] =$request->prefix."".$transaction_data['ref_no'];

            $transaction_data['purchase_category'] = $request->input('purchase_category');

            $transaction = Transaction::create($transaction_data);

            //Upload Shipping documents
            Media::uploadMedia($business_id, $transaction, $request, 'shipping_documents', false, 'shipping_document');
            
            $purchase_lines = [];
            $purchases = $request->input('purchases');


            $this->productUtil->createOrUpdatePurchaseLines($transaction, $purchases, $currency_details, $enable_product_editing);

            $this->transactionUtil->activityLog($transaction, 'added');
            
            DB::commit();
            
            $output = ['success' => 1,
                            'msg' => __('lang_v1.added_success')
                        ];
        } 
        catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __( $e->getMessage())
                        ];
        }

        if ($request->input('submit_type') == 'save_n_add_another') {
            return redirect()->action(
                'PurchaseOrderController@create_requision'
            )->with('status', $output);
        }

        return redirect()->action('PurchaseOrderController@index_requision')->with('status', $output);
    }


    public function create_invoice()
    {
    	if (!auth()->user()->can('purchase_invoice.add')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $taxes = TaxRate::where('business_id', $business_id)
                        ->where('type', 'sales_tax')
                        ->ExcludeForTaxGroup()
                        ->get();
                        
        $further_taxes = TaxRate::where('business_id', $business_id)
                        ->where('type', 'further_tax')
                        ->get();

        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $bl_attributes = $business_locations['attributes'];
        $business_locations = $business_locations['locations'];
   
        $prefix=Business::first();
        $pi=$prefix->ref_no_prefixes['purchase_invoice'];
        

        $t_no = Transaction::where('type', 'Purchase_invoice')->where('ref_no', 'not like', "%-ovr%")->select("id")->orderBy("id",'desc')->take(1)->count();
        if(empty($t_no)){
            $unni = 1;
        }else{
            $break_no = explode("-",$t_no);
            $unni = end($break_no)+1;
        }
        
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

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
        $p_type = Type::orderBy("name",'asc')->get();
        $T_C = TermsConditions::All();
        $shipping_statuses = $this->transactionUtil->shipping_statuses();
        $brand=Brands::All();
        
        $supplier=Contact::pluck('supplier_business_name','id');
        
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
            
        $store=Store::pluck('name', 'id');
        $default_id  = DB::table('default_account')->where('form_type','Purchase_invoice')->where('field_type','salesman')->first()->account_id ?? 0;
        $default_sales_man=Account::where('id',$default_id)->first()->contact_id ?? 0;
        $sale_man=Contact::where('type','Agent')->get();

        $grn = Transaction::where('type',"purchase")->where('delete_status',1)->where('grn_id',"0")->get();
        $purchase_category = purchasetype::orderBy("Type",'asc')->get();

        
        // transaction no for pm
        $pm_no = Transaction::where('type', 'Purchase_invoice')
        ->where('ref_no', 'not like', "%-ovr%")
        ->where('ref_no', 'like', '%PI-PM%')
        ->max("ref_no");
        if(empty($pm_no)){
            $pm_no = 1;
        }else{
            $break_no = explode("-",$pm_no);
            $pm_no = end($break_no)+1;
        }
        // transaction no for rmp
        $rmp_no = Transaction::where('type', 'Purchase_invoice')
        ->where('ref_no', 'not like', "%-ovr%")
        ->where('ref_no', 'like', '%PI-RMP%')
        ->max("ref_no");
        if(empty($rmp_no)){
            $rmp_no = 1;
        }else{
            $break_no = explode("-",$rmp_no);
            $rmp_no = end($break_no)+1;
        }

        $transporter = Contact::where('type','Transporter')->get();
        $contractor  = Contact::where('type','contracter')->orderBy('supplier_business_name', 'ASC')->get();
        $accounts    = Account::pluck('name','id');
        
        $transaction_account   = DB::table('default_account')->where('form_type','Purchase_invoice')->where('field_type','sales_account')->first()->account_id ?? 0;
        $addless_charges       = DB::table('default_account')->where('form_type','Purchase_invoice')->where('field_type','addAndlessCharges')->first()->account_id ?? 0;
        
        $default_contractor    = DB::table('default_account')->where('form_type','Purchase_invoice')->where('field_type','default_contractor')->first()->account_id ?? 0;
        $default_production_contractor       = DB::table('default_account')->where('form_type','Purchase_invoice')->where('field_type','default_production_contractor')->first()->account_id ?? 0;
        return view('purchase_order_invoice.create')
            ->with(compact('addless_charges','transaction_account','transporter','contractor','taxes','pi','unni','sale_man','T_C','grn','p_type','store','supplier','business_locations',
            'product','currency_details', 'customer_groups', 'types', 'shortcuts', 'bl_attributes', 'shipping_statuses','purchase_category'
            ,'pm_no','rmp_no','further_taxes','accounts','brand','default_sales_man','default_production_contractor','default_contractor'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_invoice(Request $request)
    {
           $request->validate([
                'ref_no'=> 'required|unique:transactions',
            ]);
  
        try {

            DB::beginTransaction();
            
            $business_id = $request->session()->get('user.business_id');

            //Check if subscribed or not
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse(action('PurchaseController@index'));
            }

            $transaction_data = $request->only([ 'ref_no', 'contact_id', 'transaction_date','posting_date','delivery_date',
            'tandc_type','tandc_title','purchase_type','total_before_tax', 'location_id','discount_type', 'discount_amount',
            'tax_id', 'tax_amount', 'shipping_details', 'shipping_charges', 'final_total', 'additional_notes', 'exchange_rate', 
            'pay_term_number', 'pay_term_type', 'shipping_address', 'shipping_status', 'delivered_to','sales_man','purchase_category',
            'transporter_name','vehicle_no','contractor','add_charges','less_charges','transaction_account','prd_contractor']);

            $exchange_rate = $transaction_data['exchange_rate'];

          
            //Reverse exchange rate and save it.
            //$transaction_data['exchange_rate'] = $transaction_data['exchange_rate'];

            //TODO: Check for "Undefined index: total_before_tax" issue
            //Adding temporary fix by validating
            $request->validate([
                'contact_id' => 'required',
                'transaction_date' => 'required',
                'posting_date' => 'required',
                'delivery_date' => 'required',
                'total_before_tax' => 'required',
                'location_id' => 'required',
                'final_total' => 'required',
                'document' => 'file|max:'. (config('constants.document_size_limit') / 1000)
            ]);
            
            
            
            $user_id = $request->session()->get('user.id');
            $enable_product_editing = $request->session()->get('business.enable_editing_product_from_purchase');
                
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
            $transaction_data['contractor'] = $request->input('contractor');
            $transaction_data['transporter_name'] = $request->input('transporter_name');
            $transaction_data['type'] = 'Purchase_invoice';
            $transaction_data['status'] ='received';
            $transaction_data['transaction_date'] = $this->productUtil->uf_date($transaction_data['transaction_date'], false);
             $transaction_data['purchase_order_ids']=(int)$request->input('purchase_order_ids');
            

            $transaction_data['gross_weight']  = $request->input('total_gross__weight');
            $transaction_data['net_weight']    = $request->input('total_net__weight');
            //upload document
            $transaction_data['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');
            // dd($transaction_data['purchase_order_ids']);
            
            
             if($transaction_data['purchase_order_ids']!=0){
                 $grn=Transaction::where('id',$transaction_data['purchase_order_ids'])->where('type','purchase')->first();
                 $grn->grn_id=$transaction_data['purchase_order_ids'];
                 $grn->save();
             }
            
            
            
  
            //Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount($transaction_data['type']);
            //Generate reference number
             $transaction_data['ref_no'] =$request->prefix."".$transaction_data['ref_no'];
             $transaction_data['pay_type'] =$request->input('pay_type');
            
            
            $transaction_data['add_charges_acc_id'] = $request->input('add_charges_acc_dropdown');
            $transaction_data['less_charges_acc_id'] = $request->input('less_charges_acc_dropdown');
            
            // dd($transaction_data);
            $transaction = Transaction::create($transaction_data); 
            $transaction->total_of_total = $request->input('total_of_total');
            $transaction->total_of_tax = $request->input('total_of_tax');
            
            $transaction->total_sale_tax = $request->input('total_sale_tax');
            $transaction->total_further_tax = $request->input('total_further_tax');
            $transaction->total_salesman_commission = $request->input('total_salesman_commission');
            $transaction->total_transporter_rate = $request->input('total_transporter_rate');
            $transaction->total_contractor_rate = $request->input('total_contractor_rate');
            $transaction->total_prd_contractor_rate = $request->input('total_prd_contractor_rate');
            

            //Upload Shipping documents
            Media::uploadMedia($business_id, $transaction, $request, 'shipping_documents', false, 'shipping_document');
            
            $purchase_lines = [];
            $purchases = $request->input('purchases');

            $this->productUtil->createOrUpdatePurchaseLines($transaction, $purchases, $currency_details, $enable_product_editing);

            $this->transactionUtil->activityLog($transaction, 'added');
            
            DB::commit();
            
            $output = ['success' => 1,
                            'msg' => __('lang_v1.added_success')
                        ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            // 'msg' => trans("messages.something_went_wrong")
                            'msg' =>  "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                        ];
        }

        if ($request->input('submit_type') == 'save_n_add_another') {
            return redirect()->action(
                'PurchaseOrderController@create_invoice'
            )->with('status', $output);
        }

        return redirect()->action('PurchaseOrderController@index_invoice')->with('status', $output);
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
        $query = Transaction::where('business_id', $business_id)
                                ->where('id', $id)
                                ->with(
                                    'contact',
                                    'purchase_lines',
                                    'purchase_lines.product',
                                    'purchase_lines.product.unit',
                                    'purchase_lines.variations',
                                    'purchase_lines.variations.product_variation',
                                    'purchase_lines.sub_unit',
                                    'purchase_lines.line_tax',
                                    'location',
                                    'tax'
                                );
        if (!auth()->user()->can('purchase_order.view_all') && auth()->user()->can('purchase_order.view_own')) {
            $query->where('transactions.created_by', request()->session()->get('user.id'));
        }
                                   
        $purchase = $query->firstOrFail();

         
        $digit = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $final_total = $digit->format($purchase->final_total);

        foreach ($purchase->purchase_lines as $key => $value) {
            if (!empty($value->sub_unit_id)) {
                $formated_purchase_line = $this->productUtil->changePurchaseLineUnit($value, $business_id);
                $purchase->purchase_lines[$key] = $formated_purchase_line;
            }
        }
        
        $purchase_taxes = [];
        if (!empty($purchase->tax)) {
            if ($purchase->tax->is_tax_group) {
                $purchase_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($purchase->tax, $purchase->tax_amount));
            } else {
                $purchase_taxes[$purchase->tax->name] = $purchase->tax_amount;
            }
        }
        $t_c = TermsConditions::all();
// dd($t_c);
        $activities = Activity::forSubject($purchase)
           ->with(['causer', 'subject'])
           ->latest()
           ->get();

           $r_quantity=0;
           foreach($purchase->purchase_lines as $value)
           {
               $r_quantity +=$value->quantity - $value->po_quantity_purchased;
           }

        $shipping_statuses = $this->transactionUtil->shipping_statuses();
        $status_color_in_activity = $this->purchaseOrderStatuses;
        $po_statuses = $this->purchaseOrderStatuses;
        $store=Store::All();
        // dd($store);
        return view('purchase_order.show')
                ->with(compact('taxes','t_c','r_quantity','store','purchase', 'purchase_taxes','final_total', 'activities', 'shipping_statuses', 'status_color_in_activity', 'po_statuses'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('purchase_order.update')) {
            abort(403, 'Unauthorized action.');
        }
        $page = 0 ;
        if($_GET){
            $page = $_GET['delete'];
        }

        $business_id = request()->session()->get('user.business_id');

        $business = Business::find($business_id);

        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $taxes = TaxRate::where('business_id', $business_id)
                            ->where('type', 'sales_tax')  
                            ->ExcludeForTaxGroup()
                            ->get();
        
        $further_taxes = TaxRate::where('business_id', $business_id)
                    ->where('type', 'further_tax')
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
        
        foreach ($purchase->purchase_lines as $key => $value) {
            if (!empty($value->sub_unit_id)) {
                $formated_purchase_line = $this->productUtil->changePurchaseLineUnit($value, $business_id);
                $purchase->purchase_lines[$key] = $formated_purchase_line;
            }
        }
       
        $business_locations = BusinessLocation::forDropdown($business_id);
        
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
        $p_type = Type::orderBy("name",'asc')->get();
        $T_C = TermsConditions::all();
        $store=store::All();

        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        $shipping_statuses = $this->transactionUtil->shipping_statuses();
        
            $product = Product::leftJoin(
            'variations',
            'products.id',
            '=',
            'variations.product_id'
        )
           ->where('business_id', $business_id)
            ->whereNull('variations.deleted_at')
            ->where('product_type', $purchase->purchase_type)
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

        $Prq = Transaction::where('type',"Purchase Requisition")->where('id',$purchase->purchase_order_ids)->where('delete_status',1)->get()->pluck('ref_no', 'id');

        $grn = Transaction::where('type',"purchase")->where('delete_status',1)->where('grn_id','0')->get()->pluck('ref_no', 'id');
          
        $sale_man=Contact::where('type','Agent')->get();
          $purchase_category = purchasetype::orderBy("Type",'asc')->get();
        
        $transporter=Contact::where('type','Transporter')->get();
        $contractor = Contact::where('type','contracter')->orderBy('supplier_business_name', 'ASC')->get();
        $accounts = Account::pluck('name','id');
        $brand = Brands::All();
        $vehicles = Vehicle::where('id',$purchase->vehicle_no)->get();
        return view('purchase_order.edit')
            ->with(compact(
                'vehicles',
                'accounts',
                'further_taxes',
                'transporter',
                'contractor',
                'taxes',
                'page',
                'purchase',
                'sale_man',
                'grn',
                'Prq',
                'supplier',
                'product',
                'T_C',
                'p_type',
                'store',
                'business_locations',
                'business',
                'currency_details',
                'customer_groups',
                'types',
                'shortcuts',
                'shipping_statuses',
                'purchase_category',
                'brand'
            ));
    }

    // Edit Purchase Order Invoice

    public function edit_invoice($id)
    {
       
        $page = 0 ;
        if($_GET){
            $page = $_GET['delete'];
        }

        $business_id = request()->session()->get('user.business_id');

        $business = Business::find($business_id);

        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $taxes = TaxRate::where('business_id', $business_id)
                            ->where('type', 'sales_tax')  
                            ->ExcludeForTaxGroup()
                            ->get();
        $brand=Brands::All();
        
        $further_taxes = TaxRate::where('business_id', $business_id)
                    ->where('type', 'further_tax')
                    ->get();

        $query = Transaction::where('business_id', $business_id)
                    ->where('id', $id)
                    ->with(
                        'contact',
                        'purchase_lines',
                        'purchase_lines.product',
                        'purchase_lines.product.unit',
                        'purchase_lines.variations',
                        'purchase_lines.variations.product_variation',
                        'location',
                        'purchase_lines.sub_unit'
                    );

        if (!auth()->user()->can('purchase_order.view_all') && auth()->user()->can('purchase_order.view_own')) {
            $query->where('transactions.created_by', request()->session()->get('user.id'));
        }
        
        $purchase =  $query->first();
        
        foreach ($purchase->purchase_lines as $key => $value) {
            if (!empty($value->sub_unit_id)) {
                $formated_purchase_line = $this->productUtil->changePurchaseLineUnit($value, $business_id);
                $purchase->purchase_lines[$key] = $formated_purchase_line;
            }
        }
       
        $business_locations = BusinessLocation::forDropdown($business_id);
        
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
        $p_type = Type::orderBy("name",'asc')->get();
        $T_C = TermsConditions::all();
        $store=store::All();

        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        $shipping_statuses = $this->transactionUtil->shipping_statuses();
        
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

          $Prq = Transaction::where('type',"Purchase Requisition")->where('delete_status',1)->get()->pluck('ref_no', 'id');

          $grn = Transaction::where('type',"purchase")->where('delete_status',1)->where('id',$purchase->purchase_order_ids)->get()->pluck('ref_no', 'id');
          
        $sale_man=Contact::where('type','Agent')->get();
          $purchase_category = purchasetype::orderBy("Type",'asc')->get();
        
        $transporter=Contact::where('type','Transporter')->get();
        $contractor = Contact::where('type','contracter')->orderBy('supplier_business_name', 'ASC')->get();
        $accounts = Account::pluck('name','id');
        $vehicles = Vehicle::where('id',$purchase->vehicle_no)->get();
        return view('purchase_order_invoice.edit')
            ->with(compact(
                'vehicles',
                'accounts',
                'further_taxes',
                'transporter',
                'contractor',
                'taxes',
                'page',
                'purchase',
                'sale_man',
                'grn',
                'Prq',
                'supplier',
                'product',
                'T_C',
                'p_type',
                'store',
                'business_locations',
                'business',
                'currency_details',
                'customer_groups',
                'types',
                'shortcuts',
                'shipping_statuses',
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

        try {
            $transaction = Transaction::findOrFail($id);

            //Validate document size
            $request->validate([
                'document' => 'file|max:'. (config('constants.document_size_limit') / 1000)
            ]);

            $transaction = Transaction::findOrFail($id);
            $business_id = request()->session()->get('user.business_id');

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            $update_data = $request->only([ 'ref_no', 'contact_id',
                            'transaction_date','posting_date','delivery_date','expected_date','tandc_type','tandc_title','purchase_type','total_before_tax',
                            'discount_type', 'discount_amount', 'tax_id',
                            'tax_amount', 'shipping_details',
                            'shipping_charges', 'final_total',
                             'additional_notes', 'exchange_rate', 'pay_term_number', 'pay_term_type', 'shipping_address', 'shipping_status', 'delivered_to','sales_man',
                             'pay_type','transporter_name','vehicle_no','contractor','add_charges','less_charges','prd_contractor']);

      
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
            
            $update_data['gross_weight']  = $request->input('total_gross__weight');
            $update_data['net_weight']    = $request->input('total_net__weight');

            //upload document
            $document_name = $this->transactionUtil->uploadFile($request, 'document', 'documents');
            if (!empty($document_name)) {
                $update_data['document'] = $document_name;
            }

            $transaction_before = $transaction->replicate();

            DB::beginTransaction();

            $update_data['add_charges_acc_id'] = $request->input('add_charges_acc_dropdown');
            $update_data['less_charges_acc_id'] = $request->input('less_charges_acc_dropdown');
            
            //update transaction
            $transaction->update($update_data);
            $transaction->total_of_total = $request->input('total_of_total');
            $transaction->total_of_tax = $request->input('total_of_tax');
            
            
            $transaction->total_sale_tax = $request->input('total_sale_tax');
            $transaction->total_further_tax = $request->input('total_further_tax');
            $transaction->total_salesman_commission = $request->input('total_salesman_commission');
            $transaction->total_transporter_rate = $request->input('total_transporter_rate');
            $transaction->total_contractor_rate = $request->input('total_contractor_rate');
            $transaction->total_prd_contractor_rate = $request->input('total_prd_contractor_rate');

            Media::uploadMedia($business_id, $transaction, $request, 'shipping_documents', false, 'shipping_document');

            $purchases = $request->input('purchases');

            $delete_purchase_lines = $this->productUtil->createOrUpdatePurchaseLines($transaction, $purchases, $currency_details, false);

            $this->transactionUtil->updatePurchaseOrderStatus([$transaction->id]);

            $this->transactionUtil->activityLog($transaction, 'edited', $transaction_before);

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
            return back()->with('status', $output);
        }
       

       if($transaction->type=="Purchase_invoice")
      {
        return redirect()->action('PurchaseOrderController@index_invoice')->with('status', $output);
      }else{
        return redirect()->action('PurchaseOrderController@index')->with('status', $output);
      }

    }
    
    // Requisition edit
    
     public function edit_req($id)
    {
        if (!auth()->user()->can('purchase.purchase_req.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

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
        
        foreach ($purchase->purchase_lines as $key => $value) {
            if (!empty($value->sub_unit_id)) {
                $formated_purchase_line = $this->productUtil->changePurchaseLineUnit($value, $business_id);
                $purchase->purchase_lines[$key] = $formated_purchase_line;
            }
        }
        // $r_quantity=0;
        // foreach($purchase->purchase_lines as $value)
        // {
        //     $r_quantity +=$value->quantity - $value->po_quantity_purchased;
        // }


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
            ->groupBy('variation_id')->pluck('name', 'name');

       
        $business_locations = BusinessLocation::forDropdown($business_id);
        
        
     
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
        $p_type = Type::orderBy("name",'asc')->get();
        $T_C = TermsConditions::All();
        
      
        $store=store::All();

        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        $shipping_statuses = $this->transactionUtil->shipping_statuses();
        
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
            // dd($product);
        $sale_man=Contact::where('type','Agent')->get();
        $purchase_category = purchasetype::orderBy("Type",'asc')->get();
        $brand=Brands::All();


        return view('purchase_requision.edit')
            ->with(compact(
                'taxes',
                'purchase',
                'sale_man',
                'product',
                'T_C',
                'p_type',
                'store',
                'business_locations',
                'business',
                'currency_details',
                'customer_groups',
                'types',
                'shortcuts',
                'shipping_statuses',
                'purchase_category',
                'brand',
            ));
    }
    
    
    
    // Requisition Update
    
     public function update_req(Request $request, $id)
    {
        if (!auth()->user()->can('purchase.purchase_req.edit')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $transaction = Transaction::findOrFail($id);

            //Validate document size
            $request->validate([
                'document' => 'file|max:'. (config('constants.document_size_limit') / 1000)
            ]);

            $transaction = Transaction::findOrFail($id);
            $business_id = request()->session()->get('user.business_id');

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            $update_data = $request->only([ 'ref_no', 'contact_id',
                            'transaction_date','posting_date','delivery_date','expected_date','tandc_type','tandc_title','purchase_type','total_before_tax',
                            'discount_type', 'discount_amount', 'tax_id',
                            'tax_amount', 'shipping_details',
                            'shipping_charges', 'final_total',
                            'additional_notes', 'exchange_rate', 'pay_term_number', 'pay_term_type', 'shipping_address', 'shipping_status', 'delivered_to','sales_man','purchase_category']);

            $update_data['shipping_custom_field_1'] = $request->has('shipping_custom_field_1') ? $request->input('shipping_custom_field_1') : null;
            $update_data['shipping_custom_field_2'] = $request->has('shipping_custom_field_2') ? $request->input('shipping_custom_field_2') : null;
            $update_data['shipping_custom_field_3'] = $request->has('shipping_custom_field_3') ? $request->input('shipping_custom_field_3') : null;
            $update_data['shipping_custom_field_4'] = $request->has('shipping_custom_field_4') ? $request->input('shipping_custom_field_4') : null;
            $update_data['shipping_custom_field_5'] = $request->has('shipping_custom_field_5') ? $request->input('shipping_custom_field_5') : null;

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

            $update_data['additional_expense_key_1'] = $request->input('additional_expense_key_1');
            $update_data['additional_expense_key_2'] = $request->input('additional_expense_key_2');
            $update_data['additional_expense_key_3'] = $request->input('additional_expense_key_3');
            $update_data['additional_expense_key_4'] = $request->input('additional_expense_key_4');

            $update_data['additional_expense_value_1'] = $request->input('additional_expense_value_1') != '' ? $this->productUtil->num_uf($request->input('additional_expense_value_1'), $currency_details) * $exchange_rate : 0;
            $update_data['additional_expense_value_2'] = $request->input('additional_expense_value_2') != '' ? $this->productUtil->num_uf($request->input('additional_expense_value_2'), $currency_details) * $exchange_rate: 0;
            $update_data['additional_expense_value_3'] = $request->input('additional_expense_value_3') != '' ? $this->productUtil->num_uf($request->input('additional_expense_value_3'), $currency_details) * $exchange_rate : 0;
            $update_data['additional_expense_value_4'] = $request->input('additional_expense_value_4') != '' ? $this->productUtil->num_uf($request->input('additional_expense_value_4'), $currency_details) * $exchange_rate : 0;

            
            $update_data['gross_weight']  = $request->input('total_gross__weight');
            $update_data['net_weight']    = $request->input('total_net__weight');

            //upload document
            $document_name = $this->transactionUtil->uploadFile($request, 'document', 'documents');
            if (!empty($document_name)) {
                $update_data['document'] = $document_name;
            }

            $transaction_before = $transaction->replicate();

            DB::beginTransaction();

            //update transaction
            $transaction->update($update_data);

            Media::uploadMedia($business_id, $transaction, $request, 'shipping_documents', false, 'shipping_document');

            $purchases = $request->input('purchases');
          

            $delete_purchase_lines = $this->productUtil->createOrUpdatePurchaseLines($transaction, $purchases, $currency_details, false);

            $this->transactionUtil->updatePurchaseOrderStatus([$transaction->id]);

            $this->transactionUtil->activityLog($transaction, 'edited', $transaction_before);

            DB::commit();

            $output = ['success' => 1,
                            'msg' => __('Updated Successfull')
                        ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => $e->getMessage()
                        ];
            return back()->with('status', $output);
        }
        return redirect()->action('PurchaseOrderController@index_requision')->with('status', $output);
 
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
            if (request()->ajax()) {
                
                DB::beginTransaction();
                
                $transaction = Transaction::where('id',$id)->get()->toArray();
                
                DB::table('stock_history')->where('ref_no', $transaction[0]['ref_no'])->delete(); 
                DB::table('valuation_history')->where('ref_no', $transaction[0]['ref_no'])->delete(); 
                
                $transaction[0]['transaction_id'] = $id;
                $del_transaction = DelTransaction::create($transaction[0]);
                if($transaction[0]['type'] == "Purchase_invoice"){
                    DB::table('account_transactions')->where('transaction_id',$id)->delete();
                }

                if($transaction[0]['type'] == "purchase_order"){
                    Transaction::where('id', $transaction[0]['purchase_order_ids'])->update(['purchase_order_ids' => 0]);
                }else if($transaction[0]['type'] == "Purchase_invoice"){
                    Transaction::where('id', $transaction[0]['purchase_order_ids'])->update(['grn_id' => 0]);
                }
                
                // check if Purchase Requisition has PO so throw exception
                // if($transaction[0]['type'] == "Purchase Requisition" && $transaction[0]['purchase_order_ids'] > 0){
                //     throw new \Exception("Can't delete this PR");
                // }else{
                    $transaction = Transaction::where('id',$id)->delete();
                // }
                
                DB::commit();

                $output = ['success' => true,
                            'msg' => __('Deleted Successfully')
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

    public function getPurchaseOrders($contact_id)
    {
        $business_id = request()->session()->get('user.business_id');
        
        $purchase_orders = Transaction::where('business_id', $business_id)
                        ->where('type', 'purchase_order')
                        ->whereIn('status', ['partial', 'ordered'])
                        ->where('contact_id', $contact_id)
                        ->select('ref_no as text', 'id')
                        ->get();

        return $purchase_orders;
    }

    public function delete_type($id)
    {
        try {
            DB::beginTransaction();
                $delete=purchasetype::find($id);
                $transaction=Transaction::where('delete_status','!=','0')->get();
                $should_delete = true;
                
                foreach($transaction as $r) {
                    if ($r->purchase_type == $id) {
                        $should_delete = false;
                        $output = ['success' => false, 'msg' => ("Transaction is available for this Type")];
                        return redirect()->back()->with('status', $output);
                        break;
                    }
                }
                if ($should_delete == true) {
                    $product_type = Type::where('purchase_type', $id)->count();
                    if($product_type == 0){
                        $delete->delete();
                        $output = ['success' => true, 'msg' => ("success")];
                    }else{
                        $output = ['success' => false, 'msg' => ("Product Type exist Of this Purchase Type")];
                    }
                }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }
       return redirect()->action('PurchaseOrderController@Purchase_type')->with('status', $output);

    }



    public function Purchase_type_partial(){
        $purchase_type=purchasetype::all();
        return view('Purchase_type.p_type_partial')->with(compact('purchase_type'));
    }
    
    public function purchase_store_partial(Request $request){
        $purchasetype = new purchasetype;
        $purchasetype->prefix   = $request->prefix;
        $purchasetype->type     = $request->type;
        $purchasetype->save();
        return redirect()->back();
    }
    
    public function Purchase_type()
    {
    if (!auth()->user()->can('purchase_catagory.view') && !auth()->user()->can('Purchase_type.view')) {
        abort(403, 'Unauthorized action.');
    }
        
        $purchase_type = purchasetype::all();
        $control_account = Account::All();
        return view('Purchase_type.index')->with(compact('purchase_type','control_account'));
    }
    
    public function purchase_store(Request $request){
        $purchasetype = new purchasetype;
        $purchasetype->prefix = $request->prefix;
        $purchasetype->type = $request->type;
        $purchasetype->control_account_id = $request->control_account_id;
        $purchasetype->save();
        return redirect()->action('PurchaseOrderController@Purchase_type');
    }
       
    public function Purchase_type_update($id){
    	$purchase_type = purchasetype::find($id);
	    return response()->json($purchase_type);
     }
      
 
    public function Purchase_type_edit(Request $request, $id){
      $purchse_type = purchasetype::findOrFail($id);
      $purchse_type->prefix = $request->prefix;
      $purchse_type->Type = $request->Type;
      $purchse_type->control_account_id = $request->control_account_id;
      $purchse_type->save();
      return response()->json([ 'success' => true ]);
   }

    /**
     * download pdf for given purchase order
     *
     */
    public function downloadPdf($id)
    {   
        if (!(config('constants.enable_download_pdf') && (auth()->user()->can("purchase_order.view_all") || auth()->user()->can("purchase_order.view_own")))) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $taxes = TaxRate::where('business_id', $business_id)
                                ->get();

        $purchase = Transaction::where('business_id', $business_id)
                    ->where('id', $id)
                    ->with(
                        'contact',
                        'purchase_lines',
                        'purchase_lines.product',
                        'purchase_lines.product.category',
                        'purchase_lines.variations',
                        'purchase_lines.variations.product_variation',
                        'location',
                        'payment_lines'
                    )
                    ->first();

        $location_details = BusinessLocation::find($purchase->location_id);
        $invoice_layout = $this->businessUtil->invoiceLayout($business_id, $purchase->location_id, $location_details->invoice_layout_id);

        //Logo
        $logo = $invoice_layout->show_logo != 0 && !empty($invoice_layout->logo) && file_exists(public_path('uploads/invoice_logos/' . $invoice_layout->logo)) ? asset('uploads/invoice_logos/' . $invoice_layout->logo) : false;

        $word_format = $invoice_layout->common_settings['num_to_word_format'] ? $invoice_layout->common_settings['num_to_word_format'] : 'international';
        $total_in_words = $this->transactionUtil->numToWord($purchase->final_total, null, $word_format);

        $custom_labels = json_decode(session('business.custom_labels'), true);
        
        //Generate pdf
        $body = view('purchase_order.receipts.download_pdf')
                    ->with(compact('purchase', 'invoice_layout', 'location_details', 'logo', 'total_in_words', 'custom_labels', 'taxes'))
                    ->render();

        $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('uploads/temp'), 
                    'mode' => 'utf-8', 
                    'autoScriptToLang' => true,
                    'autoLangToFont' => true,
                    'autoVietnamese' => true,
                    'autoArabic' => true,
                    'margin_top' => 8,
                    'margin_bottom' => 8,
                    'format' => 'A4'
                ]);

        $mpdf->useSubstitutions=true;
        $mpdf->SetWatermarkText($purchase->business->name, 0.1);
        $mpdf->showWatermarkText = true;
        $mpdf->SetTitle('PO-'.$purchase->ref_no.'.pdf');
        $mpdf->WriteHTML($body);
        $mpdf->Output('PO-'.$purchase->ref_no.'.pdf', 'I');
    }

    /**
     * get required resources 
     *
     * to edit purchase order status
     *
     * @return \Illuminate\Http\Response
     */
    public function getEditPurchaseOrderStatus(Request $request, $id)
    {   
        $is_admin = $this->businessUtil->is_admin(auth()->user());
        if ( !$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $transaction = Transaction::where('business_id', $business_id)
                                ->findOrFail($id);

            $status = $transaction->status;
            $statuses = $this->purchaseOrderStatuses;

            return view('purchase_order.edit_status_modal')
                ->with(compact('id', 'status', 'statuses'));
        }
    }

    /**
     * updare purchase order status
     *
     * @return \Illuminate\Http\Response
     */
    public function postEditPurchaseOrderStatus(Request $request, $id)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());
        if ( !$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            try {
                
                $business_id = request()->session()->get('user.business_id');
                $transaction = Transaction::where('business_id', $business_id)
                                ->findOrFail($id);

                $transaction_before = $transaction->replicate();
                
                $transaction->status = $request->input('status');
                $transaction->save();

                $activity_property = ['from' => $transaction_before->status, 'to' => $request->input('status')];
                $this->transactionUtil->activityLog($transaction, 'status_updated', $transaction_before, $activity_property);

                $output = [
                    'success' => 1,
                    'msg' => trans("lang_v1.success")
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = [
                    'success' => 0,
                    'msg' => trans("messages.something_went_wrong")
                ];
            }
            return $output;
        }
    }
    public function Term_conditions()
    {
        $sale_type= TermsConditions::all();
        return view('Term_conditions.index')->with(compact('sale_type'));
    }
    
    public function Term_conditions_store(Request $request)
    {
        $saletype = new TermsConditions;
        $saletype->name = $request->name;
        $saletype->title = $request->title;
        $saletype->save();

        return redirect()->action('PurchaseOrderController@Term_conditions');
    }
    
    public function Term_conditions_update($id)
    {
    	$saletype = TermsConditions::find($id);

	    return response()->json($saletype);
     }
     
    public function Term_conditions_edit(Request $request, $id)
    {
       $term=TermsConditions::find($id);
       $term->id=$id;
       $term->name=$request->name;
       $term->title=$request->title;
       $term->save();
       
       return response()->json([ 'success' => true ]);

   }
   
    public function Term_conditions_delete($id)
    {
        $delete=TermsConditions::find($id);
        $delete->delete();
        return redirect()->action('PurchaseOrderController@Term_conditions');

    }
    public function View_DR($type)
    {
        $delete_records= Transaction::where('delete_status', '0')->where('type', $type)->get();
        return view('Delete_records.index')->with(compact('delete_records','type'));
    }

}
