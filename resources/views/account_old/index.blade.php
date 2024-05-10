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
@section('title', 'Chart Of Accounts')

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Manage Your Chart Of Accounts
        <small></small>
    </h1>
</section>
<style>
    #other_account_table tbody tr:hover{
        cursor:pointer;
    }
</style>
<!-- Main content -->
<section class="content">
    @if(!empty($not_linked_payments))
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-danger">
                    <ul>
                        @if(!empty($not_linked_payments))
                            <li>{!! __('account.payments_not_linked_with_account', ['payments' => $not_linked_payments]) !!} <a href="{{action('AccountReportsController@paymentAccountReport')}}">@lang('account.view_details')</a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    @endif
    @can('account.access')
    <div class="row">
        <div class="col-sm-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#other_accounts" data-toggle="tab">
                            <i class="fa fa-book"></i> <strong>Transaction Accounts</strong>
                        </a>
                    </li>
                    {{--
                    <li>
                        <a href="#capital_accounts" data-toggle="tab">
                            <i class="fa fa-book"></i> <strong>
                            @lang('account.capital_accounts') </strong>
                        </a>
                    </li>
                    --}}
                    <li>
                        <a href="#account_types" data-toggle="tab">
                            <i class="fa fa-list"></i> <strong>
                            Control Accounts </strong>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="other_accounts">
                        <div class="row">
                            <div class="col-md-12">
                                @component('components.widget')
                                    <div class="col-md-4">
                                        {!! Form::select('account_status', ['active' => __('business.is_active'), 'closed' => __('account.closed')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'account_status']); !!}
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-danger btn-xs" id="delete_coa" >Close All</button>
                                        <button class="btn btn-success btn-xs" id="active_coa" >Activate All</button>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-primary btn-modal pull-right add-transactionAcc" 
                                            data-container=".account_model"
                                            data-href="{{action('AccountController@create')}}">
                                            <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                                    </div>
                                @endcomponent
                            </div>
                            <div class="col-sm-12">
                            <br>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped hide-footer dataTable table-styling table-hover table-primary" id="other_account_table">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" value="all" class="selectAll_chkbox"></th>
                                                <th>@lang( 'messages.action' )</th>
                                                <th>@lang( 'lang_v1.name' )</th>
                                                <th>@lang( 'lang_v1.account_type' )</th>
                                                <th>@lang( 'lang_v1.account_sub_type' )</th>
                                                <th>@lang('account.account_number')</th>
                                                <th>@lang( 'brand.note' )</th>
                                                <th>@lang('lang_v1.balance')</th>
                                                <th>@lang('lang_v1.account_details')</th>
                                                <th>@lang('lang_v1.added_by')</th>
                                                
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--
                    <div class="tab-pane" id="capital_accounts">
                        <table class="table table-bordered table-striped" id="capital_account_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'lang_v1.name' )</th>
                                    <th>@lang('account.account_number')</th>
                                    <th>@lang( 'brand.note' )</th>
                                    <th>@lang('lang_v1.balance')</th>
                                    <th>@lang( 'messages.action' )</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    --}}
                    <div class="tab-pane" id="account_types">
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" id="delete_control_acc" class="btn btn-danger btn-xs">Delete Selected</button>
                                
                    	            <button type="button" class="btn btn-xs no-print btn-info" id="expander" style="font-size: 10px;">Expand All</button>
                                    <button type="button" class="btn btn-xs no-print btn-danger" id="collapser" style="font-size: 10px;">Collapse All</button>
                                    
                                
                                <button type="button" class="btn btn-primary btn-modal pull-right" 
                                    data-href="{{action('AccountTypeController@create')}}"
                                    data-container="#account_type_modal">
                                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped table-bordered hide-footer dataTable table-styling table-hover table-primary no-footer" id="account_types_table" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" value="all" class="selectAllAccType_chkbox"></th>
                                            <th>@lang( 'lang_v1.name' )</th>
                                            <th>Code</th>
                                            <th>@lang( 'messages.action' )</th>
                                        </tr>
                                    </thead>
                                     <tbody>
                                        @foreach($account_types as $key => $account_type)
                                        <tr data-node-id="{{ $key }}" class="account_type_{{$account_type->id}}">
                                        <td>
                                            <!--<input type="checkbox" value="{{$account_type->id}}" class="acc_type_chk">-->
                                        </td>
                                        <th>{{$account_type->name}}</th>
                                        <th>{{$account_type->code}}</th>
                                        <td>
                                            {!! Form::open(['url' => action('AccountTypeController@destroy', $account_type->id), 'method' => 'delete' ]) !!}
                                            <button type="button" class="btn btn-primary btn-modal btn-xs" 
                                                data-href="{{action('AccountTypeController@edit', $account_type->id)}}"
                                                data-container="#account_type_modal">
                                            <i class="fa fa-edit"></i> @lang( 'messages.edit' )</button>
                                            <button type="button" class="btn btn-danger btn-xs delete_account_type" >
                                            <i class="fa fa-trash"></i> @lang( 'messages.delete' )</button>
                                            {!! Form::close() !!}
                                        </td>
                                        </tr>


                                        @foreach($account_type->sub_types as $key2 => $sub_type)
                                        <tr data-node-id="{{ $key.'.'.$key2 }}" data-node-pid="{{ $key }}">
                                        <td><input type="checkbox" value="{{$sub_type->id}}" class="acc_type_chk"></td>
                                        <td>&nbsp;&nbsp;-- {{$sub_type->name}}</td>
                                        <th>{{$sub_type->code}}</th>
                                        <td>
                                            {!! Form::open(['url' => action('AccountTypeController@destroy', $sub_type->id), 'method' => 'delete' ]) !!}
                                            <button type="button" class="btn btn-primary btn-modal btn-xs" 
                                                data-href="{{action('AccountTypeController@edit', $sub_type->id)}}"
                                                data-container="#account_type_modal">
                                            <i class="fa fa-edit"></i> @lang( 'messages.edit' )</button>
                                            <button type="button" class="btn btn-danger btn-xs delete_account_type" >
                                            <i class="fa fa-trash"></i> @lang( 'messages.delete' )</button>
                                            {!! Form::close() !!}
                                        </td>
                                        </tr>


                                        @foreach($sub_type->sub_types as $key3 => $child_type3)
                                        <tr data-node-id="{{ $key.'.'.$key2.'.'.$key3 }}" data-node-pid="{{ $key.'.'.$key2 }}">
                                        <td><input type="checkbox" value="{{$child_type3->id}}" class="acc_type_chk"></td>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-- {{$child_type3->name}}</td>
                                        <th>{{$child_type3->code}}</th>
                                        <td>
                                            {!! Form::open(['url' => action('AccountTypeController@destroy', $child_type3->id), 'method' => 'delete' ]) !!}
                                            <button type="button" class="btn btn-primary btn-modal btn-xs" 
                                                data-href="{{action('AccountTypeController@edit', $child_type3->id)}}"
                                                data-container="#account_type_modal">
                                            <i class="fa fa-edit"></i> @lang( 'messages.edit' )</button>
                                            <button type="button" class="btn btn-danger btn-xs delete_account_type" >
                                            <i class="fa fa-trash"></i> @lang( 'messages.delete' )</button>
                                            {!! Form::close() !!}
                                        </td>
                                        </tr>


                                        @foreach($child_type3->sub_types as $key4 => $child_type4)
                                        <tr data-node-id="{{ $key.'.'.$key2.'.'.$key3.'.'.$key4 }}" data-node-pid="{{ $key.'.'.$key2.'.'.$key3 }}">
                                        <td><input type="checkbox" value="{{$child_type4->id}}" class="acc_type_chk"></td>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-- {{$child_type4->name}}</td>
                                        <th>{{$child_type4->code}}</th>
                                        <td>
                                            {!! Form::open(['url' => action('AccountTypeController@destroy', $child_type4->id), 'method' => 'delete' ]) !!}
                                            <button type="button" class="btn btn-primary btn-modal btn-xs" 
                                                data-href="{{action('AccountTypeController@edit', $child_type4->id)}}"
                                                data-container="#account_type_modal">
                                            <i class="fa fa-edit"></i> @lang( 'messages.edit' )</button>
                                            <button type="button" class="btn btn-danger btn-xs delete_account_type" >
                                            <i class="fa fa-trash"></i> @lang( 'messages.delete' )</button>
                                            {!! Form::close() !!}
                                        </td>
                                        </tr>


                                        @foreach($child_type4->sub_types as $key5 => $child_type5)
                                        <tr data-node-id="{{ $key.'.'.$key2.'.'.$key3.'.'.$key4.'.'.$key5 }}" data-node-pid="{{ $key.'.'.$key2.'.'.$key3.'.'.$key4 }}">
                                        <td><input type="checkbox" value="{{$child_type5->id}}" class="acc_type_chk"></td>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-- {{$child_type5->name}}</td>
                                        <th>{{$child_type5->code}}</th>
                                        <td>
                                            {!! Form::open(['url' => action('AccountTypeController@destroy', $child_type5->id), 'method' => 'delete' ]) !!}
                                            <button type="button" class="btn btn-primary btn-modal btn-xs" 
                                                data-href="{{action('AccountTypeController@edit', $child_type5->id)}}"
                                                data-container="#account_type_modal">
                                            <i class="fa fa-edit"></i> @lang( 'messages.edit' )</button>
                                            <button type="button" class="btn btn-danger btn-xs delete_account_type" >
                                            <i class="fa fa-trash"></i> @lang( 'messages.delete' )</button>
                                            {!! Form::close() !!}
                                        </td>
                                        </tr>


                                        @foreach($child_type5->sub_types as $key6 => $child_type6)
                                        <tr data-node-id="{{ $key.'.'.$key2.'.'.$key3.'.'.$key4.'.'.$key5.'.'.$key6 }}" data-node-pid="{{ $key.'.'.$key2.'.'.$key3.'.'.$key4.'.'.$key5 }}">
                                        <td><input type="checkbox" value="{{$child_type6->id}}" class="acc_type_chk"></td>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-- {{$child_type6->name}}</td>
                                        <th>{{$child_type6->code}}</th>
                                        <td>
                                            {!! Form::open(['url' => action('AccountTypeController@destroy', $child_type6->id), 'method' => 'delete' ]) !!}
                                            <button type="button" class="btn btn-primary btn-modal btn-xs" 
                                                data-href="{{action('AccountTypeController@edit', $child_type6->id)}}"
                                                data-container="#account_type_modal">
                                            <i class="fa fa-edit"></i> @lang( 'messages.edit' )</button>
                                            <button type="button" class="btn btn-danger btn-xs delete_account_type" >
                                            <i class="fa fa-trash"></i> @lang( 'messages.delete' )</button>
                                            {!! Form::close() !!}
                                        </td>
                                        </tr>


                                        @foreach($child_type6->sub_types as $key7 => $child_type7)
                                        <tr data-node-id="{{ $key.'.'.$key2.'.'.$key3.'.'.$key4.'.'.$key5.'.'.$key6.'.'.$key7 }}" data-node-pid="{{ $key.'.'.$key2.'.'.$key3.'.'.$key4.'.'.$key5.'.'.$key6 }}">
                                        <td><input type="checkbox" value="{{$child_type7->id}}" class="acc_type_chk"></td>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-- {{$child_type7->name}}</td>
                                        <th>{{$child_type7->code}}</th>
                                        <td>
                                            {!! Form::open(['url' => action('AccountTypeController@destroy', $child_type7->id), 'method' => 'delete' ]) !!}
                                            <button type="button" class="btn btn-primary btn-modal btn-xs" 
                                                data-href="{{action('AccountTypeController@edit', $child_type7->id)}}"
                                                data-container="#account_type_modal">
                                            <i class="fa fa-edit"></i> @lang( 'messages.edit' )</button>
                                            <button type="button" class="btn btn-danger btn-xs delete_account_type" >
                                            <i class="fa fa-trash"></i> @lang( 'messages.delete' )</button>
                                            {!! Form::close() !!}
                                        </td>
                                        </tr>


                                        @foreach($child_type7->sub_types as $key8 => $child_type8)
                                        <tr data-node-id="{{ $key.'.'.$key2.'.'.$key3.'.'.$key4.'.'.$key5.'.'.$key6.'.'.$key7.'.'.$key8 }}" data-node-pid="{{ $key.'.'.$key2.'.'.$key3.'.'.$key4.'.'.$key5.'.'.$key6.'.'.$key7 }}">
                                        <td><input type="checkbox" value="{{$child_type8->id}}" class="acc_type_chk"></td>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-- {{$child_type8->name}}</td>
                                        <th>{{$child_type8->code}}</th>
                                        <td>
                                            {!! Form::open(['url' => action('AccountTypeController@destroy', $child_type8->id), 'method' => 'delete' ]) !!}
                                            <button type="button" class="btn btn-primary btn-modal btn-xs" 
                                                data-href="{{action('AccountTypeController@edit', $child_type8->id)}}"
                                                data-container="#account_type_modal">
                                            <i class="fa fa-edit"></i> @lang( 'messages.edit' )</button>
                                            <button type="button" class="btn btn-danger btn-xs delete_account_type" >
                                            <i class="fa fa-trash"></i> @lang( 'messages.delete' )</button>
                                            {!! Form::close() !!}
                                        </td>
                                        </tr>


                                        @foreach($child_type8->sub_types as $key9 => $child_type9)
                                        <tr data-node-id="{{ $key.'.'.$key2.'.'.$key3.'.'.$key4.'.'.$key5.'.'.$key6.'.'.$key7.'.'.$key8.'.'.$key9 }}" data-node-pid="{{ $key.'.'.$key2.'.'.$key3.'.'.$key4.'.'.$key5.'.'.$key6.'.'.$key7.'.'.$key8 }}">
                                        <td><input type="checkbox" value="{{$child_type9->id}}" class="acc_type_chk"></td>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-- {{$child_type9->name}}</td>
                                        <th>{{$child_type9->code}}</th>
                                        <td>
                                            {!! Form::open(['url' => action('AccountTypeController@destroy', $child_type9->id), 'method' => 'delete' ]) !!}
                                            <button type="button" class="btn btn-primary btn-modal btn-xs" 
                                                data-href="{{action('AccountTypeController@edit', $child_type9->id)}}"
                                                data-container="#account_type_modal">
                                            <i class="fa fa-edit"></i> @lang( 'messages.edit' )</button>
                                            <button type="button" class="btn btn-danger btn-xs delete_account_type" >
                                            <i class="fa fa-trash"></i> @lang( 'messages.delete' )</button>
                                            {!! Form::close() !!}
                                        </td>
                                        </tr>


                                        @foreach($child_type9->sub_types as $key10 => $child_type10)
                                        <tr data-node-id="{{ $key.'.'.$key2.'.'.$key3.'.'.$key4.'.'.$key5.'.'.$key6.'.'.$key7.'.'.$key8.'.'.$key9.'.'.$key10 }}" data-node-pid="{{ $key.'.'.$key2.'.'.$key3.'.'.$key4.'.'.$key5.'.'.$key6.'.'.$key7.'.'.$key8.'.'.$key9 }}">
                                        <td><input type="checkbox" value="{{$child_type10->id}}" class="acc_type_chk"></td>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-- {{$child_type10->name}}</td>
                                        <th>{{$child_type10->code}}</th>
                                        <td>
                                            {!! Form::open(['url' => action('AccountTypeController@destroy', $child_type10->id), 'method' => 'delete' ]) !!}
                                            <button type="button" class="btn btn-primary btn-modal btn-xs" 
                                                data-href="{{action('AccountTypeController@edit', $child_type10->id)}}"
                                                data-container="#account_type_modal">
                                            <i class="fa fa-edit"></i> @lang( 'messages.edit' )</button>
                                            <button type="button" class="btn btn-danger btn-xs delete_account_type" >
                                            <i class="fa fa-trash"></i> @lang( 'messages.delete' )</button>
                                            {!! Form::close() !!}
                                        </td>
                                        </tr>


                                        @foreach($child_type10->sub_types as $key11 => $child_type11)
                                        <tr data-node-id="{{ $key.'.'.$key2.'.'.$key3.'.'.$key4.'.'.$key5.'.'.$key6.'.'.$key7.'.'.$key8.'.'.$key9.'.'.$key10.'.'.$key11 }}" data-node-pid="{{ $key.'.'.$key2.'.'.$key3.'.'.$key4.'.'.$key5.'.'.$key6.'.'.$key7.'.'.$key8.'.'.$key9.'.'.$key10 }}">
                                        <td><input type="checkbox" value="{{$child_type11->id}}" class="acc_type_chk"></td>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-- {{$child_type11->name}}</td>
                                        <th>{{$child_type11->code}}</th>
                                        <td>
                                            {!! Form::open(['url' => action('AccountTypeController@destroy', $child_type11->id), 'method' => 'delete' ]) !!}
                                            <button type="button" class="btn btn-primary btn-modal btn-xs" 
                                                data-href="{{action('AccountTypeController@edit', $child_type11->id)}}"
                                                data-container="#account_type_modal">
                                            <i class="fa fa-edit"></i> @lang( 'messages.edit' )</button>
                                            <button type="button" class="btn btn-danger btn-xs delete_account_type" >
                                            <i class="fa fa-trash"></i> @lang( 'messages.delete' )</button>
                                            {!! Form::close() !!}
                                        </td>
                                        </tr>

                                        
                                        @endforeach
                                        @endforeach
                                        @endforeach
                                        @endforeach
                                        @endforeach
                                        @endforeach
                                        @endforeach
                                        @endforeach
                                        @endforeach
                                        @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--<div class="col-sm-12">-->
        <!--    <button class="btn btn-danger btn-xs" id="delete_coa" >Close All</button>-->
        <!--    <button class="btn btn-success btn-xs" id="active_coa" >Activate All</button>-->
        <!--</div>-->
    </div>
    @endcan
    
    <div class="modal fade account_model" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel" id="account_type_modal">
    </div>
    
        
    <!-- Account Book Modal -->
    <div id="account_book_modal" class="modal fade" role="dialog">
      <div class="modal-dialog">
    
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Account Book</h4>
          </div>
          <div class="modal-body">
            <!--<p>Some text in the modal.</p>-->
            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
    
      </div>
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
<script src="{{ asset('js/tree-table.js') }}"></script>
<script>
    $(document).on('change', '#parent_account_type_id', function(){
        
        var id = $(this).val();
        var selectedOption = $(this).find(':selected');
        var dataIdValue = selectedOption.data('id');
        $.ajax({ 
            url : "/account/account_number/" + id,
            type: "GET",
            dataType: "json",
            success: function(data)
            {
              $(document).find('#code').val(data);
            }
        });
    })
    
    
    $(document).on('change', '#account_type_id', function(){
        var attr_code = $(this).find(':selected').attr('code');
        $(document).find('#code_span').html(attr_code);
        
        
        var id = $(this).val();
        var selectedOption = $(this).find(':selected');
        var dataIdValue = selectedOption.data('id');
        $.ajax({ 
            url : "/account/transaction_account_number/" + id,
            type: "GET",
            dataType: "json",
            success: function(data)
            {
              $(document).find('#account_number').val(data);
            }
        });
    })
    
    // $(document).on('click', '#other_account_table tbody tr', function(){
    //     $(this).closest('.acc_book').click();
    // })
    
    
    
    $(document).ready(function(){
    
        $(document).on("click", '.saveAndNext', function(){
            setTimeout(() => {
                $('.add-transactionAcc').click();
            }, 1000);
        })
        
        $('#account_types_table').simpleTreeTable({
            expander: $('#expander'),
            collapser: $('#collapser')
        });
        
        setTimeout(function(){
            $('#collapser').click();
        },2000);
        
        
        $('#active_coa').hide();
        $('#account_status').on('change',function(){
            var value = $(this).val();
            if(value == 'active'){
                $('#active_coa').hide();
                $('#delete_coa').show();    
            }else if(value == 'closed'){
                $('#active_coa').show();
                $('#delete_coa').hide();   
            }
        })
        $(".selectAll_chkbox").on("change", function() {
            var isChecked = $(this).prop("checked");
            $(".coa_check").prop("checked", isChecked);
        });
        $('#delete_coa, #active_coa').on('click',function(){
            var valuesArray = [];
            $('.coa_check').each(function() {
                if (this.checked) {
                    valuesArray.push($(this).val());
                }
            });
            var commaSeparatedString = valuesArray.join(',');
            // console.log(valuesArray);
            if(commaSeparatedString != ''){
                swal({
                    title: LANG.sure,
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete)=>{
                    if(willDelete){
                        if (this.id === 'delete_coa') {
                            var url = '/account/closeAll/' + commaSeparatedString;
                        } else if (this.id === 'active_coa') {
                            var url = '/account/ActiveAll/' + commaSeparatedString;
                        }
                        $.ajax({
                            method: "get",
                            url: url,
                            success: function(result){
                                if(result.success == true){
                                    toastr.success(result.msg);
                                    capital_account_table.ajax.reload();
                                    other_account_table.ajax.reload();
                                }else{
                                    toastr.error(result.msg);
                                }
                            }
                        });
                    }
                });
            }else{
                alert('Please Select Atleast one checkbox');
            }
        })
        
        
        
        // for control account 
        $(".selectAllAccType_chkbox").on("change", function() {
            var isChecked = $(this).prop("checked");
            $(".acc_type_chk").prop("checked", isChecked);
        });
        
        $('#delete_control_acc').on('click',function(){
            var valuesArray = [];
            $('.acc_type_chk').each(function() {
                if (this.checked) {
                    valuesArray.push($(this).val());
                }
            });
            var commaSeparatedString = valuesArray.join(',');
            // console.log(valuesArray);
            if(commaSeparatedString != ''){
                swal({
                    title: LANG.sure,
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete)=>{
                    if(willDelete){
                        var url = '/account/del_control_acc';
                        $.ajax({
                            url: url,
                            method: "POST",
                            data:{ ids : commaSeparatedString },
                            success: function(result){
                                if(result.success == true){
                                    toastr.success(result.msg);
                                    location.reload();
                                }else{
                                    toastr.error(result.msg);
                                    location.reload();
                                }
                            }
                        });
                    }
                });
            }else{
                alert('Please Select Atleast one checkbox');
            }
        })
        
        
        
        $('#account_type_modal').on('shown.bs.modal', function(e) {
            $('#account_type_modal #parent_account_type_id').select2({ dropdownParent: $(this) })
        });
    
        $(document).on('click', 'button.close_account', function(){
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete)=>{
                if(willDelete){
                     var url = $(this).data('url');

                     $.ajax({
                         method: "get",
                         url: url,
                         dataType: "json",
                         success: function(result){
                             if(result.success == true){
                                toastr.success(result.msg);
                                capital_account_table.ajax.reload();
                                other_account_table.ajax.reload();
                             }else{
                                toastr.error(result.msg);
                            }

                        }
                    });
                }
            });
        });

        $(document).on('click', 'button.delete_account', function(){
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete)=>{
                if(willDelete){
                     var url = $(this).data('url');

                     $.ajax({
                         method: "delete",
                         url: url,
                         dataType: "json",
                         success: function(result){
                             if(result.success == true){
                                toastr.success(result.msg);
                                capital_account_table.ajax.reload();
                                other_account_table.ajax.reload();
                             }else{
                                toastr.error(result.msg);
                            }

                        }
                    });
                }
            });
        });

        $(document).on('submit', 'form#edit_payment_account_form', function(e){
            e.preventDefault();
            var data = $(this).serialize();
            $.ajax({
                method: "POST",
                url: $(this).attr("action"),
                dataType: "json",
                data: data,
                success:function(result){
                    if(result.success == true){
                        $('div.account_model').modal('hide');
                        toastr.success(result.msg);
                        capital_account_table.ajax.reload();
                        other_account_table.ajax.reload();
                    }else{
                        toastr.error(result.msg);
                    }
                }
            });
        });

        $(document).on('submit', 'form#payment_account_form', function(e){
            e.preventDefault();
            var data = $(this).serialize();
            $.ajax({
                method: "post",
                url: $(this).attr("action"),
                dataType: "json",
                data: data,
                success:function(result){
                    if(result.success == true){
                        $('div.account_model').modal('hide');
                        toastr.success(result.msg);
                        capital_account_table.ajax.reload();
                        other_account_table.ajax.reload();
                    }else{
                        toastr.error(result.msg);
                    }
                }
            });
        });

        // capital_account_table
        capital_account_table = $('#capital_account_table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: '/account/account?account_type=capital',
                        columnDefs:[{
                                "targets": 5,
                                "orderable": false,
                                "searchable": false
                            }],
                        columns: [
                            {data: 'action', name: 'action'},
                            {data: 'name', name: 'name'},
                            {data: 'account_number', name: 'account_number'},
                            {data: 'note', name: 'note'},
                            {data: 'balance', name: 'balance', searchable: false},
                            
                        ],
                        "fnDrawCallback": function (oSettings) {
                            __currency_convert_recursively($('#capital_account_table'));
                        }
                    });
        // capital_account_table
        other_account_table = $('#other_account_table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: '/account/account?account_type=other',
                            data: function(d){
                                d.account_status = $('#account_status').val();
                            }
                        },
                        columnDefs:[{
                                "targets": [6,8],
                                "orderable": false,
                                "searchable": false,
                            }],
                        columns: [
                            {data: 'checkbox', name: 'checkbox', searchable: false, orderable: false},
                            {data: 'action', name: 'action'},
                            {data: 'name', name: 'accounts.name'},
                            {data: 'parent_account_type_name', name: 'pat.name'},
                            {data: 'account_type_name', name: 'ats.name'},
                            {data: 'account_number', name: 'accounts.account_number'},
                            {data: 'note', name: 'accounts.note'},
                            {data: 'balance', name: 'balance', searchable: false, orderable: false},
                            {data: 'account_details', name: 'account_details'},
                            {data: 'added_by', name: 'u.first_name', searchable: false, orderable: false},
                            
                        ],
                        "fnDrawCallback": function (oSettings) {
                            __currency_convert_recursively($('#other_account_table'));
                        }
                        // createdRow: function(row, data, dataIndex) {
                        //     $(row).on('click', function() {
                        //         show_detail(this);
                        //     });
                        // }
                    });
                    
    });

    $('#account_status').change( function(){
        other_account_table.ajax.reload();
    });

    $(document).on('submit', 'form#deposit_form', function(e){
        e.preventDefault();
        var data = $(this).serialize();

        $.ajax({
          method: "POST",
          url: $(this).attr("action"),
          dataType: "json",
          data: data,
          success: function(result){
            if(result.success == true){
              $('div.view_modal').modal('hide');
              toastr.success(result.msg);
              capital_account_table.ajax.reload();
              other_account_table.ajax.reload();
            } else {
              toastr.error(result.msg);
            }
          }
        });
    });

    $('.account_model').on('shown.bs.modal', function(e) {
        $('.account_model .select2').select2({ dropdownParent: $(this) })
    });

    $(document).on('click', 'button.delete_account_type', function(){
        swal({
            title: LANG.sure,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete)=>{
            if(willDelete){
                $(this).closest('form').submit();
            }
        });
    })

    $(document).on('click', 'button.activate_account', function(){
        swal({
            title: LANG.sure,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willActivate)=>{
            if(willActivate){
                 var url = $(this).data('url');
                 $.ajax({
                     method: "get",
                     url: url,
                     dataType: "json",
                     success: function(result){
                         if(result.success == true){
                            toastr.success(result.msg);
                            capital_account_table.ajax.reload();
                            other_account_table.ajax.reload();
                         }else{
                            toastr.error(result.msg);
                        }

                    }
                });
            }
        });
    });
// function add_function_tr(){
//     $("#other_account_table_wrapper tbody tr").attr("onclick","show_detail(this)")
// }
function show_detail(el){
    $(el).click(function(){
        var url = $(el).find(".acc_book").attr("href");
        window.open(url, '_blank');
    });
}
</script>
@endsection