<style>



  b{  
    font-size: 18px;
  }
  p{  
    font-size: 16px;
    margin: 0px 0 -10px!important;
  }

 
</style>
<div class="modal-dialog modal-xl" role="document">
  <div class="modal-content" id="modal-content">
    <div style="background-color: #FFF !important; max-width:100% !important;" class="photo">
    {{-- <div class="modal-header">
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    {{-- <h4 class="modal-title" id="modalTitle">Sale Order (<b>@if($sell->type == 'sales_order') @lang('restaurant.order_no') @else @lang('sale.invoice_no') @endif :</b> {{ $sell->invoice_no }})
    </h4> --}}
</div>  
<div class="row">
  <div class="col-xs-8 col-md-8 col-xs-8">
  </br>
</br>
</br>
  @if($sell->type == 'sales_order')
<span class="modal-title" id="modalTitle" style=" margin: 37px;font-size: 46px;">Sale Order</span>
@elseif($sell->type == 'delivery_note')
<span class="modal-title" id="modalTitle" style=" margin: 37px;font-size: 46px;">Delivery Note</span>
@elseif($sell->type == 'sale_return_invoice')
<span class="modal-title" id="modalTitle" style=" margin: 37px;font-size: 46px;">Sale return Invoice </span>
@elseif($sell->type == 'sale_invoice')
<span class="modal-title" id="modalTitle" style=" margin: 37px;font-size: 46px;">Sale Invoice</span>
@endif
  </div>
  <div class="col-xs-4">
    @if(!empty(Session::get('business.logo')))
                  <img src="{{ asset( 'uploads/business_logos/' . Session::get('business.logo') ) }}" width="200" height="150" alt="Logo">
                @endif
      </div>
</div>
<div class="modal-body " >
    {{-- <div class="row">
      <div class="col-xs-12">
          <p class="pull-right"><b>@lang('messages.date'):</b> {{ @format_date($sell->transaction_date) }}</p>
      </div>
    </div> --}}
    <div class="row main-rol-headdata" style="margin: 16px">
      @php
        $custom_labels = json_decode(session('business.custom_labels'), true);
        $export_custom_fields = [];
        if (!empty($sell->is_export) && !empty($sell->export_custom_fields_info)) {
            $export_custom_fields = $sell->export_custom_fields_info;
        }
      @endphp
      <div class=" col-sm-8 col-md-8 col-xs-8 first-col">
        @if($sell->type == 'sales_order' || $sell->type == 'delivery_note' || $sell->type == 'sale_invoice' || $sell->type == 'sale_return_invoice')
        <b style="font-size: "><span>{{ Session::get('business.name') }}</span> </b>
      </br>
      <p>PHONE:{{Auth::user()->contact_number}}<p>
      </br>
       <p>YOUR NTN:</p>
      </br>
      <p>YOUR CNIC:</p>
      </br>
      <p>YOUR STRN:</p>

    </br>

    <h3><b>BILLED TO</b></h3>
    <p>
      {{ $sell->contact->supplier_business_name }}</p>
    </br>

      <p> {{$sell->contact->contact_addresses[0]->address_line??''}}</p>
    </br>

    <p>NTN:{{$sell->contact->ntn??''}}</p>

     </br>

    <p>STRN:{{$sell->contact->cnic??''}}</p>

    </br>

    <p>CNIC:{{$sell->contact->ntn_cnic_no??''}}</p>
    
        @else
      
      </br>
        <b>@if($sell->type == 'sales_order') {{ __('restaurant.order_no') }} @else {{ __('sale.invoice_no') }} @endif:</b> #{{ $sell->invoice_no }}<br>
        <b>{{ __('sale.status') }}:</b> 
          @if($sell->status == 'draft' && $sell->is_quotation == 1)
            {{ __('lang_v1.quotation') }}
          @else
            {{ $statuses[$sell->status] ?? __('sale.' . $sell->status) }}
          @endif
        <br>
    
        @endif

      </div>
       {{-- Headings --}}
      @if($sell->type == 'sales_order' || $sell->type == 'delivery_note' || $sell->type == 'sale_invoice' || $sell->type == 'sale_return_invoice' )
      <div class="col-sm-2 col-md-2 col-xs-2">
       @if($sell->type == 'delivery_note')
        <p>Deliverty Note No:</p>
        @elseif($sell->type == 'sale_invoice')
        <p>Invoice No:</p>
        @else
        <p>Order No:</p>
        @endif
      </br>
      <p>Customer Id:</p>
      @if($sell->type == 'sales_order')
      </br>
      <p>Order Date:</p>
      @endif
      </br>
      <p>Delvery Date:</p>
      @if($sell->type == 'sales_order' || $sell->type == 'sale_invoice')
      </br>
      <p>Terms:</p>
      @else
    </br>
    @if($sell->type != 'sale_invoice' || $sell->type != 'sale_return_invoice')
    <p>Sale Order No:</p>
    @endif
      @endif
    @if($sell->type == 'delivery_note')
    </br>
    <p>Vehicle No:</p>
    @endif
      </br>
      <p>Remarks:</p> 
      
    @if($sell->type != 'sale_invoice')
      </br>
      <h3><b>Shipped TO</b></h3>
      <p>Shipping Address </p>
      </br>
      <p>Phone:{{$sell->contact->mobile}}</p>
    </br>
    <p>Email:{{$sell->contact->email}}</p>
    @endif
      </div>
   
      {{-- Date --}}
      <div class="col-sm-2 col-md-2 col-xs-2">
        <p>&nbsp;{{$sell->ref_no}}</p>
      </br>
        <p>&nbsp;{{$sell->contact->contact_id}}</p>
        @if($sell->type == 'sales_order')
      </br>
      <p> &nbsp;{{$sell->transaction_date}}</p>
      @endif
      </br>
      <p>  &nbsp;{{$sell->delivery_date}}</p>
      @if($sell->type == 'sales_order' || $sell->type == 'sale_invoice')
      </br>
      <p>   &nbsp;{{$sell->pay_type}}</p>
      @else
    </br>
    <p>   &nbsp;{{$sell->sale_order_no}}</p>
      @endif

      @if($sell->type == 'delivery_note')
    </br>
    <p>&nbsp;{{$sell->vehicle->vhicle_number??''}}</p>
    @endif
      </br>
      <p>{{$sell->additional_notes}}</p>
      </br>
      


      </div>



      @else


      <div class="@if(!empty($export_custom_fields)) col-sm-3 col-md-3 col-xs-3 @else col-sm-4 col-md-4 col-xs-4 @endif">
        @if(!empty($sell->contact->supplier_business_name))
          {{ $sell->contact->supplier_business_name }}<br>
        @endif
        <b>{{ __('sale.customer_name') }}:</b> {{ $sell->contact->name }}<br>
        <b>{{ __('business.address') }}:</b><br>
        @if(!empty($sell->billing_address()))
          {{$sell->billing_address()}}
        @else
          {!! $sell->contact->contact_address !!}
          @if($sell->contact->mobile)
          <br>
              {{__('contact.mobile')}}: {{ $sell->contact->mobile }}
          @endif
          @if($sell->contact->alternate_number)
          <br>
              {{__('contact.alternate_contact_number')}}: {{ $sell->contact->alternate_number }}
          @endif
          @if($sell->contact->landline)
            <br>
              {{__('contact.landline')}}: {{ $sell->contact->landline }}
          @endif
     
        
      </div>
      @endif
      <div class="@if(!empty($export_custom_fields)) col-sm-3 col-md-3 col-xs-3 @else col-sm-4 col-md-4 col-xs-4 @endif">
      @if(in_array('tables' ,$enabled_modules))
         <strong>@lang('restaurant.table'):</strong>
          {{$sell->table->name ?? ''}}<br>
      @endif
      @if(in_array('service_staff' ,$enabled_modules))
          <strong>@lang('restaurant.service_staff'):</strong>
          {{$sell->service_staff->user_full_name ?? ''}}<br>
      @endif

      <strong>@lang('sale.shipping'):</strong>
      <span class="label @if(!empty($shipping_status_colors[$sell->shipping_status])) {{$shipping_status_colors[$sell->shipping_status]}} @else {{'bg-gray'}} @endif">{{$shipping_statuses[$sell->shipping_status] ?? '' }}</span><br>
      @if(!empty($sell->shipping_address()))
        {{$sell->shipping_address()}}
      @else
        {{$sell->shipping_address ?? '--'}}
      @endif
      @if(!empty($sell->delivered_to))
        <br><strong>@lang('lang_v1.delivered_to'): </strong> {{$sell->delivered_to}}
      @endif
      @if(!empty($sell->shipping_custom_field_1))
        <br><strong>{{$custom_labels['shipping']['custom_field_1'] ?? ''}}: </strong> {{$sell->shipping_custom_field_1}}
      @endif
      @if(!empty($sell->shipping_custom_field_2))
        <br><strong>{{$custom_labels['shipping']['custom_field_2'] ?? ''}}: </strong> {{$sell->shipping_custom_field_2}}
      @endif
      @if(!empty($sell->shipping_custom_field_3))
        <br><strong>{{$custom_labels['shipping']['custom_field_3'] ?? ''}}: </strong> {{$sell->shipping_custom_field_3}}
      @endif
      @if(!empty($sell->shipping_custom_field_4))
        <br><strong>{{$custom_labels['shipping']['custom_field_4'] ?? ''}}: </strong> {{$sell->shipping_custom_field_4}}
      @endif
      @if(!empty($sell->shipping_custom_field_5))
        <br><strong>{{$custom_labels['shipping']['custom_field_5'] ?? ''}}: </strong> {{$sell->shipping_custom_field_5}}
      @endif
      @php
        $medias = $sell->media->where('model_media_type', 'shipping_document')->all();
      @endphp
      @if(count($medias))
        @include('sell.partials.media_table', ['medias' => $medias])
      @endif

      @if(in_array('types_of_service' ,$enabled_modules))
        @if(!empty($sell->types_of_service))
          <strong>@lang('lang_v1.types_of_service'):</strong>
          {{$sell->types_of_service->name}}<br>
        @endif
        @if(!empty($sell->types_of_service->enable_custom_fields))
          <strong>{{ $custom_labels['types_of_service']['custom_field_1'] ?? __('lang_v1.service_custom_field_1' )}}:</strong>
          {{$sell->service_custom_field_1}}<br>
          <strong>{{ $custom_labels['types_of_service']['custom_field_2'] ?? __('lang_v1.service_custom_field_2' )}}:</strong>
          {{$sell->service_custom_field_2}}<br>
          <strong>{{ $custom_labels['types_of_service']['custom_field_3'] ?? __('lang_v1.service_custom_field_3' )}}:</strong>
          {{$sell->service_custom_field_3}}<br>
          <strong>{{ $custom_labels['types_of_service']['custom_field_4'] ?? __('lang_v1.service_custom_field_4' )}}:</strong>
          {{$sell->service_custom_field_4}}<br>
          <strong>{{ $custom_labels['types_of_service']['custom_field_5'] ?? __('lang_v1.custom_field', ['number' => 5])}}:</strong>
          {{$sell->service_custom_field_5}}<br>
          <strong>{{ $custom_labels['types_of_service']['custom_field_6'] ?? __('lang_v1.custom_field', ['number' => 6])}}:</strong>
          {{$sell->service_custom_field_6}}
        @endif
      @endif
      </div>
      @endif
      @if(!empty($export_custom_fields))
          <div class="col-sm-3 col-md-3 col-xs-3">
                @foreach($export_custom_fields as $label => $value)
                    <strong>
                        @php
                            $export_label = __('lang_v1.export_custom_field1');
                            if ($label == 'export_custom_field_1') {
                                $export_label =__('lang_v1.export_custom_field1');
                            } elseif ($label == 'export_custom_field_2') {
                                $export_label = __('lang_v1.export_custom_field2');
                            } elseif ($label == 'export_custom_field_3') {
                                $export_label = __('lang_v1.export_custom_field3');
                            } elseif ($label == 'export_custom_field_4') {
                                $export_label = __('lang_v1.export_custom_field4');
                            } elseif ($label == 'export_custom_field_5') {
                                $export_label = __('lang_v1.export_custom_field5');
                            } elseif ($label == 'export_custom_field_6') {
                                $export_label = __('lang_v1.export_custom_field6');
                            }
                        @endphp

                        {{$export_label}}
                        :
                    </strong> {{$value ?? ''}} <br>
                @endforeach
          </div>
      @endif
    </div>
    <br>


   
    <div class="row">
 

      <div class="col-sm-12 col-xs-12 col-md-12 col-xs-12 table_space">
        <div class="table-responsive">
          @include('sale_pos.partials.sale_line_details')
        </div>
      </div>
    </div>



    <div class="spane_d">
    <div class="row">
      @php
        $total_paid = 0;
      @endphp

      <div class="col-md-6 col-sm-12 col-xs-12 col-xs-6">
        <h3 style="color: #000080;">Thank You For Your Business</h3>
      </div>
 
      <div class="col-md-6 col-sm-12 col-xs-12 @if($sell->type == 'sales_order') col-md-offset-8 col-xs-8 @endif" style="margin-left: 74%;">
        <div class="table-responsive amount_p" style="width: 50%;margin-top: -60px;">
          @if($sell->type=="sales_order" || $sell->type == 'sale_invoice' || $sell->type == 'sale_return_invoice')
          <table class="table bg-gray ">
            <tr>
              <th>{{ __('Sub Total') }}: </th>
              <td><span class="display_currency pull-right" data-currency_symbol="true">{{ $sell->total_before_tax }}</span></td>
            </tr>
            <tr>
              <th>{{ __('Tax/Vat') }}:</th>
              <td><div class="pull-right"><span class="display_currency" @if( $sell->discount_type == 'fixed') data-currency_symbol="true" @endif>{{ $sell->discount_amount }}</span> @if( $sell->discount_type == 'percentage') {{ '%'}} @endif</span></div></td>
            </tr>
            <tr>
              <th>{{ __('sale.discount') }}:</th>
            
              <td><div class="pull-right"><span class="display_currency" @if( $sell->discount_type == 'fixed') data-currency_symbol="true" @endif>{{ $sell->discount_amount }}</span> @if( $sell->discount_type == 'percentage') {{ '%'}} @endif</span></div></td>
            </tr>
            
            </tr>
            @if(!empty($line_taxes))
            <tr>
              <th>{{ __('lang_v1.line_taxes') }}:</th>
              <td></td>
              <td class="text-right">
                @if(!empty($line_taxes))
                  @foreach($line_taxes as $k => $v)
                    <strong><small>{{$k}}</small></strong> - <span class="display_currency pull-right" data-currency_symbol="true">{{ $v }}</span><br>
                  @endforeach
                @else
                0.00
                @endif
              </td>
            </tr>
            @endif
          
        
          </table>
          @endif

      
       
        </div>
      
        
      </div>
      <div class="col-md-4 col-sm-4 col-xs-4 col-xs-4 pull-right">
        <div style="background-color: #000080;" class="total_am"><h3 style="color: #FFF">Total <p class="pull-right" style="font-size: 20px">{{ $amount =$sell->total_before_tax - $sell->discount_amount  }}</p></h3></div>
        @if($sell->type == 'delivery_note')
        <div>
       <h4>Gross Weight:<p class="pull-right" style="font-size: 20px">{{ $sell->gross_weight??'0' }}</h4></p>
       <h4>Net Weight:<p class="pull-right" style="font-size: 20px">{{ $sell->net_weight ??'0'}}</p></h4>
        </div>
        @endif 
      
      
      </div>

       


      @if($sell->type=="sales_order" || $sell->type == 'sale_invoice' || $sell->type == 'sale_return_invoice')
      <div class="col-md-6 col-sm-12 col-xs-12 col-xs-6">
     
        <h3>AMOUNT IN WORDS</h3>
        <?php echo AmountInWords($amount)?>
      
      </div>
      @endif
    </div>
    <h3>Terms And Condition</h3>
    <p>{!! $sell->tandc_title !!}</p>

    <hr>
    </div>
  </div>
  <div class="modal-footer text-center no-print">
    @if($sell->type == 'delivery_note')
    <a class="btn btn-primary " href="{{action('SellController@convert_dn_to_si', [$sell->id])}}">Convert To SI</a>
    <a class="btn btn-primary " id="edit_btn" href="{{action('SellController@deliverynoteedit', [$sell->id])}}">Edit</a>
    @elseif($sell->type == 'sales_order')
    <a class="btn btn-primary " id="edit_btn" href="{{action('SellController@edit', [$sell->id])}}">Edit</a>
    <a class="btn btn-primary " href="{{action('SellController@convert_so_to_dn', [$sell->id])}}">Convert To SDN</a>
    @elseif($sell->type == 'milling')
    <a class="btn btn-primary " id="edit_btn" href="{{action('SellController@Millingedit', [$sell->id])}}">Edit</a>
    @elseif($sell->type == 'sale_invoice')
    <a class="btn btn-primary " id="edit_btn" href="{{action('SellController@saleinvoiceedit', [$sell->id])}}">Edit</a>
    @elseif($sell->type == 'sale_return_invoice')
    <a class="btn btn-primary " id="edit_btn" href="{{action('SellController@salereturnedit', [$sell->id])}}">Edit</a>
    @endif
   
    
    {{-- <button onclick="window.print()">Print this page</button> --}}


    @can('print_invoice')

    <input type="button" class="print-invoice btn btn-primary" onclick="printDiv('modal-content')" value="Print" />
      {{-- <a href="#" class="print-invoice btn btn-primary" data-href="{{route('sell.printInvoice', [$sell->id])}}"><i class="fa fa-print" aria-hidden="true"></i> @lang("lang_v1.print_invoice")</a> --}}

      @endcan
    <button style="color: white"  class="btn btn-primary  btn-flat d_button" >Download JPEG</button>

      <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>
    </div>
  </div>

</div>

<?php
function AmountInWords(float $amount)
{
   $amount_after_decimal = round($amount - ($num = floor($amount)), 2) * 100;
   // Check if there is any number after decimal
   $amt_hundred = null;
   $count_length = strlen($num);
   $x = 0;
   $string = array();
   $change_words = array(0 => '', 1 => 'One', 2 => 'Two',
     3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
     7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
     10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
     13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
     16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
     19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
     40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
     70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
    $here_digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
    while( $x < $count_length ) {
      $get_divider = ($x == 2) ? 10 : 100;
      $amount = floor($num % $get_divider);
      $num = floor($num / $get_divider);
      $x += $get_divider == 10 ? 1 : 2;
      if ($amount) {
       $add_plural = (($counter = count($string)) && $amount > 9) ? 's' : null;
       $amt_hundred = ($counter == 1 && $string[0]) ? ' and ' : null;
       $string [] = ($amount < 21) ? $change_words[$amount].' '. $here_digits[$counter]. $add_plural.' 
       '.$amt_hundred:$change_words[floor($amount / 10) * 10].' '.$change_words[$amount % 10]. ' 
       '.$here_digits[$counter].$add_plural.' '.$amt_hundred;
        }
   else $string[] = null;
   }
   $implode_to_Rupees = implode('', array_reverse($string));
   $get_paise = ($amount_after_decimal > 0) ? "And " . ($change_words[$amount_after_decimal / 10] . " 
   " . $change_words[$amount_after_decimal % 10]) . ' Paise' : '';
   return ($implode_to_Rupees ? $implode_to_Rupees . 'Rupees ' : '') . $get_paise;
}
?>
<script src="{{ asset('js/html2canvas.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">


function printDiv(divName) {
    //  var printContents = document.getElementById(divName).innerHTML;
    //  var originalContents = document.body.innerHTML;

    //  document.body.innerHTML = printContents;

    //  window.print();

    //  document.body.innerHTML = originalContents;

    $('#modal-content').printThis();
}

  $(document).ready(function(){
    var element = $('div.modal-xl');
    __currency_convert_recursively(element);

 



  });
  $("body").on("keydown", function(e){
  if(e.altKey && e.which == 69) {
    var href = $("#edit_btn").attr("href");
    window.location.href = href;
  }
});
var invoice = document.getElementsByClassName("photo")[0];
var d_btn = document.getElementsByClassName("d_button")[0];
d_btn.addEventListener("click",()=>{
domtoimage.toJpeg(invoice).then((data)=>{
var link  = document.createElement("a");
var name = '<?php echo $sell->ref_no ?>';

link.download = name+".jpeg";
link.href = data;
link.click();
});
});
</script>
