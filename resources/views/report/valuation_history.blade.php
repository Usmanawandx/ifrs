@extends('layouts.app')
@section('title', 'Inventory Valuation Report')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ 'Inventory Valuation Report' }}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['title' => $valuation_history[0]->name ?? '', 'class' => 'box-solid'])
                <div id="stock_report_table_div">
                    @php
                        $total_quantity   = 0; 
                        $total_qty_amount = 0;
                    @endphp
                
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable" id="stock_history__table">
                            <thead>
                            <tr>
                                <th rowspan="2">Date</th>
                                <th>Voucher</th>
                                <th>Voucher</th>
                                <th rowspan="2">Voucher Name</th>
                                <th rowspan="2">Rate</th>
                                <th rowspan="2">Qty</th>
                                <th rowspan="2">Purchase Amount</th>
                                <th rowspan="2">Total Qty</th>
                                <th rowspan="2">Total Qty Amount</th>
                                <th rowspan="2">Avg. Rate</th>
                            </tr>
                            <tr>
                                <th>Type</th>
                                <th>No</th>
                            </tr>
                            </thead>
                            <tbody>
                              
                            @forelse($valuation_history as $key => $history)
                                <tr>
                                    <td>{{ @format_date($history->transaction_date) }}</td>
                                    <td>
                                        @if($history->ref_no == "opening_stock")
                                            OP
                                        @else
                                            {{ explode('-', $history->ref_no)[0] ?? $history->ref_no }}
                                        @endif
                                    </td>
                                    
                                    <td class="stock_ref">
                                        
                                        @php
                                            $codeToDescription = [
                                                "DI" => "Delivery Note",
                                                "GRN" => "Purchase",
                                                "DN" => "Purchase Return",
                                                "PR" => "Purchase Requisition",
                                                "PI" => "Purchase Invoice",
                                                "PO" => "Purchase Order",
                                                "ST" => "Stock Transfer",
                                                "SA" => "Stock Adjustment",
                                                "CN" => "Sell Return",
                                                "EXP" => "Expense",
                                                "CO" => "Contacts",
                                                "PP" => "Purchase Payment",
                                                "SP" => "Sell Payment",
                                                "EP" => "Expense Payment",
                                                "BL" => "Business Location",
                                                "SO" => "Sales Order",
                                                "ML" => "Milling",
                                                "DI" => "Delivery Note",
                                                "SI" => "Sales Invoice",
                                                "SR" => "Sale Return Invoice",
                                                "PRD" => "Production",
                                                "MPRD" => "Multi Production",
                                            ];
                                            
                                            $code = explode('-', $history->ref_no)[0];
                                            $description = $codeToDescription[$code] ?? $code; 
            
            
                                        $voucherNo = "";
                                        if (explode('-', $history->ref_no)[0] == 'MPRD' || explode('-', $history->ref_no)[0] == 'SA'){
                                            $voucherNo = isset(explode('-', $history->ref_no)[1]) && isset(explode('-', $history->ref_no)[2]) ? explode('-', $history->ref_no)[1] . '-' . explode('-', $history->ref_no)[2] : $history->ref_no;
                                        }elseif (explode('-', $history->ref_no)[0] == 'PRD'){
                                            $voucherNo = isset(explode('-', $history->ref_no)[1]) ? explode('-', $history->ref_no)[1] : $history->ref_no;
                                        }else{
                                            $voucherNo = isset(explode('-', $history->ref_no)[2]) ? explode('-', $history->ref_no)[2] : $history->ref_no;
                                        }
                                        
                                        
                                        // $details = '';
                                        // if (!empty($description)) {
                                        //     if ($description == 'Delivery Note') {
                                        //         $details = '<a href="#" data-href="' . action("SellController@show", [$history->tr_id]) . '" class="btn-modal" data-container=".view_modal">' . $voucherNo . '</a>';
                                        //     }elseif ($description == 'Purchase Invoice') {
                                        //         $details = '<a href="#" data-href="' . action("PurchaseOrderController@show", [$history->tr_id]) . '" class="btn-modal" data-container=".view_modal">' . $voucherNo . '</a>';
                                        //     }elseif ($description == 'Sale Return Invoice') {
                                        //         $details = '<a href="#" data-href="' . action("SellController@show", [$history->tr_id]) . '" class="btn-modal" data-container=".view_modal">' . $voucherNo . '</a>';
                                        //     }elseif ($description == 'Purchase Return') {
                                        //         $details = '<a href="#" data-href="' . action("PurchaseReturnController@show", [$history->tr_id]) . '" class="btn-modal" data-container=".view_modal">' . $voucherNo . '</a>';
                                        //     }elseif ($description == 'Purchase') {
                                        //         $details = '<a href="#" data-href="' . action("PurchaseController@show", [$history->tr_id]) . '" class="btn-modal" data-container=".view_modal">' . $voucherNo . '</a>';
                                        //     }else{
                                        //         $details = $voucherNo;
                                        //     }
                                        // }else{
                                            $details = $voucherNo;
                                        // }
                                            
                                        if($details != "opening_stock"){
                                            echo $details;
                                        }
                                        
                                        @endphp
                                    </td>
                                    <td>
                                        @if($description == "opening_stock")
                                            Opening Stock
                                        @else
                                            {{ $description }}
                                        @endif
                                        
                                    </td> 
                                    <td>
                                        {{-- @if($history->type == "delivery_note" || $history->type == "sale_return_invoice")
                                            {{ number_format($history->unit_price_before_discount, 3) }}
                                        @else
                                            {{ number_format($history->pp_without_discount, 3) }}
                                        @endif --}}
                                        {{ ($history->rate) ? number_format($history->rate, 3) : '' }}
                                    </td>
                                    <td>{{ ($history->quantity) ? number_format($history->quantity, 3) : '' }}</td>
                                    <td>{{ ($history->purchase_amount) ? number_format($history->purchase_amount, 3) : '' }}</td>
                                    @php
                                        $total_quantity        +=  $history->quantity;
                                        $total_qty_amount      +=  $history->purchase_amount;
                                        $avg_rate               = number_format((($total_qty_amount) / ($total_quantity)), 3);
                                    @endphp
                                    <td>{{ ($total_quantity) ? number_format($total_quantity, 3) : '' }}</td>
                                    <td>{{ ($total_qty_amount) ? number_format($total_qty_amount, 3) : '' }}</td>
                                    <td>{{  $avg_rate }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center">
                                        No Valuation History Found
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>Total</td>
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
                            </tfoot>
                        </table>
                    </div>







                </div>
            @endcomponent
        </div>
    </div>
</section>
    
    
<!-- /.content -->

@endsection

@section('javascript')
    <script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
    <script>
        $(function(){
            $('.dataTable').DataTable({
                "order": [],
                "footerCallback": function (row, data, start, end, display) {
                                var api = this.api();
                    
                                var qty = api
                                    .column(5, { search: 'applied' }) 
                                    .data()
                                    .reduce(function (acc, curr) {
                                        return acc + parseNumber(curr);
                                    }, 0);
                                    
                                var purchase_amount = api
                                    .column(6, { search: 'applied' }) 
                                    .data()
                                    .reduce(function (acc, curr) {
                                        return acc + parseNumber(curr);
                                    }, 0);
                                    
                               
                                
                               
                                $(api.column(5).footer()).html(number_format(qty, 2));
                                $(api.column(6).footer()).html(number_format(purchase_amount, 2));
                            }
            });
        })
        
        function number_format(number, decimals, dec_point = '.', thousands_sep = ',') {
            number = parseFloat(number);
            if (isNaN(number)) return 'NaN';
        
            const fixedNumber = number.toFixed(decimals);
            const parts = fixedNumber.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_sep);
        
            return parts.join(dec_point);
        }
        
        function parseNumber(value) {
            var numericValue = parseFloat(value.replace(/[^\d.-]/g, '').replace(',', ''));
            return isNaN(numericValue) ? 0 : numericValue;
        }
    </script>
@endsection





