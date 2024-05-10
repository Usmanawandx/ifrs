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
@section('title', __('unit.units'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('unit.units')
            <small>@lang('unit.manage_your_units')</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('unit.all_your_units')])
            @can('unit.create')
                @slot('tool')
                    <div class="box-tools">
                        <button type="button" class="btn btn-block btn-primary btn-modal"
                            data-href="{{ action('UnitController@create') }}" data-container=".unit_modal">
                            <i class="fa fa-plus"></i> @lang('messages.add')</button>
                    </div>
                @endslot
            @endcan
            @can('unit.view')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped hide-footer dataTable table-styling table-hover table-primary"
                        id="unit_table">
                        <thead>
                            <tr>
                                <th class="main-colum">@lang('messages.action')</th>
                                <th>@lang('unit.name')</th>
                                <th>@lang('unit.short_name')</th>
                                <th>@lang('unit.allow_decimal') @show_tooltip(__('tooltip.unit_allow_decimal'))</th>

                            </tr>
                        </thead>
                    </table>
                </div>
            @endcan
        @endcomponent

        <div class="modal fade unit_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

    </section>
    <!-- /.content -->

@endsection
