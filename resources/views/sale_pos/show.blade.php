<style>
   b{  
   font-size: 12px;
   }
   p{  
   font-size: 12px;
   margin: 0px 0 -10px!important;
   }
   .modal-title{
       color: #1b5596;
   }
   p{  
    font-size: 12px;
   margin: 7px 0 -14px!important;
  }

  .opacity_hide{
    opacity: 0 !important;

  }

   
   table td,table th{
    font-size: 10px;  
    /*padding: 3px;*/ 
   }
   li{
       font-size:12px !important;
   }
   .prt_title{
       font-weight: 600;
       text-align: right !important;
   }
  
   .table.bg-gray.main tbody tr td.item_delivry_qty,
   .table.bg-gray.main tbody tr td.item_unit_price,
   .table.bg-gray.main tbody tr th.item_delivry_qty,
   .table.bg-gray.main tbody tr th.item_unit_price,
   .table.bg-gray.main tbody tr td.item_unit_total{
       text-align: right !important;
   }
   
   .table.bg-gray.main tbody tr th.item_unit_total{
       text-align: center !important;
   }
   
   #t_y{
       display: block; 
       color: #1b5596; 
       margin-top: 0px; 
       margin-bottom: 25px; 
   }
   #t_y b{
       font-size: 14px;
   }
   #amount_in_wrd{
       color: #1b5596;
       display: block;
       font-size: 14px;
       font-weight: 700;
   }
   
   .tr_price td{
       font-size:12px !important;
   }
   .ad_margin{
    margin-top: 100px
   }
</style>
<div class="modal-dialog modal-xl sales_modal" role="document" >
   <div class="modal-content invoice_modal" id="modal-content" style="padding: 0px 15px;">
      <div style="background-color: #FFF !important; max-width:100% !important;" class="photo">

         
         <div class="modal-body" style="padding-bottom:0px;">
             
            <div class="row">
               
                 
                <div class="col-xs-8 col-md-8 col-xs-8 {{ ($invoice_setting->logo == 1) ? '' : ''}}">
                  <div class="{{ ($invoice_setting->logo == 1) ? '' : ' hide'}}">
                    @if(!empty(Session::get('business.logo')))
                      <img src="{{ asset( 'uploads/business_logos/' . Session::get('business.logo') ) }}" width="150" height="100" alt="Logo">
                    @endif
                  </div>
                </div>
                <div class="col-xs-4 col-md-4 {{($invoice_setting->company == 1) ? '' : '' }}">
                
                    </br>
                    @if($sell->type == 'sales_order') 
                    <h1 class="modal-title prt_title" id="modalTitle" style="font-weight:600">Sale Order</h1>
                   @elseif($sell->type == 'delivery_note')
                    <h1 class="modal-title prt_title" id="modalTitle" style="font-weight:600">Delivery Note</h1>
                   @elseif($sell->type == 'sale_return_invoice')
                    <h1 class="modal-title prt_title" id="modalTitle" style="font-weight:600">Sale return Invoice </h1>
                   @elseif($sell->type == 'sale_invoice') 
                    <h1 class="modal-title invoicess prt_title" id="modalTitle" style="font-weight:600">Sale Invoice</h1>
                    <h3 class="modal-title commercial_invoice prt_title" id="modalTitle" style="font-weight:600">Commercial Invoice</h3>
                    <h1 class="modal-title sale_tax_invoice prt_title" id="modalTitle"  style="font-weight:600">Sales Tax </h1>
                    <h1 class="modal-title sale_tax_product prt_title" id="modalTitle" style="font-weight:600">Sales Tax</h1>
                   @endif
             
                </div>
            </div>
            
           <div class="row main-rol-headdata" >
      @php
        $custom_labels = json_decode(session('business.custom_labels'), true);
        $export_custom_fields = [];
        if (!empty($sell->is_export) && !empty($sell->export_custom_fields_info)) {
            $export_custom_fields = $sell->export_custom_fields_info;
        }
      @endphp
      <div class=" col-sm-8 col-md-8 col-xs-7 first-col">
        @if($sell->type == 'sales_order' || $sell->type == 'delivery_note' || $sell->type == 'sale_invoice' || $sell->type == 'sale_return_invoice')
        <div class="{{($invoice_setting->company == 1) ? '' : '' }}">
        <h3 style="font-size:22px;font-weight:bolder;"></h3>
        </div>
        <div class="{{($invoice_setting->company == 1) ? '' : 'hide' }}">
        <h3 style="font-size:22px;font-weight:bolder;">{{ Session::get('business.name') }} </h3>
        </div>
        <div>
          <div  class="{{($invoice_setting->address == 1) ? 'hide' : '' }}">
          </br>
          </br>
          </br>
          </br>
          </br>
          </br>

          </div>
    <div class="{{($invoice_setting->address == 1) ? '' : 'hide' }}">
      
         <p>{{ $sell->location->name.", ".$sell->location->city.", ".$sell->location->state.", ".$sell->location->country.", ".$sell->location->zip_code }} </p>
         <br>
       <p>PHONE : {{ $sell->location->mobile}}</p>
           
        </br>

      <p>NTN &nbsp;&nbsp;&nbsp;&nbsp; : {{ $sell->location->custom_field1}}</p>
       
      </br>
      <p>STRN &nbsp; : {{ $sell->location->custom_field2}}</p>
    </div>
        </div>
    </br>
    </br>

    <h4><b>BILLED TO</b></h4>
    <p style="font-size:16px;font-weight:bolder">
      {{ $sell->contact->supplier_business_name }}</p>
    </br>

      <p>Address : {{$sell->contact->contact_addresses[0]->address_line??''}}</p>
    </br>

    <p>NTN | Cnic : {{ $sell->contact->ntn_cnic_no??'' }}  &nbsp; &nbsp; STRN : {{ $sell->contact->gst_no??'' }}</p>

    </br>

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
        <p>Delivery No</p>
        @elseif($sell->type == 'sale_invoice')
        <p>Invoice No</p>
        @else
        <p>Order No</p>
        @endif
      </br>
      <p>Customer Id</p>
      @if($sell->type == 'sales_order')
      </br>
      <p>Order Date</p>
      @endif
      </br>
      @if($sell->type == 'sale_invoice')
      <p>Issue Date</p>
      </br>
      <p>Delivery Note No</p>
      @else
         <p>Delivery Date</p>
      @endif
      @if($sell->type == 'sales_order' || $sell->type == 'sale_invoice')
      </br>
      <p>Terms</p>
      @else
    </br>
    @if($sell->type != 'sale_invoice' || $sell->type != 'sale_return_invoice')
    <p>Sale Order No:</p>
    @endif
      @endif
    @if($sell->type == 'delivery_note')
    </br>
    <p>Vehicle No</p>
    @endif
      </br>
      <p>Remarks</p> 

    </div>
      
   
      {{-- Date --}}
      <div class="col-sm-2 col-md-2 col-xs-3">
        <p>:&nbsp;&nbsp;&nbsp;&nbsp;{{$sell->ref_no}}</p>
      </br>
        <p>:&nbsp;&nbsp;&nbsp;&nbsp;{{$sell->contact->contact_id}}</p>
        @if($sell->type == 'sales_order')
      </br>
      <p> :&nbsp;&nbsp;&nbsp;&nbsp;{{date("d-m-Y",strtotime($sell->transaction_date))}}</p>
      @endif
      </br>
        @if($sell->type == 'sale_invoice')
      <p>  :&nbsp;&nbsp;&nbsp;&nbsp;{{date_format($sell->created_at,"d-m-Y")}}</p>
      </br>
      <?php $delievry_no=DB::table('transactions')->where('id',$sell->delivery_note_no)->value('ref_no') ?>
      <p>:&nbsp;&nbsp;&nbsp;&nbsp; {{$delievry_no}}</p>
      @else
      <p>  :&nbsp;&nbsp;&nbsp;&nbsp;{{date("d-m-Y",strtotime($sell->transaction_date))}}</p>
      @endif
      @if($sell->type == 'sales_order' || $sell->type == 'sale_invoice')
      </br>
      <p>:&nbsp;&nbsp;&nbsp;&nbsp;{{$sell->pay_type }} {{ $sell->pay_term_number }} {{ $sell->pay_term_type }}</p>
      @else
    </br>
      <?php $sale_order_no = DB::table('transactions')
    ->where('id', $sell->sale_order_no)
    ->value('ref_no'); ?>
     <p>:&nbsp;&nbsp;&nbsp;&nbsp;{{$sale_order_no}}</p>
      @endif

      @if($sell->type == 'delivery_note')
    </br>
     <p>:&nbsp;&nbsp;&nbsp;&nbsp;{{$sell->vehicle->vhicle_number??''}}</p>
    @endif
      </br>
      <p>:&nbsp;&nbsp;&nbsp;&nbsp;{{$sell->additional_notes}}</p>
      </br>
      


      </div>
      
      <div class="col-sm-4 col-md- col-xs-4  shipped_to">
      </br>
      </br>
      <h4><b>Shipped TO</b></h4>
      
      <p>Address: {{$sell->contact->contact_addresses[0]->address_line??''}}</p>
    </br>
    <!--<p>Email:{{$sell->contact->email}}</p>-->
   
    </div>
    
         <br/>
    
        @if($sell->type == 'sales_order') 
        <div class="form-check no-print" style="display: inline-block;padding: 20px;">
          <input class="form-check-input" type="radio" name="rate_radio" value="with_rate" id="with_rate" checked>
          <label class="form-check-label" for="with_rate">
            With Rate
          </label>
     
          </br>
          <input class="form-check-input" type="radio" name="rate_radio" value="without_rate" id="without_rate">
          <label class="form-check-label" for="without_rate">
            Without Rate
          </label>
          
          </br>
           <input class="form-check-input" type="radio" name="rate_radio" value="cash_memo" id="cash_memo" >
          <label class="form-check-label" for="cash_memo">
            Cash Memo
          </label>
        </div>
        @endif
        
         @if($sell->type =='sale_invoice') 
        <div class="form-check no-print">
          <input class="form-check-input" type="radio" name="invoice_radio" value="invoice" id="invoice" checked>
          <label class="form-check-label" for="invoice">
            Invoice
          </label>
     
          </br>
          <input class="form-check-input" type="radio" name="invoice_radio" value="commercial" id="commercial">
          <label class="form-check-label" for="commercial">
            Commercial Invoice
          </label>
           
             </br>
          
          <input class="form-check-input" type="radio" name="invoice_radio" value="Sale_tax" id="Sale_tax" >
          <label class="form-check-label" for="Sale_tax">
            Sales Tax Invoice
          </label>
     
          </br>
          <input class="form-check-input" type="radio" name="invoice_radio" value="st_item_wise" id="st_item_wise">
          <label class="form-check-label" for="commercial">
            Sales Tax Item Wise
          </label>
          
        </div>
        @endif
    
    </div>



      @else


      <div class="@if(!empty($export_custom_fields)) col-sm-3 col-md-3 col-xs-3 @else col-sm-4 col-md-4 col-xs-4 @endif">
        @if(!empty($sell->contact->supplier_business_name))
          {{ $sell->contact->supplier_business_name }}
           
            <!--{{ $sell->location->name.", ".$purchase->location->city.", ".$sell->location->state.", ".$sell->location->country.", ".$sell->location->zip_code }} -->
          <br>
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
                  <div class="table-responsive" style="padding:18px;padding-bottom:0px; padding-top:0px;">
                     @include('sale_pos.partials.sale_line_details')
                  </div>
            @if($sell->type=="sale_invoice")
                <table class="table " id="only_item_wise" style="padding:18px">
                    <tbody><tr>
                    <th style="
                    width: 38%;
                "></th>
                    <th style="
                    width: 7%;
                ">0.00</th>
                    <th style="
                    width: 7%;
                ">0.00</th>
                    <th style="
                    width: 18%;
                ">0.00</th>
                    <th style="
                    width: 11%;
                ">0.00</th>
                    
                <th style="
                    width: 13%;
                ">0.00</th><th>0.00</th>
                
                </tr>
                <tr></tr>
                
                    
                </tbody></table>
                @endif
               </div>
            </div>

            
            
               <div class="row" style="padding:18px;padding-top: 0px !important;">
                  @php
                  $total_paid = 0;
                  @endphp
                  <div class="col-md-6 col-sm-12 col-xs-6">
                     <span id="t_y"><b>Thank You For Your Business</b></span>
                  </div>
                  <div class="col-md-6 col-sm-12 col-xs-6 @if($sell->type == 'sales_order') col-md-offset-8 col-xs-8 @endif" style="margin-left: 66.7%;">
                     <div class="table-responsive amount_p" style="width: 65.1%;margin-top: -55px;">
                        @if($sell->type=="sales_order" || $sell->type == 'sale_invoice' || $sell->type == 'sale_return_invoice')
                        <table class="table bg-gray total_sm_table">
                            
                    
                           
                            <tr>
                              <th>{{ __('Sub Total') }}: </th>
                              <td><span class="display_currency pull-right" data-currency_symbol="true">{{ $sell->total_before_tax }}</span></td>
                           </tr>
                           
                             @if($sell->type=="sale_invoice")
                           <tr id="sales_taxx">
                              <th>Sales Tax: </th>
                              <td><span class="display_currency pull-right" data-currency_symbol="true">0.00</span></td>
                           </tr>
                          
                          
                            <tr id="add_sales_taxx">
                              <th>Add Sales Tax: </th>
                              <td><span class="display_currency pull-right" data-currency_symbol="true">0.00</span></td>
                           </tr>
                           @endif
                           
                           
                           <tr id="tax_vat">
                              <th>GST / F-GST:</th>
                              <td>
                                 <div class="pull-right"> {{$taxes =$sale_tax_amount + $total_further_tax}}</div>
                                 <!--<div class="pull-right"><span class="display_currency" @if( $sell->discount_type == 'fixed') data-currency_symbol="true" @endif>{{ $sell->discount_amount }}</span> @if( $sell->discount_type == 'percentage') {{ '%'}} @endif</span></div>-->
                              </td>
                              
                           </tr>
                           
                          @if ($sell->add_charges !== null || $sell->less_charges !== null)
                            <tr>
                                <th>{{ ($sell->add_charges !== null ? 'Add Charges' : 'Less Charges') }}</th>
                                <td>
                                    <div class="pull-right">
                                        {{ ($sell->add_charges !== null ? $sell->add_charges : $sell->less_charges) }}
                                    </div>
                                </td>
                            </tr>
                        @endif

                           </tr>
                           {{--
                           @if(!empty($line_taxes))
                           <tr>
                              <th>{{ __('lang_v1.line_taxes') }}:</th>
                              
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
                           
                           --}}
                        </table>
                        @endif
                     </div>
                  </div>
                  
                  
                  <div class="col-md-4 col-sm-4 col-xs-4 pull-right hide_div_org" style="@if($sell->type == 'delivery_note') display: none; @endif">
                     <div style="background-color: #1b5596; margin-bottom: 0px; margin-top: -35px;" class="total_am">
                        <h3 style="color: #FFF; padding: 5px;" id="heading">
                            @if($sell->type == 'sales_order' || $sell->type == 'delivery_note')
                                <span style="font-size: 16px; font-weight: bold;" id="totalqnty_sale_order">Total Quantity</span>
                                <span style="font-size: 16px; font-weight: bold;" id="total_sale_orde">Total</span>
                            @else
                                <span style="font-size: 16px; font-weight: bold;">Total</span> 
                            @endif
                             @if($sell->type == 'sales_order')
                                <p class="pull-right total_qty" style="font-size: 16px; font-weight: bold;">{{ $total_qty  }}</p>
                                <p class="pull-right total_amount" style="font-size: 16px; font-weight: bold;">
                                    {{$sell->total_before_tax - $sell->discount_amount  + $taxes
                                    + ($sell->add_charges !== null ? $sell->add_charges : 0)
                                    - ($sell->less_charges !== null ? $sell->less_charges : 0)
                                    }}
          
                                    </p>
                              @elseif($sell->type == 'delivery_note')
                                <p class="pull-right total_qty" style="font-size: 16px; font-weight: bold;">{{ $total_qty  }}</p>
                                @elseif($sell->type == 'sale_invoice')
                                <p class="pull-right total_amount" style="font-size: 16px; font-weight: bold; margin-top: 0px;"><span class="display_currency" data-currency_symbol="true">{{$sell->final_total - $sell->discount_amount  }}</span></p>
                                <p class="pull-right total_qty" style="font-size: 16px; font-weight: bold; display: none;">{{ $total_qty  }}</p>
                            @endif
                         
                        </h3>
                     </div>
                     {{--@if($sell->type == 'delivery_note')--}}
                     <div id="weight">
                        <h4>
                           Gross Weight:
                           <p class="pull-right" style="font-size: 16px">{{ $sell->gross_weight??'0' }}</p>
                        </h4>
                        
                        <h4>
                           Net Weight:
                           <p class="pull-right" style="font-size: 16px">{{ $sell->net_weight ??'0'}}</p>
                        </h4>

                     </div>
                     {{--@endif --}}
                  </div>
                  
                  <!--hided total amount div-->
                  <div class="col-md-4 col-sm-4 col-xs-4 col-xs-4 pull-right hide_div_dup" style="@if($sell->type != 'delivery_note') display: none; @endif">
                     <div style="background-color: #1b5596; margin-bottom: 0px; margin-top: -85px;" class="total_am">
                        <h3 style="color: #FFF; padding: 5px;" id="heading">
                            @if($sell->type == 'sales_order' || $sell->type == 'delivery_note')
                                Total Quantity
                            @else
                                Total
                            @endif
                             @if($sell->type == 'sales_order')
                             
                                <p class="pull-right total_qty" style="font-size: 16px">{{ $total_qty  }}</p>
                               <p class="pull-right total_amount" style="font-size: 16px">{{$sell->total_before_tax - $sell->discount_amount  }}</p>
                              @elseif($sell->type == 'delivery_note')
                                <p class="pull-right total_qty" style="font-size: 16px">{{ $total_qty  }}</p>
                                @elseif($sell->type == 'sale_invoice')
                                <p class="pull-right total_amount" style="font-size: 16px"><span class="display_currency" data-currency_symbol="true">{{$sell->total_before_tax - $sell->discount_amount  }}</span></p>
                                <p class="pull-right total_qty" style="font-size: 16px; display: none;">{{ $total_qty  }}</p>
                                
                            @endif
                            
                             
                         
                        </h3>
                     </div>
                     {{--@if($sell->type == 'delivery_note')--}}
                     <div id="weight">
                        <h4 class="gross_weight">
                           Gross Weight:
                           <p class="pull-right" style="font-size: 16px">{{ $sell->gross_weight??'0' }}</p>
                        </h4>
                        
                        <h4 class="net_weight">
                           Net Weight:
                           <p class="pull-right" style="font-size: 16px">{{ $sell->net_weight ??'0'}}</p>
                        </h4>
                        <br>
                        <div>
                            <input type="checkbox" id="gross" name="drone" value="Gross Weight"  />
                            <label for="huey">Gross Weght</label>
                            <input type="checkbox" id="net" name="drone" value="Net Weight"  />
                            <label for="huey">Net Weght</label>
                          </div>
                     </div>
                     {{--@endif --}}
                  </div>
                  
                  
                  @if($sell->type=="sales_order" || $sell->type == 'sale_invoice' || $sell->type == 'sale_return_invoice')
                  <div class="col-md-6 col-sm-12 col-xs-12 col-xs-8 amount_inverse"  style="font-size:12px; margin-top: -50px; display:flex;">
                     <span id="amount_in_wrd" style="width:25%">AMOUNT IN WORDS:</span>
                     <span class="amount_in_word" style="display: block; margin-top: 2px; height: 55px;"><?php echo AmountInWords($sell->total_before_tax + $taxes - $sell->discount_amount + ($sell->add_charges !== null ? $sell->add_charges : 0) - ($sell->less_charges !== null ? $sell->less_charges : 0))?></span>
                  </div>
                  @endif 
               </div>
               
               
               
               
               
               
           <div class="spane_d" style="padding:20px; padding-top:0px;">
               
               @if(!empty($sell->tandc_title))
               <div class="row">
                  <div class="col-sm-12">
                       <h5 style="margin-top: 0px; margin-bottom: 22px; font-weight: 600;">Terms And Condition</h5>  
                       <p>{!! $sell->tandc_title !!}</p>    
                  </div>
               </div>
               @endif
             
              
             
             
               <div class="row" style="margin: 27px; margin-bottom: 0px; margin-top:20px;">
                  </br></br>
                  <!--<hr>-->
               
                   <div class="col-md-4 col-sm-4 col-xs-4 col-xs-4 ">
                    <p>Sign:______________</p>
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-4 col-xs-4 ">
                    <p>Sign:______________</p>
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-4 col-xs-4 ">
                    <p>Sign:______________</p>
                    </div>
                   
               </div>
            </div>
         </div>
         
         <div class="modal-footer text-center no-print">
            @if($sell->type == 'delivery_note')
            @if($sell->delivery_note_no == null)
              <a class="btn btn-primary " href="/sale_invoice/create?sale_type=sale_invoice&convert_id={{$sell->id}}">Convert To SI</a>
            @endif
            {{-- <a class="btn btn-primary hide" href="{{action('SellController@convert_dn_to_si', [$sell->id])}}">Convert To SI</a> --}}
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
            <input type="button" class="print-invoice btn btn-primary print_btn" onclick="printDiv('modal-content')" value="Print" />
            {{-- <a href="#" class="print-invoice btn btn-primary" data-href="{{route('sell.printInvoice', [$sell->id])}}"><i class="fa fa-print" aria-hidden="true"></i> @lang("lang_v1.print_invoice")</a> --}}
            @endcan
            <button style="color: white"  class="btn btn-primary  btn-flat d_button" >Download JPEG</button>
            <button type="button" class="btn btn-default no-print close_btn" data-dismiss="modal">@lang( 'messages.close' )</button>
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
    
    $(document).ready(function(){
        $('input:radio[name="rate_radio"]:checked').trigger('change');
        $('input:radio[name="invoice_radio"]:checked').trigger('change');
        
        if({{ request()->isprint ?? 0 }} == true){
		    $(document).find('.modal-dialog').addClass('hide');
		    setTimeout(function(){
		        $(".print_btn").click();
		    },1000)
		    $(".close_btn").click();
		}
    
            
        
        
    })
    
    $(document).on('change', 'input:radio[name="rate_radio"]', function(){
        if ($(this).is(':checked') && $(this).val() == 'with_rate') {
            $('.item_description, #weight, .total_qty,#sales_taxx,#add_sales_taxx, .hide_div_dup,#totalqnty_sale_order').hide();
            $('.item_delivry_qty, .item_unit_total, .item_unit_price, .total_sm_table, .amount_inverse, .total_amount, .hide_div_org, .product_desc,#total_sale_orde').show();
            $('.prt_title').text('Sale Order');
        }else if ($(this).is(':checked') && $(this).val() == 'cash_memo') {
            $('.item_description, #weight, .total_qty,#sales_taxx,#add_sales_taxx, .hide_div_dup').hide();
            $('.item_delivry_qty, .item_unit_total, .item_unit_price, .total_sm_table, .amount_inverse, .total_amount, .hide_div_org, .product_desc').show();
            $('.prt_title').text('Cash Memo');
        }else{
            $('.item_description, .item_delivry_qty, #weight, .total_qty, .hide_div_dup').show();
            $('.item_unit_total, .item_unit_price, .total_sm_table, .amount_inverse, .total_amount,#sales_taxx,#add_sales_taxx, .hide_div_org, .product_desc').hide();
            $('.prt_title').text('Sale Order');
        }
    })
    
    
    $(document).on('change', 'input:radio[name="invoice_radio"]', function(){
        
        if ($(this).is(':checked') && $(this).val() == 'invoice') {
            
            $('.shipped_to,#weight,#sales_taxx,#add_sales_taxx,#only_item_wise').hide();
            $('#s_ex_tax,#stax,#a_sale_tax,#amnt').hide();
            $('.item_unit_total').show();
            $('.invoicess').show();
            $('.commercial_invoice').hide();
            $('.sale_tax_invoice').hide();
            $('.sale_tax_product').hide();
            $('#tax_vat').show();
            $('.amount_inverse').css('margin-top','-50px');
         
        }else if($(this).is(':checked') && $(this).val() == 'commercial'){
            
            $('#weight,#sales_taxx,#add_sales_taxx,#only_item_wise').hide();
            $('.shipped_to,.item_unit_total').show();
            $('#s_ex_tax,#stax,#a_sale_tax,#amnt,#only_item_wise').hide();
            $('.invoicess').hide();
            $('.commercial_invoice').show();
            $('.sale_tax_invoice').hide();
            $('.sale_tax_product').hide();
            $('#tax_vat').show();
            $('.amount_inverse').css('margin-top','-50px');
           
        }else if($(this).is(':checked') && $(this).val() == 'Sale_tax'){
            
            $('#tax_vat').hide();
            $('#only_item_wise').hide();
            $('#sales_taxx,.item_unit_total').show();
            $('#add_sales_taxx').show();
            $('.shipped_to').show();
            $('#s_ex_tax,#stax,#a_sale_tax,#amnt').hide();
            $('.invoicess').hide();
            $('.commercial_invoice').hide();
            $('.sale_tax_invoice').show();
            $('.sale_tax_product').hide();
            $('.amount_inverse').css('margin-top','-50px');
            
        }else if($(this).is(':checked') && $(this).val() == 'st_item_wise'){
            
            $('#weight,#sales_taxx,#add_sales_taxx,.item_unit_total').hide();
            $('#s_ex_tax,#stax,#a_sale_tax,#amnt,#only_item_wise').show();
            $('.invoicess').hide();
            $('.commercial_invoice').hide();
            $('.sale_tax_invoice').hide();
            $('.sale_tax_product').show();
            $('#tax_vat').hide();
            $('.amount_inverse').css('margin-top','-40px');
            
        }
    })




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
   
    $(document).ready(function () 
    {
        
    $('.gross_weight, .net_weight').css('display','none');
         
        $('#gross').change(function () {
            if ($(this).is(':checked')) {
                $('.gross_weight').show();
            } else {
                $('.gross_weight').hide();
            }
        });

        $('#net').change(function () {
            if ($(this).is(':checked')) {
                $('.net_weight').show();
            } else {
                $('.net_weight').hide();
            }
        });
    });
</script>