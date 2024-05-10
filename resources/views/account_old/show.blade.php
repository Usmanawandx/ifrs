@extends('layouts.app')
@section('title', __('account.account_book'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Detail Account Book
    </h1>
</section>
<style>
td > span.display_currency{
    text-align: end;
}
</style>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-sm-4 col-xs-6">
            <div class="box box-solid">
                <div class="box-body">
                    <table class="table">
                        <tr>
                            <th>@lang('account.account_name'): </th>
                            <td>{{$account->name}}</td>
                        </tr>
                        <tr>
                            <th>@lang('lang_v1.account_type'):</th>
                            <td>@if(!empty($account->account_type->parent_account)) {{$account->account_type->parent_account->name}} - @endif {{$account->account_type->name ?? ''}}</td>
                        </tr>
                        <tr>
                            <th>@lang('account.account_number'):</th>
                            <td>{{$account->account_number}}</td>
                        </tr>
                        <tr>
                            <th>@lang('lang_v1.balance'):</th>
                            <td><span id="account_balance"></span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-8 col-xs-12">
            <div class="box box-solid">
                <div class="box-header">
                    <h3 class="box-title"> <i class="fa fa-filter" aria-hidden="true"></i> @lang('report.filters'):</h3>
                </div>
                <div class="box-body">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('transaction_date_range', __('report.date_range') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                {!! Form::text('transaction_date_range', null, ['class' => 'form-control', 'placeholder' => __('report.date_range')]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('transaction_type', __('account.transaction_type') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fas fa-exchange-alt"></i></span>
                                {!! Form::select('transaction_type', ['' => __('messages.all'),'debit' => __('account.debit'), 'credit' => __('account.credit')], '', ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
        	<div class="box">
                <div class="box-body">
                    @can('account.access')
                        <div class="table-responsive" id="detail_ledger_list_div">
                    	    
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    

    <div class="modal fade account_model" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>
       
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
            <div class="modal-body account_modal_body">
              
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div> 
      

</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    $(document).ready(function(){
        setTimeout(function() {
            load_detail_list();
        }, 500);

        
        
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
    
        update_account_balance();
    
        // this setting is for last 7 days filter
        // dateRangeSettings.startDate = moment().subtract(6, 'days');
        // dateRangeSettings.endDate = moment();
        
        $('#transaction_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#transaction_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                // alert();
                // account_book.ajax.reload();
                load_detail_list(true); 
            }
        );

        $('#transaction_type').change( function(){
            // account_book.ajax.reload();
            load_detail_list();
        });
        $('#transaction_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#transaction_date_range').val('');
            // account_book.ajax.reload();
            load_detail_list();
        });

    });

    $(document).on('click', '.delete_account_transaction', function(e){
        e.preventDefault();
        swal({
          title: LANG.sure,
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var href = $(this).data('href');
                $.ajax({
                    url: href,
                    dataType: "json",
                    success: function(result){
                        if(result.success === true){
                            toastr.success(result.msg);
                            account_book.ajax.reload();
                            update_account_balance();
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            }
        });
    });

    function update_account_balance(argument) {
        $('span#account_balance').html('<i class="fas fa-sync fa-spin"></i>');
        $.ajax({
            url: '{{action("AccountController@getAccountBalance", [$account->id])}}',
            dataType: "json",
            success: function(data){
                var balance = data.balance;
                var formattedBalance = balance < 0 ? 'Cr: ' + __currency_trans_from_en(Math.abs(balance), true) : 'Dr: ' + __currency_trans_from_en(balance, true);
                $('span#account_balance').text(formattedBalance);
            }
        });
    }
    
    
    function load_detail_list(is_filter = false) {
            
            var start = '';
            var end = '';
            if($('#transaction_date_range').val()){
                start = $('input#transaction_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                end = $('input#transaction_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
            }
            var transaction_type = $('select#transaction_type').val();
            // var filter_date = 0; 
            
            $.ajax({
                // url: '/account/detail_show_list/',
                dataType: 'html',
                data:{
                    start_date : start,
                    end_date   : end,
                    type : transaction_type,
                    is_filter: is_filter
                },
                success: function(result) {
                    $('#detail_ledger_list_div').html(result).fadeIn();
                    $('#account_book').DataTable({
                        searching: false,
                        ordering: false,
                        "footerCallback": function (row, data, start, end, display) {
                            var api = this.api();
                
                            // Calculate the total debit
                            var debitTotal = api
                                .column(5, { search: 'applied' }) 
                                .data()
                                .reduce(function (acc, curr) {
                                    return acc + parseNumber(curr);
                                }, 0);
                
                            // Calculate the total credit
                            var creditTotal = api
                                .column(6, { search: 'applied' }) 
                                .data()
                                .reduce(function (acc, curr) {
                                    return acc + parseNumber(curr);
                                }, 0);
                                
                            // Calculate the total balance
                            var balanceTotal = api
                                .column(7, { search: 'applied' }) 
                                .data()
                                .reduce(function (acc, curr) {
                                    return acc + parseNumber(curr);
                                }, 0);
                
                            // Update the <tfoot> with the calculated totals
                            $(api.column(5).footer()).html(formatCurrency(debitTotal));
                            $(api.column(6).footer()).html(formatCurrency(creditTotal));
                            $(api.column(7).footer()).html((debitTotal - creditTotal) < 0 ? 'Cr: ' + __currency_trans_from_en(Math.abs((debitTotal - creditTotal)), true) : 'Dr: ' + __currency_trans_from_en((debitTotal - creditTotal), true));
                            
                            var opening_debit  = $(document).find('#opening_debit').html();
                            var opening_credit = $(document).find('#opening_credit').html();
                            debitTotal =debitTotal - parseNumber2(opening_debit);
                            creditTotal =creditTotal - parseNumber2(opening_credit);
                            $(document).find('#curr_debit_total').html(formatCurrency(debitTotal));
                            $(document).find('#curr_credit_total').html(formatCurrency(creditTotal));
                        }     

                    });
                },
            });
       }
       
       function parseNumber2(value) {
            if (value && typeof value === 'string') {
                if (value.includes(',')) {
                    var numericValue = parseFloat(value.replace(/[^\d.-]/g, '').replace(/,/g, ''));
                } else {
                    var numericValue = parseFloat(value.replace(/[^\d.-]/g, ''));
                }
                return isNaN(numericValue) ? 0 : numericValue;
            } else {
                return 0; // or handle the case when value is undefined or not a string
            }
        }


       
        // Helper function to parse the number correctly, considering the currency symbol and commas
        function parseNumber(value) {
            // Remove non-numeric characters and commas
            var numericValue = parseFloat(value.replace(/[^\d.-]/g, '').replace(',', ''));
        
            // Check if the numeric value is a valid number, otherwise return 0
            return isNaN(numericValue) ? 0 : numericValue;
        }
        
        // Helper function to format the numbers as currency with the currency symbol and commas
        function formatCurrency(number) {
            var currencySymbol = number < 0 ? ' ₨ ' : '₨ ';
            var formattedNumber = Math.abs(number).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            return currencySymbol + formattedNumber;
        }
    
</script>
@endsection