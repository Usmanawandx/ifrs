@extends('layouts.app')
<style>
    .btn-edt {
        font-size: 14px !important;
        padding: 7px 8px 9px !important;
        border-radius: 50px !important;
        line-height: 0px !important;
    }

    .btn-vew {
        line-height: 0px !important;
        font-size: 14px !important;
        padding: 9px 8px 9px !important;
        border-radius: 50px !important;
        line-height: 0px !important;
    }

    .btn-dlt {
        line-height: 0px !important;
        font-size: 14px !important;
        padding: 7px 8px 9px !important;
        border-radius: 50px !important;
        line-height: 0px !important;
    }
</style>
@section('title', __('Audit Trial'))
<style>
    #audit_trial {
        font-size: 12px;
    }

    .total-border {
        border-top: 1px solid !important;
        border-bottom: 1px solid !important;
        background-color: rgb(0 0 0 / 8%);
    }

    .highlight-tr {
        background-color: rgb(0 0 0 / 8%);
    }

    .highlight-tr>td {
        border-top: 1px solid !important;
        border-bottom: 1px solid !important;
    }
</style>
@section('content')
    <section class="content-header">
        <h1>Audit Trial</h1>
    </section>

    <section class="content">
        @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('expense_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('expense_date_range', null, [
                        'placeholder' => __('lang_v1.select_a_date_range'),
                        'class' => 'form-control',
                    ]) !!}
                </div>
            </div>
        @endcomponent
        @component('components.widget', ['class' => 'box-primary', 'title' => 'All VOUCHERS'])
            <div class="table-div"></div>
        @endcomponent
    </section>

@endsection
@section('javascript')
    <script>
        $(document).ready(function() {
            // $(document).find('#collapseFilter').addClass('in');
            loadtable();
            function loadtable() {
                var start_date = $('input#expense_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                var end_date = $('input#expense_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');

                $.ajax({
                    data: {
                        start: start_date,
                        end: end_date
                    },
                    success: function(response) {
                        $('.table-div').html(response);
                        $('#audit_trial').DataTable({
                            ordering: false
                        });
                    }
                });
            }
            $(document).on('change', '#expense_date_range', function() {
                loadtable();
            });

        })
    </script>
@endsection
