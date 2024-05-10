@extends('layouts.app')
@section('title', __('lang_v1.purchase_order'))
@section('content')
<!-- Content Header (Page header) -->

<!-- Main content -->
 <section class="content-header">
   <h1>Vehicle Rate Create</h1>
 </section>
 <!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary'])
    
        {!! Form::open(['url' => action('VehicleRateController@store'), 'method' => 'post', 'id' => 'VehicleRate', 'files' => true ]) !!}
            
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                       <label>Date</label>
                       <input type="date" name="date" class="form-control" required/>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                       <label>Transporter Name</label>
                       <select name="vehicle" class="form-control">
                            <option disabled selected>Please Select</option>
                            @foreach($vehicle as $c)
                              <option value="{{$c->id}}">{{$c->supplier_business_name}}</option>
                            @endforeach
                       </select>
                    </div>
                </div>
                
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped vehicle_rates">
                         <thead>
                            <tr>
                               <th width="20">Action</th>
                               <th width="60">Sub Category Name</th>
                               <th width="20">Vehicle Rate</th>
                            </tr>
                         </thead>
                         <tbody>
                             <tr>
                                 <td>
                                    <button class="btn btn-primary btn-sm add_vehicle_btn" type="button">+</button>
                                    <button class="btn btn-danger btn-sm remove_vehicle_btn" type="button">-</button>
                                 </td>
                                 <td>
                                    <select name="child_id[]" class="form-control sa select2">
                                        <option disabled selected>Please Select</option>
                                        @foreach($sub as $s)
                                          <option value="{{$s->id}}">{{$s->name}}</option>
                                        @endforeach
                                    </select>
                                 </td>
                                 <td>
                                    <input type="text" name="vehicle_rate[]" required class="form-control"/>
                                 </td>
                                 
                             </tr>
                           
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
        $('.vehicle_rates tbody tr').each(function(){
            $(this).find('.sa').select2('destroy');
        })

        $('tr:last').clone().insertAfter('tr:last').find('input').val('');
        $('.vehicle_rates').find('.sa').select2();
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