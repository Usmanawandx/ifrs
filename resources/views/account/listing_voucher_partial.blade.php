<table class="table table-xs table-striped ajax_view hide-footer dataTable table-styling table-hover table-primary"
    id="audit_trial">
    <thead>
        <tr>
            <th>Transaction Date</th>
            <th>Voucher No#</th>
            <th>Acount</th>
            <th>Debit</th>
            <th>Credit</th>
        </tr>
    </thead>
    <tbody>
        @php
            // Initialize variables for totals
            $debitTotal = 0;
            $creditTotal = 0;
            $grandDebitTotal = 0;
            $grandCreditTotal = 0;

            // Store the last voucher number
            $lastVoucher = null;
        @endphp

        @foreach ($voucher_list as $v)
            <tr>
                <td>{{ @format_date($v->operation_date) }}</td>
                <td>{{ $v->reff_no }}</td>
                <td>{{ $v->account_name }}</td>
                <td>{{ number_format($v->type == 'debit' ? $v->amount : 0, 2) }}</td>
                <td>{{ number_format($v->type == 'credit' ? $v->amount : 0, 2) }}</td>
            </tr>

            {{-- Update running totals --}}
            @php
                $debitTotal += $v->type == 'debit' ? $v->amount : 0;
                $creditTotal += $v->type == 'credit' ? $v->amount : 0;
                $grandDebitTotal += $v->type == 'debit' ? $v->amount : 0;
                $grandCreditTotal += $v->type == 'credit' ? $v->amount : 0;
            @endphp

            {{-- Check if the voucher number will change in the next iteration --}}
            @if ($loop->last || $v->reff_no != $voucher_list[$loop->index + 1]->reff_no)
                {{-- Display the total for the current voucher number --}}
                <tr>
                    <td></td>
                    <td></td>
                    <td class="text-right total-border"><strong>Total</strong></td>
                    <td class="total-border"><strong>{{ number_format($debitTotal, 2) }}</strong></td>
                    <td class="total-border"><strong>{{ number_format($creditTotal, 2) }}</strong></td>
                </tr>

                {{-- Reset totals for the new voucher number --}}
                @php
                    $debitTotal = 0;
                    $creditTotal = 0;
                @endphp
            @endif
        @endforeach
    </tbody>
    <tfoot>
        {{-- Display the grand total --}}
        <tr class="highlight-tr">
            <td><strong>Grand Total</strong></td>
            <td></td>
            <td></td>
            <td><strong>{{ number_format($grandDebitTotal, 2) }}</strong></td>
            <td><strong>{{ number_format($grandCreditTotal, 2) }}</strong></td>
        </tr>
    </tfoot>
</table>
