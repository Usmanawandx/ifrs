@extends('layouts.app')

@if(request('type') == 'cash_received_voucher')
    @section('title', 'Cash Received Voucher')
@else
   @section('title', __('Reciept Voucher'))
@endif

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        @if(request('type') == 'cash_received_voucher')
            Cash Received Voucher
        @else
           @lang('Reciept Voucher')
        @endif
        <small>@lang('account.manage_your_account')</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
        
           {!! Form::open(['url' => action('AccountController@credit_voucher_create'), 'method' => 'post', 'id' => 'add_purchase_form', 'files' => true ]) !!}
        
    	
	@component('components.widget', ['class' => 'box-primary'])
		<div class="row">
		    
		    <div class="col-md-6">
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon">
							Voucher No #
						</span>
						
						@if(request('type') == 'cash_received_voucher')
                            {!! Form::text('v_no','CRV-'.$voucher_no, ['class' => 'form-control mousetrap', 'id' => 'v_no', 'placeholder' => __('Voucher NO'), 'readonly' => 'readonly']); !!}
                            <input type="hidden" name="type" value="cash_received_voucher"/>
                        @else
                           {!! Form::text('v_no','R-'.$voucher_no, ['class' => 'form-control mousetrap', 'id' => 'v_no', 'placeholder' => __('Voucher NO'), 'readonly' => 'readonly']); !!}
                        @endif
					</div>
				</div>
			</div>
            <div class="col-md-6">
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon">
							Transaction Date
						</span>
						<input required class="form-control" type="date" value="<?php echo date("Y-m-d"); ?>" name="receipt_date">
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon">
							    Debit Account Head
						</span>
						<select class="form-control select2" name="credit_head" required onchange="get_code_top(this)">
						    <option value="">Select</option>
						    <?php foreach($payment_account as $key => $value) {?>
					                <option value="<?php echo $value->id; ?>"><?php echo $value->name ?></option>
						    <?php } ?>
						</select>
					</div>
					<div> <span class="balance_top"></span> </div>
				</div>
			</div>
		   
			
			<div class="col-md-6">
				<div class="form-group">
					<input type="file" name="attachment" class="form-control" />
				</div>
			</div>
			<div class="clearfix"></div>
			
	
			<div class="col-md-6">
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon">
							Remarks
						</span>
					   <textarea name="remarks" class="form-control"></textarea>
					</div>
				</div>
			</div>
		</div>
        
        <div class="table-responsive">
           <table class="table table-bordered table-hover voucher_id" id="debtAccVoucher">
              <thead>
                 <tr>
                    <th class="text-center" width="1%">Action</th>
                    <th class="text-center" width="10%">Code</th>
                    <th class="text-center" width="30%">Account Name<i class="text-danger">*</i></th>
                    <th class="text-center" width="15%">Document No<i class="text-danger">*</i></th>
                    <th class="text-center">Remarks<i class="text-danger">*</i></th>
                    <th class="text-center" width="11%">Amount<i class="text-danger">*</i></th>
                 </tr>
              </thead>
              <tbody id="debitvoucher">
                 <tr id="tr_data_display_1">
                    <td>
                       <button class="btn btn-danger red" type="button" value="Delete" onclick="remove_row(this)"><i class="fa fa-trash"></i></button>
                    </td>
                    <td><input type="text" name="txtCode[]" value="" class="form-control acc_code" id="txtCode_1" readonly=""></td>
                    <td class="" width="200p">
                       <!-- <select class="form-control select2" name="debit_head[]" required onchange="get_code(this)"> -->
                       <select class="form-control select2" name="debit_head[]" required onchange="get_code(this, 1)">
                          <option value="">Select</option>
                          <?php foreach($other_accounts as $key => $value) {?>
                          <option value="<?php echo $value->id; ?>" data-contact_id="{{$value->contact_id}}"  account_no="<?php echo $value->account_number; ?>" ><?php echo $value->name ?></option>
                          <?php } ?>
                       </select>
                       <div> <span class="balance"></span> </div>
                    </td>
                    <td><input type="text" name="doc_num[]" value="" class="form-control"></td>
                    <td><input type="text" name="desc[]" value="" class="form-control"></td>
                    <td><input type="number" name="txtAmount[]" value="0" class="form-control total_price text-right" id="txtAmount_1" onkeyup="total_amount()" required="" aria-required="true">
                    </td>
                 </tr>
              </tbody>
              <tfoot>
                 <tr>
                     <td colspan="2"><a id="add_more" class="btn btn-info" name="add_more" onclick="add_row(this)">Add Row</a></td>
                   
                    <td colspan="3" class="text-right"><label for="reason" class="  col-form-label">Total</label>
                    </td>
                    <td class="text-right">
                       <input type="text" id="grandTotal" class="form-control text-right " name="grand_total" value="" readonly="readonly">
                    </td>
                    
                 </tr>
              </tfoot>
           </table>
        </div>
        <br>
        <br>
        <div id="main_div">



        <div id="data_display_1"></div>
        </div>
            
        </div>
            	
          
            
        <div class="col-sm-12 fixed-button">
            <div class="text-center">
             <button type="submit"  class="btn-big btn-primary">Save</button>
             <button class="btn-big btn-danger" type="button" onclick="window.history.back()">Close</button>
            </div>
        </div>
    	@endcomponent

    	
    	

{!! Form::close() !!}


<br>

    
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
    

    function get_code_top(el)
    {
        var id = $(el).val();
        $(".balance_top").html();
        $.ajax({
            type: "GET",
            url:'/get_Account_codes/' + id,
            success:function(data){
                $(el).closest("tr").find(".txtCode").val(data.account_number);
                var text = (data.balance > 0 ) ?  data.balance+' Dr: ' : (data.balance < 0 ) ? data.balance.replace('-','')+' Cr: ' : null;
                $(".balance_top").html('Balance : ' + text);
            }
        })  
    }
   
    function add_row(el) { 
      var tr = $("#debtAccVoucher #debitvoucher tr:last");
      var clone = tr.clone();
      clone.find('input').val('');
      clone.find('.select2-container').remove();
      tr.after(clone);
      clone.find('.select2').select2();
      clone.find('.total_price').val('0');
      clone.find(".balance").html('');
      
      var dataIndex = $("#debtAccVoucher #debitvoucher tr").length;

      var dataDisplayDiv = $('<div id="data_display_' + dataIndex + '"></div>');
      clone.after(dataDisplayDiv);
      clone.attr('id','tr_data_display_' + dataIndex)


      
     // Append the data display div outside of the table
     $("#main_div").append(dataDisplayDiv);
    }

    function remove_row(el) {
       var tr_length = $("#debtAccVoucher #debitvoucher tr").length;
       if(tr_length > 1){
            var tr = $(el).closest("tr").remove();
       }else{
            alert("At least one row required");
       }		
     }

    function total_amount(){
        //  alert("Sa");
        total = 0;
        $(".total_price").each(function(i,e){
            total += parseInt($(e).val());
        });
        $("#grandTotal").val(total);
    }
    $(document).ready(function(){
        
 

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

       
    // Selecte Acoount Receipt open show data
    


   function get_code(el)
   {
       
        var id = $(el).val();
    
        var contactId = $(el).closest("tr").find("option:selected").data("contact_id");
        var row = $(el).closest("tr");
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
        
        
        get_data(contactId,row);
        
  
   }
   
   function get_data(contactId, row) {
  
    var rowNumber = row.index() + 1; 
  
    $.ajax({
        method: "get",
        url: "/invoice/fetch/" + contactId,
        success: function(result) {
            var dataDisplayDiv = $(`#data_display_${rowNumber}`);
            dataDisplayDiv.html(result);
            // row.find(".customer-table").html(result);
        }
    });
}


function autoAllocate(el) {
            
    var parentId = $(el).closest("div").parent().attr("id");
    var totalAmount = parseFloat($('#tr_' + parentId + ' .total_price').val());
    var receiptFields = $('#' + parentId + ' input[name="receipts[]"]');
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
                    receiptFields[i].value = totalAmount.toFixed(2);
                    totalAmount = 0;
                }
               
            }
        }
    }
}

    $(document).on('change','.reciept-checkbox',function(){
        var totalAmount = parseFloat(document.getElementsByName('txtAmount[]')[0].value);
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



    function income_tax(inputElement)
    {
        debugger;
    var $row = $(inputElement).closest('tr');
    var value = parseFloat($row.find('.icome_tax').val());
    var total_tax = parseFloat($row.find('.total_tax').val());
    var $income_tax = $row.find('.income_tax');
    if (!isNaN(value) && !isNaN(total_tax)) {
        var tax = (total_tax * value);
        var result = tax / 100;
        $income_tax.val(result);
    }
    }
    // 
    
    
</script>
@endsection