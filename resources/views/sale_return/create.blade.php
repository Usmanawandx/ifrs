@extends('layouts.app')

@php
	if (!empty($status) && $status == 'quotation') {
		$title = __('lang_v1.add_quotation');
	} else if (!empty($status) && $status == 'draft') {
		$title = __('lang_v1.add_draft');
	} else {
		$title = __('Create Sale Return Invoice');
	}

	if($sale_type == 'sales_order') {
		$title = __('lang_v1.sales_order');
	}
@endphp

@section('title', $title)

@section('content')

<!-- Content Header (Page header) -->
<style>
    .select2-container--default{
        width:100% !Important;
    }
    #tbody textarea.form-control {
    height: 35px !important;
    width: 100% !important;
}
		#add_charges_acc_dropdown+.select2-container,
        #less_charges_acc_dropdown+.select2-container {
            width: 70% !important;
        }
</style>
<section class="content-header">
    <h1>{{$title}}<span class="pull-right top_trans_no"></span></h1>
</section>
<!-- Main content -->
{{-- {{dd($product)}} --}}
<section class="content no-print">
<input type="hidden" id="amount_rounding_method" value="{{$pos_settings['amount_rounding_method'] ?? ''}}">
@if(!empty($pos_settings['allow_overselling']))
	<input type="hidden" id="is_overselling_allowed">
@endif
@if(session('business.enable_rp') == 1)
    <input type="hidden" id="reward_point_enabled">
@endif
@if(count($business_locations) > 0)
<div class="row hide">
	<div class="col-sm-4">
		<div class="form-group">
			<div class="input-group">
				<span class="input-group-addon">
					<i class="fa fa-map-marker"></i>
				</span>
			{!! Form::select('select_location_id', $business_locations, $default_location->id ?? null, ['class' => 'form-control input-sm',
			'id' => 'select_location_id', 
			'required', 'autofocus'], $bl_attributes); !!}
			<span class="input-group-addon">
					@show_tooltip(__('tooltip.sale_location'))
				</span> 
			</div>
		</div>
	</div>
</div>
@endif

@php
	$custom_labels = json_decode(session('business.custom_labels'), true);
	$common_settings = session()->get('business.common_settings');
@endphp
<input type="hidden" id="item_addition_method" value="{{$business_details->item_addition_method}}">
	{!! Form::open(['url' => action('SellPosController@return_store'), 'method' => 'post', 'id' => 'add_sell_form', 'files' => true ]) !!}
	@if(!empty($sale_type))
	 	<input type="hidden" id="sale_type" name="type" value="{{$sale_type}}">
	 @endif
	<div class="row" >
		<div class="col-md-12 col-sm-12">
			@component('components.widget', ['class' => 'box-solid'])
				{!! Form::hidden('location_id', !empty($default_location) ? $default_location->id : null , ['id' => 'location_id', 'data-receipt_printer_type' => !empty($default_location->receipt_printer_type) ? $default_location->receipt_printer_type : 'browser', 'data-default_payment_accounts' => !empty($default_location) ? $default_location->default_payment_accounts : '']); !!}

				@if(!empty($price_groups))
					@if(count($price_groups) > 1)
						<div class="col-sm-4" style="  display: none;">
							<div class="form-group">
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fas fa-money-bill-alt"></i>
									</span>
									@php
										reset($price_groups);
									@endphp
									{!! Form::hidden('hidden_price_group', key($price_groups), ['id' => 'hidden_price_group']) !!}
									{!! Form::select('price_group', $price_groups, null, ['class' => 'form-control select2', 'id' => 'price_group']); !!}
									<span class="input-group-addon">
										@show_tooltip(__('lang_v1.price_group_help_text'))
									</span> 
								</div>
							</div>
						</div>
						
					@else
						@php
							reset($price_groups);
						@endphp
						{!! Form::hidden('price_group', key($price_groups), ['id' => 'price_group']) !!}
					@endif
				@endif

				{!! Form::hidden('default_price_group', null, ['id' => 'default_price_group']) !!}

				@if(in_array('types_of_service', $enabled_modules) && !empty($types_of_service))
					<div class="col-md-4 col-sm-6" style="  display: none;">
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-external-link-square-alt text-primary service_modal_btn"></i>
								</span>
								{!! Form::select('types_of_service_id', $types_of_service, null, ['class' => 'form-control', 'id' => 'types_of_service_id', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.select_types_of_service')]); !!}

								{!! Form::hidden('types_of_service_price_group', null, ['id' => 'types_of_service_price_group']) !!}

								<span class="input-group-addon">
									@show_tooltip(__('lang_v1.types_of_service_help'))
								</span> 
							</div>
							<small><p class="help-block hide" id="price_group_text">@lang('lang_v1.price_group'): <span></span></p></small>
						</div>
					</div>
					<div class="modal fade types_of_service_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
				@endif
				
				@if(in_array('subscription', $enabled_modules))
					<div class="col-md-4 pull-right col-sm-6">
						<div class="checkbox">
							<label>
				              {!! Form::checkbox('is_recurring', 1, false, ['class' => 'input-icheck', 'id' => 'is_recurring']); !!} @lang('lang_v1.subscribe')?
				            </label><button type="button" data-toggle="modal" data-target="#recurringInvoiceModal" class="btn btn-link"><i class="fa fa-external-link"></i></button>@show_tooltip(__('lang_v1.recurring_invoice_help'))
						</div>
					</div>
				@endif
				<div>
					

				<div class="col-sm-3 hide">
					<div class="form-group">
						{!! Form::label('Sales Invoice No', __('Sales Invoice No') . ':*') !!}
						{!! Form::select('sale_invoice_no', $sale_invoice_no , null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>

				<div class="col-sm-4">
					<div class="form-group">
						<label>Sale Type</label>
						<div class="input-group">
                            <select class="form-control get__prefix" name="saleType"  required>
    						   <option selected disabled> Select</option>
    							@foreach ($sale_category as $tp)
    							<option value="{{$tp->id}}" data-pf="{{$tp->prefix}}" data-trans_id="{{$tp->control_account_id}}">{{$tp->name}}</option>
    							@endforeach
    						</select>
    						<span class="input-group-btn">
								<button type="button" class="btn btn-default bg-white btn-flat btn-modal"
								data-href="{{action('SalesOrderController@sale_type_partial')}}" data-container=".view_modal"><i
								class="fa fa-plus-circle text-primary fa-lg"></i></button>
						    </span>
    					</div>
					</div>
				</div>

				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('Transaction No:', __('Transaction No').':') !!}
						<input type="hidden" name="prefix" class="trn_prefix" value="{{$SR."-"}}">
						<div class="input-group">
							<span class="input-group-addon trn_prefix_addon">
								{{$SR."-"}}
							</span>
						{!! Form::text('ref_no',$unni, ['class' => 'form-control ref_no','id' => 'SRI_ref_no']); !!}
						</div>
					</div>
				</div>

				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('transaction_date', __('Transaction Date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('transaction_date', @format_date('now'), ['class' => 'form-control', 'required']); !!}
						</div>
					</div>
				</div>
					
				<div class="clearfix"></div>

				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('supplier_id', __('Customer Name') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-user"></i>
							</span>
							{!! Form::select('contact_id',$supplier, null, ['class' => 'form-control select2 quick_add_cust', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'supplier' ,'onchange'=> 'get_ntn_cnic(this)']); !!}
							<span class="input-group-btn">
								<button type="button" class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action('ContactController@create', ['type' => 'customer'])}}" 
								data-container=".contact_modal">
								<i class="fa fa-plus-circle text-primary fa-lg"></i></button>
							</span>
						</div>
					</div>
				</div>

				<div class="col-sm-5">
					<div class="form-group">
						{!! Form::label('Address', 'Address' . ':*') !!}
						<input type="text" class="form-control contact_address" readonly/>
					</div>
				</div>

				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('ntncnic', $sale_type == 'sales_order' ? __('NTN / CNIC No') : __('NTN / CNIC No') . ':') !!}
						{!! Form::text('ntncnic', null, ['class' => 'form-control ntncnic', 'placeholder' => $sale_type == 'sales_order' ? __('NTN / CNIC No') : __('NTN / CNIC No'), 'readonly']); !!}
					</div>
				</div>

				<div class="clearfix"></div>

				<div class="col-sm-3">
					{!! Form::label('Transporter Name', __('Transporter Name') . ':*') !!}
					<select name="transporter_name" class="form-control transporter" required vehicle_no="">
						<option disabled selected>Please Select</option>
						@foreach ($transporter as $transport)
						<option value="{{$transport->id}}">{{$transport->supplier_business_name}}</option>
						@endforeach
					</select>
				</div>

				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('Vehicle No', __('Vehicle No') . ':*') !!}
						<div class="vehicles_parent">
							<select class="form-control vehicles" style="width: 100%">
								<option disabled selected> Please Select</option>
							</select>
							<input type="text" class="form-control vehicles_input" style="display: none;"
								placeholder="vehicle no" />
						</div>
					</div>
				</div>

				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('sales_man', __('Sales Man') . ':*') !!}
						<input type="hidden" id="saleman_commission" name="saleman_commission" />
						<select name="sales_man" class="form-control select2" id="sales_man">
							<option selected disabled> Please Select</option>
							@foreach ($sale_man as $s)
								<option value="{{$s->id}}" data-commission="{{$s->custom_field1}}" {{ ($s->id == $default_sales_man) ? 'selected' : '' }}>{{$s->supplier_business_name}}</option>
							@endforeach
						</select>
					</div>
				</div>
				
				<div class="col-md-3">
			    	<div class="form-group">
						{!! Form::label('additional_notes',__('Remarks')) !!}
						{!! Form::textarea('additional_notes', null, ['class' => 'form-control', 'rows' => 1]); !!}
					</div>
			    </div>

				<div class="col-sm-3">
    				<label>Transaction Account</label>
        			{!! Form::select('transaction_account', $accounts, (!empty($transaction_account) ? $transaction_account : null), 
        			['class' => 'form-control select2','placeholder'=>"Select Please",'id'=>"transaction_account_return"]); !!}
    			</div>

				<div class="col-sm-3">
					<label>Add & Less Charges</label>
					<select class="form-control AddAndLess">
						<option value="add">Add Charges</option>
						<option value="less">Less Charges</option>
					</select>
				</div>

				<div class="col-sm-3 add_charges">
					<label>Add Charges ( + )</label>
					<div class="input-group" style="width:100%">
						<input type="number" class="form-control" name="add_charges" style="width:30%" />
						{!! Form::select('add_charges_acc_dropdown', $accounts, !empty($addless_charges) ? $addless_charges : null, [
							'class' => 'form-control select2',
							'placeholder' => 'Select Please',
							'style' => 'width:50%',
							'id' => 'add_charges_acc_dropdown',
						]) !!}
					</div>
				</div>
				
				<div class="col-sm-3 less_charges" style="display:none">
					<label>Less Charges ( - )</label>
					<div class="input-group" style="width:100%">
						<input type="number" class="form-control" name="less_charges" style="width:30%" />
						{!! Form::select('less_charges_acc_dropdown', $accounts, !empty($addless_charges) ? $addless_charges : null, [
							'class' => 'form-control select2',
							'placeholder' => 'Select Please',
							'style' => 'width:50%',
							'id' => 'less_charges_acc_dropdown',
						]) !!}
					</div>
				</div>

				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('upload_document', __('purchase.attach_document') . ':') !!}
						{!! Form::file('sell_document', ['id' => 'upload_document', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
					</div>
				</div>







				
				<div class="col-sm-4 hide" >
						<div class="form-group">
							{!! Form::label('gstno', $sale_type == 'sales_order' ? __('restaurant.order_no') : __('GST NO') . ':') !!}
							{!! Form::text('gstno', null, ['class' => 'form-control gst', 'placeholder' => $sale_type == 'sales_order' ? __('restaurant.order_no') : __('GST NO')]); !!}
							<p  style="display: none;" class="help-block">@lang('lang_v1.keep_blank_to_autogenerate')</p>
						</div>
					</div>
					
					<div class="clearfix"></div>
				<div class="col-sm-4 hide">
					<div class="form-group">
						{!! Form::label('posting_date', __('Posting Date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('posting_date', @format_date('now'), ['class' => 'form-control', 'readonly','required']); !!}

						</div>
					</div>
				</div>

				



				<div class="@if(!empty($commission_agent)) col-sm-4 @else col-sm-4 @endif hide">
					<div class="form-group">
						{!! Form::label('return_date', __('Return Date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::date('return_date',date('Y-m-d'), ['class' => 'form-control', 'required']); !!}

							<!-- {!! Form::text('return_date', $default_datetime, ['class' => 'form-control', 'readonly', 'required']); !!} -->
						</div>
					</div>
				</div>
				<div class="col-md-4 hide">
		          <div class="form-group">
		            <div class="multi-input">
		              {!! Form::label('pay_term_number', __('contact.pay_term') . ':') !!} @show_tooltip(__('tooltip.pay_term'))
		              <br/>
		              {!! Form::number('pay_term_number', null, ['class' => 'form-control width-40 pull-left', 'placeholder' => __('contact.pay_term')]); !!}

		              {!! Form::select('pay_term_type', 
		              	['months' => __('lang_v1.months'), 
		              		'days' => __('lang_v1.days')], 
		              		null, 
		              	['class' => 'form-control width-60 pull-left','placeholder' => __('messages.please_select')]); !!}
		            </div>
		          </div>
		        </div>

				@if(!empty($commission_agent))
				<div class="col-sm-4">
					<div class="form-group">
					{!! Form::label('commission_agent', __('lang_v1.commission_agent') . ':') !!}
					{!! Form::select('commission_agent', 
								$commission_agent, null, ['class' => 'form-control select2']); !!}
					</div>
				</div>
				@endif
				
				@if(!empty($status))
					<input type="hidden" name="status" id="status" value="{{$status}}">
				@else
					<div class="@if(!empty($commission_agent)) col-sm-4 @else col-sm-4 @endif" style="display: none;">
						<div class="form-group">
							{!! Form::label('status', __('sale.status') . ':*') !!}
							{!! Form::select('status', $statuses, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
						</div>
					</div>
				@endif
				@if($sale_type != 'sales_order')
					<div class="col-sm-4" style="display: none;">
						<div class="form-group">
							{!! Form::label('invoice_scheme_id', __('invoice.invoice_scheme') . ':') !!}
							{!! Form::select('invoice_scheme_id', $invoice_schemes, $default_invoice_schemes->id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
						</div>
					</div>
				@endif
				
					<div class="col-sm-4 hide">
						<div class="form-group">
							{!! Form::label('invoice_no', $sale_type == 'sales_order' ? __('restaurant.order_no') : __('Transporter Name') . ':') !!}
							{!! Form::text('invoice_no', null, ['class' => 'form-control', 'placeholder' => $sale_type == 'sales_order' ? __('restaurant.order_no') : __('Transporter Name')]); !!}
							<p  style="display: none;" class="help-block">@lang('lang_v1.keep_blank_to_autogenerate')</p>
						</div>
					</div>
					

				




				
					@can('edit_invoice_number')
					<div class="col-sm-4" style="display: none;">
						<div class="form-group">
							{!! Form::label('invoice_no', $sale_type == 'sales_order' ? __('restaurant.order_no') : __('sale.invoice_no') . ':') !!}
							{!! Form::text('invoice_no', null, ['class' => 'form-control', 'placeholder' => $sale_type == 'sales_order' ? __('restaurant.order_no') : __('sale.invoice_no')]); !!}
							<p class="help-block">@lang('lang_v1.keep_blank_to_autogenerate')</p>
						</div>
					</div>
					@endcan
				
				@php
			        $custom_field_1_label = !empty($custom_labels['sell']['custom_field_1']) ? $custom_labels['sell']['custom_field_1'] : '';

			        $is_custom_field_1_required = !empty($custom_labels['sell']['is_custom_field_1_required']) && $custom_labels['sell']['is_custom_field_1_required'] == 1 ? true : false;

			        $custom_field_2_label = !empty($custom_labels['sell']['custom_field_2']) ? $custom_labels['sell']['custom_field_2'] : '';

			        $is_custom_field_2_required = !empty($custom_labels['sell']['is_custom_field_2_required']) && $custom_labels['sell']['is_custom_field_2_required'] == 1 ? true : false;

			        $custom_field_3_label = !empty($custom_labels['sell']['custom_field_3']) ? $custom_labels['sell']['custom_field_3'] : '';

			        $is_custom_field_3_required = !empty($custom_labels['sell']['is_custom_field_3_required']) && $custom_labels['sell']['is_custom_field_3_required'] == 1 ? true : false;

			        $custom_field_4_label = !empty($custom_labels['sell']['custom_field_4']) ? $custom_labels['sell']['custom_field_4'] : '';

			        $is_custom_field_4_required = !empty($custom_labels['sell']['is_custom_field_4_required']) && $custom_labels['sell']['is_custom_field_4_required'] == 1 ? true : false;
		        @endphp
		        @if(!empty($custom_field_1_label))
		        	@php
		        		$label_1 = $custom_field_1_label . ':';
		        		if($is_custom_field_1_required) {
		        			$label_1 .= '*';
		        		}
		        	@endphp

		        	<div class="col-md-4">
				        <div class="form-group">
				            {!! Form::label('custom_field_1', $label_1 ) !!}
				            {!! Form::text('custom_field_1', null, ['class' => 'form-control','placeholder' => $custom_field_1_label, 'required' => $is_custom_field_1_required]); !!}
				        </div>
				    </div>
		        @endif
		        @if(!empty($custom_field_2_label))
		        	@php
		        		$label_2 = $custom_field_2_label . ':';
		        		if($is_custom_field_2_required) {
		        			$label_2 .= '*';
		        		}
		        	@endphp

		        	<div class="col-md-4">
				        <div class="form-group">
				            {!! Form::label('custom_field_2', $label_2 ) !!}
				            {!! Form::text('custom_field_2', null, ['class' => 'form-control','placeholder' => $custom_field_2_label, 'required' => $is_custom_field_2_required]); !!}
				        </div>
				    </div>
		        @endif
		        @if(!empty($custom_field_3_label))
		        	@php
		        		$label_3 = $custom_field_3_label . ':';
		        		if($is_custom_field_3_required) {
		        			$label_3 .= '*';
		        		}
		        	@endphp

		        	<div class="col-md-4">
				        <div class="form-group">
				            {!! Form::label('custom_field_3', $label_3 ) !!}
				            {!! Form::text('custom_field_3', null, ['class' => 'form-control','placeholder' => $custom_field_3_label, 'required' => $is_custom_field_3_required]); !!}
				        </div>
				    </div>
		        @endif
		        @if(!empty($custom_field_4_label))
		        	@php
		        		$label_4 = $custom_field_4_label . ':';
		        		if($is_custom_field_4_required) {
		        			$label_4 .= '*';
		        		}
		        	@endphp

		        	<div class="col-md-4">
				        <div class="form-group">
				            {!! Form::label('custom_field_4', $label_4 ) !!}
				            {!! Form::text('custom_field_4', null, ['class' => 'form-control','placeholder' => $custom_field_4_label, 'required' => $is_custom_field_4_required]); !!}
				        </div>
				    </div>
		        @endif
		        
		        
		        
		        

		        
				
		        
		        
		        
				


		        @if((!empty($pos_settings['enable_sales_order']) && $sale_type != 'sales_order') || $is_order_request_enabled)
					<div class="col-sm-4" style="display: none;">
						<div class="form-group">
							{!! Form::label('sales_order_ids', __('lang_v1.sales_order').':') !!}
							{!! Form::select('sales_order_ids[]', [], null, ['class' => 'form-control select2', 'multiple', 'id' => 'sales_order_ids']); !!}
						</div>
					</div>
					
				@endif
				<!-- Call restaurant module if defined -->
		        @if(in_array('tables' ,$enabled_modules) || in_array('service_staff' ,$enabled_modules))
		        	<span id="restaurant_module_span">
		        	</span>
		        @endif
			@endcomponent

			@component('components.widget', ['class' => 'box-solid'])
				{{-- <div class="col-sm-10 col-sm-offset-1">
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-btn">
								<button type="button" class="btn btn-default bg-white btn-flat" data-toggle="modal" data-target="#configure_search_modal" title="{{__('lang_v1.configure_product_search')}}"><i class="fas fa-search-plus"></i></button>
							</div>
							{!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'),
							'disabled' => is_null($default_location)? true : false,
							'autofocus' => is_null($default_location)? false : true,
							]); !!}
							<input type="hidden" value="1" name="forsaleorder" id="forsaleorder">

							<span class="input-group-btn">
								<button type="button" class="btn btn-default bg-white btn-flat pos_add_quick_product" data-href="{{action('ProductController@quickAdd')}}" data-container=".quick_add_product_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
							</span>
						</div>
					</div>
				</div> --}}

				{{-- <div class="row col-sm-12 pos_product_div" style="min-height: 0"> --}}

					<div class="row">
						<div class="col-sm-10">
						</div>
						<div class="col-sm-2" style="margin-bottom: 10px;">
							<button type="button" class="btn btn-primary pos_add_quick_product mb-2" data-href="{{action('ProductController@quickAdd')}}" data-container=".quick_add_product_modal"><i class="fa fa-plus"></i>@lang( 'product.add_new_product' )</button>
						</div>
					</div>

					<input type="hidden" name="sell_price_tax" id="sell_price_tax" value="{{$business_details->sell_price_tax}}">

					<!-- Keeps count of product rows -->
					{{-- <input type="hidden" id="product_row_count"
						value="0"> --}}
					@php
						$hide_tax = '';
						if( session()->get('business.enable_inline_tax') == 0){
							$hide_tax = 'hide';
						}
					@endphp

<style>
	/* .pos_product_div{
		overflow-y: unset !important;
	} */
	#pos_table thead tr th:not(:first-child){
		width: 10%;
		text-align: center;
	}
</style>


					<div class="table-responsive" style="overflow: scroll;">
						<table class="table table-condensed table-bordered table-th-green text-center table-striped" id="pos_table" style="width: 160%; max-width: 160%;">
						<thead>
							<tr>
								<th class="text-center"><i class="fas fa-times" aria-hidden="true"></i></th>
							<th class="text-center" style="width: 4%">	
									@lang('#')
								</th>
							<th class="text-center hide">	
									@lang('Store')
								</th>
							<th class="text-center" style="width: 8%">	
									SKU
								</th>
								<th class="text-center" style="width: 20%">	
									Product
								</th>
								<th class="text-center" style="width: 6%">
									Brand
								</th>
								<th class="text-center" style="width: 8%;">	
									Product Description
								</th>
								<th class="text-center" style="width: 6%;">	
									@lang('UOM')
								</th>
								<th class="text-center" style="width: 7%;">
									Qty
								</th>
								@if(!empty($pos_settings['inline_service_staff']))
									<th class="text-center">
										@lang('restaurant.service_staff')
									</th>
								@endif
								<th @can('edit_product_price_from_sale_screen') hide @endcan style="width: 7%;">
									Rate
								</th>
								<th class="hide" @can('edit_product_discount_from_sale_screen') hide @endcan>
									@lang('receipt.discount')
								</th>
								<th class="text-center {{$hide_tax}}" style="width: 7%;">
									Amount
								</th>
								<th class="text-center {{$hide_tax}}" style="width: 7%;">
									Sales Tax
								</th>
								<th class="text-center" style="width: 7%">
            						Further tax
            					</th>
								<th class="text-center {{$hide_tax}}" style="width: 7%;">
									Tax Amount
								</th>
            					<th class="text-center" style="width: 2%">
            						Commission
            					</th>
								
								@if(!empty($common_settings['enable_product_warranty']))
									<th>@lang('lang_v1.warranty')</th>
								@endif
								<th class="text-center" style="width: 10%;">
									Net Amount after tax
								</th>
							
							</tr>
						</thead>
						<tbody id="tbody">
							
							<tr >
								<td class="text-center v-center">
									
									<button class="btn btn-danger remove" type="button" onclick="remove_row(this)" style="padding: 0px 5px 2px 5px;">
										<i class="fa fa-trash" aria-hidden="true"></i>
									</button>
								</td>
								<td>
									<span class='sr_number'>1</span>
								</td>
								<td class="hide">
								<div class="col-sm-4 ">
												<div class="form-group">
													{!! Form::select('product[0][store]', $store,null, ['class' => 'form-control select2','id'=>"store",'required','style'=>"width: 146px;"]); !!}
												</div>
											</div>
								</td>
								<td>
									{{-- {!! $product->sub_sku !!}	 --}}
									<input class="form-control product_code" readonly="" id="item_code" name="product[0][item_code]" type="text">
								</td>
								<td>
									<select name="product[0][product_id]" class="form-control select2" id="prd_select" required onchange="get_product_code(this)" style="width: 200px;">
										<option value="">Please Select</option>
										@foreach ($product as $p)
											<option value="{{$p->id}}">{{$p->name}}</option>
										@endforeach
									</select>
									<input type="hidden" name="gross_weight" class="gross__weight">
									<input type="hidden" name="net_weight" class="net__weight">
										
										<input type="hidden" class="product_type" name="product[0][product_type]" value="0">
								
										@php
											$hide_tax = 'hide';
											if(session()->get('business.enable_inline_tax') == 1){
												$hide_tax = '';
											}
											if(!empty($so_line)) {
												$tax_id = $so_line->tax_id;
												$item_tax = $so_line->item_tax;
											}
								
											if($hide_tax == 'hide'){
												$tax_id = null;
												$unit_price_inc_tax = $product->default_sell_price;
											}
								
											$discount_type = !empty($product->line_discount_type) ? $product->line_discount_type : 'fixed';
											$discount_amount = !empty($product->line_discount_amount) ? $product->line_discount_amount : 0;
											
											if(!empty($discount)) {
												$discount_type = $discount->discount_type;
												$discount_amount = $discount->discount_amount;
											}
								
											if(!empty($so_line)) {
												$discount_type = $so_line->line_discount_type;
												$discount_amount = $so_line->line_discount_amount;
											}
								
											  $sell_line_note = '';
											  if(!empty($product->sell_line_note)){
												  $sell_line_note = $product->sell_line_note;
											  }
										  @endphp
								
										@if(!empty($discount))
											{!! Form::hidden("product[0][discount_id]", $discount->id); !!}
										@endif
								
										@php
											// $max_quantity = $product->qty_available;
											// $formatted_max_quantity = $product->formatted_qty_available;
								
											if(!empty($action) && $action == 'edit') {
												if(!empty($so_line)) {
													$qty_available = $so_line->quantity - $so_line->so_quantity_invoiced + $product->quantity_ordered;
													$max_quantity = $qty_available;
													$formatted_max_quantity = number_format($qty_available, config('constants.currency_precision', 2), session('currency')['decimal_separator'], session('currency')['thousand_separator']);
												}
											} else {
												if(!empty($so_line) && $so_line->qty_available <= $max_quantity) {
													$max_quantity = $so_line->qty_available;
													$formatted_max_quantity = $so_line->formatted_qty_available;
												}
											}
											
								
											// $max_qty_rule = $max_quantity;
											// $max_qty_msg = __('validation.custom-messages.quantity_not_available', ['qty'=> $formatted_max_quantity, 'unit' => $product->unit  ]);
										@endphp
								
										@if( session()->get('business.enable_lot_number') == 1 || session()->get('business.enable_product_expiry') == 1)
										@php
											$lot_enabled = session()->get('business.enable_lot_number');
											$exp_enabled = session()->get('business.enable_product_expiry');
											$lot_no_line_id = '';
											if(!empty($product->lot_no_line_id)){
												$lot_no_line_id = $product->lot_no_line_id;
											}
										@endphp
										@if(!empty($product->lot_numbers) && empty($is_sales_order))
											<select class="form-control lot_number input-sm" name="product[0][lot_no_line_id]" required @if(!empty($product->transaction_sell_lines_id)) disabled @endif>
												<option value="">@lang('lang_v1.lot_n_expiry')</option>
												@foreach($product->lot_numbers as $lot_number)
													@php
														$selected = "";
														if($lot_number->purchase_line_id == $lot_no_line_id){
															$selected = "selected";
								
															$max_qty_rule = $lot_number->qty_available;
															$max_qty_msg = __('lang_v1.quantity_error_msg_in_lot', ['qty'=> $lot_number->qty_formated, 'unit' => $product->unit  ]);
														}
								
														$expiry_text = '';
														if($exp_enabled == 1 && !empty($lot_number->exp_date)){
															if( \Carbon::now()->gt(\Carbon::createFromFormat('Y-m-d', $lot_number->exp_date)) ){
																$expiry_text = '(' . __('report.expired') . ')';
															}
														}
								
														//preselected lot number if product searched by lot number
														if(!empty($purchase_line_id) && $purchase_line_id == $lot_number->purchase_line_id) {
															$selected = "selected";
								
															$max_qty_rule = $lot_number->qty_available;
															$max_qty_msg = __('lang_v1.quantity_error_msg_in_lot', ['qty'=> $lot_number->qty_formated, 'unit' => $product->unit  ]);
														}
													@endphp
													<option value="{{$lot_number->purchase_line_id}}" data-qty_available="{{$lot_number->qty_available}}" data-msg-max="@lang('lang_v1.quantity_error_msg_in_lot', ['qty'=> $lot_number->qty_formated, 'unit' => $product->unit  ])" {{$selected}}>@if(!empty($lot_number->lot_number) && $lot_enabled == 1){{$lot_number->lot_number}} @endif @if($lot_enabled == 1 && $exp_enabled == 1) - @endif @if($exp_enabled == 1 && !empty($lot_number->exp_date)) @lang('product.exp_date'): {{@format_date($lot_number->exp_date)}} @endif {{$expiry_text}}</option>
												@endforeach
											</select>
										@endif
									@endif
									
									</td>
									<td>
										{!! Form::select('product[0][brand_id]', ['' => 'Select'] + $brands->pluck('name','id')->all(), null, ['class' => 'form-control select2','id' =>'brand_id']) !!}
									</td>
									<td>
									<textarea class="form-control" name="product[0][sell_line_note]" rows="2"  >{{$sell_line_note}}</textarea>
								
													</td>
													<td>
														<input type="text" class="form-control uom" readonly="" style="width: 92px">
													{{-- @if(count($sub_units) > 0) --}}
								
											{{-- <select name="product[0][sub_unit_id]" class="form-control input-sm sub_unit">
												@foreach($sub_units as $key => $value)
													<option value="{{$key}}" data-multiplier="{{$value['multiplier']}}" data-unit_name="{{$value['name']}}" data-allow_decimal="{{$value['allow_decimal']}}" @if(!empty($product->sub_unit_id) && $product->sub_unit_id == $key) selected @endif>
														{{$value['name']}}
													</option>
												@endforeach
										   </select> --}}
										{{-- @else
											{{$product->unit}}
										@endif --}}
													</td>
								
									<td>
										{{-- If edit then transaction sell lines will be present --}}
										{{-- @if(!empty($product->transaction_sell_lines_id))
											<input type="hidden" name="product[0][transaction_sell_lines_id]" class="form-control" value="{{$product->transaction_sell_lines_id}}">
										@endif --}}
								
										{{-- <input type="hidden" name="product[0][product_id]" class="form-control product_id" value=""> --}}
								
										<input type="hidden" value="0" 
											name="product[0][variation_id]" class="row_variation_id">
								
										<input type="hidden" value="0" 
											name="product[0][enable_stock]">
										
										{{-- @if(empty($product->quantity_ordered))
											@php
												$product->quantity_ordered = 1;
											@endphp
										@endif --}}
								
										{{-- @php
											$multiplier = 1;
											$allow_decimal = true;
											// if($product->unit_allow_decimal != 1) {
											// 	$allow_decimal = false;
											// }
										@endphp --}}
										{{-- @foreach($sub_units as $key => $value)
											@if(!empty($product->sub_unit_id) && $product->sub_unit_id == $key)
												@php
													$multiplier = $value['multiplier'];
													$max_qty_rule = $max_qty_rule / $multiplier;
													$unit_name = $value['name'];
													$max_qty_msg = __('validation.custom-messages.quantity_not_available', ['qty'=> $max_qty_rule, 'unit' => $unit_name  ]);
								
													if(!empty($product->lot_no_line_id)){
														$max_qty_msg = __('lang_v1.quantity_error_msg_in_lot', ['qty'=> $max_qty_rule, 'unit' => $unit_name  ]);
													}
								
													if($value['allow_decimal']) {
														$allow_decimal = true;
													}
												@endphp
											@endif
										@endforeach --}}
										<div class="input-group input-number">

										<input type="text" data-min="1" 
											class="form-control pos_quantity input_number mousetrap input_quantity" 
											value="0.00" name="product[0][quantity]" data-allow-overselling="@if(empty($pos_settings['allow_overselling'])){{'false'}}@else{{'true'}}@endif" 
											 required onkeyup="calculate_unitprice(this)">
											<input type="hidden" name="base_unit" class="base_unit">
											
                                            <input type="hidden" name="transporter_rate" class="transporter_rate" />
                                            <input type="hidden" name="contractor_rate" class="contractor_rate" />
									</div>
										
										{{-- <input type="hidden" name="product[0][product_unit_id]" value="{{$product->unit_id}}"> --}}
										
								
										{{-- <input type="hidden" class="base_unit_multiplier" name="product[0][base_unit_multiplier]" value="{{$multiplier}}"> --}}
								
										{{-- <input type="hidden" class="hidden_base_unit_sell_price" value="{{$product->default_sell_price / $multiplier}}"> --}}
										
										{{-- Hidden fields for combo products --}}
										{{-- @if($product->product_type == 'combo'&& !empty($product->combo_products))
								
											@foreach($product->combo_products as $k => $combo_product)
								
												@if(isset($action) && $action == 'edit')
													@php
														$combo_product['qty_required'] = $combo_product['quantity'] / $product->quantity_ordered;
								
														$qty_total = $combo_product['quantity'];
													@endphp
												@else
													@php
														$qty_total = $combo_product['qty_required'];
													@endphp
												@endif
								
												<input type="hidden" 
													name="product[0][combo][{{$k}}][product_id]"
													value="{{$combo_product['product_id']}}">
								
													<input type="hidden" 
													name="product[0][combo][{{$k}}][variation_id]"
													value="{{$combo_product['variation_id']}}">
								
													<input type="hidden"
													class="combo_product_qty" 
													name="product[0][combo][{{$k}}][quantity]"
													data-unit_quantity="{{$combo_product['qty_required']}}"
													value="{{$qty_total}}">
								
													@if(isset($action) && $action == 'edit')
														<input type="hidden" 
															name="product[0][combo][{{$k}}][transaction_sell_lines_id]"
															value="{{$combo_product['id']}}">
													@endif
								
											@endforeach
										@endif --}}
									</td>
									{{-- @if(!empty($is_direct_sell))
										@if(!empty($pos_settings['inline_service_staff']))
											<td>
												<div class="form-group">
													<div class="input-group">
														{!! Form::select("products[" . 0 . "][res_service_staff_id]", $waiters, !empty($product->res_service_staff_id) ? $product->res_service_staff_id : null, ['class' => 'form-control select2 order_line_service_staff', 'placeholder' => __('restaurant.select_service_staff'), 'required' => (!empty($pos_settings['is_service_staff_required']) && $pos_settings['is_service_staff_required'] == 1) ? true : false ]); !!}
													</div>
												</div>
											</td>
										@endif
										@php
											$pos_unit_price = !empty($product->unit_price_before_discount) ? $product->unit_price_before_discount : $product->default_sell_price;
								
											if(!empty($so_line)) {
												$pos_unit_price = $so_line->unit_price_before_discount;
											}
										@endphp --}}
										<td>
											<input type="text" name="product[0][unit_price]" class="form-control pos_unit_price input_number mousetrap" required value="0.00"  onkeyup="calculate_unitprice(this)">
										</td>
										 <td class="hide">
											{!! Form::text("product[0][line_discount_amount]", @num_format($discount_amount), ['class' => 'form-control input_number row_discount_amount','onkeyup' => 'calculate_discount(this)']); !!}<br>
											{!! Form::select("product[0][line_discount_type]", ['fixed' => __('lang_v1.fixed')], $discount_type , ['class' => 'form-control row_discount_type']); !!}
											@if(!empty($discount))
												<p class="help-block">{!! __('lang_v1.applied_discount_text', ['discount_name' => $discount->name, 'starts_at' => $discount->formated_starts_at, 'ends_at' => $discount->formated_ends_at]) !!}</p>
											@endif
										</td>
										<td class="get_total">
											<input class="calculate_discount row_total_amount  form-control" readonly type="text">
										</td>
										<td class="text-center {{$hide_tax}}">
											
											{!! Form::hidden("product[0][item_tax]",null, ['class' => 'item_tax']); !!}
										
											{{-- {!! Form::select("product[0][tax_id]", null, null, ['placeholder' => 'Select', 'class' => 'form-control tax_id'], null); !!} --}}
											
											<select name="product[0][tax_id]" class="form-control select2 input-sm tax_idd" placeholder="'Please Select'" id="tax_id" onchange="calculate_unitprice(this)">
												<option value="0" data-ratee="0">@lang('lang_v1.none')</option>
												@foreach($tax_rate as $tax_ratee)
													<option value="{{ $tax_ratee->id }}" data-ratee="{{ $tax_ratee->amount }}">{{ $tax_ratee->name }}</option>
												@endforeach
											</select>
										</td>
								
									{{-- @else
										@if(!empty($warranties))
											{!! Form::select("product[0][warranty_id]", $warranties, $warranty_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control']); !!}
										@endif
								
										@if(!empty($pos_settings['inline_service_staff']))
											<td>
												<div class="form-group">
													<div class="input-group">
														{!! Form::select("products[" . 0 . "][res_service_staff_id]", $waiters, !empty($product->res_service_staff_id) ? $product->res_service_staff_id : null, ['class' => 'form-control select2 order_line_service_staff', 'placeholder' => __('restaurant.select_service_staff'), 'required' => (!empty($pos_settings['is_service_staff_required']) && $pos_settings['is_service_staff_required'] == 1) ? true : false ]); !!}
													</div>
												</div>
											</td>
										@endif
									@endif --}}
									
										
										<td class="text-center">
                    						<input type="hidden" class="form-control further_tax_hidden" name="products[0][item_further_tax]" />
                    						<select name="products[0][further_taax_id]" class="form-control select2 input-sm further_tax" id="further_tax"  onchange="calculate_unitprice(this)" placeholder="Please Select">
                    							<option value="0" data-rate="0">NONE</option>
                    							@foreach($further_tax as $further)
                    							    <option value="{{ $further->id }}" data-rate="{{ $further->amount }}">{{ $further->name }}</option>
                    							@endforeach
                    						</select>
                    					</td>
										<td class="">
											<input type="text" name="product[0][unit_price_inc_tax]" class="form-control pos_unit_price_inc_tax input_number" value="0.00" readonly>
										</td>				
					<td>
                        <input type="number" name="products[0][salesman_commission_rate]" class="form-control salesman_commission_rate" onkeyup="calculate_unitprice(this)"/>
					</td>
                    					
                    					
									
									@if(!empty($common_settings['enable_product_warranty']) && !empty($is_direct_sell))
										<td>
											{!! Form::select("product[0][warranty_id]", $warranties, $warranty_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control']); !!}
										</td>
									@endif
									<td class="text-center">
										@php
											$subtotal_type = !empty($pos_settings['is_pos_subtotal_editable']) ? 'text' : 'hidden';
								
										@endphp
										<input type="text"  class="form-control pos_line_total @if(!empty($pos_settings['is_pos_subtotal_editable'])) input_number @endif" value="0.00">
										{{-- <span class="display_currency pos_line_total_text @if(!empty($pos_settings['is_pos_subtotal_editable'])) hide @endif" data-currency_symbol="true">{{$product->quantity_ordered*$unit_price_inc_tax}}</span> --}}
									</td>
								
					</tr>

								{{-- 2nd Row --}}	

								<tr >
									<td class="text-center v-center">
									
										<button class="btn btn-danger remove" type="button" onclick="remove_row(this)" style="padding: 0px 5px 2px 5px;">
											<i class="fa fa-trash" aria-hidden="true"></i>
										</button>
									</td>
									<td>
										<span class='sr_number'>2</span>
									</td>
									<td class="hide">
									<div class="col-sm-4">
													<div class="form-group">
														{!! Form::select('product[1][store]', $store,null, ['class' => 'form-control  select2','id'=>"store",'style'=>"width: 146px;"]); !!}
													</div>
												</div>
									</td>
									<td>
										{{-- {!! $product->sub_sku !!}	 --}}
										<input class="form-control product_code" readonly="" id="item_code" name="product[1][item_code]" type="text">
									</td>
									<td>
										<select name="product[1][product_id]" class="form-control select2" id="prd_select" onchange="get_product_code(this)">
											<option value="">Please Select</option>
											@foreach ($product as $p)
												<option value="{{$p->id}}">{{$p->name}}</option>
											@endforeach
										</select>
										<input type="hidden" name="gross_weight" class="gross__weight">
									<input type="hidden" name="net_weight" class="net__weight">
											{{-- @if(!empty($so_line))
												<input type="hidden" 
												name="product[1][so_line_id]" 
												value="{{$so_line->id}}">
											@endif
											@php
												// $product_name = $product->product_name  ;
											@endphp --}}
									
											{{-- @if( ($edit_price || $edit_discount) && empty($is_direct_sell) )
											<div title="@lang('lang_v1.pos_edit_product_price_help')">
											<span class="text-link text-info cursor-pointer" data-toggle="modal" data-target="#row_edit_product_price_modal_0">
												{!! $product_name !!}
												&nbsp;<i class="fa fa-info-circle"></i>
											</span>
											</div>
											@else
												{!! $product_name !!}
											@endif --}}
											{{-- <input type="hidden" class="enable_sr_no" value="{{$product->enable_sr_no}}"> --}}
											
											<input type="hidden" class="product_type" name="product[1][product_type]" value="0">
									
											@php
												$hide_tax = 'hide';
												if(session()->get('business.enable_inline_tax') == 1){
													$hide_tax = '';
												}
												
												// $tax_id = $product->tax_id;
												// $item_tax = !empty($product->item_tax) ? $product->item_tax : 0;
												// $unit_price_inc_tax = $product->sell_price_inc_tax;
									
												if(!empty($so_line)) {
													$tax_id = $so_line->tax_id;
													$item_tax = $so_line->item_tax;
												}
									
												if($hide_tax == 'hide'){
													$tax_id = null;
													$unit_price_inc_tax = $product->default_sell_price;
												}
									
												$discount_type = !empty($product->line_discount_type) ? $product->line_discount_type : 'fixed';
												$discount_amount = !empty($product->line_discount_amount) ? $product->line_discount_amount : 0;
												
												if(!empty($discount)) {
													$discount_type = $discount->discount_type;
													$discount_amount = $discount->discount_amount;
												}
									
												if(!empty($so_line)) {
													$discount_type = $so_line->line_discount_type;
													$discount_amount = $so_line->line_discount_amount;
												}
									
												  $sell_line_note = '';
												  if(!empty($product->sell_line_note)){
													  $sell_line_note = $product->sell_line_note;
												  }
											  @endphp
									
											@if(!empty($discount))
												{!! Form::hidden("product[1][discount_id]", $discount->id); !!}
											@endif
									
											@php
												// $warranty_id = !empty($action) && $action == 'edit' && !empty($product->warranties->first())  ? $product->warranties->first()->id : $product->warranty_id;
											@endphp
									
											@if(empty($is_direct_sell))
											<div class="modal fade row_edit_product_price_model" id="row_edit_product_price_modal_0" tabindex="-1" role="dialog">
												{{-- @include('sale_pos.partials.row_edit_product_price_modal') --}}
											</div>
											@endif
									
											<!-- Description modal end -->
											@if(in_array('modifiers' , $enabled_modules))
												<div class="modifiers_html">
													@if(!empty($product->product_ms))
														{{-- @include('restaurant.product_modifier_set.modifier_for_product', array('edit_modifiers' => true, 'row_count' => $loop->index, 'product_ms' => $product->product_ms ) ) --}}
													@endif
												</div>
											@endif
									
											@php
												// $max_quantity = $product->qty_available;
												// $formatted_max_quantity = $product->formatted_qty_available;
									
												if(!empty($action) && $action == 'edit') {
													if(!empty($so_line)) {
														$qty_available = $so_line->quantity - $so_line->so_quantity_invoiced + $product->quantity_ordered;
														$max_quantity = $qty_available;
														$formatted_max_quantity = number_format($qty_available, config('constants.currency_precision', 2), session('currency')['decimal_separator'], session('currency')['thousand_separator']);
													}
												} else {
													if(!empty($so_line) && $so_line->qty_available <= $max_quantity) {
														$max_quantity = $so_line->qty_available;
														$formatted_max_quantity = $so_line->formatted_qty_available;
													}
												}
												
									
												// $max_qty_rule = $max_quantity;
												// $max_qty_msg = __('validation.custom-messages.quantity_not_available', ['qty'=> $formatted_max_quantity, 'unit' => $product->unit  ]);
											@endphp
									
											@if( session()->get('business.enable_lot_number') == 1 || session()->get('business.enable_product_expiry') == 1)
											@php
												$lot_enabled = session()->get('business.enable_lot_number');
												$exp_enabled = session()->get('business.enable_product_expiry');
												$lot_no_line_id = '';
												if(!empty($product->lot_no_line_id)){
													$lot_no_line_id = $product->lot_no_line_id;
												}
											@endphp
											@if(!empty($product->lot_numbers) && empty($is_sales_order))
												<select class="form-control lot_number input-sm" name="product[1][lot_no_line_id]" @if(!empty($product->transaction_sell_lines_id)) disabled @endif>
													<option value="">@lang('lang_v1.lot_n_expiry')</option>
													@foreach($product->lot_numbers as $lot_number)
														@php
															$selected = "";
															if($lot_number->purchase_line_id == $lot_no_line_id){
																$selected = "selected";
									
																$max_qty_rule = $lot_number->qty_available;
																$max_qty_msg = __('lang_v1.quantity_error_msg_in_lot', ['qty'=> $lot_number->qty_formated, 'unit' => $product->unit  ]);
															}
									
															$expiry_text = '';
															if($exp_enabled == 1 && !empty($lot_number->exp_date)){
																if( \Carbon::now()->gt(\Carbon::createFromFormat('Y-m-d', $lot_number->exp_date)) ){
																	$expiry_text = '(' . __('report.expired') . ')';
																}
															}
									
															//preselected lot number if product searched by lot number
															if(!empty($purchase_line_id) && $purchase_line_id == $lot_number->purchase_line_id) {
																$selected = "selected";
									
																$max_qty_rule = $lot_number->qty_available;
																$max_qty_msg = __('lang_v1.quantity_error_msg_in_lot', ['qty'=> $lot_number->qty_formated, 'unit' => $product->unit  ]);
															}
														@endphp
														<option value="{{$lot_number->purchase_line_id}}" data-qty_available="{{$lot_number->qty_available}}" data-msg-max="@lang('lang_v1.quantity_error_msg_in_lot', ['qty'=> $lot_number->qty_formated, 'unit' => $product->unit  ])" {{$selected}}>@if(!empty($lot_number->lot_number) && $lot_enabled == 1){{$lot_number->lot_number}} @endif @if($lot_enabled == 1 && $exp_enabled == 1) - @endif @if($exp_enabled == 1 && !empty($lot_number->exp_date)) @lang('product.exp_date'): {{@format_date($lot_number->exp_date)}} @endif {{$expiry_text}}</option>
													@endforeach
												</select>
											@endif
										@endif
										
										</td>
										<td>
											{!! Form::select('product[1][brand_id]', ['' => 'Select'] + $brands->pluck('name','id')->all(), null, ['class' => 'form-control select2','id' =>'brand_id']) !!}
										</td>
										<td>
										<textarea class="form-control" name="product[1][sell_line_note]" rows="2">{{$sell_line_note}}</textarea>
									
														</td>
														<td>
															<input type="text" class="form-control uom" readonly="" style="width: 92px">
														{{-- @if(count($sub_units) > 0) --}}
									
												{{-- <select name="product[1][sub_unit_id]" class="form-control input-sm sub_unit">
													@foreach($sub_units as $key => $value)
														<option value="{{$key}}" data-multiplier="{{$value['multiplier']}}" data-unit_name="{{$value['name']}}" data-allow_decimal="{{$value['allow_decimal']}}" @if(!empty($product->sub_unit_id) && $product->sub_unit_id == $key) selected @endif>
															{{$value['name']}}
														</option>
													@endforeach
											   </select> --}}
											{{-- @else
												{{$product->unit}}
											@endif --}}
														</td>
									
										<td>
											{{-- If edit then transaction sell lines will be present --}}
											{{-- @if(!empty($product->transaction_sell_lines_id))
												<input type="hidden" name="product[1][transaction_sell_lines_id]" class="form-control" value="{{$product->transaction_sell_lines_id}}">
											@endif --}}
									
											{{-- <input type="hidden" name="product[1][product_id]" class="form-control product_id" value=""> --}}
									
											<input type="hidden" value="0" 
												name="product[1][variation_id]" class="row_variation_id">
									
											<input type="hidden" value="0" 
												name="product[1][enable_stock]">
											
											{{-- @if(empty($product->quantity_ordered))
												@php
													$product->quantity_ordered = 1;
												@endphp
											@endif --}}
									
											{{-- @php
												$multiplier = 1;
												$allow_decimal = true;
												// if($product->unit_allow_decimal != 1) {
												// 	$allow_decimal = false;
												// }
											@endphp --}}
											{{-- @foreach($sub_units as $key => $value)
												@if(!empty($product->sub_unit_id) && $product->sub_unit_id == $key)
													@php
														$multiplier = $value['multiplier'];
														$max_qty_rule = $max_qty_rule / $multiplier;
														$unit_name = $value['name'];
														$max_qty_msg = __('validation.custom-messages.quantity_not_available', ['qty'=> $max_qty_rule, 'unit' => $unit_name  ]);
									
														if(!empty($product->lot_no_line_id)){
															$max_qty_msg = __('lang_v1.quantity_error_msg_in_lot', ['qty'=> $max_qty_rule, 'unit' => $unit_name  ]);
														}
									
														if($value['allow_decimal']) {
															$allow_decimal = true;
														}
													@endphp
												@endif
											@endforeach --}}
											<div class="input-group input-number">
											
												<input type="text" data-min="1" 
												class="form-control pos_quantity input_number mousetrap input_quantity" 
												value="0.00" name="product[1][quantity]" data-allow-overselling="@if(empty($pos_settings['allow_overselling'])){{'false'}}@else{{'true'}}@endif" 
												onkeyup="calculate_unitprice(this)"
												{{-- @if($allow_decimal) 
													data-decimal=1 
												@else 
													data-decimal=0 
													data-rule-abs_digit="true" 
													data-msg-abs_digit="@lang('lang_v1.decimal_value_not_allowed')" 
												@endif --}}
												{{-- data-rule-required="true" 
												data-msg-required="@lang('validation.custom-messages.this_field_is_required')"  --}}
												{{-- @if($product->enable_stock && empty($pos_settings['allow_overselling']) && empty($is_sales_order) )
													data-rule-max-value="{{$max_qty_rule}}" data-qty_available="{{$product->qty_available}}" data-msg-max-value="{{$max_qty_msg}}" 
													data-msg_max_default="@lang('validation.custom-messages.quantity_not_available', ['qty'=> $product->formatted_qty_available, 'unit' => $product->unit  ])" 
												@endif  --}}
											>
												<input type="hidden" name="base_unit" class="base_unit">
                        <input type="hidden" name="transporter_rate" class="transporter_rate" />
                        <input type="hidden" name="contractor_rate" class="contractor_rate" />
										</div>
											
											{{-- <input type="hidden" name="product[1][product_unit_id]" value="{{$product->unit_id}}"> --}}
											
									
											{{-- <input type="hidden" class="base_unit_multiplier" name="product[1][base_unit_multiplier]" value="{{$multiplier}}"> --}}
									
											{{-- <input type="hidden" class="hidden_base_unit_sell_price" value="{{$product->default_sell_price / $multiplier}}"> --}}
											
											{{-- Hidden fields for combo products --}}
											{{-- @if($product->product_type == 'combo'&& !empty($product->combo_products))
									
												@foreach($product->combo_products as $k => $combo_product)
									
													@if(isset($action) && $action == 'edit')
														@php
															$combo_product['qty_required'] = $combo_product['quantity'] / $product->quantity_ordered;
									
															$qty_total = $combo_product['quantity'];
														@endphp
													@else
														@php
															$qty_total = $combo_product['qty_required'];
														@endphp
													@endif
									
													<input type="hidden" 
														name="product[1][combo][{{$k}}][product_id]"
														value="{{$combo_product['product_id']}}">
									
														<input type="hidden" 
														name="product[1][combo][{{$k}}][variation_id]"
														value="{{$combo_product['variation_id']}}">
									
														<input type="hidden"
														class="combo_product_qty" 
														name="product[1][combo][{{$k}}][quantity]"
														data-unit_quantity="{{$combo_product['qty_required']}}"
														value="{{$qty_total}}">
									
														@if(isset($action) && $action == 'edit')
															<input type="hidden" 
																name="product[1][combo][{{$k}}][transaction_sell_lines_id]"
																value="{{$combo_product['id']}}">
														@endif
									
												@endforeach
											@endif --}}
										</td>
										{{-- @if(!empty($is_direct_sell))
											@if(!empty($pos_settings['inline_service_staff']))
												<td>
													<div class="form-group">
														<div class="input-group">
															{!! Form::select("products[" . 0 . "][res_service_staff_id]", $waiters, !empty($product->res_service_staff_id) ? $product->res_service_staff_id : null, ['class' => 'form-control select2 order_line_service_staff', 'placeholder' => __('restaurant.select_service_staff'), 'required' => (!empty($pos_settings['is_service_staff_required']) && $pos_settings['is_service_staff_required'] == 1) ? true : false ]); !!}
														</div>
													</div>
												</td>
											@endif
											@php
												$pos_unit_price = !empty($product->unit_price_before_discount) ? $product->unit_price_before_discount : $product->default_sell_price;
									
												if(!empty($so_line)) {
													$pos_unit_price = $so_line->unit_price_before_discount;
												}
											@endphp --}}
											<td>
												<input type="text" name="product[1][unit_price]" class="form-control pos_unit_price input_number mousetrap" value="0.00"  onkeyup="calculate_unitprice(this)">
											</td>
											 <td class="hide">
												{!! Form::text("product[1][line_discount_amount]", @num_format($discount_amount), ['class' => 'form-control input_number row_discount_amount','onkeyup' => 'calculate_discount(this)']); !!}<br>
												{!! Form::select("product[1][line_discount_type]", ['fixed' => __('lang_v1.fixed')], $discount_type , ['class' => 'form-control row_discount_type']); !!}
												@if(!empty($discount))
													<p class="help-block">{!! __('lang_v1.applied_discount_text', ['discount_name' => $discount->name, 'starts_at' => $discount->formated_starts_at, 'ends_at' => $discount->formated_ends_at]) !!}</p>
												@endif
											</td>
											<td class="get_total">
												<input class="calculate_discount row_total_amount  form-control" readonly type="text">
											</td>
											<td class="text-center {{$hide_tax}}">
												
												{!! Form::hidden("product[1][item_tax]",null, ['class' => 'item_tax']); !!}
											
												{{-- {!! Form::select("product[1][tax_id]", null, null, ['placeholder' => 'Select', 'class' => 'form-control tax_id'], null); !!} --}}
												
												<select name="product[1][tax_id]" class="form-control select2 input-sm tax_idd" placeholder="'Please Select'" id="tax_id" onchange="calculate_unitprice(this)">
													<option value="0" data-ratee="0">@lang('lang_v1.none')</option>
													@foreach($tax_rate as $tax_ratee)
														<option value="{{ $tax_ratee->id }}" data-ratee="{{ $tax_ratee->amount }}">{{ $tax_ratee->name }}</option>
													@endforeach
												</select>
											</td>
									
										{{-- @else
											@if(!empty($warranties))
												{!! Form::select("product[1][warranty_id]", $warranties, $warranty_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control']); !!}
											@endif
									
											@if(!empty($pos_settings['inline_service_staff']))
												<td>
													<div class="form-group">
														<div class="input-group">
															{!! Form::select("products[" . 0 . "][res_service_staff_id]", $waiters, !empty($product->res_service_staff_id) ? $product->res_service_staff_id : null, ['class' => 'form-control select2 order_line_service_staff', 'placeholder' => __('restaurant.select_service_staff'), 'required' => (!empty($pos_settings['is_service_staff_required']) && $pos_settings['is_service_staff_required'] == 1) ? true : false ]); !!}
														</div>
													</div>
												</td>
											@endif
										@endif --}}
										
										
										<td class="text-center">
                    						<input type="hidden" class="form-control further_tax_hidden" name="products[1][item_further_tax]" />
                    						<select name="products[1][further_taax_id]" class="form-control select2 input-sm further_tax" id="further_tax"  onchange="calculate_unitprice(this)" placeholder="Please Select">
                    							<option value="0" data-rate="0">NONE</option>
                    							@foreach($further_tax as $further)
							    <option value="{{ $further->id }}" data-rate="{{ $further->amount }}">{{ $further->name }}</option>
							@endforeach
                    						</select>
                    					</td>
										<td class="{{$hide_tax}}">
											<input type="text" name="product[1][unit_price_inc_tax]" class="form-control pos_unit_price_inc_tax input_number" value="0.00" readonly>
										</td>
                    					
					<td>
                        <input type="number" name="products[1][salesman_commission_rate]" class="form-control salesman_commission_rate" onkeyup="calculate_unitprice(this)"/>
					</td>
                    					
										
										@if(!empty($common_settings['enable_product_warranty']) && !empty($is_direct_sell))
											<td>
												{!! Form::select("product[1][warranty_id]", $warranties, $warranty_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control']); !!}
											</td>
										@endif
										<td class="text-center">
											@php
												$subtotal_type = !empty($pos_settings['is_pos_subtotal_editable']) ? 'text' : 'hidden';
									
											@endphp
											<input type="text"  class="form-control pos_line_total @if(!empty($pos_settings['is_pos_subtotal_editable'])) input_number @endif" value="0.00">
											{{-- <span class="display_currency pos_line_total_text @if(!empty($pos_settings['is_pos_subtotal_editable'])) hide @endif" data-currency_symbol="true">{{$product->quantity_ordered*$unit_price_inc_tax}}</span> --}}
										</td>
									
									</tr>

									{{-- 3rd Row --}}

									
							
							<tr >
								<td class="text-center v-center">
									
									<button class="btn btn-danger remove" type="button" onclick="remove_row(this)" style="padding: 0px 5px 2px 5px;">
										<i class="fa fa-trash" aria-hidden="true"></i>
									</button>
								</td>	
									<td>
										<span class='sr_number'>3</span>
									</td>
									<td class="hide">
									<div class="col-sm-4">
													<div class="form-group">
														{!! Form::select('product[2][store]', $store,null, ['class' => 'form-control  select2','id'=>"store",'style'=>"width: 146px;"]); !!}
													</div>
												</div>
									</td>
									<td>
										{{-- {!! $product->sub_sku !!}	 --}}
										<input class="form-control product_code" readonly="" id="item_code" name="product[2][item_code]" type="text">
									</td>
									<td>
										<select name="product[2][product_id]" class="form-control select2" id="prd_select" onchange="get_product_code(this)">
											<option value="">Please Select</option>
											@foreach ($product as $p)
												<option value="{{$p->id}}">{{$p->name}}</option>
											@endforeach
										</select>
										<input type="hidden" name="gross_weight" class="gross__weight">
									<input type="hidden" name="net_weight" class="net__weight">
											{{-- @if(!empty($so_line))
												<input type="hidden" 
												name="product[1][so_line_id]" 
												value="{{$so_line->id}}">
											@endif
											@php
												// $product_name = $product->product_name  ;
											@endphp --}}
									
											{{-- @if( ($edit_price || $edit_discount) && empty($is_direct_sell) )
											<div title="@lang('lang_v1.pos_edit_product_price_help')">
											<span class="text-link text-info cursor-pointer" data-toggle="modal" data-target="#row_edit_product_price_modal_0">
												{!! $product_name !!}
												&nbsp;<i class="fa fa-info-circle"></i>
											</span>
											</div>
											@else
												{!! $product_name !!}
											@endif --}}
											{{-- <input type="hidden" class="enable_sr_no" value="{{$product->enable_sr_no}}"> --}}
											
											<input type="hidden" class="product_type" name="product[2][product_type]" value="0">
									
											@php
												$hide_tax = 'hide';
												if(session()->get('business.enable_inline_tax') == 1){
													$hide_tax = '';
												}
												
												// $tax_id = $product->tax_id;
												// $item_tax = !empty($product->item_tax) ? $product->item_tax : 0;
												// $unit_price_inc_tax = $product->sell_price_inc_tax;
									
												if(!empty($so_line)) {
													$tax_id = $so_line->tax_id;
													$item_tax = $so_line->item_tax;
												}
									
												if($hide_tax == 'hide'){
													$tax_id = null;
													$unit_price_inc_tax = $product->default_sell_price;
												}
									
												$discount_type = !empty($product->line_discount_type) ? $product->line_discount_type : 'fixed';
												$discount_amount = !empty($product->line_discount_amount) ? $product->line_discount_amount : 0;
												
												if(!empty($discount)) {
													$discount_type = $discount->discount_type;
													$discount_amount = $discount->discount_amount;
												}
									
												if(!empty($so_line)) {
													$discount_type = $so_line->line_discount_type;
													$discount_amount = $so_line->line_discount_amount;
												}
									
												  $sell_line_note = '';
												  if(!empty($product->sell_line_note)){
													  $sell_line_note = $product->sell_line_note;
												  }
											  @endphp
									
											@if(!empty($discount))
												{!! Form::hidden("product[1][discount_id]", $discount->id); !!}
											@endif
									
											@php
												// $warranty_id = !empty($action) && $action == 'edit' && !empty($product->warranties->first())  ? $product->warranties->first()->id : $product->warranty_id;
											@endphp
									
											@if(empty($is_direct_sell))
											<div class="modal fade row_edit_product_price_model" id="row_edit_product_price_modal_0" tabindex="-1" role="dialog">
												{{-- @include('sale_pos.partials.row_edit_product_price_modal') --}}
											</div>
											@endif
									
											<!-- Description modal end -->
											@if(in_array('modifiers' , $enabled_modules))
												<div class="modifiers_html">
													@if(!empty($product->product_ms))
														{{-- @include('restaurant.product_modifier_set.modifier_for_product', array('edit_modifiers' => true, 'row_count' => $loop->index, 'product_ms' => $product->product_ms ) ) --}}
													@endif
												</div>
											@endif
									
											@php
												// $max_quantity = $product->qty_available;
												// $formatted_max_quantity = $product->formatted_qty_available;
									
												if(!empty($action) && $action == 'edit') {
													if(!empty($so_line)) {
														$qty_available = $so_line->quantity - $so_line->so_quantity_invoiced + $product->quantity_ordered;
														$max_quantity = $qty_available;
														$formatted_max_quantity = number_format($qty_available, config('constants.currency_precision', 2), session('currency')['decimal_separator'], session('currency')['thousand_separator']);
													}
												} else {
													if(!empty($so_line) && $so_line->qty_available <= $max_quantity) {
														$max_quantity = $so_line->qty_available;
														$formatted_max_quantity = $so_line->formatted_qty_available;
													}
												}
												
									
												// $max_qty_rule = $max_quantity;
												// $max_qty_msg = __('validation.custom-messages.quantity_not_available', ['qty'=> $formatted_max_quantity, 'unit' => $product->unit  ]);
											@endphp
									
											@if( session()->get('business.enable_lot_number') == 1 || session()->get('business.enable_product_expiry') == 1)
											@php
												$lot_enabled = session()->get('business.enable_lot_number');
												$exp_enabled = session()->get('business.enable_product_expiry');
												$lot_no_line_id = '';
												if(!empty($product->lot_no_line_id)){
													$lot_no_line_id = $product->lot_no_line_id;
												}
											@endphp
											@if(!empty($product->lot_numbers) && empty($is_sales_order))
												<select class="form-control lot_number input-sm" name="product[1][lot_no_line_id]" @if(!empty($product->transaction_sell_lines_id)) disabled @endif>
													<option value="">@lang('lang_v1.lot_n_expiry')</option>
													@foreach($product->lot_numbers as $lot_number)
														@php
															$selected = "";
															if($lot_number->purchase_line_id == $lot_no_line_id){
																$selected = "selected";
									
																$max_qty_rule = $lot_number->qty_available;
																$max_qty_msg = __('lang_v1.quantity_error_msg_in_lot', ['qty'=> $lot_number->qty_formated, 'unit' => $product->unit  ]);
															}
									
															$expiry_text = '';
															if($exp_enabled == 1 && !empty($lot_number->exp_date)){
																if( \Carbon::now()->gt(\Carbon::createFromFormat('Y-m-d', $lot_number->exp_date)) ){
																	$expiry_text = '(' . __('report.expired') . ')';
																}
															}
									
															//preselected lot number if product searched by lot number
															if(!empty($purchase_line_id) && $purchase_line_id == $lot_number->purchase_line_id) {
																$selected = "selected";
									
																$max_qty_rule = $lot_number->qty_available;
																$max_qty_msg = __('lang_v1.quantity_error_msg_in_lot', ['qty'=> $lot_number->qty_formated, 'unit' => $product->unit  ]);
															}
														@endphp
														<option value="{{$lot_number->purchase_line_id}}" data-qty_available="{{$lot_number->qty_available}}" data-msg-max="@lang('lang_v1.quantity_error_msg_in_lot', ['qty'=> $lot_number->qty_formated, 'unit' => $product->unit  ])" {{$selected}}>@if(!empty($lot_number->lot_number) && $lot_enabled == 1){{$lot_number->lot_number}} @endif @if($lot_enabled == 1 && $exp_enabled == 1) - @endif @if($exp_enabled == 1 && !empty($lot_number->exp_date)) @lang('product.exp_date'): {{@format_date($lot_number->exp_date)}} @endif {{$expiry_text}}</option>
													@endforeach
												</select>
											@endif
										@endif
										
										</td>
										<td>
											{!! Form::select('product[2][brand_id]', ['' => 'Select'] + $brands->pluck('name','id')->all(), null, ['class' => 'form-control select2','id' =>'brand_id']) !!}
										</td>
										<td>
										<textarea class="form-control" name="product[2][sell_line_note]" rows="2">{{$sell_line_note}}</textarea>
									
														</td>
														<td>
															<input type="text" class="form-control uom" readonly="" style="width: 92px">
														{{-- @if(count($sub_units) > 0) --}}
									
												{{-- <select name="product[1][sub_unit_id]" class="form-control input-sm sub_unit">
													@foreach($sub_units as $key => $value)
														<option value="{{$key}}" data-multiplier="{{$value['multiplier']}}" data-unit_name="{{$value['name']}}" data-allow_decimal="{{$value['allow_decimal']}}" @if(!empty($product->sub_unit_id) && $product->sub_unit_id == $key) selected @endif>
															{{$value['name']}}
														</option>
													@endforeach
											   </select> --}}
											{{-- @else
												{{$product->unit}}
											@endif --}}
														</td>
									
										<td>
											{{-- If edit then transaction sell lines will be present --}}
											{{-- @if(!empty($product->transaction_sell_lines_id))
												<input type="hidden" name="product[1][transaction_sell_lines_id]" class="form-control" value="{{$product->transaction_sell_lines_id}}">
											@endif --}}
									
											{{-- <input type="hidden" name="product[1][product_id]" class="form-control product_id" value=""> --}}
									
											<input type="hidden" value="0" 
												name="product[2][variation_id]" class="row_variation_id">
									
											<input type="hidden" value="0" 
												name="product[2][enable_stock]">
											
											{{-- @if(empty($product->quantity_ordered))
												@php
													$product->quantity_ordered = 1;
												@endphp
											@endif --}}
									
											{{-- @php
												$multiplier = 1;
												$allow_decimal = true;
												// if($product->unit_allow_decimal != 1) {
												// 	$allow_decimal = false;
												// }
											@endphp --}}
											{{-- @foreach($sub_units as $key => $value)
												@if(!empty($product->sub_unit_id) && $product->sub_unit_id == $key)
													@php
														$multiplier = $value['multiplier'];
														$max_qty_rule = $max_qty_rule / $multiplier;
														$unit_name = $value['name'];
														$max_qty_msg = __('validation.custom-messages.quantity_not_available', ['qty'=> $max_qty_rule, 'unit' => $unit_name  ]);
									
														if(!empty($product->lot_no_line_id)){
															$max_qty_msg = __('lang_v1.quantity_error_msg_in_lot', ['qty'=> $max_qty_rule, 'unit' => $unit_name  ]);
														}
									
														if($value['allow_decimal']) {
															$allow_decimal = true;
														}
													@endphp
												@endif
											@endforeach --}}
											<div class="input-group input-number">
										
												<input type="text" data-min="1" 
												class="form-control pos_quantity input_number mousetrap input_quantity" 
												value="0.00" name="product[2][quantity]" data-allow-overselling="@if(empty($pos_settings['allow_overselling'])){{'false'}}@else{{'true'}}@endif" 
												onkeyup="calculate_unitprice(this)"
												{{-- @if($allow_decimal) 
													data-decimal=1 
												@else 
													data-decimal=0 
													data-rule-abs_digit="true" 
													data-msg-abs_digit="@lang('lang_v1.decimal_value_not_allowed')" 
												@endif --}}
												{{-- data-rule-required="true" 
												data-msg-required="@lang('validation.custom-messages.this_field_is_required')"  --}}
												{{-- @if($product->enable_stock && empty($pos_settings['allow_overselling']) && empty($is_sales_order) )
													data-rule-max-value="{{$max_qty_rule}}" data-qty_available="{{$product->qty_available}}" data-msg-max-value="{{$max_qty_msg}}" 
													data-msg_max_default="@lang('validation.custom-messages.quantity_not_available', ['qty'=> $product->formatted_qty_available, 'unit' => $product->unit  ])" 
												@endif  --}}
											>
											<input type="hidden" name="base_unit" class="base_unit">
                        <input type="hidden" name="transporter_rate" class="transporter_rate" />
                        <input type="hidden" name="contractor_rate" class="contractor_rate" />
											
										</div>
											
											{{-- <input type="hidden" name="product[1][product_unit_id]" value="{{$product->unit_id}}"> --}}
											
									
											{{-- <input type="hidden" class="base_unit_multiplier" name="product[1][base_unit_multiplier]" value="{{$multiplier}}"> --}}
									
											{{-- <input type="hidden" class="hidden_base_unit_sell_price" value="{{$product->default_sell_price / $multiplier}}"> --}}
											
											{{-- Hidden fields for combo products --}}
											{{-- @if($product->product_type == 'combo'&& !empty($product->combo_products))
									
												@foreach($product->combo_products as $k => $combo_product)
									
													@if(isset($action) && $action == 'edit')
														@php
															$combo_product['qty_required'] = $combo_product['quantity'] / $product->quantity_ordered;
									
															$qty_total = $combo_product['quantity'];
														@endphp
													@else
														@php
															$qty_total = $combo_product['qty_required'];
														@endphp
													@endif
									
													<input type="hidden" 
														name="product[1][combo][{{$k}}][product_id]"
														value="{{$combo_product['product_id']}}">
									
														<input type="hidden" 
														name="product[1][combo][{{$k}}][variation_id]"
														value="{{$combo_product['variation_id']}}">
									
														<input type="hidden"
														class="combo_product_qty" 
														name="product[1][combo][{{$k}}][quantity]"
														data-unit_quantity="{{$combo_product['qty_required']}}"
														value="{{$qty_total}}">
									
														@if(isset($action) && $action == 'edit')
															<input type="hidden" 
																name="product[1][combo][{{$k}}][transaction_sell_lines_id]"
																value="{{$combo_product['id']}}">
														@endif
									
												@endforeach
											@endif --}}
										</td>
										{{-- @if(!empty($is_direct_sell))
											@if(!empty($pos_settings['inline_service_staff']))
												<td>
													<div class="form-group">
														<div class="input-group">
															{!! Form::select("products[" . 0 . "][res_service_staff_id]", $waiters, !empty($product->res_service_staff_id) ? $product->res_service_staff_id : null, ['class' => 'form-control select2 order_line_service_staff', 'placeholder' => __('restaurant.select_service_staff'), 'required' => (!empty($pos_settings['is_service_staff_required']) && $pos_settings['is_service_staff_required'] == 1) ? true : false ]); !!}
														</div>
													</div>
												</td>
											@endif
											@php
												$pos_unit_price = !empty($product->unit_price_before_discount) ? $product->unit_price_before_discount : $product->default_sell_price;
									
												if(!empty($so_line)) {
													$pos_unit_price = $so_line->unit_price_before_discount;
												}
											@endphp --}}
											<td>
												<input type="text" name="product[2][unit_price]" class="form-control pos_unit_price input_number mousetrap" value="0.00"  onkeyup="calculate_unitprice(this)">
											</td>
											 <td class="hide">
												{!! Form::text("product[2][line_discount_amount]", @num_format($discount_amount), ['class' => 'form-control input_number row_discount_amount','onkeyup' => 'calculate_discount(this)']); !!}<br>
												{!! Form::select("product[2][line_discount_type]", ['fixed' => __('lang_v1.fixed')], $discount_type , ['class' => 'form-control row_discount_type']); !!}
												@if(!empty($discount))
													<p class="help-block">{!! __('lang_v1.applied_discount_text', ['discount_name' => $discount->name, 'starts_at' => $discount->formated_starts_at, 'ends_at' => $discount->formated_ends_at]) !!}</p>
												@endif
											</td>
											<td class="get_total">
												<input class="calculate_discount row_total_amount  form-control" readonly type="text">
											</td>
											<td class="text-center {{$hide_tax}}">
												
												{!! Form::hidden("product[2][item_tax]",null, ['class' => 'item_tax']); !!}
											
												{{-- {!! Form::select("product[1][tax_id]", null, null, ['placeholder' => 'Select', 'class' => 'form-control tax_id'], null); !!} --}}
												
												<select name="product[2][tax_id]" class="form-control select2 input-sm tax_idd" placeholder="'Please Select'" id="tax_id" onchange="calculate_unitprice(this)">
													<option value="0" data-ratee="0">@lang('lang_v1.none')</option>
													@foreach($tax_rate as $tax_ratee)
														<option value="{{ $tax_ratee->id }}" data-ratee="{{ $tax_ratee->amount }}">{{ $tax_ratee->name }}</option>
													@endforeach
												</select>
											</td>
									
										{{-- @else
											@if(!empty($warranties))
												{!! Form::select("product[1][warranty_id]", $warranties, $warranty_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control']); !!}
											@endif
									
											@if(!empty($pos_settings['inline_service_staff']))
												<td>
													<div class="form-group">
														<div class="input-group">
															{!! Form::select("products[" . 0 . "][res_service_staff_id]", $waiters, !empty($product->res_service_staff_id) ? $product->res_service_staff_id : null, ['class' => 'form-control select2 order_line_service_staff', 'placeholder' => __('restaurant.select_service_staff'), 'required' => (!empty($pos_settings['is_service_staff_required']) && $pos_settings['is_service_staff_required'] == 1) ? true : false ]); !!}
														</div>
													</div>
												</td>
											@endif
										@endif --}}
										
										
										<td class="text-center">
                    						<input type="hidden" class="form-control further_tax_hidden" name="products[2][item_further_tax]" />
                    						<select name="products[2][further_taax_id]" class="form-control select2 input-sm further_tax" id="further_tax"  onchange="calculate_unitprice(this)" placeholder="Please Select">
                    							<option value="0" data-rate="0">NONE</option>
                    							@foreach($further_tax as $further)
							    <option value="{{ $further->id }}" data-rate="{{ $further->amount }}">{{ $further->name }}</option>
							@endforeach
                    						</select>
                    					</td>
										<td class="{{$hide_tax}}">
											<input type="text" name="product[2][unit_price_inc_tax]" class="form-control pos_unit_price_inc_tax input_number" value="0.00" readonly>
										</td>
										
					<td>
                        <input type="number" name="products[2][salesman_commission_rate]" class="form-control salesman_commission_rate" onkeyup="calculate_unitprice(this)"/>
					</td>
										
										
										@if(!empty($common_settings['enable_product_warranty']) && !empty($is_direct_sell))
											<td>
												{!! Form::select("product[2][warranty_id]", $warranties, $warranty_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control']); !!}
											</td>
										@endif
										<td class="text-center">
											@php
												$subtotal_type = !empty($pos_settings['is_pos_subtotal_editable']) ? 'text' : 'hidden';
									
											@endphp
											<input type="text"  class="form-control pos_line_total @if(!empty($pos_settings['is_pos_subtotal_editable'])) input_number @endif" value="0.00">
											{{-- <span class="display_currency pos_line_total_text @if(!empty($pos_settings['is_pos_subtotal_editable'])) hide @endif" data-currency_symbol="true">{{$product->quantity_ordered*$unit_price_inc_tax}}</span> --}}
										</td>
									
									</tr>

						</tbody>
					</table>
					</div>

					<br/>
					<button class="btn btn-md btn-primary addBtn" type="button"  onclick="add_row(this)" style="padding: 0px 5px 2px 5px;">
						Add Row
					</button>
					<div class="table-responsive">
					<table class="table table-condensed table-bordered table-striped tabel_data">
						<tr>
							<td>
								<div class="pull-right">

									<br>
									&nbsp;&nbsp;&nbsp;&nbsp;
									<b>@lang('sale.item'):</b> 
									<span class="total_quantity">0</span>
									
									<br>
									&nbsp;&nbsp;&nbsp;&nbsp;
									<b>@lang('sale.total'): </b>
									<span class="price_total">0</span>

									<br>
									&nbsp;&nbsp;&nbsp;&nbsp;
									<b>Total Gross Weight:</b>
									<span id="total_gross__weight">0.00</span>

									<br>
									&nbsp;&nbsp;&nbsp;&nbsp;
									<b>Total Net Weight:</b>
									<span id="total_net__weight">0.00</span>
									<input type="hidden" name="total_gross__weight" class="total_gross__weight"/>
									<input type="hidden" name="total_net__weight" class="total_net__weight"/>
                					<input type="hidden" id="total_sale_tax" name="total_sale_tax">
                					<input type="hidden" id="total_further_tax" name="total_further_tax"> 
                					<input type="hidden" id="total_salesman_commission" name="total_salesman_commission">   
                					<input type="hidden" id="total_transporter_rate" name="total_transporter_rate">
                					<input type="hidden" id="total_contractor_rate" name="total_contractor_rate">

								</div>
							</td>
						</tr>
					</table>
					</div>
			
			@endcomponent
			<div style="display: none;">
			@component('components.widget', ['class' => 'box-solid'])
				<div class="col-md-4  @if($sale_type == 'sales_order') hide @endif">
			        <div class="form-group">
			            {!! Form::label('discount_type', __('sale.discount_type') . ':*' ) !!}
			            <div class="input-group">
			                <span class="input-group-addon">
			                    <i class="fa fa-info"></i>
			                </span>
			                {!! Form::select('discount_type', ['fixed' => __('lang_v1.fixed')], 'percentage' , ['class' => 'form-control','placeholder' => __('messages.please_select'), 'required', 'data-default' => 'percentage']); !!}
			            </div>
			        </div>
			    </div>
			    @php
			    	$max_discount = !is_null(auth()->user()->max_sales_discount_percent) ? auth()->user()->max_sales_discount_percent : '';

			    	//if sale discount is more than user max discount change it to max discount
			    	$sales_discount = $business_details->default_sales_discount;
			    	if($max_discount != '' && $sales_discount > $max_discount) $sales_discount = $max_discount;

			    	$default_sales_tax = $business_details->default_sales_tax;

			    	if($sale_type == 'sales_order') {
			    		$sales_discount = 0;
			    		$default_sales_tax = null;
			    	}
			    @endphp
			    <div class="col-md-4 @if($sale_type == 'sales_order') hide @endif">
			        <div class="form-group">
			            {!! Form::label('discount_amount', __('sale.discount_amount') . ':*' ) !!}
			            <div class="input-group">
			                <span class="input-group-addon">
			                    <i class="fa fa-info"></i>
			                </span>
			                {!! Form::text('discount_amount', @num_format($sales_discount), ['class' => 'form-control input_number', 'data-default' => $sales_discount, 'data-max-discount' => $max_discount, 'data-max-discount-error_msg' => __('lang_v1.max_discount_error_msg', ['discount' => $max_discount != '' ? @num_format($max_discount) : '']) ]); !!}
			            </div>
			        </div>
			    </div>
			    <div class="col-md-4 @if($sale_type == 'sales_order') hide @endif"><br>
			    	<b>@lang( 'sale.discount_amount' ):</b>(-) 
					<span class="display_currency" id="total_discount">0</span>
			    </div>
			    <div class="clearfix"></div>
			    <div class="col-md-12 well well-sm bg-light-gray @if(session('business.enable_rp') != 1 || $sale_type == 'sales_order') hide @endif">
			    	<input type="hidden" name="rp_redeemed" id="rp_redeemed" value="0">
			    	<input type="hidden" name="rp_redeemed_amount" id="rp_redeemed_amount" value="0">
			    	<div class="col-md-12"><h4>{{session('business.rp_name')}}</h4></div>
			    	<div class="col-md-4">
				        <div class="form-group">
				            {!! Form::label('rp_redeemed_modal', __('lang_v1.redeemed') . ':' ) !!}
				            <div class="input-group">
				                <span class="input-group-addon">
				                    <i class="fa fa-gift"></i>
				                </span>
				                {!! Form::number('rp_redeemed_modal', 0, ['class' => 'form-control direct_sell_rp_input', 'data-amount_per_unit_point' => session('business.redeem_amount_per_unit_rp'), 'min' => 0, 'data-max_points' => 0, 'data-min_order_total' => session('business.min_order_total_for_redeem') ]); !!}
				                <input type="hidden" id="rp_name" value="{{session('business.rp_name')}}">
				            </div>
				        </div>
				    </div>
				    <div class="col-md-4">
				    	<p><strong>@lang('lang_v1.available'):</strong> <span id="available_rp">0</span></p>
				    </div>
				    <div class="col-md-4">
				    	<p><strong>@lang('lang_v1.redeemed_amount'):</strong> (-)<span id="rp_redeemed_amount_text">0</span></p>
				    </div>
			    </div>
			    <div class="clearfix"></div>
			    <div class="col-md-4  @if($sale_type == 'sales_order') hide @endif">
			    	<div class="form-group">
			            {!! Form::label('tax_rate_id', __('sale.order_tax') . ':*' ) !!}
			            <div class="input-group">
			                <span class="input-group-addon">
			                    <i class="fa fa-info"></i>
			                </span>
			                {!! Form::select('tax_rate_id', $taxes['tax_rates'], $default_sales_tax, ['placeholder' => __('messages.please_select'), 'class' => 'form-control', 'data-default'=> $default_sales_tax], $taxes['attributes']); !!}

							<input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount" 
							value="@if(empty($edit)) {{@num_format($business_details->tax_calculation_amount)}} @else {{@num_format(optional($transaction->tax)->amount)}} @endif" data-default="{{$business_details->tax_calculation_amount}}">
			            </div>
			        </div>
			    </div>
			    <div class="col-md-4 col-md-offset-4  @if($sale_type == 'sales_order') hide @endif">
			    	<b>@lang( 'sale.order_tax' ):</b>(+) 
					<span class="display_currency" id="order_tax">0</span>
			    </div>				
				
			    <div class="col-md-12">
			    	<div class="form-group">
						{!! Form::label('sell_note',__('sale.sell_note')) !!}
						{!! Form::textarea('sale_note', null, ['class' => 'form-control', 'rows' => 3]); !!}
					</div>
			    </div>
				<input type="hidden" name="is_direct_sale" value="1">
			@endcomponent
				</div>
			@component('components.widget', ['class' => 'box-solid'])

			<div class="col-sm-4">
				<div class="form-group">
					<label>Term & Condition</label>
					<select class="form-control" name="tandc_type" id="TCS">
						<option selected disabled> Select</option>
						@foreach ($T_C as $tc)
							<option value="{{ $tc->id }}">{{ $tc->title }}</option>
						@endforeach
					</select>
				</div>
			</div>

			<div class="col-sm-4">
				{!! Form::label('contractor', 'Contractor' . ':*') !!}
				<select name="contractor" class="form-control contractor">
					<option disabled selected>Please Select</option>
					@foreach ($contractor as $c)
						<option value="{{ $c['id'] }}"{{ isset($default_contractor) && $default_contractor == $c['id'] ? 'selected' : '' }}>{{ $c['supplier_business_name'] }}</option>
					@endforeach
				</select>
			</div>

			<div class="col-sm-12">
				<div class="form-group" id="TandC" style="display:none;">
					{!! Form::label('tandc_title', __('Terms & Conditions')) !!}
					{!! Form::textarea('tandc_title', null, [
						'class' => 'form-control name',
						'id' => 'product_description1',
						'rows' => 3,
					]) !!}
				</div>
			</div>

			<div style="display: none;">
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
			            {!! Form::text('shipping_custom_field_5',null, ['class' => 'form-control','placeholder' => $shipping_custom_label_5, 'required' => $is_shipping_custom_field_5_required]); !!}
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
	        <div class="clearfix"></div>
	
		    <div class="col-md-4 col-md-offset-8">
		    	@if(!empty($pos_settings['amount_rounding_method']) && $pos_settings['amount_rounding_method'] > 0)
		    	<small id="round_off"><br>(@lang('lang_v1.round_off'): <span id="round_off_text">0</span>)</small>
				<br/>
				<input type="hidden" name="round_off_amount" 
					id="round_off_amount" value=0>
				@endif
		    	<div><b>@lang('sale.total_payable'): </b>
					<input type="hidden" name="final_total" id="final_total_input">
					<span id="total_payable">0</span>
				</div>
		    </div>
				</div>

			@endcomponent
		</div>
	</div>
	@if(!empty($common_settings['is_enabled_export']) && $sale_type != 'sales_order')
		@component('components.widget', ['class' => 'box-solid hide', 'title' => __('lang_v1.export')])
		<div style="display: none;">

			<div class="col-md-12 mb-12">
                <div class="form-check">
                    <input type="checkbox" name="is_export" class="form-check-input" id="is_export" >
                    <label class="form-check-label" for="is_export">@lang('lang_v1.is_export')</label>
                </div>
            </div>
	        @php
	            $i = 1;
	        @endphp
	        @for($i; $i <= 6 ; $i++)
	            <div class="col-md-4 export_div" >
	                <div class="form-group">
	                    {!! Form::label('export_custom_field_'.$i, __('lang_v1.export_custom_field'.$i).':') !!}
	                    {!! Form::text('export_custom_fields_info['.'export_custom_field_'.$i.']',null, ['class' => 'form-control','placeholder' => __('lang_v1.export_custom_field'.$i), 'id' => 'export_custom_field_'.$i]); !!}
	                </div>
	            </div>
	        @endfor
				</div>

		@endcomponent
	@endif
	<div style="display: none;">

	@php
		$is_enabled_download_pdf = config('constants.enable_download_pdf');
		$payment_body_id = 'payment_rows_div';
		if ($is_enabled_download_pdf) {
			$payment_body_id = '';
		}
	@endphp
	@if((empty($status) || (!in_array($status, ['quotation', 'draft'])) || $is_enabled_download_pdf) && $sale_type != 'sales_order')
		@can('sell.payments')
			@component('components.widget', ['class' => 'box-solid', 'id' => $payment_body_id, 'title' => __('purchase.add_payment')])

			@if($is_enabled_download_pdf)
				<div class="well row">
					<div class="col-md-6">
						<div class="form-group">
							{!! Form::label("prefer_payment_method" , __('lang_v1.prefer_payment_method') . ':') !!}
							@show_tooltip(__('lang_v1.this_will_be_shown_in_pdf'))
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fas fa-money-bill-alt"></i>
								</span>
								{!! Form::select("prefer_payment_method", $payment_types, 'cash', ['class' => 'form-control','style' => 'width:100%;']); !!}
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							{!! Form::label("prefer_payment_account" , __('lang_v1.prefer_payment_account') . ':') !!}
							@show_tooltip(__('lang_v1.this_will_be_shown_in_pdf'))
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fas fa-money-bill-alt"></i>
								</span>
								{!! Form::select("prefer_payment_account", $accounts, null, ['class' => 'form-control','style' => 'width:100%;']); !!}
							</div>
						</div>
					</div>
				</div>
			@endif
			@if(empty($status) || !in_array($status, ['quotation', 'draft']))
				<div class="payment_row" @if($is_enabled_download_pdf) id="payment_rows_div" @endif>
					<div class="row">
						<div class="col-md-12 mb-12">
							<strong>@lang('lang_v1.advance_balance'):</strong> <span id="advance_balance_text"></span>
							{!! Form::hidden('advance_balance', null, ['id' => 'advance_balance', 'data-error-msg' => __('lang_v1.required_advance_balance_not_available')]); !!}
						</div>
					</div>
					@include('sale_pos.partials.payment_row_form', ['row_index' => 0, 'show_date' => true])
					<hr>
					<div class="row">
						<div class="col-sm-12">
							<div class="pull-right"><strong>@lang('lang_v1.balance'):</strong> <span class="balance_due">0.00</span></div>
						</div>
					</div>
				</div>
			@endif
	</div>

			@endcomponent
		@endcan
	@endif
	@if(empty($pos_settings['disable_recurring_invoice']))
		@include('sale_pos.partials.recurring_invoice_modal')
	@endif
	
	<div class="row " style="  background-color: white; padding: 23px; margin:0px;">
		{!! Form::hidden('is_save_and_print', 0, ['id' => 'is_save_and_print']); !!}
		{!! Form::hidden('is_save_and_next', 0, ['id' => 'is_save_and_next']) !!}
		<div class="col-sm-12 text-center fixed-button">
			<button type="button" id="save-and-next" class="btn btn-primary btn-big">Save & Next</button>
			<button type="button" id="submit-sell" accesskey="s" class="btn btn-primary sale_submit btn-big">save & close</button>
			<button type="button" class="btn btn-big btn-danger" onclick="window.history.back()">Close</button>
			<button type="button" id="save-and-print" class="btn btn-success sale_submit hide">@lang('lang_v1.save_and_print')</button>
		</div>
	</div>
	
	{!! Form::close() !!}
</section>

<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	{{-- @include('contact.create', ['quick_add' => true]) --}}
</div>
<!-- /.content -->
<div class="modal fade register_details_modal" tabindex="-1" role="dialog" 
	aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade close_register_modal" tabindex="-1" role="dialog" 
	aria-labelledby="gridSystemModalLabel">
</div>

<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>

@include('sale_pos.partials.configure_search_modal')

@stop

@section('javascript')
	<script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>

	<!-- Call restaurant module if defined -->
    @if(in_array('tables' ,$enabled_modules) || in_array('modifiers' ,$enabled_modules) || in_array('service_staff' ,$enabled_modules))
    	<script src="{{ asset('js/restaurant.js?v=' . $asset_v) }}"></script>
    @endif
    <script type="text/javascript">
    	$(document).ready( function() {

			// $(document).on('click', '#submit-sell',function(e){
			// 	// e.preventDefault();
			// 	var ref_val = $('.trn_prefix').val();
			// 	var SRI_ref_no = $('#SRI_ref_no').val();
			// 	SRI_ref_no = ref_val+SRI_ref_no;
			// 	alert(SRI_ref_no);
			// })

			$(document).on('change', '.contractor', function() {
                $('#prd_select').trigger('change');
            })

            $("#TCS").change(function() {
                var id = $("#TCS").val();
                $.ajax({
                    type: "GET",
                    url: '/get_term/' + id,
                    success: function(data) {
                        tinymce.remove('textarea');
                        $('#id_edit').val(data.id);
                        $('.name').val(data.name);
                        $('#title').val(data.title);
                        tinymce.init({
                            selector: 'textarea#product_description1',
                        });
                    }
                })
                $("#TandC").show();
            });
			
			$(document).on('change','#sales_man',function(){
    	        var commission = $(this).children("option:selected").attr("data-commission");
    	        $('#saleman_commission').val(commission);
    	    })


    		$('#status').change(function(){
    			if ($(this).val() == 'final') {
    				$('#payment_rows_div').removeClass('hide');
    			} else {
    				$('#payment_rows_div').addClass('hide');
    			}
    		});
    		$('.paid_on').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });

            $('#shipping_documents').fileinput({
		        showUpload: false,
		        showPreview: false,
		        browseLabel: LANG.file_browse_label,
		        removeLabel: LANG.remove,
		    });

		    $(document).on('change', '#prefer_payment_method', function(e) {
			    var default_accounts = $('select#select_location_id').length ? 
			                $('select#select_location_id')
			                .find(':selected')
			                .data('default_payment_accounts') : $('#location_id').data('default_payment_accounts');
			    var payment_type = $(this).val();
			    if (payment_type) {
			        var default_account = default_accounts && default_accounts[payment_type]['account'] ? 
			            default_accounts[payment_type]['account'] : '';
			        var account_dropdown = $('select#prefer_payment_account');
			        if (account_dropdown.length && default_accounts) {
			            account_dropdown.val(default_account);
			            account_dropdown.change();
			        }
			    }
			});

		    function setPreferredPaymentMethodDropdown() {
			    var payment_settings = $('#location_id').data('default_payment_accounts');
			    payment_settings = payment_settings ? payment_settings : [];
			    enabled_payment_types = [];
			    for (var key in payment_settings) {
			        if (payment_settings[key] && payment_settings[key]['is_enabled']) {
			            enabled_payment_types.push(key);
			        }
			    }
			    if (enabled_payment_types.length) {
			        $("#prefer_payment_method > option").each(function() {
		                if (enabled_payment_types.indexOf($(this).val()) != -1) {
		                    $(this).removeClass('hide');
		                } else {
		                    $(this).addClass('hide');
		                }
			        });
			    }
			}
			
			setPreferredPaymentMethodDropdown();

			$('#is_export').on('change', function () {
	            if ($(this).is(':checked')) {
	                $('div.export_div').show();
	            } else {
	                $('div.export_div').hide();
	            }
	        });
    	});
		// function get_ntn_cnic(x)
		// {
		// 	var terms = $(x).val();
		// 	// alert(terms);
		// 	$.getJSON(
		// 		'/customer/get_ntncnic',
		// 		{ term: terms },
		// 		function(data){
		// 			$(this).val(data[0].ntn_cnic_no);
		// 			console.log(data[0]);            
		// 			$('.ntncnic').val(data[0].ntn_cnic_no);
		// 			$('.gst').val(data[0].gst_no);
					
		// 	});
		// }
		function get_product_code(el)
		{
			var terms = $(el).val();
        	var transporter = $('.transporter').val(); 
        	var contractor = $('.contractor').val(); 
			// alert(terms);
			$.getJSON(
				'/purchases/get_products',
				{   term: terms,
    				transporter: transporter,
    				contractor: contractor
				},
				function(data){
					$(this).val(data[0].unit.actual_name);
					console.log(data[0]);            
					$(el).closest("tr").find(".product_code").val(data[0].sku);
					$(el).closest("tr").find(".uom").val(data[0]?.unit?.actual_name || '');
					$(el).closest("tr").find(".gross__weight").val(data[0].weight);
					$(el).closest("tr").find(".net__weight").val(data[0].product_custom_field1);
					$(el).closest("tr").find(".base_unit").val(data[0].unit.base_unit_multiplier);
					$(el).closest("tr").find(".brand").val(data[0]?.brand?.name || '');
        			$(el).closest("tr").find(".transporter_rate").val(data[0].transporter_rate);
        			$(el).closest("tr").find(".contractor_rate").val(data[0].contractor_rate);
					$(el).closest("tr").find('.recorder_lst').html(''); 
					$('#prd_select ').select2('destroy');
                    $(el).after('<div class="recorder_lst">Last Sale Price of this Product Is ' + data[0].last_sale_price + '</div>');
                    $('#prd_select ').select2();
					pos_total_row();
			});
		}

		function add_row(el){
			// alert("asdgfsadf");
			$('#pos_table tbody tr').each(function(){
				// $(this).find('#prd_select').select2('destroy')
				$(this).find('#prd_select,#store,#tax_id,#further_tax,#brand_id').select2('destroy')
			})
			var tr = $("#pos_table #tbody tr:last").clone();
			tr.find('input, textarea, select').val('');
			tr.find('.tax_idd, .further_tax').val(0);
			tr.find('.recorder_lst').html('');
			$("#pos_table #tbody tr:last").after(tr);
			
			reIndexTable();
			update_table_sr_number();

		}
		function reIndexTable(){
			var j=0;
			$('#pos_table tbody tr').each(function(){
				$(this).find('#prd_select,#tax_id,#further_tax,#brand_id').select2()
				$(this).find('#store').select2()
				$(this).attr('id',j)
				$(this).find('[name*=store]').attr('name',"product["+j+"][store]")
				$(this).find('[name*=item_code]').attr('name',"product["+j+"][item_code]")
				$(this).find('[name*=product_id]').attr('name',"product["+j+"][product_id]")
				$(this).find('[name*=product_type]').attr('name',"product["+j+"][product_type]")
				$(this).find('[name*=discount_id]').attr('name',"product["+j+"][discount_id]")
				$(this).find('[name*=lot_no_line_id]').attr('name',"product["+j+"][lot_no_line_id]")
				$(this).find('[name*=sell_line_note]').attr('name',"product["+j+"][sell_line_note]")
				$(this).find('[name*=variation_id]').attr('name',"product["+j+"][variation_id]")
				$(this).find('[name*=enable_stock]').attr('name',"product["+j+"][enable_stock]")
				$(this).find('[name*=quantity]').attr('name',"product["+j+"][quantity]")
				$(this).find('[name*=unit_price]').attr('name',"product["+j+"][unit_price]")
				$(this).find('[name*=line_discount_amount]').attr('name',"product["+j+"][line_discount_amount]")
				$(this).find('[name*=line_discount_type]').attr('name',"product["+j+"][line_discount_type]")
				$(this).find('[name*=item_tax]').attr('name',"product["+j+"][item_tax]")
				$(this).find('[name*=tax_id]').attr('name',"product["+j+"][tax_id]")
        		$(this).find('[name*=item_further_tax]').attr('name', "product[" + j + "][item_further_tax]")
        		$(this).find('[name*=further_taax_id]').attr('name', "product[" + j + "][further_taax_id]")  
				$(this).find('[name*=salesman_commission_rate]').attr('name', "product[" + j +"][salesman_commission_rate]")
				$(this).find('.pos_unit_price_inc_tax').attr('name',"product["+j+"][unit_price_inc_tax]")
				$(this).find('[name*=warranty_id]').attr('name',"product["+j+"][warranty_id]")
				$(this).find('[name*=brand_id]').attr('name',"product["+j+"][brand_id]")
				j++;
			});
		}
		function remove_row(el) {
		var tr_length = $("#pos_table #tbody tr").length;
		if(tr_length > 1){
			var tr = $(el).closest("tr").remove();
			reIndexTable();
			update_table_sr_number();
			pos_total_row();
		}else{
			alert("At least one row required");
		}		
	}
	function update_table_sr_number() {
    var sr_number = 1;
    $('table#pos_table tbody')
        .find('.sr_number')
        .each(function() {
            $(this).text(sr_number);
            sr_number++;
        });
}

 // on first focus (bubbles up to document), open the menu
 $(document).on('focus', '.select2-selection.select2-selection--single', function (e) {
  $(this).closest(".select2-container").siblings('select:enabled').select2('open');
});

// steal focus during close - only capture once and stop propogation
$('select.select2').on('select2:closing', function (e) {
  $(e.target).data("select2").$selection.one('focus focusin', function (e) {
    e.stopPropagation();
  });
});

// function calculate_unitprice(e){
//         var unitprice = $(e).val();
// 		var discount = $('.row_discount_amount').val();
// 		if(discount != 0.00){
// 			unitprice = unitprice - discount;
// 		}
// 		$(e).closest("tr").find(".calculate_discount").val(unitprice);
//     }

	// Transporter 
		$(".transporter").change(function() {
			var id = $(".transporter").val();
			if (id == defaultOtherTransporterId) {
				$('.vehicles_input').show().attr('name', 'vehicle_no');
				$('.vehicles').hide().removeAttr('name');
			} else {
				$('.vehicles').show().attr('name', 'vehicle_no');
				$('.vehicles_input').hide().removeAttr('name');
			}
			$.ajax({
				type: "GET",
				url: '/get_transporter/' + id,
				success: function(data) {
                    var vehicle_no = $('.transporter').attr('vehicle_no');
                    var selected = '';
					$('.vehicles').html('');
					$.each(data, function(index, value) {
						if (value.vhicle_number != null) {
                            (vehicle_no == value.id) ? selected = "selected" : '';
							$('.vehicles').append('<option value="' + value.id + '" '+ selected +'>' + value.vhicle_number + '</option>');
						} 
					});
				}
			})
		});


    </script>
@endsection
