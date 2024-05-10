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
@section('title', __('lang_v1.purchase_order'))
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>Purchase Type</h1>
</section>
<!-- Main content -->
<div class="box-body" style="background: white">
   <section class="content no-print">
      @component('components.widget', ['class' => 'box-primary', 'title' => 'Purchase Type'])
      @if(session()->has('message'))
      <div class="alert alert-success">
         {{ session()->get('message') }}
      </div>
      @endif
      {{-- {{ session('message') }} --}}
      @slot('tool')
      @if (auth()->user()->can('purchase_catagory.create')) 
      <div class="box-tools" >
         <a class="btn btn-block btn-primary" href="#"  class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
         <i class="fa fa-plus"></i> @lang('messages.add')</a>
      </div>
      @endif

      @endslot
      <table class="table table-bordered table-striped ajax_view hide-footer dataTable table-styling table-hover table-primary" id="purchase_order_table" style="width: 100%;">
         <thead>
            <tr>
               <th class="main-colum">Action</th>
               <th>Id</th>
               <th>Prefix</th>
               <th>Type</th>
            </tr>
         </thead>
         <tbody>
            @foreach ($purchase_type as $type)
            <tr>
               <td>
               @if (auth()->user()->can('purchase_catagory.update')) 
              
                  <button type="button"  class="btn-primary btn btn-primary btn-edt"> <a href="" id="editCompany" style="color: white" data-toggle="modal" data-target='#practice_modal' data-id="{{ $type->id }}"><i class="glyphicon glyphicon-edit"></i></a></button>
               @endif

               @if (auth()->user()->can('purchase_catagory.delete')) 
                  <button type="button"  class="btn btn-danger btn-dlt" onclick="return confirm('Are you sure to delete?')"><a href="{{ url('/purchase_type', ['id' => $type->id]) }}" style="color: white"><i class="glyphicon glyphicon-trash"></i></a></button>
               @endif
               </td>
               <td>{{$loop->iteration}}</td>
               <td>{{$type->prefix}}</td>
               <td>{{$type->Type}}</td>
               @endforeach
         </tbody>
      </table>
      @endcomponent
   </section>
</div>
<div class="modal fade" id="practice_modal">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h5 class="modal-title">Edit Purchase Type</h5>
         </div>
         <form id="companydata">
            <div class="modal-body">
               <input type="hidden" id="id_edit" name="id">
               <div class="form-group">
                  <label for="prefix">Prefix</label>
                  <input type="text" name="prefix" id="prefix" class="form-control">
               </div>
               <div class="form-group">
                  <label for="Type">Type</label>
                  <input type="text" name="Type" id="Type" class="form-control">
               </div>
               <div class="form-group">
                  {!! Form::label('control_account_id', 'Control Account') !!}
                  <select class="form-control select2" name="control_account_id" id="control_account_id">
                     <option value="" selected disabled>Select Please</option>
                     @foreach($control_account as $key => $val)
                     <option value="{{ $val->id }}">{{ $val->name }}</option>
                     @endforeach
                  </select>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
               <button type="button" id="submitedit" class="btn btn-primary">Submit</button>
            </div>
         </form>
      </div>
   </div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            <h5 class="modal-title" id="exampleModalLabel">Add Purchase Type</h5>
         </div>
         <div class="modal-body">
            {!! Form::open(['url' => action('PurchaseOrderController@purchase_store'), 'method' => 'post', 'id' => 'add_purchase_form', 'files' => true ]) !!}
            <div class="form-group">
               {!! Form::label('Prefix') !!}
               {!! Form::text('prefix', null, ['class' => 'form-control']); !!}
            </div>
            <div class="form-group">
               {!! Form::label('Remarks',__('Type')) !!}
               {!! Form::text('type', null, ['class' => 'form-control', 'rows' => 2]); !!}
            </div>
            <div class="form-group">
               {!! Form::label('control_account_id','Control Account') !!}
               <select class="form-control select2" name="control_account_id" id="control_account_id">
                  <option value="" selected disabledd>Select Please</option>
                  @foreach($control_account as $key => $val)
                  <option value="{{ $val->id }}">{{ $val->name }}</option>
                  @endforeach
               </select>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
           <button type="submit" class="btn btn-primary">Submit</button>
         </div>
         
        {!! Form::close() !!}
      </div>
   </div>
</div>
<!-- /.content -->
@endsection
@section('javascript')
<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">
   $(document).ready(function(){
       
       $('.dataTable').DataTable();
       
        $(document).on("click", "#editCompany",function (event) {
           var id = $(this).data('id');
           $.ajax({
           type: "GET",
           url:"{{url('/purchase_type_update')}}" +'/' + id + '/' + 'edit' ,
           success:function(data){
               $('#id_edit').val(data.id);
               $('#Type').val(data.Type);
               $('#prefix').val(data.prefix);
               if ($('#control_account_id').data('select2')) {
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
           var Type = $("#Type").val();
           var prefix = $("#prefix").val();
           var control_account_id = $("#control_account_id").val();
          
           $.ajax({
             url:"{{url('/purchase_type_edit')}}" +'/' + id,
             type: "POST",
             data: {
               id: id,
               Type: Type,
               prefix: prefix,
               control_account_id:control_account_id,
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