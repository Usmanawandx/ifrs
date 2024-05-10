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
            <div class="box-tools">
                <a class="btn btn-block btn-primary" href="{{action('AccountController@account_book')}}">
                <i class="fa fa-plus"></i> @lang('messages.add')</a>
            </div>
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
                     
                  <a href="{{route('show.Invoiceprt',$v->reff_no)}}" class="btn btn-sm btn-primary btn-vew" id=""><i class="fas fa-eye"></i></a>
                  <a href="{{route('edit_bank_book',$v->reff_no)}}" class="btn btn-sm btn-success btn-edt" id=""><i class="fas fa-edit"></i></a>
                  <a href="{{route('account_book_delete',$v->reff_no)}}" class="btn btn-sm btn-danger btn-dlt" ><i class="fas fa-trash"></i></a>
               </td>
               <td>{{date('d-m-Y', strtotime($v->operation_date))}}</td>
               <td>{{$v->reff_no}}</td>
               <td>{{$v->note}}</td>
               <td>{{number_format($v->amount, 2)}}</td>
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
</script>
@endsection