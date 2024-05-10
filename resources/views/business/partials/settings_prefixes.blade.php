<div class="pos-tab-content">
     <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $purchase_prefix = '';
                    if(!empty($business->ref_no_prefixes['purchase'])){
                        $purchase_prefix = $business->ref_no_prefixes['purchase'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[purchase]', __('lang_v1.purchase') . ':') !!}
                {!! Form::text('ref_no_prefixes[purchase]', $purchase_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $purchase_return = '';
                    if(!empty($business->ref_no_prefixes['purchase_return'])){
                        $purchase_return = $business->ref_no_prefixes['purchase_return'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[purchase_return]', __('lang_v1.purchase_return') . ':') !!}
                {!! Form::text('ref_no_prefixes[purchase_return]', $purchase_return, ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $purchase_requision = '';
                    if(!empty($business->ref_no_prefixes['purchase_requision'])){
                        $purchase_requision = $business->ref_no_prefixes['purchase_requision'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[purchase_requision]', __('Purchase Requisition') . ':') !!}
                {!! Form::text('ref_no_prefixes[purchase_requision]', $purchase_requision, ['class' => 'form-control']); !!}
            </div>
        </div>


        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $purchase_invoice = '';
                    if(!empty($business->ref_no_prefixes['purchase_invoice'])){
                        $purchase_invoice = $business->ref_no_prefixes['purchase_invoice'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[purchase_invoice]', __('Purchase Invoice') . ':') !!}
                {!! Form::text('ref_no_prefixes[purchase_invoice]', $purchase_invoice, ['class' => 'form-control']); !!}
            </div>
        </div>
        
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $purchase_order_prefix = !empty($business->ref_no_prefixes['purchase_order']) ? $business->ref_no_prefixes['purchase_order'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[purchase_order]', __('lang_v1.purchase_order') . ':') !!}
                {!! Form::text('ref_no_prefixes[purchase_order]', $purchase_order_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $stock_transfer_prefix = '';
                    if(!empty($business->ref_no_prefixes['stock_transfer'])){
                        $stock_transfer_prefix = $business->ref_no_prefixes['stock_transfer'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[stock_transfer]', __('lang_v1.stock_transfer') . ':') !!}
                {!! Form::text('ref_no_prefixes[stock_transfer]', $stock_transfer_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $stock_adjustment_prefix = '';
                    if(!empty($business->ref_no_prefixes['stock_adjustment'])){
                        $stock_adjustment_prefix = $business->ref_no_prefixes['stock_adjustment'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[stock_adjustment]', __('stock_adjustment.stock_adjustment') . ':') !!}
                {!! Form::text('ref_no_prefixes[stock_adjustment]', $stock_adjustment_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $sell_return_prefix = '';
                    if(!empty($business->ref_no_prefixes['sell_return'])){
                        $sell_return_prefix = $business->ref_no_prefixes['sell_return'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[sell_return]', __('lang_v1.sell_return') . ':') !!}
                {!! Form::text('ref_no_prefixes[sell_return]', $sell_return_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $expenses_prefix = '';
                    if(!empty($business->ref_no_prefixes['expense'])){
                        $expenses_prefix = $business->ref_no_prefixes['expense'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[expense]', __('expense.expenses') . ':') !!}
                {!! Form::text('ref_no_prefixes[expense]', $expenses_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $contacts_prefix = '';
                    if(!empty($business->ref_no_prefixes['contacts'])){
                        $contacts_prefix = $business->ref_no_prefixes['contacts'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[contacts]', __('contact.contacts') . ':') !!}
                {!! Form::text('ref_no_prefixes[contacts]', $contacts_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $purchase_payment = '';
                    if(!empty($business->ref_no_prefixes['purchase_payment'])){
                        $purchase_payment = $business->ref_no_prefixes['purchase_payment'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[purchase_payment]', __('lang_v1.purchase_payment') . ':') !!}
                {!! Form::text('ref_no_prefixes[purchase_payment]', $purchase_payment, ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $sell_payment = '';
                    if(!empty($business->ref_no_prefixes['sell_payment'])){
                        $sell_payment = $business->ref_no_prefixes['sell_payment'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[sell_payment]', __('lang_v1.sell_payment') . ':') !!}
                {!! Form::text('ref_no_prefixes[sell_payment]', $sell_payment, ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $expense_payment = '';
                    if(!empty($business->ref_no_prefixes['expense_payment'])){
                        $expense_payment = $business->ref_no_prefixes['expense_payment'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[expense_payment]', __('lang_v1.expense_payment') . ':') !!}
                {!! Form::text('ref_no_prefixes[expense_payment]', $expense_payment, ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $business_location_prefix = '';
                    if(!empty($business->ref_no_prefixes['business_location'])){
                        $business_location_prefix = $business->ref_no_prefixes['business_location'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[business_location]', __('business.business_location') . ':') !!}
                {!! Form::text('ref_no_prefixes[business_location]', $business_location_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $username_prefix = !empty($business->ref_no_prefixes['username']) ? $business->ref_no_prefixes['username'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[username]', __('business.username') . ':') !!}
                {!! Form::text('ref_no_prefixes[username]', $username_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $subscription_prefix = !empty($business->ref_no_prefixes['subscription']) ? $business->ref_no_prefixes['subscription'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[subscription]', __('lang_v1.subscription_no') . ':') !!}
                {!! Form::text('ref_no_prefixes[subscription]', $subscription_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $draft_prefix = !empty($business->ref_no_prefixes['draft']) ? $business->ref_no_prefixes['draft'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[draft]', __('sale.draft') . ':') !!}
                {!! Form::text('ref_no_prefixes[draft]', $draft_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $sales_order_prefix = !empty($business->ref_no_prefixes['sales_order']) ? $business->ref_no_prefixes['sales_order'] : '';
                @endphp
                {!! Form::label('ref_no_prefixes[sales_order]', __('lang_v1.sales_order') . ':') !!}
                {!! Form::text('ref_no_prefixes[sales_order]', $sales_order_prefix, ['class' => 'form-control']); !!}
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $Milling = '';
                    if(!empty($business->ref_no_prefixes['Milling'])){
                        $Milling = $business->ref_no_prefixes['Milling'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[Milling]', __('Milling') . ':') !!}
                {!! Form::text('ref_no_prefixes[Milling]', $Milling, ['class' => 'form-control']); !!}
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $Delievery_note = '';
                    if(!empty($business->ref_no_prefixes['Delievery_note'])){
                        $Delievery_note = $business->ref_no_prefixes['Delievery_note'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[Delievery_note]', __('Delievery Note') . ':') !!}
                {!! Form::text('ref_no_prefixes[Delievery_note]', $Delievery_note, ['class' => 'form-control']); !!}
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $sales_invoice = '';
                    if(!empty($business->ref_no_prefixes['sales_invoice'])){
                        $sales_invoice = $business->ref_no_prefixes['sales_invoice'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[Delievery_note]', __('Sales Invoice') . ':') !!}
                {!! Form::text('ref_no_prefixes[sales_invoice]', $sales_invoice, ['class' => 'form-control']); !!}
            </div>
        </div>

        

        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $sale_return_invoice = '';
                    if(!empty($business->ref_no_prefixes['sale_return_invoice'])){
                        $sale_return_invoice = $business->ref_no_prefixes['sale_return_invoice'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[sale_return_invoice]', __('Sales Return Invoice') . ':') !!}
                {!! Form::text('ref_no_prefixes[sale_return_invoice]', $sale_return_invoice, ['class' => 'form-control']); !!}
            </div>
        </div>
        
        
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $production = '';
                    if(!empty($business->ref_no_prefixes['production'])){
                        $production = $business->ref_no_prefixes['production'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[production]', __('Production') . ':') !!}
                {!! Form::text('ref_no_prefixes[production]', $production, ['class' => 'form-control']); !!}
            </div>
        </div>
        
        
        <div class="col-sm-4">
            <div class="form-group">
                @php
                    $multi_production = '';
                    if(!empty($business->ref_no_prefixes['multi_production'])){
                        $multi_production = $business->ref_no_prefixes['multi_production'];
                    }
                @endphp
                {!! Form::label('ref_no_prefixes[multi_production]', __('Multi production') . ':') !!}
                {!! Form::text('ref_no_prefixes[multi_production]', $multi_production, ['class' => 'form-control']); !!}
            </div>
        </div>



    </div>
</div>