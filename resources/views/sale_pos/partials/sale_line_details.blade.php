<table class="table bg-gray main">
    <tr class="bg-green">
        <th>#</th>
        <th><b>{{ __('Item Details') }}</b></th>
        
        <th><b>Brand</b></th>

        @if($sell->type=="delivery_note" || $sell->type=="sales_order")
        <th  class="item_description"><b>{{ __('Item Description') }}</b></th>
        @endif
        
 
        @if($sell->type=="delivery_note" || $sell->type=="sales_order")
        <th class="item_delivry_qty"><b>{{ __('Delivered qty') }}</b></th>
        @elseif($sell->type=="sale_invoice" || $sell->type=="sale_return_invoice")
        <th><b>{{ __('sale.qty') }}</b></th>
        @endif
        
        
        
        
        @if($sell->type=="sales_order" || $sell->type=="sale_invoice" || $sell->type=="sale_return_invoice")
        <th class="item_unit_price"><b>{{ __('sale.unit_price') }}</b></th>
        @endif
        
        @if($sell->type=="sale_invoice" && !empty($sale_tax_name))
        <th class=""><b>{{ $sale_tax_name }}</b></th>
        @endif
        
        @if($sell->type=="sale_invoice" && !empty($further_tax_name))
        <th class=""><b>{{ $further_tax_name }}</b></th>
        @endif
        
        
        
        @if($sell->type=="sale_invoice")
        <th id="s_ex_tax"><b>Value Of Supplies Excl. Tax</b></th>
        <th id="stax"><b>Sale Tax @18%</b></th>
        <th id="a_sale_tax"><b>Add Sale Tax @3%</b></th>
        <th id="amnt"><b>Amount</b></th>
        @endif
        
        @if($sell->type=="sales_order" || $sell->type=="sale_invoice" || $sell->type=="sale_return_invoice")
        <th class="item_unit_total"><b>Total</b></th>
        @endif
    </tr>
    @foreach($sell->sell_lines as $sell_line)
   
        <tr class="tr_price" style="border-bottom: 2pt #efe9e9;">
            <?php 
                $total = $sell_line->quantity * $sell_line->unit_price;
                $further_tax_ammount = 0;
                $sale_tax_ammount = 0;
            ?>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $sell_line->product->name ?? '' }}
            
            @if($sell->type=="sales_order")
                <div class="product_desc">
                    {{ $sell_line->sell_line_note }}
                </div>
            @endif
            
            </td>
            <td>{{$sell_line->brand->name ??''}}</td>
            
            @if($sell->type=="delivery_note" || $sell->type=="sales_order")
            <td class="item_description">{{ $sell_line->sell_line_note }}</td>
             @endif
            <td class="item_delivry_qty">
                {{ $sell_line->quantity  }}&nbsp;&nbsp;{{$sell_line->product->unit->actual_name ?? ''}}
            </td>
            
            
            @if($sell->type=="sales_order" || $sell->type=="sale_return_invoice" || $sell->type=="sale_invoice")
            <td class="item_unit_price">
              {{ number_format($sell_line->unit_price, 2) }}

            </td>
            @endif
            
            @if($sell->type=="sale_invoice" && !empty($sale_tax))
            <td class="">
                <?php $sale_tax_ammount =  $total * $sale_tax / 100 ?>
                {{ $total * $sale_tax / 100 }}
            </td>
            @endif
            
            @if($sell->type=="sale_invoice" && !empty($further_tax))
            <td class="">
                <?php
                $further_tax_ammount =  $total * $further_tax / 100 ;
                
                ?>
                {{ $total * $further_tax / 100 }}
            </td>
            @endif
            
            
            
             @if($sell->type=="sale_invoice")
             <td id="s_ex_tax">0.00</td>
             <td id="stax">0.00</td>
             <td id="a_sale_tax">0.00</td>
             <td id="amnt">0.00</td>
             @endif
        
            
            
            @if($sell->type=="sales_order" || $sell->type=="sale_invoice" || $sell->type=="sale_return_invoice")
            <td  class="item_unit_total">
            
             {{ number_format($sell_line->quantity * $sell_line->unit_price + $further_tax_ammount + $sale_tax_ammount, 2) }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </td>
            @endif
        </tr>
        
        
        
        @if(!empty($sell_line->modifiers))
        @foreach($sell_line->modifiers as $modifier)
            <tr>
                <td>&nbsp;</td>
                <td>
                    {{ $modifier->product->name }} - {{ $modifier->variations->name ?? ''}},
                    {{ $modifier->variations->sub_sku ?? ''}}
                </td>
                @if( session()->get('business.enable_lot_number') == 1)
                    <td>&nbsp;</td>
                @endif
                <td>{{ $modifier->quantity }}</td>
                @if(!empty($pos_settings['inline_service_staff']))
                    <td>
                        &nbsp;
                    </td>
                @endif
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{ $modifier->unit_price }}</span>
                </td>
                <td>
                    &nbsp;
                </td>
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{ $modifier->item_tax }}</span> 
                    @if(!empty($taxes[$modifier->tax_id]))
                    ( {{ $taxes[$modifier->tax_id]}} )
                    @endif
                </td>
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{ $modifier->unit_price_inc_tax }}</span>
                </td>
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{ $modifier->quantity * $modifier->unit_price_inc_tax }}</span>
                </td>
            </tr>
            @endforeach
        @endif
    @endforeach
</table>