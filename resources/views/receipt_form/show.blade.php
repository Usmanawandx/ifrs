@extends('layouts.app')
@section('content')

<section class="content">
  
    @component('components.widget', ['class' => 'box-primary'])
    
    <div>
        <a href="/account/receipt_form" class="btn btn-primary" style="float:right">Create</a>
    </div>
    
    <table class="table table-bordered table-striped ajax_view hide-footer dataTable table-styling table-hover table-primary no-footer">
        <tr>
            <th>Receipt Date</th>
            <th>Customer</th>
            <th>Payment Type</th> 
            <th>Amount</th>
        </tr>
        @foreach($data as $data)
        <tr>
            <td>{{$data->receipt_date}}</td>
            <td>{{$data->supplier_business_name}}</td>
            <td>{{$data->payment_type}}</td>
            <td>{{$data->Amount}}</td>
        </tr>
        @endforeach
        
    </table>
    
    @endcomponent

{!! Form::close() !!}

</section>

@endsection
