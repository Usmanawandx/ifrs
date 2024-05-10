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
@section('title', 'Vehicle')
@section('content')
<!-- Content Header (Page header) -->
{{-- <section class="content-header no-print">
</section> --}}
<!-- Main content -->
<div class="box-body" style="background: white">
   <h1>Vehicle</h1>
   
   @if(auth()->user()->can('vehicle.create'))
   <div class="box-tools" style="float: right">
      <a class="btn btn-block btn-primary" href="#"  class="btn btn-primary" data-toggle="modal" data-target="#saleModal">
      <i class="fa fa-plus"></i> @lang('messages.add')</a>
   </div>
   @endif
   <br>
   <br>
   <section class="content no-print">
      @if(session()->has('message'))
      <div class="alert alert-success">
         {{ session()->get('message') }}
      </div>
      @endif  
      <table class="table table-bordered table-striped ajax_view hide-footer dataTable table-styling table-hover table-primary" id="purchase_order_table" style="width: 100%;">
         <thead>
            <tr>
               <th class="main-colum">Action</th>
               <th>Id</th>
               <th>Vehicle #</th>
            </tr>
         </thead>
         <tbody>
            @foreach ($data as $item)
            <tr>
               <td>
                  @if(auth()->user()->can('vehicle.update'))
                  <a class="btn btn-primary btn btn-primary btn-edt" href="" id="editCompany" style="color: white" data-toggle="modal" data-target='#practice_modal' data-id="{{ $item->id }}"><i class="glyphicon glyphicon-edit"></i></a>
                  @endif
                  
                  @if(auth()->user()->can('vehicle.delete'))
                  <a class="btn btn-danger btn-dlt" href="{{ url('/vehicle_delete', ['id' => $item->id]) }}" style="color: white" onclick="return confirm('Are you sure to delete?')"><i class="glyphicon glyphicon-trash"></i></a>
                  @endif
               </td>
               <td>{{$loop->iteration}}</td>
               <td>{!! $item->vhicle_number !!}</td>
               @endforeach
         </tbody>
      </table>
   </section>
</div>
<div class="modal fade" id="practice_modal">
   <div class="modal-dialog">
      <form id="companydata">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">Update vehicle</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <input type="hidden" id="id_edit" name="id" >
            <div class="modal-body">
               <div class="form-group">
                  <label>Vehicle number</label>
                  {!! Form::text('number', "", ['class' => 'form-control', 'id'=> 'number']); !!}
               </div>
               <div class="form-group">
                  <label>Transporter</label>
                  {{-- {!! Form::text('transporter', "", ['class' => 'form-control', 'id'=> 'transporter']); !!} --}}
                  <select name="transporter" id="transporter" class="form-control" style="width: 100%;"> 
                     <option value="" selected disabled>Please Select</option>
                     @foreach ($transporters as $transporter)
                        <option value="{{ $transporter->id }}">{{ $transporter->supplier_business_name }}</option>
                     @endforeach
                  </select>
               </div>
               <div class="form-group">
                  <button type="button" class="btn btn-primary" value="Submit" id="submitedit">Submit</button>
               </div>
            </div>
         </div>
      </form>
   </div>
</div>
</div>
<div class="modal fade" id="saleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Add Vehicle</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            {!! Form::open(['url' => action('VehicleController@store'), 'method' => 'post', 'id' => 'add_purchase_form', 'files' => true ]) !!}
            <div class="form-group">
               <label>Vehicle Number</label>
               {!! Form::text('number', null, ['class' => 'form-control ']); !!}
            </div>
            <div class="form-group">
               <label>Transporter</label>
               {{-- {!! Form::text('transporter', null, ['class' => 'form-control ']); !!} --}}
               <select name="transporter" id="" class="form-control select2" style="width: 100%;"> 
                  <option value="" selected disabled>Please Select</option>
                  @foreach ($transporters as $transporter)
                     <option value="{{ $transporter->id }}">{{ $transporter->supplier_business_name }}</option>
                  @endforeach
               </select>
            </div>
            <div class="form-group">
               <button type="submit" class="btn btn-primary">Submit</button>
            </div>
            {!! Form::close() !!}
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
         </div>
      </div>
   </div>
</div>
<!-- /.content -->
@endsection
@section('javascript')
<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
<!-- <script type="text/javascript">  -->
<script type="text/javascript">
   $(document).ready(function(){
     $(document).on("click", "#editCompany",function (event) {
       var id = $(this).data('id');
       $.ajax({
       type: "GET",
       url:"{{url('/vehicle_edit')}}" +'/' + id + '/' + 'edit' ,
       success:function(data){
           $('#id_edit').val(data.id);
           $('#number').val(data.vhicle_number);
           $('#transporter').val(data.transporter_id);
         //   $('#transporter[value=' + data.transporter_id + ']').attr('selected',true);
       }
      })
     });
   

    $(document).on("click", "#submitedit",function (event) {
        var id = $("#id_edit").val();
        var number = $("#number").val();
        var transporter = $("#transporter").val();
        $.ajax({
          url:"{{url('/vehicle_update')}}" +'/' + id,
          type: "POST",
          data: {
            id: id,
            number: number,
            transporter: transporter,
          },
          dataType: 'json',
          success: function (data) {
              $('#companydata').trigger("reset");
              $('#practice_modal').modal('hide');
              window.location.reload(true);
          }
      });
    });
   
   });
    
   
</script>
@endsection