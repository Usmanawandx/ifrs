@extends('layouts.app')
@section('title', 'Balance Sheet')
@section('content')
    <style>
        .border {
            border: 1px solid #bfbfbf;
        }
        .bg-color {
            background-color: #eaeef3;
        }
        .financial-period {
            /* display: flex;
            justify-content: space-between; */
            font-size: 14px;
            padding: 0px;
            text-align: center;
        }
        #asOndate{
            padding-left: 20px;
            text-decoration: underline;
        }
    </style>

    <section class="content-header no-print">
        <h1>Balance Sheet</h1>
    </section>

    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-4">
                <div class="form-group">
                    <label>Date</label>
                    {!! Form::text('end_date', @format_date('now'), [
                        'class' => 'form-control',
                        'required',
                        'id' => 'expense_transaction_date',
                    ]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <br>
                    <div class="checkbox">
                        <label>
                        {!! Form::checkbox('detail', 1, false,  
                        [ 'class' => 'input-icheck', 'id' => 'detail']); !!}  Detail
                        </label>
                    </div> 
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <br>
                    <div class="checkbox">
                        <label>
                        {!! Form::checkbox('zero', 1, false,  
                        [ 'class' => 'input-icheck', 'id' => 'zero']); !!}  Show Zero
                        </label>
                    </div> 
                </div>
            </div>
        @endcomponent
        @component('components.widget', ['class' => 'box-primary'])
            @slot('tool')
                <div class="box-tools">
                    <button class="btn btn-block btn-primary" onclick="return $('#balance_sheet').printThis();">
                        <i class="fa fa-print"></i> &nbsp;&nbsp; Print
                    </button>
                </div>
            @endslot
            <div id="balance_sheet"></div>
        @endcomponent
    </section>
@stop
@section('javascript')
    <script>
        $(document).ready(function() {
            load_detail_list();

            $(document).on('dp.change ifChanged', '#expense_transaction_date, #detail, #zero', function() {
                load_detail_list();
            })

            function load_detail_list() {
                var end = $('input#expense_transaction_date').val();
                var detail =  $('#detail').is(':checked') ? 1 : 0;
                var zero =  $('#zero').is(':checked') ? 1 : 0;
                $.ajax({
                    dataType: 'html',
                    data: {
                        end_date: end,
                        detail : detail,
                        zero: zero
                    },
                    success: function(result) {
                        $('#balance_sheet').html(result).fadeIn();
                        $('#asOndate').html(end);
                    },
                });
            }
        })
    </script>
@endsection
