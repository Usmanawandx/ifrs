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
    }
    
    .btn-dlt {
        font-size: 14px !important;
        padding: 7px 8px 9px !important;
        border-radius: 50px !important;
    }
        
    </style>
@section('title', __('lang_v1.types_of_service'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'lang_v1.types_of_service' ) @show_tooltip(__('lang_v1.types_of_service_help_long'))
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary'])
        @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal" 
                    data-href="{{action('TypesOfServiceController@create')}}" 
                    data-container=".type_of_service_modal">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
            </div>
        @endslot
        @can('brand.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped hide-footer dataTable table-styling table-hover table-primary" id="types_of_service_table">
                    <thead>
                        <tr>
                            <th class="main-colum">@lang( 'messages.action' )</th>
                            <th>@lang( 'tax_rate.name' )</th>
                            <th>@lang( 'lang_v1.description' )</th>
                            <th>@lang( 'lang_v1.packing_charge' )</th>
                            
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

    <div class="modal fade type_of_service_modal contains_select2" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection
