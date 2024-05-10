@extends('layouts.app')
@section('title', 'Profit & Loss Statment')
@section('content')
<style>
    .border{
        border: 1px solid #bfbfbf;
    }
    .bg-color{
        background-color: #eaeef3;
    }
    .financial-period{
        /* display: flex;
        justify-content: space-between; */
        font-size: 14px;
        padding: 0px;
        text-align: center;
    }
</style>

    <section class="content-header no-print">
        <h1>PROFIT & LOSS STATEMENT</h1>
    </section>

    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('expense_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('expense_date_range', null, [
                        'placeholder' => __('lang_v1.select_a_date_range'),
                        'class' => 'form-control',
                    ]) !!}
                </div>
            </div>
        @endcomponent
        @component('components.widget', ['class' => 'box-primary'])
            @slot('tool')
                <div class="box-tools">
                    <button class="btn btn-block btn-primary" onclick="return $('#profit_loss_report').printThis();">
                        <i class="fa fa-print"></i> &nbsp;&nbsp; Print
                    </button>
                </div>
            @endslot
        <div id="profit_loss_report"></div>
        @endcomponent
    </section>

@stop
@section('javascript')
    <script>
        $(document).ready(function(){
            load_detail_list();

            $(document).on('change', '#expense_date_range', function(){
                load_detail_list();
            })

            function load_detail_list() {

                var start = $('input#expense_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                var end = $('input#expense_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                
                
                var start_formated = $('input#expense_date_range').data('daterangepicker').startDate.format('DD-MM-YYYY');
                var end_formated = $('input#expense_date_range').data('daterangepicker').endDate.format('DD-MM-YYYY');

                $.ajax({
                    dataType: 'html',
                    data: {
                        start_date: start,
                        end_date: end,
                    },
                    success: function(result) {
                        $('#profit_loss_report').html(result).fadeIn();
                        $(document).find('#starting_date').html(start_formated);
                        $(document).find('#ending_date').html(end_formated);
                    },
                });
            }
        })
    </script>
@endsection
