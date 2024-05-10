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
@if(request('type') == 'cash_received_voucher')
    @section('title', 'Cash Received Voucher')
@else
   @section('title', __('Reciept Voucher'))
@endif

@section('content')
<section class="content-header">
    <h1>
        @if(request('type') == 'cash_received_voucher')
            Cash Received Voucher
        @else
            Reciept Vouchers
        @endif
    </h1>
</section>
<section class="content">
    
       @component('components.widget', ['class' => 'box-primary', 'title' => 'All VOUCHERS'])
        @if (auth()->user()->can('account.receiept_vouchers.add'))
            @slot('tool')
                <div class="box-tools">
                    @if(request('type') == 'cash_received_voucher')
                        <a class="btn btn-block btn-primary" href="{{action('AccountController@credit_voucher',['type' => 'cash_received_voucher'])}}">
                    @else
                        <a class="btn btn-block btn-primary" href="{{action('AccountController@credit_voucher')}}">
                    @endif
                    <i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div>
            @endslot
        @endif
      <table class="table table-bordered table-striped ajax_view hide-footer dataTable table-styling table-hover table-primary" id="purchase_order_table" style="width: 100%;">
         <thead>
            <tr>
               <th class="main-colum">Action</th>
               <th>Transaction Date</th>
               <th>Voucher No#</th>
               <th>Description</th>
               <th>Amount</th>
            </tr>
         </thead>
         <tbody>
            @foreach($list as $v)
            <tr>
                <td>
                    <a href="{{route('show.Invoiceprt',$v->reff_no)}}" class="btn btn-sm btn-primary btn-vew" id=""><i class="fas fa-eye"></i></a>
                    @if (auth()->user()->can('account.receiept_vouchers.edit'))
                      <a href="/account/cv_edit/{{$v->reff_no}}" class="btn btn-sm btn-success btn-edt" ><i class="fas fa-edit"></i></a>
                    @endif
                    @if (auth()->user()->can('account.receiept_vouchers.delete'))
                      <a href="/account/cv_delete/{{$v->reff_no}}" class="btn btn-sm btn-danger btn-dlt" ><i class="fas fa-trash"></i></a>
                    @endif
                      <a href="/account/receipt_form" class="btn btn-primary btn-vew" ><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-receipt" viewBox="0 0 16 16">
                        <path d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v12a.5.5 0 0 1-.053.224l-.5 1a.5.5 0 0 1-.8.13L13 14.707l-.646.647a.5.5 0 0 1-.708 0L11 14.707l-.646.647a.5.5 0 0 1-.708 0L9 14.707l-.646.647a.5.5 0 0 1-.708 0L7 14.707l-.646.647a.5.5 0 0 1-.708 0L5 14.707l-.646.647a.5.5 0 0 1-.708 0L3 14.707l-.646.647a.5.5 0 0 1-.801-.13l-.5-1A.5.5 0 0 1 1 14V2a.5.5 0 0 1 .053-.224l.5-1a.5.5 0 0 1 .367-.27zm.217 1.338L2 2.118v11.764l.137.274.51-.51a.5.5 0 0 1 .707 0l.646.647.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.509.509.137-.274V2.118l-.137-.274-.51.51a.5.5 0 0 1-.707 0L12 1.707l-.646.647a.5.5 0 0 1-.708 0L10 1.707l-.646.647a.5.5 0 0 1-.708 0L8 1.707l-.646.647a.5.5 0 0 1-.708 0L6 1.707l-.646.647a.5.5 0 0 1-.708 0L4 1.707l-.646.647a.5.5 0 0 1-.708 0l-.509-.51z"/>
                        <path d="M3 4.5a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm8-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5z"/>
                      </svg></a>
                 </td>
               <td>{{date('d-m-Y', strtotime($v->operation_date))}}</td>
               <td>{{$v->reff_no}}</td>
               <td>{{$v->note}}</td>
               <td>{{ number_format($v->total_amount,2) }}</td>
            </tr>
            @endforeach
         </tbody>
      </table>
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