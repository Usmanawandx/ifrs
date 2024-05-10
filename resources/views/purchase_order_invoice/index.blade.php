@extends('layouts.app')
@section('title', __('lang_v1.purchase_order'))

@section('content')
<style>
     .btn-edt {
    font-size: 14px !important;
    padding: 5px 8px 9px !important;
    border-radius: 50px !important;
    
}

.btn-vew {
    font-size: 14px !important;
    padding: 5px 8px 9px !important;
    border-radius: 50px !important;
    margin-right: 5px;
}

.btn-dlt {
    font-size: 14px !important;
    padding: 5px 8px 9px !important;
    border-radius: 50px !important;
    margin-left: 5px;
    margin-right: 5px;
}
    
    </style>

<!-- Content Header (Page header) -->
<section class="content-header no-print">
 
    <h1 class="top-heading" style="height: 55px;">Purchase Invoice 
        {{-- <span class="pull-right" style="margin-top: 4px;">Transactio No: {{$t_no+1}}</span> --}}
        {{-- <span class="pull-right">
                Transactio No: &nbsp;&nbsp;
                <ul class="pull-right" style="font-size: 16px;">
                    <li> PM: {{$pm_no}}</li>
                    <li> RMP: {{$rmp_no}}</li>
                </ul>
        </span> --}}
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
                {!! Form::label('transporter',  __('Transporter') . ':') !!}
                {!! Form::select('transporter', $transporter->pluck('supplier_business_name','id'), null, ['class' => 'form-control select2','id'=>'transporter','style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('vehicle',  __('Vehicle') . ':') !!}
                {!! Form::select('vehicle', $vehicles->pluck('vhicle_number','id'), null, ['class' => 'form-control select2','id'=>'vehicle','style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>


        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('sales_man',  __('Sales Man') . ':') !!}
                {!! Form::select('sales_man', $sales_man->pluck('supplier_business_name','id'), null, ['class' => 'form-control select2','id'=>'sales_man','style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('Transaction Account',  __('Transaction Account') . ':') !!}
                {!! Form::select('transaction_account', $transaction_accounts->pluck('name','id'), null, ['class' => 'form-control select2','id'=>'transaction_accounts','style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
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
                {!! Form::label('po_list_filter_date_range', __('report.date_range') . ':') !!}
                {!! Form::text('po_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
            </div>
        </div>
    @endcomponent
    @component('components.widget', ['class' => 'box-primary', 'title' => __('Purchase Invoice')])
        @can('purchase_invoice.add')
            @slot('tool')
                <div class="box-tools">
                    <a class="btn btn-block btn-primary" href="{{action('PurchaseOrderController@create_invoice')}}">
                    <i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div>
                <div class="box-tools" style="margin-right: 10px;">
                    <a class="btn btn-block btn-primary" href="{{ url('/DeleteRecords', ['type' => 'Purchase_invoice']) }}">
                    <i class="fa fa-trash"></i> @lang('Delete Records')</a>
                </div>
            @endslot
        @endcan

        <table class="table table-bordered table-striped ajax_view hide-footer dataTable table-styling table-hover table-primary" id="purchase_order_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang('messages.action')</th>
                    <th>@lang('messages.date')</th>
                    <th>Transaction No</th>
                    <th>GRN NO</th>
                    <th>Purchase Type</th>
                    <th>Product Type</th>
                    <th>Transporter</th>
                    <th>Vehicle</th>
                    <th>@lang('purchase.supplier')</th>
                    <th>Invoice Total</th>
                    <th>Transaction Account</th>
                    {{-- <th>@lang('lang_v1.quantity_remaining')</th> --}}
                    <th>@lang('sale.status')</th>
                    {{-- <th>@lang('lang_v1.shipping_status')</th> --}}
                    <th>Sales Man</th>
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
                url: '{{action("PurchaseOrderController@index_invoice")}}',
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
           

                if ($('#transaction_accounts').length) {
                    d.transaction_accounts = $('#transaction_accounts').val();
                }

                if ($('#transporter').length) {
                    d.transporter_name = $('#transporter').val();
                }

                if ($('#vehicle').length) {
                    d.vehicle_number = $('#vehicle').val();
                }

                if ($('#sales_man').length) {
                    d.sales_man = $('#sales_man').val();
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
  
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'ref_no', name: 'ref_no' },
                { data: 'pro_no', name: 'pro_no' , searchable: false},
    
                { data: 'purchase_type', name: 'purchase_type' , searchable: false},
                { data: 'product_type', name: 'product_type' , searchable: false}, 
                { data: 'tname', name: 'tname' , searchable: false}, 
                { data: 'vehicle', name: 'vehicle' , searchable: false}, 
               
                             
                { 
                    data: 'name', 
                    name: 'contacts.supplier_business_name', 
                    searchable: true,
                },     
                { data: 'invoice_total', name: 'transactions.final_total' },
                { data: 'transaction_account', name: 'transaction_account' },              
                // { data: 'po_qty_remaining', name: 'po_qty_remaining', searchable: false},
                { data: 'status', name: 'transactions.status'},
                // {data: 'shipping_status', name: 'transactions.shipping_status'},
                {data: 'sales_man', name: 'sales_man'},
                { data: 'added_by', name: 'u.first_name'},  
                { data: 'location_name', name: 'BS.name'},              
            ]
        });
        
        
        

        $(document).on(
            'change',
            '#po_list_filter_location_id, #po_list_filter_supplier_id, #po_list_filter_status, #shipping_status,#purchase_type,#product_type,#transporter,#vehicle,#sales_man,#transaction_accounts',
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