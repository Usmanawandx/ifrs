@php
    $total_debit = 0;
    $total_credit = 0;
@endphp

<table class="table table-border-center-col table-hover table-pl-12 hide-footer dataTable table-styling table-hover table-primary" id="trial_balance_table">
    <thead>
        <tr class="">
            <th>Account</th> 
            <th>Debit</th>
            <th>Credit</th>
        </tr>
    </thead>
    <tbody>
        {{--
        @foreach($groupedAccounts as $accountType)
            <tr>
                <td><strong>{{ $accountType[0]->account_type_name }}</strong></td>
                <td></td>
                <td></td>
            </tr>
            @foreach($accountType as $item)
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="{{ url('/account/account/'.$item->id) }}" target="_blank">{{ $item->name }}</a></td>
                    @if($item->balance > 0)
                        <td class="text-right">
                            {{ number_format(abs(round($item->balance)), 2) }}
                        </td>
                        @php $total_debit += $item->balance @endphp
                        <td class="text-right">0.0000</td>
                    @else
                        <td class="text-right">0.0000</td>
                        <td class="text-right">{{ number_format(abs(round($item->balance)), 2) }}</td>
                        @php $total_credit += $item->balance @endphp
                    @endif
                </tr>
            @endforeach
        @endforeach
        --}}
        
        <?php
                            function printRecursive($data, $indent = 0, &$total_debit, &$total_credit){
                                foreach ($data as $item) {
                                    echo '<tr style="font-size: 12px !important;">';
                                    if(isset($item->account_id)){
                                        echo '<td><a href="'. url("/account/account/" . $item->account_id) .'" target="_blank">' . str_repeat("&nbsp;&nbsp;", $indent * 4) . $item->name . '</a></td>';
                                    }else{
                                        echo '<td>' . str_repeat("&nbsp;&nbsp;", $indent * 4) . $item->name . '</td>';
                                    }
                                    if(isset($item->balance)){
                                        if ($item->balance > 0){
                                            echo '<td class="text-right">'.number_format(abs(round($item->balance)), 2) .'</td>';
                                            $total_debit += $item->balance;
                                            echo '<td class="text-right">0.00</td>';
                                        }else{
                                            echo '<td class="text-right">0.00</td>';
                                            echo '<td class="text-right">'. number_format(abs(round($item->balance)), 2) .'</td>';
                                            $total_credit += $item->balance;
                                        }
                                    }else{
                                          if(isset($item->acc_type_balance)){
                                            if ($item->acc_type_balance > 0){
                                                echo '<td class="text-right">'. (($item->acc_type_balance != 0) ? '' : '0.00') .'</td>';
                                                echo '<td class="text-right">0.00</td>';
                                            }else{
                                                echo '<td class="text-right">0.00</td>';
                                                echo '<td class="text-right">'. (($item->acc_type_balance != 0) ? '' : '0.00') .'</td>';
                                            }
                                        }else{
                                            echo '<td class="text-right">0.00</td>';    
                                            echo '<td class="text-right">0.00</td>';
                                        }
                                    }
                                    
                                    echo '</tr>';
                                    
                                    // Check if there are sub_types_recursive
                                    if (isset($item->subAccountTypes)) {
                                        printRecursive($item->subAccountTypes, $indent + 1, $total_debit, $total_credit);
                                    }

                                    // Check if there are accounts
                                    if (isset($item->accounts)) {
                                        printRecursive($item->accounts, $indent + 1, $total_debit, $total_credit);
                                    }
                                }
                            }
                        ?>
                        @foreach($accountTypes as $key => $item)
                            <?php printRecursive([$item],0 , $total_debit, $total_credit); ?>
                        @endforeach
        <tr>
            <td><strong>Difference :</strong></td>
            @php  
                $difference = number_format(round(($total_debit) - (abs($total_credit))), 2);
            @endphp
            <td class="text-right"><strong>{{ ($difference < 0) ? str_replace("-", "", $difference) : '' }}</strong></td>
            <td class="text-right"><strong>{{ ($difference > 0) ? $difference : '' }}</strong></td>
        </tr>
        <tr>
            @php
                $diff = $total_debit - abs($total_credit);
                $total_debit -= ($diff < 0) ? $diff : 0;
                $total_credit -= ($diff > 0) ? $diff : 0;
            @endphp
            <td><strong>Grand Total :</strong></td>
            <td class="text-right"><strong>{{ number_format(abs(round($total_debit)), 2) }}</strong></td>
            <td class="text-right"><strong>{{ number_format(abs(round($total_credit)), 2) }}</strong></td>
        </tr>
    </tbody>
</table>