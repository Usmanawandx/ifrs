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
@section('title', __('General Voucher'))
@section('content')
<section class="content-header">
    <h1>General Vouchers</h1>
</section>
<section class="content">
       @component('components.widget', ['class' => 'box-primary', 'title' => 'All GENERAL VOUCHERS'])
       
        @if (auth()->user()->can('account.journal_vouchers.add'))
            @slot('tool')
                <div class="box-tools">
                    <a class="btn btn-block btn-primary" href="{{action('AccountController@general_voucher')}}">
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
                    @if (auth()->user()->can('account.journal_vouchers.edit'))
                      <a href="/account/jv_edit/{{$v->reff_no}}" class="btn btn-sm btn-success btn-edt" ><i class="fas fa-edit"></i></a>
                    @endif
                    @if (auth()->user()->can('account.journal_vouchers.delete'))
                      <a href="/account/jv_delete/{{$v->reff_no}}" class="btn btn-sm btn-danger btn-dlt" ><i class="fas fa-trash"></i></a>
                    @endif
                 </td>
               <td>{{date('d-m-Y', strtotime($v->operation_date))}}</td>
               <td>{{$v->reff_no}}</td>
               <td>{{$v->note}}</td>
               <td>{{ number_format($v->amount, 2) }}</td>
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