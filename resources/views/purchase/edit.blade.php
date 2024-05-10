
@extends('layouts.app')
@section('title', __('purchase.edit_purchase'))

@section('content')
<style>
  .select2-container--default {
    width: 100% !Important;
  }

  #tbody textarea.form-control {
    height: 35px !important;
    width: 100% !important;
  }
</style>
@php
$custom_labels = json_decode(session('business.custom_labels'), true);
@endphp
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1 class="top-heading">@lang('Edit Goods received note') <i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true" data-container="body" data-toggle="popover" data-placement="bottom" data-content="@include('purchase.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover" data-original-title="" title=""></i>
    <span class="pull-right top_trans_no">{{$purchase->ref_no}}</span> </h1>
</section>

<!-- Main content -->
<section class="content">

  <!-- Page level currency setting -->
  <input type="hidden" id="p_code" value="{{$currency_details->code}}">
  <input type="hidden" id="p_symbol" value="{{$currency_details->symbol}}">
  <input type="hidden" id="p_thousand" value="{{$currency_details->thousand_separator}}">
  <input type="hidden" id="p_decimal" value="{{$currency_details->decimal_separator}}">

  @include('layouts.partials.error')

  {!! Form::open(['url' => action('PurchaseController@update' , [$purchase->id] ), 'method' => 'PUT', 'id' => 'add_purchase_form', 'files' => true ]) !!}

  @php
  $currency_precision = config('constants.currency_precision', 2);
  @endphp

  <input type="hidden" id="purchase_id" value="{{ $purchase->id }}">

  @component('components.widget', ['class' => 'box-primary'])

  @if(!empty($common_settings['enable_purchase_order']))
  <div class="row">
    <div class="col-sm-3">
      <div class="form-group">
        {!! Form::label('purchase_order_ids', __('lang_v1.purchase_order').':') !!}
        {{-- {!! Form::select('purchase_order_ids', $purchase_orders, $purchase->purchase_order_ids, ['class' => 'form-control select2','placeholder'=>"Please Select",'required','id' => 'purchase_order_ids']); !!} --}}
        <select name="purchase_order_ids" class="form-control select2" id="purchase_order_ids">
          <option>Please select</option>
          @foreach ($purchase_orders as $s)
          @if($s->id== $purchase->purchase_order_ids)
          <option value="{{$s->id}}" selected>{{$s->ref_no}}({{$s->contact->supplier_business_name??''}})</option>
          @else
          <option value="{{$s->id}}">{{$s->ref_no}}({{$s->contact->supplier_business_name??''}})</option>
          @endif
          @endforeach
        </select>

      </div>
    </div>

    <div class="col-sm-2">
      <div class="form-group">
        <label>Purchase Type</label>
        <select class="form-control purchase_category purchase__type_edit no-pointer-events" name="purchase_category" readonly required>
          <option selected disabled> Select</option>
          @foreach ($purchase_category as $tp)

          @if($tp->id == $purchase->purchase_category)
          <option value="{{$tp->id}}" data-pf="{{$tp->prefix}}" selected>{{$tp->Type}}</option>
          @else
          <option value="{{$tp->id}}" data-pf="{{$tp->prefix}}">{{$tp->Type}}</option>
          @endif

          @endforeach
        </select>
      </div>
    </div>



    <div class="col-sm-2">
      <div class="form-group">
        <label>Product Type</label>
        <select class="form-control purchase_type no-pointer-events" name="purchase_type" readonly id="is_purchase_order_dd">
          <option selected disabled> Select</option>
          @foreach ($p_type as $tp)
          @if($tp->id == $purchase->purchase_type)
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
          {!! Form::label('ref_no', __('Transaction No') . '*') !!}
          @show_tooltip(__('lang_v1.leave_empty_to_autogenerate'))
          {!! Form::text('ref_no', $purchase->ref_no, ['class' => 'form-control tr_no__edit','readonly','required']); !!}
        </div>
      </div>

      <div class="col-sm-3">
        <div class="form-group">
          {!! Form::label('transaction_date', __('Transaction Date') . ':*') !!}
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </span>
            {!! Form::text('transaction_date', @format_datetime($purchase->transaction_date), ['class' => 'form-control', 'required','id'=>'transaction_date']); !!}
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
         
            <select name="contact_id" class="form-control select2 contact_id" onchange="get_ntn_cnic(this)" id="supplier">
              @foreach ($supplier as $supplier)
              @if($purchase->contact_id == $supplier->id)
              <option value="{{$supplier->id}}" selected>{{$supplier->supplier_business_name??''}}</option>
              @else
              <option value="{{$supplier->id}}">{{$supplier->supplier_business_name??''}}</option>
              @endif
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
          <div class="multi-input">
            {!! Form::label('Pay_type', __('Pay Type') . ':') !!} @show_tooltip(__('tooltip.pay_term'))
            <br />
  
  
            <select name="pay_type" class="form-control" id="Pay_type">
              <option>Please Select</option>
              @if($purchase->pay_type =="Cash")
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
            {!! Form::number('pay_term_number', $purchase->pay_term_number, ['class' => 'form-control width-40 pull-left', 'placeholder' => __('contact.pay_term')]); !!}
  
            {!! Form::select('pay_term_type',
            ['months' => __('lang_v1.months'),
            'days' => __('lang_v1.days')],
            $purchase->pay_term_type,
            ['class' => 'form-control width-60 pull-left','placeholder' => __('messages.please_select'), 'id' => 'pay_term_type']); !!}
          </div>
        </div>
      </div>

   

    <div class="col-sm-3">
			<div class="form-group">
				{!! Form::label('Sales Man', __('Sales Man') . ':*') !!}
				<select name="sales_man" class="form-control select2">
					<option selected disabled> Please Select</option>
					@foreach ($sale_man as $s)
					<option value="{{$s->id}}" {{ ($purchase->sales_man == $s->id) ? 'selected' : '' }}>{{$s->supplier_business_name}}</option>
					@endforeach
				</select>
			</div>
		</div>
    <div class="col-sm-4">
    <div class="form-group">
      {!! Form::label('additional_notes',__('Remarks')) !!}
      {!! Form::textarea('additional_notes', $purchase->additional_notes, ['class' => 'form-control', 'rows' => 1]); !!}
    </div>
    </div>

  @endif

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


  <div class="col-sm-3">
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



  

  

  
    <div class="col-sm-4 hide">
      <div class="form-group">
        {!! Form::label('Posting Date', __('Posting Date') . ':*') !!}
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </span>

          {!! Form::date('posting_date', date('Y-m-d', strtotime($purchase->posting_date)), ['class' => 'form-control','required']); !!}
          
        </div>
      </div>
    </div>


    
    <div class="col-sm-2">
      <div class="form-group">
        {!! Form::label('Factory Weight', __('Factory Weight') . ':*') !!}

        {!! Form::text('factory_weight',$purchase->factory_weight, ['class' => 'form-control']); !!}

      </div>
    </div>
    <div class="col-sm-4 ">
      <div class="form-group">
        {!! Form::label('location_id', __('purchase.business_location').':*') !!}
        @show_tooltip(__('tooltip.purchase_location'))
        {!! Form::select('location_id', $business_locations, $purchase->location_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
      </div>
    </div>
    <div class="col-sm-4">
      <div class="form-group">
        {!! Form::label('document', __('purchase.attach_document') . ':') !!}
        {!! Form::file('document', ['id' => 'upload_document', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
  
      </div>
    </div>
   
    <div class="col-sm-4 hide">
      <div class="form-group">
        {!! Form::label('status', __('purchase.purchase_status') . ':*') !!}
        @show_tooltip(__('tooltip.order_status'))
        {!! Form::select('status', $orderStatuses, $purchase->status, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select') , 'required']); !!}
      </div>
    </div>


    <!-- Currency Exchange Rate -->
    <div class="col-sm-3 @if(!$currency_details->purchase_in_diff_currency) hide @endif">
      <div class="form-group">
        {!! Form::label('exchange_rate', __('purchase.p_exchange_rate') . ':*') !!}
        @show_tooltip(__('tooltip.currency_exchange_factor'))
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-info"></i>
          </span>
          {!! Form::number('exchange_rate', $purchase->exchange_rate, ['class' => 'form-control', 'required', 'step' => 0.001]); !!}
        </div>
        <span class="help-block text-danger">
          @lang('purchase.diff_purchase_currency_help', ['currency' => $currency_details->name])
        </span>
      </div>
    </div>




    


  </div>
  <div class="row">
    @php
    $custom_field_1_label = !empty($custom_labels['purchase']['custom_field_1']) ? $custom_labels['purchase']['custom_field_1'] : '';

    $is_custom_field_1_required = !empty($custom_labels['purchase']['is_custom_field_1_required']) && $custom_labels['purchase']['is_custom_field_1_required'] == 1 ? true : false;

    $custom_field_2_label = !empty($custom_labels['purchase']['custom_field_2']) ? $custom_labels['purchase']['custom_field_2'] : '';

    $is_custom_field_2_required = !empty($custom_labels['purchase']['is_custom_field_2_required']) && $custom_labels['purchase']['is_custom_field_2_required'] == 1 ? true : false;

    $custom_field_3_label = !empty($custom_labels['purchase']['custom_field_3']) ? $custom_labels['purchase']['custom_field_3'] : '';

    $is_custom_field_3_required = !empty($custom_labels['purchase']['is_custom_field_3_required']) && $custom_labels['purchase']['is_custom_field_3_required'] == 1 ? true : false;

    $custom_field_4_label = !empty($custom_labels['purchase']['custom_field_4']) ? $custom_labels['purchase']['custom_field_4'] : '';

    $is_custom_field_4_required = !empty($custom_labels['purchase']['is_custom_field_4_required']) && $custom_labels['purchase']['is_custom_field_4_required'] == 1 ? true : false;
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
        {!! Form::text('custom_field_1', $purchase->custom_field_1, ['class' => 'form-control','placeholder' => $custom_field_1_label, 'required' => $is_custom_field_1_required]); !!}
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
        {!! Form::text('custom_field_2', $purchase->custom_field_2, ['class' => 'form-control','placeholder' => $custom_field_2_label, 'required' => $is_custom_field_2_required]); !!}
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
        {!! Form::text('custom_field_3', $purchase->custom_field_3, ['class' => 'form-control','placeholder' => $custom_field_3_label, 'required' => $is_custom_field_3_required]); !!}
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
        {!! Form::text('custom_field_4', $purchase->custom_field_4, ['class' => 'form-control','placeholder' => $custom_field_4_label, 'required' => $is_custom_field_4_required]); !!}
      </div>
    </div>
    @endif
  </div>

  @endcomponent

  @component('components.widget', ['class' => 'box-primary'])
  <div class="row">
    <div class="col-sm-8 col-sm-offset-2">
 
    </div>
    <div class="col-sm-2" style="display:none;">
      <div class="form-group">
        <button tabindex="-1" type="button" class="btn btn-link btn-modal" data-href="{{action('ProductController@quickAdd')}}" data-container=".quick_add_product_modal"><i class="fa fa-plus"></i> @lang( 'product.add_new_product' ) </button>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-12">
      @include('purchase.partials.edit_purchase_entry_row')

      <hr />


      <button class="btn btn-md btn-primary addBtn" type="button" onclick="add_row(this)" style="padding: 0px 5px 2px 5px;">
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
          <tr>
            <th class="col-md-7 text-right">@lang( 'purchase.net_total_amount' ):</th>
            <td class="col-md-5 text-left">
              <span id="total_subtotal" class="display_currency">{{$purchase->total_before_tax/$purchase->exchange_rate}}</span>
              <!-- This is total before purchase tax-->
              <input type="hidden" id="total_subtotal_input" value="{{$purchase->total_before_tax/$purchase->exchange_rate}}" name="total_before_tax">
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

    </div>
  </div>
  @endcomponent

  @component('components.widget', ['class' => 'box-primary'])
  <div class="row">
    <div class="col-sm-12">
      <table class="table hide">
        <tr>
          <td class="col-md-3">
            <div class="form-group">
              {!! Form::label('discount_type', __( 'purchase.discount_type' ) . ':') !!}
              {!! Form::select('discount_type', [ '' => __('lang_v1.none'), 'fixed' => __( 'lang_v1.fixed' ), 'percentage' => __( 'lang_v1.percentage' )], $purchase->discount_type, ['class' => 'form-control', 'placeholder' => __('messages.please_select')]); !!}
            </div>
          </td>
          <td class="col-md-3">
            <div class="form-group">
              {!! Form::label('discount_amount', __( 'purchase.discount_amount' ) . ':') !!}
              {!! Form::text('discount_amount',

              ($purchase->discount_type == 'fixed' ?
              number_format($purchase->discount_amount/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)
              :
              number_format($purchase->discount_amount, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)
              )
              , ['class' => 'form-control input_number']); !!}
            </div>
          </td>
          <td class="col-md-3">
            &nbsp;
          </td>
          <td class="col-md-3">
            <b>Discount:</b>(-)
            <span id="discount_calculated_amount" class="display_currency">0</span>
          </td>
        </tr>
        <tr>
          <td>
            <div class="form-group">
              {!! Form::label('tax_id', __( 'purchase.purchase_tax' ) . ':') !!}
              <select name="tax_id" id="tax_id" class="form-control select2" placeholder="'Please Select'">
                <option value="" data-tax_amount="0" selected>@lang('lang_v1.none')</option>
                @foreach($taxes as $tax)
                <option value="{{ $tax->id }}" @if($purchase->tax_id == $tax->id) {{'selected'}} @endif data-tax_amount="{{ $tax->amount }}"
                  >
                  {{ $tax->name }}
                </option>
                @endforeach
              </select>
              {!! Form::hidden('tax_amount', $purchase->tax_amount, ['id' => 'tax_amount']); !!}
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
      <label>Term & Condtions</label>
      <select class="form-control" name="tandc_type" id="TCS">
        <option selected disabled> Select</option>
        @foreach ($T_C as $tc)
        @if($tc->id == $purchase->tandc_type)
        <option value="{{$tc->id}}" selected>{{$tc->title}}</option>
        @else
        <option value="{{$tc->id}}">{{$tc->title}}</option>
        @endif
        @endforeach
      </select>

    </div>
  </div>
  <div class="col-sm-12">
    <div class="form-group" id="TandC">
      {!! Form::label('tandc_title',__('Terms & Conditions')) !!}
      {!! Form::textarea('tandc_title', $purchase->tandc_title, ['class' => 'form-control name','id'=>'product_description','rows' => 3]); !!}
    </div>
  </div>
  @endcomponent
  @component('components.widget', ['class' => 'box-primary'])
  <div class="row" id="shipping_div" style="display:none">
    <div class="col-md-4">
      <div class="form-group">
        {!! Form::label('shipping_details', __( 'purchase.shipping_details' ) . ':') !!}
        {!! Form::text('shipping_details', $purchase->shipping_details, ['class' => 'form-control']); !!}
      </div>
    </div>
    <div class="col-md-4 col-md-offset-4">
      <div class="form-group">
        {!! Form::label('shipping_charges','(+) ' . __( 'purchase.additional_shipping_charges') . ':') !!}
        {!! Form::text('shipping_charges', number_format($purchase->shipping_charges/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input_number']); !!}
      </div>
    </div>
  </div>
  <div class="row">
    @php
    $shipping_custom_label_1 = !empty($custom_labels['purchase_shipping']['custom_field_1']) ? $custom_labels['purchase_shipping']['custom_field_1'] : '';

    $is_shipping_custom_field_1_required = !empty($custom_labels['purchase_shipping']['is_custom_field_1_required']) && $custom_labels['purchase_shipping']['is_custom_field_1_required'] == 1 ? true : false;

    $shipping_custom_label_2 = !empty($custom_labels['purchase_shipping']['custom_field_2']) ? $custom_labels['purchase_shipping']['custom_field_2'] : '';

    $is_shipping_custom_field_2_required = !empty($custom_labels['purchase_shipping']['is_custom_field_2_required']) && $custom_labels['purchase_shipping']['is_custom_field_2_required'] == 1 ? true : false;

    $shipping_custom_label_3 = !empty($custom_labels['purchase_shipping']['custom_field_3']) ? $custom_labels['purchase_shipping']['custom_field_3'] : '';

    $is_shipping_custom_field_3_required = !empty($custom_labels['purchase_shipping']['is_custom_field_3_required']) && $custom_labels['purchase_shipping']['is_custom_field_3_required'] == 1 ? true : false;

    $shipping_custom_label_4 = !empty($custom_labels['purchase_shipping']['custom_field_4']) ? $custom_labels['purchase_shipping']['custom_field_4'] : '';

    $is_shipping_custom_field_4_required = !empty($custom_labels['purchase_shipping']['is_custom_field_4_required']) && $custom_labels['purchase_shipping']['is_custom_field_4_required'] == 1 ? true : false;

    $shipping_custom_label_5 = !empty($custom_labels['purchase_shipping']['custom_field_5']) ? $custom_labels['purchase_shipping']['custom_field_5'] : '';

    $is_shipping_custom_field_5_required = !empty($custom_labels['purchase_shipping']['is_custom_field_5_required']) && $custom_labels['purchase_shipping']['is_custom_field_5_required'] == 1 ? true : false;
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
        {!! Form::text('shipping_custom_field_1', $purchase->shipping_custom_field_1 ?? null, ['class' => 'form-control','placeholder' => $shipping_custom_label_1, 'required' => $is_shipping_custom_field_1_required]); !!}
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
        {!! Form::text('shipping_custom_field_2', $purchase->shipping_custom_field_2 ?? null, ['class' => 'form-control','placeholder' => $shipping_custom_label_2, 'required' => $is_shipping_custom_field_2_required]); !!}
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
        {!! Form::text('shipping_custom_field_3', $purchase->shipping_custom_field_3 ?? null, ['class' => 'form-control','placeholder' => $shipping_custom_label_3, 'required' => $is_shipping_custom_field_3_required]); !!}
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
        {!! Form::text('shipping_custom_field_4', $purchase->shipping_custom_field_4 ?? null, ['class' => 'form-control','placeholder' => $shipping_custom_label_4, 'required' => $is_shipping_custom_field_4_required]); !!}
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
        {!! Form::text('shipping_custom_field_5', $purchase->shipping_custom_field_5 ?? null, ['class' => 'form-control','placeholder' => $shipping_custom_label_5, 'required' => $is_shipping_custom_field_5_required]); !!}
      </div>
    </div>
    @endif
  </div>

  <div class="row" style="display:none;">
    <div class="col-md-12 text-center">
      <button type="button" class="btn btn-primary btn-sm" id="toggle_additional_expense"> <i class="fas fa-plus"></i> @lang('lang_v1.add_additional_expenses') <i class="fas fa-chevron-down"></i></button>
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
              {!! Form::text('additional_expense_key_1', $purchase->additional_expense_key_1, ['class' => 'form-control', 'id' => 'additional_expense_key_1']); !!}
            </td>
            <td>
              {!! Form::text('additional_expense_value_1', number_format($purchase->additional_expense_value_1/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input_number', 'id' => 'additional_expense_value_1']); !!}
            </td>
          </tr>
          <tr>
            <td>
              {!! Form::text('additional_expense_key_2', $purchase->additional_expense_key_2, ['class' => 'form-control', 'id' => 'additional_expense_key_2']); !!}
            </td>
            <td>
              {!! Form::text('additional_expense_value_2', number_format($purchase->additional_expense_value_2/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input_number', 'id' => 'additional_expense_value_2']); !!}
            </td>
          </tr>
          <tr>
            <td>
              {!! Form::text('additional_expense_key_3', $purchase->additional_expense_key_3, ['class' => 'form-control', 'id' => 'additional_expense_key_3']); !!}
            </td>
            <td>
              {!! Form::text('additional_expense_value_3', number_format($purchase->additional_expense_value_3/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input_number', 'id' => 'additional_expense_value_3']); !!}
            </td>
          </tr>
          <tr>
            <td>
              {!! Form::text('additional_expense_key_4', $purchase->additional_expense_key_4, ['class' => 'form-control', 'id' => 'additional_expense_key_4']); !!}
            </td>
            <td>
              {!! Form::text('additional_expense_value_4', number_format($purchase->additional_expense_value_4/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input_number', 'id' => 'additional_expense_value_4']); !!}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="row" style="display:none;">
    <div class="col-md-12 text-right">
      {!! Form::hidden('final_total', $purchase->final_total , ['id' => 'grand_total_hidden']); !!}
      <b>@lang('purchase.purchase_total'): </b><span id="grand_total" class="display_currency" data-currency_symbol='true'>{{$purchase->final_total}}</span>
    </div>
  </div>
  @endcomponent

  <div class="col-sm-12 text-center fixed-button">

    @if($purchase->grn_id == null || $purchase->grn_id == 0)
      <a class="btn-big btn-primary " href="/Purchase_invoice/create?convert_id={{$purchase->id}}">Convert To PI</a>
    @endif
      <button type="button" id="submit_purchase_form" class="btn-big btn-primary submit_purchase_form btn-flat" style="margin-right: 5px;" accesskey="s">Update & CLose</button>
      <button class="btn-big btn-danger" type="button" onclick="window.history.back()">Close</button>
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
    $('.purchase_unit_cost_without_discount').trigger('change');
    $('.purchase_line_tax_id').trigger('change')
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
                // purchase_order_table.ajax.reload();
                // location.reload('purchase-order.edit');
                window.location = '/purchases';

              } else {
                toastr.error(result.msg);
              }
            },
          });
        }
      });
    });
    $(".get_product").trigger("change");
    update_table_total();
    update_grand_total();
    __page_leave_confirmation('#add_purchase_form');
    $("#is_purchase_order_dd").change(function() {
      if ($(this).val() == 14) {
        $("#shipping_div").show()
      } else {
        $("#shipping_div").hide()
      }
    });
    $('#TandC').hide();
    $("#TCS").change(function() {
      var id = $("#TCS").val();
      if(id !='' || id !=null)
      {
      $.ajax({
        type: "GET",
        url: '/get_term/' + id,
        // dataType: "text"
        success: function(data) {
          if(data !='' || data !=null)
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


  function get_product_code(el) {

    var terms = $(el).val();
    $.getJSON(
      '/purchases/get_products', {
        term: terms
      },
      function(data) {
        $(this).val(data[0].unit.actual_name);
        console.log(data[0]);
        $(el).closest("tr").find(".product_code").val(data[0].sku);
        
        
        // $(el).closest("tr").find(".uom").val(data[0].unit.actual_name);
         if(data[0].unit.is_purchase_unit ==1)
            {
                $(el).closest("tr").find(".uom").val(data[0].shipping_custom_field_1);
            }else{
                $(el).closest("tr").find(".uom").val(data[0].unit.actual_name);
                
            }
        
        $(el).closest("tr").find(".category_type").val(data[0].category_id);
        // $(el).closest("tr").find(".base_unit").val(data[0].unit.base_unit_multiplier);
        if (data[0] && data[0].unit && data[0].unit.base_unit_multiplier && data[0].unit.is_purchase_unit == 1 && data[0].unit.base_unit_multiplier > 0 ) {
            $(el).closest("tr").find(".base_unit").val(data[0].unit.base_unit_multiplier);
        } else {
            $(el).closest("tr").find(".base_unit").val(1);
        }
        
        $(el).closest("tr").find(".gross__weight").val(data[0].weight);
        $(el).closest("tr").find(".net__weight").val(data[0].product_custom_field1);
        $(el).closest("tr").find(".old_quantity_purchase").val(data[0].alert_quantity);



        update_table_total();
        $('.purchase_quantity').trigger('change');

      });
  }

  function add_row(el) {
    $('#purchase_entry_table tbody tr').each(function() {
      $(this).find('#search_product,.brand_select').select2('destroy')


    })

    $('#purchase_entry_table tbody tr').each(function() {
      $(this).find('[name*=store]').select2('destroy')

    })

    var tr = $("#purchase_entry_table #tbody tr:last").clone();
    tr.find('input').val('');
    tr.find('textarea').val('');
    // console.log(tr);
    $("#purchase_entry_table #tbody tr:last").after(tr);


    reIndexTable();
    update_table_sr_number();

  }

  function reIndexTable() {
    var j = 0
    $('#purchase_entry_table tbody tr').each(function() {
      $(this).find('#search_product,.brand_select ').select2()
      $(this).find('[name*=store]').select2()
      $(this).attr('id', j)
      $(this).find('[name*=store]').attr('name', "purchases[" + j + "][store]")
      $(this).find('[name*=item_code]').attr('name', "purchases[" + j + "][item_code]")
      $(this).find('[name*=item_description]').attr('name', "purchases[" + j + "][item_description]")
      $(this).find('[name*=quantity]').attr('name', "purchases[" + j + "][quantity]")
      $(this).find('[name*=product_id]').attr('name', "purchases[" + j + "][product_id]")
      $(this).find('[name*=pp_without_discount]').attr('name', "purchases[" + j + "][pp_without_discount]")
      $(this).find('[name*=discount_percent]').attr('name', "purchases[" + j + "][discount_percent]")
      $(this).find('[name*=purchase_price]').attr('name', "purchases[" + j + "][purchase_price]")
      $(this).find('[name*=purchase_price_inc_tax]').attr('name', "purchases[" + j + "][purchase_price_inc_tax]")
      $(this).find('[name*=purchase_line_tax_id]').attr('name', "purchases[" + j + "][purchase_line_tax_id]")
      $(this).find('[name*=profit_percent]').attr('name', "purchases[" + j + "][profit_percent]")
      $(this).find('[name*=item_tax]').attr('name', "purchases[" + j + "][item_tax]")
      $(this).find('[name*=variation_id]').attr('name', "purchases[" + j + "][variation_id]")
      $(this).find('[name*=purchase_line_id]').attr('name', "purchases[" + j + "][purchase_line_id]")
      $(this).find('[name*=product_unit_id]').attr('name', "purchases[" + j + "][product_unit_id]")
      $(this).find('[name*=po_qty_change]').attr('name', "purchases[" + j + "][po_qty_change]")

      j++;
    });
  }

  function remove_row(el) {
    var tr_length = $("#purchase_entry_table #tbody tr").length;
    if (tr_length > 1) {
      var tr = $(el).closest("tr").remove();
      reIndexTable();
      update_table_sr_number();
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


  // 

  $("#purchase_order_ids").on("select2:select", function(e) {
        var purchase_order_id = e.params.data.id;
        var row_count = $('#row_count').val();
        $.ajax({
            url: '/get-purchase-order-lines/' + purchase_order_id + '?row_count=' + row_count,
            dataType: 'json',
            success: function(data) {
              //   $("#purchase_entry_table #tbody tr").remove();
              var IsRemoved = $('#purchase_entry_table').attr('_isRemoved')
              if (IsRemoved == 'true') {} else {
                $("#purchase_entry_table #tbody tr").remove();
                // $('#purchase_entry_table').attr('_isRemoved','true')
              }
              $('.purchase_type option').removeAttr("selected")
              $('.purchase_type option[value=' + data.po.purchase_type + ']').attr("selected", true)
              $("#additional_notes").val(data.po.additional_notes);

              $('.purchase_category option').removeAttr("selected")
              $('.purchase_category  option[value=' + data.po.purchase_category + ']').attr("selected", true);
              $('.purchase__type_edit').trigger('change');
              $('.products_change').select2();
              $('.products_change').trigger('change');
              $('#supplier').trigger('change');

              set_po_values(data.po);
              append_purchase_lines(data.html, row_count);
              $('.purchase_unit_cost_without_discount').trigger('change'); reIndexTable();
              $('.products_change').trigger('change');
              },
            });

        });


      $("#purchase_order_ids").on("select2:unselect", function(e) {
        var purchase_order_id = e.params.data.id;
        $('#purchase_entry_table tbody').find('tr').each(function() {
          if (typeof($(this).data('purchase_order_id')) !== 'undefined' &&
            $(this).data('purchase_order_id') == purchase_order_id) {
            $(this).remove();
          }
        });
      });


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
        $('.transporter,#tcs').trigger('change');
        $('.purchase_quantity').trigger('change');
        $('.contact_id').trigger('change');

        setTimeout(function() {
                $('.purchase_line_tax_id').trigger('change');
       
            }, 5000);
        

      })
</script>
@include('purchase.partials.keyboard_shortcuts')
@endsection