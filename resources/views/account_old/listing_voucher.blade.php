@extends('layouts.app')
<style>
.btn-edt {
        font-size: 14px !important;
        padding: 7px 8px 9px !important;
        border-radius: 50px !important;
        line-height: 0px !important;
    }
    
    .btn-vew {
        line-height: 0px !important;
        font-size: 14px !important;
        padding: 9px 8px 9px !important;
        border-radius: 50px !important;
        line-height: 0px !important;
    }
    
    .btn-dlt {
        line-height: 0px !important;
        font-size: 14px !important;
        padding: 7px 8px 9px !important;
        border-radius: 50px !important;
        line-height: 0px !important;
    }
    
    </style>
@section('title', __('Reciept Voucher'))

@section('content')
<!-- Content Header (Page header) -->

<!-- Main content -->


<div class="box-body" style="background: white;margin: 22px;">
    <h1>Voucher Listing
    </h1>

    
   
    </br>
</br>

    
<section class="prnt">
  

        <table class="table table-bordered table-striped ajax_view hide-footer dataTable table-styling table-hover table-primary" id="purchase_order_table" style="width: 100%;">
            <thead>
              
                    <tr>
                        <th class="main-colum">Action</th>
                        <th>SNo#</th>
                        <th>Voucher No#</th>
                        <th>Transaction Date</th>
                        <th>Description</th>
                        <th>Amount</th>
                        
                    </tr>    
            </thead>
            <tbody>
       @foreach($voucher_list as $v)
                <tr>
                    <td>
                        <a href="{{route('show.Invoiceprt',$v->reff_no)}}" class="btn btn-sm btn-success btn-vew" id=""><i class="fa fa-eye"></i></a>
                        <!--<a href="{{route('show.edit',$v->reff_no)}}" class="btn btn-sm btn-danger" id="">Edit</a>-->
                    </td>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$v->reff_no}}</td>
                    <td>{{date('d-m-Y', strtotime($v->operation_date))}}</td>
                    <td>{{$v->note}}</td>
                    <td>{{number_format($v->amount,2)}}</td>

                </tr>
    @endforeach
             
             
            </tbody>
        </table>
        
        
        
        

        
  
</section>

   <!--<button type="button" class="btn btn-primary no-print" aria-label="Print" -->
   <!--   onclick="$('section.prnt').printThis();"><i class="fa fa-print"></i> @lang( 'messages.print' )-->
   <!--   </button>-->
</div>



<!-- /.content -->


<!-- /.content -->

@endsection
