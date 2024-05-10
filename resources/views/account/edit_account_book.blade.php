@extends('layouts.app')
@section('title', __('Bank Book'))
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>@lang('Bank Book')
      <small>@lang('account.manage_your_account')</small>
   </h1>
</section>
<style>

    .select-group{
        margin-bottom: 0px;
    }
    
    .input-group{
        width: 100%;
    }


    </style>
<!-- Main content -->
<section class="content">
   {!! Form::open(['url' => action('AccountController@account_book_update'), 'method' => 'post', 'id' => 'add_purchase_form','files' => true ]) !!}
   @component('components.widget', ['class' => 'box-primary'])
   <div class="row">
      <div class="col-md-6">
         <div class="form-group">
            <div class="input-group">
               <span class="input-group-addon">
               Voucher No #
               </span>
               {!! Form::text('v_no',$AccountTransaction[0]->reff_no, ['class' => 'form-control mousetrap', 'id' => 'v_no', 'placeholder' => __('Voucher NO'), 'readonly' => 'readonly']); !!}
            </div>
         </div>
      </div>
      <div class="col-md-6">
         <div class="form-group">
            <div class="input-group">
               <span class="input-group-addon">
               Transaction Date
               </span>
               <input required class="form-control voucher_date" type="date" value="<?php echo date('Y-m-d', strtotime($AccountTransaction[0]['operation_date'])); ?>"  name="date">
            </div>
         </div>
      </div>
      <div class="col-md-6">
         <div class="form-group">
            <div class="input-group">
               <span class="input-group-addon">
               Remarks
               </span>
               <textarea name="remarks" class="form-control">{{$AccountTransaction[0]->note}}</textarea>
            </div>
         </div>
      </div>
   </div>
   {{-- <input type="text" value="{{$AccountTransaction[0]->reff_no}}"> --}}
   <div class="table-responsive">
     <table class="table table-bordered table-hover voucher_id" id="debtAccVoucher" style="max-width: 150% !important; width: 150% !important;">
         <thead>
            <tr>
                <th class="text-center" style="width: 5%;">Action</th>
                <th class="text-center" style="width: 8%;">Account Code</th>
                <th class="text-center" style="width: 20%;">Credit Account<i class="text-danger">*</i></th>
                <th class="text-center" style="width: 8%;">Account Code</th>
                <th class="text-center" style="width: 20%;">Debit Account<i class="text-danger">*</i></th>
                <th class="text-center" style="width: 10%;">Remarks</th>
                <th class="text-center" style="width: 10%;">Document No<i class="text-danger">*</i></th>
                <th class="text-center" style="width: 8%;">Attachment</th>
                <th class="text-center" style="width: 8%;">Amount<i class="text-danger" style="width: 10%;">*</i></th> 

            </tr>
         </thead>
         <tbody id="debitvoucher">
            @foreach ($AccountTransaction as $key2=>$debit)

            <tr class="tr_h">
                <td>
                    <button class="btn btn-danger red" type="button" value="Delete" onclick="remove_row(this)"><i class="fa fa-trash"></i></button>
                 </td>
               <td><input type="text" name="txtCode[]" value="" class="form-control  txtCodecredit" id="txtCodecredit" readonly="">
            <input type="hidden" name="id" class="id" value="{{$AccountTransaction[0]->id}}" >
            </td>
               <td class="" width="">
                   <div class="input-group select-group">
                     <select class="form-control select2 account_id_credit" name="credit_head[]" onchange="get_code_credit(this)" class="account_id" required>
                        <option value="">Select</option>
                        <?php foreach($other_accounts as $key => $value) {?>
   
                       @if($value->id ==$Account_credit[$key2]->account_id)
                        <option  selected value="<?php echo $value->id; ?>"  data-contact_id="{{$value->contact_id}}"><?php echo $value->name ?></option>
                        @else
                        <option value="<?php echo $value->id; ?>"  data-contact_id="{{$value->contact_id}}"><?php echo $value->name ?></option>
                        @endif
                        <?php } ?>
                     </select>
                     <span class="input-group-btn span_td"> 
                        <button  type="button" class="btn btn-info btn-adjust-credit" data-toggle="modal" data-target="#exampleModalLong" >
                            <i class="fa fa-tasks" aria-hidden="true"></i>
                        </button>
                     </span>
                    </div>
                     <div> <span class="balancecredit"></span> </div>

                </td>
                  <td><input type="text" name="txtCode[]" value="" class="form-control txtCode" id="txtCode_1" readonly=""></td>
                  <td class="" width="200p">
                <div class="input-group select-group">
                  <select class="form-control select2 account_id" name="debit_head[]" onchange="get_code(this)" class="account_id" required>
                     <option value="">Select</option>
                     <?php foreach($other_accounts as $key => $value) {?>

                    @if($value->id ==$debit->account_id)
                     <option  selected value="<?php echo $value->id; ?>"  data-contact_id="{{$value->contact_id}}"><?php echo $value->name ?></option>
                     @else
                     <option value="<?php echo $value->id; ?>"  data-contact_id="{{$value->contact_id}}"><?php echo $value->name ?></option>
                     @endif
                     <?php } ?>
                  </select>
                  
                <span class="input-group-btn span_td">
                    <button  type="button" class="btn btn-info btn-adjust-debit" data-toggle="modal" data-target="#exampleModalLong">
                        <i class="fa fa-tasks" aria-hidden="true"></i>
                    </button>
                </span>
                </div>
                  <div> <span class="balancedebit"></span> </div>
                  
               </td>
               <td><textarea name="description[]" cols="30">{{$debit->description}}</textarea></td>
            
               <td><input type="number" name="document[]"  class="form-control text-right" value="{{$debit->document}}" ></td>
               <td>
                <input type="hidden" name="attachments[]" value="{{$debit->attachment}}" class="form-control">
                <input type="file" name="attachment[]" value="{{$debit->attachment}}" class="form-control"></td>
             
               </td>
               <td><input type="number" name="amount[]" required value="{{($debit->type == "debit") ? $debit->amount:'0'}}" min="1" class="form-control total_price text-right"  onkeyup="calculationContravoucher(1)"  aria-required="true">
         
             
            </tr>
            @endforeach

         </tbody>
         <tfoot>
            <tr>
                <td colspan="2">
                    <button class="btn btn-md btn-primary addBtn" type="button"  onclick="add_row(this)" style="padding: -1px 5px 2px 5px;font-size: 17px">
                    Add Row
                    </button>
                 </td>
                
                <td colspan="6" class="text-right"><label for="reason" class="  col-form-label">Total</label>
                </td>
                
                <td class="text-right">
                   <input type="text" id="grandTotal" class="form-control text-right " name="grand_total" value="" readonly="readonly">
                </td>
         
         
            </tr>
         </tfoot>
      </table>
   </div>
   <div class="col-sm-12 fixed-button">
        <div class="text-center">
            <button type="submit" value="Save" class="btn btn-big btn-primary">Update</button>
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



   <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        {!! Form::open(['url' => action('AccountController@Aging_add'), 'method' => 'post', 'id' => 'add_purchase_form','files' => true ]) !!}
        <form action="" method="POST">
        <div class="modal-body">
        <input type="hidden" name="id_voucher" class="id_voucher" />
        Allocate Amount: <input type="text" readonly class="total_price_allocate" >
    
        <div class="invoice_data">
   
        </div>
   
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
        </form>
      </div>
    </div>
</div>





</section>
<!-- /.content -->
@endsection
@section('javascript')
<script>
   function total_amount(){
       //  alert("Sa");
       total = 0;
       $(".total_price").each(function(i,e){
           total += parseInt($(e).val());
       });
       $("#grandTotal").val(total);
   }
   $(document).ready(function(){



    $(document).on('change', '.account_id_credit, .account_id', function() {
        var contactId = $(this).find("option:selected").data("contact_id");
        if (contactId) {
            $(this).closest('td').find('button').css('display', 'block');
        } else {
            $(this).closest('td').find('button').css('display', 'none');
        }
    });
    
    // setTimeout(function () {
        $('.account_id, .account_id_credit').trigger('change');
    // }, 2000); 



    $(document).on('click','.btn-adjust-credit',function() {

        var contactId = $(this).closest("td").find("option:selected").data("contact_id");
        var total_price = $(this).closest("tr").find(".total_price").val(); 
        var id=$(".id").val(); 
        var currentUrl = window.location.href;
        var urlParts = currentUrl.split('/');
        var parameter = urlParts[urlParts.length - 1];
        // Show the modal

        $('#exampleModalLong .modal-body .total_price_allocate').val(total_price);

        $('#exampleModalLong .modal-body  .id_voucher').val(id);

        $('#exampleModalLong').modal('show');

        if (contactId) {

        $.ajax({
            method: "get",
            url: "/invoice/fetch/" + contactId,
            data:{parameter:parameter},
            success: function(result) {
                // var dataDisplayDiv = $(`#data_display_${rowNumber}`);
                $('.invoice_data').html(result);
                // row.find(".customer-table").html(result);
            }
        });
        }else{
        $('.invoice_data').html('');
        }

        });

        $(document).on('click','.btn-adjust-debit',function() {
        // Get the selected ID from the dropdown

        var contactId = $(this).closest("td").find("option:selected").data("contact_id");
        var total_price = $(this).closest("tr").find(".total_price").val(); 
        var id=$(".id").val(); 
        // Show the modal

        $('#exampleModalLong .modal-body .total_price_allocate').val(total_price);

        $('#exampleModalLong .modal-body .id_voucher').val(id);
        


        $('#exampleModalLong').modal('show');

   
        if (contactId) {

            $.ajax({
                method: "get",
                url: "/invoice/fetch/" + contactId,
                success: function(result) {
                    // var dataDisplayDiv = $(`#data_display_${rowNumber}`);
                    $('.invoice_data').html(result);
                    // row.find(".customer-table").html(result);
                }
            });
            }else{

            $('.invoice_data').html('');
            }



        });

    



   
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
   //   alert($(el).val());
   
   
         $.ajax({
         type: "GET",
         url:'/get_Account_codes/' + id,
         success:function(data){
           console.log(data);
           // $('.txtCode').val(data.account_number);
           $(el).closest("tr").find(".txtCode").val(data.account_number);
           var text = (data.balance > 0 ) ?  data.balance+' Dr: ' : (data.balance < 0 ) ? data.balance.replace('-','')+' Cr: ' : null;
           $(el).closest("tr").find(".balancedebit").html('Balance : ' + text);
   
   
         }
        })
   
       
   }
   function get_code_credit(el)
   {
     var id = $(el).val();
   //   alert($(el).val());
   
   
         $.ajax({
         type: "GET",
         url:'/get_Account_codes/' + id,
         success:function(data){
           console.log(data);
           // $('.txtCode').val(data.account_number);
           $(el).closest("tr").find(".txtCodecredit").val(data.account_number);
           var text = (data.balance > 0 ) ?  data.balance+' Dr: ' : (data.balance < 0 ) ? data.balance.replace('-','')+' Cr: ' : null;
            $(el).closest("tr").find(".balancecredit").html('Balance : ' + text);
   
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
   
   

      
   function add_row(el){
   
   // alert("s");
   
       $('#debtAccVoucher tbody tr').each(function(){
         $(this).find('.account_id,.account_id_credit').select2('destroy')
        })
        var tr = $("#debtAccVoucher #debitvoucher .tr_h:last").clone();
        tr.find('input').val('');
        tr.find('textarea').val('');
        tr.find('.balancedebit').html('');
        tr.find('.balancecredit').html('');
        tr.find('select').val('');
        $("#debtAccVoucher #debitvoucher .tr_h:last").after(tr);
            $('.account_id,.account_id_credit').select2()


}

   
    function remove_row(el) {
   var tr_length = $("#debtAccVoucher #debitvoucher .tr_h").length;
   if(tr_length > 1){
   var tr = $(el).closest("tr").remove();
   
   }else{
   alert("At least one row required");
   }		
   }
   $('.total_price').trigger('keyup');
   $('.account_id').trigger('change');
   $('.account_id_credit').trigger('change')
   
   
   
   
   
</script>
<script>
   function tryParseFloat(value) {
      if (isNaN(parseFloat(value))) {
          return 0;
      } else {
          return parseFloat(value);
      }
   }
//    $(document).on('submit','form',function(e){
//       if(tryParseFloat($('#grandTotal').val())!=tryParseFloat($('#grandTotal1').val())){
//           e.preventDefault()
//           alert('Total of Credit or Debit must be Equal to each other')

//       }
//    })

    function autoAllocate(el)
    {
         debugger;            
    var parentId = $(el).closest("div").parent().attr("id");
    var totalAmount = $('.total_price_allocate').val();
    var receiptFields = document.getElementsByName('receipts[]');
    var totalFields = document.getElementsByName('total[]');
    var totalDueFields = document.getElementsByName('due_amount[]');
    var numberOfFields = receiptFields.length;

    if (numberOfFields > 0 && totalAmount > 0) {
        for (var i = 0; i < numberOfFields; i++) {
            var due = parseFloat(totalDueFields[i].value);

            if (due > 0) {
                if (totalAmount >= due) {
                    receiptFields[i].value = due.toFixed(2);
                    totalAmount -= due;
                } else {
                    receiptFields[i].value = totalAmount;
                    totalAmount = 0;
                }
               
            }
        }
    }
}

    function income_tax(inputElement)
    {
    var $row = $(inputElement).closest('tr');
    var value = parseFloat($row.find('.icome_tax').val());
    var total_tax = parseFloat($row.find('.due_amount').val());
    var $income_tax = $row.find('.income_tax');
    if (!isNaN(value) && !isNaN(total_tax)) {
        var tax = (total_tax * value);
        var result = tax / 100;
        $income_tax.val(result);
    }
    }

function sale_tax(inputElement) {
    // Find the closest <tr> element to the input field

    var $row = $(inputElement).closest('tr');

    // Find and parse the necessary values within the same row
    var value = parseFloat($row.find('.sale_tax').val());
    var total_tax = parseFloat($row.find('.total_tax').val());
    var $wht_tax = $row.find('.wht_tax');

    if (!isNaN(value) && !isNaN(total_tax)) {
        var tax = (total_tax * value);
        var result = tax / 100;
        $wht_tax.val(result);
    }
}


$(document).ready(function(){
    $(document).on('change','.reciept-checkbox',function(){
        debugger;
        var totalAmount = $('.total_price_allocate').val();
        var receiptFields = document.getElementsByName('receipts[]');
        var totalFields = document.getElementsByName('total[]');
        var totalDueFields = document.getElementsByName('due_amount[]');
        var numberOfFields = receiptFields.length;
        var selectedElement = $(this);
        var allElements = $('.reciept-checkbox');
        var i = allElements.index(selectedElement);
        console.log('i',i);
        if($(this).is(':checked')){
            if (numberOfFields > 0 && totalAmount > 0) {
                // var amountPerField = totalAmount / numberOfFields;
                var totalPaid = 0;
                $(receiptFields).each(function(i,v){
                    if(v.value > 0){
                        totalPaid += parseFloat(v.value);
                    }
                });
                totalAmount = totalAmount - totalPaid;
                console.log("totalAmount",totalAmount);
                var totalDues = 0;
                $(totalDueFields).each(function(i,v){
                    totalDues += parseFloat(v.value);
                });
                console.log("totalDues",totalDues); 
                if(totalAmount <= totalDues){
                    var due = parseFloat(totalDueFields[i].value);
                    if(due >= totalAmount && totalAmount > 0){
                        receiptFields[i].value = totalAmount.toFixed(2);
                        totalAmount = 0;
                    }else if(due < totalAmount){
                        receiptFields[i].value = due.toFixed(2);
                        totalAmount = totalAmount - due;
                    }
                }else{
                    $(receiptFields).each(function(i,v){
                        v.value = parseFloat(0);
                    });
                    $('.reciept-checkbox').attr('checked',false);
                    $('.reciept-checkbox').attr('readonly',false);
                    alert("You Enter Larger Amount then you'r dues. Please Enter Valid Amount!");
                    $(this).attr('checked',false);
                }
            }else{
                $(receiptFields).each(function(i,v){
                    v.value = parseFloat(0);
                });
                $('.reciept-checkbox').attr('checked',false);
                $('.reciept-checkbox').attr('readonly',false);
                alert("Please Enter Valid Amount!");
                $(this).attr('checked',false);
             }
        }else{
            receiptFields[i].value = 0;
        }
    })
})


</script>
@endsection