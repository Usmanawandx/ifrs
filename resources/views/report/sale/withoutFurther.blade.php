<button class="btn btn-info print_btn btn-sm pull-right hide">Print</button>
<div class="table-responsive">
    <table class="table table-striped ajax_view hide-footer dataTable table-styling table-hover table-primary"
        id="report_table">
        <thead>
            <tr>
                <th>@lang('messages.date')</th>
                <th>@lang('sale.customer_name')</th>
                <th>@lang('Transaction No')</th>
                <th>Delievry Note No</th>
                <th>NTN / CNIC</th>
                <th>Vehicle</th>
                <th>@lang('Remarks')</th>
                <th>Invoice Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandTotal = 0;
                $grandAddCharges = 0;
                $grandLessCharges = 0;
                $grandAddLessCharges = 0;
                $grandQty = 0;
                $grandWeight = 0;
                $grandSaleTax = 0;
                $grandFurtherTax = 0;
            @endphp
            @foreach ($data as $item)
                @php
                    $grandTotal += $item->final_total;
                    $grandAddCharges += $item->add_charges;
                    $grandLessCharges += $item->less_charges;
                    $grandAddLessCharges += (!empty($item->less_charges) ? '-' . $item->less_charges : $item->add_charges) ?? '0.00';
                @endphp
                <tr>
                    <td>{{ @format_date($item->transaction_date) }}</td>
                    <td>{{ $item->supplier_business_name }}</td>
                    <td>{{ $item->ref_no }}</td>
                    <td>{{ $item->rf_d }}</td>
                    <td>{{ $item->ntn_cnic_no }}</td>
                    <td>{{ !empty($item->vehicle) ? $item->vehicle : $item->vehicle_no }}</td>
                    <td>{{ $item->additional_notes }}</td>
                    <td>{{ number_format($item->final_total, 2) }}</td>
                </tr>
                {{-- Product rows start  --}}
                @php
                    $Totalamount = 0;
                    $add_less_charges = (!empty($item->less_charges) ? -$item->less_charges : $item->add_charges) ?? 0;
                    $TotalQty = 0;
                    $TotalWeight = 0;
                    $TotalSalesTax = 0;
                    $TotalFurtherTax = 0;
                @endphp
                <tr class="product-row nested-thead">
                    <th>S#</th>
                    <th>Product</th>
                    <th></th>
                    <th class="text-right">Qty</th>
                    <th class="text-right net_weight">Weight</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Sale Tax</th>
                    {{-- <th class="text-right further_tax">Further Tax</th> --}}
                    <th class="text-right">Amount</th>
                </tr>
                @foreach ($item->sell_lines as $item)
                    @php
                        $amount = $item->quantity * $item->unit_price;
                        $saletaxRate = $item->line_tax->amount ?? 0;
                        $salestax = $saletaxRate > 0 ? ($amount * $saletaxRate) / 100 : 0;
                        $furthertaxRate = $item->further_taxs->amount ?? 0;
                        $furtherTax = $furthertaxRate > 0 ? ($amount * $furthertaxRate) / 100 : 0;
                        $amount += $salestax;

                        $Totalamount += $amount;
                        $TotalQty += $item->quantity ?? 0;
                        $TotalWeight += $item->product->product_custom_field1 * $item->quantity ?? 0;
                        $TotalSalesTax += $salestax;
                        $TotalFurtherTax += $furtherTax;

                        $grandQty += $item->quantity ?? 0;
                        $grandSaleTax += $salestax;
                        $grandFurtherTax += $furtherTax;
                        $grandWeight += $item->product->product_custom_field1 * $item->quantity ?? 0;
                    @endphp
                    <tr class="product-row nested-tbody" style="border: none !important;">
                        <td style="float: left;">{{ $loop->iteration }}</td>
                        <td>{{ $item->product->name ?? '' }}</td>
                        <td></td>
                        <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                        <td class="net_weight text-right">
                            {{ number_format($item->product->product_custom_field1 * $item->quantity ?? 0, 2) }}
                        </td>
                        <td class="text-right">{{ number_format($item->unit_price) }}</td>
                        <td class="text-right">{{ number_format($salestax, 2) }}</td>
                        {{-- <td class="further_tax text-right">{{ number_format($item->further_tax, 2) }}</td> --}}
                        <td class="text-right">{{ number_format($amount, 2) }}</td>
                    </tr>
                @endforeach
                {{-- total tr  --}}
                @if ($add_less_charges != 0)
                    <tr class="product-row nested-tfoot-1">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        {{-- <td class="further_tax"></td> --}}
                        <td class="net_weight"></td>
                        <td class="text-right">Add/Less Charges</td>
                        <td>{{ number_format($add_less_charges, 2) }}</td>
                    </tr>
                @endif
                <tr class="product-row nested-tfoot-2">
                    <td><b>Total</b></td>
                    <td></td>
                    <td></td>
                    <td class="text-right"><b>{{ number_format($TotalQty, 2) }}</b></td>
                    <td class="text-right net_weight"><b>{{ number_format($TotalWeight, 2) }}</b></td>
                    <td></td>
                    <td class="text-right"><b>{{ number_format($TotalSalesTax, 2) }}</b></td>
                    {{-- <td class="text-right further_tax"><b>{{ number_format($TotalFurtherTax, 2) }}</b></td> --}}
                    <td class="text-right"><b>{{ number_format($Totalamount + $add_less_charges, 2) }}</b></td>
                </tr>
                <tr class="product-row" style="background-color: rgb(0 0 0 / 8%) !important;">
                    <td>&nbsp;</td>
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
                <td><b>Add/Less Charges</b></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right"><b>{{ number_format($grandAddLessCharges, 2) }}</b></td>
            </tr>
            <tr>
                <td><b>Grand Total</b></td>
                <td></td>
                <td></td>
                <td><b style="float: left">Qty :</b><b style="float: right">{{ number_format($grandQty, 2) }}</b></td>
                <td><b style="float: left">Weight :</b><b style="float: right">{{ number_format($grandWeight, 2) }}</b>
                </td>
                <td><b style="float: left">Sale Tax :</b><b
                        style="float: right">{{ number_format($grandSaleTax, 2) }}</b></td>
                <td><b style="float: left">Further Tax :</b><b
                        style="float: right">{{ number_format($grandFurtherTax, 2) }}</b></td>
                <td><b style="float: left">Amount :</b><b style="float: right">{{ number_format($grandTotal, 2) }}</b>
                </td>
            </tr>
        </tbody>
    </table>

</div>
