@extends('layouts.app')
@section('title', __('lang_v1.add_purchase_order'))

@section('content')

<style>
    .select2-container--default{
        width:100% !Important;
    }
    #tbody textarea.form-control {
    height: 35px !important;
    width: 100% !important;
}
</style>

<!-- Content Header (Page header) -->


<section class="content-header">
    <div> 
		<h1 class="top-heading">
		@lang('Create Purchase Requisation')
		 <i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true" data-container="body" data-toggle="popover" data-placement="bottom" data-content="@include('purchase.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover" data-original-title="" title=""></i>
		 <span class="pull-right top_trans_no"></span>
		</h1>

	</div>
	
</section>

<!-- Main content -->
<section class="content">

	<!-- Page level currency setting -->
	<input type="hidden" id="p_code" value="{{$currency_details->code}}">
	<input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
	<input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
	<input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">

	@include('layouts.partials.error')

	{!! Form::open(['url' => action('PurchaseOrderController@store_requision'), 'method' => 'post', 'id' => 'add_purchase_form', 'files' => true ]) !!}
	
	
	{{-- <div class="row " style="  background-color: white;
    
    padding: 23px;
	margin:0px;
	">
		<div class="col-sm-12">
			<button type="button" id="submit_purchase_form_req" class="btn btn-primary submit_purchase_form btn-flat" accesskey="s">@lang('messages.save')</button>
		</div>
	</div> --}}
	<br>
	<br/>
	@component('components.widget', ['class' => 'box-solid'])
		<input type="hidden" id="is_purchase_order">
		<div class="row">

			<div class="col-sm-4">
				<div class="form-group">
					<label>Purchase Type</label>
					<div class="input-group">
						<select class="form-control purchase_category get__prefix" name="purchase_category" required>
							<option selected disabled> Select</option>
							@foreach ($purchase_category as $tp)
							<option value="{{$tp->id}}" data-pf="{{$tp->prefix}}">{{$tp->Type}}</option>
							@endforeach
						</select>
						<span class="input-group-btn">
							<button type="button" class="btn btn-default bg-white btn-flat btn-modal"
							data-href="{{action('PurchaseOrderController@Purchase_type_partial')}}" data-container=".view_modal"><i
							class="fa fa-plus-circle text-primary fa-lg"></i></button>
						</span>
					</div>
				</div>
			</div>
			
			
			
			<div class="col-sm-4">
				<div class="form-group">
					<label>Product Type</label>
					<select class="form-control purchase_type" name="purchase_type" id="is_purchase_order_dd" required>
                       <option selected disabled> Select</option>
					</select>
				</div>
			</div>




@if(count($business_locations) == 1)
				@php 
					$default_location = current(array_keys($business_locations->toArray()));
					$search_disable = true; 
				@endphp
			@else
				@php $default_location = null;
				$search_disable = false;
				@endphp
			@endif
				<div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-4 @endif">
				<div class="form-group">
					{!! Form::label('Supplier Reference', __('Transaction No').':') !!}
					<input type="hidden" name="prefix" class="trn_prefix" value="{{$pr."-"}}">
					<div class="input-group">
						<span class="input-group-addon trn_prefix_addon">
							{{$pr."-"}}
						</span>
					{!! Form::text('ref_no',$unni, ['class' => 'form-control ref_no']); !!}
				</div>
				</div>
			</div>

			<div class="clearfix"></div>

			

			<div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-4 @endif hide" >
				<div class="form-group">
					{!! Form::label('supplier_id', __('purchase.supplier') . ':*') !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-user"></i>
						</span>
						{!! Form::select('contact_id', [], null, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'supplier_id']); !!}
						<span class="input-group-btn">
							<button type="button" class="btn btn-default bg-white btn-flat add_new_supplier" data-name=""><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
						</span>
					</div>
				</div>
				<strong>
					@lang('business.address'):
				</strong>
				<div id="supplier_address_div"></div>
			</div>
			
		
			<div class="col-sm-4" >
				<div class="form-group">
					{!! Form::label('transaction_date', __('Transaction Date') . ':*') !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</span>
						{!! Form::text('transaction_date', @format_date('now'), ['class' => 'form-control', 'required','id'=>'expense_transaction_date']); !!}
					</div>
				</div>
			</div>
	
			
				
				
			
		
			<div class=" col-sm-4 hide">
				<div class="form-group">
					{!! Form::label('Posting Date', __('Posting Date') . ':*') !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</span>
						{!! Form::text('posting_date',date('Y-m-d'), ['class' => 'form-control','readonly','required']); !!}
					</div>
				</div>
			</div>
			
				<div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-4 @endif hide" >
				<div class="form-group">
					{!! Form::label('transaction_date', __('Transaction Date') . ':*') !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</span>
								{!! Form::text('expected_date', @format_datetime('now'), ['class' => 'form-control', 'required','id'=>'expense_transaction_date']); !!}
					</div>
				</div>
			</div>
			<div class="@if(!empty($default_purchase_status)) col-sm-4 @else col-sm-4 @endif hide"  >
				<div class="form-group">
					{!! Form::label('Delivery Date', __('Delivery Date') . ':*') !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</span>
						{!! Form::date('delivery_date', @format_date('now'), ['class' => 'form-control', 'required']); !!}
					</div>
				</div>
			</div>

			<div class="col-sm-4">
				<div class="form-group">
					{!! Form::label('Remarks',__('Remarks')) !!}
					{!! Form::textarea('additional_notes', null, ['class' => 'form-control','rows' => 1]); !!}
			</div>
			</div>

			<div class="col-sm-4 ">
				<div class="form-group">
					{!! Form::label('location_id', __('purchase.business_location').':*') !!}
					@show_tooltip(__('tooltip.purchase_location'))
					{!! Form::select('location_id', $business_locations, $default_location, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required'], $bl_attributes); !!}
				</div>
			</div>
			<div class="col-sm-4">
				<div class="form-group">
					{!! Form::label('document', __('purchase.attach_document') . ':') !!}
					{!! Form::file('document', ['id' => 'upload_document', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
				</div>
			</div>
			

			<!-- Currency Exchange Rate -->
			<div class="col-sm-4 @if(!$currency_details->purchase_in_diff_currency) hide @endif">
				<div class="form-group">
					{!! Form::label('exchange_rate', __('purchase.p_exchange_rate') . ':*') !!}
					@show_tooltip(__('tooltip.currency_exchange_factor'))
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-info"></i>
						</span>
						{!! Form::number('exchange_rate', $currency_details->p_exchange_rate, ['class' => 'form-control', 'required', 'step' => 0.001]); !!}
					</div>
					<span class="help-block text-danger">
						@lang('purchase.diff_purchase_currency_help', ['currency' => $currency_details->name])
					</span>
				</div>
			</div>

			<div class="col-md-4" style="display:none" >
		          <div class="form-group">
		            <div class="multi-input">
		              {!! Form::label('pay_term_number', __('contact.pay_term') . ':') !!} @show_tooltip(__('tooltip.pay_term'))
		              <br/>
		              {!! Form::number('pay_term_number', null, ['class' => 'form-control width-40 pull-left', 'placeholder' => __('contact.pay_term')]); !!}

		              {!! Form::select('pay_term_type', 
		              	['months' => __('lang_v1.months'), 
		              		'days' => __('lang_v1.days')], 
		              		null, 
		              	['class' => 'form-control width-60 pull-left','placeholder' => __('messages.please_select'), 'id' => 'pay_term_type']); !!}
		            </div>
		        </div>
		    </div>
		    
		    	<div class="col-sm-4" style="display: none;" >
				<div class="form-group">
					{!! Form::label('Delivery Date', __('Sales Man') . ':*') !!}
					<div class="input-group">
						
						<select name="sales_man" class="form-control select2">
							<option selected disabled> Please Select</option>
							@foreach ($sale_man as $s)
								<option value="{{$s->id}}">{{$s->supplier_business_name}}</option>
							@endforeach
							
						</select>
					</div>
				</div>
			</div>

		
		</div>
	@endcomponent

	@component('components.widget', ['class' => 'box-solid'])
		<div class="row">
			
			<div class="col-sm-2">
				<div class="form-group">
					<button tabindex="-1" type="button" class="btn btn-primary btn-modal"data-href="{{action('ProductController@quickAdd')}}" 
            		data-container=".quick_add_product_modal"><i class="fa fa-plus"></i> @lang( 'product.add_new_product' ) </button>
				</div>
			</div>
		</div>
		@php
			$hide_tax = '';
			if( session()->get('business.enable_inline_tax') == 0){
				$hide_tax = 'hide';
			}
		@endphp
		<div class="row">
			<div class="col-sm-12">
				<div class="table-responsive">
					<table class="table table-condensed table-bordered table-th-green text-center table-striped" id="purchase_entry_table_req" >
						<thead>
							<tr>
								<th>Delete</th>
								<th>#</th>
								<th width=12% class="hide">Store</th>
								<th>Sku</th>
								<th width=40%>Product</th>
								<th width=12%>Brand</th>
							    <th>UOM</th>
								<th>Product Descriptions</th>	
								<th>Qty</th>
								<th class="hide">
									@lang( 'lang_v1.profit_margin' )
								</th>

							</tr>
						</thead>
						<tbody id="tbody">

							<tr>
								<td><button class="btn btn-danger remove" type="button" onclick="remove_req(this)" style="padding: 0px 5px 2px 5px;"><i class="fa fa-trash" aria-hidden="true"></i></button>
								</td>
								<td><span class="sr_number"></span></td>
								<td>{!! Form::text('purchases[2][item_code]',null, ['class' => 'form-control product_code','readonly','id' => 'item_code']); !!}</td>
								<td class="hide">
									{!! Form::select('purchases[0][store]', $store,null, ['class' => 'form-control ','required' ]); !!}
								 </td>
								 <td>
								     <input type="hidden" class="old_product_id">
									{!! Form::select('purchases[0][product_id]', array(),null, ['class' => 'form-control products_change select2','placeholder'=>"Search Your Product" ,'id' => 'search_product','required','Style' => 'width:200px;','onchange'=>"get_product_code_req(this)"]); !!}
									<input type="hidden" name="gross_weight" class="gross__weight">
									<input type="hidden" name="net_weight" class="net__weight">
								</td>
								
								<td>
								<select class="form-control select2 brand_select" name="purchases[0][brand_id]" >
									<option value="" selected disabled>Please Select</option>
									@foreach($brand as $br)
									<option value="{{$br->id}}">{{$br->name}}</option>
									@endforeach
								</select>
								</td>
								<td>
								    <input type="text" class="form-control uom" readonly>
								</td>
								 <td>{!! Form::textarea('purchases[0][item_description]',null, ['class' => 'form-control ','rows'=>'1','placeholder'=>"descrition" ]); !!}
									
								</td>
								
								 <td>{!! Form::text('purchases[0][quantity]',null, ['class' => 'form-control purchase_quantity input_number mousetrap','maxlength'=>'5','placeholder'=>"Qty",'required']); !!}
								
									{!! Form::hidden('purchases[0][pp_without_discount]',0, ['class' => 'form-control input-sm purchase_unit_cost_without_discount input_number','placeholder'=>"Unit Price" ]); !!}
									{!! Form::hidden('purchases[0][discount_percent]',0, ['class' => 'form-control input-sm inline_discounts input_number','placeholder'=>"Discount" ]); !!}
									{!! Form::hidden('purchases[0][purchase_price]',0, ['class' => 'form-control input-sm purchase_unit_cost input_number','placeholder'=>"Unit Cost (Before Tax)" ]); !!}
								
									<input type="hidden" class="row_subtotal_before_tax_hidden" value=0>
	
										
											{!! Form::hidden('purchases[0][item_tax]', 0, ['class' => 'purchase_product_unit_tax']); !!}
										
									{!! Form::hidden('purchases[0][purchase_price_inc_tax]',0, ['class' => 'form-control input-sm purchase_unit_cost_after_tax input_number','placeholder'=>"Sales Tax Amount" ]); !!}
								
						
									{!! Form::hidden('purchases[0][purchase_line_tax_id]', 0, ['class' => 'purchase_product_unit_tax','data-tax_amount'=>'0']); !!}
									{!! Form::hidden('purchases[0][item_tax]', 0, ['class' => 'purchase_product_unit_tax']); !!}



									<input type="hidden" class="row_subtotal_after_tax_hidden" value=0>
								
								</td>
		
						
							</tr>

							<tr>
								<td><button class="btn btn-danger remove" type="button" onclick="remove_req(this)" style="padding: 0px 5px 2px 5px;"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
								<td>
								
									<span class="sr_number"></span></td>
								<td class="hide">
									{!! Form::select('purchases[1][store]', $store,null, ['class' => 'form-control ' ]); !!}
								 </td>
								 <td>{!! Form::text('purchases[1][item_code]',null, ['class' => 'form-control product_code','readonly','id' => 'item_code']); !!}</td>
								 <td>
								     <input type="hidden" class="old_product_id">
									{!! Form::select('purchases[1][product_id]', array(),null,['class' => 'form-control products_change select2','placeholder'=>"Search Your Product" ,'id' => 'search_product','Style' => 'width:200px;','onchange'=>"get_product_code_req(this)"]); !!}
									<input type="hidden" name="gross_weight" class="gross__weight">
									<input type="hidden" name="net_weight" class="net__weight">
								</td>
							
								<td>
								<select class="form-control select2" name="purchases[1][brand_id]" >
									<option value="" selected disabled>Please Select</option>
									@foreach($brand as $br)
									<option value="{{$br->id}}">{{$br->name}}</option>
									@endforeach
								</select>
								</td>
									
									<td>
								     <input type="text" class="form-control uom" readonly>
								</td>
								
								</td>
								 <td>{!! Form::textarea('purchases[1][item_description]',null, ['class' => 'form-control ','rows'=>'1','placeholder'=>"descrition" ]); !!}
									
								</td>
								
								 <td>{!! Form::text('purchases[1][quantity]',null, ['class' => 'form-control purchase_quantity input_number mousetrap','maxlength'=>'5','placeholder'=>"Qty" ]); !!}
								
									{!! Form::hidden('purchases[1][pp_without_discount]',0, ['class' => 'form-control input-sm purchase_unit_cost_without_discount input_number','placeholder'=>"Unit Price" ]); !!}
									{!! Form::hidden('purchases[1][discount_percent]',0, ['class' => 'form-control input-sm inline_discounts input_number','placeholder'=>"Discount" ]); !!}
									{!! Form::hidden('purchases[1][purchase_price]',0, ['class' => 'form-control input-sm purchase_unit_cost input_number','placeholder'=>"Unit Cost (Before Tax)" ]); !!}
								
									<input type="hidden" class="row_subtotal_before_tax_hidden" value=0>
	
										
											{!! Form::hidden('purchases[1][item_tax]', 0, ['class' => 'purchase_product_unit_tax']); !!}
										
									{!! Form::hidden('purchases[1][purchase_price_inc_tax]',0, ['class' => 'form-control input-sm purchase_unit_cost_after_tax input_number','placeholder'=>"Sales Tax Amount"]); !!}
								
						
									{!! Form::hidden('purchases[1][purchase_line_tax_id]', 0, ['class' => 'purchase_product_unit_tax','data-tax_amount'=>'0']); !!}
									{!! Form::hidden('purchases[1][item_tax]', 0, ['class' => 'purchase_product_unit_tax']); !!}



									<input type="hidden" class="row_subtotal_after_tax_hidden" value=0>
								
								</td>
							</tr>

							<tr>
								<td><button class="btn btn-danger remove" type="button" onclick="remove_req(this)" style="padding: 0px 5px 2px 5px;"><i class="fa fa-trash" aria-hidden="true"></i></button>
								</td>
														
								<td><span class="sr_number"></span></td>
								<td class="hide">
									{!! Form::select('purchases[2][store]', $store,null, ['class' => 'form-control ' ]); !!}
								 </td>
								 <td>{!! Form::text('purchases[2][item_code]',null, ['class' => 'form-control product_code','readonly','id' => 'item_code']); !!}</td>
								 <td>
								     
								     <input type="hidden" class="old_product_id">
									{!! Form::select('purchases[2][product_id]', array(),null, ['class' => 'form-control products_change select2','placeholder'=>"Search Your Product" ,'id' => 'search_product','Style' => 'width:200px;','onchange'=>"get_product_code_req(this)"]); !!}
									<input type="hidden" name="gross_weight" class="gross__weight">
									<input type="hidden" name="net_weight" class="net__weight">
								</td>
								<td>
								<select class="form-control select2" name="purchases[2][brand_id]" id="brand_id">
									<option value="" selected disabled>Please Select</option>
									@foreach($brand as $br)
									<option value="{{$br->id}}">{{$br->name}}</option>
									@endforeach
								</select>
								</td>
									<td>
								     <input type="text" class="form-control uom" readonly>
								</td>
								 <td>{!! Form::textarea('purchases[2][item_description]',null, ['class' => 'form-control ','rows'=>'1','placeholder'=>"descrition" ]); !!}
									
								</td>
								
								 <td>{!! Form::text('purchases[2][quantity]',null, ['class' => 'form-control purchase_quantity input_number mousetrap','maxlength'=>'5','placeholder'=>"Qty" ]); !!}
								
									{!! Form::hidden('purchases[2][pp_without_discount]',0, ['class' => 'form-control input-sm purchase_unit_cost_without_discount input_number','placeholder'=>"Unit Price" ]); !!}
									{!! Form::hidden('purchases[2][discount_percent]',0, ['class' => 'form-control input-sm inline_discounts input_number','placeholder'=>"Discount" ]); !!}
									{!! Form::hidden('purchases[2][purchase_price]',0, ['class' => 'form-control input-sm purchase_unit_cost input_number','placeholder'=>"Unit Cost (Before Tax)" ]); !!}
								
									<input type="hidden" class="row_subtotal_before_tax_hidden" value=0>
	
										
											{!! Form::hidden('purchases[2][item_tax]', 0, ['class' => 'purchase_product_unit_tax']); !!}
										
									{!! Form::hidden('purchases[2][purchase_price_inc_tax]',0, ['class' => 'form-control input-sm purchase_unit_cost_after_tax input_number','placeholder'=>"Sales Tax Amount" ]); !!}
								
						
									{!! Form::hidden('purchases[2][purchase_line_tax_id]', 0, ['class' => 'purchase_product_unit_tax','data-tax_amount'=>'0']); !!}
									{!! Form::hidden('purchases[2][item_tax]', 0, ['class' => 'purchase_product_unit_tax']); !!}



									<input type="hidden" class="row_subtotal_after_tax_hidden" value=0>
								
								</td>
								



							
							</tr>


						</tbody>
					</table>
				</div>
				<hr/>
				<button class="btn btn-md btn-primary addBtn" type="button"  onclick="add_req(this)" 
				style="padding: 0px 5px 2px 5px;">
				Add Row</button>
				<div class="pull-right col-md-5">
					<table class="pull-right col-md-12 total_data">
						<tr>
							<th class="col-md-7 text-right">@lang( 'lang_v1.total_items' ):</th>
							<td class="col-md-5 text-left">
								<span id="total_quantity" class="display_currency" data-currency_symbol="false"></span>
							</td>
						</tr>
						<tr class="hide">
							<th class="col-md-7 text-right">@lang( 'purchase.total_before_tax' ):</th>
							<td class="col-md-5 text-left">
								<span id="total_st_before_tax" class="display_currency"></span>
								<input type="hidden" id="st_before_tax_input" value=0>
							</td>
						</tr>
						<tr class="hide">
							<th class="col-md-7 text-right">@lang( 'purchase.net_total_amount' ):</th>
							<td class="col-md-5 text-left">
								<span id="total_subtotal" class="display_currency"></span>
								<!-- This is total before purchase tax-->
								<input type="hidden" id="total_subtotal_input" value=0  name="total_before_tax">
							</td>
						</tr>
						<tr>
							<th class="col-md-7 text-right">Total Gross Weight:</th>
							<td class="col-md-5 text-left">
								<span id="total_gross__weight" class="display_currency" data-currency_symbol="false"></span>
								<input type="hidden" name="total_gross__weight" class="total_gross__weight"/>
							</td>
						</tr>
						<tr>
							<th class="col-md-7 text-right">Total Net Weight:</th>
							<td class="col-md-5 text-left">
								<span id="total_net__weight" class="display_currency" data-currency_symbol="false"></span>
								<input type="hidden" name="total_net__weight" class="total_net__weight"/>
							</td>
						</tr>

					</table>
				</div>

				<input type="hidden" id="row_count" value="0">
			</div>
		</div>
	@endcomponent

	@component('components.widget', ['class' => 'box-solid'])
	<div class="row" id="shipping_div" style="display:none" >
		<div class="col-md-4">
			<div class="form-group">
	            {!! Form::label('shipping_details', __('sale.shipping_details')) !!}
	            {!! Form::textarea('shipping_details',null, ['class' => 'form-control','placeholder' => __('sale.shipping_details') ,'rows' => '3', 'cols'=>'30']); !!}
	        </div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
	            {!! Form::label('shipping_address', __('lang_v1.shipping_address')) !!}
	            {!! Form::textarea('shipping_address',null, ['class' => 'form-control','placeholder' => __('lang_v1.shipping_address') ,'rows' => '3', 'cols'=>'30']); !!}
	        </div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				{!!Form::label('shipping_charges', __('sale.shipping_charges'))!!}
				<div class="input-group">
				<span class="input-group-addon">
				<i class="fa fa-info"></i>
				</span>
				{!!Form::text('shipping_charges',@num_format(0.00),['class'=>'form-control input_number','placeholder'=> __('sale.shipping_charges')]);!!}
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="col-md-4">
			<div class="form-group">
	            {!! Form::label('shipping_status', __('lang_v1.shipping_status')) !!}
	            {!! Form::select('shipping_status',$shipping_statuses, null, ['class' => 'form-control','placeholder' => __('messages.please_select')]); !!}
	        </div>
		</div>
		<div class="col-md-4">
	        <div class="form-group">
	            {!! Form::label('delivered_to', __('lang_v1.delivered_to') . ':' ) !!}
	            {!! Form::text('delivered_to', null, ['class' => 'form-control','placeholder' => __('lang_v1.delivered_to')]); !!}
	        </div>
	    </div>
	    @php
	    	$custom_labels = json_decode(session('business.custom_labels'), true);
	        $shipping_custom_label_1 = !empty($custom_labels['shipping']['custom_field_1']) ? $custom_labels['shipping']['custom_field_1'] : '';

	        $is_shipping_custom_field_1_required = !empty($custom_labels['shipping']['is_custom_field_1_required']) && $custom_labels['shipping']['is_custom_field_1_required'] == 1 ? true : false;

	        $shipping_custom_label_2 = !empty($custom_labels['shipping']['custom_field_2']) ? $custom_labels['shipping']['custom_field_2'] : '';

	        $is_shipping_custom_field_2_required = !empty($custom_labels['shipping']['is_custom_field_2_required']) && $custom_labels['shipping']['is_custom_field_2_required'] == 1 ? true : false;

	        $shipping_custom_label_3 = !empty($custom_labels['shipping']['custom_field_3']) ? $custom_labels['shipping']['custom_field_3'] : '';
	        
	        $is_shipping_custom_field_3_required = !empty($custom_labels['shipping']['is_custom_field_3_required']) && $custom_labels['shipping']['is_custom_field_3_required'] == 1 ? true : false;

	        $shipping_custom_label_4 = !empty($custom_labels['shipping']['custom_field_4']) ? $custom_labels['shipping']['custom_field_4'] : '';
	        
	        $is_shipping_custom_field_4_required = !empty($custom_labels['shipping']['is_custom_field_4_required']) && $custom_labels['shipping']['is_custom_field_4_required'] == 1 ? true : false;

	        $shipping_custom_label_5 = !empty($custom_labels['shipping']['custom_field_5']) ? $custom_labels['shipping']['custom_field_5'] : '';
	        
	        $is_shipping_custom_field_5_required = !empty($custom_labels['shipping']['is_custom_field_5_required']) && $custom_labels['shipping']['is_custom_field_5_required'] == 1 ? true : false;
        @endphp

        @if(!empty($shipping_custom_label_1))
        	@php
        		$label_1 = $shipping_custom_label_1 . ':';
        		if($is_shipping_custom_field_1_required) {
        			$label_1 .= '*';
        		}
        	@endphp

        	<div class="col-md-4">
		        <div class="form-group">
		            {!! Form::label('shipping_custom_field_1', $label_1 ) !!}
		            {!! Form::text('shipping_custom_field_1', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_1, 'required' => $is_shipping_custom_field_1_required]); !!}
		        </div>
		    </div>
        @endif
        @if(!empty($shipping_custom_label_2))
        	@php
        		$label_2 = $shipping_custom_label_2 . ':';
        		if($is_shipping_custom_field_2_required) {
        			$label_2 .= '*';
        		}
        	@endphp

        	<div class="col-md-4">
		        <div class="form-group">
		            {!! Form::label('shipping_custom_field_2', $label_2 ) !!}
		            {!! Form::text('shipping_custom_field_2', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_2, 'required' => $is_shipping_custom_field_2_required]); !!}
		        </div>
		    </div>
        @endif
        @if(!empty($shipping_custom_label_3))
        	@php
        		$label_3 = $shipping_custom_label_3 . ':';
        		if($is_shipping_custom_field_3_required) {
        			$label_3 .= '*';
        		}
        	@endphp

        	<div class="col-md-4">
		        <div class="form-group">
		            {!! Form::label('shipping_custom_field_3', $label_3 ) !!}
		            {!! Form::text('shipping_custom_field_3', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_3, 'required' => $is_shipping_custom_field_3_required]); !!}
		        </div>
		    </div>
        @endif
        @if(!empty($shipping_custom_label_4))
        	@php
        		$label_4 = $shipping_custom_label_4 . ':';
        		if($is_shipping_custom_field_4_required) {
        			$label_4 .= '*';
        		}
        	@endphp

        	<div class="col-md-4">
		        <div class="form-group">
		            {!! Form::label('shipping_custom_field_4', $label_4 ) !!}
		            {!! Form::text('shipping_custom_field_4', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_4, 'required' => $is_shipping_custom_field_4_required]); !!}
		        </div>
		    </div>
        @endif
        @if(!empty($shipping_custom_label_5))
        	@php
        		$label_5 = $shipping_custom_label_5 . ':';
        		if($is_shipping_custom_field_5_required) {
        			$label_5 .= '*';
        		}
        	@endphp

        	<div class="col-md-4">
		        <div class="form-group">
		            {!! Form::label('shipping_custom_field_5', $label_5 ) !!}
		            {!! Form::text('shipping_custom_field_5', null, ['class' => 'form-control','placeholder' => $shipping_custom_label_5, 'required' => $is_shipping_custom_field_5_required]); !!}
		        </div>
		    </div>
        @endif
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('shipping_documents', __('lang_v1.shipping_documents') . ':') !!}
                {!! Form::file('shipping_documents[]', ['id' => 'shipping_documents', 'multiple', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
                <p class="help-block">
                	@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                	@includeIf('components.document_help_text')
                </p>
            </div>
        </div>        
	</div>
	<div class="row">
			<div class="col-md-12 text-center" style="display:none;">
				<button type="button" class="btn btn-primary btn-sm" id="toggle_additional_expense"> <i class="fas fa-plus"></i> @lang('lang_v1.add_additional_expenses') <i class="fas fa-chevron-down"></i></button>
			</div>
			<div class="col-md-8 col-md-offset-4" id="additional_expenses_div" style="display: none;">
				<table class="table table-condensed">
					<thead>
						<tr>
							<th>@lang('lang_v1.additional_expense_name')</th>
							<th>@lang('sale.amount')</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								{!! Form::text('additional_expense_key_1', null, ['class' => 'form-control']); !!}
							</td>
							<td>
								{!! Form::text('additional_expense_value_1', 0, ['class' => 'form-control input_number', 'id' => 'additional_expense_value_1']); !!}
							</td>
						</tr>
						<tr>
							<td>
								{!! Form::text('additional_expense_key_2', null, ['class' => 'form-control']); !!}
							</td>
							<td>
								{!! Form::text('additional_expense_value_2', 0, ['class' => 'form-control input_number', 'id' => 'additional_expense_value_2']); !!}
							</td>
						</tr>
						<tr>
							<td>
								{!! Form::text('additional_expense_key_3', null, ['class' => 'form-control']); !!}
							</td>
							<td>
								{!! Form::text('additional_expense_value_3', 0, ['class' => 'form-control input_number', 'id' => 'additional_expense_value_3']); !!}
							</td>
						</tr>
						<tr>
							<td>
								{!! Form::text('additional_expense_key_4', null, ['class' => 'form-control']); !!}
							</td>
							<td>
								{!! Form::text('additional_expense_value_4', 0, ['class' => 'form-control input_number', 'id' => 'additional_expense_value_4']); !!}
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	<div class="row" style="display:none;">
		<div class="col-md-4 col-md-offset-8">
	    {!! Form::hidden('final_total', 0 , ['id' => 'grand_total_hidden']); !!}
		<b>@lang('lang_v1.order_total'): </b><span id="grand_total" class="display_currency" data-currency_symbol='true'>0</span>
		</div>
	</div>
	@endcomponent

	@component('components.widget', ['class' => 'box-solid'])
		<div class="row">
			<div class="col-sm-12">
			<table class="table">
				<tr class="hide">
					<td class="col-md-3">
						<div class="form-group">
							{!! Form::label('discount_type', __( 'purchase.discount_type' ) . ':') !!}
							{!! Form::select('discount_type', [ '' => __('lang_v1.none'), 'fixed' => __( 'lang_v1.fixed' ), 'percentage' => __( 'lang_v1.percentage' )], '', ['class' => 'form-control select2']); !!}
						</div>
					</td>
					<td class="col-md-3">
						<div class="form-group">
						{!! Form::label('discount_amount', __( 'purchase.discount_amount' ) . ':') !!}
						{!! Form::text('discount_amount', 0, ['class' => 'form-control input_number', 'required']); !!}
						</div>
					</td>
					<td class="col-md-3">
						&nbsp;
					</td>
					<td class="col-md-3">
						<b>@lang( 'purchase.discount' ):</b>(-) 
						<span id="discount_calculated_amount" class="display_currency">0</span>
					</td>
				</tr>
				<tr class="hide">
					<td>
						<div class="form-group">
						{!! Form::label('tax_id', __('purchase.purchase_tax') . ':') !!}
						<select name="tax_id" id="tax_id" class="form-control select2" placeholder="'Please Select'">
							<option value="" data-tax_amount="0" data-tax_type="fixed" selected>@lang('lang_v1.none')</option>
							@foreach($taxes as $tax)
								<option value="{{ $tax->id }}" data-tax_amount="{{ $tax->amount }}" data-tax_type="{{ $tax->calculation_type }}">{{ $tax->name }}</option>
							@endforeach
						</select>
						{!! Form::hidden('tax_amount', 0, ['id' => 'tax_amount']); !!}
						</div>
					</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>
						<b>@lang( 'purchase.purchase_tax' ):</b>(+) 
						<span id="tax_calculated_amount" class="display_currency">0</span>
					</td>
				</tr>
			

			</table>
			
			</div>
			
		</div>
		
		<div class="col-sm-3">
				<div class="form-group">
			<label>Term & Condition</label>
					<select class="form-control" name="tandc_type" id="TCS">
                       <option selected disabled> Select</option>
						@foreach ($T_C as $tc)
						<option value="{{$tc->id}}">{{$tc->title}}</option>
						@endforeach
					</select>

				</div>
			</div>
			<div class="col-sm-12">
			<div class="form-group" id="TandC" style="display:none;">
							{!! Form::label('tandc_title',__('Terms & Conditions')) !!}
							{!! Form::textarea('tandc_title', null, ['class' => 'form-control name','id'=>'product_description1','rows' => 3]); !!}
			</div>
		</div>
	@endcomponent


	<div class="col-sm-12 fixed-button">
		<input type="hidden" name="submit_type" id="submit_type">
		<div class="text-center">
			<div class="btn-group">
			  
				<button type="submit" name='save' value="save_n_add_another" id="submit_purchase_form_req"
					class="btn-big btn-primary submit_purchase_order_form" >Save & Next</button>
					
				<button type="submit" value="submit"
					class="btn-big btn-primary submit_purchase_order_form" id="submit_purchase_form_req" accesskey="s">Save & Close</button>
					<button class="btn-big btn-danger " onclick="window.history.back()">Close</button>
			</div>
		</div>
	</div>
</div>
{!! Form::close() !!}
</section>
<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	{{-- @include('contact.create', ['quick_add' => true]) --}}
</div>
<!-- /.content -->
@endsection

@section('javascript')
	<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/purchase_req.js') }}"></script>
	<script type="text/javascript">


	</script>
	
	@include('purchase.partials.keyboard_shortcuts')
@endsection
