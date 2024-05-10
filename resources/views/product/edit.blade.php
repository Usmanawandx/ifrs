@extends('layouts.app')
@section('title', __('product.edit_product'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="top-heading">@lang('product.edit_product')</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        {!! Form::open([
            'url' => action('ProductController@update', [$product->id]),
            'method' => 'PUT',
            'id' => 'product_add_form',
            'class' => 'product_form',
            'files' => true,
        ]) !!}
        <input type="hidden" id="product_id" value="{{ $product->id }}">

        @component('components.widget', ['class' => 'box-primary'])
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('name', __('product.product_name') . ':*') !!}
                        {!! Form::text('name', $product->name, [
                            'class' => 'form-control',
                            'required',
                            'placeholder' => __('product.product_name'),
                        ]) !!}
                    </div>
                </div>

                <div class="col-sm-4 @if (!(session('business.enable_category') && session('business.enable_sub_category'))) hide @endif">
                    <div class="form-group">
                        {!! Form::label('sku', __('product.sku') . ':*') !!} @show_tooltip(__('tooltip.sku'))
                        <input type="hidden" name="prefix" value="{{ $sku_prefixx . '-' }}"
                            class="input-group-addon trn_prefix">
                        <div class="input-group">
                            <span class="input-group-addon trn_prefix_addon text-uppercase">
                                {{ $sku_prefixx . '-' }}
                            </span>
                            @php
                                $string = $product->sku;
                                $parts = explode('-', $string);
                                $valueAfterLastHyphen = end($parts);
                            @endphp
                            {!! Form::text('sku', $valueAfterLastHyphen, [
                                'class' => 'form-control sku_no__edit',
                                'placeholder' => __('product.sku'),
                                'required',
                                'full_sku' => $string,
                                'sku' => $valueAfterLastHyphen,
                            ]) !!}
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('barcode_type', __('product.barcode_type') . ':*') !!}
                        {!! Form::select('barcode_type', $barcode_types, $product->barcode_type, [
                            'placeholder' => __('messages.please_select'),
                            'class' => 'form-control select2',
                            'required',
                        ]) !!}
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('unit_id', __('product.unit') . ':*') !!}
                        <div class="input-group">
                            {!! Form::select('unit_id', $units, $product->unit_id, [
                                'placeholder' => __('messages.please_select'),
                                'class' => 'form-control select2',
                                'required',
                            ]) !!}
                            <span class="input-group-btn">
                                <button type="button" @if (!auth()->user()->can('unit.create')) disabled @endif
                                    class="btn btn-default bg-white btn-flat quick_add_unit btn-modal"
                                    data-href="{{ action('UnitController@create', ['quick_add' => true]) }}"
                                    title="@lang('unit.add_unit')" data-container=".view_modal"><i
                                        class="fa fa-plus-circle text-primary fa-lg"></i></button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 @if (!session('business.enable_brand')) hide @endif">
                    <div class="form-group">
                        {!! Form::label('brand_id', __('product.brand') . ':') !!}
                        <div class="input-group">
                            {!! Form::select('brand_id', $brands, $product->brand_id, [
                                'placeholder' => __('messages.please_select'),
                                'class' => 'form-control select2',
                            ]) !!}
                            <span class="input-group-btn">
                                <button type="button" @if (!auth()->user()->can('brand.create')) disabled @endif
                                    class="btn btn-default bg-white btn-flat btn-modal"
                                    data-href="{{ action('BrandController@create', ['quick_add' => true]) }}"
                                    title="@lang('brand.add_brand')" data-container=".view_modal"><i
                                        class="fa fa-plus-circle text-primary fa-lg"></i></button>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4" id="alert_quantity_div" @if (!$product->enable_stock)  @endif>
                    <div class="form-group">
                        {!! Form::label('alert_quantity', __('product.alert_quantity') . ':') !!} @show_tooltip(__('tooltip.alert_quantity'))
                        {!! Form::text('alert_quantity', @format_quantity($product->alert_quantity), [
                            'class' => 'form-control input_number',
                            'placeholder' => __('product.alert_quantity'),
                            'min' => '0',
                        ]) !!}
                    </div>
                </div>
                <div class="clearfix"></div>

                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('product_type', __('Product Type') . ':') !!} @show_tooltip(__('lang_v1.product_location_help'))
                        <select name="product_type" id="prod_type" class="form-control select2" required>
                            <option selected disabled> Select</option>
                            @foreach ($type as $tp)
                                <option value="{{ $tp->id }}" data-pf="{{ $tp->prefix }}"
                                    {{ $product->product_type == $tp->id ? 'selected' : '' }}>{{ $tp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('product_category', 'Product Category' . ':') !!}
                        {!! Form::select('category_id', [], null, [
                            'placeholder' => __('messages.please_select'),
                            'required',
                            'class' => 'form-control product_category select2',
                        ]) !!}
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('sub_category', 'Sub Category' . ':') !!}
                        {!! Form::select('sub_category_id', [], null, [
                            'placeholder' => __('messages.please_select'),
                            'class' => 'form-control sub_category select2',
                        ]) !!}
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('product_type', 'Milling Category' . ':') !!}
                        <select name="milling_category" id="milling_category" class="form-control select2">
                            <option selected disabled> Select</option>
                            @foreach ($milling_category as $mill)
                                <option value="{{ $mill->id }}" {{ ($product->milling_category == $mill->id) ? 'selected' : '' }}>{{ $mill->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('product_locations', __('business.business_locations') . ':') !!} @show_tooltip(__('lang_v1.product_location_help'))
                        {!! Form::select('product_locations[]', $business_locations, $product->product_locations->pluck('id'), [
                            'class' => 'form-control select2',
                            'multiple',
                            'id' => 'product_locations',
                        ]) !!}
                    </div>
                </div>

                <div class="col-sm-4 hide">
                    <div class="form-group">
                        {!! Form::label('types', __('Type') . ':') !!}
                        <select class="form-control select2" name="types">
                            @foreach ($types as $t)
                                @if ($product->product_type == $t->id)
                                    <option value="{{ $t->id }}" selected>{{ $t->name }}</option>
                                @else
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('image', __('lang_v1.product_image') . ':') !!}
                        {!! Form::file('image', ['id' => 'upload_image', 'accept' => 'image/*']) !!}
                        <small>
                            <p class="help-block">@lang('purchase.max_file_size', ['size' => config('constants.document_size_limit') / 1000000]). @lang('lang_v1.aspect_ratio_should_be_1_1') @if (!empty($product->image))
                                    <br> @lang('lang_v1.previous_image_will_be_replaced')
                                @endif
                            </p>
                        </small>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <br>
                        <label>
                            {!! Form::checkbox('enable_stock', 1, $product->enable_stock, [
                                'class' => 'input-icheck',
                                'id' => 'enable_stock',
                            ]) !!} <strong>@lang('product.manage_stock')</strong>
                        </label>@show_tooltip(__('tooltip.enable_stock')) <p class="help-block"><i>@lang('product.enable_stock_help')</i></p>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <br>
                        <label>
                            {!! Form::checkbox('is_non_inventory', 1, $product->is_non_inventory, [
                                'class' => 'input-icheck',
                                'id' => 'is_non_inventory',
                            ]) !!} <strong>Non Inventory</strong>
                        </label><p class="help-block"><i>Disable valuation for this product</i></p>
                    </div>
                </div>
                
                <!-- include module fields -->
                @if (!empty($pos_module_data))
                    @foreach ($pos_module_data as $key => $value)
                        @if (!empty($value['view_path']))
                            @includeIf($value['view_path'], ['view_data' => $value['view_data']])
                        @endif
                    @endforeach
                @endif
                <div class="clearfix"></div>
                <div class="col-sm-8">
                    <div class="form-group">
                        {!! Form::label('product_description', __('lang_v1.product_description') . ':') !!}
                        {!! Form::textarea('product_description', $product->product_description, ['class' => 'form-control']) !!}
                    </div>
                </div>

            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('product_brochure', __('lang_v1.product_brochure') . ':') !!}
                    {!! Form::file('product_brochure', [
                        'id' => 'product_brochure',
                        'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types'))),
                    ]) !!}
                    <small>
                        <p class="help-block">
                            @lang('lang_v1.previous_file_will_be_replaced')<br>
                            @lang('purchase.max_file_size', ['size' => config('constants.document_size_limit') / 1000000])
                            @includeIf('components.document_help_text')
                        </p>
                    </small>
                </div>
            </div>
        @endcomponent

        @component('components.widget', ['class' => 'box-primary'])
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('weight', __('Gross weight') . ':') !!}
                        {!! Form::text('weight', $product->weight, ['class' => 'form-control', 'placeholder' => __('lang_v1.weight')]) !!}
                    </div>
                </div>
                @php
                    $custom_labels = json_decode(session('business.custom_labels'), true);
                    $product_custom_field1 = !empty($custom_labels['product']['custom_field_1']) ? $custom_labels['product']['custom_field_1'] : __('lang_v1.product_custom_field1');
                @endphp
                <!--custom fields-->
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('product_custom_field1', __('Net Weight') . ':') !!}
                        {!! Form::text('product_custom_field1', $product->product_custom_field1, [
                            'class' => 'form-control',
                            'placeholder' => '',
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('contractor', 'Contractor' . ':') !!}
                        <select name="contractor" class="form-control">
                            @foreach ($contractor as $contr)
                                <option value="{{ $contr['id'] }}"
                                    {{ $contr['id'] == $product->contractor ? 'selected' : '' }}>
                                    {{ empty($contr['name']) ? $contr['supplier_business_name'] : $contr['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('rate', 'Rate' . ':') !!}
                        <input type="text" class="form-control" name="rate" value="{{ $product->rate }}" />
                    </div>
                </div>

                @include('layouts.partials.module_form_part')
            </div>
        @endcomponent

        <div class="row">
            <div class="col-sm-4 @if (!session('business.enable_price_tax')) hide @endif">
                <div class="form-group">
                    {!! Form::label('tax', __('product.applicable_tax') . ':') !!}
                    {!! Form::select(
                        'tax',
                        $taxes,
                        $product->tax,
                        ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2'],
                        $tax_attributes,
                    ) !!}
                </div>
            </div>

            <div class="col-sm-4 @if (!session('business.enable_price_tax')) hide @endif">
                <div class="form-group">
                    {!! Form::label('tax_type', __('product.selling_price_tax_type') . ':*') !!}
                    {!! Form::select(
                        'tax_type',
                        ['inclusive' => __('product.inclusive'), 'exclusive' => __('product.exclusive')],
                        $product->tax_type,
                        ['class' => 'form-control select2', 'required'],
                    ) !!}
                </div>
            </div>

            <div class="clearfix"></div>
            <div class="col-sm-4 hide">
                <div class="form-group">
                    {!! Form::label('type', __('product.product_type') . ':*') !!} @show_tooltip(__('tooltip.product_type'))
                    {!! Form::select('type', $product_types, $product->type, [
                        'class' => 'form-control select2',
                        'required',
                        'disabled',
                        'data-action' => 'edit',
                        'data-product_id' => $product->id,
                    ]) !!}
                </div>
            </div>

            <div class="form-group col-sm-12" id="product_form_part"></div>
            <input type="hidden" id="variation_counter" value="0">
            <input type="hidden" id="default_profit_percent" value="{{ $default_profit_percent }}">
        </div>

        <div class="row">
            <input type="hidden" name="submit_type" id="submit_type">
            <div class="col-sm-12">
                <div class="text-center">
                    <div class="btn-group">
                        @if ($selling_price_group_count)
                            <button type="submit" value="submit_n_add_selling_prices"
                                class="btn btn-warning submit_product_form">@lang('lang_v1.save_n_add_selling_price_group_prices')</button>
                        @endif

                        @can('product.opening_stock')
                            <button type="submit" @if (empty($product->enable_stock)) disabled="true" @endif
                                id="opening_stock_button" value="update_n_edit_opening_stock"
                                class="btn bg-purple submit_product_form">@lang('lang_v1.update_n_edit_opening_stock')</button>
                            @endif

                            <button type="submit" value="save_n_add_another"
                                class="btn bg-maroon submit_product_form">@lang('lang_v1.update_n_add_another')</button>

                            <button type="submit" value="submit"
                                class="btn btn-primary submit_product_form">@lang('messages.update')</button>
                        </div>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}
        </section>
        <!-- /.content -->

    @endsection

@section('javascript')
    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {

            __page_leave_confirmation('#product_add_form');
            setTimeout(() => {
                $('#prod_type').trigger('change');
            }, 1000);

            $("#prod_type").change(function() {
                var id = $("#prod_type").val();
                $.ajax({
                    type: "GET",
                    url: '/products/get_product_category/' + id,
                    success: function(data) {
                        $('.product_category').html(
                            '<option disabled selected>Please Select</option>');
                        $('.sub_category').html(
                            '<option disabled selected>Please Select</option>');

                        $.each(data, function(index, value) {
                            var selected = ({{ $product->category_id ?? 0 }} == value
                                .id) ? 'selected' : '';
                            if (value.name != null) {
                                $('.product_category').append('<option value="' + value
                                    .id + '" ' + selected + '>' + value.name +
                                    '</option>');
                            }
                        });
                        $('.product_category').trigger('change');
                    }
                })
            });

            $(".product_category").change(function() {
                var id = $(".product_category").val();
                $.ajax({
                    type: "GET",
                    url: '/products/get_product_subcategory/' + id,
                    success: function(data) {
                        $('.sub_category').html(
                            '<option disabled selected>Please Select</option>');
                        $.each(data, function(index, value) {
                            var selected = ({{ $product->sub_category_id ?? 0 }} ==
                                value.id) ? 'selected' : '';
                            if (value.name != null) {
                                $('.sub_category').append('<option value="' + value.id +
                                    '" ' + selected + '>' + value.name + '</option>'
                                );
                            }
                        });
                        $('.sub_category').trigger('change');
                    }
                })
            });

            $(".categormain").change(function() {
                var id = $(".categormain").val();
                $.ajax({
                    type: "GET",
                    url: '/get_sub/' + id,
                    success: function(data) {
                        console.log(data)
                        $('.sub').html('<option disabled selected>Please Select</option>');;

                        $.each(data, function(index, value) {
                            if (value.name != null) {
                                $('.sub').append('<option value="' + value.id + '">' +
                                    value.name + '</option>');
                            }
                        });
                    }
                })
            });

            $(".sub").change(function() {
                var id = $(".sub").val();
                $.ajax({
                    type: "GET",
                    url: '/get_child_cat/' + id,
                    success: function(data) {
                        console.log(data)
                        $('.child').html('<option disabled selected>Please Select</option>');;

                        $.each(data, function(index, value) {
                            if (value.name != null) {
                                $('.child').append('<option value="' + value.id + '">' +
                                    value.name + '</option>');
                            }
                        });
                    }
                })
            });

        });
    </script>
@endsection
