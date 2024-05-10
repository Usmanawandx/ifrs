@extends('layouts.app')
<style>
    .btn-edt {
        font-size: 14px !important;
        padding: 7px 8px 9px !important;
        border-radius: 50px !important;
    }
    
    .btn-vew {
        font-size: 14px !important;
        padding: 9px 8px 9px !important;
        border-radius: 50px !important;
    }
    
    .btn-dlt {
        font-size: 14px !important;
        padding: 7px 8px 9px !important;
        border-radius: 50px !important;
    }
        
    </style>
@if(request('type') == 'cash_payment_voucher')
    @section('title', 'Cash Payment Voucher')
@else
    @section('title', __('Payment Voucher'))
@endif
@section('content') 
<section class="content-header">
    <h1>
        @if(request('type') == 'cash_payment_voucher')
            Cash Payment Voucher
        @else
            Payment Vouchers
        @endif
        
    </h1>
</section>
<section class="content">
       @component('components.widget', ['class' => 'box-primary', 'title' => 'All VOUCHERS'])
        @if (auth()->user()->can('account.payment_vouchers.add'))
           @slot('tool')
                <div class="box-tools">
                    @if(request('type') == 'cash_payment_voucher' && auth()->user()->can('account.cash_payment_vouchers.add'))
                        <a class="btn btn-block btn-primary" href="{{action('AccountController@debit_voucher',['type' => 'cash_payment_voucher'])}}"> <i class="fa fa-plus"></i> @lang('messages.add')</a>
                    @elseif(request('type') != 'cash_payment_voucher' && auth()->user()->can('account.payment_vouchers.add'))
                        <a class="btn btn-block btn-primary" href="{{action('AccountController@debit_voucher')}}"> <i class="fa fa-plus"></i> @lang('messages.add')</a>
                    @endif
                    
                   
                </div>
            @endslot
        @endif
        <div class="table-responsive ">
      <table class="table table-bordered table-striped ajax_view hide-footer dataTable table-styling table-hover table-primary" id="purchase_order_table" style="width: 100%;">
         <thead>
            <tr>
                <th class="main-colum">Action</th>
               <th>Transaction Date</th>
               <th>Voucher No#</th>
               <th>Description</th>
               <th>Amount</th>
               <th>Attachment</th>
            </tr>
         </thead>
         <tbody>
            @foreach($list as $v)
            <tr>
              <td>
                    @if(request('type') == 'cash_payment_voucher' && auth()->user()->can('account.cash_payment_voucher.print'))
                    <a href="{{route('show.Invoiceprt',$v->reff_no)}}" class="btn btn-sm btn-primary btn-vew" id=""><i class="fas fa-eye"></i></a>
                    @elseif(request('type') != 'cash_payment_voucher' && auth()->user()->can('account.payment_vouchers.print'))
                    <a href="{{route('show.Invoiceprt',$v->reff_no)}}" class="btn btn-sm btn-primary btn-vew" id=""><i class="fas fa-eye"></i></a>
                    @endif
                    
                    @if(request('type') == 'cash_payment_voucher' && auth()->user()->can('account.cash_payment_vouchers.edit'))
                      <a href="/account/dv_edit/{{$v->reff_no}}" class="btn btn-sm btn-success btn-edt" ><i class="fas fa-edit"></i></a>
                    @elseif(request('type') != 'cash_payment_voucher' && auth()->user()->can('account.payment_vouchers.edit'))
                    <a href="/account/dv_edit/{{$v->reff_no}}" class="btn btn-sm btn-success btn-edt" ><i class="fas fa-edit"></i></a>
                    @endif
                    
                    @if(request('type') == 'cash_payment_voucher' && auth()->user()->can('account.cash_payment_vouchers.delete'))
                      <a href="/account/dv_delete/{{$v->reff_no}}" class="btn btn-sm btn-danger del_btn btn-dlt" ><i class="fas fa-trash"></i></a>
                    @elseif(request('type') != 'cash_payment_voucher' && auth()->user()->can('account.payment_vouchers.delete'))
                    <a href="/account/dv_delete/{{$v->reff_no}}" class="btn btn-sm btn-danger del_btn btn-dlt" ><i class="fas fa-trash"></i></a>
                    @endif
                </td>
               <td>{{date('d-m-Y', strtotime($v->operation_date))}}</td>
               <td>{{$v->reff_no}}</td>
               <td>{{$v->note}}</td>
               <td>{{ number_format($v->total_amount, 2) }}</td>
               <td>
                @if($v->attachment)
                    <a href="{{ asset("payment_voucher/{$v->attachment}") }}" target="_blank">View Attachment</a>
               
                @endif
            </td>
            </tr>
            @endforeach
         </tbody>
      </table>
      </div>
      @endcomponent
</section>

@endsection
@section('javascript')
<script>
$(document).ready( function () {
    $('.ajax_view').DataTable();
});

$(document).on('click', '.del_btn', function(e){
   e.preventDefault();
        swal({
            title: LANG.sure,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete)=>{
            if(willDelete){
               window.location = this.href;
            }
        });
    })
</script>
@endsection