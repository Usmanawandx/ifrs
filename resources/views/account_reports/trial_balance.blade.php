@extends('layouts.app')
@section('title', __( 'account.trial_balance' ))

@section('content')
<style>
    .sub a{
        font-size: 10px;
    }
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'account.trial_balance')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row no-print">
        <div class="col-sm-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('account_id',  'Accounts Name' . ':') !!}
                    {!! Form::select('account_id', $accounts, null, ['class' => 'form-control select2', 'multiple' , 'style' => 'width:100%', 'onchange' => 'trial_balance()']); !!}
                </div>
            </div>
            <div class="col-md-4">
                    {!! Form::label('trial_date_range', 'Date' . ':') !!} 
            		<div class="input-group date">
                		{!! Form::text('trial_date_range', null, ['class' => 'form-control', 'id' => 'trial_date_range', 'readonly', 'onchange' => 'trial_balance()' ]); !!}
                		<span class="input-group-addon"><i class="fas fa-calendar"></i></span>
                	</div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <br>
                    <div class="checkbox">
                        <label>
                          {!! Form::checkbox('is_zero', 1, false,  
                          [ 'class' => 'input-icheck', 'id' => 'is_zero']); !!}  Show with Zero
                        </label>
                    </div> 
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    <br>
    <div class="box box-solid">
        <div class="box-header print_section">
            <h3 class="box-title">{{session()->get('business.name')}} - @lang( 'account.trial_balance') - <span id="hidden_date">{{@format_date('now')}}</span></h3>
        </div>
        <div class="box-body">
            
            <div class="">
	            <button type="button" class="btn btn-xs no-print btn-info" id="expander" style="font-size: 10px;">Expand All</button>
                <button type="button" class="btn btn-xs no-print btn-danger" id="collapser" style="font-size: 10px;">Collapse All</button>
                <br><br>
            </div>
            
            
            
          <table class="table table-border-center-col table-hover table-pl-12 hide-footer dataTable table-styling table-hover table-primary" id="trial_balance_table">
            <thead>
                <tr class="">
                    <th>Name</th>
                    <th>Debit</th>
                    <th>Credit</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

            
            
            
            
        </div>
        <div class="box-footer">
            <button type="button" class="btn btn-primary no-print pull-right"onclick="window.print()">
          <i class="fa fa-print"></i> @lang('messages.print')</button>
        </div>
    </div>

</section>
<!-- /.content -->
@stop
@section('javascript')
<script src="{{ asset('js/tree-table.js') }}"></script>
<script type="text/javascript">
    
    $(document).on('ifChanged', '#is_zero', function(){
        trial_balance();
        // $('.transaction_acc').toggle();
    });
    
//     function checkAndHideRows() {
//     if (!$('#is_zero').is(':checked')) { 
//         $('#trial_balance_table tbody tr').each(function() {
//             var debit = parseFloat($(this).find('td:eq(1)').text().trim()); 
//             var credit = parseFloat($(this).find('td:eq(2)').text().trim()); 

//             if (debit === 0 && credit === 0) {
//                 $(this).hide()
//             } else {
//                 $(this).show();
//             }
//         });
//     }
// }

// $(document).ready(function() {
//     setTimeout(function() {
//         checkAndHideRows();
      
//     }, 5000); 

//     $(document).on('ifChanged', '#is_zero', function() {
//         checkAndHideRows();
//     });
//       // Manually trigger the "Collapse All" button to collapse the table
//     $('#collapser').trigger('click');
// });


      function trial_balance(){
            setTimeout(function(){
                var start   = null;
                var end     = null;
                if($('#trial_date_range').val()) {
                    start = $('#trial_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    end = $('#trial_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                }
                data = {
                    account_id: $('#account_id').val(),
                    is_zero: $('#is_zero').length && $('#is_zero').is(':checked') ? 1 : 0,
                    start_date: start,
                    end_date: end,
                };
                
                $.ajax({
                    url: "/account/trial_balacnce_data",
                    data: data,
                    dataType: 'html',
                    success: function(result) {
                        
                        $('table#trial_balance_table tbody').html(result);
                        $('#trial_balance_table').simpleTreeTable({
                            expander: $('#expander'),
                            collapser: $('#collapser')
                        });
                        $('#collapser').click();
                    },
                });
            },2000);
        }


    $(document).ready( function(){
        
        $('#trial_date_range').daterangepicker(
            dateRangeSettings,
            // { startDate: moment(), endDate: moment() },
            function(start, end) {
                $('#trial_date_range').val(
                    start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                );
                // expense_table.ajax.reload();
            }
        );

        $('#trial_date_range').on('cancel.daterangepicker', function(ev, picker) {
            // $('#product_sr_date_filter').val('');
            // expense_table.ajax.reload();
        });
        
    });

</script>

@endsection
