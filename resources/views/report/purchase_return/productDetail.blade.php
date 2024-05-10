<div class="table-responsive">
    <table class="table table-striped ajax_view hide-footer dataTable table-styling table-hover table-primary"
        id="report_table">
        <thead>
            <tr>
                <th>S.no</th>
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
                $grandAmount = 0;
            @endphp
            @foreach ($data as $key => $item)
                <tr style="font-size: 12px">
                    <td style="float: left">{{ $key+1 }}</td>
                    <td></td>
                    <td >{{ $item->name }}</td>
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
                @if ($item->purchase_lines)
                    <tr>
                        <th>S.No</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Purchase Invoice No</th>
                        <th></th>
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
                    @foreach ($item->purchase_lines as $purchase_line)
                        @if ($purchase_line->purchaseReturntransaction)
                            @php
                                $i++;
                                if (isset($purchase_line, $purchase_line->purchaseReturntransaction, $purchase_line->purchaseReturntransaction->purchase_order_ids)) {
                                    $grn_no = DB::table('transactions')
                                        ->where('id', $purchase_line->purchaseReturntransaction->purchase_order_ids)
                                        ->first()->ref_no ?? '';
                                }
                                $amount = $purchase_line->quantity_returned * $purchase_line->pp_without_discount;
                                $saletaxRate = $purchase_line->line_tax->amount ?? 0;
                                $salestax = $saletaxRate > 0 ? ($amount * $saletaxRate) / 100 : 0;
                                $furthertaxRate = $purchase_line->further_taxs->amount ?? 0;
                                $furtherTax = $furthertaxRate > 0 ? ($amount * $furthertaxRate) / 100 : 0;

                                
                                $totalQty += $purchase_line->quantity_returned;
                                $totalWeight += $item->product_custom_field1 * $purchase_line->quantity_returned;
                                $totalRate += $purchase_line->pp_without_discount;
                                $totalSaleTax += $salestax;
                                $totalFurtherTax += $furtherTax;
                                $totalAmount += ($amount + $salestax + $furtherTax);

                                $grandQty += $purchase_line->quantity_returned;
                                $grandWeight += $item->product_custom_field1 * $purchase_line->quantity_returned;
                                $grandRate += $purchase_line->pp_without_discount;
                                $grandSaleTax += $salestax;
                                $grandFurtherTax += $furtherTax;
                                $grandAmount += ($amount + $salestax + $furtherTax);
                            @endphp
                            <tr>
                                <td style="float: left">{{ $i }}</td>
                                <td>{{ @format_date($purchase_line->purchaseReturntransaction->transaction_date) }}</td>
                                <td>{{ $purchase_line->transaction->contact->supplier_business_name }}</td>
                                <td>{{ $purchase_line->purchaseReturntransaction->ref_no }}</td>
                                <td>{{ $grn_no ?? '' }}</td>
                                <td>{{ $purchase_line->brand->name ?? '' }}</td>
                                <td>{{ $purchase_line->sell_line_note }}</td>
                                <td class="text-right">{{ number_format($purchase_line->quantity_returned, 2) }}</td>
                                <td class="text-right">{{ number_format($item->product_custom_field1 * $purchase_line->quantity_returned,2) }}</td>
                                <td class="text-right">{{ number_format($purchase_line->pp_without_discount, 2) }}</td>
                                <td class="text-right">{{ number_format($salestax, 2) }}</td>
                                <td class="text-right">{{ number_format($furtherTax, 2) }}</td>
                                <td class="text-right">{{ number_format(($amount + $salestax + $furtherTax), 2) }}</td>
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
                        <td>{{ number_format($totalQty, 2) }}</td>
                        <td>{{ number_format($totalWeight, 2) }}</td>
                        <td>{{ number_format($totalRate, 2) }}</td>
                        <td>{{ number_format($totalSaleTax, 2) }}</td>
                        <td>{{ number_format($totalFurtherTax, 2) }}</td>
                        <td>{{ number_format($totalAmount, 2) }}</td>
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
                <td><b style="float: left">Amount :</b><b style="float: right">{{ number_format($grandAmount, 2) }}</b></td>
            </tr>
        </tbody>
    </table>
</div>
