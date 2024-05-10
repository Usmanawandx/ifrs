<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BusinessLocation;
use App\Contact;
use App\Account;
use App\Type;
use App\saletype;
use App\Transaction;
use App\Utils\TransactionUtil;
use App\Utils\BusinessUtil;
use App\Utils\Util;

class SalesOrderController extends Controller
{
    protected $transactionUtil;
    protected $businessUtil;
    protected $commonUtil;
    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, BusinessUtil $businessUtil, Util $commonUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
        $this->commonUtil = $commonUtil;
        $this->sales_order_statuses = [
            'ordered' => [
                'label' => __('lang_v1.ordered'),
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
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('so.view_own') && !auth()->user()->can('so.view_all') && !auth()->user()->can('so.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);    

        $shipping_statuses = $this->transactionUtil->shipping_statuses();

        $sales_order_statuses = [];
        foreach ($this->sales_order_statuses as $key => $value) {
            $sales_order_statuses[$key] = $value['label'];
        }
        $t_no = Transaction::where('type', 'sales_order')->where('ref_no', 'not like', "%-ovr%")
        ->select("ref_no")
        ->orderBy("id",'desc')->take(1)->count();
        $saleType = saletype::All()->pluck('name', 'id');
        // $t_no+1;


        return view('sales_order.index')
            ->with(compact('saleType','business_locations','t_no','customers', 'shipping_statuses', 'sales_order_statuses'));
    }

    public function getSalesOrders($customer_id)
    {
        $business_id = request()->session()->get('user.business_id');
        $location_id = request()->input('location_id');
        
        $sales_orders = Transaction::where('business_id', $business_id)
                            ->where('location_id', $location_id)
                            ->where('type', 'sales_order')
                            ->whereIn('status', ['partial', 'ordered'])
                            ->where('contact_id', $customer_id)
                            ->select('invoice_no as text', 'id')
                            ->get();

        return $sales_orders;
    }

    /**
     * get required resources 
     *
     * to edit sales order status
     *
     * @return \Illuminate\Http\Response
     */
    public function getEditSalesOrderStatus(Request $request, $id)
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
            $statuses = $this->sales_order_statuses;

            return view('sales_order.edit_status_modal')
                ->with(compact('id', 'status', 'statuses'));
        }
    }

    /**
     * updare sales order status
     *
     * @return \Illuminate\Http\Response
     */
    public function postEditSalesOrderStatus(Request $request, $id)
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
                $this->commonUtil->activityLog($transaction, 'status_updated', $transaction_before, $activity_property);

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
    
    public function sale_type_partial()
    {
        $sale_type=saletype::all();
        $purchase_type = Type::All();
        return view('Sale_type.s_type_partial')->with(compact('sale_type','purchase_type'));
    }
    public function sale_store_partial(Request $request)
    {
        $saletype = new saletype;
        $saletype->prefix = $request->prefix;
        $saletype->name = $request->name;
        $saletype->purchase_type = $request->purchase_type;
        $saletype->save();
        return redirect()->back();
    }

    
    public function sale_type()
    {
        $sale_type=saletype::all();
        $purchase_type = Type::All();
        $control_account = Account::All();
        return view('Sale_type.index')->with(compact('sale_type','purchase_type','control_account'));
        
    }
    public function sale_store(Request $request)
    {
        // dd($request->type);
        $saletype = new saletype;
        $saletype->prefix = $request->prefix;
        $saletype->name = $request->name;
        $saletype->purchase_type = $request->purchase_type;
        $saletype->control_account_id = $request->control_account_id;
        $saletype->save();

        return redirect()->action('SalesOrderController@sale_type');
    }
    public function update($id)
    {
    	$saletype = saletype::find($id);

	    return response()->json($saletype);
    }

    public function edit(Request $request, $id)
    {

        // dd($request->name);
    //     saletype::updateOrCreate(
    //   [
    //     'id' => $id
    //   ],
    //   [
    //     'name' => $request->name,
    //   ]
    //   );
    
     $sale_type=saletype::find($id);
      $sale_type->prefix=$request->prefix;
      $sale_type->name=$request->name;
      $sale_type->purchase_type=$request->purchase_type;
      $sale_type->control_account_id=$request->control_account_id; 
      $sale_type->save();

      return response()->json([ 'success' => true ]);

    }
    public function delete_type($id)
    {
       $delete=saletype::find($id);
    //   $transaction=Transaction::all();
       $transaction=Transaction::where('delete_status','!=','0')->get();
       $should_delete = true;
       
       foreach($transaction as $r) {
        // dd($r);
        if ($r->saleType == $id) {
            $should_delete = false;
          

            // dd("sa");
            return redirect()->back()->with('message', 'Transaction Available for This Type...');

            break;
            // dd("sa");
        }
        }
        
        if ($should_delete == true) {
            $delete->delete();
        }

       return redirect()->action('SalesOrderController@sale_type');

    }
}
