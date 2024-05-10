@extends('layouts.app')
@section('title', __('lang_v1.add_purchase_return'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
	<br>
	<h1 class="top-heading">@lang('lang_v1.add_purchase_return')
		<span class="pull-right top_trans_no"></span>
	</h1>
</section>

<!-- Main content -->
<style>
	.select2-container--default {
		width: 100% !Important;
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
<section class="content no-print">

	@include('layouts.partials.error')

	{!! Form::open(['url' => action('CombinedPurchaseReturnController@save'), 'method' => 'post', 'id' => 'purchase_return_form', 'files' => true ]) !!}



	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				

				<div class="col-sm-4">
					<div class="form-group">
						<label>Purchase Type</label>
						<div class="input-group">
							<select class="form-control purchase_category get__prefix_dn" name="purchase_category" required>
								<option selected disabled> Select</option>
								@foreach ($purchase_category as $tp)
								<option value="{{$tp->id}}" data-pf="{{$tp->prefix}}"  data-trans_id="{{$tp->control_account_id}}">{{$tp->Type}}</option>
								@endforeach
							</select>

							<span class="input-group-btn">
								<button type="button" class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action('PurchaseOrderController@Purchase_type_partial')}}" data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
							</span>
						</div>

					</div>
				</div>

				<div class="col-sm-3">
					<div class="form-group">
						<label>Product Type</label>
						<select class="form-control purchase_type" name="purchase_type" required>
							<option selected disabled> Select</option>
						</select>
					</div>
				</div>

				<div class="col-sm-2">
					<div class="form-group">
						{!! Form::label('ref_no', __('Transaction No').':') !!}
						<input type="hidden" name="prefix" class="trn_prefix" value="{{$dn_prefix."-"}}">
						<div class="input-group">
							<span class="input-group-addon trn_prefix_addon">
								{{$dn_prefix."-"}}
							</span>
							{!! Form::text('ref_no',$unni, ['class' => 'form-control ref_no']); !!}
						</div>
					</div>
				</div>

            
				<div class="col-sm-4 hide">
					<div class="form-group">
						{!! Form::label('location_id', __('purchase.business_location').':*') !!}
						{!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
					</div>
				</div>

				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('transaction_date', __('Transaction Date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('transaction_date', @format_date('now'),['class' => 'form-control', 'required']); !!}
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="col-sm-4 hide">
					<div class="form-group">
						{!! Form::label('Return Date', __('Return Date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::date('return_date',date('Y-m-d'), ['class' => 'form-control', 'required']); !!}
						</div>
					</div>
				</div>

				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('supplier_id', __('purchase.supplier') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-user"></i>
							</span>
							
							<select name="contact_id" class="form-control select2" onchange="get_ntn_cnic(this)">
								<option disabled selected>Select Supplier</option>
								@foreach($supplier as $s)

								<option value="{{$s->id}}">{{$s->supplier_business_name}}</option>
								@endforeach
							</select>


						</div>
					</div>
				</div>
				

				<div class="col-sm-5">
					<div class="form-group">
						{!! Form::label('Address', __('Address') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-user"></i>
							</span>
							<input type="text" name="address_line" class="form-control contact_address" readonly>
						</div>
					</div>
				</div>
		
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('NtcCnic', __('Ntn Cnic') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-user"></i>
							</span>
							<input type="text" name="ntcnic" class="form-control ntncnic" readonly>
						</div>
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">

						{!! Form::label('Pay_type', __('Pay Type') . ':') !!} @show_tooltip(__('tooltip.pay_term'))
						<!--<div class="input-group">-->
						{!! Form::select('Pay_type',
						['Cash' => __('Cash'),
						'Credit' => __('Credit')],
						null,
						['class' => 'form-control','required','placeholder' => __('messages.please_select'),'id' => 'Pay_type']); !!}
						<!--</div>-->
					</div>
				</div>

				<div class="col-md-3 pay_term">
					<div class="form-group">
						<div class="multi-input">
							{!! Form::label('pay_term_number', __('contact.pay_term') . ':') !!} @show_tooltip(__('tooltip.pay_term'))
							<br />
							{!! Form::number('pay_term_number', null, ['class' => 'form-control width-40 pull-left','placeholder' => __('contact.pay_term')]); !!}

							{!! Form::select('pay_term_type',
							['months' => __('lang_v1.months'),
							'days' => __('lang_v1.days')],
							null,
							['class' => 'form-control width-60 pull-left','placeholder' => __('messages.please_select'),'id' => 'pay_term_type']); !!}
						</div>
					</div>
				</div>
		

				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('Sales Man', __('Sales Man') . ':*') !!}
						<select name="sales_man" class="form-control select2">
							<option selected disabled> Please Select</option>
							@foreach ($sale_man as $s)
							<option value="{{ $s->id }}" {{ $default_sales_man == $s->id ? 'selected' : '' }}>
								{{ $s->supplier_business_name }}
							</option>
							@endforeach
						</select>
					</div>
				</div>

				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('Remarks ', __('Remarks') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-paper-plane"></i>
							</span>
							{!! Form::text('additional_notes','', ['class' => 'form-control']); !!}
						</div>
					</div>
				</div>



				
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('Transporter Name', __('Transporter Name') . ':*') !!}
						<!--<div class="input-group">-->
						<select name="transporter_name" class="form-control transporter" required>
							<option disabled selected>Please Select</option>
							@foreach ($transporter as $transport)
							<option value="{{$transport->id}}">{{$transport->supplier_business_name}}</option>

							@endforeach
						</select>

					</div>
				</div>



				<div class="col-sm-2">
					<div class="form-group">
						{!! Form::label('Vehicle No', __('Vehicle No') . ':*') !!}
						<!--<div class="input-group">-->
						<select name="vehicle_no" class="form-control vehicles" style="width: 105%%">
							<option disabled selected> Please Select Vehicles</option>
						</select>
						<input type="text" class="form-control vehicles_input" style="display: none;" placeholder="vehicle no" />
						<!--</div>-->
					</div>
				</div> 

	
				<div class="col-sm-3">
                    <label>Add & Less Charges</label>
                    <select class="form-control AddAndLess">
                        <option value="add">Add Charges</option>
                        <option value="less">Less Charges</option>
                    </select>
                </div>

                <div class="col-sm-4 add_charges">
                    <label>Add Charges ( + )</label>
                    <div class="input-group" >
                        <input type="number" class="form-control add_charges_val" name="add_charges" style="width:30%" />
                        {!! Form::select('add_charges_acc_dropdown', $accounts, !empty($addless_charges) ? $addless_charges : null, [
                            'class' => 'form-control select2',
                            'placeholder' => 'Select Please',
                            'style' => 'width:70%',
                            'id' => 'add_charges_acc_dropdown',
                        ]) !!}
                    </div>
                </div>

                
                <div class="col-sm-4 less_charges">
                    <label>Less Charges ( - )</label>
                    <div class="input-group" style="width:100%">
                        <input type="number" class="form-control less_charges_val" name="less_charges" style="width:30%" />
                        {!! Form::select('less_charges_acc_dropdown', $accounts, !empty($addless_charges) ? $addless_charges : null, [
                            'class' => 'form-control select2',
                            'placeholder' => 'Select Please',
                            'style' => 'width:70%',
                            'id' => 'less_charges_acc_dropdown',
                        ]) !!}
                    </div>
                </div>
				<div class="clearfix"></div>
				<div class="col-sm-3">
    				<label>Transaction Account</label>
					{!! Form::select('transaction_account', $accounts, $default_account, ['class' => 'form-control select2', 'placeholder' => 'Select Please']) !!}
    			</div>

				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('document', __('purchase.attach_document') . ':') !!}
						{!! Form::file('document', ['id' => 'upload_document', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
			
					</div>
				</div>
			</div>
		</div>
	</div> <!--box end-->
	<div class="box box-solid">
		<div class="box-header">
			<h3 class="box-title">{{ __('stock_adjustment.search_products') }}</h3>
		</div>
		<div class="box-body">
			<div class="row">
				
				<div class="col-sm-2">
					<div class="form-group">
						<button tabindex="-1" type="button" class="btn btn-primary btn-modal" data-href="{{action('ProductController@quickAdd')}}" data-container=".quick_add_product_modal"><i class="fa fa-plus"></i> @lang( 'product.add_new_product' ) </button>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<input type="hidden" id="product_row_index" value="0">
					<input type="hidden" id="total_amount" name="final_total" value="0">
					<div class="table-responsive" style="overflow: scroll;">
						<table class="table table-bordered table-th-green table-striped table-condensed" id="purchase_return_product_table" style="width: 130%; max-width: 130%;">
							<thead>
								<tr>
									<th style="text-align: center;"><i class="fa fa-trash" aria-hidden="true"></i></th>
									<th>
										S#
									</th>
									<th class="hide">
										Store
									</th>
									<th>
									Sku
									</th>
									<th width=20%>
										Products
									</th>
									<th class="text-center" width=8%>
										Brand
									</th>
									<th width=8%>
										Product Description
									</th>
									<th width=4%>
										UOM
									</th>
								
								
									@if(session('business.enable_lot_number'))
									<th>
										@lang('lang_v1.lot_number')
									</th>
									@endif
									@if(session('business.enable_product_expiry'))
									<th>
										@lang('product.exp_date')
									</th>
									@endif
									<th class="text-center">
										QTY
									</th>
									<th class="text-center">
										Rate
									</th>
									<th class="hide">Discount</th>
									<th class="text-center">
										Amount
									</th>
									<th>Sale Tax</th>
									<th>Further Tax</th>
									<th style="width: 2%;">Salesman Commission</th>
									<th class="hide">Sale Tax Amount</th>
									<th>Net Amount After Tax</th>

								</tr>
							</thead>
							<tbody id="tbody">
								<tr class="product_row">
									<td><button class="btn btn-danger remove" type="button" onclick="remove_row(this)"><i class="fa fa-trash" aria-hidden="true"></i></button>
									</td>
									<td class="sr_number"></td>
									<td class="hide">
										<!--<input type="text" name="products[0][store]" class="form-control" value="">-->
										<select name="products[0][store]" class="form-control" style="width:100px">
											<option selected disabled>Please Select</option>
											@foreach($store as $s)
											<option value="{{$s->id}}">{{$s->name}}</option>
											@endforeach
										</select>
									</td>
									<td> <input type="text" name="products[0][item_code]" class="form-control product_code" readonly value="" style="width:100px">
									</td>
									
									<td>
										{!! Form::select('products[0][product_id]', $product,null, ['class' => 'form-control products_change select2 abc','placeholder'=>"Search Your Product",'required','id' => 'search_product','Style' => 'width:200px;','onchange'=>"get_product_code(this)"]); !!}
										<input type="hidden" name="gross_weight" class="gross__weight">
										<input type="hidden" name="net_weight" class="net__weight">
                                        <input type="hidden" name="transporter_rate" class="transporter_rate" />
                                        <input type="hidden" name="contractor_rate" class="contractor_rate" />
									</td>
									<td>
										{!! Form::select('products[0][brand_id]', ['' => 'Select'] + $brand->pluck('name','id')->all(), null, ['class' => 'form-control select2','id' =>'brand_id']) !!}
										</td>
									<td> {!! Form::textarea('products[0][item_description]',null, ['class' => 'form-control ','rows'=>'1','placeholder'=>"descrition" ]); !!}</td>
									<td> <input type="text" name="products[0][uom]" class="form-control uom" readonly value="" style="width:60px"></td>
								
									@if(session('business.enable_lot_number'))
									<td>
										<input type="text" name="products[0][lot_number]" class="form-control" value="">
									</td>
									@endif
									@if(session('business.enable_product_expiry'))
									<td>
										<input type="text" name="products[0][exp_date]" class="form-control expiry_datepicker" value="" readonly>
									</td>
									@endif
									<td>
										{{-- <input type="hidden" name="products[0][product_id]" class="form-control product_id" value=""> --}}

										<input type="hidden" value="" name="products[0][variation_id]">

										<input type="hidden" value="" name="products[0][enable_stock]">

										@if(!empty($edit))
										<input type="hidden" value="" name="products[0][purchase_line_id]">
										@php
										// $qty = $product->quantity_returned;
										// $purchase_price = $product->purchase_price;
										@endphp
										@else
										@php
										$qty = 1;
										// $purchase_price = $product->last_purchased_price;
										@endphp
										@endif

										<input type="text" class="form-control product_quantity input_quantity" value="" required name="products[0][quantity]" style="width:100px">

									</td>


									<td>
										<input type="text" name="products[0][pricee]" class="form-control product_unit_price input_number" required value="" style="width:100px">
									</td>
									<td class="hide">
										<input type="text" name="products[0][discount_percent]" value="0" class="form-control discount input_number" required id="discount" value="" style="width:100px">
									</td>
									<td>
										<input type="text" readonly class="form-control product_line_total" id="subtotal" value="">
									</td>
									<td>

										<div class="input-group">
                                            <select name="products[0][tax_id]"
                                                class="form-control select2 input-sm purchase_line_tax_id"
                                                placeholder="'Please Select'" onchange="calculate_discount(this)" >
                                                <option value="0" data-tax_amount="0">@lang('lang_v1.none')</option>
                                                @foreach ($taxes as $tax_ratee)
                                                    <option value="{{ $tax_ratee->id }}"
                                                        data-tax_amount="{{ $tax_ratee->amount }}">{{ $tax_ratee->name }}
                                                    </option>
                                                @endforeach

                                            </select>
                                            {!! Form::hidden('products[0][item_tax]', 0, ['class' => 'purchase_product_unit_tax']) !!}
                                            <span class="input-group-addon purchase_product_unit_tax_text">
                                                0.00</span>
									</td>
									
									<td class="text-center">
                						<input type="hidden" class="form-control further_tax_hidden" name="products[0][item_further_tax]" />
                						<div class="input-group">
                        						<select name="products[0][further_taax_id]" class="form-control select2 input-sm further_tax" id="further_tax" placeholder="Please Select">
                        							<option value="0" data-rate="0">NONE</option>
                        							@foreach($further_taxes as $tax)
                        								<option value="{{ $tax->id }}" data-rate="{{ $tax->amount }}">{{ $tax->name }}</option>
                        							@endforeach
                        						</select>
                        						{!! Form::hidden('products[0][further_item_tax]', 0, ['class' => 'purchase_product_unit_further_tax']); !!}
                								<span class="input-group-addon purchase_product_unit_further_tax_text">0.00</span>
            								</div>
                					</td>
                					<td>
                                        <input type="number" name="products[0][salesman_commission_rate]" class="form-control salesman_commission_rate"/>
                					</td>
									
									
									<td class="hide">
										<input type="text" name="products[0][purchase_price_inc_tax]" class="form-control saletaxamount input_number" value="" readonly>
									</td>
									<td>
										<input type="text" name="products[0][net_amount]" class="form-control product_line_total_net input_number" value="">
									</td>
								</tr>

								<tr class="product_row">
									<td><button class="btn btn-danger remove" type="button" onclick="remove_row(this)"><i class="fa fa-trash" aria-hidden="true"></i></button>
									</td>
									<td class="sr_number"></td>
									<td class="hide">
										<!--<input type="text" name="products[1][store]" class="form-control" value="">-->
										<select name="products[1][store]" class="form-control" style="width:100px">
											<option selected disabled>Please Select</option>
											@foreach($store as $s)
											<option value="{{$s->id}}">{{$s->name}}</option>
											@endforeach
										</select>
									</td>
									<td> <input type="text" name="products[1][item_code]" class="form-control product_code" readonly value="" style="width:100px"></td>
									<td>
										{!! Form::select('products[1][product_id]', $product,null, ['class' => 'form-control products_change select2 abc','placeholder'=>"Search Your Product",'id' => 'search_product','Style' => 'width:200px;','onchange'=>"get_product_code(this)"]); !!}
										<input type="hidden" name="gross_weight" class="gross__weight">
										<input type="hidden" name="net_weight" class="net__weight">
                                <input type="hidden" name="transporter_rate" class="transporter_rate" />
                                <input type="hidden" name="contractor_rate" class="contractor_rate" />
									</td>
									
									<td>    {!! Form::select('products[1][brand_id]', ['' => 'Select'] + $brand->pluck('name','id')->all(), null, ['class' => 'form-control select2','id' =>'brand_id']) !!}</td>
									
									<td> {!! Form::textarea('products[1][item_description]',null, ['class' => 'form-control ','rows'=>'1','placeholder'=>"descrition" ]); !!}</td>
									<td> <input type="text" name="products[1][uom]" class="form-control uom" readonly value="" style="width:60px"></td>

								
									@if(session('business.enable_lot_number'))
									<td>
										<input type="text" name="products[1][lot_number]" class="form-control" value="">
									</td>
									@endif
									@if(session('business.enable_product_expiry'))
									<td>
										<input type="text" name="products[1][exp_date]" class="form-control expiry_datepicker" value="" readonly>
									</td>
									@endif
									<td>
										{{-- <input type="hidden" name="products[1][product_id]" class="form-control product_id" value=""> --}}

										<input type="hidden" value="" name="products[1][variation_id]">

										<input type="hidden" value="" name="products[1][enable_stock]">

										@if(!empty($edit))
										<input type="hidden" value="" name="products[1][purchase_line_id]">
										@php
										// $qty = $product->quantity_returned;
										// $purchase_price = $product->purchase_price;
										@endphp
										@else
										@php
										$qty = 1;
										// $purchase_price = $product->last_purchased_price;
										@endphp
										@endif

										<input type="text" class="form-control product_quantity input_number input_quantity" value="" name="products[1][quantity]" style="width:100px">

									</td>


									<td>
										<input type="text" name="products[1][pricee]" class="form-control product_unit_price input_number" value="" style="width:100px">
									</td>
									<td class="hide">
										<input type="text" name="products[1][discount_percent]" value="0" class="form-control discount input_number" id="discount" value="" style="width:100px">
									</td>
									<td>
										<input type="text" readonly class="form-control product_line_total" id="subtotal" value="">
									</td>
									<td>
									
										<div class="input-group">
                                            <select name="products[1][tax_id]"
                                                class="form-control select2 input-sm purchase_line_tax_id"
                                                placeholder="'Please Select'" onchange="calculate_discount(this)" >
                                                <option value="0" data-tax_amount="0">@lang('lang_v1.none')</option>
                                                @foreach ($taxes as $tax_ratee)
                                                    <option value="{{ $tax_ratee->id }}"
                                                        data-tax_amount="{{ $tax_ratee->amount }}">{{ $tax_ratee->name }}
                                                    </option>
                                                @endforeach

                                            </select>
                                            {!! Form::hidden('products[1][item_tax]', 0, ['class' => 'purchase_product_unit_tax']) !!}
                                            <span class="input-group-addon purchase_product_unit_tax_text">
                                                0.00</span>
									</td>
									
									<td class="text-center">
                						<input type="hidden" class="form-control further_tax_hidden" name="products[1][item_further_tax]" />
                						<div class="input-group">
                        						<select name="products[1][further_taax_id]" class="form-control select2 input-sm further_tax" id="further_tax" placeholder="Please Select">
                        							
                        							<option value="0" data-rate="0">NONE</option>
                        							@foreach($further_taxes as $tax)
                        								<option value="{{ $tax->id }}" data-rate="{{ $tax->amount }}">{{ $tax->name }}</option>
                        							@endforeach
                        							
                        						</select>
                        						{!! Form::hidden('products[1][further_item_tax]', 0, ['class' => 'purchase_product_unit_further_tax']); !!}
                								<span class="input-group-addon purchase_product_unit_further_tax_text">0.00</span>
            								</div>
                					</td>
                					<td>
                                        <input type="number" name="products[1][salesman_commission_rate]" class="form-control salesman_commission_rate"/>
                					</td>
									
									
									<td class="hide">
										<input type="text" name="products[1][purchase_price_inc_tax]" class="form-control saletaxamount input_number" value="" readonly>
									</td>
									<td>
										<input type="text" name="products[1][net_amount]" class="form-control product_line_total_net input_number" value="">
									</td>

								</tr>


								<tr class="product_row">
									<td><button class="btn btn-danger remove" type="button" onclick="remove_row(this)"><i class="fa fa-trash" aria-hidden="true"></i></button>
									</td>
									<td class="sr_number"></td>
									<td class="hide">
										<!--<input type="text" name="products[2][store]" class="form-control" value="">-->
										<select name="products[2][store]" class="form-control" style="width:100px">
											<option selected disabled>Please Select</option>
											@foreach($store as $s)
											<option value="{{$s->id}}">{{$s->name}}</option>
											@endforeach
										</select>
									</td>
									<td> <input type="text" name="products[2][item_code]" class="form-control product_code" readonly value="" style="width:100px"></td>
								
									<td>
										{!! Form::select('products[2][product_id]', $product,null, ['class' => 'form-control products_change select2 abc','placeholder'=>"Search Your Product",'id' => 'search_product','Style' => 'width:200px;','onchange'=>"get_product_code(this)"]); !!}
										<input type="hidden" name="gross_weight" class="gross__weight">
										<input type="hidden" name="net_weight" class="net__weight">
                                <input type="hidden" name="transporter_rate" class="transporter_rate" />
                                <input type="hidden" name="contractor_rate" class="contractor_rate" />
									</td>
									<td>    {!! Form::select('products[2][brand_id]', ['' => 'Select'] + $brand->pluck('name','id')->all(), null, ['class' => 'form-control select2','id' =>'brand_id']) !!}</td>
									<td> {!! Form::textarea('products[2][item_description]',null, ['class' => 'form-control ','rows'=>'1','placeholder'=>"descrition" ]); !!}</td>
									<td> <input type="text" name="products[2][uom]" class="form-control uom" readonly value="" style="width:60px"></td>
								
									@if(session('business.enable_lot_number'))
									<td>
										<input type="text" name="products[2][lot_number]" class="form-control" value="">
									</td>
									@endif
									@if(session('business.enable_product_expiry'))
									<td>
										<input type="text" name="products[2][exp_date]" class="form-control expiry_datepicker" value="" readonly>
									</td>
									@endif
									<td>
										{{-- <input type="hidden" name="products[2][product_id]" class="form-control product_id" value=""> --}}

										<input type="hidden" value="" name="products[2][variation_id]">

										<input type="hidden" value="" name="products[2][enable_stock]">

										@if(!empty($edit))
										<input type="hidden" value="" name="products[2][purchase_line_id]">
										@php
										// $qty = $product->quantity_returned;
										// $purchase_price = $product->purchase_price;
										@endphp
										@else
										@php
										$qty = 1;
										// $purchase_price = $product->last_purchased_price;
										@endphp
										@endif

										<input type="text" class="form-control product_quantity input_number input_quantity" value="" name="products[2][quantity]" style="width:100px">

									</td>


									<td>
										<input type="text" name="products[2][pricee]" class="form-control product_unit_price input_number" value="" style="width:100px">
									</td>
									<td class="hide">
										<input type="text" name="products[2][discount_percent]" class="form-control discount input_number" id="discount" value="" style="width:100px">
									</td>
									<td>
										<input type="text" readonly class="form-control product_line_total" id="subtotal" value="">
									</td>
									<td>
										
										<div class="input-group">
                                            <select name="products[2][tax_id]"
                                                class="form-control select2 input-sm purchase_line_tax_id"
                                                placeholder="'Please Select'" onchange="calculate_discount(this)" >
                                                <option value="0" data-tax_amount="0">@lang('lang_v1.none')</option>
                                                @foreach ($taxes as $tax_ratee)
                                                    <option value="{{ $tax_ratee->id }}"
                                                        data-tax_amount="{{ $tax_ratee->amount }}">{{ $tax_ratee->name }}
                                                    </option>
                                                @endforeach

                                            </select>
                                            {!! Form::hidden('products[2][item_tax]', 0, ['class' => 'purchase_product_unit_tax']) !!}
                                            <span class="input-group-addon purchase_product_unit_tax_text">
                                                0.00</span>
									</td>
									
									<td class="text-center">
                						<input type="hidden" class="form-control further_tax_hidden" name="products[2][item_further_tax]" />
                						<div class="input-group">
                        						<select name="products[2][further_taax_id]" class="form-control select2 input-sm further_tax" id="further_tax" placeholder="Please Select">
                        							<option value="0" data-rate="0">NONE</option>
                        							@foreach($further_taxes as $tax)
                        								<option value="{{ $tax->id }}" data-rate="{{ $tax->amount }}">{{ $tax->name }}</option>
                        							@endforeach
                        						</select>
                        						{!! Form::hidden('products[2][further_item_tax]', 0, ['class' => 'purchase_product_unit_further_tax']); !!}
                								<span class="input-group-addon purchase_product_unit_further_tax_text">0.00</span>
            								</div>
                					</td>
                					<td>
                                        <input type="number" name="products[2][salesman_commission_rate]" class="form-control salesman_commission_rate"/>
                					</td>
									
									
									<td class="hide">
										<input type="text" name="products[2][purchase_price_inc_tax]" class="form-control saletaxamount input_number" value="" readonly>
									</td>
									<td>
										<input type="text" name="products[2][net_amount]" class="form-control product_line_total_net input_number" value="">
									</td>

								</tr>



							</tbody>
						</table>
					</div>
				</div>
				<hr>
				<button class="btn btn-md btn-primary addBtn" type="button" onclick="add_row(this)" style="padding: 0px 5px 2px 5px;margin-left:30px">
					Add Row</button>

				<div class="clearfix"></div>

				<div class="col-md-4 hide">
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
				</div>
				<div class="col-md-8 total_data">
					<div class="pull-right"><b>@lang('stock_adjustment.total_amount'):</b> <span id="total_return">0.00</span></div>
					<br>
					<div class="pull-right"><b>Total Gross Weight:</b> <span id="total_gross__weight">0.00</span></div>
					<br>
					<div class="pull-right"><b>Total Net Weight:</b> <span id="total_net__weight">0.00</span></div>
							<input type="hidden" name="total_gross__weight" class="total_gross__weight"/>
							<input type="hidden" name="total_net__weight" class="total_net__weight"/>
    						<input type="hidden" id="total_sale_tax" name="total_sale_tax">
    						<input type="hidden" id="total_further_tax" name="total_further_tax"> 
    						<input type="hidden" id="total_salesman_commission" name="total_salesman_commission">  
    						<input type="hidden" id="total_transporter_rate" name="total_transporter_rate">
    						<input type="hidden" id="total_contractor_rate" name="total_contractor_rate">
				</div>


			</div>

			


			<div class="col-sm-4">
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
			<div class="col-sm-4">
				{!! Form::label('contractor', 'Contractor' . ':*') !!}
				<select name="contractor" class="form-control contractor">
					<option disabled selected>Please Select</option>
					@foreach ($contractor as $c)
					<option value="{{$c['id']}}" {{ isset($default_contractor) && $default_contractor == $c['id'] ? 'selected' : '' }}>{{ $c['supplier_business_name'] }}</option>
					@endforeach
				</select>
			</div>
			<div class="col-sm-12">
				<div class="form-group" id="TandC" style="display:none;">
					{!! Form::label('tandc_title',__('Terms & Conditions')) !!}
					{!! Form::textarea('tandc_title', null, ['class' => 'form-control name','id'=>'product_description1','rows' => 3]); !!}
				</div>
			</div>
		</div>

	</div> <!--box end-->

	<div class="col-sm-12 fixed-button">
		<input type="hidden" name="submit_type" id="submit_type">
		<div class="text-center">
			<div class="btn-group" >
			  
				<button type="submit" name='save' value="save_n_add_another" id="submit_return_form"
					class="btn-big btn-primary submit_return_form">Save & Next</button>
					
				<button type="submit" value="submit"
					class="btn-big btn-primary submit_return_form" id="submit_return_form">Save & Close</button>
				<button class="btn-big btn-danger" type="button" onclick="window.history.back()">Close</button>
			</div>
		</div>
	</div>
	{!! Form::close() !!}
</section>

<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	@stop
	@section('javascript')

	<script src="{{ asset('js/purchase_return.js?v=' . $asset_v) }}"></script>
	<script type="text/javascript">
		$(document).ready(function() {

			$(document).on('change', '.product_quantity, .product_unit_price', function(){
				$(this).closest("tr").find('.saletax').trigger('keyup');
			})
			
			$(document).on('change', '.product_quantity, .product_unit_price, .saletax, .salesman_commission_rate', function(){
				$(this).closest("tr").find('.further_tax').trigger('change');
			})
			
			$(document).on('change', '.further_tax', function() {
			    var total       = 0;
			    var rate        = 0;
			    var unit_price  = 0;
			    var quantity    = 0;
                var line_total  = 0;
                var saletaxamount = 0;
                
                rate = $(this).find('option:selected').data('rate');
                quantity = $(this).closest('tr').find('.product_quantity').val();
                unit_price = $(this).closest('tr').find('.product_unit_price').val();
                
                total = parseFloat(quantity) * parseFloat(unit_price);
                var furthertax = parseFloat(rate) * parseFloat(total) / 100;
                $(this).closest('tr').find('.purchase_product_unit_further_tax').val(furthertax);
                $(this).closest('tr').find('.purchase_product_unit_further_tax_text').html(furthertax);
                
                saletaxamount = $(this).closest('tr').find('.saletaxamount').val();
                $(this).closest('tr').find('.product_line_total_net').val(parseFloat(total) + parseFloat(saletaxamount) + parseFloat(furthertax));
				update_table_total();
            });


			if ($('textarea#product_description1').length > 0) {
				tinymce.init({
					selector: 'textarea#product_description1',
					height: 250
				});
			}
			$("#TCS").change(function() {
				var id = $("#TCS").val();
				// alert(id);
				$.ajax({
					type: "GET",
					url: '/get_term/' + id,
					// dataType: "text"
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
			// on first focus (bubbles up to document), open the menu
			$(document).on('focus', '.select2-selection.select2-selection--single', function(e) {
				$(this).closest(".select2-container").siblings('select:enabled').select2('open');
			});

			// steal focus during close - only capture once and stop propogation
			$('select.select2').on('select2:closing', function(e) {
				$(e.target).data("select2").$selection.one('focus focusin', function(e) {
					e.stopPropagation();
				});
			});
		});
		$(document).on('keyup', '#discount', function() {
			var value = $(this).val();
			var total = $("#subtotal").val();
			var total = $("#subtotal").val();
			product_quantity

			var subtotal = total - value;
			$('#subtotal').val(subtotal);
			// alert(subtotal);


		});
		__page_leave_confirmation('#purchase_return_form');

		// row add
		function add_row(el) {
			// alert("asdgfsadf");
			$('#purchase_return_product_table tbody tr').each(function() {
				$(this).find('#search_product, .further_tax,#brand_id,.purchase_line_tax_id').select2('destroy')
			})
			var tr = $("#purchase_return_product_table tbody tr:last").clone();
			console.log(tr);
			$("#purchase_return_product_table tbody tr:last").after(tr);


			reIndexTable();
			update_table_sr_number();

		}

		function reIndexTable() {
			var j = 0;
			$('#purchase_return_product_table tbody tr').each(function() {
				$(this).find('#search_product, .further_tax,#brand_id,.purchase_line_tax_id').select2()
				$(this).attr('id', j)
				$(this).find('[name*=store]').attr('name', "products[" + j + "][store]")
				$(this).find('[name*=net_amount]').attr('name', "products[" + j + "][net_amount]")

				$(this).find('[name*=pricee]').attr('name', "products[" + j + "][pricee]")
				$(this).find('[name*=uom]').attr('name', "products[" + j + "][uom]")
				$(this).find('[name*=enable_stock]').attr('name', "products[" + j + "][enable_stock]")
				$(this).find('[name*=item_code]').attr('name', "products[" + j + "][item_code]")
				$(this).find('[name*=item_description]').attr('name', "products[" + j + "][item_description]")
				$(this).find('[name*=quantity]').attr('name', "products[" + j + "][quantity]")
				$(this).find('[name*=unit_price]').attr('name', "products[" + j + "][unit_price]")
				$(this).find('[name*=product_id]').attr('name', "products[" + j + "][product_id]")
				$(this).find('[name*=brand_id]').attr('name', "products[" + j + "][brand_id]")
				$(this).find('[name*=pp_without_discount]').attr('name', "products[" + j + "][pp_without_discount]")
				$(this).find('[name*=discount_percent]').attr('name', "products[" + j + "][discount_percent]")
				$(this).find('[name*=purchase_price]').attr('name', "products[" + j + "][purchase_price]")
				$(this).find('[name*=purchase_price_inc_tax]').attr('name', "products[" + j + "][purchase_price_inc_tax]")
				$(this).find('[name*=purchase_line_tax_id]').attr('name', "products[" + j + "][purchase_line_tax_id]")
				$(this).find('[name*=item_further_tax]').attr('name', "products[" + j + "][item_further_tax]")
				$(this).find('[name*=further_taax_id]').attr('name', "products[" + j + "][further_taax_id]")  
				$(this).find('[name*=salesman_commission_rate]').attr('name', "products[" + j + "][salesman_commission_rate]")
				$(this).find('[name*=profit_percent]').attr('name', "products[" + j + "][profit_percent]")
				$(this).find('[name*=item_tax]').attr('name', "products[" + j + "][item_tax]")
				$(this).find('[name*=variation_id]').attr('name', "products[" + j + "][variation_id]")
				$(this).find('[name*=purchase_line_id]').attr('name', "products[" + j + "][purchase_line_id]")
				$(this).find('[name*=product_unit_id]').attr('name', "products[" + j + "][product_unit_id]")
				j++;
			});
		}

		function remove_row(el) {
			var tr_length = $("#purchase_return_product_table #tbody tr").length;
			if (tr_length > 1) {
				var tr = $(el).closest("tr").remove();
				reIndexTable();
				update_table_sr_number();
			} else {
				alert("At least one row required");
			}
			update_table_total();
		}

		function update_table_sr_number() {
			var sr_number = 1;
			$('table#purchase_return_product_table tbody')
				.find('.sr_number')
				.each(function() {
					$(this).text(sr_number);
					sr_number++;
				});
		}

		function get_product_code(el) {

			var terms = $(el).val();
            var transporter = $('.transporter').val(); 
            var contractor = $('.contractor').val(); 
			$.getJSON(
				'/purchases/get_products', {
					term: terms,
            		transporter: transporter,
            		contractor: contractor
				},
				function(data) {
					$(this).val(data[0].unit.actual_name);
					console.log(data[0]);
					$(el).closest("tr").find(".product_code").val(data[0].sku);
				// 	$(el).closest("tr").find(".uom").val(data[0].unit.actual_name);
					 if(data[0].unit.is_purchase_unit ==1)
                    {
                        $(el).closest("tr").find(".uom").val(data[0].shipping_custom_field_1);
                    }else{
                        $(el).closest("tr").find(".uom").val(data[0].unit.actual_name);
                        
                    }
					
					
					
            		$(el).closest("tr").find(".transporter_rate").val(data[0].transporter_rate);
            		$(el).closest("tr").find(".contractor_rate").val(data[0].contractor_rate);
					$(el).closest("tr").find("#item_brand").val(data[0]?.brand?.name || '');
					
					$(el).closest("tr").find(".gross__weight").val(data[0].weight);
					$(el).closest("tr").find(".net__weight").val(data[0].product_custom_field1);
					update_table_total();
				});
		}


		$(".transporter").change(function() {
			// alert("Sa");
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
				// 	console.log(data)
					$('.vehicles').html('');

					$.each(data, function(index, value) {
						// APPEND OR INSERT DATA TO SELECT ELEMENT.
						//   console.log(value.id);
						if (value.vhicle_number != null) {
							$('.vehicles').append('<option value="' + value.id + '">' + value.vhicle_number + '</option>');
						} else {

						}

					});

					// $('#bk_qty').val(data['booking_qnty']);

				}
			})
			 // }

		});



		function pad(str, max) {
			str = str.toString();
			return str.length < max ? pad("0" + str, max) : str;
		}

		$(document).on('change', '.ref_no', function() {

			var ref_no = $('.ref_no').val();

			var ref_no_lead = pad(ref_no, 4);

			$('.ref_no').val(ref_no_lead);
		})

		$(document).ready(function() {
			var ref_no = $('.ref_no').val();
			var ref_no_lead = pad(ref_no, 4);
			$('.ref_no').val(ref_no_lead);
		})
	</script>
	@endsection