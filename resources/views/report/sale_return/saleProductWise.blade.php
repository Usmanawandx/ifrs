@extends('layouts.app')
@section('title', 'Sale Return Product wise')

@section('content')
<style> 
    .total-row td{
        font-weight: 700;
        border-top: 1px solid #0000003e !important; 
        border-bottom: 1px solid #00000051 !important; 
    }
</style>
    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1>Sale Return Product Wise Report </h1>
    </section>

    <!-- Main content -->
    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
            <!--@include('sell.partials.sell_list_filters')-->
             <div class="col-md-3">
                <div class="form-group">
              
                    <div class="">
                     
                        <label>Product</label>
                        <select name="products" id="products" class="form-control select2" required>
                            <option value="">@lang('messages.please_select')</option>
                            @foreach ($products as $p)
                                <option value="{{ $p->id }}" >
                                    {{ $p->name }}</option>
                            @endforeach
                        </select>

                    </div> 
                </div>
            </div>
             <div class="col-md-3">
                <div class="form-group">
                    <div class="">
                        <label>Product Type</label>
                        <select name="product_type" id="product_type" class="form-control select2" required>
                            <option value="">@lang('messages.please_select')</option>
                            @foreach ($product_type as $p_type)
                                <option value="{{ $p_type->id }}" >
                                    {{ $p_type->name }}</option>
                            @endforeach
                        </select>

                    </div> 
                </div>
            </div>
                <div class="col-md-3">
                <div class="form-group">
              
                    <div class="">
                     
                        <label>Product Category</label>
                        <select name="product_category" id="product_category" class="form-control select2" required>
                            <option value="">@lang('messages.please_select')</option>
                            @foreach ($product_caetgory as $p_cat)
                                <option value="{{ $p_cat->id }}" >
                                    {{ $p_cat->name }}</option>
                            @endforeach
                        </select>

                    </div> 
                </div>
            </div>
                <div class="col-md-3">
                <div class="form-group">
              
                    <div class="">
                     
                        <label>Product SubCategory</label>
                        <select name="product_sub_category" id="product_sub_category" class="form-control select2" required>
                            <option value="">@lang('messages.please_select')</option>
                            @foreach ($subcategory as $p_cat)
                                <option value="{{ $p_cat->id }}" >
                                    {{ $p_cat->name }}</option>
                            @endforeach
                        </select>

                    </div> 
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <br>
                    <div class="checkbox">
                        <label>
                          {!! Form::checkbox('summary_report', 1, false,  
                          [ 'class' => 'input-icheck', 'id' => 'summary_report']); !!}  Summary Report
                        </label>
                    </div> 
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <br>
                    <div class="checkbox">
                      <label>
                            {!! Form::checkbox('show_zero', 1, false, ['class' => 'input-icheck', 'id' => 'show_zero']); !!}
                            Show Zero
                        </label>
                    </div> 
                </div>
            </div>
           
            {{-- <div class="col-md-3">
                <div class="form-group">
                    <br>
                    <div class="checkbox">
                        <label>
                          {!! Form::checkbox('hide_weight', 1, false,  
                          [ 'class' => 'input-icheck', 'id' => 'hide_weight']); !!}  Hide Weight
                        </label>
                    </div> 
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <br>
                    <div class="checkbox">
                        <label>
                          {!! Form::checkbox('hide_further_tax', 1, false,  
                          [ 'class' => 'input-icheck', 'id' => 'hide_further_tax']); !!}  Hide Further Tax
                        </label>
                    </div> 
                </div>
            </div> --}}

        @endcomponent

        @component('components.widget', ['class' => 'box-primary'])
            <div id="sale-report-table">
               
            </div>
        @endcomponent

    </section>

    <!-- /.content -->
@stop
@section('javascript')
<script type="text/javascript" src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var buttons = [
                {
                    extend: 'copy',
                    text: '<i class="fa fa-file" aria-hidden="true"></i> ' + LANG.copy,
                    className: 'buttons-csv btn-sm',
                    exportOptions: {
                        columns: ':visible',
                        stripHtml: true,
                    },
                    footer: true,
                },
                {
                    extend: 'csv',
                    text: '<i class="fa fa-file-csv" aria-hidden="true"></i> ' + LANG.export_to_csv,
                    className: 'btn-sm',
                    exportOptions: {
                        columns: ':visible',
                        stripHtml: true
                    },
                    footer: true,
                },
                {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel" aria-hidden="true"></i> ' + LANG.export_to_excel,
                    className: 'btn-sm',
                    exportOptions: {
                        columns: ':visible',
                        stripHtml: true
                    },
                    footer: true,
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print" aria-hidden="true"></i> ' + LANG.print,
                    className: 'btn-sm',
                    exportOptions: {
                        columns: ':visible',
                        stripHtml: true,
                    },
                    footer: true,
                    customize: function ( win ) {
                        // if ($('.print_table_part').length > 0 ) {
                        //     $($('.print_table_part').html()).insertBefore($(win.document.body).find( 'table' ));
                        // }
                        $(win.document.body).find( '.table tbody .hide' ).remove();
                        if ($(win.document.body).find( 'table.hide-footer').length) {
                            $(win.document.body).find( 'table.hide-footer tfoot' ).remove();
                        }
                        __currency_convert_recursively($(win.document.body).find( 'table' ));
                        // ///////////// //

                        var gap = '15mm';
                        var style = document.createElement('style');
                        style.innerHTML = '@page { margin: ' + gap + '; }';
                        win.document.head.appendChild(style);
 
                        $(win.document.body).find( 'table' ).addClass( 'compact' ).css( 'font-size', 'inherit' );
                        $(win.document.body).find( 'table tbody td' ).css({'font-size': '8px'});
                    }
                },
                {
                    extend: 'colvis',
                    text: '<i class="fa fa-columns" aria-hidden="true"></i> ' + LANG.col_vis,
                    className: 'btn-sm',
                },
                {
                    text: '<i class="fa fa-image" aria-hidden="true"></i> Export Image',
                    className: 'buttons-csv btn-sm',
                    action: function ( e, dt, node, config ) {
                        html2canvas(document.getElementById('report_table')).then(function(canvas) {
                            var imgData = canvas.toDataURL('image/png');
                            var link = document.createElement('a');
                            link.href = imgData;
                            link.download = 'Sales-Report.png';
                            link.click();
                        });
                    }
                },
            ];
            var pdf_btn = {
                extend: 'pdf',
                text: '<i class="fa fa-file-pdf" aria-hidden="true"></i> ' + LANG.export_to_pdf,
                className: 'btn-sm',
                exportOptions: {
                    columns: ':visible',
                    stripHtml: true
                },
                customize: function (doc) {
                    doc.pageMargins = [20, 30, 20, 30];
                    doc.defaultStyle.fontSize = 12;
                    doc.defaultStyle.textColor = '#333';
                    doc.styles.tableHeader.fontSize = 8;
                    doc.styles.tableHeader.bold = true;
                    doc.styles.tableBodyEven.fontSize = 6;
                    doc.styles.tableBodyOdd.fontSize = 6;
                    doc.footer = function (currentPage, pageCount) {
                        return {
                            text: 'Page ' + currentPage.toString() + ' of ' + pageCount,
                            style: 'footer'
                        };
                    };
                },
                footer: true,
            };

            if (non_utf8_languages.indexOf(app_locale) == -1) {
                buttons.push(pdf_btn);
            }
            jQuery.extend($.fn.dataTable.defaults, {
                buttons: buttons,
                iDisplayLength: -1,
            });
            //Date range as a button
            $('#sell_list_filter_date_range').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                    loadSaleReport();
                }
            );
            $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#sell_list_filter_date_range').val('');
                loadSaleReport();
            });

            loadSaleReport();
            function loadSaleReport(summary = null) {
                if($('#sell_list_filter_date_range').val()) {
                    var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                }
                $.ajax({
                    data: {
                            start_date          : start,
                            end_date            : end,
                            location_id         : $('#sell_list_filter_location_id').val(),
                            customer_id         : $('#sell_list_filter_customer_id').val(),
                            type                : $('#sell_list_filter_sale_type').val(),
                            transporter         : $('#sell_list_filter_transporter').val(),
                            vehicle             : $('#sell_list_filter_vehicle').val(),
                            salesman            : $('#sell_list_filter_salesman').val(),
                            transaction_account : $('#sell_list_filter_accounts').val(),
                            control_account     : $('#sell_list_filter_accountsType').val(),
                            products            : $('#products').val(),
                            product_type        : $('#product_type').val(),
                            product_category    : $('#product_category').val(),
                            product_sub_category: $('#product_sub_category').val(),
                            show_zero           : $('#show_zero').prop('checked') ? 1 : 0,
                            summary             : summary,
                        },
                    success: function (response) {
                        $('#sale-report-table').html(response);
                        var table = $('#report_table').DataTable({
                            ordering: false,
                            searching: false,

                        });
                    }
                });
            }
            

            $(document).on('change', '#sell_list_filter_products,#products,#product_type,#product_category,#product_sub_category,#sell_list_filter_location_id, #sell_list_filter_customer_id, #sell_list_filter_payment_status, #created_by, #sales_cmsn_agnt, #service_staffs, #shipping_status, #sell_list_filter_source, #sell_list_filter_sale_type, #sell_list_filter_transporter, #sell_list_filter_vehicle, #sell_list_filter_salesman, #sell_list_filter_accounts, #sell_list_filter_accountsType',  function() {
                $('#summary_report, #hide_weight, #hide_further_tax').trigger('ifChanged');
            });


            $('#summary_report, #hide_weight, #hide_further_tax,#show_zero,#products,#product_type,#product_category,#product_sub_category').off('ifChanged').on('ifChanged', function(e){
                if ($('#summary_report').is(':checked')) {
                    loadSaleReport(1);
                }else{
                    loadSaleReport();
                }
            })

        });
    </script>

@endsection
