<div class="table-responsive">
    <table class="table table-striped ajax_view hide-footer dataTable table-styling table-hover table-primary"
        id="report_table">
        <thead>
            <tr>
                <th>S. no</th>
                <th></th>
                <th>Product</th>
                <th></th>
                <th></th> 
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandQty = 0;
                $grandWeight = 0;
                $grandRate = 0;
                $grandSaleTax = 0;
                $grandFurtherTax = 0;
                $grandTotal = 0;
            @endphp
            @foreach ($data as $key => $item)
                <tr style="font-size: 12px;">
                    <td style="float: left;">{{ $key+1 }}</td>
                    <td></td>
                    <td>{{ $item->name }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                {{-- sell line rows start  --}}
                @if ($item->sale_lines)
                    <tr>
                        <th>S.No</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Sale Invoice No</th>
                        <th>Delivery Note No</th>
                        <th>Brand</th>
                        <th>Product Description</th>
                        <th class="text-right">Qty</th>
                        <th class="text-right">Weight</th>
                        <th class="text-right">Rate</th>
                        <th class="text-right">Sales Tax</th>
                        <th class="text-right">Further Tax</th>
                        <th class="text-right">Amount</th>
                    </tr>
                    @php
                        $totalQty = 0;
                        $totalRate = 0;
                        $totalWeight = 0;
                        $totalSaleTax = 0;
                        $totalFurtherTax = 0;
                        $totalAmount = 0;
                        $i = 0;
                    @endphp
                    @foreach ($item->sale_lines as $sale_line)
                        @if ($sale_line->saletransaction)
                            @php
                                $i++;
                                if (isset($sale_line, $sale_line->transaction, $sale_line->transaction->delivery_note_no)) {
                                    $delivery_no = DB::table('transactions')
                                        ->where('id', $sale_line->transaction->delivery_note_no)
                                        ->first()->ref_no;
                                }
                                $amount = $sale_line->quantity * $sale_line->unit_price;
                                $saletaxRate = $sale_line->line_tax->amount ?? 0;
                                $salestax = $saletaxRate > 0 ? ($amount * $saletaxRate) / 100 : 0;
                                $furthertaxRate = $sale_line->further_taxs->amount ?? 0;
                                $furtherTax = $furthertaxRate > 0 ? ($amount * $furthertaxRate) / 100 : 0;

                                
                                $totalQty += $sale_line->quantity;
                                $totalWeight += $item->product_custom_field1 * $sale_line->quantity;
                                $totalRate += $sale_line->unit_price;
                                $totalSaleTax += $salestax;
                                $totalFurtherTax += $furtherTax;
                                $totalAmount += ($amount + $salestax + $furtherTax);

                                $grandQty += $sale_line->quantity;
                                $grandWeight += $item->product_custom_field1 * $sale_line->quantity;
                                $grandRate += $sale_line->unit_price;
                                $grandSaleTax += $salestax;
                                $grandFurtherTax += $furtherTax;
                                $grandTotal += ($amount + $salestax + $furtherTax);
                            @endphp
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ @format_date($sale_line->saletransaction->transaction_date) }}</td>
                                <td>{{ $sale_line->transaction->contact->supplier_business_name }}</td>
                                <td>{{ $sale_line->saletransaction->ref_no }}</td>
                                <td>{{ $delivery_no ?? '' }}</td>
                                <td>{{ $sale_line->brand->name ?? '' }}</td>
                                <td>{{ $sale_line->sell_line_note }}</td>
                                <td class="text-right">{{ number_format($sale_line->quantity, 2) }}</td>
                                <td class="text-right">{{ number_format(($item->product_custom_field1 * $sale_line->quantity),2) }}</td>
                                <td class="text-right">{{ number_format($sale_line->unit_price, 2) }}</td>
                                <td class="text-right">{{ number_format($salestax, 2) }}</td>
                                <td class="text-right">{{ number_format($furtherTax, 2) }}</td>
                                <td class="text-right">{{ number_format(($amount + $salestax + $furtherTax),2 ) }}</td>
                            </tr>
                        @endif
                    @endforeach
                    {{-- total tr  --}}
                    <tr class="total-row">
                        <td> Total :</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right">{{ number_format($totalQty, 2) }}</td>
                        <td class="text-right">{{ number_format($totalWeight, 2) }}</td>
                        <td class="text-right">{{ number_format($totalRate, 2) }}</td>
                        <td class="text-right">{{ number_format($totalSaleTax, 2) }}</td>
                        <td class="text-right">{{ number_format($totalFurtherTax, 2) }}</td>
                        <td class="text-right">{{ number_format($totalAmount, 2) }}</td>
                    </tr>
                    @endif
                    <tr style="background-color: rgb(0 0 0 / 8%) !important;">
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                
            @endforeach
            <tr>
                <td><b>Grand Total</b></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><b style="float: left">Qty :</b><b style="float: right">{{ number_format($grandQty, 2) }}</b></td>
                <td><b style="float: left">Weight :</b><b style="float: right">{{ number_format($grandWeight, 2) }}</b></td>
                <td><b style="float: left">Rate :</b><b style="float: right">{{ number_format($grandRate, 2) }}</b></td>
                <td><b style="float: left">Sale Tax :</b><b style="float: right">{{ number_format($grandSaleTax, 2) }}</b></td>
                <td><b style="float: left">Further Tax :</b><b style="float: right">{{ number_format($grandFurtherTax, 2) }}</b></td>
                <td><b style="float: left">Amount :</b><b style="float: right">{{ number_format($grandTotal, 2) }}</b></td>
            </tr>
        </tbody>
    </table>
</div>
