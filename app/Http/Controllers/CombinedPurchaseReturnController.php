<?php

namespace App\Http\Controllers;

use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\Brands;
use App\PurchaseLine;
use App\TaxRate;
use App\purchasetype;
use App\TermsConditions;
use App\Transaction;
use App\Product;
use App\Transporter;
use App\Vehicle;
use App\Type;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\store;
use App\AccountTransaction;
use App\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CombinedPurchaseReturnController extends Controller
{

    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $moduleUtil;
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, ModuleUtil $moduleUtil, TransactionUtil $transactionUtil)
    {
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('purchase.debit_note.add')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $t_no = Transaction::where('type', 'purchase_return')->where('ref_no', 'not like', "%-ovr%")
        ->select("id")
        ->orderBy("id",'desc')->take(1)->count();

        $prefix=Business::first();
        $dn_prefix=$prefix->ref_no_prefixes['purchase_return'];

        if($t_no== null){
          $unni="1"; 
        }else{
            //   $unni = $t_no + 1 ;
            $tr_no=$t_no+1;
            $unni = $tr_no;
        }
        

        
        $transporter=Contact::where('type','Transporter')->get();
        $taxes = TaxRate::where('business_id', $business_id)
                        ->where('type', 'sales_tax')   
                        ->ExcludeForTaxGroup()
                        ->get();
                        
        $further_taxes = TaxRate::where('business_id', $business_id)
                    ->where('type', 'further_tax')
                    ->get();
                    
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
            )->groupBy('variation_id')->pluck('name', 'product_id');
            $supplier=Contact::All();

            $store=store::All();
            
            
            $row_index = "0";
            $sale_man=Contact::where('type','Agent')->get();
     
          
            $purchase_category = purchasetype::orderBy("Type",'asc')->get();
            $contractor = Contact::where('type','contracter')->orderBy('supplier_business_name', 'ASC')->get();
            $default_account= DB::table('default_account')->where('form_type','purchase_return')->where('field_type','sales_account')->first()->account_id ?? 0;
            $accounts = Account::pluck('name','id');
            $brand=Brands::All();
            $default_id  = DB::table('default_account')->where('form_type','purchase_return')->where('field_type','salesman')->first()->account_id ?? 0;
            $default_sales_man=Account::where('id',$default_id)->first()->contact_id ?? 0;
            $default_contractor    = DB::table('default_account')->where('form_type','purchase_return')->where('field_type','default_contractor')->first()->account_id ?? 0;
        return view('purchase_return.create')
            ->with(compact('accounts','business_locations','sale_man','dn_prefix','transporter','unni','T_C','store','supplier','row_index','product','p_type','taxes','purchase_category','contractor','further_taxes','brand','default_sales_man','default_account'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        
          $request->validate([
                'ref_no'=> 'required|unique:transactions',
            ]);

        try {
            DB::beginTransaction();

            $input_data = $request->only([ 'location_id', 'transaction_date', 'final_total', 'ref_no',
                                'tax_id', 'tax_amount', 'contact_id','tandc_type','tandc_title','purchase_type', 
                                'pay_term_number', 'pay_term_type','return_date','additional_notes','transporter_name',
                                'vehicle_no','Pay_type','purchase_category','sales_man','contractor','add_charges','less_charges','transaction_account']);
            $business_id = $request->session()->get('user.business_id');

            //Check if subscribed or not
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse();
            }
             
        
            $user_id = $request->session()->get('user.id');

            $input_data['type'] = 'purchase_return';
            $input_data['business_id'] = $business_id;
            $input_data['created_by'] = $user_id;
            $input_data['transaction_date'] = $this->productUtil->uf_date($input_data['transaction_date'], false);
            $input_data['total_before_tax'] = $input_data['final_total'] - $input_data['tax_amount'];

            
            $input_data['gross_weight']  = $request->input('total_gross__weight');
            $input_data['net_weight']    = $request->input('total_net__weight');
            //Update reference count
           
            $ref_count = $this->productUtil->setAndGetReferenceCount('purchase_return');
            
           
           
            //Generate reference number

                $input_data['ref_no'] = $request->prefix."".$input_data['ref_no'];

            //upload document
            $input_data['document'] = $this->productUtil->uploadFile($request, 'document', 'documents');

            $input_data['add_charges_acc_id'] = $request->input('add_charges_acc_dropdown');
            $input_data['less_charges_acc_id'] = $request->input('less_charges_acc_dropdown');

            $products = $request->input('products');

            // first delete old record
            DB::table('stock_history')->where('ref_no', $input_data['ref_no'])->delete();
            DB::table('valuation_history')->where('ref_no', $input_data['ref_no'])->delete();
            
            if (!empty($products)) {
                $product_data = [];

                foreach ($products as $product) {
                      if(empty($product['product_id'])){
                continue;
                   }
                    $unit_price = $this->productUtil->num_uf($product['pricee']);
                    // dd($product['variation_id']);
                    $return_line = [
                        'product_id' => $product['product_id'],
                        'variation_id' => $product['variation_id'], 
                        'quantity' => 0,
                        'purchase_price' => $unit_price,
                        'brand_id' => $product['brand_id'],
                        'pp_without_discount' => $unit_price,
                        'purchase_price_inc_tax' => !empty($product['purchase_price_inc_tax']) ? $product['purchase_price_inc_tax'] : 0,
                        'item_code' => !empty($product['item_code']) ? $product['item_code'] : null,
                        'discount_percent'=>!empty($product['discount_percent']) ? $product['discount_percent'] : 0,
                        'store' => !empty($product['store']) ? $product['store'] : null,
                        'tax_id' => !empty($product['tax_id']) ? $product['tax_id'] : null,
                        'item_tax' => !empty($product['item_tax']) ? $product['item_tax'] : 0,
                        'item_description' => !empty($product['item_description']) ? $product['item_description'] : null,
                        'uom' => !empty($product['uom']) ? $product['uom'] : null,
                        'quantity_returned' => $this->productUtil->num_uf($product['quantity']),
                        'lot_number' => !empty($product['lot_number']) ? $product['lot_number'] : null,
                        'exp_date' => !empty($product['exp_date']) ? $this->productUtil->uf_date($product['exp_date']) : null,
                        'further_tax' => !empty($product['further_taax_id']) ? $product['further_taax_id'] : null,
                        'salesman_commission' => !empty($product['salesman_commission_rate']) ? $product['salesman_commission_rate'] : null
                    ];
                    
                    $product_data[] = $return_line;

                    //Decrease available quantity
                    $this->productUtil->decreaseProductQuantity(
                        $product['product_id'],
                        $product['variation_id'],
                        $input_data['location_id'],
                        $this->productUtil->num_uf($product['quantity'])
                    );

                    // if($input_data['type'] == 'purchase_return'){ 
                    //     $this->stock_history($product['product_id'], $input_data['ref_no'], $input_data['transaction_date'], $this->productUtil->num_uf($product['quantity']), 1);
                    // }

                }

                $purchase_return = Transaction::create($input_data);
                
                $this->productUtil->purchase_return_ledger_entries($request, $purchase_return, $input_data);
                
                foreach($product_data as $key => $value){
                    $value['transaction_id'] = $purchase_return->id;
                    $purchase_line = PurchaseLine::create($value);
                    
                    // hit stock history
                    $this->moduleUtil->stock_history($purchase_line, $purchase_return, 2);
                    $this->moduleUtil->valuation_history($purchase_line, $purchase_return);
                    
                }

                // $purchase_return->purchase_lines()->createMany($product_data);
                
                //update payment status
                $this->transactionUtil->updatePaymentStatus($purchase_return->id, $purchase_return->final_total);
            }

            

            $output = ['success' => 1,
                            'msg' => __('lang_v1.purchase_return_added_success')
                        ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = ['success' => 0,
                            'msg' => __("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage())
                        ];
        }

        if ($request->input('submit_type') == 'save_n_add_another') {
            return redirect()->action(
                'CombinedPurchaseReturnController@create'
            )->with('status', $output);
        }

        return redirect('purchase-return')->with('status', $output);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('purchase.debit_note.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $purchase_return = Transaction::where('business_id', $business_id)
                                    ->with(['contact'])
                                    ->find($id);
        $transporter = Contact::where('type','Transporter')->get();
        $vehicles = Vehicle::where('id',$purchase_return->vehicle_no)->get();
    
        $location_id = $purchase_return->location_id;
        $purchase_lines = PurchaseLine::
        leftjoin(
                            'products AS p',
                            'purchase_lines.product_id',
                            '=',
                            'p.id'
                        )
                        ->leftjoin(
                            'variations AS variations',
                            'purchase_lines.variation_id',
                            '=',
                            'variations.id'
                        )
                        ->leftjoin(
                            'product_variations AS pv',
                            'variations.product_variation_id',
                            '=',
                            'pv.id'
                        )
                        ->leftjoin('variation_location_details AS vld', function ($join) use ($location_id) {
                            $join->on('variations.id', '=', 'vld.variation_id')
                                ->where('vld.location_id', '=', $location_id);
                        })
                        ->leftjoin('units', 'units.id', '=', 'p.unit_id')
                        ->where('purchase_lines.transaction_id', $id)
                        ->select(
                            DB::raw("IF(pv.is_dummy = 0, CONCAT(p.name, 
                                    ' (', pv.name, ':',variations.name, ')'), p.name) AS product_name"),
                            'p.id as product_id',
                            'p.enable_stock',
                            'pv.is_dummy as is_dummy',
                            'variations.sub_sku',
                            'vld.qty_available',
                            'variations.id as variation_id',
                            'units.short_name as unit',
                            'units.allow_decimal as unit_allow_decimal',
                            'purchase_lines.further_tax',
                            'purchase_lines.salesman_commission',
                            'purchase_lines.tax_id as tax_id',
                            'purchase_lines.purchase_price',
                            'purchase_lines.id as purchase_line_id',
                            'purchase_lines.store as store',
                            'purchase_lines.item_code as code',
                            'purchase_lines.item_description as description',
                            'purchase_lines.quantity_returned as quantity_returned',
                            'purchase_lines.purchase_price_inc_tax as sales_price_with_tax',
                            'purchase_lines.item_tax as sales_tax',
                            'purchase_lines.discount_percent as discount',
                            'purchase_lines.lot_number',
                            'purchase_lines.exp_date',
                            'purchase_lines.brand_id',
                        )->get();
                        
        foreach ($purchase_lines as $key => $value) {
            $purchase_lines[$key]->qty_available += $value->quantity_returned;
            $purchase_lines[$key]->formatted_qty_available = $this->productUtil->num_f($purchase_lines[$key]->qty_available);
            
        };
        $business_locations = BusinessLocation::forDropdown($business_id);

        $taxes = TaxRate::where('business_id', $business_id)
                        ->where('type', 'sales_tax')   
                        ->ExcludeForTaxGroup()
                        ->get();
                        
        $further_taxes = TaxRate::where('business_id', $business_id)
                    ->where('type', 'further_tax')
                    ->get();

        $products_t = Product::leftJoin(
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
            )->get();


        
        $supplier=Contact::All();
        
        $store=store::All();
         $T_C = TermsConditions::All();

         $sale_man=Contact::where('type','Agent')->get();
        $purchase_category = purchasetype::orderBy("Type",'asc')->get();
        $p_type = Type::orderBy("name",'asc')->get();
        $accounts = Account::pluck('name','id');
        $brands=Brands::All();
        $contractor = Contact::where('type','contracter')->orderBy('supplier_business_name', 'ASC')->get();
        return view('purchase_return.edit')
            ->with(compact('contractor','accounts','business_locations','sale_man','transporter','vehicles','store','T_C','products_t','supplier','taxes', 'purchase_return', 'purchase_lines','purchase_category','p_type','further_taxes','brands'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        try {
            DB::beginTransaction();


            $input_data = $request->only(['transaction_date', 'final_total',
                    'tax_id', 'tax_amount', 'tandc_type', 'tandc_title','contact_id','return_date','vehicle_no','transporter_name','sale_man','pay_type','purchase_category','transaction_account','add_charges','less_charges']);
            $business_id = $request->session()->get('user.business_id');

            // $input_data['type'] = 'purchase_return';
            if (!empty($request->input('ref_no'))) {
                $input_data['ref_no'] = $request->input('ref_no');
            }

            //Check if subscribed or not
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse();
            }
        
            $input_data['transaction_date'] = $this->productUtil->uf_date($input_data['transaction_date'], false);
            $input_data['total_before_tax'] = $input_data['final_total'] - $input_data['tax_amount'];
            $input_data['add_charges_acc_id'] = $request->input('add_charges_acc_dropdown');
            $input_data['less_charges_acc_id'] = $request->input('less_charges_acc_dropdown');
            
            $input_data['gross_weight']  = $request->input('total_gross__weight');
            $input_data['net_weight']    = $request->input('total_net__weight');
            //upload document
            $doc_name = $this->productUtil->uploadFile($request, 'document', 'documents');

            if (!empty($doc_name)) {
                $input_data['document'] = $doc_name;
            }

            $products = $request->input('products');
            //   dd($products);
            $purchase_return_id = $request->input('purchase_return_id');
            $purchase_return = Transaction::where('business_id', $business_id)
                                ->where('type', 'purchase_return')
                                ->find($purchase_return_id);
            
            
            // first delete old record
            DB::table('stock_history')->where('ref_no', $input_data['ref_no'])->delete();
            DB::table('valuation_history')->where('ref_no', $input_data['ref_no'])->delete();
            
            if (!empty($products)) {
                $product_data = [];
                $updated_purchase_lines = [];

                foreach ($products as $product) {
                    $unit_price = $this->productUtil->num_uf($product['unit_price']);
                    if (!empty($product['purchase_line_id'])) {
                        $return_line = PurchaseLine::find($product['purchase_line_id']);
                        $updated_purchase_lines[] = $return_line->id;

                        $this->productUtil->decreaseProductQuantity(
                            $product['product_id'],
                            $product['variation_id'],
                            $purchase_return->location_id,
                            $this->productUtil->num_uf($product['quantity']),
                            $return_line->quantity_returned
                        );
                    } else {
                        $return_line = new PurchaseLine([
                            'product_id' => $product['product_id'],
                            'variation_id' => $product['variation_id'],
                            'quantity' => 0
                        ]);

                        //Decrease available quantity
                        $this->productUtil->decreaseProductQuantity(
                            $product['product_id'],
                            $product['variation_id'],
                            $purchase_return->location_id,
                            $this->productUtil->num_uf($product['quantity'])
                        );
                    }
                    $return_line->purchase_price = $unit_price;
                    $return_line->brand_id               = !empty($product['brand_id']) ? $product['brand_id'] : null;
                    $return_line->pp_without_discount    = $unit_price;
                    $return_line->discount_percent       = !empty($product['discount_percent']) ? $product['discount_percent'] : null;
                    $return_line->purchase_price_inc_tax = !empty($product['purchase_price_inc_tax']) ? $product['purchase_price_inc_tax'] : 0;
                    $return_line->quantity_returned      = $this->productUtil->num_uf($product['quantity']);
                    $return_line->tax_id                 = !empty($product['tax_id']) ? $product['tax_id'] : null;
                    $return_line->item_tax               = !empty($product['item_tax']) ? $product['item_tax'] : 0;
                    $return_line->item_code              = !empty($product['item_description']) ? $product['item_description'] : 0;
                    $return_line->store                  = !empty($product['store']) ? $product['store'] : 0;
                    $return_line->lot_number             = !empty($product['lot_number']) ? $product['lot_number'] : null;
                    $return_line->exp_date               = !empty($product['exp_date']) ? $this->productUtil->uf_date($product['exp_date']) : null;
                    $return_line->further_tax            = !empty($product['further_taax_id']) ? $product['further_taax_id'] : null;
                    $return_line->salesman_commission    = !empty($product['salesman_commission_rate']) ? $product['salesman_commission_rate'] : null;
                    $return_line->transaction_id         = $purchase_return_id;
                    $product_data[]                      = $return_line;

                    // hit stock history
                    $this->moduleUtil->stock_history($return_line, $purchase_return, 2); 
                    $this->moduleUtil->valuation_history($return_line, $purchase_return);  
                }
                

                $purchase_return->update($input_data);
                $this->productUtil->purchase_return_ledger_entries($request, $purchase_return, $input_data);
            
                //If purchase line deleted add return quantity to stock
                $deleted_purchase_lines = PurchaseLine::where('transaction_id', $purchase_return_id)
                            ->whereNotIn('id', $updated_purchase_lines)
                            ->get();

                foreach ($deleted_purchase_lines as $dpl) {
                    $this->productUtil->updateProductQuantity($purchase_return->location_id, $dpl->product_id, $dpl->variation_id, $dpl->quantity_returned, 0, null, false);
                }

             PurchaseLine::where('transaction_id', $purchase_return_id)->delete();         
                            
            foreach($product_data as $key => $value){

               
                PurchaseLine::create($value->toArray());


                 }

                // $purchase_return->purchase_lines()->saveMany($product_data);

                //update payment status
                $this->transactionUtil->updatePaymentStatus($purchase_return->id, $purchase_return->final_total);
            }

            $output = ['success' => 1,
                            'msg' => __('lang_v1.purchase_return_updated_success')
                        ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = ['success' => 0,
                            'msg' => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                        ];
        }

        return redirect('purchase-return')->with('status', $output);
    }

    /**
     * Return product rows
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getProductRow(Request $request)
    {
        if (request()->ajax()) {
            $row_index = $request->input('row_index');
            $variation_id = $request->input('variation_id');
            $location_id = $request->input('location_id');

            $business_id = $request->session()->get('user.business_id');
            $product = $this->productUtil->getDetailsFromVariation($variation_id, $business_id, $location_id);
            $product->formatted_qty_available = $this->productUtil->num_f($product->qty_available);
            
            
            return view('purchase_return.partials.product_table_row')
            ->with(compact('product', 'row_index'));
        }
    }
}
