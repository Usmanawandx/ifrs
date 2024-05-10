<div class="table-responsive">
    <table class="table table-bordered table-striped ajax_view hide-footer dataTable table-styling table-hover table-primary" id="purchase_return_datatable">
        <thead>
            <tr>
                <th>@lang('messages.action')</th>
                <th>S#</th>
                <th>@lang('messages.date')</th>
                <th>Transaction No</th>
                <th>@lang('lang_v1.parent_purchase')</th>
             
                <th>@lang('purchase.supplier')</th>
                <th>@lang('purchase.payment_status')</th>
                <th>Final Total</th>
                <th>@lang('purchase.location')</th>
                <th>@lang('purchase.payment_due') &nbsp;&nbsp;<i class="fa fa-info-circle text-info" data-toggle="tooltip" data-placement="bottom" data-html="true" data-original-title="{{ __('messages.purchase_due_tooltip')}}" aria-hidden="true"></i></th>
                
            </tr>
        </thead>
        <tfoot>
            <tr class="bg-gray font-17 text-center footer-total">
                <td colspan="6"><strong>@lang('sale.total'):</strong></td>
                <td id="footer_payment_status_count"></td>
                <td><span class="display_currency" id="footer_purchase_return_total" data-currency_symbol ="true"></span></td>
                <td><span class="display_currency" id="footer_total_due" data-currency_symbol ="true"></span></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>