@php
$hide_tax = '';
if( session()->get('business.enable_inline_tax') == 0){
$hide_tax = 'hide';
}
$currency_precision = config('constants.currency_precision', 2);
$quantity_precision = config('constants.quantity_precision', 2);
@endphp
<div class="table-responsive">
   <table class="table table-condensed table-bordered table-th-green text-center table-striped" 
      id="purchase_entry_table_req" >
      <thead>
         <tr>
            <th style="text-align: center;">
               <i class="fa fa-trash" aria-hidden="true"></i>
            </th>
            <th>#</th>
            <th class="hide">Store</th>
            <th>Sku</th>
            <th width=20%>Product</th>
            <th width=12%>Brand</th>
            <th>Product Description</th>
            <th width=6%>UOM</th>
            <th width=6%> Qty</th>
            @if($purchase->type!="Purchase Requisition")
            <th>@lang( 'lang_v1.unit_cost_before_discount' )</th>
            <th>@lang( 'lang_v1.discount_percent' )</th>
            <th>@lang( 'purchase.unit_cost_before_tax' )</th>
            <th class="{{$hide_tax}}">@lang( 'purchase.subtotal_before_tax' )</th>
            <th class="{{$hide_tax}}">@lang( 'purchase.product_tax' )</th>
            <th class="{{$hide_tax}}">@lang( 'purchase.net_cost' )</th>
            <th>@lang( 'purchase.line_total' )</th>
            @else
            @endif
            <th class="@if(!session('business.enable_editing_product_from_purchase') || !empty($is_purchase_order)) hide @endif">
               @lang( 'lang_v1.profit_margin' )
            </th>
            @if(empty($is_purchase_order))
            <th>@lang( 'purchase.unit_selling_price') <small>(@lang('product.inc_of_tax'))</small></th>
            @if(session('business.enable_lot_number'))
            <th>
               @lang('lang_v1.lot_number')
            </th>
            @endif
            @if(session('business.enable_product_expiry'))
            <th>@lang('product.mfg_date') / @lang('product.exp_date')</th>
            @endif
            @endif
         </tr>
      </thead>
      <tbody id="tbody">
         <?php $row_count = 0; ?>
         @foreach($purchase->purchase_lines as $purchase_line)
         <tr @if(!empty($purchase_line->purchase_order_line) && !empty($common_settings['enable_purchase_order'])) data-purchase_order_id="{{$purchase_line->purchase_order_line->transaction_id}}" @endif id="{{$row_count}}" >
         <td> 
            <button class="btn btn-danger remove" type="button" onclick="remove_req(this)" style="padding: 0px 5px 2px 5px;"><i class="fa fa-trash" aria-hidden="true"></i></button>
         </td>
         <td class="sr_number"></td>
         <td class="hide">
            <div class="col-sm-3">
               <div class="form-group">
                  <select name="purchases[{{$loop->index}}][store]" class="form-control select2 " style="width:200px">
                     @foreach($store as $v)
                     @if($v->name  == $purchase_line->store)
                     <option value="{{ $v->name }}" selected >{{ $v->name  }}</option>
                     @else
                     <option value="{{ $v->name }}" >{{ $v->name  }}</option>
                     @endif
                     @endforeach 
                  </select>
                  <!--{!! Form::select('purchases[' .$loop->index. '][store]',$store,null, ['class' => 'form-control ','style'=>'width:200px' ]); !!}-->
               </div>
            </div>
         </td>
         <td>
            <input type="text" value="{{ $purchase_line->item_code }}" readonly name="purchases[{{$row_count}}][item_code]" class="form-control product_code" style="width: 90px;">
         </td>
         <td>
 			<input type="hidden" name="gross_weight" class="gross__weight">
			<input type="hidden" name="net_weight" class="net__weight">
            <select name="purchases[{{$loop->index}}][product_id]" class="form-control select2 get_product products_change" required style="width: 200px" id="search_product" onchange="get_product_code_req(this)">
               @foreach ($product as $p)
               @if($purchase_line->product_id == $p->product_id)
               <option selected value="{{ $p->product_id}}">{{$p->name}}</option>
               @else
               <option value="{{ $p->product_id}}">{{$p->name}}</option>
               @endif
               @endforeach
            </select>
         </td>
         <td>
         <select class="form-control select2" name="purchases[{{$loop->index}}][brand_id]" id="brand_id">
         <option value="">Please select</option>
			@foreach($brand as $br)
			<option value="{{$br->id}}" {{ ($br->id == $purchase_line->brand_id) ? 'selected' : '' }}>{{$br->name}}</option>
			@endforeach
		</select>
         </td>
         <td>
            <!--<input type="text" value="{{ $purchase_line->item_description }}" name="purchases[{{$loop->index}}][item_description]" class="form-control" style="width: 90px;">-->
            <textarea name="purchases[{{$loop->index}}][item_description]"  class="form-control" rows="2">{{ $purchase_line->item_description }}</textarea>
         </td>
         <td>  <input type="text" class="form-control uom" readonly style="width:"></td>
         <td>
            @if(!empty($purchase_line->purchase_order_line_id) && !empty($common_settings['enable_purchase_order']))
            {!! Form::hidden('purchases[{{$loop->index}}][purchase_order_line_id]', $purchase_line->purchase_order_line_id ); !!}
            @endif
            {!! Form::hidden('purchases['.$loop->index.'][variation_id]', $purchase_line->variation_id ); !!}
            {!! Form::hidden('purchases['.$loop->index.'][purchase_line_id]',
            $purchase_line->id); !!}
            @php
            $check_decimal = 'false';
            if($purchase_line->product->unit->allow_decimal == 0){
            $check_decimal = 'true';
            }
            $max_quantity = 0;
            if(!empty($purchase_line->purchase_order_line_id) && !empty($common_settings['enable_purchase_order'])){
            $max_quantity = $purchase_line->purchase_order_line->quantity - $purchase_line->purchase_order_line->po_quantity_purchased + $purchase_line->quantity;
            }
            @endphp
            <input type="text" 
            name="purchases[{{$loop->index}}][quantity]" 
            value="{{$purchase_line->quantity}}"
            class="form-control input-sm purchase_quantity input_number mousetrap"
            required
            maxlength="5"
            data-rule-abs_digit={{$check_decimal}}
            data-msg-abs_digit="{{__('lang_v1.decimal_value_not_allowed')}}"
            @if(!empty($max_quantity))
            data-rule-max-value="{{$max_quantity}}"
            data-msg-max-value="{{__('lang_v1.max_quantity_quantity_allowed', ['quantity' => $max_quantity])}}" 
            @endif
            >
            <input type="hidden" class="base_unit_cost" value="{{$purchase_line->variations->default_purchase_price??''}}">
            @if(!empty($purchase_line->sub_units_options))
            <br>
            <select name="purchases[{{$loop->index}}][sub_unit_id]" class="form-control input-sm sub_unit">
            @foreach($purchase_line->sub_units_options as $sub_units_key => $sub_units_value)
            <option value="{{$sub_units_key}}" 
            data-multiplier="{{$sub_units_value['multiplier']}}"
            @if($sub_units_key == $purchase_line->sub_unit_id) selected @endif>
            {{$sub_units_value['name']}}
            </option>
            @endforeach
            </select>
            @else
            @endif
            <input type="hidden" name="purchases[{{$loop->index}}][product_unit_id]" value="{{$purchase_line->product->unit->id}}">
            <input type="hidden" class="base_unit_selling_price" value="{{$purchase_line->variations->sell_price_inc_tax??''}}">
         </td>
         {{-- {{dd($purchase)}} --}}
         <td class="hide">
            {!! Form::text('purchases['.$loop->index.'][pp_without_discount]', number_format($purchase_line->pp_without_discount/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm purchase_unit_cost_without_discount input_number', 'required']); !!}
         </td>
         <td class="hide">
            {!! Form::text('purchases['.$loop->index.'][discount_percent]', number_format($purchase_line->discount_percent, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm inline_discounts input_number', 'required']); !!} <b>%</b>
         </td>
         <td class="hide">
            {!! Form::text('purchases['.$loop->index.'][purchase_price]', 
            number_format($purchase_line->purchase_price/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm purchase_unit_cost input_number', 'required']); !!}
         </td>
         <td class="{{$hide_tax}} hide">
            <span class="row_subtotal_before_tax">
            {{number_format($purchase_line->quantity * $purchase_line->purchase_price/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}
            </span>
            <input type="hidden" class="row_subtotal_before_tax_hidden" value="{{number_format($purchase_line->quantity * $purchase_line->purchase_price/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}">
         </td>
         <td class="{{$hide_tax}} hide">
            <div class="input-group">
               <select name="purchases[{{$loop->index }}][purchase_line_tax_id]" class="form-control input-sm purchase_line_tax_id" placeholder="'Please Select'">
               <option value="" data-tax_amount="0" @if( empty( $purchase_line->tax_id ) )
               selected @endif >@lang('lang_v1.none')</option>
               @foreach($taxes as $tax)
               <option value="{{ $tax->id }}" data-tax_amount="{{ $tax->amount }}" @if( $purchase_line->tax_id == $tax->id) selected @endif >{{ $tax->name }}</option>
               @endforeach
               </select>
               <span class="input-group-addon purchase_product_unit_tax_text">
               {{number_format($purchase_line->item_tax/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}
               </span>
               {!! Form::hidden('purchases['.$loop->index.'][item_tax]', number_format($purchase_line->item_tax/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'purchase_product_unit_tax']); !!}
            </div>
         </td>
         <td class="{{$hide_tax}} hide">
            {!! Form::hidden('purchases['.$loop->index.'][purchase_price_inc_tax]',0, ['class' => 'form-control input-sm purchase_unit_cost_after_tax input_number', 'required']); !!}
         </td>
         <td class="hide">
            <span class="row_subtotal_after_tax">
            {{number_format($purchase_line->purchase_price_inc_tax * $purchase_line->quantity/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}
            </span>
            <input type="hidden" class="row_subtotal_after_tax_hidden" value="{{number_format($purchase_line->purchase_price_inc_tax * $purchase_line->quantity/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}">
         </td>
         <td class="@if(!session('business.enable_editing_product_from_purchase') || !empty($is_purchase_order)) hide @endif">
            @php
            $pp = $purchase_line->purchase_price_inc_tax;
            $sp = $purchase_line->variations->sell_price_inc_tax??'';
            if(!empty($purchase_line->sub_unit->base_unit_multiplier)) {
            $sp = $sp * $purchase_line->sub_unit->base_unit_multiplier;
            }
            if($pp == 0){
            $profit_percent = 100;
            } else {
            $profit_percent = (( $pp) * 100 / $pp);
            }
            @endphp
            {!! Form::text('purchases['.$row_count.'][profit_percent]', 
            number_format($profit_percent, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), 
            ['class' => 'form-control input-sm input_number profit_percent', 'required']); !!}
         </td>
         @if(empty($is_purchase_order))
         <td>
            @if(session('business.enable_editing_product_from_purchase'))
            {!! Form::text('purchases[{{$row_count}}][default_sell_price]', number_format($sp, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm input_number default_sell_price', 'required']); !!}
            @else
            {{number_format($sp, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}
            @endif
         </td>
         @if(session('business.enable_lot_number'))
         <td>
            {!! Form::text('purchases[{{ $loop->index}}][lot_number]', $purchase_line->lot_number, ['class' => 'form-control input-sm']); !!}
         </td>
         @endif
         @if(session('business.enable_product_expiry'))
         <td style="text-align: left;">
            @php
            $expiry_period_type = !empty($purchase_line->product->expiry_period_type) ? $purchase_line->product->expiry_period_type : 'month';
            @endphp
            @if(!empty($expiry_period_type))
            <input type="hidden" class="row_product_expiry" value="{{ $purchase_line->product->expiry_period }}">
            <input type="hidden" class="row_product_expiry_type" value="{{ $expiry_period_type }}">
            @if(session('business.expiry_type') == 'add_manufacturing')
            @php
            $hide_mfg = false;
            @endphp
            @else
            @php
            $hide_mfg = true;
            @endphp
            @endif
            <b class="@if($hide_mfg) hide @endif"><small>@lang('product.mfg_date'):</small></b>
            @php
            $mfg_date = null;
            $exp_date = null;
            if(!empty($purchase_line->mfg_date)){
            $mfg_date = $purchase_line->mfg_date;
            }
            if(!empty($purchase_line->exp_date)){
            $exp_date = $purchase_line->exp_date;
            }
            @endphp
            <div class="input-group @if($hide_mfg) hide @endif">
               <span class="input-group-addon">
               <i class="fa fa-calendar"></i>
               </span>
               {!! Form::text('purchases[{{$loop->index}}][mfg_date]', !empty($mfg_date) ? @format_date($mfg_date) : null, ['class' => 'form-control input-sm expiry_datepicker mfg_date', 'readonly']); !!}
            </div>
            <b><small>@lang('product.exp_date'):</small></b>
            <div class="input-group">
               <span class="input-group-addon">
               <i class="fa fa-calendar"></i>
               </span>
               {!! Form::text('purchases[{{$loop->index}}][exp_date]', !empty($exp_date) ? @format_date($exp_date) : null, ['class' => 'form-control input-sm expiry_datepicker exp_date', 'readonly']); !!}
            </div>
            @else
            <div class="text-center">
               @lang('product.not_applicable')
            </div>
            @endif
         </td>
         @endif
         @endif
         </tr>
         <?php $row_count = $loop->index + 1 ; ?>
         @endforeach
      </tbody>
   </table>
</div>
<input type="hidden" id="row_count" value="{{ $row_count }}">