 <div style="float:right">
 <button type="button" class="btn btn-secondary allocate-button" onclick="autoAllocate(this)">Auto Allocate</button>
</div>

{{-- <button type="button" class="btn btn-secondary allocate-button" onclick="autoAllocate(this)">Auto Allocate</button> --}}
{{-- <label>Advance Payment</label><input type="text" name="advance_payment"  /> --}}
<table class="table table-bordered table-hover " >
   <tr>
      <th class="text-right" >Trans Date</th>
      <th class="text-right" >Ref No</th>
      <th class="text-right" >Invoice Total (Inc tax)</th>
      <th class="text-right" >Invoice Taxes</th>
      <th class="text-right" > Due Amount</th>
      <th class="text-right hide" >Sale Tax (WHT)</th>
      <th class="text-right hide" >Income Tax (WHT)</th>
      <th class="text-right">Receipt </th>
      <th class="text-right" >Total</th>
      <th class="text-right" ></th>
   </tr>


   @foreach($transaction as $data)


   {{-- @if(($data->final_total - $data->aging_amount) != 0 )  --}}
   
   <tr>
   <td>{{@format_date($data->transaction_date)}}  <input type="hidden" value="{{@format_date($data->transaction_date)}}" name="date[]">
         <input type="hidden" value="{{$param}}" name="voucher_no[]">
         <input type="hidden" value="{{$data->t_id}}" name="transaction_id[]">
         <input type="hidden" value="{{$data->rec_id}}" name="rec_id[]">
         
      </td>
      <td>{{$data->ref_no}} <input type="hidden" value="{{$data->ref_no}}" name="ref[]"></td>
      <th>{{ number_format($data->final_total,2) }} <input type="hidden" value="{{$data->final_total}}" name="inc_tax[]"></th>
      <th>{{($data->total_tax) + ($data->total_further_tax) }} <input type="hidden"  class='total_tax' value="{{($data->total_tax) + ($data->total_further_tax) }}" name="tax_name[]"></th>
      <th>{{ number_format(($data->final_total) - ($data->aging_amount),2)}} <input type="hidden" value="{{ ($data->final_total) - ($data->aging_amount) }}" name="due_amount[]" class="due_amount"></th>
      
      
      <th>
         <div class="inline-container2"  >
            <div>
               <span class="input-group-addon">PKR
               <input type="text" name="receipts[]"  class="receipt_data" value="{{$data->aging_amount}}" style="width: 100px;">
            </span>
            </div>
         </div>
      </th>
      <th><input type="text" value="{{number_format(($data->final_total)-($data->aging_amount) ,2)}}" readonly name="total[]">
      </th>
      <th>
         <input type="checkbox" class="reciept-checkbox" name="paid[]" {{ !empty($data->aging_amount) ? 'checked' : '' }}>
      </th>
   </tr>

   @if($data->aging_amount)
   <tr>
   <td>{{@format_date($data->transaction_date)}}  <input type="hidden" value="{{@format_date($data->transaction_date)}}" name="date[]">
         <input type="hidden" value="{{$param}}" name="voucher_no[]">
         <input type="hidden" value="{{$data->t_id}}" name="transaction_id[]">
         <input type="hidden" value="{{$data->rec_id}}" name="rec_id[]">
         
      </td>
      <td>{{$data->ref_no}} <input type="hidden" value="{{$data->ref_no}}" name="ref[]"></td>
      <th>{{ number_format($data->final_total,2) }} <input type="hidden" value="{{$data->final_total}}" name="inc_tax[]"></th>
      <th>{{($data->total_tax) + ($data->total_further_tax) }} <input type="hidden"  class='total_tax' value="{{($data->total_tax) + ($data->total_further_tax) }}" name="tax_name[]"></th>
      <th>{{ number_format(($data->final_total) - ($data->aging_amount),2)}} <input type="hidden" value="{{ ($data->final_total) - ($data->aging_amount) }}" name="due_amount[]" class="due_amount"></th>
      
      
      <th>
         <div class="inline-container2"  >
            <div>
               <span class="input-group-addon">PKR
               <input type="text" name="receipts[]"  class="receipt_data" value="{{$data->aging_amount}}" style="width: 100px;">
            </span>
            </div>
         </div>
      </th>
      <th><input type="text" value="{{number_format(($data->final_total)-($data->aging_amount) ,2)}}" readonly name="total[]">
      </th>
      <th>
         <input type="checkbox" class="reciept-checkbox" name="paid[]" {{ !empty($data->aging_amount) ? 'checked' : '' }}>
      </th>
   </tr>

   @endif

   @endforeach
   
</table>