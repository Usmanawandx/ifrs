@extends('layouts.app')
@section('title', __('lang_v1.product_stock_history'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('lang_v1.product_stock_history')</h1>
</section>

<!-- Main content -->
<section class="content">
<div class="row">
    <div class="col-md-12">
    @component('components.widget', ['title' => $product->name])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
            </div>
            
        </div>
        <div class="col-md-3">
    <div class="form-group">
        {!! Form::label('sell_list_filter_date_range', __('report.date_range') . ':') !!}
        {!! Form::text('sell_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control']); !!}
    </div>
</div>
        @if($product->type == 'variable')
            <div class="col-md-3">
                <div class="form-group">
                    <label for="variation_id">@lang('product.variations'):</label>
                    <select class="select2 form-control" name="variation_id" id="variation_id">
                        @foreach($product->variations as $variation)
                            <option value="{{$variation->id}}">{{$variation->product_variation->name}} - {{$variation->name}} ({{$variation->sub_sku}})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @else
            <!--old input value $product->variations->first()->id-->
            <input type="hidden" id="variation_id" name="variation_id" value="{{$product->id}}">
        @endif
    @endcomponent
    @component('components.widget')
        <div id="product_stock_history" style="display: none;"></div>
    @endcomponent
    </div>
</div>

</section>
<!-- /.content -->
@endsection

@section('javascript')
   <script type="text/javascript">
        $(document).ready( function(){
            // load_stock_history($('#variation_id').val(), $('#location_id').val());
              $('#sell_list_filter_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
              load_stock_history($('#variation_id').val(), $('#location_id').val());
            }
        );
        $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#sell_list_filter_date_range').val('');
             load_stock_history($('#variation_id').val(), $('#location_id').val());
          
        });
        });

       function load_stock_history(variation_id, location_id) {
            $('#product_stock_history').fadeOut();
            
            if($('#sell_list_filter_date_range').val()) {
                
                var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                var start_date = start;
                var end_date = end;
            } 
            
            
            $.ajax({
                url: '/products/stock-history/' + variation_id + "?location_id=" + location_id,
                dataType: 'html',
                data:{ 
                    start_date : start_date,
                    end_date   : end_date
                },
                success: function(result) {
                    $('#product_stock_history')
                        .html(result)
                        .fadeIn();

                    __currency_convert_recursively($('#product_stock_history'));

                    $('#stock_history__table').DataTable({ 
                        searching: false,
                        ordering: false,
                        
                        
                        
                        "footerCallback": function (row, data, start, end, display) {
                            var api = this.api();
                
                            // Calculate the totals
                            // var purchaserate = api
                            //     .column(6, { search: 'applied' }) 
                            //     .data()
                            //     .reduce(function (acc, curr) {
                            //         return acc + parseNumber(curr);
                            //     }, 0);
                                
                            // var avg_rate = api
                            //     .column(7, { search: 'applied' }) 
                            //     .data()
                            //     .reduce(function (acc, curr) {
                            //         return acc + parseNumber(curr);
                            //     }, 0);
                                
                            var rec_qty = api
                                .column(7, { search: 'applied' }) 
                                .data()
                                .reduce(function (acc, curr) {
                                    return acc + parseNumber(curr);
                                }, 0);
                                
                            var rec_weight = api
                                .column(8, { search: 'applied' }) 
                                .data()
                                .reduce(function (acc, curr) {
                                    return acc + parseNumber(curr);
                                }, 0);
                                
                            var issu_qty = api
                                .column(9, { search: 'applied' }) 
                                .data()
                                .reduce(function (acc, curr) {
                                    return acc + parseNumber(curr);
                                }, 0);
                            
                            var issu_weight = api
                                .column(10, { search: 'applied' }) 
                                .data()
                                .reduce(function (acc, curr) {
                                    return acc + parseNumber(curr);
                                }, 0);
                            
                            // var bal_qty = api
                            //     .column(12, { search: 'applied' }) 
                            //     .data()
                            //     .reduce(function (acc, curr) {
                            //         return acc + parseNumber(curr);
                            //     }, 0);
                            
                            // var bal_weight = api
                            //     .column(13, { search: 'applied' }) 
                            //     .data()
                            //     .reduce(function (acc, curr) {
                            //         return acc + parseNumber(curr);
                            //     }, 0);
                
                
                            // Update the <tfoot> with the calculated totals
                            // $(api.column(6).footer()).html(number_format(purchaserate,2));
                            // $(api.column(7).footer()).html(number_format(avg_rate,2));
                            $(api.column(7).footer()).html(number_format(rec_qty,2));
                            $(api.column(8).footer()).html(number_format(rec_weight,2));
                            $(api.column(9).footer()).html(number_format(issu_qty,2));
                            $(api.column(10).footer()).html(number_format(issu_weight,2));
                            // $(api.column(12).footer()).html(number_format(bal_qty,2));
                            // $(api.column(13).footer()).html(number_format(bal_weight,2));
                            
                            
                            // For QTY
                            var opening_recived = $(document).find('#opening_recived').html();
                            var opening_issued  = $(document).find('#opening_issued').html();
                            rec_qty  = rec_qty  - parseNumber2(opening_recived);
                            issu_qty = issu_qty - parseNumber2(opening_issued);
                            $(document).find('#total_received_qty').html(number_format(rec_qty,2));
                            $(document).find('#total_issue_qty').html(number_format(issu_qty,2));

                            // For Weight
                            var recived_weight = $(document).find('#opening_recived_weight').html();
                            var issued_weight  = $(document).find('#opening_issued_weight').html();
                            rec_weight  = rec_weight  - parseNumber2(recived_weight);
                            issu_weight = issu_weight - parseNumber2(issued_weight);
                            $(document).find('#total_received_weight').html(number_format(rec_weight,2));
                            $(document).find('#total_issue_weight').html(number_format(issu_weight,2));
                        }    
                        
                        
                        
                    });
                },
            });
       }
       
              
        function parseNumber2(value) {
            if (value !== undefined && value !== null) {
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
        
        function number_format(number, decimals, dec_point = '.', thousands_sep = ',') {
            number = parseFloat(number);
            if (isNaN(number)) return 'NaN';
        
            const fixedNumber = number.toFixed(decimals);
            const parts = fixedNumber.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_sep);
        
            return parts.join(dec_point);
        }

       $(document).on('change', '#variation_id, #location_id', function(){
            load_stock_history($('#variation_id').val(), $('#location_id').val());
       });
   </script>
@endsection