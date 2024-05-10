@extends('layouts.app')
@section('title', __('product.add_new_product'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="top-heading">@lang('product.add_new_product')</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
@php
  $form_class = empty($duplicate_product) ? 'create' : '';
@endphp
{!! Form::open(['url' => action('ProductController@store'), 'method' => 'post', 
    'id' => 'product_add_form','class' => 'product_form ' . $form_class, 'files' => true ]) !!}
    @component('components.widget', ['class' => 'box-primary'])
        <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('name', __('product.product_name') . ':*') !!}
              {!! Form::text('name', !empty($duplicate_product->name) ? $duplicate_product->name : null, ['class' => 'form-control', 'required',
              'placeholder' => __('product.product_name')]); !!}
          </div>
        </div>

        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('sku', __('product.sku') . ':') !!} @show_tooltip(__('tooltip.sku'))

            <input type="hidden" name="prefix" value="{{ $sku_prefixx.'-' }}" class="input-group-addon trn_prefix">
            <div class="input-group">
              <span class="input-group-addon trn_prefix_addon text-uppercase">
                {{ $sku_prefixx.'-' }}
              </span>
         
            {!! Form::text('sku', null, ['class' => 'form-control','placeholder' => __('product.sku')]); !!}
              
          </div>
        </div>
        </div>
   

        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('barcode_type', __('product.barcode_type') . ':*') !!}
              {!! Form::select('barcode_type', $barcode_types, !empty($duplicate_product->barcode_type) ? $duplicate_product->barcode_type : $barcode_default, ['class' => 'form-control select2', 'required']); !!}
          </div>
        </div>

      <div class="clearfix"></div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('unit_id', __('product.unit') . ':*') !!}
            <div class="input-group">
              {!! Form::select('unit_id', $units, !empty($duplicate_product->unit_id) ? $duplicate_product->unit_id : session('business.default_unit'), ['class' => 'form-control select2', 'required']); !!}
              <span class="input-group-btn">
                <button type="button" @if(!auth()->user()->can('unit.create')) disabled @endif class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action('UnitController@create', ['quick_add' => true])}}" title="@lang('unit.add_unit')" data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
              </span>
            </div>
          </div>
        </div>
        
        <div class="col-sm-4 @if(!session('business.enable_sub_units')) hide @endif">
          <div class="form-group">
            {!! Form::label('sub_unit_ids', __('lang_v1.related_sub_units') . ':') !!} @show_tooltip(__('lang_v1.sub_units_tooltip'))

            {!! Form::select('sub_unit_ids[]', [], !empty($duplicate_product->sub_unit_ids) ? $duplicate_product->sub_unit_ids : null, ['class' => 'form-control select2', 'multiple', 'id' => 'sub_unit_ids']); !!}
          </div>
        </div>

        <div class="col-sm-4 @if(!session('business.enable_brand')) hide @endif">
          <div class="form-group">
            {!! Form::label('brand_id', __('product.brand') . ':') !!}
            <div class="input-group">
              {!! Form::select('brand_id', $brands, !empty($duplicate_product->brand_id) ? $duplicate_product->brand_id : null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2']); !!}
            <span class="input-group-btn">
                <button type="button" @if(!auth()->user()->can('brand.create')) disabled @endif class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action('BrandController@create', ['quick_add' => true])}}" title="@lang('brand.add_brand')" data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
              </span>
            </div>
          </div>
        </div>
        
        <div class="col-sm-4 @if(!empty($duplicate_product) && $duplicate_product->enable_stock == 0) hide @endif" id="alert_quantity_div">
          <div class="form-group">
            {!! Form::label('alert_quantity',  __('product.alert_quantity') . ':') !!} @show_tooltip(__('tooltip.alert_quantity'))
            {!! Form::text('alert_quantity', !empty($duplicate_product->alert_quantity) ? @format_quantity($duplicate_product->alert_quantity) : null , ['class' => 'form-control input_number',
            'placeholder' => __('product.alert_quantity'), 'min' => '0']); !!}
          </div>
        </div>

        <div class="clearfix"></div>

        
        <div class="col-sm-4 @if(!session('business.enable_category')) hide @endif">
          <div class="form-group">
            {!! Form::label('category_id', 'PURCHASE CATEGORY' . ':') !!}
            <div class="input-group">
              {!! Form::select('category_id', $main,null,['placeholder' => __('messages.please_select'), 'class' => 'form-control categormain select2']); !!}
              <span class="input-group-btn">
                <button type="button" class="btn btn-default bg-white btn-flat btn-modal" data-toggle="modal" data-target="#exampleModalCenter">
                  <i class="fa fa-plus-circle text-primary fa-lg"></i>
                </button>
              </span>
            </div>
            </div>
        </div>

        <div class="col-sm-4 @if(!(session('business.enable_category') && session('business.enable_sub_category'))) hide @endif">
          <div class="form-group">
            {!! Form::label('sub_category_id', __('product.sub_category') . ':') !!}
            <div class="input-group">
              {!! Form::select('sub_category_id', $sub_categories, !empty($duplicate_product->sub_category_id) ? $duplicate_product->sub_category_id : null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control sub select2']); !!}
              <span class="input-group-btn">
                <button type="button" class="btn btn-default bg-white btn-flat btn-modal" data-toggle="modal" data-target="#exampleModalCenter2">
                  <i class="fa fa-plus-circle text-primary fa-lg"></i>
                </button>
              </span>
            </div>
            </div>
        </div>


         <div class="col-sm-4 @if(!(session('business.enable_category') && session('business.enable_sub_category'))) hide @endif">
          <div class="form-group">
            {!! Form::label('child_cat', __('Product Category') . ':') !!}
              {{-- {!! Form::select('child_cat', $sub_categories, !empty($duplicate_product->sub_category_id) ? $duplicate_product->sub_category_id : null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control sub select2']); !!} --}}
          <div class="input-group">
          <select name="child_cat" class="form-control child select2">
           <option disabled selected>Please Select</option>

          </select>
          <span class="input-group-btn">
            <button type="button" class="btn btn-default bg-white btn-flat btn-modal" data-toggle="modal" data-target="#exampleModalCenter3">
              <i class="fa fa-plus-circle text-primary fa-lg"></i>
            </button>
          </span>
        </div> 
          
            </div>
        </div>
        
        <div class="col-sm-4">
          <div class="form-group">
              {!! Form::label('product_type', __('Product Type') . ':') !!} @show_tooltip(__('lang_v1.product_location_help'))
              {{-- {!! Form::select('product_type',$type,null,['class' => 'form-control select2','placeholder'=>"Please select",'id' => 'prod_type']); !!} --}}
              <select name="product_type" id="prod_type" class="form-control select2" required>
                <option selected disabled> Select</option>
                @foreach ($type as $tp)
                <option value="{{$tp->id}}" data-pf="{{$tp->prefix}}">{{$tp->name}}</option>
                @endforeach
              </select>
          </div>
        </div>
        
        
        
        <div class="col-sm-4">
          <div class="form-group">
              {!! Form::label('product_type', 'Milling Category' . ':') !!} 
              <select name="milling_category" id="milling_category" class="form-control select2">
                <option selected disabled> Select</option>
                @foreach ($milling_category as $mill)
                    <option value="{{$mill->id}}">{{$mill->name}}</option>
                @endforeach
              </select>
          </div>
        </div>
        
        
        

        @php
          $default_location = null;
          if(count($business_locations) == 1){
            $default_location = array_key_first($business_locations->toArray());
          }
        @endphp
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('product_locations', __('business.business_locations') . ':') !!} @show_tooltip(__('lang_v1.product_location_help'))
              {!! Form::select('product_locations[]', $business_locations, $default_location, ['class' => 'form-control select2', 'multiple', 'id' => 'product_locations']); !!}
          </div>
        </div>

        

        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('image', __('lang_v1.product_image') . ':') !!}
            {!! Form::file('image', ['id' => 'upload_image', 'accept' => 'image/*']); !!}
            <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small>
          </div>
        </div>

        <div class="col-sm-4 hide">
          <div class="form-group">
            {!! Form::label('types', __('Product Type') . ':') !!} @show_tooltip(__('lang_v1.product_location_help'))
              {!! Form::select('types',$Product_type,null,['class' => 'form-control select2','placeholder'=>"Please select",'id' => 'type']); !!}
          </div>
        </div>

        <div class="clearfix"></div>
        
        <div class="col-sm-4">
          <div class="form-group">
          <br>
            <label>
              {!! Form::checkbox('enable_stock', 1, !empty($duplicate_product) ? $duplicate_product->enable_stock : true, ['class' => 'input-icheck', 'id' => 'enable_stock']); !!} <strong>@lang('product.manage_stock')</strong>
            </label>@show_tooltip(__('tooltip.enable_stock')) <p class="help-block"><i>@lang('product.enable_stock_help')</i></p>
          </div>
        </div>
        
        @if(!empty($common_settings['enable_product_warranty']))
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('warranty_id', __('lang_v1.warranty') . ':') !!}
            {!! Form::select('warranty_id', $warranties, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
          </div>
        </div>
        @endif
        <!-- include module fields -->
        @if(!empty($pos_module_data))
            @foreach($pos_module_data as $key => $value)
                @if(!empty($value['view_path']))
                    @includeIf($value['view_path'], ['view_data' => $value['view_data']])
                @endif
            @endforeach
        @endif
        <div class="clearfix"></div>
        <div class="col-sm-8">
          <div class="form-group">
            {!! Form::label('product_description', __('lang_v1.product_description') . ':') !!}
              {!! Form::textarea('product_description', !empty($duplicate_product->product_description) ? $duplicate_product->product_description : null, ['class' => 'form-control']); !!}
          </div>
        </div>
        
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('product_brochure', __('lang_v1.product_brochure') . ':') !!}
            {!! Form::file('product_brochure', ['id' => 'product_brochure', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
            <small>
                <p class="help-block">
                    @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                    @includeIf('components.document_help_text')
                </p>
            </small>
          </div>
        </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary'])
        <div class="row">
        @if(session('business.enable_product_expiry'))

            @if(session('business.expiry_type') == 'add_expiry')
              @php
                $expiry_period = 12;
                $hide = true;
              @endphp
            @else
              @php
                $expiry_period = null;
                $hide = false;
              @endphp
            @endif
          <div class="col-sm-4 @if($hide) hide @endif">
            <div class="form-group">
              <div class="multi-input">
                {!! Form::label('expiry_period', __('product.expires_in') . ':') !!}<br>
                {!! Form::text('expiry_period', !empty($duplicate_product->expiry_period) ? @num_format($duplicate_product->expiry_period) : $expiry_period, ['class' => 'form-control pull-left input_number',
                  'placeholder' => __('product.expiry_period'), 'style' => 'width:60%;']); !!}
                {!! Form::select('expiry_period_type', ['months'=>__('product.months'), 'days'=>__('product.days'), '' =>__('product.not_applicable') ], !empty($duplicate_product->expiry_period_type) ? $duplicate_product->expiry_period_type : 'months', ['class' => 'form-control select2 pull-left', 'style' => 'width:40%;', 'id' => 'expiry_period_type']); !!}
              </div>
            </div>
          </div>
        @endif

        <div class="col-sm-4">
          <div class="form-group">
            <br>
            <label>
              {!! Form::checkbox('enable_sr_no', 1, !(empty($duplicate_product)) ? $duplicate_product->enable_sr_no : false, ['class' => 'input-icheck']); !!} <strong>@lang('lang_v1.enable_imei_or_sr_no')</strong>
            </label> @show_tooltip(__('lang_v1.tooltip_sr_no'))
          </div>
        </div>

        <div class="col-sm-4">
          <div class="form-group">
            <br>
            <label>
              {!! Form::checkbox('not_for_selling', 1, !(empty($duplicate_product)) ? $duplicate_product->not_for_selling : false, ['class' => 'input-icheck']); !!} <strong>@lang('lang_v1.not_for_selling')</strong>
            </label> @show_tooltip(__('lang_v1.tooltip_not_for_selling'))
          </div>
        </div>

        <div class="clearfix"></div>

        <!-- Rack, Row & position number -->
        @if(session('business.enable_racks') || session('business.enable_row') || session('business.enable_position'))
          <div class="col-md-12">
            <h4>@lang('lang_v1.rack_details'):
              @show_tooltip(__('lang_v1.tooltip_rack_details'))
            </h4>
          </div>
          @foreach($business_locations as $id => $location)
            <div class="col-sm-3">
              <div class="form-group">
                {!! Form::label('rack_' . $id,  $location . ':') !!}
                
                @if(session('business.enable_racks'))
                  {!! Form::text('product_racks[' . $id . '][rack]', !empty($rack_details[$id]['rack']) ? $rack_details[$id]['rack'] : null, ['class' => 'form-control', 'id' => 'rack_' . $id, 
                    'placeholder' => __('lang_v1.rack')]); !!}
                @endif

                @if(session('business.enable_row'))
                  {!! Form::text('product_racks[' . $id . '][row]', !empty($rack_details[$id]['row']) ? $rack_details[$id]['row'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.row')]); !!}
                @endif
                
                @if(session('business.enable_position'))
                  {!! Form::text('product_racks[' . $id . '][position]', !empty($rack_details[$id]['position']) ? $rack_details[$id]['position'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.position')]); !!}
                @endif
              </div>
            </div>
          @endforeach
        @endif
        
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('weight',  __('Gross weight') . ':') !!}
            {!! Form::text('weight', !empty($duplicate_product->weight) ? $duplicate_product->weight : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.weight')]); !!}
          </div>
        </div>
        @php
          $custom_labels = json_decode(session('business.custom_labels'), true);
          $product_custom_field1 = !empty($custom_labels['product']['custom_field_1']) ? $custom_labels['product']['custom_field_1'] : __('lang_v1.product_custom_field1');
          $product_custom_field2 = !empty($custom_labels['product']['custom_field_2']) ? $custom_labels['product']['custom_field_2'] : __('lang_v1.product_custom_field2');
          $product_custom_field3 = !empty($custom_labels['product']['custom_field_3']) ? $custom_labels['product']['custom_field_3'] : __('lang_v1.product_custom_field3');
          $product_custom_field4 = !empty($custom_labels['product']['custom_field_4']) ? $custom_labels['product']['custom_field_4'] : __('lang_v1.product_custom_field4');
        @endphp
        <!--custom fields-->
        <div class="clearfix"></div>
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('product_custom_field1',  __('Net Weight') . ':') !!}
            {!! Form::text('product_custom_field1', !empty($duplicate_product->product_custom_field1) ? $duplicate_product->product_custom_field1 : null, ['class' => 'form-control', 'placeholder' => $product_custom_field1]); !!}
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('product_custom_field2',  $product_custom_field1. ':') !!}
            {!! Form::text('product_custom_field2', !empty($duplicate_product->product_custom_field2) ? $duplicate_product->product_custom_field2 : null, ['class' => 'form-control', 'placeholder' => $product_custom_field2]); !!}
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('product_custom_field3',  $product_custom_field2 . ':') !!}
            {!! Form::text('product_custom_field3', !empty($duplicate_product->product_custom_field3) ? $duplicate_product->product_custom_field3 : null, ['class' => 'form-control', 'placeholder' => $product_custom_field3]); !!}
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('product_custom_field4',  $product_custom_field3 . ':') !!}
            {!! Form::text('product_custom_field4', !empty($duplicate_product->product_custom_field4) ? $duplicate_product->product_custom_field4 : null, ['class' => 'form-control', 'placeholder' => $product_custom_field4]); !!}
          </div>
        </div>
        <!--custom fields-->
        <div class="clearfix"></div>
        @include('layouts.partials.module_form_part')
      </div>
    @endcomponent
    
    
     
     @component('components.widget', ['class' => 'box-primary'])
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <lable>Contractor<lable>
                    <select name="contractor" class="form-control"> 
                        <!--<option disabled selected>Select</option>-->
                        @foreach($contractor as $contr)
                            <option value="{{ $contr['id'] }}">{{ empty($contr['name']) ? $contr['supplier_business_name'] : $contr['name']  }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-group">
                    <lable>Rate<lable>
                    <input type="text" class="form-control" name="rate" />
                </div>
            </div>
        </div>
    @endcomponent
    
    

    @component('components.widget', ['class' => 'box-primary'])
        <div class="row">

        <div class="col-sm-4 @if(!session('business.enable_price_tax')) hide @endif">
          <div class="form-group">
            {!! Form::label('tax', __('product.applicable_tax') . ':') !!}
              {!! Form::select('tax', $taxes, !empty($duplicate_product->tax) ? $duplicate_product->tax : null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2'], $tax_attributes); !!}
          </div>
        </div>

        <div class="col-sm-4 @if(!session('business.enable_price_tax')) hide @endif">
          <div class="form-group">
            {!! Form::label('tax_type', __('product.selling_price_tax_type') . ':*') !!}
              {!! Form::select('tax_type', ['inclusive' => __('product.inclusive'), 'exclusive' => __('product.exclusive')], !empty($duplicate_product->tax_type) ? $duplicate_product->tax_type : 'exclusive',
              ['class' => 'form-control select2', 'required']); !!}
          </div>
        </div>

        <div class="clearfix"></div>

        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('type', __('product.product_type') . ':*') !!} @show_tooltip(__('tooltip.product_type'))
            {!! Form::select('type', $product_types, !empty($duplicate_product->type) ? $duplicate_product->type : null, ['class' => 'form-control select2',
            'required', 'data-action' => !empty($duplicate_product) ? 'duplicate' : 'add', 'data-product_id' => !empty($duplicate_product) ? $duplicate_product->id : '0']); !!}
          </div>
        </div>

        <div class="form-group col-sm-12" id="product_form_part">
          @include('product.partials.single_product_form_part', ['profit_percent' => $default_profit_percent])
        </div>

        <input type="hidden" id="variation_counter" value="1">
        <input type="hidden" id="default_profit_percent" 
          value="{{ $default_profit_percent }}">

      </div>
    @endcomponent
    
    
   
    
    
    
    
    
    
    <div class="row">
    <div class="col-sm-12">
      <input type="hidden" name="submit_type" id="submit_type">
      <div class="text-center">
      <div class="btn-group">
        @if($selling_price_group_count)
          <button type="submit" value="submit_n_add_selling_prices" class="btn btn-warning submit_product_form">@lang('lang_v1.save_n_add_selling_price_group_prices')</button>
        @endif

        @can('product.opening_stock')
        <button id="opening_stock_button" @if(!empty($duplicate_product) && $duplicate_product->enable_stock == 0) disabled @endif type="submit" value="submit_n_add_opening_stock" class="btn bg-purple submit_product_form">@lang('lang_v1.save_n_add_opening_stock')</button>
        @endcan

        <button type="submit" value="save_n_add_another" class="btn bg-maroon submit_product_form">@lang('lang_v1.save_n_add_another')</button>

        <button type="submit" value="submit" class="btn btn-primary submit_product_form">@lang('messages.save')</button>
      </div>
      
      </div>
    </div>
  </div>
{!! Form::close() !!}
  
</section>



<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Category</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="category_form">
          <div class="form-group">
            <label for="cat-name" class="col-form-label">Category:</label>
            <input type="text" class="form-control" name="cat_name" id="cat-name">
          </div>
  
          <button type="button" class="btn btn-primary" id="save_category">Save</button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="exampleModalCenter2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Sub Category</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="category_form">
          <div class="form-group">
            <label for="cat-name" class="col-form-label">Sub Category:</label>
            <input type="text" class="form-control" name="cat_name" id="sub-name" required>
          </div>


          <div class="form-group">
            <label for="cat-name" class="col-form-label">Category:</label>
            {!! Form::select('category_id', $main,null,['placeholder' => __('messages.please_select'),'id'=>'parent_id','required','class' => 'form-control categormain select2','style'=>'width:200px']); !!}
          </div>
  
          <button type="button" class="btn btn-primary" id="save_subcategory">Save</button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
  
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="exampleModalCenter3" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">child Category</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="category_form">
          <div class="form-group">
            <label for="cat-name" class="col-form-label">child Category:</label>
            <input type="text" class="form-control" name="cat_name" id="child-name" required>
          </div>

          <div class="form-group">
            <label for="cat-name" class="col-form-label">Category:</label>
            {!! Form::select('sub_category_id', $sub_categories, !empty($duplicate_product->sub_category_id) ? $duplicate_product->sub_category_id : null, ['placeholder' => __('messages.please_select'),'style'=>'width:200px','id'=>'child_cat','class' => 'form-control sub select2']); !!}
          </div>
  
          <button type="button" class="btn btn-primary" id="save_childcategory">Save</button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
  
      </div>
    </div>
  </div>
</div>


<!-- /.content -->

@endsection

@section('javascript')
  @php $asset_v = env('APP_VERSION'); @endphp
  <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>

    <script type="text/javascript">
        $(document).ready(function(){
            __page_leave_confirmation('#product_add_form');
            onScan.attachTo(document, {
                suffixKeyCodes: [13], // enter-key expected at the end of a scan
                reactToPaste: true, // Compatibility to built-in scanners in paste-mode (as opposed to keyboard-mode)
                onScan: function(sCode, iQty) {
                    $('input#sku').val(sCode);
                },
                onScanError: function(oDebug) {
                    console.log(oDebug); 
                },
                minLength: 2,
                ignoreIfFocusOn: ['input', '.form-control']
                // onKeyDetect: function(iKeyCode){ // output all potentially relevant key events - great for debugging!
                //     console.log('Pressed: ' + iKeyCode);
                // }
            });
        });

        // 
        $(".categormain").change(function(){
    // alert("Sa");
      var id = $(".categormain").val();
      // alert(id);
          $.ajax({
          type: "GET",
          url:'/get_sub/' + id,
          success:function(data){
         console.log(data)
         $('.sub').html('<option disabled selected>Please Select</option>');
 
         $.each(data, function (index, value) {
                  // APPEND OR INSERT DATA TO SELECT ELEMENT.
                  // console.log(value.name);
                if(value.name!=null)
                {
                  $('.sub').append('<option value="' + value.id + '">' + value.name + '</option>');
                }else{

                }

                });
            
          // $('#bk_qty').val(data['booking_qnty']);
  
          }
         })
  });





// 

        $(".sub").change(function(){
    // alert("Sa");
      var id = $(".sub").val();
      // alert(id);
          $.ajax({
          type: "GET",
          url:'/get_child_cat/' + id,
          success:function(data){
         console.log(data)
         $('.child').html('<option disabled selected>Please Select</option>');
 
         $.each(data, function (index, value) {
                  // APPEND OR INSERT DATA TO SELECT ELEMENT.
                  // console.log(value.name);
                if(value.name!=null)
                {
                  $('.child').append('<option value="' + value.id + '">' + value.name + '</option>');
                }else{

                }

                });
            
          // $('#bk_qty').val(data['booking_qnty']);
  
          }
         })
  });


  $("#save_category").click(function(){
    // alert("Sa");
    var category = $("#cat-name").val();
  if(category=="" )
    {
      alert("fill data properly")
    }else{
   
    $.ajax({
      url:"{{url('/add-category')}}",
      type: "POST",
      data: {
      category: category,
      },
      dataType: 'json',
      success: function (data) {
        console.log(data);
        $('#exampleModalCenter').modal('hide');
            var $subcarousel =$('#category_id').empty();
        for (i in data)
        {
        $subcarousel.append('<option value = ' + data[i].id + '>' + data[i].name + '</option>');
        }
      }
  });
}

    
  });


// Sub
  $("#save_subcategory").click(function(){
    // alert("Sa");
    var category = $("#sub-name").val();
    var parent_id = $("#parent_id").val();
 
    if(category=="" )
    {
      alert("fill data properly")
    }else{
 
   
   
    $.ajax({
      url:"{{url('/add-subcategory')}}",
      type: "POST",
      data: {
      category: category,
      parent_id:parent_id,
      },
      dataType: 'json',
      success: function (data) {
        console.log(data);
        $('#exampleModalCenter2').modal('hide');
        // var $subcarousel =$('.categormain').empty();
        // for (i in data)
        // {
        // $subcarousel.append('<option value = ' + data[i].id + '>' + data[i].name + '</option>');
        // }
      }
  });
}
});



$("#save_childcategory").click(function(){
    var category = $("#child-name").val();
    var child_cat = $("#child_cat").val();
 
    if(category=="" )
    {
      alert("fill data properly")
    }else{
 
   
   
    $.ajax({
      url:"{{url('/add-child')}}",
      type: "POST",
      data: {
      category: category,
      child_cat:child_cat,
      },
      dataType: 'json',
      success: function (data) {
        console.log(data);
        $('#exampleModalCenter3').modal('hide');
        // var $subcarousel =$('.categormain').empty();
        // for (i in data)
        // {
        // $subcarousel.append('<option value = ' + data[i].id + '>' + data[i].name + '</option>');
        // }
      }
  });
}
});



    $(document).on('change','#prod_type',function(){
      var get__prf = $(this).find(':selected').data('pf');
      var base__prf = $('.trn_prefix').val();
      base__prf = base__prf.split('-')[0];
      base__prf = base__prf+'-';
      base__prf = base__prf+get__prf+'-';
      $('.trn_prefix').val(base__prf);
      $('.trn_prefix_addon').html(base__prf);
    })





    </script>










@endsection