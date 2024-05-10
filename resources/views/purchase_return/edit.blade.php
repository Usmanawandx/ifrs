@extends('layouts.app')
@section('title', __('lang_v1.edit_purchase_return'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
	<br>
	<h1 class="top-heading">@lang('lang_v1.edit_purchase_return')
		<span class="pull-right top_trans_no">{{$purchase_return->ref_no}}</span>
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
	{!! Form::open(['url' => action('CombinedPurchaseReturnController@update'), 'method' => 'post', 'id' => 'purchase_return_form', 'files' => true ]) !!}

	
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
			

				<div class="col-sm-4">
					<div class="form-group">
						<label>Purchase Type</label>
						<select class="form-control purchase_category purchase__type_edit no-pointer-events" name="purchase_category" readonly required>
							<option selected disabled> Select</option>
							@foreach ($purchase_category as $tp)

							@if($tp->id == $purchase_return->purchase_category)
							<option value="{{$tp->id}}" data-pf="{{$tp->prefix}}" data-trans_id="{{$tp->control_account_id}}" selected>{{$tp->Type}}</option>
							@else
							<option value="{{$tp->id}}" data-pf="{{$tp->prefix}}" data-trans_id="{{$tp->control_account_id}}">{{$tp->Type}}</option>
							@endif

							@endforeach
						</select>
					</div>
				</div>


				<div class="col-sm-3">
					<div class="form-group">
						<label>Product Type</label>
						<select class="form-control purchase_type no-pointer-events" name="purchase_type" readonly id="is_purchase_order_dd">
							<option selected disabled> Select</option>
							@foreach ($p_type as $tp)
							@if($tp->id == $purchase_return->purchase_type)
							<option value="{{$tp->id}}" selected data-purchasetype="{{ $tp->purchase_type }}">{{$tp->name}}</option>
							@else
							<option value="{{$tp->id}}" data-purchasetype="{{ $tp->purchase_type }}">{{$tp->name}}</option>
							@endif
							@endforeach
						</select>

					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						{!! Form::label('ref_no', __('Transaction No').':') !!}
						{!! Form::text('ref_no', $purchase_return->ref_no, ['class' => 'form-control tr_no__edit','readonly']); !!}
					</div>
				</div>

				<div class="col-sm-4 hide">
					<div class="form-group">
						{!! Form::label('location_id', __('purchase.business_location').':*') !!}
						{!! Form::select('location_id', $business_locations, $purchase_return->location_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
					</div>
				</div>

				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('transaction_date', __('Posting Date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('transaction_date', @format_datetime($purchase_return->transaction_date), ['class' => 'form-control', 'required']); !!}
						</div>
					</div>
				</div>
				<div class="col-sm-4 hide">
					<div class="form-group">
						{!! Form::label('Return Date', __('Return Date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::date('return_date', $purchase_return->return_date, ['class' => 'form-control', 'required']); !!}
						</div>
					</div>
				</div>

				<div class="col-sm-4">
					<div class="form-group">
						<input type="hidden" name="purchase_return_id" value="{{$purchase_return->id}}">
						<input type="hidden" id="location_id" value="{{$purchase_return->location_id}}">
						{!! Form::label('supplier_id', __('purchase.supplier') . ':*') !!}

						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-user"></i>
							</span>
							<select name="contact_id" class="form-control select2 supplier" onchange="get_ntn_cnic(this)">
								@foreach ($supplier as $supplier)
								@if($purchase_return->contact_id == $supplier->id)
								<option value="{{$supplier->id}}" selected>{{$supplier->supplier_business_name}}</option>
								@else
								<option value="{{$supplier->id}}">{{$supplier->supplier_business_name}}</option>
								@endif
								@endforeach
							</select>
							{{-- {!! Form::select('contact_id', [ $purchase_return->contact_id => $purchase_return->contact->name], $purchase_return->contact_id, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'supplier_id']); !!} --}}
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
						<div class="multi-input">
							{!! Form::label('Pay_type', __('Pay Type') . ':') !!} @show_tooltip(__('tooltip.pay_term'))
							<br />
							<select name="pay_type" class="form-control" id="Pay_type">
								<option>Please Select</option>
								@if($purchase_return->pay_type =="Cash")
								<option value="Cash" selected>Cash</option>
								<option value="Credit"> Credit</option>
								@else
								<option value="Credit" selected> Credit</option>
								<option value="Cash">Cash</option>


								@endif
							</select>

						</div>
					</div>
				</div>

				
				<div class="col-md-3 pay_term">
					<div class="form-group">
						<div class="multi-input">
							{!! Form::label('pay_term_number', __('contact.pay_term') . ':') !!} @show_tooltip(__('tooltip.pay_term'))
							<br />
							{!! Form::number('pay_term_number', $purchase_return->pay_term_number, ['class' => 'form-control width-40 pull-left','placeholder' => __('contact.pay_term')]); !!}

							{!! Form::select('pay_term_type',
							['months' => __('lang_v1.months'),
							'days' => __('lang_v1.days')],
							$purchase_return->pay_term_type,
							['class' => 'form-control width-60 pull-left','placeholder' => __('messages.please_select'),'id' => 'pay_term_type']); !!}
						</div>
					</div>
				</div>


				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('sales Man ', __('Sales Man') . ':*') !!}
						<!--<div class="input-group">-->

						<select name="sales_man" class="form-control select2">
							<option selected disabled> Please Select</option>


							@foreach ($sale_man as $s)
							@if($purchase_return->sales_man == $s->id)

							<option value="{{$s->id}}" selected>{{$s->supplier_business_name}}</option>
							@else
							<option value="{{$s->id}}">{{$s->supplier_business_name}}</option>
							@endif
							@endforeach

						</select>
						<!--</div>-->
					</div>
				</div>

				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('Remarks ', __('Remarks') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-paper-plane"></i>
							</span>
							{!! Form::text('additional_notes',$purchase_return->additional_notes, ['class' => 'form-control']); !!}
						</div>
					</div>
				</div>



				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('Transporter Name', __('Transporter Name') . ':*') !!}
						<!--<div class="input-group">-->
					
						<select name="transporter_name" class="form-control transporter" vehicle_no="{{ $purchase_return->vehicle_no }}">
							@foreach ($transporter as $transporter)
							@if($transporter->id == $purchase_return->transporter_name)
							<option value="{{$transporter->id}}" selected>{{$transporter->supplier_business_name}}</option>
							@else
							<option value="{{$transporter->id}}">{{$transporter->supplier_business_name}}</option>
							@endif
							@endforeach
						</select>

					</div>
				</div>


				<div class="col-sm-2">
					<div class="form-group">
						{!! Form::label('Vehicle No', __('Vehicle No') . ':*') !!}
						<div class="vehicles_parent">
							<select name="vehicle_no" class="form-control vehicles">
								<option>Please select Vehicle </option>
								@foreach ($vehicles as $vehicle)
									@if ($vehicle->id == $purchase_return->vehicle_no)
										<option value="{{ $vehicle->id }}" selected>{{ $vehicle->vhicle_number }}</option>
									@else
										<option value="{{ $vehicle->id }}">{{ $vehicle->vhicle_number }}</option>
									@endif
								@endforeach
							</select>
							<input type="text" class="form-control vehicles_input" value="{{ $purchase_return->vehicle_no }}"
								style="display: none;" placeholder="vehicle no" />
						</div>
					</div>
				</div>


				<div class="col-sm-3">
                    <label>Add & Less Charges</label>
                    <select class="form-control AddAndLess">
                        <option value="add" {{ !empty($purchase_return->add_charges) ? 'selected' : '' }}>Add Charges</option>
                        <option value="less" {{ !empty($purchase_return->less_charges) ? 'selected' : '' }}>Less Charges</option>
                    </select>
                </div>
              
                <div class="col-sm-4 add_charges">
                    <label>Add Charges ( + )</label>
                    <div class="input-group" style="width:100%">
                        <input type="number" class="form-control add_charges_val" name="add_charges" value="{{ $purchase_return->add_charges }}"
                            style="width:30%" />
                        {!! Form::select('add_charges_acc_dropdown', $accounts, $purchase_return->add_charges_acc_id, [
                            'class' => 'form-control select2',
                            'placeholder' => 'Select Please',
                            'style' => 'width:70%',
                            'id' => 'add_charges_acc_dropdown',
                        ]) !!}
                    </div>
                </div>


                <div class="col-sm-4 less_charges" style="display:none">
                    <label>Less Charges ( - )</label>
                    <div class="input-group" style="width:100%">
                        <input type="number" class="form-control less_charges_val" name="less_charges"
                            value="{{ $purchase_return->less_charges }}" style="width:30%" />
                        {!! Form::select('less_charges_acc_dropdown', $accounts, $purchase_return->less_charges_acc_id, [
                            'class' => 'form-control select2',
                            'placeholder' => 'Select Please',
                            'style' => 'width:70%',
                            'id' => 'less_charges_acc_dropdown',
                        ]) !!}
                    </div>
                </div>
				{{-- {{dd($transaction_account)}} --}}

				<div class="clearfix"></div>
				<div class="col-sm-3">
    				<label>Transaction Account</label>
        			{!! Form::select('transaction_account', $accounts, $purchase_return->transaction_account,
        			['class' => 'form-control select2','placeholder'=>"Select Please"]); !!}
    				
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
				<div class="col-sm-8 col-sm-offset-2">

				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<input type="hidden" id="total_amount" name="final_total" value="{{$purchase_return->final_total}}">
					<div class="table-responsive">
						<table class="table table-bordered table-th-green table-striped table-condensed" id="purchase_return_product_table" style="width: 150%; max-width: 150%;">
							<thead>
								<tr>
									<th class="text-center" style="text-align: center;"><i class="fa fa-trash" aria-hidden="true"></i></th>
									<th>
										S#
									</th>
									<th>
										Sku
									</th>
									<th class="text-center" width=20%>
										Products
									</th>
									<th width=12%>Brand</th>
									<th width=8%>
										Product Description
									</th>
									
									<th>
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
										Unit Price
									</th>
									<!--<th>Discount</th>-->
									
									<th class="text-center">
										Total
									</th>
									<th width=10%>Sale Tax</th>
									<th>Further Tax</th>
									<th>Salesman Commission</th>
									<th class="hide">Sale Tax Amount</th>
									<th>Net Amount</th>


								</tr>
							</thead>
							<tbody id="tbody">

								@foreach($purchase_lines as $purchase_line)
								
								@include('purchase_return.partials.product_table_row', ['product' => $purchase_line,'store'=>$store,'taxes'=>$taxes,'product_t'=>$products_t,'row_index' => $loop->index, 'edit' => true])

								@php
								$row_index = $loop->iteration;
								@endphp
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
		

				<button class="btn btn-md btn-primary addBtn" type="button" onclick="add_row(this)" style="padding: 0px 5px 2px 5px;margin-left: 20px">
					Add Row
				</button>
				<div class="clearfix"></div>
				<div class="col-md-4">
					<input type="hidden" id="product_row_index" value="{{$row_index}}">
					<div class="form-group">
						{!! Form::label('tax_id', __('purchase.purchase_tax') . ':') !!}
						<select name="tax_id" id="tax_id" class="form-control select2" placeholder="'Please Select'">
							<option value="" data-tax_amount="0" data-tax_type="fixed" selected>@lang('lang_v1.none')</option>
							@foreach($taxes as $tax)
							<option value="{{ $tax->id }}" data-tax_amount="{{ $tax->amount }}" data-tax_type="{{ $tax->calculation_type }}" @if($purchase_return->tax_id == $tax->id) selected @endif>{{ $tax->name }}</option>
							@endforeach
						</select>
						{!! Form::hidden('tax_amount', $purchase_return->tax_amount, ['id' => 'tax_amount']); !!}
					</div>
				</div>
				<div class="col-md-8 total_data">
					<div class="pull-right"><b>@lang('stock_adjustment.total_amount'):</b> <span id="total_return" class="display_currency">{{$purchase_return->final_total}}</span></div>
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
					{!! Form::label('document', __('purchase.attach_document') . ':') !!}
					{!! Form::file('document', ['id' => 'upload_document', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
				</div>
			</div>


			<div class="col-sm-4">
				<div class="form-group">
					<label>Term & Condtions</label>
					<select class="form-control" name="tandc_type" id="TCS">
						<option selected disabled> Select</option>
						@foreach ($T_C as $tc)
						@if($tc->id == $purchase_return->tandc_type)
						<option value="{{$tc->id}}" selected>{{$tc->title}}</option>
						@else
						<option value="{{$tc->id}}">{{$tc->title}}</option>
						@endif
						@endforeach
					</select>

				</div>
			</div>
			<div class="col-sm-3">
				{!! Form::label('contractor', 'Contractor' . ':*') !!}
				<select name="contractor" class="form-control contractor">
					<option disabled selected>Please Select</option>
					@foreach ($contractor as $c)
					<option value="{{$c['id']}}" {{ ($purchase_return->contractor == $c['id']) ? 'selected' : '' }}>{{ $c['supplier_business_name'] }}</option>
					@endforeach
				</select>
			</div>
			<div class="col-sm-12">
				<div class="form-group" id="TandC">
					{!! Form::label('tandc_title',__('Terms & Conditions')) !!}
					{!! Form::textarea('tandc_title', $purchase_return->tandc_title, ['class' => 'form-control name','id'=>'product_description','rows' => 3]); !!}
				</div>
			</div>
		</div>

	</div> <!--box end-->
	
		<div class="col-sm-12 text-center fixed-button">
			<button type="button" id="submit_purchase_return_form" class="btn-big btn-primary submit_purchase_form  btn-flat" accesskey="s">Update & Close</button>
			<button class="btn-big btn-danger" type="button" onclick="window.history.back()">Close</button>  
		</div>
	
	{!! Form::close() !!}
</section>
@stop
@section('javascript')
<script src="{{ asset('js/purchase_return.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">
	__page_leave_confirmation('#purchase_return_form');
	$(document).ready(function() {
	        $('.purchase__type_edit').trigger('change');
	        
	        setTimeout(function() {
    	        $('#search_product').trigger('change');
            }, 2000); 


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





		setTimeout(function() {
			$('.product_unit_price').trigger('change');
		}, 1000);

		reIndexTable();

		$('#TandC').hide();
    $("#TCS").change(function() {
      var id = $("#TCS").val();
      if(id !='' || data !=null)
      {
      $.ajax({
        type: "GET",
        url: '/get_term/' + id,
        // dataType: "text"
        success: function(data) {
          if(data !='' || id !=null)
          {
          $('#TandC').show();
          tinymce.remove('textarea');
          $('#id_edit').val(data.id);
          $('.name').val(data.name);
          $('#title').val(data.title);
          tinymce.init({
            selector: 'textarea#product_description',
          });
         }
          
        }
      })
    }
    });
  });
</script>
<script>
	var i = 1;


	// Add row
	function add_row(el) {
		// alert("asdgfsadf");
		$('#purchase_return_product_table tbody tr').each(function() {
			$(this).find('#search_product,.further_tax ').select2('destroy')
		})
		var tr = $("#purchase_return_product_table #tbody tr:last").clone();
		tr.find('input').val('');
		tr.find('textarea').val('');
		// console.log(tr);
		$("#purchase_return_product_table #tbody tr:last").after(tr);
		reIndexTable();
		update_table_sr_number();

	}

	function reIndexTable() {
		var j = 0;
		$('#purchase_return_product_table tbody tr').each(function() {
			$(this).find('#search_product,.further_tax').select2()
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
			$(this).find('[name*=pp_without_discount]').attr('name', "products[" + j + "][pp_without_discount]")
			$(this).find('[name*=discount_percent]').attr('name', "products[" + j + "][discount_percent]")
			$(this).find('[name*=purchase_price]').attr('name', "products[" + j + "][purchase_price]")
			$(this).find('[name*=purchase_price_inc_tax]').attr('name', "products[" + j + "][purchase_price_inc_tax]")
			$(this).find('[name*=purchase_line_tax_id]').attr('name', "products[" + j + "][purchase_line_tax_id]")
			$(this).find('[name*=item_further_tax]').attr('name', "products[" + j + "][item_further_tax]")
			$(this).find('[name*=further_taax_id]').attr('name', "products[" + j + "][further_taax_id]")  
			$(this).find('[name*=salesman_commission_rate]').attr('name', "products[" + j + "][salesman_commission_rate]")
			$(this).find('[name*=profit_percent]').attr('name', "products[" + j + "][profit_percent]")
			$(this).find('[name*=tax_id]').attr('name', "products[" + j + "][tax_id]")
			$(this).find('[name*=item_tax]').attr('name', "products[" + j + "][item_tax]")
			$(this).find('[name*=variation_id]').attr('name', "products[" + j + "][variation_id]")
			$(this).find('[name*=purchase_line_id]').attr('name', "products[" + j + "][purchase_line_id]")
			$(this).find('[name*=product_unit_id]').attr('name', "products[" + j + "][product_unit_id]")
			j++;
		});
	}


	// 
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
				
				
				 if(data[0].unit.is_purchase_unit ==1)
                {
                    $(el).closest("tr").find(".uom").val(data[0].shipping_custom_field_1);
                }else{
                    $(el).closest("tr").find(".uom").val(data[0].unit.actual_name);
                    
                }
				// $(el).closest("tr").find(".uom").val(data[0].unit.actual_name);

				$(el).closest("tr").find("#item_brand").val(data[0]?.brand?.name || '');
				
					
            		$(el).closest("tr").find(".transporter_rate").val(data[0].transporter_rate);
            		$(el).closest("tr").find(".contractor_rate").val(data[0].contractor_rate);
					
					$(el).closest("tr").find(".gross__weight").val(data[0].weight);
					$(el).closest("tr").find(".net__weight").val(data[0].product_custom_field1);
			});
	}
	// Transporter 
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

	$(document).ready(function() {
		$('#Pay_type').trigger('change');
		$('.supplier,#TCS').trigger('change');
	

	})
</script>
@endsection