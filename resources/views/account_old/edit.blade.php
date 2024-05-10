<div class="modal-dialog" role="document">
    <div class="modal-content">
  
      {!! Form::open(['url' => action('AccountController@update',$account->id), 'method' => 'PUT', 'id' => 'edit_payment_account_form' ]) !!}
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang( 'account.edit_account' )</h4>
      </div>
  
      <div class="modal-body">
              <div class="form-group">
                  {!! Form::label('name', __( 'lang_v1.name' ) .":*") !!}
                  {!! Form::text('name', $account->name, ['class' => 'form-control', 'required','placeholder' => __( 'lang_v1.name' ) ]); !!}
              </div>
  
               <div class="form-group">
                  {!! Form::label('account_number', __( 'account.account_number' ) .":*") !!}
                  {!! Form::text('account_number', $account->account_number, ['class' => 'form-control', 'readonly', 'required','placeholder' => __( 'account.account_number' ) ]); !!}
              </div>
  
              <div class="form-group">
                  {!! Form::label('account_type_id', __( 'account.account_type' ) .":") !!}
                  <select name="account_type_id" class="form-control select2">
                      <option>@lang('messages.please_select')</option>
                      @foreach($account_types as $account_type)
                          {{-- <optgroup label="{{$account_type->name}}"> --}}
                              @if($account->account_type_id == $account_type->id)
                              <option value="{{$account_type->id}}" selected>{{$account_type->name}}</option>
                              @else
                              <option value="{{$account_type->id}}"  >{{$account_type->name}}</option>
                              @endif
                              {{-- @foreach($account_type->sub_types as $sub_type)
                                  <option value="{{$sub_type->id}}" @if($account->account_type_id == $sub_type->id) selected @endif >{{$sub_type->name}}</option>
                              @endforeach --}}
                          {{-- </optgroup> --}}
                      @endforeach
                  </select>
              </div>
              
              <div class="form-group">
                  {!! Form::label('type', 'Type' .":") !!}
                  <select name="type" class="form-control" required>
                      <option selected disabled value="">Select Please</option>
                      <option value="debit" {{ !empty($acc_transaction->type) ? ($acc_transaction->type == 'debit') ? 'selected' : '' : '' }}>Debit</option>
                      <option value="credit" {{ !empty($acc_transaction->type) ? ($acc_transaction->type == 'credit') ? 'selected' : '' : '' }}>Credit</option>
                  </select>
              </div>
              
               <div class="form-group">
                  {!! Form::label('opening_balance', __( 'account.opening_balance' ) .":") !!}
                  {!! Form::text('opening_balance', (!empty($acc_transaction->amount) ? $acc_transaction->amount : 0), ['class' => 'form-control input_number','placeholder' => __( 'account.opening_balance' ) ]); !!}
              </div>
              
              @if($account->contact_id == null OR $account->is_allow_customer == 1)
              <div class="form-group">
                  {!! Form::label('is_allow', __('Show As Supplier/Customer') . ":") !!}
                  {!! Form::hidden('is_allow', 0) !!} <!-- Hidden field with value 0 -->
                  {!! Form::checkbox('is_allow', 1, $account->is_allow_customer == 1, ['class' => 'is_allow']) !!} <!-- Checkbox with value 1 -->
              </div>
              @endif
  
      </div>
  
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->