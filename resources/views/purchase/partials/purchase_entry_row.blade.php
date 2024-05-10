@foreach( $variations as $variation)

    <tr @if(!empty($purchase_order_line)) data-purchase_order_id="{{$purchase_order_line->transaction_id}}" @endif>
       
        <td> 
            <button class="btn btn-danger remove" type="button" onclick="remove_row(this)" style="padding: 0px 5px 2px 5px;"><i class="fa fa-trash" aria-hidden="true"></i></button>
            </td>

        <td><span class="sr_number"></span>
            
        	<input type="hidden" name="gross_weight" class="gross__weight">
		<input type="hidden" name="net_weight" class="net__weight">
        </td>
  
       
        <td class="hide">
            <div class="col-sm-3">
				<div class="form-group">
                

                    <select name="purchases[{{ $row_count }}][store]" class="form-control select2 input-sm purchase_line_tax_id" placeholder="'Please Select'" style="width: 90px">
                       
                        @foreach ($store as $store)
                        @if($store->name == $purchase_order_line->store )
                            <option value="{{ $store->name }}" selected >{{ $store->name  }}</option>
                        @else
                         <option value="{{ $store->name }}" >{{ $store->name  }}</option>
                        @endif
                    
                    @endforeach
                    </select>
                </div>
			</div>
        </td>
        <td>
            <input type="text" value="{{ $product->sku }}" readonly name="purchases[{{$row_count}}][item_code]" class="form-control product_code " style="width: 121px;">
        </td>


        <td>
            <!--{{ $product->name }} -->
             <select  name="purchases[' . $row_count . '][product_id]" class="form-control select2 products_change" id ="search_product" onchange="get_product_code(this)" style="width: 200px;"> 
            @foreach ($product_name as $product_name)
            @if($product_name->name ==$product->name )
                <option selected value="{{$product_name->id}}">{{$product_name->name}}</option>
                @else
                <option  value="{{$product_name->id}}">{{$product_name->name}}</option>
                @endif
            @endforeach
         </select>
        
            
        </td>
        <td>
            <select class="form-control select2 brand_select" name="purchases[{{$row_count}}][brand_id]">
                <option value="">Please Select</option>
                @foreach($brand as $br)
                <option value="{{$br->id}}" {{ ($br->id == $purchase_order_line->brand_id) ? 'selected' : '' }}>{{$br->name}}</option>
                @endforeach
            </select>
        </td>
          @php
                $check_decimal = 'false';
                if($product->unit->allow_decimal == 0){
                    $check_decimal = 'true';
                }
                $currency_precision = config('constants.currency_precision', 2);
                $quantity_precision = config('constants.quantity_precision', 2);
                if($purchase_order_line->r_quantity != null){
                $quantity_value = $purchase_order_line->quantity;
                }
                else {
                    $quantity_value = !empty($purchase_order_line) ? $purchase_order_line->quantity : 1;
                }
                $max_quantity = !empty($purchase_order_line) ? $purchase_order_line->quantity - $purchase_order_line->po_quantity_purchased : 0;
            @endphp

        <td>
            
            <textarea name="purchases[{{$row_count}}][item_description]" rows="1" class="form-control">{{$purchase_order_line->item_description }}</textarea>
        </td>
        <td>
            <input type="hidden" name="base_unit" value="{{$product->unit->base_unit_multiplier}}" class="base_unit">
            <input type="hidden" name="category_type" class="category_type" value="{{$product->category_id}}">
            <input type="text" name="purchases[{{$row_count}}][sub_unit_id]" value="{{$product->unit->actual_name}}"  class="form-control input-sm uom sub_unit" readonly>
                
            @if(!empty($purchase_order_line))
                {!! Form::hidden('purchases[' . $row_count . '][purchase_order_line_id]', $purchase_order_line->id ); !!}
            @endif

            <!--{!! Form::hidden('purchases[' . $row_count . '][product_id]', $product->id ); !!}-->
            {!! Form::hidden('purchases[' . $row_count . '][variation_id]', "0" , ['class' => 'hidden_variation_id']); !!}

          
                <input type="hidden" name="transporter_rate" class="transporter_rate" />
                <input type="hidden" name="contractor_rate" class="contractor_rate" />
            
    


            <input type="hidden" class="base_unit_cost" value="0">
            <input type="hidden" class="base_unit_selling_price" value="0">

            <input type="hidden" name="purchases[{{$row_count}}][product_unit_id]" value="{{$product->unit->id}}">
            @if(!empty($sub_units))
                <br>
               
                
             
        
            @else 
                {{ $product->unit->short_name }}
            @endif
        </td>
        <td>
       
                    <input type="number" 
                    {{-- max="{{$quantity_value}}" --}}
                name="purchases[{{$row_count}}][quantity]" 
                value="{{$quantity_value}}"
                min="1"
                class="form-control input-sm purchase_quantity input_number mousetrap"
                required
                data-rule-abs_digit={{$check_decimal}}
                data-msg-abs_digit="{{__('lang_v1.decimal_value_not_allowed')}}"style="width: 61px;">



            <input type="hidden" name="purchases[{{$row_count}}][po_qty_change]" class="old_quantity_purchase">
        </td>
       
        <td>
            @php
                $pp_without_discount = !empty($purchase_order_line) ? $purchase_order_line->pp_without_discount/$purchase_order->exchange_rate : $variation->default_purchase_price;

                $discount_percent = !empty($purchase_order_line) ? $purchase_order_line->discount_percent : 0;

                $purchase_price = !empty($purchase_order_line) ? $purchase_order_line->purchase_price/$purchase_order->exchange_rate : $variation->default_purchase_price;

                $tax_id = !empty($purchase_order_line) ? $purchase_order_line->tax_id : $product->tax;
            @endphp
            {!! Form::text('purchases[' . $row_count . '][pp_without_discount]',
            number_format($pp_without_discount, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm purchase_unit_cost_without_discount input_number', 'required']); !!}
        </td>
        <td class="hide">
            {!! Form::text('purchases[' . $row_count . '][discount_percent]', number_format($discount_percent, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm inline_discounts input_number', 'required']); !!}
        </td>
        <td class="hide">
            {!! Form::text('purchases[' . $row_count . '][purchase_price]',
            number_format($purchase_price, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm purchase_unit_cost input_number', 'required']); !!}
        </td>
        <td class="{{$hide_tax}}">
            <span class="row_subtotal_before_tax display_currency">0</span>
            <input type="hidden" class="row_subtotal_before_tax_hidden" value=0>
        </td>
        <td class="{{$hide_tax}}">
            <div class="input-group">
                <select name="purchases[{{ $row_count }}][purchase_line_tax_id]" class="form-control select2 input-sm purchase_line_tax_id" placeholder="'Please Select'">
                    <option value="" data-tax_amount="0" @if( $hide_tax == 'hide' )
                    selected @endif >@lang('lang_v1.none')</option>
                    @foreach($taxes as $tax)
                        <option value="{{ $tax->id }}" data-tax_amount="{{ $tax->amount }}" @if( $tax_id == $tax->id && $hide_tax != 'hide') selected @endif >{{ $tax->name }}</option>
                    @endforeach
                </select>
                {!! Form::hidden('purchases[' . $row_count . '][item_tax]', 0, ['class' => 'purchase_product_unit_tax']); !!}
                <span class="input-group-addon purchase_product_unit_tax_text">
                    0.00</span>
            </div>
        </td>
        
        @if($purchase_order->type == "purchase")
            <td class="text-center">
				<input type="hidden" class="form-control further_tax_hidden" name="purchases[0][item_further_tax]" />
				<div class="input-group">
					<select name="purchases[0][further_taax_id]" class="form-control select2 input-sm further_tax" id="further_tax" placeholder="Please Select">
						<option value="0" data-rate="0">NONE</option>
						<option value="1" data-rate="5" {{ ($purchase_line->further_tax == 1) ? 'selected' : '' }}>5</option> 
						<option value="2" data-rate="10" {{ ($purchase_line->further_tax == 2) ? 'selected' : '' }}>10</option>
					</select>
					{!! Form::hidden('purchases[0][further_item_tax]', 0, ['class' => 'purchase_product_unit_further_tax']); !!}
					<span class="input-group-addon purchase_product_unit_further_tax_text">0.00</span>
				</div>
			</td>
			<td>
                <input type="number" name="purchases[0][salesman_commission_rate]" class="form-control salesman_commission_rate" value="{{ $purchase_line->salesman_commission }}"/>
			</td>
		@endif
            
        
        
        <td class="hide">
     
            {!! Form::text('purchases[' . $row_count . '][purchase_price_inc_tax]',"0", ['class' => 'form-control input-sm purchase_unit_cost_after_tax input_number', 'required']); !!}
        </td>
        <td>
            <span class="row_subtotal_after_tax display_currency">0</span>
            <input type="hidden" class="row_subtotal_after_tax_hidden" value=0>
        </td>
        <td class="@if(!session('business.enable_editing_product_from_purchase') || !empty($is_purchase_order)) hide @endif" style="display: none;">
            {!! Form::text('purchases[' . $row_count . '][profit_percent]',"0", ['class' => 'form-control input-sm input_number profit_percent', 'required']); !!}
        </td>
        @if(empty($is_purchase_order))
        <td style="display: none;">
            @if(session('business.enable_editing_product_from_purchase'))
                {!! Form::text('purchases[' . $row_count . '][default_sell_price]',"0", ['class' => 'form-control input-sm input_number default_sell_price', 'required']); !!}
            @else
        
            @endif
        </td>
        @if(session('business.enable_lot_number'))
            <td>
                {!! Form::text('purchases[' . $row_count . '][lot_number]', null, ['class' => 'form-control input-sm']); !!}
            </td>
        @endif
        @if(session('business.enable_product_expiry'))
            <td style="text-align: left;">

                {{-- Maybe this condition for checkin expiry date need to be removed --}}
                @php
                    $expiry_period_type = !empty($product->expiry_period_type) ? $product->expiry_period_type : 'month';
                @endphp
                @if(!empty($expiry_period_type))
                <input type="hidden" class="row_product_expiry" value="{{ $product->expiry_period }}">
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
                <div class="input-group @if($hide_mfg) hide @endif">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    {!! Form::text('purchases[' . $row_count . '][mfg_date]', null, ['class' => 'form-control input-sm expiry_datepicker mfg_date', 'readonly']); !!}
                </div>
                <b><small>@lang('product.exp_date'):</small></b>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    {!! Form::text('purchases[' . $row_count . '][exp_date]', null, ['class' => 'form-control input-sm expiry_datepicker exp_date', 'readonly']); !!}
                </div>
                @else
                <div class="text-center">
                    @lang('product.not_applicable')
                </div>
                @endif
            </td>
        @endif
        @endif
        <?php $row_count++ ;?>

    </tr>
@endforeach

<input type="hidden" id="row_count" value="{{ $row_count }}">