@extends('layouts.app')
@section('title', __('lang_v1.edit_purchase_order'))


@section('content')
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
        width: 50% !important;
    }
</style>
    <section class="content-header">
        <h1>Edit Purchase Invoice
            <i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true" data-container="body" data-toggle="popover"
                data-placement="bottom" data-content="@include('purchase.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover"
                data-original-title="" title=""></i>
                <span class="pull-right top_trans_no">{{$purchase->ref_no}}</span>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">

        <input type="hidden" id="p_code" value="{{ $currency_details->code }}">
        <input type="hidden" id="p_symbol" value="{{ $currency_details->symbol }}">
        <input type="hidden" id="p_thousand" value="{{ $currency_details->thousand_separator }}">
        <input type="hidden" id="p_decimal" value="{{ $currency_details->decimal_separator }}">

        @include('layouts.partials.error')

        {!! Form::open([
            'url' => action('PurchaseOrderController@update', [$purchase->id]),
            'method' => 'PUT',
            'id' => 'add_purchase_form',
            'files' => true,
        ]) !!}


        @php
            $currency_precision = config('constants.currency_precision', 2);
        @endphp
        <input type="hidden" id="is_purchase_order">
        <input type="hidden" id="purchase_id" value="{{ $purchase->id }}">
        @component('components.widget', ['class' => 'box-primary'])
            <div class="row">
                <div class="col-sm-2">
                    <div class="form-group">
                        {!! Form::label('purchase_order_ids', __('Grn') . ':') !!}
                        {!! Form::select('purchase_order_ids', $grn, $purchase->purchase_order_ids, [
                            'class' => 'form-control select2',
                            'id' => 'purchase_order_grn',
                        ]) !!}
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Purchase Type</label>
                        <div class="input-group">
                            <select class="form-control purchase_category purchase__type_edit no-pointer-events" name="purchase_category"
                                required readonly>
                                <option selected disabled> Select</option>
                                @foreach ($purchase_category as $tp)
                                    @if ($tp->id == $purchase->purchase_category)
                                        <option value="{{ $tp->id }}" data-pf="{{ $tp->prefix }}"
                                            data-trans_id="{{ $tp->control_account_id }}" selected>{{ $tp->Type }}</option>
                                    @else
                                        <option value="{{ $tp->id }}" data-pf="{{ $tp->prefix }}"
                                            data-trans_id="{{ $tp->control_account_id }}">{{ $tp->Type }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default bg-white btn-flat btn-modal"
                                    data-href="{{ action('PurchaseOrderController@Purchase_type_partial') }}"
                                    data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="form-group">
                        <label>Product Type</label>
                        <select class="form-control purchase_type no-pointer-events" name="purchase_type" id="purchase_type" readonly required>
                            <option selected disabled> Select</option>
                            @foreach ($p_type as $tp)
                                @if ($tp->id == $purchase->purchase_type)
                                    <option value="{{ $tp->id }}" selected data-purchasetype="{{ $tp->purchase_type }}">
                                        {{ $tp->name }}</option>
                                @else
                                    <option value="{{ $tp->id }}" data-purchasetype="{{ $tp->purchase_type }}">
                                        {{ $tp->name }}</option>
                                @endif
                            @endforeach
                        </select>

                    </div>
                </div>

                

                <div class=" col-sm-4 hide">
                    <div class="form-group">
                        {!! Form::label('Invoice Date', __('Invoice Date') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! Form::date('delivery_date', $purchase->delivery_date, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="form-group">
                        {!! Form::label('ref_no', __('Transaction No:') . '*') !!}
                        {!! Form::text('ref_no', $purchase->ref_no, ['class' => 'form-control tr_no__edit', 'required','readonly']) !!}
                    </div>
                </div>




                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('transaction_date', 'Transaction Date' . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! Form::text('transaction_date', @format_datetime($purchase->transaction_date), [
                                'class' => 'form-control',
                                'required',
                            ]) !!}
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 hide">
                    <div class="form-group">
                        {!! Form::label('Posting Date', __('Posting Date') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! Form::date('posting_date', date('Y-m-d', strtotime($purchase->posting_date)), [
                                'class' => 'form-control posting_date',
                                'required',
                            ]) !!}
                        </div>
                    </div>
                </div>

                <div class="@if (!empty($default_purchase_status)) col-sm-4 @else col-sm-4 @endif">
                    <div class="form-group">
                        {!! Form::label('supplier_id', __('purchase.supplier') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user"></i>
                            </span>
                            <select name="contact_id" class="form-control select2" id="contact_id" onchange="get_ntn_cnic(this)" required>
                                <option selected disabled>Please Select</option>
                                @foreach ($supplier as $supplier)
                                    @if ($purchase->contact_id == $supplier->id)
                                        <option value="{{ $supplier->id }}" selected>{{ $supplier->supplier_business_name }}
                                        </option>
                                    @else
                                        <option value="{{ $supplier->id }}">{{ $supplier->supplier_business_name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <span class="input-group-btn">

                            </span>
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
        



                <!-- Currency Exchange Rate -->
                <div class="col-sm-4 @if (!$currency_details->purchase_in_diff_currency) hide @endif">
                    <div class="form-group">
                        {!! Form::label('exchange_rate', __('purchase.p_exchange_rate') . ':*') !!}
                        @show_tooltip(__('tooltip.currency_exchange_factor'))
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-info"></i>
                            </span>
                            {!! Form::number('exchange_rate', $purchase->exchange_rate, [
                                'class' => 'form-control',
                                'required',
                                'step' => 0.001,
                            ]) !!}
                        </div>
                        <span class="help-block text-danger">
                            @lang('purchase.diff_purchase_currency_help', ['currency' => $currency_details->name])
                        </span>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <div class="multi-input">
                            {!! Form::label('Pay_type', __('Pay Type') . ':') !!} @show_tooltip(__('tooltip.pay_term'))
                            <select name="pay_type" class="form-control" id="Pay_type">
                                <option>Please Select</option>
                                @if ($purchase->pay_type == 'Cash')
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
                            {!! Form::number('pay_term_number', $purchase->pay_term_number, [
                                'class' => 'form-control width-40 pull-left',
                                'placeholder' => __('contact.pay_term'),
                            ]) !!}

                            {!! Form::select(
                                'pay_term_type',
                                ['months' => __('lang_v1.months'), 'days' => __('lang_v1.days')],
                                $purchase->pay_term_type,
                                [
                                    'class' => 'form-control  width-60 pull-left ',
                                    'placeholder' => __('messages.please_select'),
                                    'id' => 'pay_term_type',
                                ],
                            ) !!}
                        </div>

                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('sales Man ', __('Sales Man') . ':*') !!}
                        <select name="sales_man" class="form-control select2 sales_man">
                            <option selected disabled> Please Select</option>
                            @foreach ($sale_man as $s)
                                @if ($purchase->sales_man == $s->id)
                                    <option value="{{ $s->id }}" selected>{{ $s->supplier_business_name }}</option>
                                @else
                                    <option value="{{ $s->id }}">{{ $s->supplier_business_name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('additional_notes', __('Remarks')) !!}
                    {!! Form::textarea('additional_notes', $purchase->additional_notes, ['class' => 'form-control', 'rows' => 1]) !!}
                </div>
                </div>


                <div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('Transporter Name', __('Transporter Name') . ':*') !!}
						<select name="transporter_name" class="form-control transporter" vehicle_no="{{ $purchase->vehicle_no }}">
							@foreach ($transporter as $transporter)
							@if($transporter->id == $purchase->transporter_name)
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
									@if ($vehicle->id == $purchase->vehicle_no)
										<option value="{{ $vehicle->id }}" selected>{{ $vehicle->vhicle_number }}</option>
									@else
										<option value="{{ $vehicle->id }}">{{ $vehicle->vhicle_number }}</option>
									@endif
								@endforeach
							</select>
							<input type="text" class="form-control vehicles_input" value="{{ $purchase->vehicle_no }}"
								style="display: none;" placeholder="vehicle no" />
						</div>
					</div>
				</div>

                <div class="col-sm-3">
                    <label>Add & Less Charges</label>
                    <select class="form-control AddAndLess">
                        <option value="add" {{ !empty($purchase->add_charges) ? 'selected' : '' }}>Add Charges</option>
                        <option value="less" {{ !empty($purchase->less_charges) ? 'selected' : '' }}>Less Charges</option>
                    </select>
                </div>
              
                <div class="col-sm-4 add_charges">
                    <label>Add Charges ( + )</label>
                    <div class="input-group" style="width:100%">
                        <input type="number" class="form-control add_charges_val" name="add_charges" value="{{ $purchase->add_charges }}"
                            style="width:50%" />
                        {!! Form::select('add_charges_acc_dropdown', $accounts, $purchase->add_charges_acc_id, [
                            'class' => 'form-control select2',
                            'placeholder' => 'Select Please',
                            'style' => 'width:50%',
                            'id' => 'add_charges_acc_dropdown',
                        ]) !!}
                    </div>
                </div>


                <div class="col-sm-4 less_charges" style="display:none">
                    <label>Less Charges ( - )</label>
                    <div class="input-group" style="width:100%">
                        <input type="number" class="form-control less_charges_val" name="less_charges"
                            value="{{ $purchase->less_charges }}" style="width:50%" />
                        {!! Form::select('less_charges_acc_dropdown', $accounts, $purchase->less_charges_acc_id, [
                            'class' => 'form-control select2',
                            'placeholder' => 'Select Please',
                            'style' => 'width:50%',
                            'id' => 'less_charges_acc_dropdown',
                        ]) !!}
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-3">
                    <label>Transaction Account</label>
                    {!! Form::select('transaction_account', $accounts, !empty($transaction_account) ? $transaction_account : null, [
                        'class' => 'form-control select2',
                        'placeholder' => 'Select Please',
                        'id' => 'transaction_account',
                    ]) !!}

                </div>


                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('location_id', __('purchase.business_location') . ':*') !!}
                        @show_tooltip(__('tooltip.purchase_location'))
                        {!! Form::select('location_id', $business_locations, $purchase->location_id, [
                            'class' => 'form-control select2',
                            'placeholder' => __('messages.please_select')
                        ]) !!}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('document', __('purchase.attach_document') . ':') !!}
                        {!! Form::file('document', [
                            'id' => 'upload_document',
                            'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types'))),
                        ]) !!}
                    </div>
                </div>
                
                
               
                

             
            </div>
        @endcomponent

        @component('components.widget', ['class' => 'box-primary'])
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2">

                </div>
                <div class="col-sm-2" style="display:none;">
                    <div class="form-group">
                        <button tabindex="-1" type="button" class="btn btn-link btn-modal"
                            data-href="{{ action('ProductController@quickAdd') }}"
                            data-container=".quick_add_product_modal"><i class="fa fa-plus"></i> @lang('product.add_new_product') </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    @include('purchase.partials.edit_purchase_entry_row', ['is_purchase_order' => true])
                    <hr />
                    <button class="btn btn-md btn-primary addBtn" type="button" onclick="add_row(this)"
                        style="padding: 0px 5px 2px 5px;">
                        Add Row</button>
                    <div class="pull-right col-md-5">
                        <table class="pull-right col-md-12 total_data">
                            <tr>
                                <th class="col-md-7 text-right">@lang('lang_v1.total_items'):</th>
                                <td class="col-md-5 text-left">
                                    <span id="total_quantity" class="display_currency" data-currency_symbol="false"></span>
                                    <input type="hidden" id="total_of_total" name="total_of_total" value=0>
                                    <input type="hidden" id="total_of_tax" name="total_of_tax" value=0>
                                </td>
                            </tr>
                            <tr class="hide">
                                <th class="col-md-7 text-right">@lang('purchase.total_before_tax'):</th>
                                <td class="col-md-5 text-left">
                                    <span id="total_st_before_tax" class="display_currency"></span>
                                    <input type="hidden" id="st_before_tax_input" value=0>
                                </td>
                            </tr>
                            <tr>
                                <th class="col-md-7 text-right">@lang('purchase.net_total_amount'):</th>
                                <td class="col-md-5 text-left">
                                    {{-- <span id="total_subtotal"
                                        class="display_currency">{{ $purchase->total_before_tax / $purchase->exchange_rate }}</span> --}}
                                        <span id="grand_total" class="display_currency"
                            data-currency_symbol='true'>{{ $purchase->final_total }}</span>
                                    <!-- This is total before purchase tax-->
                                    <input type="hidden" id="total_subtotal_input"
                                        value="{{ $purchase->total_before_tax / $purchase->exchange_rate }}"
                                        name="total_before_tax">
                                </td>
                            </tr>

                            <tr>
                                <th class="col-md-7 text-right">Total Gross Weight:</th>
                                <td class="col-md-5 text-left">
                                    <span id="total_gross__weight" class="display_currency"
                                        data-currency_symbol="false"></span>
                                    <input type="hidden" name="total_gross__weight" class="total_gross__weight" />
                                </td>
                            </tr>
                            <tr>
                                <th class="col-md-7 text-right">Total Net Weight:</th>
                                <td class="col-md-5 text-left">
                                    <span id="total_net__weight" class="display_currency"
                                        data-currency_symbol="false"></span>
                                    <input type="hidden" name="total_net__weight" class="total_net__weight" />

                                    <input type="hidden" id="total_sale_tax" name="total_sale_tax">
                                    <input type="hidden" id="total_further_tax" name="total_further_tax">
                                    <input type="hidden" id="total_salesman_commission" name="total_salesman_commission">
                                    <input type="hidden" id="total_transporter_rate" name="total_transporter_rate">
                                    <input type="hidden" id="total_contractor_rate" name="total_contractor_rate">
                                    <input type="hidden" id="total_prd_contractor_rate" name="total_prd_contractor_rate">
                                </td>
                            </tr>

                        </table>
                    </div>

                </div>
            </div>
        @endcomponent
        <div style="display: none;">
            @component('components.widget', ['class' => 'box-solid'])
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('shipping_details', __('sale.shipping_details')) !!}
                            {!! Form::textarea('shipping_details', $purchase->shipping_details, [
                                'class' => 'form-control',
                                'placeholder' => __('sale.shipping_details'),
                                'rows' => '3',
                                'cols' => '30',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('shipping_address', __('lang_v1.shipping_address')) !!}
                            {!! Form::textarea('shipping_address', $purchase->shipping_address, [
                                'class' => 'form-control',
                                'placeholder' => __('lang_v1.shipping_address'),
                                'rows' => '3',
                                'cols' => '30',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('shipping_charges', __('sale.shipping_charges')) !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-info"></i>
                                </span>
                                {!! Form::text(
                                    'shipping_charges',
                                    number_format(
                                        $purchase->shipping_charges / $purchase->exchange_rate,
                                        $currency_precision,
                                        $currency_details->decimal_separator,
                                        $currency_details->thousand_separator,
                                    ),
                                    ['class' => 'form-control input_number', 'placeholder' => __('sale.shipping_charges')],
                                ) !!}
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('shipping_status', __('lang_v1.shipping_status')) !!}
                            {!! Form::select('shipping_status', $shipping_statuses, $purchase->shipping_status, [
                                'class' => 'form-control',
                                'placeholder' => __('messages.please_select'),
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('delivered_to', __('lang_v1.delivered_to') . ':') !!}
                            {!! Form::text('delivered_to', $purchase->delivered_to, [
                                'class' => 'form-control',
                                'placeholder' => __('lang_v1.delivered_to'),
                            ]) !!}
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

                    @if (!empty($shipping_custom_label_1))
                        @php
                            $label_1 = $shipping_custom_label_1 . ':';
                            if ($is_shipping_custom_field_1_required) {
                                $label_1 .= '*';
                            }
                        @endphp

                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('shipping_custom_field_1', $label_1) !!}
                                {!! Form::text('shipping_custom_field_1', $purchase->shipping_custom_field_1, [
                                    'class' => 'form-control',
                                    'placeholder' => $shipping_custom_label_1,
                                    'required' => $is_shipping_custom_field_1_required,
                                ]) !!}
                            </div>
                        </div>
                    @endif
                    @if (!empty($shipping_custom_label_2))
                        @php
                            $label_2 = $shipping_custom_label_2 . ':';
                            if ($is_shipping_custom_field_2_required) {
                                $label_2 .= '*';
                            }
                        @endphp

                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('shipping_custom_field_2', $label_2) !!}
                                {!! Form::text('shipping_custom_field_2', $purchase->shipping_custom_field_2, [
                                    'class' => 'form-control',
                                    'placeholder' => $shipping_custom_label_2,
                                    'required' => $is_shipping_custom_field_2_required,
                                ]) !!}
                            </div>
                        </div>
                    @endif
                    @if (!empty($shipping_custom_label_3))
                        @php
                            $label_3 = $shipping_custom_label_3 . ':';
                            if ($is_shipping_custom_field_3_required) {
                                $label_3 .= '*';
                            }
                        @endphp

                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('shipping_custom_field_3', $label_3) !!}
                                {!! Form::text('shipping_custom_field_3', $purchase->shipping_custom_field_3, [
                                    'class' => 'form-control',
                                    'placeholder' => $shipping_custom_label_3,
                                    'required' => $is_shipping_custom_field_3_required,
                                ]) !!}
                            </div>
                        </div>
                    @endif
                    @if (!empty($shipping_custom_label_4))
                        @php
                            $label_4 = $shipping_custom_label_4 . ':';
                            if ($is_shipping_custom_field_4_required) {
                                $label_4 .= '*';
                            }
                        @endphp

                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('shipping_custom_field_4', $label_4) !!}
                                {!! Form::text('shipping_custom_field_4', $purchase->shipping_custom_field_4, [
                                    'class' => 'form-control',
                                    'placeholder' => $shipping_custom_label_4,
                                    'required' => $is_shipping_custom_field_4_required,
                                ]) !!}
                            </div>
                        </div>
                    @endif
                    @if (!empty($shipping_custom_label_5))
                        @php
                            $label_5 = $shipping_custom_label_5 . ':';
                            if ($is_shipping_custom_field_5_required) {
                                $label_5 .= '*';
                            }
                        @endphp

                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('shipping_custom_field_5', $label_5) !!}
                                {!! Form::text('shipping_custom_field_5', $purchase->shipping_custom_field_4, [
                                    'class' => 'form-control',
                                    'placeholder' => $shipping_custom_label_5,
                                    'required' => $is_shipping_custom_field_5_required,
                                ]) !!}
                            </div>
                        </div>
                    @endif
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('shipping_documents', __('lang_v1.shipping_documents') . ':') !!}
                            {!! Form::file('shipping_documents[]', [
                                'id' => 'shipping_documents',
                                'multiple',
                                'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types'))),
                            ]) !!}
                            <p class="help-block">
                                @lang('purchase.max_file_size', ['size' => config('constants.document_size_limit') / 1000000])
                                @includeIf('components.document_help_text')
                            </p>
                            @php
                                $medias = $purchase->media->where('model_media_type', 'shipping_document')->all();
                            @endphp
                            @include('sell.partials.media_table', ['medias' => $medias, 'delete' => true])
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn btn-primary btn-sm" id="toggle_additional_expense"> <i
                                class="fas fa-plus"></i> @lang('lang_v1.add_additional_expenses') <i class="fas fa-chevron-down"></i></button>
                    </div>
                    <div class="col-md-8 col-md-offset-4" id="additional_expenses_div">
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
                                        {!! Form::text('additional_expense_key_1', $purchase->additional_expense_key_1, ['class' => 'form-control']) !!}
                                    </td>
                                    <td>
                                        {!! Form::text(
                                            'additional_expense_value_1',
                                            number_format(
                                                $purchase->additional_expense_value_1 / $purchase->exchange_rate,
                                                $currency_precision,
                                                $currency_details->decimal_separator,
                                                $currency_details->thousand_separator,
                                            ),
                                            ['class' => 'form-control input_number', 'id' => 'additional_expense_value_1'],
                                        ) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        {!! Form::text('additional_expense_key_2', $purchase->additional_expense_key_2, ['class' => 'form-control']) !!}
                                    </td>
                                    <td>
                                        {!! Form::text(
                                            'additional_expense_value_2',
                                            number_format(
                                                $purchase->additional_expense_value_2 / $purchase->exchange_rate,
                                                $currency_precision,
                                                $currency_details->decimal_separator,
                                                $currency_details->thousand_separator,
                                            ),
                                            ['class' => 'form-control input_number', 'id' => 'additional_expense_value_2'],
                                        ) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        {!! Form::text('additional_expense_key_3', $purchase->additional_expense_key_3, ['class' => 'form-control']) !!}
                                    </td>
                                    <td>
                                        {!! Form::text(
                                            'additional_expense_value_3',
                                            number_format(
                                                $purchase->additional_expense_value_3 / $purchase->exchange_rate,
                                                $currency_precision,
                                                $currency_details->decimal_separator,
                                                $currency_details->thousand_separator,
                                            ),
                                            ['class' => 'form-control input_number', 'id' => 'additional_expense_value_3'],
                                        ) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        {!! Form::text('additional_expense_key_4', $purchase->additional_expense_key_4, ['class' => 'form-control']) !!}
                                    </td>
                                    <td>
                                        {!! Form::text(
                                            'additional_expense_value_4',
                                            number_format(
                                                $purchase->additional_expense_value_4 / $purchase->exchange_rate,
                                                $currency_precision,
                                                $currency_details->decimal_separator,
                                                $currency_details->thousand_separator,
                                            ),
                                            ['class' => 'form-control input_number', 'id' => 'additional_expense_value_4'],
                                        ) !!}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-md-offset-8">
                        {!! Form::hidden('final_total', $purchase->final_total, ['id' => 'grand_total_hidden']) !!}
                        <b>@lang('lang_v1.order_total'): </b><span id="grand_total" class="display_currency"
                            data-currency_symbol='true'>{{ $purchase->final_total }}</span>
                    </div>
                </div>
            @endcomponent
        </div>
        @component('components.widget', ['class' => 'box-solid'])
            <div class="row">
                <div class="col-sm-12">
                    <table class="table">
                        <tr class="">
                            <td class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('discount_type', __('purchase.discount_type') . ':') !!}
                                    <br>
                                    {!! Form::select(
                                        'discount_type',
                                        ['' => __('lang_v1.none'), 'fixed' => __('lang_v1.fixed'), 'percentage' => __('lang_v1.percentage')],
                                        $purchase->discount_type,
                                        ['class' => 'form-control', 'placeholder' => __('messages.please_select')],
                                    ) !!}
                                </div>
                            </td>


                        </tr>
                        <tr>
                            <td class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('discount_amount', __('purchase.discount_amount') . ':') !!}
                                    {!! Form::text(
                                        'discount_amount',
                                    
                                        $purchase->discount_type == 'fixed'
                                            ? number_format(
                                                $purchase->discount_amount / $purchase->exchange_rate,
                                                $currency_precision,
                                                $currency_details->decimal_separator,
                                                $currency_details->thousand_separator,
                                            )
                                            : number_format(
                                                $purchase->discount_amount,
                                                $currency_precision,
                                                $currency_details->decimal_separator,
                                                $currency_details->thousand_separator,
                                            ),
                                        ['class' => 'form-control input_number'],
                                    ) !!}
                                </div>
                            </td>
                            <td class="col-md-3">
                                &nbsp;
                            </td>

                        </tr>
                        <tr class="hide" style="display: none;">
                            <td>
                                <div class="form-group">
                                    {!! Form::label('tax_id', __('purchase.purchase_tax') . ':') !!}
                                    <select name="tax_id" id="tax_id" class="form-control select2"
                                        placeholder="'Please Select'">
                                        <option value="" data-tax_amount="0" selected>@lang('lang_v1.none')</option>
                                        @foreach ($taxes as $tax)
                                            <option value="{{ $tax->id }}"
                                                @if ($purchase->tax_id == $tax->id) {{ 'selected' }} @endif
                                                data-tax_amount="{{ $tax->amount }}">
                                                {{ $tax->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    {!! Form::hidden('tax_amount', $purchase->tax_amount, ['id' => 'tax_amount']) !!}
                                </div>
                            </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>
                                <b>@lang('purchase.purchase_tax'):</b>(+)
                                <span id="tax_calculated_amount" class="display_currency">0</span>
                            </td>
                        </tr>
                    

                    </table>
                </div>
            </div>
           

            
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Term & Conditions</label>
                    <select class="form-control" name="tandc_type" id="TCS">
                        <option selected disabled> Select</option>
                        @foreach ($T_C as $tc)
                            @if ($tc->id == $purchase->tandc_type)
                                <option value="{{ $tc->id }}" selected>{{ $tc->title }}</option>
                            @else
                                <option value="{{ $tc->id }}">{{ $tc->title }}</option>
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
                        <option value="{{ $c['id'] }}" {{ $purchase->contractor == $c['id'] ? 'selected' : '' }}>
                            {{ $c['supplier_business_name'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-sm-3">
                {!! Form::label('prd_contractor', 'Production Contractor' . ':*') !!}
                <select name="prd_contractor" class="form-control prd_contractor">
                    <option disabled selected>Please Select</option>
                    @foreach ($contractor as $c)
                        <option value="{{ $c['id'] }}" {{ $purchase->prd_contractor == $c['id'] ? 'selected' : '' }}>
                            {{ $c['supplier_business_name'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-sm-12">
                <div class="form-group" id="TandC">
                    {!! Form::label('tandc_title', __('Terms & Conditions')) !!}
                    {!! Form::textarea('tandc_title', $purchase->tandc_title, [
                        'class' => 'form-control name',
                        'id' => 'product_description',
                        'rows' => 3,
                    ]) !!}
                </div>
            </div>
        @endcomponent

            <div class="col-sm-12 text-center fixed-button">
                <button type="button" id="submit_purchase_form"
                    class="btn btn-big btn-primary submit_purchase_form btn-flat"
                    accesskey="s">Update & Close</button>
                    <button class="btn btn-big btn-danger" type="button" onclick="window.history.back()">Close</button> 
                    <span class="btn btn-big btn-warning save_grand_total" class="display_currency" data-currency_symbol='true' >0</span>
                </div>
            


        {!! Form::close() !!}
    </section>
    <!-- /.content -->
    <!-- quick product modal -->
    <div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">

    </div>

@endsection

@section('javascript')
    <script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.purchase__type_edit').trigger('change');
            
            
            setTimeout(function() {
                $(".purchase_unit_cost_without_discount").trigger("change");
                // $('.purchase_quantity').trigger('change');
                $('.purchase_line_tax_id').trigger('change');
                
                $(".transporter").trigger('change');
            }, 3000);

            setTimeout(function() {
                $('.purchase_line_tax_id').trigger('change');
                $('.further_tax').trigger('change');
            }, 5000);


            $(document).on('change', '.contractor, .prd_contractor', function() {
                $('.products_change').trigger('change');
            })


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


            $(document).on('click', 'a.delete-purchase-order', function(e) {
                e.preventDefault();
                swal({
                    title: LANG.sure,
                    icon: 'warning',
                    buttons: true,
                    dangerMode: true,
                }).then(willDelete => {
                    if (willDelete) {
                        var href = $(this).attr('href');
                        $.ajax({
                            method: 'DELETE',
                            url: href,
                            dataType: 'json',
                            success: function(result) {
                                if (result.success == true) {
                                    toastr.success(result.msg);
                                    var jpage = "<?php echo $page; ?>";
                                    if (jpage === 'convert_pr_to_po') {
                                        window.location = '/purchase-order';
                                    } else if (jpage === 'convert_grn_to_pi') {
                                        window.location = '/Purchase_invoice';
                                    }
                                    // purchase_order_table.ajax.reload();
                                    // location.reload('purchase-order.edit');

                                } else {
                                    toastr.error(result.msg);
                                }
                            },
                        });
                    }
                });
            });
            // get_product_code();
            $(".get_product").trigger("change");
            $('select').select2()
            update_table_total();
            update_grand_total();
            __page_leave_confirmation('#add_purchase_form');
            $('#shipping_documents').fileinput({
                showUpload: false,
                showPreview: false,
                browseLabel: LANG.file_browse_label,
                removeLabel: LANG.remove,
            });

            if ($('textarea#product_description').length > 0) {
                tinymce.init({
                    selector: 'textarea#product_description',
                    height: 250
                });
            }


            $("#TCS").change(function() {
                var id = $("#TCS").val();
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
                            selector: 'textarea#product_description',
                        });

                    }
                })
            });
        });

        function get_product_code(el) {

            var terms = $(el).val();
            var transporter = $('.transporter').val(); 
            var contractor = $('.contractor').val(); 
            var prd_contractor = $('.prd_contractor').val();
            $.getJSON(
                '/purchases/get_products', {
                    term: terms,
                    transporter: transporter,
                    contractor: contractor,
                    prd_contractor: prd_contractor
                },
                function(data) {
                    $(this).val(data[0].unit.actual_name);
                    console.log(data[0]);
                    $(el).closest("tr").find(".product_code").val(data[0].sku);

                    if (data[0].unit.is_purchase_unit == 1) {
                        $(el).closest("tr").find(".uom").val(data[0].shipping_custom_field_1);
                    } else {
                        $(el).closest("tr").find(".uom").val(data[0].unit.actual_name);

                    }

                    $(el).closest("tr").find(".category_type").val(data[0].category_id);
                    // $(el).closest("tr").find(".base_unit").val(data[0].unit.base_unit_multiplier);
                    if (data[0] && data[0].unit && data[0].unit.base_unit_multiplier && data[0].unit.is_purchase_unit == 1 && data[0].unit.base_unit_multiplier > 0 ) {
                        $(el).closest("tr").find(".base_unit").val(data[0].unit.base_unit_multiplier);
                    } else {
                        $(el).closest("tr").find(".base_unit").val(1);
                    }
                    $(el).closest("tr").find(".transporter_rate").val(data[0].transporter_rate);
                    $(el).closest("tr").find(".contractor_rate").val(data[0].contractor_rate); 
                    $(el).closest("tr").find(".contractor_rate").attr('prd_contr_rate',data[0].prd_contractor);

                    $(el).closest("tr").find(".gross__weight").val(data[0].weight);
                    $(el).closest("tr").find(".net__weight").val(data[0].product_custom_field1);
                    update_table_total();

                    $('.purchase_quantity').trigger('change');
                    $(".purchase_quantity").trigger('keyup');

                });
        }

        function add_row(el) {
            $('.products_change,.brand_select,.purchase_line_tax_id,.further_tax').select2('destroy')
            $('#purchase_entry_table tbody tr').each(function() {
            })
            var tr = $("#purchase_entry_table #tbody tr:last").clone();
            tr.find('input').val('');
            tr.find('textarea').val('');
            // console.log(tr);
            $("#purchase_entry_table #tbody tr:last").after(tr);


            reIndexTable();
            update_table_sr_number();
            $('select').select2()
        }

        function reIndexTable() {
            var j = 0
            $('#purchase_entry_table tbody tr').each(function() {
                $(this).find('#search_product,.purchase_line_tax_id,.further_tax').select2()
                $(this).attr('id', j)
                $(this).find('[name*=store]').attr('name', "purchases[" + j + "][store]")
                $(this).find('[name*=item_code]').attr('name', "purchases[" + j + "][item_code]")
                $(this).find('[name*=item_description]').attr('name', "purchases[" + j + "][item_description]")
                $(this).find('[name*=quantity]').attr('name', "purchases[" + j + "][quantity]")
                $(this).find('[name*=product_id]').attr('name', "purchases[" + j + "][product_id]")
                $(this).find('[name*=pp_without_discount]').attr('name', "purchases[" + j +
                    "][pp_without_discount]")
                $(this).find('[name*=discount_percent]').attr('name', "purchases[" + j + "][discount_percent]")
                $(this).find('[name*=purchase_price]').attr('name', "purchases[" + j + "][purchase_price]")
                $(this).find('[name*=purchase_price_inc_tax]').attr('name', "purchases[" + j +
                    "][purchase_price_inc_tax]")
                $(this).find('[name*=purchase_line_tax_id]').attr('name', "purchases[" + j +
                    "][purchase_line_tax_id]")
                $(this).find('[name*=item_further_tax]').attr('name', "purchases[" + j + "][item_further_tax]")
                $(this).find('[name*=further_taax_id]').attr('name', "purchases[" + j + "][further_taax_id]")
                $(this).find('[name*=salesman_commission_rate]').attr('name', "purchases[" + j +
                    "][salesman_commission_rate]")
                $(this).find('[name*=profit_percent]').attr('name', "purchases[" + j + "][profit_percent]")
                $(this).find('[name*=item_tax]').attr('name', "purchases[" + j + "][item_tax]")
                $(this).find('[name*=variation_id]').attr('name', "purchases[" + j + "][variation_id]")
                $(this).find('[name*=purchase_line_id]').attr('name', "purchases[" + j + "][purchase_line_id]")
                $(this).find('[name*=purchase_line_id]').attr('name', "purchases[" + j + "][purchase_line_id]")
                $(this).find('[name*=product_unit_id]').attr('name', "purchases[" + j + "][product_unit_id]")
                j++;
            });
        }

        function remove_row(el) {
            var tr_length = $("#purchase_entry_table #tbody tr").length;
            if (tr_length > 1) {
                var tr = $(el).closest("tr").remove();
                reIndexTable();
                update_table_sr_number();
                update_table_total();
                update_grand_total();
            } else {
                alert("At least one row required");
            }
        }

        function update_table_sr_number() {
            var sr_number = 1;
            $('table#purchase_entry_table tbody')
                .find('.sr_number')
                .each(function() {
                    $(this).text(sr_number);
                    sr_number++;
                });
        }
        $(document).on('focus', '.select2-selection.select2-selection--single', function(e) {
            $(this).closest(".select2-container").siblings('select:enabled').select2('open');
        });

        // steal focus during close - only capture once and stop propogation
        $('select.select2').on('select2:closing', function(e) {
            $(e.target).data("select2").$selection.one('focus focusin', function(e) {
                e.stopPropagation();
            });
        });






        $("#purchase_order_grn").on("select2:select", function(e) {


            var purchase_order_id = e.params.data.id;
            // alert(purchase_order_id);
            var row_count = $('#row_count').val();
            // alert(row_count);
            $.ajax({
                url: '/get-purchase-grn/' + purchase_order_id + '?row_count=' + row_count,
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    var IsRemoved = $('#purchase_entry_table').attr('_isRemoved')
                    if (IsRemoved == 'true') {} else {
                        $("#purchase_entry_table #tbody tr").remove();
                        // $('#purchase_entry_table').attr('_isRemoved','true')
                    }
                    $('.purchase__type_edit  option').removeAttr("selected")
                   
                    $('.purchase__type_edit  option[value=' + data.po.purchase_type + ']').attr(
                        "selected", true)
                    $('.purchase__type_edit').select2()

                    $('.transporter option').removeAttr("selected")
                    $('.transporter').select2('destroy')
                    $('.transporter  option[value=' + data.po.transporter_name + ']').attr("selected",
                        true)
                    $('.transporter').select2()
                    $('.transporter').trigger('change');


                    $('.vehicle').val(data.po.vehicle_no);


                    $('.pay_type option[value=' + data.po.pay_type + ']').attr("selected", true)
                    $('.pay_type').select2()

                    $('.sales_man').select2('destroy')
                    $('.sales_man option[value=' + data.po.sales_man + ']').attr("selected", true)
                    $('.sales_man').select2()

                    $('.posting_date').val(data.po.posting_date);


                    $('#contact_id option').removeAttr("selected")
                    $('#contact_id').select2('destroy')
                    $('#contact_id option[value=' + data.po.contact_id + ']').attr("selected", true)
                    $('#contact_id').select2()

                    $('.purchase_category  option[value=' + data.po.purchase_category + ']').attr(
                        "selected", true);
                    $('.purchase__type_edit').trigger('change');

                    $('.purchase_category  option[value=' + data.po.purchase_category + ']').attr(
                        "selected", true);
                    $('.purchase__type_edit').trigger('change');

                    $('.products_change').select2();
                    $('#brand_id').select2();
                    $('.products_change').trigger('change');
                    $('#contact_id').trigger('change');

                    set_po_values(data.po);
                    append_purchase_lines(data.html, row_count);
                    $('.purchase_unit_cost_without_discount').trigger('change');
                    reIndexTable();
                    $('.products_change').trigger('change');
                },
            });

        });

        $("#purchase_order_grn").on("select2:unselect", function(e) {
            // alert("sa");
            var purchase_order_id = e.params.data.id;
            $('#purchase_entry_table tbody').find('tr').each(function() {
                if (typeof($(this).data('purchase_order_grn')) !== 'undefined' &&
                    $(this).data('purchase_order_grn') == purchase_order_id) {
                    $(this).remove();
                }
            });
        });

        $(document).ready(function() {
            $('#Pay_type').trigger('change');
            $('.purchase_quantity').trigger('change');
            $('#contact_id').trigger('change');
            $('#purchase_type,.purchase_category,.vehicles').select2('destroy')

        })
    </script>
    @include('purchase.partials.keyboard_shortcuts')
@endsection
