@extends('layouts.app')
@section('title','Milling Details')

@section('content')

@php
	$custom_labels = json_decode(session('business.custom_labels'), true);
@endphp
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Milling Details <i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true" data-container="body" data-toggle="popover" data-placement="bottom" data-content="@include('purchase.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover" data-original-title="" title=""></i></h1>
</section>

<!-- Main content -->
<section class="content">
	@include('layouts.partials.error')

    @component('components.widget', ['class' => 'box-primary'])

    {{-- <h4>Milling Details</h4> --}}



    {!! Form::open(['url' => action('sodaController@millingdetail_store'), 'method' => 'post', 'id' => 'add_purchase_form', 'files' => true ]) !!}
     
        <div class="row">

            {{-- <div class="col-sm-3">
				<div class="form-group">
					{!! Form::label('ref_no:', __('Transaction No').':') !!}
					<input type="text" value="{{"MD-".$md_id}}" class="form-control" name="ref_no" required>
				</div>
			</div> --}}

            <div class="col-sm-3">
				
				<div class="form-group">
					{!! Form::label('Party_name:', __('Party name').':') !!}

					<select  class="form-control select2" class="form-control" name="party_name" required>
                        <option disabled selected>Please Select Item</option>
                        @foreach ($contact as $p)
                        <option value="{{$p->id}}">{{$p->supplier_business_name}}</option>
                        @endforeach
                    </select>

				</div>
			</div>



            <div class="col-sm-3">
				<div class="form-group">
					{!! Form::label('milling_rate', __('Milling Rate').':') !!}
					<input type="text" class="form-control" name="miling_rate" required>
				</div>
			</div>

            <div class="col-sm-3">
				<div class="form-group">
					{!! Form::label('oil_rate', __('Oil Rate').':') !!}
					<input type="text" class="form-control" name="oil_rate" required>
				</div>
			</div>

            <div class="col-sm-3">
				<div class="form-group">
					{!! Form::label('product_id', 'Products'.':') !!}
                    <select  class="form-control select2" name="product_id" required>
                        <option disabled selected>Please Select Item</option>
                        @foreach ($product as $p)
                        <option value="{{$p->id}}">{{$p->name}}</option>
                        @endforeach
                    </select>
				</div>
			</div>

            <div class="col-sm-3">
				<div class="form-group">
					{!! Form::label('gross_weight', __('Gross weight').':') !!}
					<input type="text" class="form-control" name="gross_weight" required>
				</div>
			</div>

            <div class="col-sm-3">
				<div class="form-group">
					{!! Form::label('empty_weight', __('Empty Weight').':') !!}
					<input type="text" class="form-control" name="empty_weight" required>
				</div>
			</div>



			<div class="col-sm-3">
				<div class="form-group">
					{!! Form::label('net_weight', __('Net Weight').':') !!}
					<input type="text" class="form-control" name="net_weight" required>
				</div>
			</div>

			<div class="col-sm-3">
				<div class="form-group">
					{!! Form::label('empty_rate', __('Empty Rate').':') !!}
					<input type="text" class="form-control" name="empty_rate" required>
				</div>
			</div>


			<div class="col-sm-3">
				<div class="form-group">
					{!! Form::label('unit_price', __('Unit Price').':') !!}
					<input type="text" class="form-control" name="unit_price" id="unit_price" required>
				</div>
			</div>


			<div class="col-sm-3">
				<div class="form-group">
					{!! Form::label('qty', __('Qty').':') !!}
					<input type="text" class="form-control" name="qty" id="qty" required>
				</div>
			</div>



			<div class="col-sm-3">
				<div class="form-group">
					{!! Form::label('amount', __('Amount').':') !!}
					<input type="text" class="form-control" name="amount" id="t_amount" required>
				</div>
			</div>


			<div class="col-sm-3">
				<div class="form-group">
					{!! Form::label('description', __('Description').':') !!}
					<input type="text" class="form-control" name="description" required>
				</div>
			</div>


        </div>








      
        <input type="submit" class="btn btn-primary">
        
        {!! Form::close() !!}



    @endcomponent

	@section('javascript')	
	
<script>
	$(document).ready( function(){
		// alert("Sasss");
		$("#unit_price,#qty").on("keyup",function()
		{
			var unit_price=parseInt($('#unit_price').val());
			var qty=parseInt($('#qty').val());
			// alert(unit_price);
			var result=(unit_price * qty);

			$("#t_amount").val(result)



			// alert("Sa");
		});
	
	});
</script>
@endsection



</section>


@endsection
