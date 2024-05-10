@extends('layouts.app')
@section('title', __('report.stock_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('report.stock_report')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
              {!! Form::open(['url' => action('ReportController@getStockReport'), 'method' => 'get', 'id' => 'stock_report_filter_form' ]) !!}
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('product_type_id', 'Product Type' . ':') !!}
                        {!! Form::select('product_type', $product_type, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_type_id']); !!}
                    </div>
                </div>
                
                
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('category_id', __('category.category') . ':') !!}
                        {!! Form::select('category', $categories, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'category_id']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('sub_category_id', __('product.sub_category') . ':') !!}
                        {!! Form::select('sub_category', $sub_categories, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'sub_category_id']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('brand', __('product.brand') . ':') !!}
                        {!! Form::select('brand', $brands, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('unit',__('product.unit') . ':') !!}
                        {!! Form::select('unit', $units, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('stock__date_range', __('report.date_range') . ':') !!}        
                        {!! Form::text('stock__date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                    </div>
                </div>
                
               {{-- @if($show_manufacturing_data)
                    <div class="col-md-3">
                        <div class="form-group">
                            <br>
                            <div class="checkbox">
                                <label>
                                  {!! Form::checkbox('only_mfg', 1, false, 
                                  [ 'class' => 'input-icheck', 'id' => 'only_mfg_products']); !!} {{ __('manufacturing::lang.only_mfg_products') }}
                                </label>
                            </div>
                        </div>
                    </div>
                @endif
                --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <br>
                        <div class="checkbox">
                            <label>
                              {!! Form::checkbox('zero', 1, false,  
                              [ 'class' => 'input-icheck', 'id' => 'zero']); !!}  Show with Zero
                            </label>
                        </div> 
                    </div>
                </div> 
                
                
                
                {!! Form::close() !!}
            @endcomponent
        </div>
    </div>
    <!---->
    {{--
    @can('view_product_stock_value')
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-solid'])
            <table class="table no-border">
                <tr>
                    <td>@lang('report.closing_stock') (@lang('lang_v1.by_purchase_price'))</td>
                    <td>@lang('report.closing_stock') (@lang('lang_v1.by_sale_price'))</td>
                    <td>@lang('lang_v1.potential_profit')</td>
                    <td>@lang('lang_v1.profit_margin')</td>
                </tr>
                <tr>
                    <td><h3 id="closing_stock_by_pp" class="mb-0 mt-0"></h3></td>
                    <td><h3 id="closing_stock_by_sp" class="mb-0 mt-0"></h3></td>
                    <td><h3 id="potential_profit" class="mb-0 mt-0"></h3></td>
                    <td><h3 id="profit_margin" class="mb-0 mt-0"></h3></td>
                </tr>
            </table>
            @endcomponent
        </div>
    </div>
    @endcan
    --}}
    <!---->
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-solid'])
                <div class="col-md-12">
                    <button class="btn btn-sm btn-primary" id="upload_stock" style="float: right;">Import Stock</button>
                    <br><br><br>
                </div>
                <div id="stock_report_table_div">
                    
                </div>
            @endcomponent
        </div>
    </div>
</section>




    <!-- upload csv  -->
   <div id="uploadCsvModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Upload Stock </h4>
                </div>
                <form action="{{action('ReportController@importstock')}}" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <label for="csv">Import Stock</label>
                    <input type="file" name="csv" class="form-control" id="csv" required>
                    <br>
                    <a href="{{ asset('files/stock_import.xls') }}" class="btn btn-success btn-sm" download><i class="fa fa-download"></i> @lang('lang_v1.download_template_file')</a>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <input type="submit" class="btn btn-primary" value="Upload" name="submitBtn">
                </div>
                </form>
            </div>
        </div>
    </div>
    
    
    
    
    
<!-- /.content -->

@endsection

@section('javascript')
    <script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
    <script>
        $(document).on('change','#stock__date_range, #location_id, #product_type_id, #category_id, #sub_category_id, #brand, #unit', function(){
            stock_report();
        })
        $(document).on('ifChanged', '#only_mfg_products, #zero', function(){
            checkAndHideRows();
        });
        $(document).on('click', '#upload_stock', function(){
            $('#uploadCsvModal').modal('show');
        })
        
        function stock_report(){
            setTimeout(function(){
                var start   = null;
                var end     = null;
                if($('#stock__date_range').val()) {
                    start = $('#stock__date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    end = $('#stock__date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                }
                data = {
                    location_id         : $('#location_id').val(), 
                    product_type_id     : $('#product_type_id').val(),
                    category_id         : $('#category_id').val(),
                    sub_category_id     : $('#sub_category_id').val(),
                    brand               : $('#brand').val(),
                    unit                : $('#unit').val(), 
                    only_mfg_products   : $('#only_mfg_products').length && $('#only_mfg_products').is(':checked') ? 1 : 0,
                    zero                : $('#zero').length && $('#zero').is(':checked') ? 1 : 0,
                    start_date          : start,
                    end_date            : end,
                };
                $.ajax({
                    url: "/reports/stock-report",
                    data: data,
                    dataType: 'html',
                    success: function(result) {
                        $('#stock_report_table_div').html(result);
                        var table = $('#stock_report_table_new').DataTable({
                            "drawCallback": function() {
                                checkAndHideRows();
                            }
                        });
                
                        checkAndHideRows(); // Run initially
                    },
                });
            },1000);
        }
        
        
        function checkAndHideRows() {
            if (!$('#zero').is(':checked')) {
                $('#stock_report_table_new tbody tr').each(function() {
                    var opening = parseFloat($(this).find('td:eq(2)').text().trim());
                    var stock_in = parseFloat($(this).find('td:eq(3)').text().trim());
                    var stock_out = parseFloat($(this).find('td:eq(4)').text().trim());
                    var curr_stock = parseFloat($(this).find('td:eq(5)').text().trim());

                    if (opening === 0 && stock_in === 0 && stock_out === 0 && curr_stock === 0) {
                        $(this).hide()
                    } else {
                        $(this).show();
                    }
                });
            } else {

                $('#stock_report_table_new tbody tr').each(function() {
                    var opening = parseFloat($(this).find('td:eq(2)').text().trim());
                    var stock_in = parseFloat($(this).find('td:eq(3)').text().trim());
                    var stock_out = parseFloat($(this).find('td:eq(4)').text().trim());
                    var curr_stock = parseFloat($(this).find('td:eq(5)').text().trim());

                    if (opening === 0 && stock_in === 0 && stock_out === 0 && curr_stock === 0) {
                        $(this).show()
                    } else {
                        $(this).show();
                    }
                });

            }

        }
    </script>
@endsection





