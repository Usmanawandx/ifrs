@extends('layouts.app')
@section('title', '')
@section('content')

<section class="content">
       @component('components.widget', ['class' => 'box-primary', 'title' => ''])
       
      <table class="table table-bordered table-striped ajax_view" id="purchase_order_table" style="width: 100%;">
         <thead>
            <tr>
               <th>Contacts.ID</th>
               <th>Contacts.Name</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
            @foreach($data as $v)
            <tr>
               <td>{{$v->id}}</td>
               <td>{{$v->supplier_business_name}}</td>
               <td>
                    <a href="#" class="btn btn-primary" id="">Update</a>
               </td>
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
</script>
@endsection