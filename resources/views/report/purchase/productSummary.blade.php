<div class="table-responsive">
    <table class="table table-striped ajax_view hide-footer dataTable table-styling table-hover table-primary"
        id="report_table">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Product Name</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Weight</th>
                <th class="text-right">Sales Tax</th>
                <th class="text-right">Further Tax</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
                @php
                    $grandtotalQty = 0;
                    $grandtotalRate = 0;
                    $grandtotalWeight = 0;
                    $grandtotalSaleTax = 0;
                    $grandtotalFurtherTax = 0;
                    $grandtotalAmount = 0;
                @endphp
            @foreach ($data as $key => $item)
                @php
                    $totalQty = 0;
                    $totalRate = 0;
                    $totalWeight = 0;
                    $totalSaleTax = 0;
                    $totalFurtherTax = 0;
                    $totalAmount = 0;
                @endphp
                @foreach ($item->purchase_lines as $purchase_line)
                    @if ($purchase_line->purchasetransaction)
                        @php
                            $amount = $purchase_line->quantity * $purchase_line->pp_without_discount;
                            $saletaxRate = $purchase_line->line_tax->amount ?? 0;
                            $salestax = $saletaxRate > 0 ? ($amount * $saletaxRate) / 100 : 0;
                            $furthertaxRate = $purchase_line->further_taxs->amount ?? 0;
                            $furtherTax = $furthertaxRate > 0 ? ($amount * $furthertaxRate) / 100 : 0;

                            $totalQty += $purchase_line->quantity;
                            $totalWeight += $item->product_custom_field1 * $purchase_line->quantity;
                            $totalRate += $purchase_line->pp_without_discount;
                            $totalSaleTax += $salestax;
                            $totalFurtherTax += $furtherTax;
                            $totalAmount += ($amount + $salestax + $furtherTax);
                        @endphp
                    @endif
                @endforeach
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->name }}</td>
                    <td class="text-right">{{ number_format($totalQty, 2) }}</td>
                    <td class="text-right">{{ number_format($totalWeight, 2) }}</td>
                    <td class="text-right">{{ number_format($totalSaleTax, 2) }}</td>
                    <td class="text-right">{{ number_format($totalFurtherTax, 2) }}</td>
                    <td class="text-right">{{ number_format($totalAmount, 2) }}</td>
                </tr>
                @php
                    $grandtotalQty += $totalQty;
                    $grandtotalRate += $totalRate;
                    $grandtotalWeight += $totalWeight;
                    $grandtotalSaleTax += $totalSaleTax;
                    $grandtotalFurtherTax += $totalFurtherTax;
                    $grandtotalAmount += $totalAmount;
                @endphp
            @endforeach
            <tr>
                <td><b>Grand Total :</b></td>
                <td></td>
                <td class="text-right"><b>{{ number_format($grandtotalQty, 2) }}</b></td>
                <td class="text-right"><b>{{ number_format($grandtotalWeight, 2) }}</b></td>
                <td class="text-right"><b>{{ number_format($grandtotalSaleTax, 2) }}</b></td>
                <td class="text-right"><b>{{ number_format($grandtotalFurtherTax, 2) }}</b></td>
                <td class="text-right"><b>{{ number_format($grandtotalAmount, 2) }}</b></td>
            </tr>
        </tbody>
    </table>
</div>
