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
       margin-right: 5px;
   }
   
   .btn-dlt {
       font-size: 14px !important;
       padding: 7px 8px 9px !important;
       border-radius: 50px !important;
       margin-left: 5px;
       margin-right: 5px;
   }
       
   </style>
@section('content')
<!-- Main content -->
<div class="box-body" style="background: white">
<h1>Sale Type 
</h1>
@if(auth()->user()->can('sale_type.add'))
<div class="box-tools" style="float: right">
   <a class="btn btn-block btn-primary" href="#"  class="btn btn-primary" data-toggle="modal" data-target="#saleModal">
   <i class="fa fa-plus"></i> @lang('messages.add')</a>
</div>
@endif
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
            <th>Prefix</th>
            <th>Purchase Type</th>
            <th>Type</th>
         </tr>
      </thead>
      <tbody>
         @foreach ($sale_type as $type)
         <tr>
            <td>
              @if(auth()->user()->can('sale_type.edit'))
               <button type="button"  class="btn btn-primary btn-edt"> <a href="" id="editCompany" style="color: white" data-toggle="modal" data-target='#practice_modal' data-id="{{ $type->id }}"><i class="fas fa-edit"></i></a></button>
             @endif
               @if(auth()->user()->can('sale_type.delete'))
               <button type="button"  class="btn btn-danger btn-dlt"><a href="{{ url('/sale_type', ['id' => $type->id]) }}" style="color: white" onclick="return confirm('Are you sure to delete?')"><i class="fas fa-trash"></i></a></button>
               @endif
            </td>
            <td>{{$loop->iteration}}</td>
            <td>{{$type->prefix}}</td>
            <td>{{$type->name}}</td>
            <td>{{($type->purchase_type) }} </td>
         </tr>
         @endforeach
      </tbody>
   </table>
</section>
    <div class="modal fade" id="practice_modal">
       <div class="modal-dialog">
          <form id="companydata">
             <div class="modal-content">
                <input type="hidden" id="id_edit" name="id" >
                <div class="modal-body">
                   {!! Form::label('prefix','Prefix') !!}
                   <input type="text" name="prefix" id="prefix" value="" class="form-control">
                </div>
                <div class="modal-body">
              
                   <div class="form-group">
                      {!! Form::label('control_account_id','Transaction Account') !!}
                      <select class="form-control select2" name="control_account_id" id="control_account_id">
                         <option value="" selected disabledd>Select Please</option>
                         @foreach($control_account as $key => $val)
                         <option value="{{ $val->id }}">{{ $val->name }}</option>
                         @endforeach
                      </select>
                   </div>
                </div>
                <div class="modal-body">
                   {!! Form::label('name',__('Name')) !!}
                   <input type="text" name="name" id="name" value="" class="form-control">
                </div>
                <div style="float:right">
                <input type="button" value="Submit" id="submitedit" class="btn btn-primary btn-outline-danger py-0" style="font-size: 0.8em;">
                </div>
                <br>
                <br>
             </div>
          </form>
       </div>
    </div>
    <div class="modal fade" id="saleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
       <div class="modal-dialog" role="document">
          <div class="modal-content">
             <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Sale Type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
             </div>
             <div class="modal-body">
                {!! Form::open(['url' => action('SalesOrderController@sale_store'), 'method' => 'post', 'id' => 'add_purchase_form', 'files' => true ]) !!}
                <div class="form-group">
                   {!! Form::label('prefix','Prefix') !!}
                   {!! Form::text('prefix', null, ['class' => 'form-control','required']); !!}
                </div>
                <div class="form-group">
                   {!! Form::label('name',__('Sales Type')) !!}
                   {!! Form::text('name', null, ['class' => 'form-control','required']); !!}
                </div>
                <!--<div class="form-group">-->
                <!--   {!! Form::label('purchase_type',__('Purchase Type')) !!}-->
                <!--   <select name="purchase_type" class="form-control" required >-->
                <!--      @foreach ($purchase_type as $p)-->
                <!--      <option value="{{$p->name}}">{{$p->name}}</option>-->
                <!--      @endforeach-->
                <!--   </select>-->
                <!--</div>-->
                <div class="form-group">
                   {!! Form::label('control_account_id','Transaction Account') !!}
                   <select class="form-control select2" name="control_account_id" id="control_account_id">
                      <option value="" selected disabledd>Select Please</option>
                      @foreach($control_account as $key => $val)
                      <option value="{{ $val->id }}">{{ $val->name }}</option>
                      @endforeach
                   </select>
                </div>
                <div class="form-group">
                   <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                {!! Form::close() !!}
                </form>
             </div>
             <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
             </div>
          </div>
       </div>
    </div>
<!-- /.content -->
</br>
</br>
</br>
</br>
</br>
</br>
</br>
</br>
@endsection 
@section('javascript')
<script type="text/javascript">
  $(document).ready(function(){ 
      
    $(document).on("click", "#editCompany",function (event) {
       var id = $(this).data('id');
       $.ajax({
       type: "GET",
       url:"{{url('/sale_type_update')}}" +'/' + id + '/' + 'edit' ,
       success:function(data){
         console.log(data);
           $('#id_edit').val(data.id);
           $('#prefix').val(data.prefix);
           $('#name').val(data.name);
           $('#purchase_type').val(data.purchase_type);
           if($('#control_account_id').data('select2')) {
                $('#control_account_id').select2('destroy');
           }
           $('#control_account_id').val(data.control_account_id);
           $('#control_account_id').select2();
           $('#practice_modal').modal('show');  
       }
      })
   
   });
   
    $(document).on("click", "#submitedit",function (event) {
       var id = $("#id_edit").val();
       var prefix = $("#prefix").val();
       var name = $("#name").val();
       var purchase_type = $("#purchase_type").val();
       var control_account_id = $("#control_account_id").val();
       $.ajax({
         url:"{{url('/sale_type_edit')}}" +'/' + id,
         type: "POST",
         data: {
           id: id,
           prefix: prefix,
           name: name,
           purchase_type: purchase_type,
           control_account_id: control_account_id,
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