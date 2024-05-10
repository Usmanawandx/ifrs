@extends('layouts.app')
@section('title', __('lang_v1.purchase_order'))
@section('content')
<!-- Content Header (Page header) -->

<!-- Main content -->
 <section class="content-header">
   <h1>Vehicle Rate Update</h1>
 </section>
 <!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary'])
    
        {!! Form::open(['url' => action('VehicleRateController@update',[$vehicle_rates['id']]), 'method' => 'post', 'id' => 'vehicle_rates', 'files' => true ]) !!}
        
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                       <label>Date</label>
                       <input type="text" name="date" class="form-control" value="{{ $vehicle_rates['date'] }}" readonly/>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                       <label>Transporter Name</label>
                       <select name="vehicle" class="form-control">
                            <option disabled selected>Please Select</option>
                            @foreach($vehicle as $c)
                              <option value="{{$c->id}}" {{ ($c->id == $vehicle_rates['vehicle_id']) ? 'selected' : '' }}>{{$c->supplier_business_name}}</option>
                            @endforeach
                       </select>
                    </div>
                </div>
                
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped vehicle_rates">
                         <thead>
                            <tr>
                               <th>Product Category Name</th>
                               <th>vehicle Rate</th>
                               <th>Action</th>
                            </tr>
                         </thead>
                         <tbody>
                             @foreach($vehicle_child as $child)
                             <tr>
                                 <td>
                                    <select name="child_id[]" class="form-control">
                                        <option disabled selected>Please Select</option>
                                        @foreach($sub as $s)
                                          <option value="{{$s->id}}" {{ ($s->id == $child->child_id) ? 'selected' : '' }}>{{$s->name}}</option>
                                        @endforeach
                                    </select>
                                 </td>
                                 <td>
                                    <input type="text" name="vehicle_rate[]" class="form-control" required value="{{ $child->rate }}"/>
                                 </td>
                                 <td>
                                    <button class="btn btn-primary btn-sm add_vehicle_btn" type="button">+</button>
                                    <button class="btn btn-danger btn-sm remove_vehicle_btn" type="button">-</button>
                                 </td>
                             </tr>
                            @endforeach
                         </tbody>
                    </table>
                </div>
                
                
            </div>
            
            <button type="submit" class="btn btn-primary">Submit</button>
            
        {!! Form::close() !!}
    @endcomponent
</section>
<!-- /.content -->
@endsection
@section('javascript')
<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">
   $(document).ready(function(){
       
        $(document).on("click",".add_vehicle_btn",function(){
            $('tr:last').clone().insertAfter('tr:last').find('input').val('');
        });
           
        $(document).on("click",".remove_vehicle_btn",function(el){
            if($('.vehicle_rates tbody tr').length < 2){
               alert("Atleast One Row Is Required");
            }else{
               $(this).closest('tr').remove();
            }
        });
   
   
   });
    
   
</script>
@endsection