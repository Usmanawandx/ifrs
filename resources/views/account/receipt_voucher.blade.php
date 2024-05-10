@extends('layouts.app')

@if(request('type') == 'cash_received_voucher')
    @section('title', 'Cash Received Voucher')
@else
   @section('title', __('Reciept Voucher'))
@endif

@section('content')
<style>
    .fields_tax{
    height: 27px;
    width: 45px;
    }
    
     .fields_tax1{
    height: 27px;
    width: 70px;
    }
    
    .fields_tax2{
    height: 27px;
    width: 137px;
    }
    
    .table_part th {
        white-space: nowrap; /* Prevent text from wrapping */
        overflow: hidden; /* Hide overflowing content */
        text-overflow: ellipsis; /* Show ellipsis (...) when content overflows */
    }

    /* Optional: Increase the width of the th element */
    .table_part th div {
        
      width: 353px;; /* Set your desired width */
    }
    .inline-container {
    display: inline-flex;
    vertical-align: text-bottom;
    }
    
    
    .input-group-addons {
        display: inline-block;
        width: auto; /* Optional: Adjust the width as needed */
    }


</style>

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
        
           {!! Form::open(['method' => 'post', 'id' => 'add_purchase_form', 'files' => true ]) !!}
        
    	
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
					<div class="input-group">
						<span class="input-group-addon">
							 Customer
						</span>
						<select name="customer_supplier customer_id" class="form-control select2 " multiple onchange="get_invoice(this)" required>
                        @foreach($contact as $c)
                           <option value="{{$c->id}}">{{$c->supplier_business_name}}</option>
                        @endforeach
                      </select>
					</div>
					<div> <span class="balance_top"></span> </div>
				</div>
			</div>
		
		    <div class="col-md-6">
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon">
							Transaction Date
						</span>
						<input required class="form-control" type="date" value="<?php echo date("Y-m-d"); ?>" name="date">
					</div>
				</div>
			</div>
				<br>	<br>
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
           <table class="table table-bordered table-hover" id="debtAccVoucher">
              <thead>
                 <tr>
                    <th class="text-center">Action</th>
                    <th class="text-center">Code</th>
                    <th class="text-center">Account Name<i class="text-danger">*</i></th>
                    <th class="text-center">Document No<i class="text-danger">*</i></th>
                    <th class="text-center">Remarks<i class="text-danger">*</i></th>
                    <th class="text-center">Amount<i class="text-danger">*</i></th>
                 </tr>
              </thead>
              <tbody id="debitvoucher">
                 <tr>
                    <td>
                       <button class="btn btn-danger red" type="button" value="Delete" onclick="remove_row(this)"><i class="fa fa-trash"></i></button>
                    </td>
                    <td><input type="text" name="txtCode[]" value="" class="form-control acc_code" id="txtCode_1" readonly=""></td>
                    <td class="" width="200p">
                       <select class="form-control select2" name="debit_head[]" required onchange="get_code(this)">
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
                     <td><a id="add_more" class="btn btn-info" name="add_more" onclick="add_row(this)"><i class="fa fa-plus"></i></a></td>
                    <td>
                    </td>
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
        
        	<div id="customerTables">
            
        </div>
        
        
        
        
                        
                <input type="submit" value="Save" class="btn btn-primary">
                
                
                
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
   
   

    // function add_row(el){
    //     var tr = $("#debtAccVoucher #debitvoucher tr:last").clone();
    //     tr.find('input').val('');
    //     $("#debtAccVoucher #debitvoucher tr:last").after(tr);
    //     // $('[name="debit_head[]"]').select2('destroy');
    //     // $('[name="debit_head[]"]').select2();
    // }


    
    function add_row(el) { 
      var tr = $("#debtAccVoucher #debitvoucher tr:last");
      var clone = tr.clone();
      clone.find('input').val('');
      clone.find('.select2-container').remove();
      tr.after(clone);
      clone.find('.select2').select2();
      clone.find('.total_price').val('0');
      clone.find(".balance").html('');
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
    
    
    $(document).on('change','.payment_type_change',function(){
       var selectedPaymentType = $(this).val();
    if (selectedPaymentType === 'cheque') {
        $('.payment_type').show(); 
        $('.chquet_date').show(); 
    } else {
        $('.payment_type').hide();
        $('.chquet_date').hide();
        
    }
    })



  function get_invoice(el)
   {
        var selectedCustomers = $(el).val();
        
        // Initialize an empty array to store customer tables
        var customerTables = [];
        
        // Make an AJAX request for each selected customer
        selectedCustomers.forEach(function (customer_id) {
            $.ajax({
                method: "get",
                url: "/invoice/fetch/" + customer_id,
                success: function(result) {
                    // Add the HTML table for this customer to the array
                    customerTables.push(result);
                    
                    // Check if all AJAX requests are complete
                    if (customerTables.length === selectedCustomers.length) {
                        // Combine all tables and display them
                        $('#customerTables').html(customerTables.join(''));
                    }
                }
            });
        });
    }

    function autoAllocate() {
        var totalAmount = parseFloat(document.getElementsByName('txtAmount[]')[0].value);
        var receiptFields = document.getElementsByName('receipts[]');
         var totalFields = document.getElementsByName('total[]');
         
        
        var numberOfFields = receiptFields.length;

        if (numberOfFields > 0 && totalAmount > 0) {
            var amountPerField = totalAmount / numberOfFields;

            for (var i = 0; i < numberOfFields; i++) {
                receiptFields[i].value = amountPerField.toFixed(2);
                
            var existingTotal = parseFloat(totalFields[i].value) || 0;
            var updatedTotal = existingTotal + parseFloat(receiptFields[i].value);
            totalFields[i].value = updatedTotal.toFixed(2);
            }
        }
    }

    
    
    
    
</script>
@endsection