<div class="table-responsive">
    <table class="table table-bordered table-striped hide-footer dataTable table-styling table-hover table-primary no-footer" id="stock_report_table_new">
        <thead>
            <tr>
                <td>SKU</td>
                <td>Product</td>
                <!--<td>Location</td>-->
                <!--<td>Unit Price</td>-->
                <td>Opening</td>
                <td>Stock In</td>
                <td>Stock Out</td>
                <td>Current Stock</td>
                <!--<td>Current Stock Value</td>-->
            </tr>
        </thead>
        <tbody>
            @foreach($products as $key => $val)
            <tr>
                <td>{{ $val['sku'] ?? '' }}</td>
                <td><a href="/products/stock-history/<?php echo $val['id'] ?>" target="_blank">{{ $val['name'] }}</td>
                <!--<td></td>-->
                {{-- <!--<td>{{ number_format($val['unit_price'], 2) }}</td>--> --}}
                <!--<td>{{ number_format($val['opening'], 2) }}</td>-->
                @php
                    // $opening_balance = $opening_balance->opening_balance;
                    $opening_balance = app('App\Http\Controllers\ReportController')->getOpeningBalanceStock($val['id'], $start);
                    $opening_balance = $opening_balance->opening_balance ?? 0;
                    $current = ($opening_balance + $val['sum_stock_in'] - $val['sum_stock_out']);
                @endphp
                <td>{{ number_format($opening_balance, 2) }}</td>
                <td>{{ number_format($val['sum_stock_in'],2) }}</td>
                <td>{{ number_format($val['sum_stock_out'],2) }}</td>
                {{-- <!--<td>{{ number_format($val['sum_current_stock'],2) }}</td>--> --}}
                <td>
                    {{ number_format($current,2) }}
                </td>
                {{-- <!--<td>{{ number_format($val['current_stock_value'],2) }}</td>--> --}}
                {{-- 
                <td>
                    @php
                        $current_value = ($val['unit_price'] * $current);
                    @endphp
                    {{ number_format($current_value, 2) }}
                    
                </td>
                --}}
            </tr>
            @endforeach
        </tbody>
    </table>
</div>