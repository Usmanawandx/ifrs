@extends('layouts.app')
@section('title', __('role.add_role'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>@lang( 'role.add_role' )</h1>
</section>

<!-- Main content -->
<section class="content">
    @php
      $pos_settings = !empty(session('business.pos_settings')) ? json_decode(session('business.pos_settings'), true) : [];
    @endphp
    @component('components.widget', ['class' => 'box-primary'])
        {!! Form::open(['url' => action('RoleController@store'), 'method' => 'post', 'id' => 'role_add_form' ]) !!}
        <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('name', __( 'user.role_name' ) . ':*') !!}
              {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'user.role_name' ) ]); !!}
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
              {!! Form::checkbox('is_service_staff', 1, false, 
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
          <input type="radio"  name="check_trusted" value="1">
      </div>
      <div class="col-md-12">
          <label>
            Can login Only Trusted Pc
          </label>
          <input type="radio" name="check_trusted" value="0">
      </div>
      <div class="col-md-12">
          <label>
            Allow Convert 
          </label>
          {!! Form::checkbox('permissions[]', 'convert', false  , 
                [ 'class' => 'input-icheck']); !!} 
      </div>
      
      
      
        </div>
        </br>
        </br>
        </br>
        </br>
        <table class="table " id="user_roles_manage">
          <thead>
          <tr>
            <th>Reports</th>
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
              <td>  {!! Form::checkbox('permissions[]', 'accounts.view', false, 
                [ 'class' => 'input-icheck']); !!}        
              </td>
              <td> {!! Form::checkbox('permissions[]', 'accounts.access',false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'accounts.edit', false, 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'accounts.delete',  false,
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td></td>
            </tr>
  
            {{-- Vouchers --}}
 
            
  
            <tr>
              <td>Journal Vocuher</td>
              <td>  {!! Form::checkbox('permissions[]', 'account.journal_vouchers', false  , 
                [ 'class' => 'input-icheck']); !!}        
              </td>
              <td> {!! Form::checkbox('permissions[]', 'journal_voucher.add',    false,
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'journal_voucher.edit',  false , 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'journal_voucher.delete', false , 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'journal_voucher.print',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
            </tr>
  
            <tr>
              <td>Bank Book</td>
              <td>  {!! Form::checkbox('permissions[]', 'bank_book.view',  false, 
                [ 'class' => 'input-icheck']); !!}        
              </td>
              <td> {!! Form::checkbox('permissions[]', 'bank_book.add',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'bank_book.edit', false, 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'bank_book.delete',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'bank_book.print', false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
            </tr>
  
            <tr>
              <td>Payment Voucher</td>
              <td>  {!! Form::checkbox('permissions[]', 'account.cash_payment_vouchers', false, 
                [ 'class' => 'input-icheck']); !!}        
              </td>
              <td> {!! Form::checkbox('permissions[]', 'account.cash_payment_vouchers.add',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'account.cash_payment_vouchers.edit',  false, 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'account.cash_payment_vouchers.delete',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'account.cash_payment_vouchers.print',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
            </tr>
  
            <tr>
              <td>Receipt  Voucher</td>
              <td>  {!! Form::checkbox('permissions[]', 'account.receiept_vouchers',  false, 
                [ 'class' => 'input-icheck']); !!}        
              </td>
              <td> {!! Form::checkbox('permissions[]', 'account.receiept_vouchers.add',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'account.receiept_vouchers.edit',  false, 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'account.receiept_vouchers.delete',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'account.receiept_vouchers.print',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
            </tr>
  
            <tr>
              <td>Cash Receipt  Voucher</td>
              <td>  {!! Form::checkbox('permissions[]', 'cash_received_voucher.view',  false, 
                [ 'class' => 'input-icheck']); !!}        
              </td>
              <td> {!! Form::checkbox('permissions[]', 'cash_received_voucher.add',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'cash_received_voucher.edit',  false, 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'cash_received_voucher.delete',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'cash_received_voucher.print',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
            </tr>
  
            
            <tr>
              <td>Payment  Voucher</td>
              <td>  {!! Form::checkbox('permissions[]', 'account.payment_vouchers',  false, 
                [ 'class' => 'input-icheck']); !!}        
              </td>
              <td> {!! Form::checkbox('permissions[]', 'account.payment_vouchers.add', false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'account.payment_vouchers.edit',  false, 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'account.payment_vouchers.delete',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'account.payment_vouchers.print',  false, 
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
              <td>  {!! Form::checkbox('permissions[customer_view]', 'customer.view',  false, 
                [ 'class' => 'input-icheck']); !!}            
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'customer.create',  false, 
                [ 'class' => 'input-icheck']); !!}           
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'customer.update',  false, 
                [ 'class' => 'input-icheck']); !!}        
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'customer.delete',  false, 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td></td>
            </tr>

            {{-- Supplier --}}
            <tr>
              <td>@lang( 'role.supplier' )</td>
              <td> {!! Form::checkbox('permissions[]', 'supplier.view',  false, 
                [ 'class' => 'input-icheck']); !!}
                </td>
              <td>        {!! Form::checkbox('permissions[]', 'supplier.create',  false, 
                [ 'class' => 'input-icheck']); !!} 
                </td>
              <td>
                {!! Form::checkbox('permissions[]', 'supplier.update',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'supplier.delete',  false, 
                [ 'class' => 'input-icheck']); !!} 
              </td>     
              <td></td>         
            </tr>

            <tr>
              <td>Customer Group</td>
              <td> {!! Form::checkbox('permissions[]', 'group.view',  false, 
                [ 'class' => 'input-icheck']); !!}
                </td>
              <td>        {!! Form::checkbox('permissions[]', 'group.create',  false, 
                [ 'class' => 'input-icheck']); !!} 
                </td>
              <td>
                {!! Form::checkbox('permissions[]', 'group.update',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'group.delete',  false, 
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
            
              <td>   {!! Form::checkbox('permissions[]', 'contractor.view',  false, 
                  [ 'class' => 'input-icheck']); !!}          
                    </td>
              <td>  {!! Form::checkbox('permissions[]', 'contractor.create',  false, 
                [ 'class' => 'input-icheck']); !!} 
                </td>
              <td>  {!! Form::checkbox('permissions[]', 'contractor.update',  false, 
                [ 'class' => 'input-icheck']); !!} 
                </td>
              <td>    {!! Form::checkbox('permissions[]', 'contractor.delete',  false, 
                [ 'class' => 'input-icheck']); !!}
                </td>
                <td></td>
            </tr>

            <tr>
              <td>Contractor Rate</td>
            
              <td>   {!! Form::checkbox('permissions[]', 'contractor_rate.view',  false, 
                  [ 'class' => 'input-icheck']); !!}          
                    </td>
              <td>  {!! Form::checkbox('permissions[]', 'contractor_rate.create',  false, 
                [ 'class' => 'input-icheck']); !!} 
                </td>
              <td>  {!! Form::checkbox('permissions[]', 'contractor_rate.update', false, 
                [ 'class' => 'input-icheck']); !!} 
                </td>
              <td>    {!! Form::checkbox('permissions[]', 'contractor_rate.delete',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td></td>
            </tr>


            <tr>
              <td>Transporter Rate</td>
            
              <td>   {!! Form::checkbox('permissions[]', 'transporter_rate.view',  false, 
                  [ 'class' => 'input-icheck']); !!}          
                    </td>
              <td>  {!! Form::checkbox('permissions[]', 'transporter_rate.create',  false, 
                [ 'class' => 'input-icheck']); !!} 
                </td>
              <td>  {!! Form::checkbox('permissions[]', 'transporter_rate.update',  false, 
                [ 'class' => 'input-icheck']); !!} 
                </td>
              <td>    {!! Form::checkbox('permissions[]', 'transporter_rate.delete',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td></td>
            </tr>

            <tr>
              <td>Vehicle</td>
            
              <td>   {!! Form::checkbox('permissions[]', 'vehicle.view',  false, 
                  [ 'class' => 'input-icheck']); !!}          
                    </td>
              <td>  {!! Form::checkbox('permissions[]', 'vehicle.create',  false, 
                [ 'class' => 'input-icheck']); !!} 
                </td>
              <td>  {!! Form::checkbox('permissions[]', 'vehicle.update',  false, 
                [ 'class' => 'input-icheck']); !!} 
                </td>
              <td>    {!! Form::checkbox('permissions[]', 'vehicle.delete',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td></td>
            </tr>

            <tr>
              <td>Sales Agent</td>
            
              <td>   {!! Form::checkbox('permissions[]', 'sale_agent.view',  false, 
                  [ 'class' => 'input-icheck']); !!}          
                    </td>
              <td>  {!! Form::checkbox('permissions[]', 'sale_agent.create', false, 
                [ 'class' => 'input-icheck']); !!} 
                </td>
              <td>  {!! Form::checkbox('permissions[]', 'sale_agent.update', false, 
                [ 'class' => 'input-icheck']); !!} 
                </td>
              <td>    {!! Form::checkbox('permissions[]', 'sale_agent.delete', false, 
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
              <td>  {!! Form::checkbox('permissions[]', 'purchase.purchase_req.view',  false, 
                [ 'class' => 'input-icheck']); !!}          
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'purchase.purchase_req.add',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase.purchase_req.edit',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase.purchase_req.delete',  false,
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase.purchase_req.print',  false,
                [ 'class' => 'input-icheck']); !!}
              </td>
             
            </tr>
  
            <tr>
              <td>Purchase Order</td>
              <td> {!! Form::checkbox('permissions[]', 'purchase_order.view_all',  false,
                [ 'class' => 'input-icheck']); !!}         
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'purchase_order.create',  false,
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase_order.update',  false,
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase_order.delete',  false,
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase_order.print',  false,
                [ 'class' => 'input-icheck']); !!}
              </td>
            </tr>
            {{-- GRN --}}
            <tr>
              <td>Grn</td>
              <td> {!! Form::checkbox('permissions[]', 'purchase.view',  false, 
                [ 'class' => 'input-icheck']); !!}          
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'purchase.create',   false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase.update',   false, 
                  [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase.delete',   false, 
                  [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase.print',   false, 
                  [ 'class' => 'input-icheck']); !!}
              </td>
            </tr>
            {{-- Order Invoice --}}
            <tr>
              <td>Purchase Order Invoice</td>
              <td> {!! Form::checkbox('permissions[]', 'purchase_invoice.view',   false, 
                [ 'class' => 'input-icheck']); !!}         
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'purchase_invoice.add', false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase_invoice.edit', false, 
                      [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase_invoice.delete', false, 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase_invoice.print', false, 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
            </tr>
  
            {{-- Debit Note--}}
  
            <tr>
              <td>Debit Note</td>
              <td> {!! Form::checkbox('permissions[]', 'purchase.debit_note',   false, 
                [ 'class' => 'input-icheck']); !!}         
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'purchase.debit_note.add',   false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase.debit_note.edit',  false, 
                      [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase.debit_note.delete',   false, 
                      [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase.debit_note.print',   false, 
                      [ 'class' => 'input-icheck']); !!}
              </td>
              
            {{-- Purchase Category --}}
            <tr>
              <td>Purchase Caetgory</td>
              <td>  {!! Form::checkbox('permissions[]', 'purchase_catagory.view',  false, 
                [ 'class' => 'input-icheck']); !!}          
              </td>
              <td> {!! Form::checkbox('permissions[]', 'purchase_catagory.create',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase_catagory.update',  false, 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'purchase_catagory.delete',  false, 
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
              <td>   {!! Form::checkbox('radio_option[so_view]', 'so.view_all',   false, 
                [ 'class' => 'input-icheck']); !!}        
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'so.create',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'so.update',   false, 
                    [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'so.delete',  false, 
                    [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'so.print',   false, 
                    [ 'class' => 'input-icheck']); !!}
              </td>
            </tr>
  
            {{-- Delivery Note--}}
            <tr>
              <td>Delivery Note</td>
              <td> {!! Form::checkbox('permissions[]', 'sale.delivery_note',   false, 
                [ 'class' => 'input-icheck']); !!}       
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'sale.delivery_note.add',   false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'sale.delivery_note.edit',   false, 
                      [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'sale.delivery_note.delete',   false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'sale.delivery_note.print',   false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
            </tr>
            {{-- Sale Invoice --}}
            <tr>
              <td>Sale Invoice</td>
              <td>  {!! Form::checkbox('permissions[]', 'sale.sale_invoice',   false, 
                [ 'class' => 'input-icheck']); !!}     
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'sale.sale_invoice.add',  false, 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'sale.sale_invoice.edit',  false, 
                      [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'sale.sale_invoice.delete',   false, 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'sale.sale_invoice.print',   false, 
                [ 'class' => 'input-icheck']); !!} 
              </td>
            </tr>
  
            {{-- Sale Return Invoice --}}
  
            <tr>
              <td>Sale Return Invoice</td>
              <td>  {!! Form::checkbox('permissions[]', 'sale.sale_return_invoice',   false, 
                [ 'class' => 'input-icheck']); !!}        
              </td>
              <td> {!! Form::checkbox('permissions[]', 'sale.sale_return_invoice.add',   false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'sale.sale_return_invoice.edit',   false, 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'sale.sale_return_invoice.delete',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'sale.sale_return_invoice.print',   false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
            </tr>

            <!-- Sale Type -->
            <tr>
              <td>Sale Type</td>
              <td>  {!! Form::checkbox('permissions[]', 'sale_type.view',   false, 
                [ 'class' => 'input-icheck']); !!}        
              </td>
              <td> {!! Form::checkbox('permissions[]', 'sale_type.add',   false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'sale_type.edit',   false, 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'sale_type.delete',   false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
               
              </td>
            </tr>


            <!-- Milling  -->
  
            <tr>
              <td>Milling</td>
              <td>  {!! Form::checkbox('permissions[]', 'milling.view',   false, 
                [ 'class' => 'input-icheck']); !!}        
              </td>
              <td> {!! Form::checkbox('permissions[]', 'milling.add',   false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'milling.edit',   false, 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'milling.delete',  false, 
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
              <td> {!! Form::checkbox('permissions[]', 'product.view',  false, 
                [ 'class' => 'input-icheck']); !!}             
              </td>
              <td>   {!! Form::checkbox('permissions[]', 'product.create',  false, 
                [ 'class' => 'input-icheck']); !!}      
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'product.update',  false, 
                  [ 'class' => 'input-icheck']); !!}      
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'product.delete',  false, 
                  [ 'class' => 'input-icheck']); !!} 
              </td>
              <td></td>
            </tr>

            {{-- Brands --}}
            <tr>
              <td>Brands</td>
              <td> {!! Form::checkbox('permissions[]', 'brand.view',  false, 
                [ 'class' => 'input-icheck']); !!}            
              </td>
              <td> {!! Form::checkbox('permissions[]', 'brand.create',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'brand.update',  false, 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'brand.delete',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td></td>
            </tr>
            {{-- Unit --}}
            <tr>
              <td>Unit</td>
              <td>  {!! Form::checkbox('permissions[]', 'unit.view',  false, 
                [ 'class' => 'input-icheck']); !!}             
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'unit.create', false, 
                [ 'class' => 'input-icheck']); !!}    
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'unit.update',  false, 
                [ 'class' => 'input-icheck']); !!}    
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'unit.delete',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td></td>
            </tr>

            {{-- Product Type --}}
            <tr>
              <td>Product Type</td>
              <td>  {!! Form::checkbox('permissions[]', 'pro_type.view',  false, 
                [ 'class' => 'input-icheck']); !!}          
              </td>
              <td> {!! Form::checkbox('permissions[]', 'pro_type.create', false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'pro_type.update', false, 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'pro_type.delete', false, 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td></td>
            </tr>

            {{-- Product Category --}}
            <tr>
              <td>Product Category</td>
              <td>  {!! Form::checkbox('permissions[]', 'pro_catagory.view', false, 
                [ 'class' => 'input-icheck']); !!}          
              </td>
              <td> {!! Form::checkbox('permissions[]', 'pro_catagory.create', false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'pro_catagory.update', false, 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'pro_catagory.delete', false, 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td></td>
            </tr>

            {{-- Sub Category --}}
            <tr>
              <td>Sub Type</td>
              <td>  {!! Form::checkbox('permissions[]', 'sub_catagory.view',  false, 
                [ 'class' => 'input-icheck']); !!}          
              </td>
              <td> {!! Form::checkbox('permissions[]', 'sub_catagory.create',  false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'sub_catagory.update', false, 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'sub_catagory.delete', false, 
                      [ 'class' => 'input-icheck']); !!} 
              </td>
              <td></td>
            </tr>


            {{-- Milling Type --}}
            <tr>
              <td>Milling Category</td>
              <td> {!! Form::checkbox('permissions[]', 'milling_type.view', false, 
                [ 'class' => 'input-icheck']); !!}          
              </td>
              <td> {!! Form::checkbox('permissions[]', 'milling_type.create', false, 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'milling_type.update', false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'milling_type.delete', false, 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td></td>
            </tr>

            <tr>
              {{-- Tank --}}
              <td>Tank</td>
              <td>   {!! Form::checkbox('permissions[]', 'tank.view', false, 
                [ 'class' => 'input-icheck']); !!}        
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'tank.create', false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'tank.update', false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'tank.delete', false, 
                [ 'class' => 'input-icheck']); !!}
              </td>
              <td></td>
            </tr>
           
            
            
            {{-- Tank Transaction --}}
            <tr>
              <td>Tank Transaction</td>
              <td>  {!! Form::checkbox('permissions[]', 'tank_tran.view',  false, 
                [ 'class' => 'input-icheck']); !!}           
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'tank_tran.create', false, 
                [ 'class' => 'input-icheck']); !!}  
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'tank_tran.update', false, 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'tank_tran.delete', false, 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td></td>
            </tr>
  
             <tr>
            <td>Product Import </td>
             <td>  {!! Form::checkbox('permissions[]', 'import',false, 
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
              <td>  {!! Form::checkbox('permissions[]', 'receipe.view', false, 
                [ 'class' => 'input-icheck']); !!}           
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'receipe.create', false, 
                [ 'class' => 'input-icheck']); !!}  
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'receipe.update',  false, 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'receipe.delete',  false, 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'receipe.print', false,
                [ 'class' => 'input-icheck']); !!} 
              </td>
          
            </tr>
            <tr>
              <td>Production</td>
              <td>  {!! Form::checkbox('permissions[]', 'production.view',  false, 
                [ 'class' => 'input-icheck']); !!}           
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'production.create',  false, 
                [ 'class' => 'input-icheck']); !!}  
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'production.update',  false, 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'production.delete',  false, 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'production.print', false, 
                [ 'class' => 'input-icheck']); !!} 
              </td>
            </tr>

            <tr>
              <td>Multy Production</td>
              <td>  {!! Form::checkbox('permissions[]', 'multy_production.view', false, 
                [ 'class' => 'input-icheck']); !!}           
              </td>
              <td>  {!! Form::checkbox('permissions[]', 'multy_production.create',  false, 
                [ 'class' => 'input-icheck']); !!}  
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'multy_production.update',  false, 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'multy_production.delete',  false, 
                [ 'class' => 'input-icheck']); !!} 
              </td>
              <td>
                {!! Form::checkbox('permissions[]', 'multy_production.print',false, 
                [ 'class' => 'input-icheck']); !!} 
              </td>
            </tr>
            


            <!-- End Manufacturing -->

    
              {{-- Adjustment --}}
              <tr>
                <td>@lang( 'Stock Adjustment' )</td>
                   
                <td>   {!! Form::checkbox('permissions[]', 'adjustment.view',  false, 
                [ 'class' => 'input-icheck']); !!}          
                  </td>
                <td>  {!! Form::checkbox('permissions[]',  'adjustment.create',  false, 
                  [ 'class' => 'input-icheck']); !!} 
                  </td>
                <td>  {!! Form::checkbox('permissions[]',  'adjustment.update',  false, 
                  [ 'class' => 'input-icheck']); !!} 
                  </td>
                <td>    {!! Form::checkbox('permissions[]', 'adjustment.delete',  false, 
                  [ 'class' => 'input-icheck']); !!}
                  </td>
                  <td></td>
              </tr>
       
              <tr>
              

      

            <!-- Reports -->

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
                
                {!! Form::checkbox('permissions[]', 'trial_balance.view',false, 
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
                
                {!! Form::checkbox('permissions[]', 'audit.view',false, 
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
                
                {!! Form::checkbox('permissions[]', 'valuation.view', false, 
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
                
                {!! Form::checkbox('permissions[]', 'stock.view',false, 
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
                
                {!! Form::checkbox('permissions[]', 'balance_sheet.view', false, 
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
                
                {!! Form::checkbox('permissions[]', 'profit_loss.view', false, 
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
                
                {!! Form::checkbox('permissions[]', 'account_ledger.view', false, 
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
                
                {!! Form::checkbox('permissions[]', 'ingredient_report.view', false, 
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
                
                {!! Form::checkbox('permissions[]', 'product_wise_return_report.view', false, 
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
                
                {!! Form::checkbox('permissions[]', 'sale_report.view',false, 
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
                
                {!! Form::checkbox('permissions[]', 'purchase_return_report.view', false, 
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
                
                {!! Form::checkbox('permissions[]', 'product_sale_report.view', false, 
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
                
                {!! Form::checkbox('permissions[]', 'sale_return_report.view',false, 
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
                
                {!! Form::checkbox('permissions[]', 'product_purchase_report.view',false, 
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
                
                {!! Form::checkbox('permissions[]', 'purchase_report.view', false, 
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
                
                {!! Form::checkbox('permissions[]', 'product_sale_retur_report.view', false, 
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
                
                {!! Form::checkbox('permissions[]', 'activity_log.view', false, 
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




            <!-- End Reports Section -->
              
          </tbody>
        </table>


    <hr>


       
        
        @if(in_array('tables', $enabled_modules))
          <div class="row">
            <div class="col-md-3">

            </div>
            <div class="col-md-9">
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'access_tables', false, 
                    [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.access_tables') }}
                  </label>
                </div>
              </div>
            </div>
          </div>
        @endif

        <div class="row">
        <div class="col-sm-12 text-center fixed-button">
        
           <button type="submit"  class="btn-big btn-primary btn-flat" style="margin-right: 5px;" accesskey="s">@lang( 'messages.save' )</button>
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
            buttons: []   ,
            paging: false 
        });

       })
</script>
@endsection