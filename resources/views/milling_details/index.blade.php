@extends('layouts.app')
@section('title', __('lang_v1.purchase_order'))
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
</section>

<!-- Main content -->
<div class="box-body" style="background: white;margin: 22px;">
    <h1>Add Store</h1>
    <section>
        @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
        @endif
        <div class="box-tools">
            <a class="btn btn-block btn-primary" href="{{action('sodaController@millingdetail_create')}}" style="width: 90px">
            <i class="fa fa-plus"></i> @lang('messages.add')</a>
        </div>
        <br>
            <table class="table table-bordered table-striped ajax_view" id="purchase_order_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>S#</th>
                        <th>Party Name</th>
                        <th>Milling Rate</th>
                        <th>Oil Rate</th>
                        <th>Description</th>
                        <th>Product</th>
                        <th>Gross weight</th>
                        <th>Empty Weight</th>
                        <th>Net Weight</th>
                        <th>Unit Price</th>
                        <th>Qty</th>
                        <th>Amount</th>
                    </tr>    
                </thead>
                <tbody>
                    @foreach ($milling_details as $s)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$s->party_name}}</td>
                        <td>{{$s->miling_rate}}</td>
                        <td>{{$s->oil_rate}}</td>
                        <td>{{$s->description}}</td>
                        <td>{{$s->product_id}}</td>
                        <td>{{$s->gross_weight}}</td>
                        <td>{{$s->empty_weight}}</td>
                        <td>{{$s->net_weight}}</td>
                        <td>{{$s->unit_price}}</td>
                        <td>{{$s->qty}}</td>
                        <td>{{$s->amount}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
    </section>
</div>

<!-- /.content -->
@endsection