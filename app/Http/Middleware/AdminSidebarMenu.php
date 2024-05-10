<?php

namespace App\Http\Middleware;

use App\Utils\ModuleUtil;
use Closure;
use Menu;

class AdminSidebarMenu
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->ajax()) {
            return $next($request);
        }

        Menu::create('admin-sidebar-menu', function ($menu) {
            $enabled_modules = !empty(session('business.enabled_modules')) ? session('business.enabled_modules') : [];

            $common_settings = !empty(session('business.common_settings')) ? session('business.common_settings') : [];
            $pos_settings = !empty(session('business.pos_settings')) ? json_decode(session('business.pos_settings'), true) : [];

            $is_admin = auth()->user()->hasRole('Admin#' . session('business.id')) ? true : false;
            //Home
            $menu->url(action('HomeController@index'), __('Dashboard'), ['icon' => 'fa fas fa-tachometer-alt', 'active' => request()->segment(1) == 'home'])->order(1);

            // Account and Finance

            // if (auth()->user()->can('account.access') && in_array('account', $enabled_modules)) {
            
                if ($is_admin || auth()->user()->hasAnyPermission(['accounts.view','journal_voucher.view','bank_book.view',
                'payment_voucher.view','receipt_voucher.view','cash_received_voucher.view','cash_payment_voucher.view','account.income_statement']) && in_array('account', $enabled_modules)) {
                $menu->dropdown(
                    __('Accounts & Finance'),
                    function ($sub) use ($is_admin) {
                        
                        if($is_admin || auth()->user()->hasAnyPermission(['accounts.view'])){
                            $sub->url(
                                action('AccountController@index'),
                                __('Chart Of Accounts'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'account']
                            );
                        }
                        if($is_admin || auth()->user()->hasAnyPermission(['account.journal_vouchers'])){
                            $sub->url(
                                action('AccountController@general_voucher_list'),
                                __('Journal Voucher'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'general_voucher_list' || request()->segment(2) == 'general_voucher']
                            );
                        }
                        
                        //  $sub->url(
                        //         action('AccountController@ShowReceipts'),
                        //         __('Receipt Add'),
                        //         ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'receipt_form' || request()->segment(2) == 'general_voucher']
                        //     );
                        
                        
                        
                        
                        if($is_admin || auth()->user()->hasAnyPermission(['bank_book.view'])){
                            $sub->url(
                                action('AccountController@account_book_list'),
                                __('Bank Book'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'account_book_list' || request()->segment(2) == 'account_book']
                            );
                        }
                        if($is_admin || auth()->user()->hasAnyPermission(['account.payment_vouchers'])){
                            $sub->url(
                                action('AccountController@debit_voucher_list'),
                                __('Payment Voucher'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'account' && (request()->input('type') == null) &&  request()->segment(2) == 'debit_voucher_list' || request()->segment(2) == 'debit_voucher' && (request()->input('type') == null)]
                            );
                        }
                        if($is_admin || auth()->user()->hasAnyPermission(['account.receiept_vouchers'])){
                            $sub->url(
                                action('AccountController@credit_voucher_list'), 
                                __('Receipt Voucher'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'account' && (request()->input('type') == null) && request()->segment(2) == 'credit_voucher_list' || request()->segment(2) == 'credit_voucher' && (request()->input('type') == null)]
                            );
                        }
                        if($is_admin || auth()->user()->hasAnyPermission(['cash_received_voucher.view'])){
                            $sub->url(
                                action('AccountController@credit_voucher_list',['type' => 'cash_received_voucher']),
                                __('Cash Recived Voucher'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'account' && request()->input('type') == 'cash_received_voucher']
                            );
                          
                        }
                        if($is_admin || auth()->user()->hasAnyPermission(['account.cash_payment_vouchers'])){
                        $sub->url(
                            action('AccountController@debit_voucher_list',['type' => 'cash_payment_voucher']),
                            __('Cash Payment Voucher'),
                        ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'account' && request()->input('type') == 'cash_payment_voucher']
                        );
                        }
                       
                        
                        
                        if($is_admin || auth()->user()->hasAnyPermission(['account.balance_sheet'])){
                            $sub->url(
                                action('AccountReportsController@balanceSheet'),
                                __('account.balance_sheet'),
                                ['icon' => 'fa fas fa-book', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'balance-sheet']
                            );
                        }
                        // if($is_admin || auth()->user()->hasAnyPermission(['account.trial_balance'])){
                        //     $sub->url(
                        //         action('AccountReportsController@trialBalance'),
                        //         __('account.trial_balance'),
                        //         ['icon' => 'fa fas fa-balance-scale', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'trial-balance']
                        //     );
                        // }
                        if($is_admin || auth()->user()->hasAnyPermission(['account.access'])){
                            $sub->url(
                                action('AccountController@cashFlow'),
                                __('lang_v1.cash_flow'),
                                ['icon' => 'fa fas fa-exchange-alt', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'cash-flow']
                            );
                        }
                        if($is_admin || auth()->user()->hasAnyPermission(['account.access'])){
                            $sub->url(
                                action('AccountReportsController@paymentAccountReport'),
                                __('account.payment_account_report'),
                                ['icon' => 'fa fas fa-file-alt', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'payment-account-report']
                            );
                        }
                    },
                    ['icon' => 'fa fas fa-money-check-alt']
                )->order(2);
            }
            
            // Customer
        

            //Contacts dropdown
            
            if (auth()->user()->can('supplier.view') || auth()->user()->can('customer.view') || auth()->user()->can('supplier.view_own') || auth()->user()->can('customer.view_own')) {
                $menu->dropdown(
                    __('Customer & Supplier'),
                    function ($sub) {
                       
                                            if (auth()->user()->can('customer.view') || auth()->user()->can('customer.view_own')) {
                            $sub->url(
                                action('ContactController@index', ['type' => 'customer']),
                                __('report.customer'),
                                ['icon' => 'fa fas fa-star', 'active' => request()->input('type') == 'customer']
                            );
                        if (auth()->user()->can('group.view') ) {

                            $sub->url(
                                action('CustomerGroupController@index'),
                                __('lang_v1.customer_groups'),
                                ['icon' => 'fa fas fa-users', 'active' => request()->segment(1) == 'customer-group']
                            );
                        }
                        }
                        
                        if (auth()->user()->can('supplier.view')) {
                            $sub->url(
                                action('ContactController@index', ['type' => 'supplier']),
                                __('report.supplier'),
                                ['icon' => 'fa fas fa-star', 'active' => request()->input('type') == 'supplier']
                            );
                        }

                        
                        
                    },
                    ['icon' => 'fa fas fa-address-book', 'id' => "tour_step4"]
                )->order(3);
            }


                // Contractor
                 //Contacts dropdown

        if (auth()->user()->can('contractor.view') || auth()->user()->can('contractor_rate.view') || auth()->user()->can('sale_agent.view') || auth()->user()->can('transporter_rate.view') || auth()->user()->can('vehicle.view')  ) {
                    $menu->dropdown(
                        __('Contrctr / Transporter'),
                        function ($sub) {
                           
                            
                            
                             if (auth()->user()->can('contractor.view')) {
                                $sub->url(
                                    action('ContactController@index', ['type' => 'contracter']),
                                    __('Contracter'),
                                    ['icon' => 'fa fas fa-star', 'active' => request()->input('type') == 'contracter']
                                );
                            }

                            if (auth()->user()->can('contractor_rate.view') ) {
                            $sub->url(
                                action('ContractorController@index'),
                                __('Contractor Rate'),
                                ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'contractors_rate']
                            );
                            }

                            
                         
                            if (auth()->user()->can('transporter_rate.view') ) {
                            $sub->url(
                                action('VehicleRateController@index'),
                                __('Transporter Rate'),
                                ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'vehicle_rate']
                                );
                            }

                            if (auth()->user()->can('vehicle.view')) {
                                $sub->url(
                                    action('VehicleController@index'),
                                    __('Vehicle'),
                                    ['icon' => 'fa fas fa-undo', 'active' => request()->segment(1) == 'vehicle']
                                );
                            }
                            
                            
                            if (auth()->user()->can('sale_agent.view')) {
                                $sub->url(
                                    action('ContactController@index', ['type' => 'Agent']),
                                    __('Sales Agent'),
                                    ['icon' => 'fa fas fa-star', 'active' => request()->input('type') == 'Agent']
                                );
                            }
                            
                       
                        },
                        ['icon' => 'fa fas fa-address-book', 'id' => "tour_step4"]
                    )->order(3);
            }
    
           
            // Products dropdown
            if (
                auth()->user()->can('product.view') ||
                auth()->user()->can('brand.view') ||
                auth()->user()->can('category.view') || 
                auth()->user()->can('unit.view') ||
                auth()->user()->can('pro_type.view') ||
                auth()->user()->can('pro_category.view') ||
                auth()->user()->can('sub_catagory.view') ||
                
                auth()->user()->can('sub_catagory.view') ||
                auth()->user()->can('milling_type.view') ||
                auth()->user()->can('tank.view') ||
                auth()->user()->can('tank.view') ||
                auth()->user()->can('tank_tran.view') ||
                auth()->user()->can('tank_tran.view') 
                ) {
            $menu->dropdown(
               'Inventory',
                function ($sub) {
            if (auth()->user()->can('product.view')) {
                $sub->url(
                    action('ProductController@index'),
                    __('lang_v1.list_products'),
                    ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'products' && request()->segment(2) == '']
                );
            }

            if (auth()->user()->can('brand.view') ) {
                $sub->url(
                    action('BrandController@index'),
                    __('brand.brands'),
                    ['icon' => 'fa fas fa-gem', 'active' => request()->segment(1) == 'brands']
                );
            }


            if (auth()->user()->can('unit.view')) {
                $sub->url(
                    action('UnitController@index'),
                    __('unit.units'),
                    ['icon' => 'fa fas fa-balance-scale', 'active' => request()->segment(1) == 'units']
                );
            }

            if (auth()->user()->can('pro_type.view')) {
            $sub->url(
                action('TypeController@ProductIndex'),
                __('Product Type'),
                ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'products' && request()->segment(2) == 'ProductType']
            );
            }
            
            if(auth()->user()->can('pro_catagory.view'))
            {
            
            $sub->url(
                action('TaxonomyController@ChildIndex') . '?type=product',
                __('product category'),
                ['icon' => 'fa fas fa-tags', 'active' => request()->segment(1) == 'taxonomies' && request()->get('type') == 'child-category']
            );
            }

             if(auth()->user()->can('sub_catagory.view'))
            {
             $sub->url(
                    action('CategoryController@subIndex'),
                    __('Sub Category'),
                    ['icon' => 'fa fas fa-balance-scale', 'active' => request()->segment(1) == 'sub']
                );
            }
            // if (auth()->user()->can('product.create')) {
            //     $sub->url(
            //         action('ProductController@create'),
            //         __('product.add_product'),
            //         ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'products' && request()->segment(2) == 'create']
            //     );
            // }
            
             if(auth()->user()->can('purchase_cat.view'))
            {
            // Rearranged links here
                $sub->url(
                    action('CategoryController@index'),
                    'Purchase Category',
                    ['icon' => 'fa fas fa-balance-scale', 'active' => request()->segment(1) == 'Category'] 
                );
            }
            
            
             if(auth()->user()->can('milling_type.view'))
            {
            $sub->url(
                action('TypeController@typeIndex'),
                'MILLING CATEGORY',
                ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'products' && request()->segment(2) == 'Type']
            );
            }

             if(auth()->user()->can('tank.view'))
            {
            $sub->url(
                action('TankController@index'),
                'Tank',
                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'tank' && request()->segment(2) == '']
            );
            }
            
            
             if(auth()->user()->can('tank_tran.view'))
            {
            $sub->url(
                action('TankTransactionController@index'),
                'TankTransaction',
                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'tank_transaction' && request()->segment(2) == '']
            );
            }
      
            if(auth()->user()->can('import'))
            {
            $sub->url(
                action('ImportProductsController@index'),
                __('product.import_products'),
                ['icon' => 'fa fas fa-download', 'active' => request()->segment(1) == 'import-products']
            );
            }

         
            
            // if (auth()->user()->can('product.opening_stock')) {
            //     $sub->url(
            //         action('ImportOpeningStockController@index'),
            //         __('lang_v1.import_opening_stock'),
            //         ['icon' => 'fa fas fa-download', 'active' => request()->segment(1) == 'import-opening-stock']
            //     );
            // }
            

            
         
        },
        ['icon' => 'fa fas fa-cubes', 'id' => 'tour_step5']
    )->order(6);
}


            
            //Purchase dropdown
        if (auth()->user()->can('purchase.purchase_req.view') || auth()->user()->can('purchase_order.view_all') || auth()->user()->can('purchase.view') || auth()->user()->can('purchase_invoice.view') || auth()->user()->can('purchase.debit_note') || auth()->user()->can('purchase_catagory.view') ) {
            $menu->dropdown(
                    __(' Purchase'),
                    function ($sub) use ($common_settings) {
                        
                         if (auth()->user()->can('purchase.purchase_req.view') ) {
                            $sub->url(
                                action('PurchaseOrderController@index_requision'),
                                __('Purchase Requision'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'Purchase_requision']
                            );
                        }

                        if (auth()->user()->can('purchase_order.view_all')) {
                            $sub->url(
                                action('PurchaseOrderController@index'),
                                __('lang_v1.purchase_order'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'purchase-order']
                            );
                        }

                        
                        if (auth()->user()->can('purchase.view') || auth()->user()->can('view_own_purchase')) {
                            $sub->url(
                                action('PurchaseController@index'),
                                __('Good Received Note'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'purchases' && request()->segment(2) == null]
                            );
                        }
                     
                        
                        if (auth()->user()->can('purchase_invoice.view')) {
                            $sub->url(
                                action('PurchaseOrderController@index_invoice'),
                                __('Purchase Invoice'),
                                [
                                    'icon' => 'fa fas fa-list',
                                    'active' => request()->segment(1) == 'Purchase_invoice' || request()->segment(1) == 'edit_invoice'
                                ]
                            );
                        }
                        if (auth()->user()->can('purchase.debit_note')) {
                            $sub->url(
                                action('PurchaseReturnController@index'),
                                __('Debit Note'),
                                ['icon' => 'fa fas fa-undo', 'active' => request()->segment(1) == 'purchase-return']
                            );
                        }
                        
                        if (auth()->user()->can('purchase_catagory.view')) {
                            $sub->url(
                                action('PurchaseOrderController@Purchase_type'),
                                __('Purchase Type'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'Purchasetype']
                            );
                        }
                      
                    },
                    ['icon' => 'fa fas fa-arrow-circle-down', 'id' => 'tour_step6']
                )->order(4);
            }
            //Sell dropdown
            if ($is_admin || auth()->user()->hasAnyPermission(['so.view_all', 'sale.delivery_note','sale.sale_invoice','sale.sale_return_invoice','sale_type.view','milling.view']) ) {
                $menu->dropdown(
                    __('Sales'),
                    function ($sub) use ($enabled_modules, $is_admin, $pos_settings) {
                        
                        if (auth()->user()->can('so.view_all')) {
                            $sub->url(
                                action('SalesOrderController@index'),
                                __('lang_v1.sales_order'),
                                ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'sales-order']
                            );
                        }
                        
                        if ($is_admin || auth()->user()->can('sale.delivery_note') ) {
                            $sub->url(
                                action('SellController@deliverynoteindex'),
                                __('Delivery Note'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'Delivery_Note' && request()->segment(2) == null]
                            );
                        }
                        if ($is_admin || auth()->user()->hasAnyPermission(['sale.sale_invoice']) ) {
                            $sub->url(
                                action('SellController@saleinvoiceindex'),
                                __('Sale Invoice'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'Sale_Invoice' && request()->segment(2) == null]
                            );
                        }
                        if ($is_admin || auth()->user()->hasAnyPermission(['sale.sale_return_invoice']) ) {
                            $sub->url(
                                action('SellController@salereturnindex'),
                                __('Sale Return Invoice'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'Sale_Return' && request()->segment(2) == null]
                            );
                        }
                       
                    
                        if ($is_admin || auth()->user()->hasAnyPermission(['sale_type.view']) ) {
                            $sub->url(
                                action('SalesOrderController@sale_type'),
                                __('Sale Type'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'SaleType' ]
                            );
                        }

                        if ($is_admin || auth()->user()->hasAnyPermission(['milling.view']) ) {
                            $sub->url(
                                action('SellController@index'),
                                __('Milling'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'sells' && request()->segment(2) == null]
                            );
                        }
                
                    },
                    ['icon' => 'fa fas fa-arrow-circle-up', 'id' => 'tour_step7']
                )->order(5);
            }


            //Stock transfer dropdown
            // if (in_array('stock_transfers', $enabled_modules) && (auth()->user()->can('purchase.view') || auth()->user()->can('purchase.create'))) {
            //     $menu->dropdown(
            //         __('lang_v1.stock_transfers'),
            //         function ($sub) {
                     
            //             if (auth()->user()->can('purchase.create')) {
            //                 $sub->url(
            //                     action('StockTransferController@create'),
            //                     __('lang_v1.add_stock_transfer'),
            //                     ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'stock-transfers' && request()->segment(2) == 'create']
            //                 );
            //             }
            //         },
            //         ['icon' => 'fa fas fa-truck']
            //     )->order(35);
            // }

            //stock adjustment dropdown
            if ((auth()->user()->can('adjustment.view'))) {
                $menu->dropdown(
                'Stock Adjustment',
                    function ($sub) {
                    if (auth()->user()->can('adjustment.view')) {
                        $sub->url(
                            action('\Modules\Manufacturing\Http\Controllers\ProductionController@multiproduction_index', ['type' => 'stock_adjustment']),
                            'Stock Adjustment',
                            ['icon' => 'fa fas fa-list', 'active' => request()->input('type') == 'stock_adjustment']
                        );
                    }
                    },
                    ['icon' => 'fa fas fa-database']
                )->order(40);
            }

          

            //Reports dropdown
            if (auth()->user()->can('trial_balance.view') || auth()->user()->can('audit.view') || auth()->user()->can('valuation.view') || auth()->user()->can('stock.view') || auth()->user()->can('balance_sheet.view') || auth()->user()->can('profit_loss.view') || auth()->user()->can('account_ledger.view')|| auth()->user()->can('ingredient_report.view') || auth()->user()->can('product_wise_return_report.view') || auth()->user()->can('sale_report.view') || auth()->user()->can('purchase_return_report.view') || auth()->user()->can('product_sale_report.view') || auth()->user()->can('sale_return_report.view') || auth()->user()->can('product_purchase_report.view') || auth()->user()->can('product_sale_retur_report.view') || auth()->user()->can('activity_log.view')) {
                $menu->dropdown(
                    __('report.reports'),
                    function ($sub) use ($enabled_modules, $is_admin) {
                        
                        if (auth()->user()->can('trial_balance.view')) {
                            $sub->url(
                                action('AccountReportsController@Transaction_trial_balance'),
                                __('Trial Balance'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'trial-transaction']
                            );
                        }
                        
                        
                        if (auth()->user()->can('sale_report.view')) {
                        $sub->url(
                            action('ReportController@salesSummaryReport'),
                            'Sale Report',
                            ['icon' => 'fa fas fa-file-invoice-dollar', 'active' => request()->segment(2) == 'sales-summary-report']
                        );
                        }
                        
                          if (auth()->user()->can('product_wise_return_report.view')) {
                        $sub->url(
                            action('ReportController@saleProductWise'),
                            'Product Wise Sale Report',
                            ['icon' => 'fa fas fa-file-invoice-dollar', 'active' => request()->segment(2) == 'saleProductWise']
                        );
                        }
                        if (auth()->user()->can('purchase_report.view')) {
                        $sub->url(
                            action('ReportController@purchaseReportDetail'),
                            'Purchase Report',
                            ['icon' => 'fa fas fa-file-invoice-dollar', 'active' => request()->segment(2) == 'purchaseReport']
                        );
                        }
                        
                        if (auth()->user()->can('product_purchase_report.view')) {
                        $sub->url(
                            action('ReportController@purchaseProductReport'),
                            'Product Wise Purchase Report',
                            ['icon' => 'fa fas fa-file-invoice-dollar', 'active' => request()->segment(2) == 'purchaseProductReport']
                        );
                        }
                        
                        if (auth()->user()->can('sale_return_report.view')) {
                        $sub->url(
                            action('ReportController@saleReturnDetail'),
                            'Sale Return Report',
                            ['icon' => 'fa fas fa-file-invoice-dollar', 'active' => request()->segment(2) == 'saleReturnDetail']
                        );
                        }
                        
                      
                        if (auth()->user()->can('product_sale_retur_report.view')) {
                        $sub->url(
                            action('ReportController@saleReturnProductWise'),
                            'Product Wise Sale Return Report',
                            ['icon' => 'fa fas fa-file-invoice-dollar', 'active' => request()->segment(2) == 'saleReturnProductWise']
                        );
                        }
                        
                        if (auth()->user()->can('purchase_return_report.view')) {
                        $sub->url(
                            action('ReportController@purchaseReturnDetail'),
                            'Purchase Return Report',
                            ['icon' => 'fa fas fa-file-invoice-dollar', 'active' => request()->segment(2) == 'purchaseReturnDetail']
                        );
                        }
                         if (auth()->user()->can('product_wise_return_report.view')) {
                        $sub->url(
                            action('ReportController@purchaseReturnProductWise'),
                            'Product Wise Purchase Return Report',
                            ['icon' => 'fa fas fa-file-invoice-dollar', 'active' => request()->segment(2) == 'purchaseReturnProductWise']
                        );
                         }
                        
                        if (auth()->user()->can('audit.view')) {
                        $sub->url(
                            action('AccountController@voucher_listing'),
                            'Audit Trial',
                            ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'voucher-listing']
                        );
                        }
                        
                        if (auth()->user()->can('ingredient_report.view')) {
                        $sub->url(
                            action('ReportController@recipeIngredient'),
                            'Recipt Ingredient Report',
                            ['icon' => 'fa fas fa-file-invoice-dollar', 'active' => request()->segment(2) == 'recipeIngredient']
                        );
                        }
                        
                        if (auth()->user()->can('account_ledger.view')) {
                        $sub->url(
                            action('ReportController@ledgerSearch'),
                            'Accounts Ledger',
                            ['icon' => 'fa fas fa-file-invoice-dollar', 'active' => request()->segment(2) == 'LedgerSearch']
                        );
                        }
                        
                        if (auth()->user()->can('profit_loss.view')) {
                        
                        $sub->url(
                            action('ReportController@profitLoss'),
                            'Profit & Loss',
                            ['icon' => 'fa fas fa-file-invoice-dollar', 'active' => request()->segment(2) == 'profitLoss']
                        );
                        
                        }
                        
                        if (auth()->user()->can('balance_sheet.view')) {
                        $sub->url(
                            action('ReportController@balance_sheet'),
                            'Balance Sheet',
                            ['icon' => 'fa fas fa-file-invoice-dollar', 'active' => request()->segment(2) == 'balance-sheet']
                        );
                        }

                        if (auth()->user()->can('stock.view')) {
                            $sub->url(
                                action('ReportController@getStockReport'),
                                __('report.stock_report'),
                                ['icon' => 'fa fas fa-hourglass-half', 'active' => request()->segment(2) == 'stock-report']
                            );
                        }
                          if (auth()->user()->can('valuation.view')) {
                            $sub->url(
                                action('ReportController@valuationReport'),
                                'Inventory Valuation',
                                ['icon' => 'fa fas fa-hourglass-half', 'active' => request()->segment(2) == 'valuation-report']
                            );
                          }

                        if (auth()->user()->can('activity_log.view')) {
                            $sub->url(
                                action('ReportController@activityLog'),
                                __('lang_v1.activity_log'),
                                ['icon' => 'fa fas fa-user-secret', 'active' => request()->segment(2) == 'activity-log']
                            );
                        }
                    },
                    ['icon' => 'fa fas fa-chart-bar', 'id' => 'tour_step8']
                )->order(89);
            }

   
            //Modules menu
            // if (auth()->user()->can('manage_modules')) {
            //     $menu->url(action('Install\ModulesController@index'), __('lang_v1.modules'), ['icon' => 'fa fas fa-plug', 'active' => request()->segment(1) == 'manage-modules'])->order(60);
            // }


            //Notification template menu
            // if (auth()->user()->can('send_notifications')) {
            //     $menu->url(action('NotificationTemplateController@index'), __('lang_v1.notification_templates'), ['icon' => 'fa fas fa-envelope', 'active' => request()->segment(1) == 'notification-templates'])->order(80);
            // }

            if (auth()->user()->can('user.view') || auth()->user()->can('user.create') || auth()->user()->can('roles.view')) {
                $menu->dropdown(
                    __('user.user_management'),
                    function ($sub) {
                        if (auth()->user()->can('user.view')) {
                            $sub->url(
                                action('ManageUserController@index'),
                                __('user.users'),
                                ['icon' => 'fa fas fa-user', 'active' => request()->segment(1) == 'users']
                            );
                        }
                        if (auth()->user()->can('roles.view')) {
                            $sub->url(
                                action('RoleController@index'),
                                __('user.roles'),
                                ['icon' => 'fa fas fa-briefcase', 'active' => request()->segment(1) == 'roles']
                            );
                        }
                        if (auth()->user()->can('roles.view')) {
                            $sub->url(
                                action('AccountController@assign_coa_list'),
                                'Assign COA',
                                ['icon' => 'fa fas fa-briefcase', 'active' => request()->segment(2) == 'assign_coa_list']
                            );
                        }
                        // if (auth()->user()->can('user.create')) {
                        //     $sub->url(
                        //         action('SalesCommissionAgentController@index'),
                        //         __('lang_v1.sales_commission_agents'),
                        //         ['icon' => 'fa fas fa-handshake', 'active' => request()->segment(1) == 'sales-commission-agents']
                        //     );
                        // }
                    },
                    ['icon' => 'fa fas fa-users']
                )->order(50);
            }
                     //Backup menu
            if (auth()->user()->can('backup')) {
                $menu->url(action('BackUpController@index'), __('lang_v1.backup'), ['icon' => 'fa fas fa-hdd', 'active' => request()->segment(1) == 'backup'])->order(95);
            }


            //Settings Dropdown
            if (auth()->user()->can('business_settings.access') ||
                auth()->user()->can('barcode_settings.access') ||
                auth()->user()->can('invoice_settings.access') ||
                auth()->user()->can('tax_rate.view') ||
                auth()->user()->can('tax_rate.create') ||
                auth()->user()->can('access_package_subscriptions')) {
                $menu->dropdown(
                    __('business.settings'),
                    function ($sub) use ($enabled_modules) {
                        if (auth()->user()->can('business_settings.access')) {
                            $sub->url(
                                action('BusinessController@getBusinessSettings'),
                                __('business.business_settings'),
                                ['icon' => 'fa fas fa-cogs', 'active' => request()->segment(1) == 'business', 'id' => "tour_step2"]
                            );
                            $sub->url(
                                action('BusinessLocationController@index'),
                                __('business.business_locations'),
                                ['icon' => 'fa fas fa-map-marker', 'active' => request()->segment(1) == 'business-location']
                            );
                        }
                        
                        //  if (auth()->user()->can('access_printers')) {
                            $sub->url(
                                action('AccountController@default_account'),
                                'Default Accounts',
                                ['icon' => 'fa fas fa-share-alt', 'active' => request()->segment(1) == 'default_account']
                            );
                        // }
                        
                        if (auth()->user()->can('invoice_settings.access')) {
                            $sub->url(
                                action('InvoiceSchemeController@index'),
                                __('invoice.invoice_settings'),
                                ['icon' => 'fa fas fa-file', 'active' => in_array(request()->segment(1), ['invoice-schemes', 'invoice-layouts'])]
                            );
                        }
                        if (auth()->user()->can('barcode_settings.access')) {
                            $sub->url(
                                action('BarcodeController@index'),
                                __('barcode.barcode_settings'),
                                ['icon' => 'fa fas fa-barcode', 'active' => request()->segment(1) == 'barcodes']
                            );
                        }
                        if (auth()->user()->can('access_printers')) {
                            $sub->url(
                                action('PrinterController@index'),
                                __('printer.receipt_printers'),
                                ['icon' => 'fa fas fa-share-alt', 'active' => request()->segment(1) == 'printers']
                            );
                        }

                        if (auth()->user()->can('tax_rate.view') || auth()->user()->can('tax_rate.create')) {
                            $sub->url(
                                action('TaxRateController@index'),
                                __('tax_rate.tax_rates'),
                                ['icon' => 'fa fas fa-bolt', 'active' => request()->segment(1) == 'tax-rates']
                            );
                        }

                        if (in_array('tables', $enabled_modules) && auth()->user()->can('access_tables')) {
                            $sub->url(
                                action('Restaurant\TableController@index'),
                                __('restaurant.tables'),
                                ['icon' => 'fa fas fa-table', 'active' => request()->segment(1) == 'modules' && request()->segment(2) == 'tables']
                            );
                        }

                        if (in_array('modifiers', $enabled_modules) && (auth()->user()->can('product.view') || auth()->user()->can('product.create'))) {
                            $sub->url(
                                action('Restaurant\ModifierSetsController@index'),
                                __('restaurant.modifiers'),
                                ['icon' => 'fa fas fa-pizza-slice', 'active' => request()->segment(1) == 'modules' && request()->segment(2) == 'modifiers']
                            );
                        }

                        if (in_array('types_of_service', $enabled_modules) && auth()->user()->can('access_types_of_service')) {
                            $sub->url(
                                action('TypesOfServiceController@index'),
                                __('lang_v1.types_of_service'),
                                ['icon' => 'fa fas fa-user-circle', 'active' => request()->segment(1) == 'types-of-service']
                            );
                        }
                        
                            $sub->url(
                                action('PurchaseOrderController@Term_conditions'),
                                __('Term conditions'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'Term_Conditions']
                            );

                            $sub->url(
                                action('CountryController@index'),
                                __('Country/City/State'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'insert_data']
                            );

                            $sub->url(
                                action('CountryController@combineIndex'),
                                __('Manages Country'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'combine_data']
                            );


                        
                    },
                    ['icon' => 'fa fas fa-cog', 'id' => 'tour_step3']
                )->order(91);
            }
        });
        
        //Add menus from modules
        $moduleUtil = new ModuleUtil;
        $moduleUtil->getModuleData('modifyAdminMenu');

        return $next($request);
    }
}