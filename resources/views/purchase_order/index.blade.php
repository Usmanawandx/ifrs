@extends('layouts.app')

<style>
.btn-edt {
    font-size: 14px !important;
    padding: 7px 8px 9px !important;
    border-radius: 50px !important;
    
}

.btn-vew {
    font-size: 14px !important;
    padding: 9px 8px 9px !important;
    border-radius: 50px !important;
    margin-right: 5px;
}

.btn-dlt {
    font-size: 14px !important;
    padding: 7px 8px 9px !important;
    border-radius: 50px !important;
    margin-left: 5px;
    margin-right: 5px;
}
    
</style>
@section('title', __('lang_v1.purchase_order'))

@section('content')


<!-- Content Header (Page header) -->
<section class="content-header no-print">
    
    <!--<h1></h1>-->
    <h1 class="top-heading">@lang('lang_v1.purchase_order')
        {{-- <span  class="pull-right" style="margin-top:4px;">Transaction No: {{$t_no+1 }}</span> --}}
    </h1>
</section>

<!-- Main content -->
<section class="content no-print">
    @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('po_list_filter_location_id',  __('purchase.business_location') . ':') !!}
                {!! Form::select('po_list_filter_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('po_list_filter_supplier_id',  __('purchase.supplier') . ':') !!}
                {!! Form::select('po_list_filter_supplier_id', $suppliers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3 hide">
            <div class="form-group">
                {!! Form::label('po_list_filter_status',  __('sale.status') . ':') !!}
                {!! Form::select('po_list_filter_status', $purchaseOrderStatuses, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        @if(!empty($shipping_statuses))
            <div class="col-md-3 hide">
                <div class="form-group">
                    {!! Form::label('shipping_status', __('lang_v1.shipping_status') . ':') !!}
                    {!! Form::select('shipping_status', $shipping_statuses, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
        @endif
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('purchase_type',  __('Purchase Type') . ':') !!}
                {!! Form::select('purchase_type', $purchase_type->pluck('Type','id'), null, ['class' => 'form-control select2','id'=>'purchase_type', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('product_type',  __('Product Type') . ':') !!}
                {!! Form::select('product_type', $product_type->pluck('name','id'), null, ['class' => 'form-control select2','id'=>'product_type','style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('po_list_filter_date_range', __('report.date_range') . ':') !!}
                {!! Form::text('po_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
            </div>
        </div>
    @endcomponent
    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_purchase_orders')])
        @can('purchase_order.create')
            @slot('tool')
                <div class="box-tools">
                    <a class="btn btn-block btn-primary" href="{{action('PurchaseOrderController@create')}}">
                    <i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div>
                <div class="box-tools" style="margin-right: 10px;">
                    <a class="btn btn-block btn-primary" href="{{ url('/DeleteRecords', ['type' => 'purchase_order']) }}" style="display:none;">
                    <i class="fa fa-trash"></i> @lang('Delete Records')</a>
                </div>
            @endslot
        @endcan
        {{-- <button id="export">Table Export</button> --}}
        <table class="table table-bordered table-striped ajax_view hide-footer dataTable table-styling table-hover table-primary no-footer" id="purchase_order_table" style="width: 100%;">
            <thead>
                <tr>
                    <th class="">@lang('messages.action')</th>
                    <th>@lang('messages.date')</th>
                    <th>Transaction No</th>
                    <th>Purchase Type</th>
                    <th>Product Type</th>
                    <th>Supplier Name</th>
                    <th>Generated By</th>
                    <th>@lang('purchase.location')</th>
           
                </tr>
            </thead>
        </table>
    @endcomponent
    <div class="modal fade edit_pso_status_modal" tabindex="-1" role="dialog"></div>
</section>
<!-- /.content -->
@stop
@section('javascript')	
@includeIf('purchase_order.common_js')
<script type="text/javascript">
    $(document).ready( function(){
        //Purchase table
        var i=1;
        purchase_order_table = $('#purchase_order_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[1, 'desc']],
            scrollY: "75vh",
            scrollX:        true,
            scrollCollapse: true,
            ajax: {
                url: '{{action("PurchaseOrderController@index")}}',
                data: function(d) {
                    if ($('#po_list_filter_location_id').length) {
                        d.location_id = $('#po_list_filter_location_id').val();
                    }
                    if ($('#po_list_filter_supplier_id').length) {
                        d.supplier_id = $('#po_list_filter_supplier_id').val();
                    }
                    if ($('#po_list_filter_status').length) {
                        d.status = $('#po_list_filter_status').val();
                    }
                    if ($('#shipping_status').length) {
                        d.shipping_status = $('#shipping_status').val();
                    }

                    if ($('#purchase_type').length) {
                        d.purchase_category = $('#purchase_type').val();
                    }
                    if ($('#product_type').length) {
                        d.purchase_type = $('#product_type').val();
                    }
                    var start = '';
                    var end = '';
                    if ($('#po_list_filter_date_range').val()) {
                        start = $('input#po_list_filter_date_range')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        end = $('input#po_list_filter_date_range')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                    }
                    d.start_date = start;
                    d.end_date = end;

                    d = __datatable_ajax_callback(d);
                },
            },
       
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false },
               
               
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'ref_no', name: 'ref_no'  },
                { data: 'purchase_type', name: 'purchase_typess' , searchable: false },
                { data: 'product_type', name: 'product_type' , searchable: false },
                { data: 'name', name: 'contacts.name'  },
                { data: 'added_by', name: 'u.first_name'  },
                { data: 'location_name', name: 'BS.name'  },
                
            ]
        });

        $(document).on(
            'change',
            '#po_list_filter_location_id, #po_list_filter_supplier_id, #po_list_filter_status, #shipping_status,#purchase_type,#product_type',
            function() {
                purchase_order_table.ajax.reload();
            }
        );

        $('#po_list_filter_date_range').daterangepicker(
        dateRangeSettings,
            function (start, end) {
                $('#po_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
               purchase_order_table.ajax.reload();
            }
        );
        $('#po_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#po_list_filter_date_range').val('');
            purchase_order_table.ajax.reload();
        });

        $(document).on('click', 'a.delete-purchase-order', function(e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).attr('href');
                    $.ajax({
                        method: 'DELETE',
                        url: href,
                        dataType: 'json',
                        success: function(result) {
                            if (result.success == true) {
                                toastr.success(result.msg);
                                purchase_order_table.ajax.reload();
                                location.reload();
                               
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });
    });
</script>

@endsection