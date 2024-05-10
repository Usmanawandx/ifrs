@php
	$type = $delivery_note->type;
@endphp
@foreach ($delivery_note->sell_lines as $delivery_note)

    <tr>
        <td class="text-center v-center">
            <button class="btn btn-danger remove" type="button" onclick="remove_row(this)"
                style="padding: 0px 5px 2px 5px;">
                <i class="fa fa-trash" aria-hidden="true"></i>
            </button>
        </td>
        <td>
            <span class='sr_number'>{{ $row_count + 1 }}</span>
            <input type="hidden" name="gross_weight" class="gross__weight">
            <input type="hidden" name="net_weight" class="net__weight">
        </td>
        <td class="hide">
            <div class="form-group">
                <select name="products[{{ $row_count }}][store]" class="form-control select2" required
                    id="prd_select" onchange="get_product_code(this)">
                    <option value="">Please Select</option>
                    @foreach ($store as $p)
                        @if ($delivery_note->store == $p->id)
                            <option selected value="{{ $p->id }}">{{ $p->name }}</option>
                        @else
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </td>
        <td>
            <input class="form-control product_code" readonly="" id="item_code"
                name="products[{{ $row_count }}][item_code]" type="text">
            <input type="hidden" name="base_unit" class="base_unit">
        </td>
        <td>
            <select name="products[{{ $row_count }}][product_id]" class="form-control prd_select select2" required
                id="prd_select" onchange="get_product_code(this)">
                <option value="">Please Select</option>
                @foreach ($product as $p)
                    @if ($delivery_note->product_id == $p->id)
                        <option selected value="{{ $p->id }}">{{ $p->name }}</option>
                    @else
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endif
                @endforeach
            </select>
            <input type="hidden" class="product_type" name="products[{{ $row_count }}][product_type]"
                value="0">

            @php
                $hide_tax = 'hide';
                if (session()->get('business.enable_inline_tax') == 1) {
                    $hide_tax = '';
                }
                
                if (!empty($so_line)) {
                    $tax_id = $so_line->tax_id;
                    $item_tax = $so_line->item_tax;
                }
                
                if ($hide_tax == 'hide') {
                    $tax_id = null;
                    $unit_price_inc_tax = $product->default_sell_price;
                }
                
                $discount_type = !empty($product->line_discount_type) ? $product->line_discount_type : 'fixed';
                $discount_amount = !empty($product->line_discount_amount) ? $product->line_discount_amount : 0;
                
                if (!empty($discount)) {
                    $discount_type = $discount->discount_type;
                    $discount_amount = $discount->discount_amount;
                }
                
                if (!empty($so_line)) {
                    $discount_type = $so_line->line_discount_type;
                    $discount_amount = $so_line->line_discount_amount;
                }
                
                $sell_line_note = '';
                if (!empty($product->sell_line_note)) {
                    $sell_line_note = $product->sell_line_note;
                }
            @endphp

            @if (!empty($discount))
                {!! Form::hidden("products['.$row_count.'][discount_id]", $discount->id) !!}
            @endif

            @if (empty($is_direct_sell))
                <div class="modal fade row_edit_product_price_model" id="row_edit_product_price_modal_0" tabindex="-1"
                    role="dialog">
                </div>
            @endif

            <!-- Description modal end -->
            @if (in_array('modifiers', $enabled_modules))
                <div class="modifiers_html">
                    @if (!empty($product->product_ms))
                        @include('restaurant.product_modifier_set.modifier_for_product', [
                            'edit_modifiers' => true,
                            '{{ $row_count }}' => $loop->index,
                            'product_ms' => $product->product_ms,
                        ])
                    @endif
                </div>
            @endif

            @php
                if (!empty($action) && $action == 'edit') {
                    if (!empty($so_line)) {
                        $qty_available = $so_line->quantity - $so_line->so_quantity_invoiced + $product->quantity_ordered;
                        $max_quantity = $qty_available;
                        $formatted_max_quantity = number_format($qty_available, config('constants.currency_precision', 2), session('currency')['decimal_separator'], session('currency')['thousand_separator']);
                    }
                } else {
                    if (!empty($so_line) && $so_line->qty_available <= $max_quantity) {
                        $max_quantity = $so_line->qty_available;
                        $formatted_max_quantity = $so_line->formatted_qty_available;
                    }
                }
                
            @endphp

            @if (session()->get('business.enable_lot_number') == 1 || session()->get('business.enable_product_expiry') == 1)
                @php
                    $lot_enabled = session()->get('business.enable_lot_number');
                    $exp_enabled = session()->get('business.enable_product_expiry');
                    $lot_no_line_id = '';
                    if (!empty($product->lot_no_line_id)) {
                        $lot_no_line_id = $product->lot_no_line_id;
                    }
                @endphp
                @if (!empty($product->lot_numbers) && empty($is_sales_order))
                    <select class="form-control lot_number input-sm"
                        name="products[{{ $row_count }}][lot_no_line_id]"
                        @if (!empty($product->transaction_sell_lines_id)) disabled @endif>
                        <option value="">@lang('lang_v1.lot_n_expiry')</option>
                        @foreach ($product->lot_numbers as $lot_number)
                            @php
                                $selected = '';
                                if ($lot_number->purchase_line_id == $lot_no_line_id) {
                                    $selected = 'selected';
                                
                                    $max_qty_rule = $lot_number->qty_available;
                                    $max_qty_msg = __('lang_v1.quantity_error_msg_in_lot', ['qty' => $lot_number->qty_formated, 'unit' => $product->unit]);
                                }
                                
                                $expiry_text = '';
                                if ($exp_enabled == 1 && !empty($lot_number->exp_date)) {
                                    if (\Carbon::now()->gt(\Carbon::createFromFormat('Y-m-d', $lot_number->exp_date))) {
                                        $expiry_text = '(' . __('report.expired') . ')';
                                    }
                                }
                                
                                //preselected lot number if product searched by lot number
                                if (!empty($purchase_line_id) && $purchase_line_id == $lot_number->purchase_line_id) {
                                    $selected = 'selected';
                                
                                    $max_qty_rule = $lot_number->qty_available;
                                    $max_qty_msg = __('lang_v1.quantity_error_msg_in_lot', ['qty' => $lot_number->qty_formated, 'unit' => $product->unit]);
                                }
                            @endphp
                            <option value="{{ $lot_number->purchase_line_id }}"
                                data-qty_available="{{ $lot_number->qty_available }}" data-msg-max="@lang('lang_v1.quantity_error_msg_in_lot', ['qty' => $lot_number->qty_formated, 'unit' => $product->unit])"
                                {{ $selected }}>
                                @if (!empty($lot_number->lot_number) && $lot_enabled == 1)
                                    {{ $lot_number->lot_number }}
                                    @endif @if ($lot_enabled == 1 && $exp_enabled == 1)
                                        -
                                        @endif @if ($exp_enabled == 1 && !empty($lot_number->exp_date))
                                            @lang('product.exp_date'): {{ @format_date($lot_number->exp_date) }}
                                        @endif {{ $expiry_text }}
                            </option>
                        @endforeach
                    </select>
                @endif
            @endif

        </td>
        <td>
            {!! Form::select('products[{{$row_count}}][brand_id]', ['' => 'Select'] + $brands->pluck('name','id')->all(), $delivery_note->brand_id, ['class' => 'form-control select2','id' =>'brand_id']) !!}
        </td>
        <td>
            <textarea class="form-control" name="products[{{ $row_count }}][sell_line_note]" rows="2">{{ $delivery_note->sell_line_note }}</textarea>

        </td>
        <td>
            <input type="text" class="form-control uom" readonly="">
        </td>

        <td>

            <input type="hidden" name="transporter_rate" class="transporter_rate" />
            <input type="hidden" name="contractor_rate" class="contractor_rate" />


            <input type="hidden" value="0" name="products[{{ $row_count }}][variation_id]"
                class="row_variation_id">

            <input type="hidden" value="0" name="products[{{ $row_count }}][enable_stock]">

            <div class="input-group input-number">
                <input type="text" required class="form-control pos_quantity input_number mousetrap input_quantity"
                    min="1" value="{{ $delivery_note->quantity }}"
                    name="products[{{ $row_count }}][quantity]" required
                    data-allow-overselling="@if (empty($pos_settings['allow_overselling'])) {{ 'false' }}@else{{ 'true' }} @endif"
                    onkeyup="calculate_unitprice(this)">


            </div>
        </td>
        <td>
            <input type="text" name="products[{{ $row_count }}][unit_price]"
                class="form-control pos_unit_price input_number mousetrap" value="{{ $delivery_note->unit_price }}"
                onkeyup="calculate_unitprice(this)">
        </td>
        <td class="hide">
            {!! Form::text(
                "products['.$row_count.'][line_discount_amount]",
                @num_format($delivery_note->line_discount_amount),
                ['class' => 'form-control input_number row_discount_amount', 'onkeyup' => 'calculate_discount(this)'],
            ) !!}<br>
            {!! Form::select(
                "products['.$row_count.'][line_discount_type]",
                ['fixed' => __('lang_v1.fixed')],
                $discount_type,
                ['class' => 'form-control row_discount_type'],
            ) !!}
            @if (!empty($discount))
                <p class="help-block">{!! __('lang_v1.applied_discount_text', [
                    'discount_name' => $discount->name,
                    'starts_at' => $discount->formated_starts_at,
                    'ends_at' => $discount->formated_ends_at,
                ]) !!}</p>
            @endif
        </td>
        <td class="get_total">
            <input class="calculate_discount row_total_amount  form-control" readonly type="text">
        </td>
        <td class="text-center {{ $hide_tax }}">

            {!! Form::hidden("products['.$row_count.'][item_tax]", null, ['class' => 'item_tax']) !!}

            <select name="products[{{ $row_count }}][tax_id]" class="form-control select2 input-sm tax_idd"
                onchange="calculate_unitprice(this)" placeholder="'Please Select'" id="tax_id">
                <option value="0" data-ratee="0">@lang('lang_v1.none')</option>
                @foreach ($taxes as $tax_ratee)
                    <option value="{{ $tax_ratee->id }}" data-ratee="{{ $tax_ratee->amount }}"
                        {{ $delivery_note->tax_id == $tax_ratee->id ? 'selected' : '' }}>{{ $tax_ratee->name }}
                    </option>
                @endforeach
            </select>
        </td>

        <td class="text-center @if($type != "delivery_note") hide @endif">
			<input type="hidden" class="form-control further_tax_hidden" name="products[{{$row_count}}][item_further_tax]" />
			<select name="products[{{$row_count}}][further_taax_id]" class="form-control select2 input-sm further_tax" id="further_tax" onchange="calculate_unitprice(this)" placeholder="Please Select">
				<option value="0" data-rate="0">NONE</option>
				@foreach($further_tax as $further)
				    <option value="{{ $further->id }}" data-rate="{{ $further->amount }}" {{ ($delivery_note->further_tax == $further->id) ? 'selected' : '' }}>{{ $further->name }}</option>
				@endforeach
			</select>
		</td>
		

        <td class="{{ $hide_tax }}">
            <input type="text" name="products[{{ $row_count }}][unit_price_inc_tax]"
                class="form-control pos_unit_price_inc_tax input_number"
                value="{{ $delivery_note->unit_price_inc_tax }}" readonly>
        </td>

        <td class="@if($type != "delivery_note") hide @endif">
            <input type="number" name="products[{{$row_count}}][salesman_commission_rate]" class="form-control salesman_commission_rate" value="{{ $delivery_note->salesman_commission }}" onkeyup="calculate_unitprice(this)"/>
		</td>
        
        @if (!empty($common_settings['enable_product_warranty']) && !empty($is_direct_sell))
            <td>
                {!! Form::select("products['.$row_count.'][warranty_id]", $warranties, $warranty_id, [
                    'placeholder' => __('messages.please_select'),
                    'class' => 'form-control',
                ]) !!}
            </td>
        @endif
        <td class="text-center">
            @php
                $subtotal_type = !empty($pos_settings['is_pos_subtotal_editable']) ? 'text' : 'hidden';
                
            @endphp
            <input type="text"
                class="form-control pos_line_total @if (!empty($pos_settings['is_pos_subtotal_editable'])) input_number @endif"
                value="0.00">
        </td>

    </tr>

    <?php $row_count++; ?>
@endforeach
<input type="hidden" id="row_count" value="{{ $row_count }}">