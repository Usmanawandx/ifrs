@extends('layouts.app')
@section('title', 'Aging Report')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header no-print">
   <h1>Aging Report
   </h1>
</section>
<!-- Main content -->
<section class="content no-print">
   @component('components.widget', ['class' => 'box-primary'])
   <div class="table-responsive">
      <table class="table table-bordered table-striped ajax_view hide-footer dataTable table-styling table-hover table-primary" id="sale_report_table">
         <thead>
            <tr>
               <th>Invoice Date</th>
               <th>Invoice No</th>
               <th>Customer name</th>
               <th>Credit Days</th>
               <th>Total Amount</th>
               <th>Outstanding Amount</th>
               <th>Outstanding Days</th>
               <th>01-30</th>
               <th>31-60</th>
               <th>61-90</th>
               <th>91-120</th>
               <th>121-150</th>
               <th>151-180</th>
               <th>181-270</th>
               <th>271-365</th>
               <th>Over 1 Year</th>
               <th>Provided</th>
               <th>Provision Recovered</th>
               <th>Write Off</th>
               <th>Addition</th>
               <th>Provision Date</th>
               <th>Provide Remarks</th>
               <th>Provision Recovered Date</th>
            </tr>
            @foreach($data as $item)
            <tr>
               <td>{{$item->transaction_date}}</td>
               <td>{{$item->ref_no}}</td>
               <td>{{$item->supplier_business_name}}</td>
               <td>{{$item->pay_term_number}}{{$item->pay_term_type}}</td>
               <td>{{$item->final_total}}</td>
               <td>{{ $item->final_total - $item->pay_amount }}</td>
               <td>{{$item->outstanding_days}}</td>
               <td>
                  @if($item->outstanding_days >= 1 && $item->outstanding_days <= 30)
                  <!-- Display data for 01-30 -->
                  {{ $item->final_total }}
                  @endif
               </td>
               <td>
                  @if($item->outstanding_days >= 31 && $item->outstanding_days <= 60)
                  <!-- Display data for 31-60 -->
                  {{ $item->pay_amount }}
                  @endif
               </td>
               <td>
                  @if($item->outstanding_days >= 61 && $item->outstanding_days <= 90)
                  <!-- Display data for 31-60 -->
                  {{ $item->pay_amount }}
                  @endif
               </td>
               <td>
                  @if($item->outstanding_days >= 91 && $item->outstanding_days <= 120)
                  <!-- Display data for 31-60 -->
                  {{ $item->pay_amount }}
                  @endif
               </td>
               <td>
                  @if($item->outstanding_days >= 121 && $item->outstanding_days <= 150)
                  <!-- Display data for 31-60 -->
                  {{ $item->pay_amount }}
                  @endif
               </td>
               <td>
                  @if($item->outstanding_days >= 151 && $item->outstanding_days <= 180)
                  <!-- Display data for 31-60 -->
                  {{ $item->pay_amount }}
                  @endif
               </td>
               <td>
                  @if($item->outstanding_days >= 181 && $item->outstanding_days <= 270)
                  <!-- Display data for 31-60 -->
                  {{ $item->pay_amount }}
                  @endif
               </td>
               <td> 
                  @if($item->outstanding_days >= 271 && $item->outstanding_days <= 365)
                  {{ $item->pay_amount }}
                  @endif
               </td>
               <td>   
                  @if($item->outstanding_days > 365)
                  {{ $item->pay_amount }}
                  @endif
               </td>
            
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
            </tr>
            @endforeach
         </thead>
      </table>
   </div>
   @endcomponent
</section>
@stop
@section('javascript')
@endsection