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
@section('title', __('purchase.purchases'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
 
    <h1 style="height: 55px;">@lang('Goods received note')
        {{-- <span class="pull-right" style="margin-top:4px;">Transaction No: {{$t_no+1}}</span> --}}
        <span class="pull-right">
            {{-- Transactio No: &nbsp;&nbsp; --}}
            {{-- <ul class="pull-right" style="font-size: 16px;">
                <li> PM: {{$pm_no}}</li>
                <li> RMP: {{$rmp_no}}</li>
            </ul> --}}
        </span>
    </h1>

</section>

<!-- Main content -->
<section class="content no-print">
    @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_location_id',  __('purchase.business_location') . ':') !!}
                {!! Form::select('purchase_list_filter_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_supplier_id',  __('purchase.supplier') . ':') !!}
                {!! Form::select('purchase_list_filter_supplier_id', $suppliers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
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

        
        <div class="col-md-3 hide">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_status',  __('purchase.purchase_status') . ':') !!}
                {!! Form::select('purchase_list_filter_status', $orderStatuses, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3 hide">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_payment_status',  __('purchase.payment_status') . ':') !!}
                {!! Form::select('purchase_list_filter_payment_status', ['paid' => __('lang_v1.paid'), 'due' => __('lang_v1.due'), 'partial' => __('lang_v1.partial'), 'overdue' => __('lang_v1.overdue')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('purchase_list_filter_date_range', __('report.date_range') . ':') !!}
                {!! Form::text('purchase_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
            </div>
        </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __('purchase.all_purchases')])
        @can('purchase.create')
            @slot('tool')
                <div class="box-tools">
                    <a class="btn btn-block btn-primary" href="{{action('PurchaseController@create')}}">
                    <i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div>
                <div class="box-tools" style="margin-right: 10px;">
                    <a class="btn btn-block btn-primary" href="{{ url('/DeleteRecords', ['type' => 'purchase']) }}">
                    <i class="fa fa-trash"></i> @lang('Delete Records')</a>
                </div>
            @endslot
        @endcan
        @include('purchase.partials.purchase_table')
    @endcomponent

    <div class="modal fade product_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade payment_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

    @include('purchase.partials.update_purchase_status_modal')

</section>

<section id="receipt_section" class="print_section"></section>

<!-- /.content -->
@stop
@section('javascript')
<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
<script>
        //Date range as a button
    $('#purchase_list_filter_date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#purchase_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
           purchase_table.ajax.reload();
        }
    );
    $('#purchase_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#purchase_list_filter_date_range').val('');
        purchase_table.ajax.reload();
    });

    $(document).on('click', '.update_status', function(e){
        e.preventDefault();
        $('#update_purchase_status_form').find('#status').val($(this).data('status'));
        $('#update_purchase_status_form').find('#purchase_id').val($(this).data('purchase_id'));
        $('#update_purchase_status_modal').modal('show');
    });

    $(document).on('submit', '#update_purchase_status_form', function(e){
        e.preventDefault();
        var form = $(this);
        var data = form.serialize();

        $.ajax({
            method: 'POST',
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            beforeSend: function(xhr) {
                __disable_submit_button(form.find('button[type="submit"]'));
            },
            success: function(result) {
                if (result.success == true) {
                    $('#update_purchase_status_modal').modal('hide');
                    toastr.success(result.msg);
                    purchase_table.ajax.reload();
                    $('#update_purchase_status_form')
                        .find('button[type="submit"]')
                        .attr('disabled', false);
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });
</script>
	
@endsection