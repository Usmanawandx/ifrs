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
@section('title', __('Account Book'))
@section('content')
<section class="content-header">
    <h1>Bank Book
        <!--<small>General Voucher Listing</small>-->
    </h1>
</section>
<section class="content">
       @component('components.widget', ['class' => 'box-primary', 'title' => 'All VOUCHERS'])
       @slot('tool')
       @can('bank_book.add')
            <div class="box-tools">
                <a class="btn btn-block btn-primary" href="{{action('AccountController@account_book')}}">
                <i class="fa fa-plus"></i> @lang('messages.add')</a>
            </div>
        @endcan
        @endslot
      <table class="table table-bordered table-striped ajax_view hide-footer dataTable table-styling table-hover table-primary" id="purchase_order_table" style="width: 100%;">
         <thead>
            <tr>
               <th  class="main-colum">Action</th>
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
                @can('bank_book.print')
                  <a href="{{route('show.Invoiceprt',$v->reff_no)}}" class="btn btn-sm btn-primary btn-vew" id=""><i class="fas fa-eye"></i></a>
                @endcan
                @can('bank_book.edit')
                  <a href="{{route('edit_bank_book',$v->reff_no)}}" class="btn btn-sm btn-success btn-edt" id=""><i class="fas fa-edit"></i></a>
                @endcan
                @can('bank_book.delete')
                  <a href="{{route('account_book_delete',$v->reff_no)}}" class="btn btn-sm btn-danger del_btn btn-dlt" ><i class="fas fa-trash"></i></a>
                @endcan
                </td>
               <td>{{date('d-m-Y', strtotime($v->operation_date))}}</td>
               <td>{{$v->reff_no}}</td>
               <td>{{$v->note}}</td>
               <td>{{number_format($v->total_debit_amount, 2)}}</td>
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