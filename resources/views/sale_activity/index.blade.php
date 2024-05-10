@extends('layouts.app')
@section('title',"Sale Activity")
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> Sale Activity
        <small>Add Your Sale Activities</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-bd lobidrag">
                    <form action="{{route('sale_activity.add')}}" method="post">
                        @csrf
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label for="activity_date" class="col-sm-4 col-form-label">Activity Date <i class="text-danger">*</i></label>
                                    <div class="col-sm-8">
                                        <input class="form-control" name="activity_date" type="date" id="activity_date" placeholder="Please Select Activity Date" required tabindex="1" >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label for="doctor_name" class="col-sm-4 col-form-label">Doctor Name</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" id="doctor_name" name="doctor_name" tabindex="-1" aria-hidden="true">
                                            <option value="">Select Doctor</option>
                                            @foreach($doctors as $doctor)
                                            <option value="{{$doctor->id}}">{{$doctor->name}}</option>                                                    
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--<div class="col-sm-6">-->
                            <!--    <div class="form-group row">-->
                            <!--        <label for="city" class="col-sm-4 col-form-label">City Name</label>-->
                            <!--        <div class="col-sm-8">-->
                            <!--            <select class="form-control" id="city" name="city" tabindex="-1" aria-hidden="true">-->
                            <!--                <option value="">Select City</option>-->
                            <!--            </select>-->
                            <!--        </div>-->
                            <!--    </div>-->
                            <!--</div>-->
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label for="specialty" class="col-sm-4 col-form-label">Specialty<i class="text-danger">*</i></label>
                                    <div class="col-sm-8">
                                        <input class="form-control" name="specialty" type="text" id="specialty" placeholder="Please Enter Specialty" value="" required tabindex="1" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label for="invested_amount" class="col-sm-4 col-form-label">Invested Amount</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" name="invested_amount" type="number" min="0" id="invested_amount" placeholder="Please Enter Invested Amount" value="" required tabindex="1" >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!--<div class="col-sm-6">-->
                            <!--    <div class="form-group row">-->
                            <!--        <label for="chemist_name" class="col-sm-4 col-form-label">Chemist Name (Brick)<i class="text-danger">*</i></label>-->
                            <!--        <div class="col-sm-8">-->
                            <!--            <select class="form-control" id="chemist_name" name="chemist_name" tabindex="-1" aria-hidden="true">-->
                            <!--                <option value="">Select Chemist</option>-->
                            <!--            </select>-->
                            <!--        </div>-->
                            <!--    </div>-->
                            <!--</div>-->
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label for="committed_products" class="col-sm-4 col-form-label">Committed Products</label>
                                    <div class="col-sm-8">
                                       <select class="form-control" id="committed_products" name="committed_products[]" tabindex="-1" aria-hidden="true" multiple>
                                            @foreach($products as $product)
                                            <option value="{{$product->id}}">{{$product->name}}</option>                                                    
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group row">
                                    <label for="tp" class="col-sm-4 col-form-label">Commission<i class="text-danger">*</i></label>
                                    <div class="col-sm-8">
                                        <input class="form-control" name="commission" type="number" min="0" max="100" id="commission"  tabindex="1" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group row">
                                    <label for="tp" class="col-sm-4 col-form-label">TP<i class="text-danger">*</i></label>
                                    <div class="col-sm-8">
                                        <input class="form-control" name="tp" type="number" min="0" id="tp"  tabindex="1" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group row">
                                    <label for="monthly_unit" class="col-sm-4 col-form-label">Monthly Units</label>
                                    <div class="col-sm-8">
                                       <input class="form-control" name="monthly_unit" type="number" min="0" id="monthly_unit"  tabindex="1" >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-info">Save</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
</section>
<!-- /.content -->
@stop
@section('javascript')
    // <script>
    //     $(document).ready(function(){
    //         $('#doctor_name').on('change',function(){
    //             var id = $(this).val();
    //             $.ajax({
    // 				url: "{{route('getCities')}}",
    // 				data: {
    // 				    'id':id
    // 				},
    // 				success: function(result){
    // 				    res=JSON.parse(result);
    // 				    var option = '';
    // 				    $(res).each(function(i,e) {
    //                       option += `<option value="${e.city}">${e.city}</option>`;
    //                     });
    //                     $('#city').html(option);
    // 				}
    // 			});
    // 			$.ajax({
    // 				url: "{{route('getDoctors')}}",
    // 				data: {
    // 				    'id':id
    // 				},
    // 				success: function(result){
    // 				    res=JSON.parse(result);
    // 				    var option = '';
    // 				    $(res).each(function(i,e) {
    //                       option += `<option value="${e.id}">${e.first_name} ${e.last_name}</option>`;
    //                     });
    //                     $('#chemist_name').html(option);
    // 				}
    // 			});
    //         })
    //     })
    // </script>
@endsection
