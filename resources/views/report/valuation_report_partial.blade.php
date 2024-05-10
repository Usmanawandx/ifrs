<table class="table table-bordered table-striped dataTable table-styling table-hover table-primary">
    <thead>
        <tr>
            <th>SKU</th>
            <th>Product Name</th>
            <th>Total Quantity</th>
            <th>Avg Rate</th>
            <th>Total Amount</th>
            {{-- <th>Total Qty Amount</th> --}}
        </tr>
    </thead>
    <tbody>
        @forelse ($products as $item)
            @php
                $avg_rate = ($item->sum_qty != 0) ? ($item->sum_pur_amount / $item->sum_qty) : 0    
            @endphp
            <tr>
                <td>{{ $item->sku }}</td>
                <td><a target="_blank" href="{{ url('/reports/valuation-history/'.$item->id) }}">{{ $item->name }}</a></td>
                <td>{{ (!empty($item->current_stock) ? number_format($item->current_stock, 3) : '0.000') }}</td>
                <td>
                    {{ (!empty($avg_rate) ? number_format($avg_rate, 3) : '0.00') }}
                </td>
                {{-- <td>{{ (!empty($item->total_qty_amount) ? number_format($item->total_qty_amount, 3) : '0.000') }}</td> --}}
                <td>{{ !empty($avg_rate) ? number_format($item->current_stock * $avg_rate, 3) : '0.000' }}</td>
            </tr>
        @empty
            No data available.
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">Total</td>
            <td></td>
            <td></td>
            <td class="text-right"></td>
        </tr>
    </tfoot>
</table>