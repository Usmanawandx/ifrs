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
    id="purchase_entry_table" style="width: 160%; max-width: 160%;">
        <thead>
              <tr>
                <th style="text-align: center;">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </th>
                <th>#</th>
                <th class="hide">Store</th>
			    <th>SKU</th>
			    <th width=20%>Product</th>
                <th >Brand</th>
			    <th>Product Description</th>
			    <th width=5%>UOM</th>
                <th width=5%>Qty</th>
                @if($purchase->type!="Purchase Requisition")
                <th width=5%>Rate</th>
                <th class="hide">@lang( 'lang_v1.discount_percent' )</th>
                {{-- <th>@lang( 'purchase.unit_cost_before_tax' )</th> --}}
                <th class="{{$hide_tax}}">Amount</th>
                <th>Sales Tax</th>
                @if($purchase->type == "Purchase_invoice")
                    <th>Further Tax</th>
                    <th style="width: 4%;">Salesman Commission</th>
                @endif
                {{-- <th>Tax Amount</th> --}}
                <th>Net Amount After Tax</th>
                @else
                @endif
                <!--<th class="@if(!session('business.enable_editing_product_from_purchase') || !empty($is_purchase_order)) hide @endif">-->
                <!--    @lang( 'lang_v1.profit_margin' )-->
                <!--</th>-->
                @if(empty($is_purchase_order))
                    <!--<th>@lang( 'purchase.unit_selling_price') <small>(@lang('product.inc_of_tax'))</small></th>-->
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
    <!--if($$purchase_line)-->
      <input type="hidden" 
    name="purchases[{{$loop->index}}][old_quantity]" 
    value="{{ $purchase_line->transaction->custom_field_4=='convert' ? 0 : @format_quantity($purchase_line->quantity)}}"
    class="form-control input-sm input_number mousetrap">

        <tr @if(!empty($purchase_line->purchase_order_line) && !empty($common_settings['enable_purchase_order'])) data-purchase_order_id="{{$purchase_line->purchase_order_line->transaction_id}}" @endif>
            <td><button class="btn btn-danger remove" type="button" onclick="remove_row(this)" style="padding: 0px 2px 0px 2px;"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
            <td><span class="sr_number"></span></td>
            <td class="hide">
          
             
                <div class="col-sm-3">
                    <div class="form-group">
                        <select name="purchases[{{$loop->index}}][store]" class="form-control select2 input-sm purchase_line_tax_id" placeholder="'Please Select'" style="width: 90px">
                     
                            @foreach($store as $v)
                
                            @if($v->name  == $purchase_line->store)

                            <option value="{{ $v->name }}" selected >{{ $v->name  }}</option>
                            @else
                            <option value="{{ $v->name }}" >{{ $v->name  }}</option>
                            @endif
                             @endforeach 
                    
                        </select>
                    </div>
                </div>
            </td>
            <td>
                <input type="text" value="{{ $purchase_line->item_code }}" readonly name="purchases[{{$loop->index}}][item_code]" class="form-control product_code" style="width: 90px;">
            </td>
    
            <td>


   
<select name="purchases[{{$loop->index}}][product_id]" class="form-control get_product select2 products_change" id="search_product" style="width: 200px" onchange="get_product_code(this)">
  
    @foreach ($product as $p)

@if($purchase_line->product->id == $p->product_id)
   <option selected value="{{$p->product_id}}">{{$p->name}}</option>    
@else
<option value="{{$p->product_id}}">{{$p->name}}</option>    
@endif
@endforeach
</select>  


<input type="hidden" name="gross_weight" class="gross__weight">
									<input type="hidden" name="net_weight" class="net__weight">
</td>	
    <td>
		<select class="form-control select2 brand_select" name="purchases[{{$loop->index}}][brand_id]">
        <option value="">Please Select</option>
			@foreach($brand as $br)
			<option value="{{$br->id}}" {{ ($br->id == $purchase_line->brand_id) ? 'selected' : '' }}>{{$br->name}}</option>
			@endforeach
		</select>

	</td>
            <td>
                <!--<input type="text" value="{{ $purchase_line->item_description }}" name="purchases[{{$loop->index}}][item_description]" class="form-control" style="width: 90px;">-->
             <textarea name="purchases[{{$loop->index}}][item_description]"  class="form-control" rows="2">{{ $purchase_line->item_description  }}</textarea>
            </td>

             <td>  <input type="text" class="form-control uom" readonly style="width:"></td>
            <td>
                @if(!empty($purchase_line->purchase_order_line_id) && !empty($common_settings['enable_purchase_order']))
                    {!! Form::hidden('purchases[' . $loop->index . '][purchase_order_line_id]', $purchase_line->purchase_order_line_id ); !!}
                @endif
                {!! Form::hidden('purchases[' . $loop->index . '][variation_id]', $purchase_line->variation_id ); !!}
                {!! Form::hidden('purchases[' . $loop->index . '][purchase_line_id]',
                $purchase_line->id); !!}

                @php
                    $check_decimal = 'false';
                    if($purchase_line->product->unit->allow_decimal == 0){
                        $check_decimal = 'true';
                    }
                    $max_quantity = 0;

                    if(!empty($purchase_line->purchase_order_line_id) && !empty($common_settings['enable_purchase_order'])){
                     
                    }
                @endphp

                <input type="text" 
                name="purchases[{{$loop->index}}][quantity]" 
                value="{{$purchase_line->quantity}}"
                class="form-control input-sm purchase_quantity input_number mousetrap"
                
                data-rule-abs_digit={{$check_decimal}}
                data-msg-abs_digit="{{__('lang_v1.decimal_value_not_allowed')}}"
                @if(!empty($max_quantity))
                    data-rule-max-value="{{$max_quantity}}"
                    data-msg-max-value="{{__('lang_v1.max_quantity_quantity_allowed', ['quantity' => $max_quantity])}}" 
                @endif
                min="1"
                >
                
                <input type="hidden" name="transporter_rate" class="transporter_rate" />
                <input type="hidden" name="contractor_rate" class="contractor_rate" />
                
                <input type="hidden" name="purchases[{{$loop->index}}][po_qty_change]" class="old_quantity_purchase">
                <input type="hidden" name="purchases[{{$loop->index}}][unit_price_up]" class="unit_price_up" />
                <input type="hidden" name="base_unit" value="{{$purchase_line->product->unit->base_unit_multiplier}}" class="base_unit">
				<input type="hidden" name="category_type" class="category_type">
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
            @if($purchase->type=="Purchase Requisition")
            <td class="hide">
                {!! Form::text('purchases[' . $loop->index . '][pp_without_discount]', number_format($purchase_line->pp_without_discount/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm purchase_unit_cost_without_discount input_number']); !!}
            </td>
            @else
            <td >
                {!! Form::text('purchases[' . $loop->index . '][pp_without_discount]', number_format($purchase_line->pp_without_discount/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm purchase_unit_cost_without_discount input_number']); !!}
            </td>
            @endif
            @if($purchase->type=="Purchase Requisition")
            <td class="hide">
                {!! Form::text('purchases[' . $loop->index . '][discount_percent]', number_format($purchase_line->discount_percent, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm inline_discounts input_number', 'required']); !!} <b>%</b>
            </td>
            
            @else
            <td class="hide">
                {!! Form::text('purchases[' . $loop->index . '][discount_percent]', number_format($purchase_line->discount_percent, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm inline_discounts input_number', 'required']); !!} <b>%</b>
            </td>

            @endif
            @if($purchase->type=="Purchase Requisition")
            <td class="hide">
                {!! Form::text('purchases[' . $loop->index . '][purchase_price]', 
                number_format($purchase_line->purchase_price/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm purchase_unit_cost input_number']); !!}
            </td>
            @else
            <td class="hide">
                {!! Form::text('purchases[' . $loop->index . '][purchase_price]', 
                number_format($purchase_line->purchase_price/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm purchase_unit_cost input_number','readonly']); !!}
            </td>
            
            @endif
            @if($purchase->type=="Purchase Requisition")
            <td class="{{$hide_tax}} hide">
                <span class="row_subtotal_before_tax">
                    {{number_format($purchase_line->quantity * $purchase_line->purchase_price/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}
                </span>
                <input type="hidden" class="row_subtotal_before_tax_hidden" value="{{number_format($purchase_line->quantity * $purchase_line->purchase_price/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}">
            </td>
            @else
            <td class="{{$hide_tax}}">
                <span class="row_subtotal_before_tax">
                    {{number_format($purchase_line->quantity * $purchase_line->purchase_price/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}
                </span>
                <input type="hidden" class="row_subtotal_before_tax_hidden" value="{{number_format($purchase_line->quantity * $purchase_line->purchase_price/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}">
            </td>

            @endif
            @if($purchase->type=="Purchase Requisition")
            <td class="{{$hide_tax}} hide">
                <div class="input-group">
                    <select name="purchases[{{ $loop->index }}][purchase_line_tax_id]" class="form-control input-sm purchase_line_tax_id" placeholder="'Please Select'">
                        <option value="" data-tax_amount="0" @if( empty( $purchase_line->tax_id ) )
                        selected @endif >@lang('lang_v1.none')</option>
                        @foreach($taxes as $tax)
                            <option value="{{ $tax->id }}" data-tax_amount="{{ $tax->amount }}" @if( $purchase_line->tax_id == $tax->id) selected @endif >{{ $tax->name }}</option>
                        @endforeach
                    </select>
                    <span class="input-group-addon purchase_product_unit_tax_text">
                        {{number_format($purchase_line->item_tax/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}
                    </span>
                    {!! Form::hidden('purchases[' . $loop->index . '][item_tax]', number_format($purchase_line->item_tax/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'purchase_product_unit_tax']); !!}
                </div>
            </td>
            
            
            @else
            <td class="{{$hide_tax}}">
                <div class="input-group">
                    <select style="width:70px" name="purchases[{{ $loop->index }}][purchase_line_tax_id]" class="form-control input-sm purchase_line_tax_id" placeholder="'Please Select'">
                        <option value="" data-tax_amount="0" @if( empty( $purchase_line->tax_id ) )
                        selected @endif >@lang('lang_v1.none')</option>
                        @foreach($taxes as $tax)
                            <option value="{{ $tax->id }}" data-tax_amount="{{ $tax->amount }}" @if( $purchase_line->tax_id == $tax->id) selected @endif >{{ $tax->name }}</option>
                        @endforeach
                    </select>
                    <span class="input-group-addon purchase_product_unit_tax_text">
                        {{number_format($purchase_line->item_tax/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}
                    </span>
                    {!! Form::hidden('purchases[' . $loop->index . '][item_tax]', number_format($purchase_line->item_tax/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'purchase_product_unit_tax']); !!}
                </div>
            </td>
            @endif
            
            @if($purchase->type == "Purchase_invoice")
            <td class="text-center">
				<input type="hidden" class="form-control further_tax_hidden" name="purchases[{{$loop->index}}][item_further_tax]" />
				<div class="input-group">
					<select name="purchases[{{$loop->index}}][further_taax_id]" class="form-control select2 input-sm further_tax" id="further_tax" placeholder="Please Select">
					    
							<option value="0" data-rate="0">NONE</option>
							@foreach($further_taxes as $tax)
								<option value="{{ $tax->id }}" data-rate="{{ $tax->amount }}" {{ ($purchase_line->further_tax == $tax->id) ? 'selected' : '' }}>{{ $tax->name }}</option>
							@endforeach
							
					</select>
					{!! Form::hidden('purchases[' . $loop->index . '][further_item_tax]', 0, ['class' => 'purchase_product_unit_further_tax']); !!}
					<span class="input-group-addon purchase_product_unit_further_tax_text">0.00</span>
				</div>
			</td>
			<td>
                <input type="number" name="purchases[{{$loop->index}}][salesman_commission_rate]" class="form-control salesman_commission_rate" value="{{ $purchase_line->salesman_commission }}"/>
			</td>
			@endif
            
            
            
            
            
            
            
          
            <td class="hide">
                {!! Form::text('purchases[' . $loop->index . '][purchase_price_inc_tax]', number_format($purchase_line->purchase_price_inc_tax/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), ['class' => 'form-control input-sm purchase_unit_cost_after_tax input_number', 'required']); !!}
            </td>
        @if($purchase->type=="Purchase Requisition")
            <td class="hide">
                <span class="row_subtotal_after_tax">
                {{number_format($purchase_line->purchase_price_inc_tax * $purchase_line->quantity/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}
                </span>
                <input type="hidden" class="row_subtotal_after_tax_hidden" value="{{number_format($purchase_line->purchase_price_inc_tax * $purchase_line->quantity/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}">
            </td>
            @else
            <td>
                <span class="row_subtotal_after_tax">
                {{number_format($purchase_line->purchase_price_inc_tax * $purchase_line->quantity/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}
                </span>
                <input type="hidden" class="row_subtotal_after_tax_hidden" value="{{number_format($purchase_line->purchase_price_inc_tax * $purchase_line->quantity/$purchase->exchange_rate, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}">
            </td>
            @endif

           
            @if(empty($is_purchase_order))
            <!--<td>-->
            <!--    @if(session('business.enable_editing_product_from_purchase'))-->
            <!--        {!! Form::text('purchases[' . $loop->index . '][default_sell_price]',0, ['class' => 'form-control input-sm input_number default_sell_price', 'required']); !!}-->
            <!--    @else-->
            <!--        {{number_format($sp, $currency_precision, $currency_details->decimal_separator, $currency_details->thousand_separator)}}-->
            <!--    @endif-->

            <!--</td>-->
            @if(session('business.enable_lot_number'))
                <td>
                    {!! Form::text('purchases[' . $loop->index . '][lot_number]', $purchase_line->lot_number, ['class' => 'form-control input-sm']); !!}
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
                        {!! Form::text('purchases[' . $loop->index . '][mfg_date]', !empty($mfg_date) ? @format_date($mfg_date) : null, ['class' => 'form-control input-sm expiry_datepicker mfg_date', 'readonly']); !!}
                    </div>
                    <b><small>@lang('product.exp_date'):</small></b>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        {!! Form::text('purchases[' . $loop->index . '][exp_date]', !empty($exp_date) ? @format_date($exp_date) : null, ['class' => 'form-control input-sm expiry_datepicker exp_date', 'readonly']); !!}
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