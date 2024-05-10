 <div style="float:right">
 <button type="button" class="btn btn-secondary allocate-button" onclick="autoAllocate(this)">Auto Allocate</button>
</div>
<table class="table table-bordered table-hover table-responsive" >
   <tr>
      <th class="text-right" >Trans Date</th>
      <th class="text-right" >Ref No</th>
      <th class="text-right" >Invoice Total (Inc tax)</th>
      <th class="text-right" >Invoice Taxes</th>
      <th class="text-right" > Due Amount</th>
      <th class="text-right" >Sale Tax (WHT)</th>
      <th class="text-right" >Income Tax (WHT)</th>
      <th class="text-right">Receipt </th>
      <th class="text-right" >Total</th>
      <th class="text-right" ></th>
   </tr>


   @foreach($data as $data)
  
   <tr>
      <td>{{$data->transaction_date}}  <input type="hidden" value="{{$data->transaction_date}}" name="date[]"></td>
      <td>{{$data->ref_no}} <input type="hidden" value="{{$data->ref_no}}" name="ref[]"></td>
      <th>{{ $data->final_total }} <input type="hidden" value="{{$data->final_total}}" name="inc_tax[]"></th>
      <th>{{($data->total_tax) + ($data->total_further_tax) }} <input type="hidden"  class='total_tax' value="{{($data->total_tax) + ($data->total_further_tax) }}" name="tax_name[]"></th>
      <th>{{ $data->final_total }} <input type="hidden" value="{{ $data->final_total }}" name="due_amount[]"></th>
      
      <th>
         <div class="inline-container"  style="display: flex">
            <div>
               <span class="input-group-addon">% 
               <input type="text"  name="percent[]" onkeyup="sale_tax(this)"  class="sale_tax" style="width: 40px;">
            </span>
            </div>
            <div>
               <span class="input-group-addon">PKR
               <input type="text"  name="pkr[]"   class="wht_tax" style="width: 50px;">
            </span>
            </div>
            <div>
              <select name="tax[]" class="d-none">
                   <option value="">Please select</option>
                   @foreach($Tax as $t)

                    <option value="{{$t->id}}">{{$t->name}}</option>
              
                   @endforeach
               </select>
            </div>
         </div>
      </th>
      <th>
         <div class="inline-container" style="display: flex">
            <div>
               <span class="input-group-addon">%
               <input type="text" name="income_percent[]" value="" onkeyup="income_tax(this)"  class="icome_tax" style="width: 40px;">
            </span>
            </div>
            <div>
               <span class="input-group-addon">PKR
               <input type="text" name="pkr_inc[]" class="income_tax" style="width: 50px;">
            </span>
            </div>
         </div>
      </th>
      <th>
         <div class="inline-container2"  >
            <div>
               <span class="input-group-addon">PKR
               <input type="text" name="receipts[]" class="receipt_data" style="width: 100px;">
            </span>
            </div>
         </div>
      </th>
      <th><input type="text" value="{{$data->final_total}}" readonly name="total[]">
      </th>
      <th><input type="checkbox" class="reciept-checkbox" name="paid[]">
      </th>
   </tr>
   @endforeach
</table>