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
@section('title', __('vehicle Rate'))
@section('content')
<!-- Content Header (Page header) -->

<!-- Main content -->
 <section class="content-header">
   <h1>Transporter Rate</h1>
 </section>
 <section class="content no-print">
    @component('components.widget', ['class' => 'box-primary'])

    @if(auth()->user()->can('transporter_rate.create'))
       <div class="box-tools" style="float: right">
          <a class="btn btn-primary" href="/vehicle_rate/create" >
          <i class="fa fa-plus"></i> @lang('messages.add')</a> 
       </div>
       <br>
       <br>
   @endif
      @if(session()->has('message'))
      <div class="alert alert-success">
         {{ session()->get('message') }}
      </div>
      @endif  
      
      <table class="table table-bordered table-striped ajax_view hide-footer dataTable table-styling table-hover table-primary no-footer" id="purchase_order_table" style="width: 100%;">
         <thead>
            <tr>
               <th class="main-colum">Action</th>
               <th>Id</th>
               <th>Date</th>
               <th>Transporter</th>
               
            </tr>
         </thead>
         <tbody>
            @foreach ($data as $item)
            <tr>
               <td>
               @if(auth()->user()->can('transporter_rate.view'))
                  <a class="btn btn-success btn-sm btn-vew"  style="color: white" id="vehicle_view" data-id="{{ $item->rate_id }}"><i class="fa fa-eye"></i></a>
               @endif
                  
               @if(auth()->user()->can('transporter_rate.update'))
                  <a class="btn btn-primary btn-sm btn-edt" href="/vehicle_rate/edit/{{ $item->rate_id }}" style="color: white"><i class="glyphicon glyphicon-edit"></i></a>
               @endif
                  
               @if(auth()->user()->can('transporter_rate.delete'))
                  <a class="btn btn-danger btn-sm btn-dlt" href="{{ url('/vehicle_rate_delete', ['id' => $item->rate_id]) }}" style="color: white" onclick="return confirm('Are you sure to delete?')"><i class="glyphicon glyphicon-trash"></i></a>
               @endif
               </td>
               <td>{{$loop->iteration}}</td>
               <td>{!! $item->date !!}</td>
               <td>{!! $item->supplier_business_name !!}</td>

            </tr>
            @endforeach
         </tbody>
      </table>
      
    @endcomponent
 </section>
 
 
<!-- The Modal -->
<div class="modal" id="show_modal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">vehicle Rate Detail</h4>
      </div>

      <!-- Modal body -->
      <div class="modal-body c_detail">
        Modal body..
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
 
 
 
@endsection
@section('javascript')
<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">
   $(document).ready(function(){
        $('.ajax_view').DataTable();
        $(document).on('click','#vehicle_view',function(){
            var id = $(this).data('id');
            $.ajax({
              url: "/vehicle_rate/show/"+id,
              cache: false,
              success: function(html){
                $(".c_detail").html(html);
                $('#show_modal').modal('show');
              }
            });
        })
   });
</script>
@endsection