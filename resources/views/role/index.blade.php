@extends('layouts.app')
<style>
    
/*    td.sorting_1 a {*/
/*    font-size: 10px !important;*/
/*}*/

/*td.sorting_1 button {*/
/*    font-size: 10px !important;*/
/*}*/

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
@section('title', __('user.roles'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'user.roles' )
        <small>@lang( 'user.manage_roles' )</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'user.all_roles' )])
        @can('roles.create')
            @slot('tool')
                <div class="box-tools">
                    <a class="btn btn-block btn-primary" 
                    href="{{action('RoleController@create')}}" >
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</a>
                </div>
            @endslot
        @endcan
        @can('roles.view')
            <table class="table table-bordered table-striped hide-footer dataTable table-styling table-hover table-primary" id="roles_table">
                <thead>
                    <tr>
                        <th class="main-colum">@lang( 'messages.action' )</th>
                        <th>@lang( 'user.roles' )</th>
                    </tr>
                </thead>
            </table>
        @endcan
    @endcomponent

</section>
<!-- /.content -->
@stop
@section('javascript')
<script type="text/javascript">
    //Roles table
    $(document).ready( function(){
        var roles_table = $('#roles_table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '/roles',
                    buttons:[],
                    // columnDefs: [ {
                    //     "targets": 1,
                    //     "orderable": false,
                    //     "searchable": false
                    // } ]
                    columns : [
                        {data: 'action', name: 'action'},
                        {data: 'name', name: 'name'}
                        ]
                });
        $(document).on('click', 'button.delete_role_button', function(){
            swal({
              title: LANG.sure,
              text: LANG.confirm_delete_role,
              icon: "warning",
              buttons: true,
              dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();

                    $.ajax({
                        method: "DELETE",
                        url: href,
                        dataType: "json",
                        data: data,
                        success: function(result){
                            if(result.success == true){
                                toastr.success(result.msg);
                                roles_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
