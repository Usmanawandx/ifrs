@extends('layouts.app')



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
    
    .table th {
        white-space: nowrap; /* Prevent text from wrapping */
        overflow: hidden; /* Hide overflowing content */
        text-overflow: ellipsis; /* Show ellipsis (...) when content overflows */
    }

    /* Optional: Increase the width of the th element */
    .table th div {
        
      width: 353px;; /* Set your desired width */
    }
    .inline-container {
    display: inline-flex;
    vertical-align: text-bottom;
    }
    
    
    .input-group-addon {
        display: inline-block;
        width: auto; /* Optional: Adjust the width as needed */
    }


</style>

<!-- Main content -->
<section class="content">
        
        {!! Form::open(['url' => action('AccountController@save_invoice'), 'method' => 'post', 'id' => 'add_purchase_form', 'files' => true ]) !!}
        @component('components.widget', ['class' => 'box-primary'])
        @csrf
    	
    	      <div class="row">
            <div class="col-md-4">
                <!-- Field 1 -->
                <div class="form-group">
                    {!! Form::label('customer_name', 'Customer Name') !!}
                    <select name="customer_supplier customer_id" class="form-control select2 " multiple onchange="get_data(this)" required>
                        @foreach($contact as $c)
                           <option value="{{$c->id}}">{{$c->supplier_business_name}}</option>
                        @endforeach
                        
                    </select>
                </div>
            </div>
            <br>
            
              <div class="col-md-4">
                <!-- Field 2 -->
                <div class="form-group">
                    {!! Form::label('date', 'Receipt Date') !!}
                    {!! Form::date('receipt_date', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Field 2 -->
                <div class="form-group">
                    {!! Form::label('amount', 'Amount') !!}
                    {!! Form::number('amount', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-md-4">
                <!-- Field 3 -->
                <div class="form-group">
                    {!! Form::label('account head', 'Account Head') !!}
                        <select name="account_head" class="form-control select2" required>
                        <option value="" selected >Please select Account</option>
                        @foreach($Account_head as $a)
                           <option value="{{$a->id}}">{{$a->name}}</option>
                        @endforeach
                        
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <!-- Field 4 -->
                
                <div class="form-group">
                    {!! Form::label('payment_type', 'Payment Type') !!}
                        <select name="payment_type" class="form-control select2 payment_type_change">
                        <option value="" selected >Please select Payment Type</option>
                        <option value="cheque">Cheaque</option>
                        <option value="cash">Cash</option>
                        <option value="online">Online</option>
               
                        
                    </select>
                </div>
            </div>
            
            <div class="col-md-4 payment_type" style="display:none">
                <!-- Field 2 -->
                <div class="form-group">
                    {!! Form::label('cheque', 'chque no') !!}
                    <input type="text" name="cheque_no" class="form-control cheque">
            </div>
            </div>
            <div class="col-md-4 chquet_date" style="display:none">
                <!-- Field 2 -->
                <div class="form-group">
                    {!! Form::label('cheque', 'chque date') !!}
                    <input type="date" name="cheque_date" class="form-control cheque">
                 </div>   
            </div>
        </div>
            
            
            
            
            

        

	

        <br>
       
    	<div id="customerTables">
            
        </div>
      
        <div style="float:right">
            
        <button type="submit" class="btn btn-primary">Save</button>
    
        </div>
              </div>
    	@endcomponent

    	

{!! Form::close() !!}


<br>


</section>
<!-- /.content -->

@endsection

@section('javascript')

<script>

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



  function get_data(el)
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
    function autoAllocate(row) {
    var totalAmountInput = row.querySelector('input[name="txtAmount[]"]');
    var receiptFields = row.querySelectorAll('input[name="receipts[]"]');
    var totalFields = row.querySelectorAll('input[name="total[]"]');
    var totalDueFields = row.querySelectorAll('input[name="due_amount[]"]');
    var totalAmount = parseFloat(totalAmountInput.value);
    var numberOfFields = receiptFields.length;

    if (numberOfFields > 0 && totalAmount > 0) {
        var totalPaid = 0;

        receiptFields.forEach(function (receiptField, i) {
            if (receiptField.value > 0) {
                totalPaid += parseFloat(receiptField.value);
            }
        });

        totalAmount = totalAmount - totalPaid;

        var totalDues = 0;
        totalDueFields.forEach(function (dueField) {
            totalDues += parseFloat(dueField.value);
        });

        if (totalAmount <= totalDues) {
            receiptFields.forEach(function (receiptField, i) {
                var due = parseFloat(totalDueFields[i].value);
                if (due >= totalAmount && totalAmount > 0) {
                    receiptField.value = totalAmount.toFixed(2);
                    totalAmount = 0;
                } else if (due < totalAmount) {
                    receiptField.value = due.toFixed(2);
                    totalAmount = totalAmount - due;
                }
            });
        } else {
            receiptFields.forEach(function (receiptField) {
                receiptField.value = parseFloat(0);
            });
            row.querySelectorAll('.reciept-checkbox').forEach(function (checkbox) {
                checkbox.checked = false;
            });
            row.querySelectorAll('.reciept-checkbox').forEach(function (checkbox) {
                checkbox.removeAttribute('readonly');
            });
            alert("You entered a larger amount than your dues. Please enter a valid amount!");
        }
    } else {
        receiptFields.forEach(function (receiptField) {
            receiptField.value = parseFloat(0);
        });
        row.querySelectorAll('.reciept-checkbox').forEach(function (checkbox) {
            checkbox.checked = false;
        });
        row.querySelectorAll('.reciept-checkbox').forEach(function (checkbox) {
            checkbox.removeAttribute('readonly');
        });
        alert("Please enter a valid amount!");
    }
}


    $(document).on('change','.reciept-checkbox',function(){
        var totalAmount = parseFloat(document.getElementsByName('amount')[0].value);
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
</script>

@endsection