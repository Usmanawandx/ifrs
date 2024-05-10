@extends('layouts.app')
@section('title', __('General Voucher'))
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>@lang('General Voucher')
      <small>@lang('account.manage_your_account')</small>
   </h1>
   <button type="button" class="btn btn-primary btn-modal pull-right" 
      data-href="{{action('AccountTypeController@create')}}"
      data-container="#account_type_modal" style="margin-top: -28px;">
   <i class="fa fa-plus"></i> Add Account Type</button>
</section>
<!-- Main content -->
<section class="content">

   {!! Form::open(['url' => action('AccountController@jv_update', ['reff_no' => $jv[0]['reff_no'] ]), 'method' => 'post', 'id' => 'add_purchase_form', 'files' => true ]) !!}
   @component('components.widget', ['class' => 'box-primary'])
   <div class="row">
      <div class="col-md-6">
         <div class="form-group">
            <div class="input-group">
               <span class="input-group-addon">
               Voucher No #
               </span>
               {!! Form::text('v_no',$jv[0]['reff_no'], ['class' => 'form-control mousetrap', 'id' => 'v_no', 'placeholder' => __('Voucher NO'), 'readonly' => 'readonly']); !!}
            </div>
         </div>
      </div>
      <div class="col-md-6">
         <div class="form-group">
            <div class="input-group">
               <span class="input-group-addon">
               Transaction Date
               </span>
               <input required class="form-control voucher_date" type="date" value="<?php echo date('Y-m-d', strtotime($jv[0]['operation_date'])); ?>"  name="date">
            </div>
         </div>
      </div>
      <div class="col-md-6">
         <div class="form-group">
            <div class="input-group">
               <span class="input-group-addon">
               Remarks
               </span>
               <textarea name="remarks" class="form-control">{{ $jv[0]['note'] }}</textarea>
            </div>
         </div>
      </div>
   </div>
   <div class="table-responsive">
      <table class="table table-bordered table-hover voucher_id" id="debtAccVoucher">
         <thead>
            <tr>
                <th class="text-center">Action</th>
                <th class="text-center">Code</th>
                <th class="text-center" width="30%">Account Name<i class="text-danger">*</i></th>
                <th class="text-center" width="20%">Remarks<i class="text-danger">*</i></th>
                <th class="text-center" width="15%">Document No<i class="text-danger">*</i></th>
                <th class="text-center">Debit<i class="text-danger">*</i></th>
                <th class="text-center">Credit<i class="text-danger">*</i></th>
              
            </tr>
         </thead>
         <tbody id="debitvoucher">
             
             
            @foreach($jv as $key => $val)
            <tr class="tr_h">
                <td>
                    <button class="btn btn-danger red" type="button" value="Delete" onclick="remove_row(this)"><i class="fa fa-trash"></i></button>
                 </td>
                <td><input type="text" name="txtCode[]" value="" class="form-control txtCode" id="txtCode_1" readonly=""></td>
                <td class="" width="200p">
                   <select class="form-control select2 account_id" name="debit_head[]" onchange="get_code(this)" required>
                      <option value="">Select</option>
                      <?php foreach($other_accounts as $key => $value) {?>
                      <option value="<?php echo $value->id; ?>" {{ ($val['account_id'] == $value->id) ? 'selected' : '' }}><?php echo $value->name ?></option>
                      <?php } ?>
                   </select>
                   <div> <span class="balance"></span> </div>
                </td>
                <td><input type="text" name="desc[]" value="{{ $val['description'] }}" class="form-control"></td>
               <td><input type="text" name="doc_num[]" value="{{ $val['document'] }}" class="form-control"></td>

                <td><input type="number" name="debit[]" value="{{ ($val['type'] == 'debit') ? $val['amount'] : '0' }}" class="form-control total_price text-right"  onkeyup="calculationContravoucher(1)"  aria-required="true">
                <td><input type="number" name="credit[]" value="{{ ($val['type'] == 'credit') ? $val['amount'] : '0' }}" class="form-control total_price1 text-right"  onkeyup="calculationContravoucher(1)"  aria-required="true">
                </td>
                
             </tr>
            @endforeach
            


         </tbody>
         <tfoot>
            <tr>
                <td colspan="2">
                <button class="btn btn-md btn-primary addBtn" type="button"  onclick="add_row(this)" style="padding: -1px 5px 2px 5px;font-size:17px">
                Add Row
                </button>
                </td>
               <td colspan="3" class="text-right"><label for="reason" class="  col-form-label">Total</label>
               </td>
               <td class="text-right">
                  <input type="text" id="grandTotal" class="form-control text-right " name="grand_total" value="" readonly="readonly">
               </td>
               <td class="text-right">
                  <input type="text" id="grandTotal1" class="form-control text-right " name="grand_total" value="" readonly="readonly">
               </td>
               
            </tr>
         </tfoot>
      </table>
   </div>
   <div class="col-sm-12 fixed-button">
        <div class="text-center">
            <button type="submit" class="btn btn-big btn-primary save_vocuher">update</button>
            <button class="btn btn-big btn-danger " onclick="window.history.back()">Close</button>
        </div>
   </div>
   @endcomponent
   {!! Form::close() !!}
   <div class="modal fade account_model" tabindex="-1" role="dialog" 
      aria-labelledby="gridSystemModalLabel">
   </div>
   <div class="modal fade" tabindex="-1" role="dialog" 
      aria-labelledby="gridSystemModalLabel" id="account_type_modal">
   </div>
</section>
<!-- /.content -->
@endsection
@section('javascript')
<script>
   function total_amount(){
       total = 0;
       $(".total_price").each(function(i,e){
           total += parseInt($(e).val());
       });
       $("#grandTotal").val(total);
   }
   $(document).ready(function(){
       
        $('.account_id').trigger('change');
        $('.total_price').trigger('keyup');
   
        $(document).on('click', 'button.close_account', function(){
           swal({
               title: LANG.sure,
               icon: "warning",
               buttons: true,
               dangerMode: true,
           }).then((willDelete)=>{
               if(willDelete){
                    var url = $(this).data('url');
        
                    $.ajax({
                        method: "get",
                        url: url,
                        dataType: "json",
                        success: function(result){
                            if(result.success == true){
                               toastr.success(result.msg);
                               capital_account_table.ajax.reload();
                               other_account_table.ajax.reload();
                            }else{
                               toastr.error(result.msg);
                           }
        
                       }
                   });
               }
           });
        });
        
        $(document).on('submit', 'form#edit_payment_account_form', function(e){
           e.preventDefault();
           var data = $(this).serialize();
           $.ajax({
               method: "POST",
               url: $(this).attr("action"),
               dataType: "json",
               data: data,
               success:function(result){
                   if(result.success == true){
                       $('div.account_model').modal('hide');
                       toastr.success(result.msg);
                       capital_account_table.ajax.reload();
                       other_account_table.ajax.reload();
                   }else{
                       toastr.error(result.msg);
                   }
               }
           });
        });
        
        $(document).on('submit', 'form#payment_account_form', function(e){
                   e.preventDefault();
                   var data = $(this).serialize();
                   $.ajax({
                       method: "post",
                       url: $(this).attr("action"),
                       dataType: "json",
                       data: data,
                       success:function(result){
                           if(result.success == true){
                               $('div.account_model').modal('hide');
                               toastr.success(result.msg);
                               capital_account_table.ajax.reload();
                               other_account_table.ajax.reload();
                           }else{
                               toastr.error(result.msg);
                           }
                       }
                   });
               });
   
       // capital_account_table
       capital_account_table = $('#capital_account_table').DataTable({
                       processing: true,
                       serverSide: true,
                       ajax: '/account/account?account_type=capital',
                       columnDefs:[{
                               "targets": 5,
                               "orderable": false,
                               "searchable": false
                           }],
                       columns: [
                           {data: 'name', name: 'name'},
                           {data: 'account_number', name: 'account_number'},
                           {data: 'note', name: 'note'},
                           {data: 'balance', name: 'balance', searchable: false},
                           {data: 'action', name: 'action'}
                       ],
                       "fnDrawCallback": function (oSettings) {
                           __currency_convert_recursively($('#capital_account_table'));
                       }
                   });
       // capital_account_table
       other_account_table = $('#other_account_table').DataTable({
                       processing: true,
                       serverSide: true,
                       ajax: {
                           url: '/account/account?account_type=other',
                           data: function(d){
                               d.account_status = $('#account_status').val();
                           }
                       },
                       columnDefs:[{
                               "targets": [6,8],
                               "orderable": false,
                               "searchable": false
                           }],
                       columns: [
                           {data: 'name', name: 'accounts.name'},
                           {data: 'parent_account_type_name', name: 'pat.name'},
                           {data: 'account_type_name', name: 'ats.name'},
                           {data: 'account_number', name: 'accounts.account_number'},
                           {data: 'note', name: 'accounts.note'},
                           {data: 'balance', name: 'balance', searchable: false},
                           {data: 'account_details', name: 'account_details'},
                           {data: 'added_by', name: 'u.first_name'},
                           {data: 'action', name: 'action'}
                       ],
                       "fnDrawCallback": function (oSettings) {
                           __currency_convert_recursively($('#other_account_table'));
                       }
                   });
   
   });
   
   $('#account_status').change( function(){
       other_account_table.ajax.reload();
   });
   
   $(document).on('submit', 'form#deposit_form', function(e){
       e.preventDefault();
       var data = $(this).serialize();
   
       $.ajax({
         method: "POST",
         url: $(this).attr("action"),
         dataType: "json",
         data: data,
         success: function(result){
           if(result.success == true){
             $('div.view_modal').modal('hide');
             toastr.success(result.msg);
             capital_account_table.ajax.reload();
             other_account_table.ajax.reload();
           } else {
             toastr.error(result.msg);
           }
         }
       });
   });
   
   $('.account_model').on('shown.bs.modal', function(e) {
       $('.account_model .select2').select2({ dropdownParent: $(this) })
   });
   
   $(document).on('click', 'button.delete_account_type', function(){
       swal({
           title: LANG.sure,
           icon: "warning",
           buttons: true,
           dangerMode: true,
       }).then((willDelete)=>{
           if(willDelete){
               $(this).closest('form').submit();
           }
       });
   })
   
   $(document).on('click', 'button.activate_account', function(){
       swal({
           title: LANG.sure,
           icon: "warning",
           buttons: true,
           dangerMode: true,
       }).then((willActivate)=>{
           if(willActivate){
                var url = $(this).data('url');
                $.ajax({
                    method: "get",
                    url: url,
                    dataType: "json",
                    success: function(result){
                        if(result.success == true){
                           toastr.success(result.msg);
                           capital_account_table.ajax.reload();
                           other_account_table.ajax.reload();
                        }else{
                           toastr.error(result.msg);
                       }
   
                   }
               });
           }
       });
   });
   
   
   function get_code(el)
   {
        var id = $(el).val();
        $(el).closest("tr").find(".balance").html();
        $.ajax({
            type: "GET",
            url:'/get_Account_codes/' + id,
            success:function(data){
                $(el).closest("tr").find(".txtCode").val(data.account_number);
                var text = (data.balance > 0 ) ?  data.balance+' Dr: ' : (data.balance < 0 ) ? data.balance.replace('-','')+' Cr: ' : null;
                $(el).closest("tr").find(".balance").html('Balance : ' + text);
                var account_no = $(el).find("option:selected").attr('account_no');
                $(el).closest("tr").find(".acc_code").val(account_no);

            }
        })  
   }
   
    function calculationContravoucher(sl) {
        var gr_tot1=0;
        var gr_tot = 0;
        $(".total_price").each(function() {
           isNaN(this.value) || 0 == this.value.length || (gr_tot += parseFloat(this.value))
        });
   
        $(".total_price1").each(function() {
           isNaN(this.value) || 0 == this.value.length || (gr_tot1 += parseFloat(this.value))
        });
        $("#grandTotal").val(gr_tot.toFixed(2,2));
        $("#grandTotal1").val(gr_tot1.toFixed(2,2));
    }
   
     function add_row(el) {
          var tr = $("#debtAccVoucher #debitvoucher tr:last");
          var clone = tr.clone();
          clone.find('input').val(''); 
          clone.find('select').val(''); 
          clone.find('.balance').html('');
          clone.find('.select2-container').remove();
          tr.after(clone);
          clone.find('.select2').select2();
        }
        
   
   
    function remove_row(el) {
        var tr_length = $("#debtAccVoucher #debitvoucher .tr_h").length;
        if(tr_length > 1){
            var tr = $(el).closest("tr").remove();
        }else{
            alert("At least one row required");
        }		
   }
   
    function tryParseFloat(value) {
      if (isNaN(parseFloat(value))) {
          return 0;
      } else {
          return parseFloat(value);
      }
    }
   $(document).on('submit','form',function(e){
      if(tryParseFloat($('#grandTotal').val())!=tryParseFloat($('#grandTotal1').val())){
          e.preventDefault()
          alert('Total of Credit or Debit must be Equal to each other')
          $('.save_vocuher').attr('disabled',false)
   
      }
   })
</script>
@endsection