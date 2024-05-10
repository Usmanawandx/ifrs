@extends('layouts.app')
@section('title', 'Inventory Valuation Summary')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ 'Inventory Valuation Summary' }}</h1>
</section>

<!-- Main content -->
<section class="content">
            @component('components.filters', ['title' => __('report.filters')])

            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('expense_transaction_date', 'Date' . ':') !!}
                    {!! Form::text('expense_transaction_date', @format_date('now'), ['class' => 'form-control', 'required','id'=>'expense_transaction_date']); !!}
                </div>
            </div>

            @endcomponent
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-solid'])
                <div id="stock_report_table_div">
                    
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
            load_valuation_summary();
        });


        $(document).on('dp.change','#expense_transaction_date', function(){
            load_valuation_summary();
        })
            
        function load_valuation_summary(){
            var formattedDate = $('#expense_transaction_date').val();
            if (formattedDate) {
                $.ajax({
                    url: "{{action('ReportController@valuationReport')}}",
                    data: { date: formattedDate },
                    dataType: 'html',
                    success: function(result) {
                        if ($.fn.DataTable.isDataTable('.dataTable')) {
                            $('.dataTable').DataTable().destroy();
                        }
                        $('#stock_report_table_div').html(result);
                        $('.dataTable').DataTable({
                            "footerCallback": function (row, data, start, end, display) {
                                var api = this.api();

                                var qty = api
                                    .column(2, { search: 'applied' }) 
                                    .data()
                                    .reduce(function (acc, curr) {
                                        return acc + parseNumber(curr);
                                    }, 0);
                    
                                var amount = api
                                    .column(4, { search: 'applied' }) 
                                    .data()
                                    .reduce(function (acc, curr) {
                                        return acc + parseNumber(curr);
                                    }, 0);
                                
                                $(api.column(2).footer()).html(qty);
                                $(api.column(4).footer()).html(formatCurrency(amount));
                            }    
                        });
                    },
                });
            } else {
                console.error('Selected date is not available or invalid.');
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
    </script>
@endsection





