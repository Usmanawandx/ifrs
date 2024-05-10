@extends('layouts.app')
@section('title', 'Ledger')
@section('content')
<style>
    .small-row td{
        font-size: 10px !important;
    }
    .small-row.purchase td, .small-row.sale td{
        /* border: none !important; */
    }
    .t-head td{
        font-weight: 700 !important;
    }
    .small-row.total-row td{
        border-top: 1px solid #000 !important;
        border-bottom: 1px solid #000 !important;
    }
    .small-row.t-head td{
        /* border-top: 1px solid #000 !important; */
    }
    .main-row td{
        /*font-size: 14px !important;*/
    }
</style>
    <section class="content-header no-print">
        <h1>Accounts Ledger</h1>
    </section>

    <section class="content no-print">

        <div class="row">
            <div class="col-sm-4 col-xs-6">
                <div class="box box-solid">
                    <div class="box-body">
                        <table class="table">
                            <tr>
                                <th>@lang('account.account_name'): </th>
                                <td id="account_name"></td>
                            </tr>
                            <tr>
                                <th>@lang('lang_v1.account_type'):</th>
                                <td id="account_type"></td>
                            </tr>
                            <tr>
                                <th>@lang('account.account_number'):</th>
                                <td id="account_number"></td>
                            </tr>
                            <tr>
                                <th>@lang('lang_v1.balance'):</th>
                                <td><span id="account_balance"></span></td>
                            </tr>
                        </table>
                        <br /><br /><br /><br /><br/>
                    </div>
                </div>
            </div>
            <div class="col-sm-8 col-xs-12">
                <div class="box box-solid">
                    <div class="box-header">
                        <h3 class="box-title"> <i class="fa fa-filter" aria-hidden="true"></i> @lang('report.filters'):</h3>
                    </div>
                    <div class="box-body">
                        <div class="col-md-5">
                            <div class="form-group">
                                {!! Form::label('transaction_accounts', 'Accounts :') !!}
                                {{-- {!! Form::select('transaction_accounts', $transaction_accounts, null, [
                                    'class' => 'form-control select2',
                                    'id' => 'transaction_accounts',
                                    'style' => 'width:100%'
                                ]) !!} --}}
                                <select name="transaction_accounts" class="form-control select2" id="transaction_accounts">
                                    <option disabled selected>Please Select</option>
                                    @foreach ($transaction_accounts as $val)
                                        <option 
                                        value="{{ $val->id }}"
                                        type="@if(!empty($val->account_type->parent_account)) {{$val->account_type->parent_account->name}} - @endif {{$val->account_type->name ?? ''}}"
                                        number="{{$val->account_number}}"
                                        >{{ $val->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('expense_date_range', __('report.date_range') . ':') !!}
                                {!! Form::text('expense_date_range', null, [
                                    'placeholder' => __('lang_v1.select_a_date_range'),
                                    'class' => 'form-control',
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <br>
                                <div class="checkbox">
                                    <label>
                                    {!! Form::checkbox('detail_ledger', 1, false,  
                                    [ 'class' => 'input-icheck', 'id' => 'detail_ledger']); !!}  Detail Ledger
                                    </label>
                                </div> 
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group" style="margin-top: 20px;">
                                <input type="button" value="Search" class="btn btn-primary" id="submit">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @component('components.widget', ['class' => 'box-primary hide'])
            <div id="ledger_list_div" class="table-responsive"></div>
        @endcomponent

         <!-- Modal -->
        <div class="modal fade" id="account_show" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h5 class="modal-title" id="exampleModalLabel">Voucher</h5>
                    </div>
                    <div class="modal-body account_modal_body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div> 

    </section>

@stop
@section('javascript')
    <script>
        $(document).ready(function() {
            $(document).find('#collapseFilter').addClass('in');

            $('#submit').on('click', function() {
                var start = $('input#expense_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                var end = $('input#expense_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                var account_id = $('#transaction_accounts').val();

                if (start && end && account_id) {
                    load_detail_list()
                }

            })


            function load_detail_list() {

                var start = $('input#expense_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                var end = $('input#expense_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                var account_id = $('#transaction_accounts').val();
                var account_name = $('#transaction_accounts option:selected').text();
                var account_type = $('#transaction_accounts option:selected').attr('type');
                var account_number = $('#transaction_accounts option:selected').attr('number');
                var transaction_type = $('select#transaction_type').val();

                if($('#detail_ledger').is(':checked')){
                    var url = '/account/detail_show/' + account_id;
                }else{
                    var url = '/account/account/' + account_id;
                }

                $.ajax({
                    url: url,
                    dataType: 'html',
                    data: {
                        start_date: start,
                        end_date: end,
                        type: transaction_type
                    },
                    success: function(result) {
                        $('.box-primary').removeClass('hide');
                        $('.box-primary .box-title').html(account_name);
                        $('#account_name').html(account_name);
                        $('#account_type').html(account_type);
                        $('#account_number').html(account_number);
                        $('#ledger_list_div').html(result).fadeIn();
                        $('#account_book').DataTable({
                            searching: false,
                            ordering: false,
                            "footerCallback": function(row, data, start, end, display) {
                                var api = this.api();

                                // Calculate the total debit
                                var debitTotal = api
                                    .column(5, {
                                        search: 'applied'
                                    })
                                    .data()
                                    .reduce(function(acc, curr) {
                                        return acc + parseNumber(curr);
                                    }, 0);

                                // Calculate the total credit
                                var creditTotal = api
                                    .column(6, {
                                        search: 'applied'
                                    })
                                    .data()
                                    .reduce(function(acc, curr) {
                                        return acc + parseNumber(curr);
                                    }, 0);

                                // Calculate the total balance
                                var balanceTotal = api
                                    .column(7, {
                                        search: 'applied'
                                    })
                                    .data()
                                    .reduce(function(acc, curr) {
                                        return acc + parseNumber(curr);
                                    }, 0);

                                // Update the <tfoot> with the calculated totals
                                $(api.column(5).footer()).html(formatCurrency(debitTotal));
                                $(api.column(6).footer()).html(formatCurrency(creditTotal));
                                $(api.column(7).footer()).html((debitTotal - creditTotal) <
                                    0 ? 'Cr: ' + __currency_trans_from_en(Math.abs((
                                        debitTotal - creditTotal)), true) : 'Dr: ' +
                                    __currency_trans_from_en((debitTotal - creditTotal),
                                        true));

                                $('#account_balance').html((debitTotal - creditTotal) <
                                    0 ? 'Cr: ' + __currency_trans_from_en(Math.abs((
                                        debitTotal - creditTotal)), true) : 'Dr: ' +
                                    __currency_trans_from_en((debitTotal - creditTotal),
                                        true));

                                var opening_debit = $(document).find('#opening_debit')
                                .html();
                                var opening_credit = $(document).find('#opening_credit')
                                    .html();
                                debitTotal = debitTotal - parseNumber2(opening_debit);
                                creditTotal = creditTotal - parseNumber2(opening_credit);
                                $(document).find('#curr_debit_total').html(formatCurrency(
                                    debitTotal));
                                $(document).find('#curr_credit_total').html(formatCurrency(
                                    creditTotal));
                            }

                        });
                    },
                });
            }





            $(document).on('click','.account_book_show',function(){
                var id = $(this).data('id');
                var refno = $(this).data('refno');
                var base_url = window.location.protocol + "//" + window.location.host;
                $.ajax({
                    url: base_url+"/account/invoice-prt-voucher/"+refno,
                    success: function(result){
                        
                        $(".account_modal_body").html(result);
                }
                });
                
                $('#account_show').modal('show');
                
            }) 
            function parseNumber2(value) {
                if (value && typeof value === 'string') {
                    if (value.includes(',')) {
                        var numericValue = parseFloat(value.replace(/[^\d.-]/g, '').replace(/,/g, ''));
                    } else {
                        var numericValue = parseFloat(value.replace(/[^\d.-]/g, ''));
                    }
                    return isNaN(numericValue) ? 0 : numericValue;
                } else {
                    return 0;
                }
            }
            function parseNumber(value) {
                var numericValue = parseFloat(value.replace(/[^\d.-]/g, '').replace(',', ''));
                return isNaN(numericValue) ? 0 : numericValue;
            }
            function formatCurrency(number) {
                var currencySymbol = number < 0 ? ' ₨ ' : '₨ ';
                var formattedNumber = Math.abs(number).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                return currencySymbol + formattedNumber;
            }

        })
    </script>
@endsection
