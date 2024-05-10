@extends('layouts.app')
@section('title', __('account.trial_balance'))

@section('content')
    <style>
        .sub a {
            font-size: 10px;
        }
    </style>
    @php
        $total_debit = 0;
        $total_credit = 0;
    @endphp

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('account.trial_balance')
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        {{-- <div class="row no-print">
        <div class="col-sm-12">
    
    </div> --}}
        <div class="row no-print">
            <div class="col-sm-12">
                @component('components.filters', ['title' => __('report.filters')])
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Date</label>
                            {{-- <input type="date" class="transaction_date form-control" id="transaction_date"> --}}
                            {!! Form::text('transaction_date', @format_date('now'), [
                                'class' => 'form-control',
                                'required',
                                'id' => 'expense_transaction_date',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Account Type</label>
                        <select name="account_type_id" id="account_type_id" class="form-control select2" required>
                            <option value="">@lang('messages.please_select')</option>
                            @foreach ($account_types as $account_type)
                                <option value="{{ $account_type->id }}" code="{{ $account_type->code }}">
                                    {{ $account_type->name }}</option>
                            @endforeach
                        </select>

                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <br>
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('is_zero', 1, false, ['class' => 'input-icheck', 'id' => 'is_zero']) !!} Show with Zero
                                </label>
                            </div>
                        </div>
                    </div>
                @endcomponent
            </div>
        </div>
        <br>
        <div class="box box-solid">
            <div class="box-header print_section">
                <h3 class="box-title">{{ session()->get('business.name') }} - @lang('account.trial_balance') - <span
                        id="hidden_date">{{ @format_date('now') }}</span></h3>
            </div>
            <div class="box-body">



         <div class="table_ledger">
    @php
        $total_debit = 0;
        $total_credit = 0;
    @endphp

    <table class="table table-border-center-col table-hover table-pl-12 hide-footer dataTable table-styling table-hover table-primary"
        id="trial_balance_table" name="filtered-data-container">
        <thead>
            <tr class="">
                <th>Account</th>
                <th>Debit</th>
                <th>Credit</th>
            </tr>
        </thead>
        {{--
        @foreach ($groupedAccounts as $accountType)
            <tr class="account-type-row">
                <td colspan="3"><b>{{ $accountType[0]->account_type_name }}</b></td>
            </tr>
            @foreach ($accountType as $item)
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="{{ url('/account/account/' . $item->account_id) }}" target="_blank">{{ $item->name }}</a></td>
                    @if ($item->balance > 0)
                        <td class="text-right">{{ number_format(abs(round($item->balance)), 2) }}</td>
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
            <td><strong>Difference:</strong></td>
            @php
                $difference = number_format(round($total_debit - abs($total_credit)), 2);
            @endphp
            <td class="text-right"><strong>{{ $difference < 0 ? str_replace("-", "", $difference) : '' }}</strong></td>
            <td class="text-right"><strong>{{ $difference > 0 ? $difference : '' }}</strong></td>
        </tr>
        <tr>
            @php
                $diff = $total_debit - abs($total_credit);
                $total_debit -= ($diff < 0) ? $diff : 0;
                $total_credit -= ($diff > 0) ? $diff : 0;
            @endphp
            <td><strong>Grand Total:</strong></td>
            <td class="text-right"><strong>{{ number_format(abs(round($total_debit)), 2) }}</strong></td>
            <td class="text-right"><strong>{{ number_format(abs(round($total_credit)), 2) }}</strong></td>
        </tr>
    </table>
</div>



            </div>
            {{-- <div class="box-footer">
                <button type="button" class="btn btn-primary no-print pull-right"onclick="window.print()">
                    <i class="fa fa-print"></i> @lang('messages.print')</button>
            </div> --}}
        </div>

    </section>
    <!-- /.content -->
@stop
@section('javascript')
    <script src="{{ asset('js/tree-table.js') }}"></script>

    <script type="text/javascript">
        function checkAndHideRows() {
            if (!$('#is_zero').is(':checked')) {
                $('#trial_balance_table tbody tr').each(function() {
                    var debit = parseFloat($(this).find('td:eq(1)').text().trim());
                    var credit = parseFloat($(this).find('td:eq(2)').text().trim());

                    if (debit === 0 && credit === 0) {
                        $(this).hide()
                    } else {
                        $(this).show();
                    }
                });
            } else {

                $('#trial_balance_table tbody tr').each(function() {
                    var debit = parseFloat($(this).find('td:eq(1)').text().trim());
                    var credit = parseFloat($(this).find('td:eq(2)').text().trim());

                    if (debit === 0 && credit === 0) {
                        $(this).show()
                    } else {
                        $(this).show();
                    }
                });

            }

        }

        $(document).ready(function() {

            $('#transaction_date').trigger('dp.change');

            checkAndHideRows();
            $(document).on('ifChanged', '#is_zero', function() {
                checkAndHideRows();
            });
            // Manually trigger the "Collapse All" button to collapse the table
            $('#collapser').trigger('click');

            $('#expense_transaction_date, #account_type_id').on('change dp.change', function() {

                var date = $("#expense_transaction_date").val();
                var accounttype = $("#account_type_id").val();

                
                // alert(date);

                $.ajax({
                    url: '{{ route('transaction-trial-balance') }}', // Use the named route
                    type: 'GET',
                    data: {
                        date: date, 
                        accounttype : accounttype,
                    },
                    success: function(response) {
                        // alert(response)
                        // Update your container with the received HTML
                        $('.table_ledger').html(response.html);
                        checkAndHideRows();
                        $('#trial_balance_table').DataTable({
                            searching: false,
                            order:false,
                            paging: false
                        });
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            });
            
            
            $('#trial_balance_table').DataTable({
                searching: false,
                order:false,
                paging: false
            });
            
            

        });

 
    </script>
    {{-- <script>
        function filterPageByAccountType() {
            var selectedAccountType = $('#account_type_id').val();

            // Perform AJAX request to fetch and display filtered data based on selected account type
            $.ajax({
                type: 'GET',
                url: '{{ route('filter-data') }}', // Update this URL
                data: {
                    account_type_id: selectedAccountType
                },
                success: function(response) {
                    // Update the page content with the filtered data
                    $('#trial_balance_table').html(response);
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        }
    </script> --}}
@endsection