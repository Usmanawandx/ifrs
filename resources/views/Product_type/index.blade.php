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
    <section class="content-header no-print">
        <h1>Product Type</h1>
    </section>
    <!-- Main content -->
    <div class="box-body" style="background: white">

        <section class="content no-print">
            @component('components.widget', ['class' => 'box-primary', 'title' => 'Product Type'])
                @if (session()->has('message'))
                    <div class="alert alert-success">
                        {{ session()->get('message') }}
                    </div>
                @endif
                @slot('tool')
                    @can('pro_type.create')
                        <div class="box-tools">
                            <a class="btn btn-block btn-primary" href="#" class="btn btn-primary" data-toggle="modal"
                                data-target="#saleModal">
                                <i class="fa fa-plus"></i> @lang('messages.add')</a>
                        </div>
                    @endcan
                @endslot
                <table
                    class="table table-bordered table-striped ajax_view hide-footer dataTable table-styling table-hover table-primary"
                    id="purchase_order_table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="main-colum">Action</th>
                            <th>Id</th>
                            <th>Type</th>
                            <th>Purchase Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $type)
                            <tr>
                                <td>
                                    @can('pro_type.update')
                                        <a class="btn btn-primary btn-edt" href="" id="editCompany" style="color: white"
                                            data-toggle="modal" data-target='#practice_modal' data-id="{{ $type->id }}"><i
                                                class="glyphicon glyphicon-edit"></i></a>
                                    @endcan
                                    @can('pro_type.delete')
                                        <a class="btn btn-danger btn-dlt" href="{{ url('/delete_type', ['id' => $type->id]) }}"
                                            style="color: white" onclick="return confirm('Are you sure to delete?')"><i
                                                class="glyphicon glyphicon-trash"></i></a>
                                    @endcan
                                </td>
                                <td>{{ $loop->iteration }}</td>
                                <td>{!! $type->name !!}</td>
                                <td>{!! $type->purchase_type !!}</td>
                        @endforeach
                    </tbody>
                </table>
            @endcomponent
        </section>
    </div>
    <div class="modal fade" id="practice_modal">
        <div class="modal-dialog">
            <form id="companydata">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h5 class="modal-title" id="exampleModalLabel">Update Product Type</h5>
                    </div>
                    <input type="hidden" id="id_edit" name="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Purchase Type</label>
                            <select class="form-control purchase_category" id="purchase_type_edit" name="purchase_category"
                                required>
                                <option selected disabled> Select</option>
                                @foreach ($purchase_category as $tp)
                                    <option value="{{ $tp->id }}" data-pf="{{ $tp->prefix }}">{{ $tp->Type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            {!! Form::label('name', __('Type') . ':') !!}
                            {!! Form::text('name', '', ['class' => 'form-control name', 'id' => 'name']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('prefix', __('Prefix') . ':') !!}
                            {!! Form::text('prefix', null, ['class' => 'form-control ', 'id' => 'prefix_edit']) !!}
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-primary" value="Submit" id="submitedit">Submit</button>
                        </div>
                    </div>
                </div>
        </div>
        </form>
    </div>
    </div>
    <div class="modal fade" id="saleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="exampleModalLabel">Add Product Type</h5>
                </div>
                <div class="modal-body">
                    {!! Form::open([
                        'url' => action('TypeController@store'),
                        'method' => 'post',
                        'id' => 'add_purchase_form',
                        'files' => true,
                    ]) !!}
                    <div class="form-group">
                        <label>Purchase Type</label>
                        <div class="input-group">
                            <select class="form-control purchase_category" name="purchase_category" required>
                                <option selected disabled> Select</option>
                                @foreach ($purchase_category as $tp)
                                    <option value="{{ $tp->id }}" data-pf="{{ $tp->prefix }}">{{ $tp->Type }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default bg-white btn-flat btn-modal"
                                    data-href="{{ action('PurchaseOrderController@Purchase_type_partial') }}"
                                    data-container=".view_modal"><i
                                        class="fa fa-plus-circle text-primary fa-lg"></i></button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="hidden" value="none" name="type">
                        {!! Form::label('name', __('Type') . ':') !!}
                        {!! Form::text('name', null, ['class' => 'form-control ']) !!}
                    </div>
                    <div class="form-group hide">
                        {!! Form::label('prefix', __('Prefix') . ':') !!}
                        {!! Form::text('prefix', null, ['class' => 'form-control ']) !!}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                    {!! Form::close() !!}
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content -->
@endsection
@section('javascript')
    <script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.dataTable').DataTable();

            $(document).on("click", "#editCompany", function(event) {
                console.log("sa");
                var id = $(this).data('id');
                console.log(id)
                $.ajax({
                    type: "GET",
                    url: "{{ url('/type_edit') }}" + '/' + id + '/' + 'edit',
                    success: function(data) {
                        $('#id_edit').val(data.id);
                        $('.name').val(data.name);
                        $('#purchase_type_edit').val(data.purchase_type);
                        $('#prefix_edit').val(data.prefix);
                    }
                })
            });



            $(document).on("click", "#submitedit", function(event) {
                var id = $("#id_edit").val();
                var name = $(".name").val();
                var purchase_category = $("#purchase_type_edit").val();
                var prefix = $("#prefix_edit").val();

                $.ajax({
                    url: "{{ url('/type_update') }}" + '/' + id,
                    type: "POST",
                    data: {
                        id: id,
                        name: name,
                        purchase_category: purchase_category,
                        prefix: prefix
                    },
                    dataType: 'json',
                    success: function(data) {
                        $('#companydata').trigger("reset");
                        $('#practice_modal').modal('hide');
                        window.location.reload(true);
                    }
                });
            });

        });
    </script>
@endsection
