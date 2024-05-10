@extends('layouts.app')
@section('title', __('role.edit_role'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>@lang( 'role.edit_role' )</h1>
</section>

<!-- Main content -->
<section class="content">
    @php
      $pos_settings = !empty(session('business.pos_settings')) ? json_decode(session('business.pos_settings'), true) : [];
    @endphp
    @component('components.widget', ['class' => 'box-primary'])
        {!! Form::open(['url' => action('RoleController@update', [$role->id]), 'method' => 'PUT', 'id' => 'role_form' ]) !!}
        <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('name', __( 'user.role_name' ) . ':*') !!}
              {!! Form::text('name', str_replace( '#' . auth()->user()->business_id, '', $role->name) , ['class' => 'form-control', 'required', 'placeholder' => __( 'user.role_name' ) ]); !!}
          </div>
        </div>
        </div>
        @if(in_array('service_staff', $enabled_modules))
        <div class="row">
        <div class="col-md-2">
          <h4>@lang( 'lang_v1.user_type' )</h4>
        </div>
        <div class="col-md-9 col-md-offset-1">
          <div class="col-md-12">
          <div class="checkbox">
            <label>
              {!! Form::checkbox('is_service_staff', 1, $role->is_service_staff, 
              [ 'class' => 'input-icheck']); !!} {{ __( 'restaurant.service_staff' ) }}
            </label>
            @show_tooltip(__('restaurant.tooltip_service_staff'))
          </div>
          </div>
        </div>
        </div>
        @endif

        <div class="row">
          <div class="col-md-12">
            <label>
              Can Set Trusted Pc
            </label>
            <input type="radio" name="check_trusted" {{ $role->can_set_trusted == 1 ? 'checked' : '' }} value="1">

        </div>
        <div class="col-md-12">
            <label>
              Can login Only Trusted Pc
            </label>
            <input type="radio" name="check_trusted"  {{ $role->can_set_trusted == 0 ? 'checked' : '' }} value="0">
        </div>
         <div class="col-md-12">
          <label>
            Allow Convert 
          </label>
          {!! Form::checkbox('permissions[]', 'convert', in_array('convert', $role_permissions),  
                [ 'class' => 'input-icheck']); !!} 
      </div>
          </div>
          <table class="table " id="user_roles_manage">
            <thead>
            <tr>
              <th>Module</th>
              <th>Read</th>
              <th>Write</th>
              <th>Modify</th>
              <th>Delete</th>
              <th>Print</th>
            </tr>
            </thead>
            <tbody>
              <tr>
            <td  style="background-color: ghostwhite;">Accounts & Finance</td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
              </tr>
            <tr>
              <td>Chart Of Accounts(Transaction Accounts)</td>
              <td>  {!! Form::checkbox('permissions[]', 'accounts.view',  in_array('accounts.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}        
              </td>
              <td> {!! Form::checkbox('permissions[]', 'accounts.access',  in_array('accounts.access', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'accounts.edit',  in_array('accounts.edit', $role_permissions), 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'accounts.delete',  in_array('accounts.delete', $role_permissions),
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td></td>
            </tr>
            
  
            {{-- Vouchers --}}
 
            
  
            <tr>
              <td>Journal Vocuher</td>
              <td>  {!! Form::checkbox('permissions[]', 'account.journal_vouchers',  in_array('account.journal_vouchers', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}        
              </td>
              <td> {!! Form::checkbox('permissions[]', 'journal_voucher.add',  in_array('journal_voucher.add', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'journal_voucher.edit',  in_array('journal_voucher.edit', $role_permissions), 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'journal_voucher.delete',  in_array('journal_voucher.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'journal_voucher.print',  in_array('journal_voucher.print', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
            </tr>
  
            <tr>
              <td>Bank Book</td>
              <td>  {!! Form::checkbox('permissions[]', 'bank_book.view',  in_array('bank_book.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}        
              </td>
              <td> {!! Form::checkbox('permissions[]', 'bank_book.add', in_array('bank_book.add', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'bank_book.edit', in_array('bank_book.edit', $role_permissions), 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'bank_book.delete', in_array('bank_book.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'bank_book.print', in_array('bank_book.print', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
            </tr>
  
            <tr>
              <td>Payment Voucher</td>
              <td>  {!! Form::checkbox('permissions[]', 'account.payment_vouchers', in_array('account.payment_vouchers', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}        
              </td>
              <td> {!! Form::checkbox('permissions[]', 'account.payment_vouchers.add', in_array('account.payment_vouchers.add', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'account.payment_vouchers.edit', in_array('account.payment_vouchers.edit', $role_permissions), 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'account.payment_vouchers.delete', in_array('account.payment_vouchers.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'account.payment_vouchers.print', in_array('account.payment_vouchers.print', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
            </tr>
  
            <tr>
              <td>Receipt  Voucher</td>
              <td>  {!! Form::checkbox('permissions[]', 'account.receiept_vouchers', in_array('account.receiept_vouchers', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}        
              </td>
              <td> {!! Form::checkbox('permissions[]', 'account.receiept_vouchers.add', in_array('account.receiept_vouchers.add', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'account.receiept_vouchers.edit', in_array('account.receiept_vouchers.edit', $role_permissions), 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'account.receiept_vouchers.delete', in_array('account.receiept_vouchers.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'account.receiept_vouchers.print', in_array('account.receiept_vouchers.print', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
            </tr>
  
            <tr>
              <td>Cash Receipt  Voucher</td>
              <td>  {!! Form::checkbox('permissions[]', 'cash_received_voucher.view', in_array('cash_received_voucher.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}        
              </td>
              <td> {!! Form::checkbox('permissions[]', 'cash_received_voucher.add', in_array('cash_received_voucher.add', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'cash_received_voucher.edit', in_array('cash_received_voucher.edit', $role_permissions), 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'cash_received_voucher.delete', in_array('cash_received_voucher.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'cash_received_voucher.print', in_array('cash_received_voucher.print', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
            </tr>
  
            
            <tr>
              <td>Cash Payment Voucher</td>
              <td>  {!! Form::checkbox('permissions[]', 'account.cash_payment_vouchers', in_array('account.cash_payment_vouchers', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}       
                 
              </td>
              <td> {!! Form::checkbox('permissions[]', 'account.cash_payment_vouchers.add', in_array('account.cash_payment_vouchers.add', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'account.cash_payment_vouchers.edit', in_array('account.cash_payment_vouchers.edit', $role_permissions), 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'account.cash_payment_vouchers.delete', in_array('account.cash_payment_vouchers.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'account.cash_payment_voucher.print', in_array('account.cash_payment_voucher.print', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
            </tr>
  
  

            {{-- End Vocuher --}}
            <tr>
            <td  style="background-color: ghostwhite;">Customer & Supplier</td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            </tr>

            {{-- Customer --}}
            <tr>
              <td>@lang( 'role.customer' )</td>
              <td>  {!! Form::checkbox('permissions[customer_view]', 'customer.view', in_array('customer.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}            
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'customer.create', in_array('customer.create', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}           
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'customer.update', in_array('customer.update', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}        
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'customer.delete', in_array('customer.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td></td>
            </tr>

            {{-- Supplier --}}
            <tr>
              <td>@lang( 'role.supplier' )</td>
              <td> {!! Form::checkbox('permissions[]', 'supplier.view', in_array('supplier.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
                </td>
              <td>        {!! Form::checkbox('permissions[]', 'supplier.create', in_array('supplier.create', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
                </td>
              <td>
                {!! Form::checkbox('permissions[]', 'supplier.update', in_array('supplier.update', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'supplier.delete', in_array('supplier.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>     
              <td></td>         
            </tr>

            <tr>
              <td>Customer Group</td>
              <td> {!! Form::checkbox('permissions[]', 'group.view', in_array('group.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
                </td>
              <td>        {!! Form::checkbox('permissions[]', 'group.create', in_array('group.create', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
                </td>
              <td>
                {!! Form::checkbox('permissions[]', 'group.update', in_array('group.update', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'group.delete', in_array('group.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td> 
              <td></td>             
            </tr>
           
            <tr>
            <td  style="background-color: ghostwhite;">Contractor & Transporter</td>
            <td  style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
              </tr>
            <tr>
            <!-- Contractor -->
            <tr>
              <td>Contractor And Transporter</td>
            
              <td>   {!! Form::checkbox('permissions[]', 'contractor.view', in_array('contractor.view', $role_permissions), 
                  [ 'class' => 'input-icheck']); !!}          
                    </td>
              <td>  {!! Form::checkbox('permissions[]', 'contractor.create', in_array('contractor.create', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
                </td>
              <td>  {!! Form::checkbox('permissions[]', 'contractor.update', in_array('contractor.update', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
                </td>
              <td>    {!! Form::checkbox('permissions[]', 'contractor.delete', in_array('contractor.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
                </td>
                <td></td>
            </tr>

            <tr>
              <td>Contractor Rate</td>
            
              <td>   {!! Form::checkbox('permissions[]', 'contractor_rate.view', in_array('contractor_rate.view', $role_permissions), 
                  [ 'class' => 'input-icheck']); !!}          
                    </td>
              <td>  {!! Form::checkbox('permissions[]', 'contractor_rate.create', in_array('contractor_rate.create', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
                </td>
              <td>  {!! Form::checkbox('permissions[]', 'contractor_rate.update', in_array('contractor_rate.update', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
                </td>
              <td>    {!! Form::checkbox('permissions[]', 'contractor_rate.delete', in_array('contractor_rate.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td></td>
            </tr>


            <tr>
              <td>Transporter Rate</td>
            
              <td>   {!! Form::checkbox('permissions[]', 'transporter_rate.view', in_array('transporter_rate.view', $role_permissions), 
                  [ 'class' => 'input-icheck']); !!}          
                    </td>
              <td>  {!! Form::checkbox('permissions[]', 'transporter_rate.create', in_array('transporter_rate.create', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
                </td>
              <td>  {!! Form::checkbox('permissions[]', 'transporter_rate.update', in_array('transporter_rate.update', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
                </td>
              <td>    {!! Form::checkbox('permissions[]', 'transporter_rate.delete', in_array('transporter_rate.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td></td>
            </tr>

            <tr>
              <td>Vehicle</td>
            
              <td>   {!! Form::checkbox('permissions[]', 'vehicle.view', in_array('vehicle.view', $role_permissions), 
                  [ 'class' => 'input-icheck']); !!}          
                    </td>
              <td>  {!! Form::checkbox('permissions[]', 'vehicle.create', in_array('vehicle.create', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
                </td>
              <td>  {!! Form::checkbox('permissions[]', 'vehicle.update', in_array('vehicle.update', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
                </td>
              <td>    {!! Form::checkbox('permissions[]', 'vehicle.delete', in_array('vehicle.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td></td>
            </tr>

            <tr>
              <td>Sales Agent</td>
            
              <td>   {!! Form::checkbox('permissions[]', 'sale_agent.view', in_array('sale_agent.view', $role_permissions), 
                  [ 'class' => 'input-icheck']); !!}          
                    </td>
              <td>  {!! Form::checkbox('permissions[]', 'sale_agent.create', in_array('sale_agent.create', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
                </td>
              <td>  {!! Form::checkbox('permissions[]', 'sale_agent.update', in_array('sale_agent.update', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
                </td>
              <td>    {!! Form::checkbox('permissions[]', 'sale_agent.delete', in_array('sale_agent.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td></td>
            </tr>


            <!-- Contractor -->

            <tr>
            <td  style="background-color: ghostwhite;">Purchase</td>
            <td  style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
              </tr>

            <!-- Purchase Module -->

            <tr>
              <td>Purchase Requisition</td>
              <td>  {!! Form::checkbox('permissions[]', 'purchase.purchase_req.view', in_array('purchase.purchase_req.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}          
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'purchase.purchase_req.add', in_array('purchase.purchase_req.add', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase.purchase_req.edit', in_array('purchase.purchase_req.edit', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase.purchase_req.delete', in_array('purchase.purchase_req.delete', $role_permissions),
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase.purchase_req.print', in_array('purchase.purchase_req.print', $role_permissions),
                [ 'class' => 'input-icheck']); !!}
              </td>
             
            </tr>
  
            <tr>
              <td>Purchase Order</td>
              <td> {!! Form::checkbox('permissions[]', 'purchase_order.view_all', in_array('purchase_order.view_all', $role_permissions),
                [ 'class' => 'input-icheck']); !!}         
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'purchase_order.create', in_array('purchase_order.create', $role_permissions),
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase_order.update', in_array('purchase_order.update', $role_permissions),
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase_order.delete', in_array('purchase_order.delete', $role_permissions),
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase_order.print', in_array('purchase_order.print', $role_permissions),
                [ 'class' => 'input-icheck']); !!}
              </td>
            </tr>
            {{-- GRN --}}
            <tr>
              <td>Grn</td>
              <td> {!! Form::checkbox('permissions[]', 'purchase.view', in_array('purchase.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}          
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'purchase.create',  in_array('purchase.create', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase.update',  in_array('purchase.update', $role_permissions), 
                  [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase.delete',  in_array('purchase.delete', $role_permissions), 
                  [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase.print',  in_array('purchase.print', $role_permissions), 
                  [ 'class' => 'input-icheck']); !!}
              </td>
            </tr>
            {{-- Order Invoice --}}
            <tr>
              <td>Purchase Order Invoice</td>
              <td> {!! Form::checkbox('permissions[]', 'purchase_invoice.view',  in_array('purchase_invoice.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}         
              </td>
              <td>{!! Form::checkbox('permissions[]', 'purchase_invoice.add',  in_array('purchase_invoice.add', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}    
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase_invoice.edit',  in_array('purchase_invoice.edit', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}    
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase_invoice.delete',  in_array('purchase_invoice.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}    
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase_invoice.print',  in_array('purchase_invoice.print', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}    
              </td>
            </tr>
  
            {{-- Debit Note--}}
  
            <tr>
              <td>Debit Note</td>
              <td> {!! Form::checkbox('permissions[]', 'purchase.debit_note',  in_array('purchase.debit_note', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}         
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'purchase.debit_note.add',  in_array('purchase.debit_note.add', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase.debit_note.edit',  in_array('purchase.debit_note.edit', $role_permissions), 
                      [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase.debit_note.delete',  in_array('purchase.debit_note.delete', $role_permissions), 
                      [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase.debit_note.print',  in_array('purchase.debit_note.print', $role_permissions), 
                      [ 'class' => 'input-icheck']); !!}
              </td>
              {{-- Purchase Category --}}
            <tr>
              <td>Purchase Type</td>
              <td>  {!! Form::checkbox('permissions[]', 'purchase_catagory.view', in_array('purchase_catagory.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}          
              </td>
              <td> {!! Form::checkbox('permissions[]', 'purchase_catagory.create', in_array('purchase_catagory.create', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase_catagory.update', in_array('purchase_catagory.update', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase_catagory.delete', in_array('purchase_catagory.delete', $role_permissions), 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td></td>
            </tr>

            </tr>

            <!-- End Purchase Module -->

            <tr>
            <td  style="background-color: ghostwhite;">Sale</td>
            <td  style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
              </tr>
            
            <!-- Sale Module -->

            
            {{-- Sales Order --}}
            <tr>
              <td>Sales Order</td>
              <td>   {!! Form::checkbox('radio_option[so_view]', 'so.view_all',  in_array('so.view_all', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}        
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'so.create',  in_array('so.create', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'so.update',  in_array('so.update', $role_permissions), 
                    [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'so.delete',  in_array('so.delete', $role_permissions), 
                    [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'so.print',  in_array('so.print', $role_permissions), 
                    [ 'class' => 'input-icheck']); !!}
              </td>
            </tr>
  
            {{-- Delivery Note--}}
            <tr>
              <td>Delivery Note</td>
              <td> {!! Form::checkbox('permissions[]', 'sale.delivery_note',  in_array('sale.delivery_note', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}       
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'sale.delivery_note.add',  in_array('sale.delivery_note.add', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'sale.delivery_note.edit',  in_array('sale.delivery_note.edit', $role_permissions), 
                      [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'sale.delivery_note.delete',  in_array('sale.delivery_note.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'sale.delivery_note.print',  in_array('sale.delivery_note.print', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
            </tr>
            {{-- Sale Invoice --}}
            <tr>
              <td>Sale Invoice</td>
              <td>  {!! Form::checkbox('permissions[]', 'sale.sale_invoice',  in_array('sale.sale_invoice', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}     
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'sale.sale_invoice.add',  in_array('sale.sale_invoice.add', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'sale.sale_invoice.edit',  in_array('sale.sale_invoice.edit', $role_permissions), 
                      [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'sale.sale_invoice.delete',  in_array('sale.sale_invoice.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'sale.sale_invoice.print',  in_array('sale.sale_invoice.print', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
            </tr>
  
            {{-- Sale Return Invoice --}}
  
            <tr>
              <td>Sale Return Invoice</td>
              <td>  {!! Form::checkbox('permissions[]', 'sale.sale_return_invoice',  in_array('sale.sale_return_invoice', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}        
              </td>
              <td> {!! Form::checkbox('permissions[]', 'sale.sale_return_invoice.add',  in_array('sale.sale_return_invoice.add', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'sale.sale_return_invoice.edit',  in_array('sale.sale_return_invoice.edit', $role_permissions), 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'sale.sale_return_invoice.delete',  in_array('sale.sale_return_invoice.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'sale.sale_return_invoice.print',  in_array('sale.sale_return_invoice.print', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
            </tr>

            <!-- Sale Type -->
            <tr>
              <td>Sale Type</td>
              <td>  {!! Form::checkbox('permissions[]', 'sale_type.view',  in_array('sale_type.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}        
              </td>
              <td> {!! Form::checkbox('permissions[]', 'sale_type.add',  in_array('sale_type.add', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'sale_type.edit',  in_array('sale_type.edit', $role_permissions), 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'sale_type.delete',  in_array('sale_type.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
               
              </td>
            </tr>


            <!-- Milling  -->
  
            <tr>
              <td>Milling</td>
              <td>  {!! Form::checkbox('permissions[]', 'milling.view',  in_array('milling.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}        
              </td>
              <td> {!! Form::checkbox('permissions[]', 'milling.add',  in_array('milling.add', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'milling.edit',  in_array('milling.edit', $role_permissions), 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'milling.delete',  in_array('milling.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
               
              </td>
            </tr>
            

            <!-- End Sale Module -->

            <!-- Inventory Module -->

            <tr>
            <td  style="background-color: ghostwhite;">Inventory</td>
            <td  style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
              </tr>

            <tr>
              <td>Product</td>
              <td> {!! Form::checkbox('permissions[]', 'product.view', in_array('product.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}             
              </td>
              <td>   {!! Form::checkbox('permissions[]', 'product.create', in_array('product.create', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}      
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'product.update', in_array('product.update', $role_permissions), 
                  [ 'class' => 'input-icheck']); !!}      
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'product.delete', in_array('product.delete', $role_permissions), 
                  [ 'class' => 'input-icheck']); !!} 
              </td>
              <td></td>
            </tr>

            {{-- Brands --}}
            <tr>
              <td>Brands</td>
              <td> {!! Form::checkbox('permissions[]', 'brand.view', in_array('brand.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}            
              </td>
              <td> {!! Form::checkbox('permissions[]', 'brand.create', in_array('brand.create', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'brand.update', in_array('brand.update', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'brand.delete', in_array('brand.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td></td>
            </tr>
            {{-- Unit --}}
            <tr>
              <td>Unit</td>
              <td>  {!! Form::checkbox('permissions[]', 'unit.view', in_array('unit.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}             
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'unit.create', in_array('unit.create', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}    
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'unit.update', in_array('unit.update', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}    
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'unit.delete', in_array('unit.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td></td>
            </tr>

            {{-- Product Type --}}
            <tr>
              <td>Product Type</td>
              <td>  {!! Form::checkbox('permissions[]', 'pro_type.view', in_array('pro_type.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}          
              </td>
              <td> {!! Form::checkbox('permissions[]', 'pro_type.create', in_array('pro_type.create', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'pro_type.update', in_array('pro_type.update', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'pro_type.delete', in_array('pro_type.delete', $role_permissions), 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td></td>
            </tr>

            {{-- Product Category --}}
            <tr>
              <td>Product Category</td>
              <td>  {!! Form::checkbox('permissions[]', 'pro_catagory.view', in_array('pro_catagory.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}          
              </td>
              <td> {!! Form::checkbox('permissions[]', 'pro_catagory.create', in_array('pro_catagory.create', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'pro_catagory.update', in_array('pro_catagory.update', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'pro_catagory.delete', in_array('pro_catagory.delete', $role_permissions), 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td></td>
            </tr>

            {{-- Sub Category --}}
            <tr>
              <td>Sub Type</td>
              <td>  {!! Form::checkbox('permissions[]', 'sub_catagory.view', in_array('sub_catagory.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}          
              </td>
              <td> {!! Form::checkbox('permissions[]', 'sub_catagory.create', in_array('sub_catagory.create', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'sub_catagory.update', in_array('sub_catagory.update', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'sub_catagory.delete', in_array('sub_catagory.delete', $role_permissions), 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td></td>
            </tr>


            {{-- Milling Type --}}
            <tr>
              <td>Milling Category</td>
              <td> {!! Form::checkbox('permissions[]', 'milling_type.view', in_array('milling_type.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}          
              </td>
              <td> {!! Form::checkbox('permissions[]', 'milling_type.create', in_array('milling_type.create', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'milling_type.update', in_array('milling_type.update', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'milling_type.delete', in_array('milling_type.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td></td>
            </tr>

            <tr>
              {{-- Tank --}}
              <td>Tank</td>
              <td>   {!! Form::checkbox('permissions[]', 'tank.view', in_array('tank.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}        
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'tank.create', in_array('tank.create', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'tank.update', in_array('tank.update', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'tank.delete', in_array('tank.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td></td>
            </tr>
           
            
            
            {{-- Tank Transaction --}}
            <tr>
              <td>Tank Transaction</td>
              <td>  {!! Form::checkbox('permissions[]', 'tank_tran.view', in_array('tank_tran.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}           
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'tank_tran.create', in_array('tank_tran.create', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}  
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'tank_tran.update', in_array('tank_tran.update', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'tank_tran.delete', in_array('tank_tran.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td></td>
            </tr>
  
            <tr>
             <td>Product Import </td>
             <td>  {!! Form::checkbox('permissions[]', 'import', in_array('import', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}           
             </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            </tr>

            <!-- End Inventory Module -->

            <!-- Manufacturing -->

            <tr>
            <td style="background-color: ghostwhite;">Manufacturing</td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td> 
            </tr>

            
            <tr>
              <td>Recipe</td>
              <td>  {!! Form::checkbox('permissions[]', 'receipe.view', in_array('receipe.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}           
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'receipe.create', in_array('receipe.create', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}  
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'receipe.update', in_array('receipe.update', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'receipe.delete', in_array('receipe.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'receipe.print', in_array('receipe.print', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              </tr>
            <tr>
              <td>Production</td>
              <td>  {!! Form::checkbox('permissions[]', 'production.view', in_array('production.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}           
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'production.create', in_array('production.create', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}  
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'production.update', in_array('production.update', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'production.delete', in_array('production.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'production.print', in_array('production.print', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
            </tr>

            <tr>
              <td>Multy Production</td>
              <td>  {!! Form::checkbox('permissions[]', 'multy_production.view', in_array('multy_production.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}           
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'multy_production.create', in_array('multy_production.create', $role_permissions), 
                [ 'class' => 'input-icheck']); !!}  
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'multy_production.update', in_array('multy_production.update', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'multy_production.delete', in_array('multy_production.delete', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'multy_production.print', in_array('multy_production.print', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
            </tr>
            

            


            <!-- End Manufacturing -->

               {{-- Adjustment --}}

               <tr>
                <td style="background-color: ghostwhite;">Stock Adjustment</td>
                <td style="background-color: ghostwhite;"></td>
                <td style="background-color: ghostwhite;"></td>
                <td style="background-color: ghostwhite;"></td>
                <td style="background-color: ghostwhite;"></td>
                <td style="background-color: ghostwhite;"></td>
                <td style="background-color: ghostwhite;"></td> 
                </tr>
    

               <tr>
                <td>@lang( 'Stock Adjustment' )</td>
                   
                <td>   {!! Form::checkbox('permissions[]', 'adjustment.view',in_array('adjustment.view', $role_permissions),
                [ 'class' => 'input-icheck']); !!}          
                  </td>
                <td>  {!! Form::checkbox('permissions[]',  'adjustment.create',in_array('adjustment.create', $role_permissions),
                  [ 'class' => 'input-icheck']); !!} 
                  </td>
                <td>  {!! Form::checkbox('permissions[]',  'adjustment.update',in_array('adjustment.update', $role_permissions),
                  [ 'class' => 'input-icheck']); !!} 
                  </td>
                <td>    {!! Form::checkbox('permissions[]', 'adjustment.delete',in_array('adjustment.delete', $role_permissions),
                  [ 'class' => 'input-icheck']); !!}
                  </td>
                <td>    {!! Form::checkbox('permissions[]', 'adjustment.print',in_array('adjustment.print', $role_permissions),
                  [ 'class' => 'input-icheck']); !!}
                </td>
                
              </tr>

              {{-- User Management --}}


    

            <tr>
            <td  style="background-color: ghostwhite;">Reports</td>
            <td  style="background-color: ghostwhite;">Allow View And Print</td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            <td style="background-color: ghostwhite;"></td>
            </tr>
            <tr>
            <td>
               Trial Balance
              </td>
              <td>
                
                {!! Form::checkbox('permissions[]', 'trial_balance.view', in_array('trial_balance.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
               
              </td>
              <td>
               
              </td>
              <td>
                
              </td>
              <td></td>

            </tr>

            <tr>
            <td>
              Audit Trial
              </td>
              <td>
                
                {!! Form::checkbox('permissions[]', 'audit.view', in_array('audit.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
               
              </td>
              <td>
               
              </td>
              <td>
                
              </td>
              <td></td>

            </tr>

            <tr>
            <td>
               Inventory Valuation
              </td>
              <td>
                
                {!! Form::checkbox('permissions[]', 'valuation.view', in_array('valuation.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
               
              </td>
              <td>
               
              </td>
              <td>
                
              </td>
              <td></td>

            </tr>

            <tr>
            <td>
               Stock Report
              </td>
              <td>
                
                {!! Form::checkbox('permissions[]', 'stock.view', in_array('stock.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
               
              </td>
              <td>
               
              </td>
              <td>
                
              </td>
              <td></td>

            </tr>

            <tr>
            <td>
              Balance Sheet
              </td>
              <td>
                
                {!! Form::checkbox('permissions[]', 'balance_sheet.view', in_array('balance_sheet.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
               
              </td>
              <td>
               
              </td>
              <td>
                
              </td>
              <td></td>

            </tr>

            <tr>
            <td>
              Prift & Loss
              </td>
              <td>
                
                {!! Form::checkbox('permissions[]', 'profit_loss.view', in_array('profit_loss.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
               
              </td>
              <td>
               
              </td>
              <td>
                
              </td>
              <td></td>

            </tr>

            <tr>
            <td>
              Account Ledger
              </td>
              <td>
                
                {!! Form::checkbox('permissions[]', 'account_ledger.view', in_array('account_ledger.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
               
              </td>
              <td>
               
              </td>
              <td>
                
              </td>
              <td></td>

            </tr>

            <tr>
            <td>
              Recipe Ingredient Report
              </td>
              <td>
                
                {!! Form::checkbox('permissions[]', 'ingredient_report.view', in_array('ingredient_report.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
               
              </td>
              <td>
               
              </td>
              <td>
                
              </td>
              <td></td>

            </tr>


            <tr>
            <td>
              Product Wise Purchase Return report
              </td>
              <td>
                
                {!! Form::checkbox('permissions[]', 'product_wise_return_report.view', in_array('product_wise_return_report.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
               
              </td>
              <td>
               
              </td>
              <td>
                
              </td>
              <td></td>

            </tr>

            <tr>
            <td>
              Sale Report
              </td>
              <td>
                
                {!! Form::checkbox('permissions[]', 'sale_report.view', in_array('sale_report.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
               
              </td>
              <td>
               
              </td>
              <td>
                
              </td>
              <td></td>

            </tr>


            <tr>
            <td>
              Purchase Return Report
              </td>
              <td>
                
                {!! Form::checkbox('permissions[]', 'purchase_return_report.view', in_array('purchase_return_report.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
               
              </td>
              <td>
               
              </td>
              <td>
                
              </td>
              <td></td>

            </tr>

            <tr>
            <td>
             Product Wise Sale Return report
              </td>
              <td>
                
                {!! Form::checkbox('permissions[]', 'product_sale_report.view', in_array('product_sale_report.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
               
              </td>
              <td>
               
              </td>
              <td>
                
              </td>
              <td></td>

            </tr>

            <tr>
            <td>
              Sale Return Report
              </td>
              <td>
                
                {!! Form::checkbox('permissions[]', 'sale_return_report.view', in_array('sale_return_report.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
               
              </td>
              <td>
               
              </td>
              <td>
                
              </td>
              <td></td>

            </tr>

            <tr>
            <td>
              Product Wise Purchase Report
              </td>
              <td>
                
                {!! Form::checkbox('permissions[]', 'product_purchase_report.view', in_array('product_purchase_report.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
               
              </td>
              <td>
               
              </td>
              <td>
                
              </td>
              <td></td>

            </tr>


            <tr>
            <td>
              Purchase Report
              </td>
              <td>
                
                {!! Form::checkbox('permissions[]', 'purchase_report.view', in_array('purchase_report.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
               
              </td>
              <td>
               
              </td>
              <td>
                
              </td>
              <td></td>

            </tr>

            <tr>
            <td>
              Product Wise Sale Return Report
              </td>
              <td>
                
                {!! Form::checkbox('permissions[]', 'product_sale_retur_report.view', in_array('product_sale_retur_report.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
               
              </td>
              <td>
               
              </td>
              <td>
                
              </td>
              <td></td>

            </tr>

            <tr>
            <td>
              Activity Log
              </td>
              <td>
                
                {!! Form::checkbox('permissions[]', 'activity_log.view', in_array('activity_log.view', $role_permissions), 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
               
              </td>
              <td>
               
              </td>
              <td>
                
              </td>
              <td></td>

            </tr>





              
          </tbody>
          </table>

      <hr>

        <div class="row">
  
        <div class="col-sm-12 text-center fixed-button">
        
          <button type="submit"  class="btn-big btn-primary btn-flat" style="margin-right: 5px;" accesskey="s">Update & Close</button>
          <button class="btn-big btn-danger" type="button" onclick="window.history.back()">Close</button>  
        </div>
        </div>

        {!! Form::close() !!}
    @endcomponent
</section>


<!-- /.content -->
@endsection


@section('javascript')
    <script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
          $('#user_roles_manage').DataTable({
              ordering: false, // Disable sorting
              dom: 'Bfrtip',   // Display only the buttons section
              buttons: [],
              searching: true, // Enable searching
              paging: false 
          });

       
       })
</script>
@endsection