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
  
  .modal-header{
      border-bottom: 0px !important;
  }
  
</style>
<div style="background-color: #FFF !important; max-width:100% !important;" class="photo">
 <div class="modal-header">
   {{-- <!--<button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
   @php
   $title = $purchase->type == 'purchase_order' ? __('lang_v1.purchase_order_details') : __('purchase.purchase_details');
   $custom_labels = json_decode(session('business.custom_labels'), true);
   @endphp
   <!--<h4 class="modal-title" id="modalTitle"> {{$title}} (<b>@lang('purchase.ref_no'):</b> #{{ $purchase->ref_no }})</h4>--> --}}
       <div class="row">
           <div class="col-xs-6 col-md-6 col-xs-6">
               @if(!empty(Session::get('business.logo')))
                 <img src="{{ asset( 'uploads/business_logos/' . Session::get('business.logo') ) }}" width="150" height="100" alt="Logo">
               @endif
           </div>
            <div class="col-xs-6 col-md-6 col-xs-6">
               <br><br>
               @if($purchase->type == 'Purchase Requisition') 
                   <h3 class="modal-title prt_title pull-right" id="modalTitle" style="font-weight:600">Purchase Requisition</h3>
               @elseif($purchase->type == 'purchase') 
                   <h3 class="modal-title prt_title pull-right" id="modalTitle" style="font-weight:600">purchase</h3>
               @elseif($purchase->type == 'Purchase_invoice') 
                   <h3 class="modal-title prt_title pull-right" id="modalTitle" style="font-weight:600">Purchase Invoice</h3>
               @elseif($purchase->type == 'purchase_order') 
                   <h3 class="modal-title prt_title pull-right" id="modalTitle" style="font-weight:600">Purchase Order</h3>
               
               @endif
               
           </div>
       </div>
 </div>
 
 
 <div class="modal-body invoice_modal">
   <div class="row">
  
            @if($purchase->type =="Purchase_invoice")
     <div class="col-sm-12">
       <p class="pull-right"><b>Supplier:</b> {{ @format_date($purchase->contact->supplier_business_name) }}</p>
     </div>
     @endif
   </div>
   <div class="row invoice-info">
       
     <div class="col-sm-4 invoice-col hide">

       <address>
         @if(!empty($purchase->contact->tax_number))
         <br>@lang('contact.tax_no'): {{$purchase->contact->tax_number}}
         @endif
         @if(!empty($purchase->contact->supplier_business_name))
         <br><b>@lang('Supplier'):</b> {{$purchase->contact->supplier_business_name}}
         @endif
         @if(!empty($purchase->contact->mobile))
         <br>@lang('contact.mobile'): {{$purchase->contact->mobile}}
         @endif
         @if(!empty($purchase->contact->email))
         <br>@lang('business.email'): {{$purchase->contact->email}}
         @endif
       </address>
       
       @if($purchase->document_path)
       <a href="{{$purchase->document_path}}" download="{{$purchase->document_name}}" class="btn btn-sm btn-success pull-left no-print">
         <i class="fa fa-download"></i>
         &nbsp;{{ __('purchase.download_document') }}
       </a>
       @endif
       
     </div>

       <div class="col-sm-8 col-xs-8 invoice-col">

         <h3 style="font-size:16px; font-weight:bolder;">{{ $purchase->business->name }}</h3>
                   @if($purchase->type =="Purchase_invoice")
       
           <p >{{$purchase->contact->supplier_business_name }}</p>

         @endif
         <br>
         <p>{{ $purchase->location->name }}</p>
         @if(!empty($purchase->location->landmark))
         <br><p>{{$purchase->location->landmark}}</p>
         @endif
         @if(!empty($purchase->location->city) || !empty($purchase->location->state) || !empty($purchase->location->country))
         <br><p>{{implode(',', array_filter([$purchase->location->city, $purchase->location->state, $purchase->location->country]))}}</p>
         @endif

         {{-- @if(!empty($purchase->business->tax_number_1))
         <br>{{$purchase->business->tax_label_1}}: {{$purchase->business->tax_number_1}}
         @endif

         @if(!empty($purchase->business->tax_number_2))
         <br>{{$purchase->business->tax_label_2}}: {{$purchase->business->tax_number_2}}
         @endif --}}

         <br><p>@lang('contact.mobile'): {{ !empty($purchase->location->mobile) ? $purchase->location->mobile : Auth::user()->contact_number }}</p>
         
         @if(!empty($purchase->location->email))
         <br><p>@lang('business.email'): {{$purchase->location->email}}</p>
         @endif
     </div>
       <div class="col-sm-2 col-xs-2 invoice-col">
           <p><b>@lang('purchase.ref_no')</b></p>
           <br />
           <p><b>@lang('Expected Date')</b></p>
           <br />
           @if(!empty($purchase->status))
           <p><b>@lang('purchase.purchase_status')</b></p>
           <br>
           @endif
           @if(!empty($purchase->payment_status))
           <p><b>@lang('purchase.payment_status')</b></p>
           <br>
           @endif
           @if(!empty($purchase->po_ref_no))
           <p><b>PO #</b></p>
           <br>
           @endif
           @if(!empty($purchase->grn_ref_no))
           <p><b>GR #</b></p>
           @endif
       </div>
       <div class="col-sm-2 col-xs-2 invoice-col">
           <p> :&nbsp;&nbsp;&nbsp; #{{ $purchase->ref_no }}</p><br />
           <p> :&nbsp;&nbsp;&nbsp;{{ @format_date($purchase->expected_date) }}</p><br />
           @if(!empty($purchase->status))
           <p>:&nbsp;&nbsp;&nbsp; @if($purchase->type == 'purchase_order'){{$po_statuses[$purchase->status]['label'] ?? ''}} @else {{ __($purchase->status) }} @endif</p><br>
           @endif
           @if(!empty($purchase->payment_status))
           <p>:&nbsp;&nbsp;&nbsp;{{ __('lang_v1.' . $purchase->payment_status) }}</p><br>
           @endif
           @if(!empty($purchase->po_ref_no))
           <p>:&nbsp;&nbsp;&nbsp;{{ __($purchase->po_ref_no) }}</p><br>
           @endif
           @if(!empty($purchase->grn_ref_no))
           <p>:&nbsp;&nbsp;&nbsp;{{ __($purchase->grn_ref_no) }}</p>
           @endif
       </div>


     <div class="col-sm-2 invoice-col hide">
       

       @if(!empty($custom_labels['purchase']['custom_field_1']))
       <br><strong>{{$custom_labels['purchase']['custom_field_1'] ?? ''}}: </strong> {{$purchase->custom_field_1}}
       @endif
       @if(!empty($custom_labels['purchase']['custom_field_2']))
       <br><strong>{{$custom_labels['purchase']['custom_field_2'] ?? ''}}: </strong> {{$purchase->custom_field_2}}
       @endif
       @if(!empty($custom_labels['purchase']['custom_field_3']))
       <br><strong>{{$custom_labels['purchase']['custom_field_3'] ?? ''}}: </strong> {{$purchase->custom_field_3}}
       @endif
       @if(!empty($custom_labels['purchase']['custom_field_4']))
       <br><strong>{{$custom_labels['purchase']['custom_field_4'] ?? ''}}: </strong> {{$purchase->custom_field_4}}
       @endif
       @if($purchase->type == 'purchase_order')
       @php
       $custom_labels = json_decode(session('business.custom_labels'), true);
       @endphp
       <strong>@lang('sale.shipping'):</strong>
       <span class="label @if(!empty($shipping_status_colors[$purchase->shipping_status])) {{$shipping_status_colors[$purchase->shipping_status]}} @else {{'bg-gray'}} @endif">{{$shipping_statuses[$purchase->shipping_status] ?? '' }}</span><br>
       @if(!empty($purchase->shipping_address()))
       {{$purchase->shipping_address()}}
       @else
       {{$purchase->shipping_address ?? '--'}}
       @endif
       @if(!empty($purchase->delivered_to))
       <br><strong>@lang('lang_v1.delivered_to'): </strong> {{$purchase->delivered_to}}
       @endif
       @if(!empty($purchase->shipping_custom_field_1))
       <br><strong>{{$custom_labels['shipping']['custom_field_1'] ?? ''}}: </strong> {{$purchase->shipping_custom_field_1}}
       @endif
       @if(!empty($purchase->shipping_custom_field_2))
       <br><strong>{{$custom_labels['shipping']['custom_field_2'] ?? ''}}: </strong> {{$purchase->shipping_custom_field_2}}
       @endif
       @if(!empty($purchase->shipping_custom_field_3))
       <br><strong>{{$custom_labels['shipping']['custom_field_3'] ?? ''}}: </strong> {{$purchase->shipping_custom_field_3}}
       @endif
       @if(!empty($purchase->shipping_custom_field_4))
       <br><strong>{{$custom_labels['shipping']['custom_field_4'] ?? ''}}: </strong> {{$purchase->shipping_custom_field_4}}
       @endif
       @if(!empty($purchase->shipping_custom_field_5))
       <br><strong>{{$custom_labels['shipping']['custom_field_5'] ?? ''}}: </strong> {{$purchase->shipping_custom_field_5}}
       @endif
       @php
       $medias = $purchase->media->where('model_media_type', 'shipping_document')->all();
       @endphp
       @if(count($medias))
       @include('sell.partials.media_table', ['medias' => $medias])
       @endif
       @endif
     </div>
   </div>

   <br>
   <div class="row">
     <div class="col-sm-12 col-xs-12 table_space">
       <div class="table-responsive">
         <table class="table bg-gray main">
           <thead>
             <tr class="bg-green">
               <th>#</th>

               @if($purchase->type =="Purchase Requisition")
               <th>Store</th>
               @endif
               <th>@lang('product.product_name')</th>
               <th>Item Code</th>
               @if($purchase->type == 'purchase_order')
               <th class="text-right">@lang( 'lang_v1.quantity_remaining' )</th>
               @endif

               @if($purchase->type =="Purchase Requisition")
               <th class="">UOM</th>
               <th class="">Item Description</th>
               @endif
               <th class="">@if($purchase->type == 'purchase_order') @lang('lang_v1.order_quantity') @else @lang('purchase.purchase_quantity') @endif</th>
               @if($purchase->type !="Purchase Requisition")

               <th class="text-right">@lang( 'lang_v1.unit_cost_before_discount' )</th>
               @endif
               @if($purchase->type !="Purchase Requisition")
               <th class="text-right">@lang( 'lang_v1.discount_percent' )</th>
               <th class="no-print text-right">@lang('purchase.unit_cost_before_tax')</th>
               <th class="no-print text-right">@lang('purchase.subtotal_before_tax')</th>
               @endif
               @if($purchase->type !="Purchase Requisition")

               <th class="text-right">@lang('sale.tax')</th>
               <th class="text-right">@lang('purchase.unit_cost_after_tax')</th>
               @endif
               @if($purchase->type != 'purchase_order' && $purchase->type !="Purchase Requisition")

               <th class="text-right">SubTotal</th>
               @if(session('business.enable_lot_number'))
               <th>@lang('lang_v1.lot_number')</th>
               @endif
               @if(session('business.enable_product_expiry'))
               <th>@lang('product.mfg_date')</th>
               <th>@lang('product.exp_date')</th>
               @endif
               @endif

               @if($purchase->type !="Purchase Requisition")
               <!--<th class="text-right">@lang('sale.subtotal')</th>-->
               @endif
             </tr>
           </thead>
           @php
           $total_before_tax = 0.00;
           @endphp
           @foreach($purchase->purchase_lines as $purchase_line)
           <tr>
             <td>{{ $loop->iteration }}</td>

             @if($purchase->type =="Purchase Requisition")

             <td>{{$purchase_line->store??' '}}</td>
             @endif
             <td>
               {{ $purchase_line->product->name }}
               @if( $purchase_line->product->type == 'variable')
               - {{ $purchase_line->variations->product_variation->name??''}}
               - {{ $purchase_line->variations->name??''}}
               @endif
             </td>
             <td>
               @if( $purchase_line->product->type == 'variable')
               {{ $purchase_line->variations->sub_sku??''}}
               @else
               {{ $purchase_line->product->sku }}
               @endif
             </td>
             @if($purchase->type == 'purchase_order')
             <td>
               <span class="display_currency" data-is_quantity="true" data-currency_symbol="false">{{ $purchase_line->quantity - $purchase_line->po_quantity_purchased }}</span> @if(!empty($purchase_line->sub_unit)) @else {{$purchase_line->product->unit->short_name}} {{$purchase_line->sub_unit->short_name}} @endif
             </td>
             @endif

             @if($purchase->type =="Purchase Requisition")
             <td>@if(!empty($purchase_line->sub_unit)) {{$purchase_line->sub_unit->short_name}} @else {{$purchase_line->product->unit->short_name}} @endif</td>
             <td>{{$purchase_line->item_description}}</td>
             @endif
             <td><span class="display_currency" data-is_quantity="true" data-currency_symbol="false">{{ $purchase_line->quantity }}</span> </td>
             @if($purchase->type !="Purchase Requisition")
             <td class="text-right"><span class="display_currency" data-currency_symbol="true">{{ $purchase_line->pp_without_discount}}</span></td>


             <td class="text-right"><span class="display_currency">{{ $purchase_line->discount_percent}}</span> %</td>
             <td class="no-print text-right"><span class="display_currency" data-currency_symbol="true">{{ $purchase_line->purchase_price }}</span></td>
             <td class="no-print text-right">
                 <span class="display_currency" data-currency_symbol="true">
                     
                 @if($purchase_line->product->unit->is_purchase_unit == 1 && $purchase_line->product->unit->base_unit_multiplier > 0)
                    {{ ($purchase_line->quantity * $purchase_line->purchase_price) / $purchase_line->product->unit->base_unit_multiplier  }}
                 @else
                    {{ $purchase_line->quantity * $purchase_line->purchase_price }}
                 @endif
                 </span>
            </td>
             
             @endif
             @if($purchase->type !="Purchase Requisition")
             <td class="text-right"><span class="display_currency" data-currency_symbol="true">{{ $purchase_line->item_tax }} </span> <br /><small>@if(!empty($taxes[$purchase_line->tax_id])) ( {{ $taxes[$purchase_line->tax_id]}} ) </small>@endif</td>
             <td class="text-right"><span class="display_currency" data-currency_symbol="true">{{ $purchase_line->purchase_price_inc_tax }}</span></td>
             @endif
             @if($purchase->type != 'purchase_order' && $purchase->type != 'Purchase Requisition' && $purchase->type != 'Purchase_invoice' )
             @php
             $sp = $purchase_line->variations->default_sell_price;
             if(!empty($purchase_line->sub_unit->base_unit_multiplier)) {
             $sp = $sp * $purchase_line->sub_unit->base_unit_multiplier;
             }
             @endphp

             <td class="text-right"><span class="display_currency" data-currency_symbol="true">{{$sp}}</span></td>

             @if(session('business.enable_lot_number'))
             <td>{{$purchase_line->lot_number}}</td>
             @endif

             @if(session('business.enable_product_expiry'))
             <td>
               @if(!empty($purchase_line->mfg_date))
               {{ @format_date($purchase_line->mfg_date) }}
               @endif
             </td>
             <td>
               @if(!empty($purchase_line->exp_date))
               {{ @format_date($purchase_line->exp_date) }}
               @endif
             </td>
             @endif
             @endif
             @if($purchase->type !="Purchase Requisition")
            <td class="text-right">
                <span class="display_currency" data-currency_symbol="true">
                    
                 
                 
                 
                 @if($purchase_line->product->unit->is_purchase_unit == 1 && $purchase_line->product->unit->base_unit_multiplier > 0)
                    {{ ($purchase_line->quantity * $purchase_line->purchase_price_inc_tax) / $purchase_line->product->unit->base_unit_multiplier  }}
                 @else
                    {{ $purchase_line->purchase_price_inc_tax * $purchase_line->quantity }}
                 @endif
                 
                 
                </span>
            </td>
             @endif
           </tr>
           @php
           $total_before_tax += ($purchase_line->quantity * $purchase_line->purchase_price);
           @endphp
           @endforeach
         </table>
       </div>
     </div>
   </div>
   <br>
   <div class="row">
     @if(!empty($purchase->type == 'purchase'))
     <div class="col-sm-12 col-xs-12">
       <h4>{{ __('sale.payment_info') }}:</h4>
     </div>
     <div class="col-md-6 col-sm-12 col-xs-12">
       <div class="table-responsive">
         <table class="table">
           <tr class="bg-green">
             <th>#</th>
             <th>{{ __('messages.date') }}</th>
             <th>{{ __('purchase.ref_no') }}</th>
             <th>{{ __('sale.amount') }}</th>
             <th>{{ __('sale.payment_mode') }}</th>
             <th>{{ __('sale.payment_note') }}</th>
           </tr>
           @php
           $total_paid = 0;
           @endphp
           @forelse($purchase->payment_lines as $payment_line)
           @php
           $total_paid += $payment_line->amount;
           @endphp
           <tr>
             <td>{{ $loop->iteration }}</td>
             <td>{{ @format_date($payment_line->paid_on) }}</td>
             <td>{{ $payment_line->payment_ref_no }}</td>
             <td><span class="display_currency" data-currency_symbol="true">{{ $payment_line->amount }}</span></td>
             <td>{{ $payment_methods[$payment_line->method] ?? '' }}</td>
             <td>@if($payment_line->note)
               {{ ucfirst($payment_line->note) }}
               @else
               --
               @endif
             </td>
           </tr>
           @empty
           <tr>
             <td colspan="5" class="text-center">
               @lang('purchase.no_payments')
             </td>
           </tr>
           @endforelse
         </table>
       </div>
     </div>
     @endif
     <div class="col-md-6 col-sm-12 col-xs-6 hide @if($purchase->type == 'purchase_order') col-md-offset-6 @endif">
       <div class="table-responsive">
         <table class="table">
           <!-- <tr class="hide">
           <th>@lang('purchase.total_before_tax'): </th>
           <td></td>
           <td><span class="display_currency pull-right">{{ $total_before_tax }}</span></td>
         </tr> -->
           <tr>
             <th>@lang('purchase.net_total_amount'): </th>
             <td></td>
             <td><span class="display_currency pull-right" data-currency_symbol="true">{{ $total_before_tax }}</span></td>
           </tr>
           <tr>
             <th>@lang('purchase.discount'):</th>
             <td>
               <b>(-)</b>
               @if($purchase->discount_type == 'percentage')
               ({{$purchase->discount_amount}} %)
               @endif
             </td>
             <td>
               <span class="display_currency pull-right" data-currency_symbol="true">
                 @if($purchase->discount_type == 'percentage')
                 {{$purchase->discount_amount * $total_before_tax / 100}}
                 @else
                 {{$purchase->discount_amount}}
                 @endif
               </span>
             </td>
           </tr>
           <tr>
             <th>@lang('purchase.purchase_tax'):</th>
             <td><b>(+)</b></td>

             <td class="text-right">
               @if(!empty($purchase_taxes))
               @foreach($purchase_taxes as $k => $v)
               <strong><small>{{$k}}</small></strong> - <span class="display_currency pull-right" data-currency_symbol="true">{{ $v }}</span><br>
               @endforeach
               @else
               0.00
               @endif
             </td>
           </tr>
           @if( !empty( $purchase->shipping_charges ) )
           <tr>
             <th>@lang('purchase.additional_shipping_charges'):</th>
             <td><b>(+)</b></td>
             <td><span class="display_currency pull-right">{{ $purchase->shipping_charges }}</span></td>
           </tr>
           @endif
           @if( !empty( $purchase->additional_expense_value_1 ) && !empty( $purchase->additional_expense_key_1 ))
           <tr>
             <th>{{ $purchase->additional_expense_key_1 }}:</th>
             <td><b>(+)</b></td>
             <td><span class="display_currency pull-right">{{ $purchase->additional_expense_value_1 }}</span></td>
           </tr>
           @endif
           @if( !empty( $purchase->additional_expense_value_2 ) && !empty( $purchase->additional_expense_key_2 ))
           <tr>
             <th>{{ $purchase->additional_expense_key_2 }}:</th>
             <td><b>(+)</b></td>
             <td><span class="display_currency pull-right">{{ $purchase->additional_expense_value_2 }}</span></td>
           </tr>
           @endif
           @if( !empty( $purchase->additional_expense_value_3 ) && !empty( $purchase->additional_expense_key_3 ))
           <tr>
             <th>{{ $purchase->additional_expense_key_3 }}:</th>
             <td><b>(+)</b></td>
             <td><span class="display_currency pull-right">{{ $purchase->additional_expense_value_3 }}</span></td>
           </tr>
           @endif
           @if( !empty( $purchase->additional_expense_value_4 ) && !empty( $purchase->additional_expense_key_4 ))
           <tr>
             <th>{{ $purchase->additional_expense_key_4 }}:</th>
             <td><b>(+)</b></td>
             <td><span class="display_currency pull-right">{{ $purchase->additional_expense_value_4 }}</span></td>
           </tr>
           @endif
           <tr>
             <th>@lang('purchase.purchase_total'):</th>
             <td></td>
             <td><span class="display_currency pull-right" data-currency_symbol="true">{{ $purchase->final_total }}</span></td>
           </tr>
         </table>
       </div>
     </div>
   </div>
   <div class="row hide">
     <div class="col-sm-6">
       <strong>@lang('purchase.shipping_details'):</strong><br>
       <p class="well well-sm no-shadow bg-gray">
         {{ $purchase->shipping_details ?? '' }}

         @if(!empty($purchase->shipping_custom_field_1))
         <br><strong>{{$custom_labels['purchase_shipping']['custom_field_1'] ?? ''}}: </strong> {{$purchase->shipping_custom_field_1}}
         @endif
         @if(!empty($purchase->shipping_custom_field_2))
         <br><strong>{{$custom_labels['purchase_shipping']['custom_field_2'] ?? ''}}: </strong> {{$purchase->shipping_custom_field_2}}
         @endif
         @if(!empty($purchase->shipping_custom_field_3))
         <br><strong>{{$custom_labels['purchase_shipping']['custom_field_3'] ?? ''}}: </strong> {{$purchase->shipping_custom_field_3}}
         @endif
         @if(!empty($purchase->shipping_custom_field_4))
         <br><strong>{{$custom_labels['purchase_shipping']['custom_field_4'] ?? ''}}: </strong> {{$purchase->shipping_custom_field_4}}
         @endif
         @if(!empty($purchase->shipping_custom_field_5))
         <br><strong>{{$custom_labels['purchase_shipping']['custom_field_5'] ?? ''}}: </strong> {{$purchase->shipping_custom_field_5}}
         @endif
       </p>
     </div>
     <div class="col-sm-6">
       <strong>@lang('purchase.additional_notes'):</strong><br>
       <p class="well well-sm no-shadow bg-gray">
         @if($purchase->additional_notes)
         {{ $purchase->additional_notes }}
         @else
         --
         @endif
       </p>
     </div>
   </div>
   @if(!empty($activities))
   <div class="row hide">
     <div class="col-md-12">
       <strong>{{ __('lang_v1.activities') }}:</strong><br>
       @includeIf('activity_log.activities', ['activity_type' => 'purchase'])
     </div>
   </div>
   @endif

   {{-- Barcode --}}
   <!--<div class="row print_section">-->
   <!--  <div class="col-xs-12">-->
   <!--    <img class="center-block" src="data:image/png;base64,{{DNS1D::getBarcodePNG($purchase->ref_no, 'C128', 2,30,array(39, 48, 54), true)}}">-->
   <!--  </div>-->
   <!--</div>-->
 </div>
</div>

<div class="card-body" style="display: none;">
 <table id="employee_data" class="table table-striped table-bordered">
   <thead>
     <tr class="bg-green">
       <th>#</th>
       @if($purchase->type !="Purchase Requisition")
       @if(!empty($purchase->contact->tax_number))
       <th>tax no</th>
       @endif
       @if(!empty($purchase->contact->supplier_business_name))
       <th>Supplier</th>
       @endif
       @if(!empty($purchase->contact->mobile))
       <th>mobile</th>
       @endif
       @if(!empty($purchase->contact->email))
       <th>email</th>
       @endif
       @endif

       <th>reference no</th>
     <th>Expected Date</th>
     @if(!empty($purchase->status))
       <th>Status</th>
     @endif
     @if(!empty($purchase->payment_status))
     <th>payment status</th>
     @endif
     @if(!empty($purchase->po_ref_no))
     <th>PO #</th>
     @endif
     @if(!empty($purchase->grn_ref_no))
     <th>GR #</th>
     @endif


       @if($purchase->type =="Purchase Requisition")
       <th>Store</th>
       @endif
       <th>@lang('product.product_name')</th>
       <th>Item Code</th>
       @if($purchase->type == 'purchase_order')
       <th class="text-right">@lang( 'lang_v1.quantity_remaining' )</th>
       @endif

       @if($purchase->type =="Purchase Requisition")
       <th class="">UOM</th>
       <th class="">Item Description</th>
       @endif
       <th class="">@if($purchase->type == 'purchase_order') @lang('lang_v1.order_quantity') @else @lang('purchase.purchase_quantity') @endif</th>
       @if($purchase->type !="Purchase Requisition")

       <th class="text-right">@lang( 'lang_v1.unit_cost_before_discount' )</th>
       @endif
       @if($purchase->type !="Purchase Requisition")
       <th class="text-right">@lang( 'lang_v1.discount_percent' )</th>
       <th class="no-print text-right">@lang('purchase.unit_cost_before_tax')</th>
       <th class="no-print text-right">@lang('purchase.subtotal_before_tax')</th>
       @endif
       @if($purchase->type !="Purchase Requisition")

       <th class="text-right">@lang('sale.tax')</th>
       <th class="text-right">@lang('purchase.unit_cost_after_tax')</th>
       @endif
       @if($purchase->type != 'purchase_order' && $purchase->type !="Purchase Requisition")

       <th class="text-right">SubTotal</th>
       @if(session('business.enable_lot_number'))
       <th>@lang('lang_v1.lot_number')</th>
       @endif
       @if(session('business.enable_product_expiry'))
       <th>@lang('product.mfg_date')</th>
       <th>@lang('product.exp_date')</th>
       @endif
       @endif

       @if($purchase->type !="Purchase Requisition")
       <!--<th class="text-right">@lang('sale.subtotal')</th>-->
       @endif
     </tr>
   </thead>
   @php
   $total_before_tax = 0.00;
   @endphp
   @foreach($purchase->purchase_lines as $purchase_line)
   <tr>
     <td>{{ $loop->iteration }}</td>

     @if($purchase->type !="Purchase Requisition")

     @if(!empty($purchase->contact->tax_number))
     <td> {{$purchase->contact->tax_number}}</td>
     @endif
     @if(!empty($purchase->contact->supplier_business_name))
     <td> {{$purchase->contact->supplier_business_name}}</td>
     @endif
     @if(!empty($purchase->contact->mobile))
     <td>{{$purchase->contact->mobile}}</td>
     @endif
     @if(!empty($purchase->contact->email))
     <td> {{$purchase->contact->email}} </td>
     @endif
     @endif

     <td>{{ $purchase->ref_no }}</td>
       <td>{{ @format_date($purchase->expected_date) }}</td>
       @if(!empty($purchase->status))
       <td>@if($purchase->type == 'purchase_order'){{$po_statuses[$purchase->status]['label'] ?? ''}} @else {{ __($purchase->status) }} @endif</td>
       @endif
       @if(!empty($purchase->payment_status))
       <td>{{ __('lang_v1.' . $purchase->payment_status) }}</td>
       @endif
       @if(!empty($purchase->po_ref_no))
       <td>{{ __($purchase->po_ref_no) }}</td>
       @endif
       @if(!empty($purchase->grn_ref_no))
       <td>{{ __($purchase->grn_ref_no) }}</td>
       @endif

     @if($purchase->type =="Purchase Requisition")

     <td>{{$purchase_line->store??' '}}</td>
     @endif
     <td>
       {{ $purchase_line->product->name }}
       @if( $purchase_line->product->type == 'variable')
       - {{ $purchase_line->variations->product_variation->name??''}}
       - {{ $purchase_line->variations->name??''}}
       @endif
     </td>
     <td>
       @if( $purchase_line->product->type == 'variable')
       {{ $purchase_line->variations->sub_sku??''}}
       @else
       {{ $purchase_line->product->sku }}
       @endif
     </td>
     @if($purchase->type == 'purchase_order')
     <td>
       <span class="display_currency" data-is_quantity="true" data-currency_symbol="false">{{ $purchase_line->quantity - $purchase_line->po_quantity_purchased }}</span> @if(!empty($purchase_line->sub_unit)) @else {{$purchase_line->product->unit->short_name}} {{$purchase_line->sub_unit->short_name}} @endif
     </td>
     @endif

     @if($purchase->type =="Purchase Requisition")
     <td>@if(!empty($purchase_line->sub_unit)) {{$purchase_line->sub_unit->short_name}} @else {{$purchase_line->product->unit->short_name}} @endif</td>
     <td>{{$purchase_line->item_description}}</td>
     @endif
     <td><span class="display_currency" data-is_quantity="true" data-currency_symbol="false">{{ $purchase_line->quantity }}</span> </td>
     @if($purchase->type !="Purchase Requisition")
     <td class="text-right"><span class="display_currency" data-currency_symbol="true">{{ $purchase_line->pp_without_discount}}</span></td>


     <td class="text-right"><span class="display_currency">{{ $purchase_line->discount_percent}}</span> %</td>
     <td class="no-print text-right"><span class="display_currency" data-currency_symbol="true">{{ $purchase_line->purchase_price }}</span></td>
     <td class="no-print text-right"><span class="display_currency" data-currency_symbol="true">{{ $purchase_line->quantity * $purchase_line->purchase_price }}</span></td>
     @endif
     @if($purchase->type !="Purchase Requisition")
     <td class="text-right"><span class="display_currency" data-currency_symbol="true">{{ $purchase_line->item_tax }} </span> <br /><small>@if(!empty($taxes[$purchase_line->tax_id])) ( {{ $taxes[$purchase_line->tax_id]}} ) </small>@endif</td>
     <td class="text-right"><span class="display_currency" data-currency_symbol="true">{{ $purchase_line->purchase_price_inc_tax }}</span></td>
     @endif
     @if($purchase->type != 'purchase_order' && $purchase->type != 'Purchase Requisition' && $purchase->type != 'Purchase_invoice' )
     @php
     $sp = $purchase_line->variations->default_sell_price;
     if(!empty($purchase_line->sub_unit->base_unit_multiplier)) {
     $sp = $sp * $purchase_line->sub_unit->base_unit_multiplier;
     }
     @endphp

     <td class="text-right"><span class="display_currency" data-currency_symbol="true">{{$sp}}</span></td>

     @if(session('business.enable_lot_number'))
     <td>{{$purchase_line->lot_number}}</td>
     @endif

     @if(session('business.enable_product_expiry'))
     <td>
       @if(!empty($purchase_line->mfg_date))
       {{ @format_date($purchase_line->mfg_date) }}
       @endif
     </td>
     <td>
       @if(!empty($purchase_line->exp_date))
       {{ @format_date($purchase_line->exp_date) }}
       @endif
     </td>
     @endif
     @endif
     @if($purchase->type !="Purchase Requisition")
     <td class="text-right"><span class="display_currency" data-currency_symbol="true">{{ $purchase_line->purchase_price_inc_tax * $purchase_line->quantity }}</span></td>
     @endif
   </tr>
   @php
   $total_before_tax += ($purchase_line->quantity * $purchase_line->purchase_price);
   @endphp
   @endforeach
 </table>
</div>