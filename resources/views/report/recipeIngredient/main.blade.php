@extends('layouts.app')
@section('title', 'Recipe Ingredient Report')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1>Recipe Ingredient Report</h1>
    </section>

    <!-- Main content -->
    <section class="content no-print">
        {{-- @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    <br>
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('recipeWise', 1, false, ['class' => 'input-icheck', 'id' => 'recipeWise']) !!} Recipe Wise
                        </label>
                    </div>
                </div>
            </div>
        @endcomponent --}}

        @component('components.widget', ['title' => 'Recipe wise Ingredients', 'class' => 'box-primary'])
        <div id="report-table">
            <table class="table table-striped ajax_view hide-footer dataTable table-styling table-hover table-primary"
                id="report_table">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Recipe Name</th>
                        <th>Recipe Qty</th>
                        <th>Ingredient Name</th>
                        <th>Ingredient Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $val)
                        <tr>
                            <td style="float: left;">{{ $loop->iteration }}</td>
                            <td>{{ $val->recipe_name }}</td>
                            <td>{{ $val->total_quantity }}</td>
                            <td>{{ $val->ingredient_name }}</td>
                            <td>{{ $val->quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endcomponent

    </section>

    <!-- /.content -->
@stop
@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#report_table').DataTable({
                ordering: false
            });
            // loadtable();
            // function loadtable() {
            //     var recipeWise = ($('#recipeWise').is(':checked')) ? 1 : 0;
            //     $.ajax({
            //         data: { recipeWise: recipeWise },
            //         success: function(response) {
            //             $('#report-table').html(response);
            //         }
            //     });
            // }

        });
    </script>

@endsection
