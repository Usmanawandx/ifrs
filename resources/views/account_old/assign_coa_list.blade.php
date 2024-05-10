@extends('layouts.app')
<style>

.btn-edt {
    font-size: 14px !important;
    padding: 7px 8px 9px !important;
    border-radius: 50px !important;
}

.btn-vew {
    font-size: 14px !important;
    padding: 8px 8px 10px !important;
    border-radius: 50px !important;
}

.btn-dlt {
    font-size: 14px !important;
    padding: 7px 8px 9px !important;
    border-radius: 50px !important;
}
    
</style>
@section('title', 'Assign Chart Of Account')
@section('content')
<section class="content-header">
    <h1>Assign Chart Of Account</h1>
</section>
<section class="content">
       @component('components.widget', ['class' => 'box-primary', 'title' => 'Assign Chart Of Account'])
        @slot('tool')
            <div class="box-tools">
                <a class="btn btn-block btn-primary" href="{{action('AccountController@assign_coa')}}">
                <i class="fa fa-plus"></i> @lang('messages.add')</a>
            </div>
        @endslot
      <table class="table table-bordered table-striped ajax_view hide-footer dataTable table-styling table-hover table-primary" id="purchase_order_table" style="width: 100%;">
         <thead>
            <tr>
                <th class="main-colum">Action</th>
               <th>SNo#</th>
               <th>Role</th>
               
            </tr>
         </thead>
         <tbody>
            @foreach($assign_coa_list as $v)
            <tr>
               <td> 
                    <a href="/account/assign_coa_edit/{{ $v->role_id }}" class="btn btn-sm btn-success btn-edt" ><i class="fas fa-edit"></i></a>
                    <a href="/account/assign_coa_delete/{{ $v->role_id }}" class="btn btn-sm btn-danger btn-dlt" ><i class="fas fa-trash"></i></a>
               </td>
               <td>{{$loop->iteration}}</td>
               <td>{{$v->name}}</td>

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