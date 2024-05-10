


<tr class="product_row">
    <td>
        <button class="btn btn-danger remove" type="button" onclick="remove_row(this)" style="padding: 0px 5px 2px 5px;"><i class="fa fa-trash" aria-hidden="true"></i></button>
        </td>
    <td class="sr_number"></td>
    <td class="hide">
        <select name="products[0][store]" class="form-control" style="width:100px">
            @foreach($store as $s)
            @if($product->store == $s->id )
            <option selected value="{{$s->id}}">{{$s->name}}</option>
            @else
            <option value="{{$s->id}}">{{$s->name}}</option>
            @endif
            @endforeach
        </select>
        <!--<input type="text" name="products[0][store]" class="form-control" value="{{$product->store ?? ''}}">-->
    
    
    </td>

    <td><input type="text" name="products[0][item_code]" readonly class="form-control product_code" value="{{$product->code ?? ''}}" style="width:70px"></td>
    <td>
        <input type="hidden" name="gross_weight" class="gross__weight">
        <input type="hidden" name="net_weight" class="net__weight">
        <input type="hidden" name="transporter_rate" class="transporter_rate" />
        <input type="hidden" name="contractor_rate" class="contractor_rate" />

<select class="form-control select2" name="products[0][product_id]" id="search_product"  style="width: 200px" onchange="get_product_code(this)">
@foreach ($product_t as $p)
@if($product->product_id == $p->product_id)
<option selected value="{{$p->product_id}}" >{{$p->name}}</option> 
@else
<option value="{{$p->product_id}}">{{$p->name}}</option> 
@endif
@endforeach

</select>
</td>
<td>{!! Form::select('products[0][brand_id]', ['' => 'Select'] + $brands->pluck('name','id')->all(), $product->brand_id, ['class' => 'form-control select2','id' =>'brand_id']) !!}</td>
    <td> <input type="text" name="products[0][item_description]" class="form-control" value="{{$product->description ?? ''}}" style="width:100px"></td>
    <td>  <input type="text" name="products[0][uom]" readonly class="form-control uom" value="{{$product->unit?? ''}}" style="width:70px"></td>
    
    @if(session('business.enable_lot_number'))
        <td>
            <input type="text" name="products[0][lot_number]" class="form-control" value="{{$product->lot_number ?? ''}}">
        </td>
    @endif
    @if(session('business.enable_product_expiry'))
        <td>
            <input type="text" name="products[0][exp_date]" class="form-control expiry_datepicker" value="@if(!empty($product->exp_date)){{@format_date($product->exp_date)}}@endif" readonly>
        </td>
    @endif
    <td>
    

        <input type="hidden" value="{{$product->variation_id}}" 
            name="products[0][variation_id]">

        <input type="hidden" value="{{$product->enable_stock}}" 
            name="products[0][enable_stock]">

        @if(!empty($edit))
            <input type="hidden" value="{{$product->purchase_line_id}}" 
            name="products[0][purchase_line_id]">
            @php
                $qty = $product->quantity_returned;
                $purchase_price = $product->purchase_price;
            @endphp
        @else
            @php
                $qty = 1;
                $purchase_price = $product->last_purchased_price;
            @endphp
        @endif

        <input type="text" class="form-control product_quantity input_number input_quantity" value="{{$qty}}" name="products[0][quantity]" style="width:100px">
    </td>

  
    <td>
        <input type="text" name="products[0][unit_price]" class="form-control product_unit_price input_number" value="{{@num_format($purchase_price)}}">
    </td>
    <!--<td>-->
    <!--    <input type="text" name="products[0][discount_percent]" class="form-control discount input_number" id="discount" value="{{$product->discount}}">-->
    <!--</td>-->
    <td>
        <input type="text" readonly name="products[0][pricee]" class="form-control product_line_total"id="subtotal" value="{{@num_format($purchase_price)}}">
    </td>
    <td>
        <div class="input-group">
            <select name="products[0][purchase_line_tax_id]" class="form-control input-sm purchase_line_tax_id" onchange="calculate_discount(this)" placeholder="'Please Select'">
                <option value="" data-tax_amount="0" @if( empty( $purchase_line->tax_id ) )
                selected @endif >@lang('lang_v1.none')</option>
                @foreach($taxes as $tax)
                    <option value="{{ $tax->id }}" data-tax_amount="{{ $tax->amount }}" @if( $purchase_line->tax_id == $tax->id) selected @endif >{{ $tax->name }}</option>
                @endforeach
            </select>
            <span class="input-group-addon purchase_product_unit_tax_text">
                {{number_format($purchase_line->item_tax)}}
            </span>
            {!! Form::hidden('products[0][item_tax]', number_format($purchase_line->item_tax), ['class' => 'purchase_product_unit_tax']); !!}
        </div>
    </td>
    
        <td class="text-center">
    		<input type="hidden" class="form-control further_tax_hidden" name="products[0][item_further_tax]" />
    		<div class="input-group">
    				<select name="products[0][further_taax_id]" class="form-control select2 input-sm further_tax" id="further_tax" placeholder="Please Select">
    					<option value="0" data-rate="0">NONE</option>
    					@foreach($further_taxes as $tax)
    						<option value="{{ $tax->id }}" data-rate="{{ $tax->amount }}" {{ ($product->further_tax == $tax->id) ? 'selected' : '' }}>{{ $tax->name }}</option>
    					@endforeach
    				</select>
    				{!! Form::hidden('products[0][further_item_tax]', 0, ['class' => 'purchase_product_unit_further_tax']); !!}
    				<span class="input-group-addon purchase_product_unit_further_tax_text">0.00</span>
    			</div>
    	</td>
    	<td>
            <input type="number" name="products[0][salesman_commission_rate]" class="form-control salesman_commission_rate" value="{{ $product->salesman_commission }}"/>
    	</td>
    	
    <td class="hide">
        <input type="text" name="products[0][purchase_price_inc_tax]" class="form-control saletaxamount input_number" value="{{$product->sales_price_with_tax}}">
    </td>
    <td>
        <input type="text" name="products[0][net_amount]" class="form-control product_line_total_net input_number" value="">
    </td>
 
    

</tr>



