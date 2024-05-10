<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/cleareverything', function () {
    $clearcache = Artisan::call('cache:clear');
    echo "Cache cleared<br>";

    $clearview = Artisan::call('view:clear');
    echo "View cleared<br>";

    $clearconfig = Artisan::call('config:cache');
    echo "Config cleared<br>";

});
include_once('install_r.php');

Route::middleware(['setData'])->group(function () {
    Route::get('/', function () {
        $locations = DB::table('business_locations')->whereNull('deleted_at')->get();
        return view('auth/login')->with(compact('locations'));
    });

    // Route::get('get_bussiness_locations_id', 'BusinessLocationController@get_bussiness_locations_id');
    Route::get('get_bussiness_locations_id', function () {
        return session()->get('bussiness_location');
    });

    Route::get('/transaction_nums/{prefix}', function ($prefix) {
        $trans_no = DB::table('transactions')
        ->where('ref_no', 'not like', "%-ovr%")
        ->where('ref_no', 'like', '%'. $prefix .'%')
        ->select('ref_no', DB::raw("substring_index(substring_index(ref_no,'-',-1),',',-1) as max_no"))->get()
        ->max('max_no');
        if(empty($trans_no)){
            $trans_no_ = 1;
        }else{
            $break_no = explode("-",$trans_no);
            $trans_no_ = end($break_no)+1;
        }
        return ['full_data' => $trans_no ,'no' => $trans_no_];
    });

    Route::get('/product_sku/{prefix}', function ($prefix) {
        $trans_no = DB::table('products')
            ->where('sku', 'like', '%' . $prefix . '%')
            ->select('sku', DB::raw("substring_index(substring_index(sku,'-',-1),',',-1) as max_no"))->get()
            ->max('max_no');

        if (empty($trans_no)) {
            $trans_no_ = 1;
        } else {
            $break_no = explode("-", $trans_no);
            $trans_no_ = end($break_no) + 1;
        }
        $trans_no_ = str_pad($trans_no_, 4, '0', STR_PAD_LEFT);
        return ['full_data' => $trans_no ,'no' => $trans_no_];
    });
    

    Auth::routes();

    Route::get('/business/register', 'BusinessController@getRegister')->name('business.getRegister');
    Route::post('/business/register', 'BusinessController@postRegister')->name('business.postRegister');
    Route::post('/business/register/check-username', 'BusinessController@postCheckUsername')->name('business.postCheckUsername');
    Route::post('/business/register/check-email', 'BusinessController@postCheckEmail')->name('business.postCheckEmail');

    Route::get('/invoice/{token}', 'SellPosController@showInvoice')
        ->name('show_invoice');
    Route::get('/quote/{token}', 'SellPosController@showInvoice')
        ->name('show_quote');

    Route::get('/pay/{token}', 'SellPosController@invoicePayment')
        ->name('invoice_payment');
    Route::post('/confirm-payment/{id}', 'SellPosController@confirmPayment')
        ->name('confirm_payment');
});

//Routes for authenticated users only
Route::middleware(['setData', 'auth', 'SetSessionData', 'language', 'timezone', 'AdminSidebarMenu', 'CheckUserLogin'])->group(function () {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/home/get-totals', 'HomeController@getTotals');
    Route::get('/home/product-stock-alert', 'HomeController@getProductStockAlert');
    Route::get('/home/purchase-payment-dues', 'HomeController@getPurchasePaymentDues');
    Route::get('/home/sales-payment-dues', 'HomeController@getSalesPaymentDues');
    Route::post('/attach-medias-to-model', 'HomeController@attachMediasToGivenModel')->name('attach.medias.to.model');
    Route::get('/calendar', 'HomeController@getCalendar')->name('calendar');
    
    Route::post('/test-email', 'BusinessController@testEmailConfiguration');
    Route::post('/test-sms', 'BusinessController@testSmsConfiguration');
    Route::get('/business/settings', 'BusinessController@getBusinessSettings')->name('business.getBusinessSettings');
    Route::post('/business/update', 'BusinessController@postBusinessSettings')->name('business.postBusinessSettings');
    Route::get('/user/profile', 'UserController@getProfile')->name('user.getProfile');
    Route::post('/user/update', 'UserController@updateProfile')->name('user.updateProfile');
    Route::post('/user/update-password', 'UserController@updatePassword')->name('user.updatePassword');

    Route::resource('brands', 'BrandController');
    
    Route::resource('payment-account', 'PaymentAccountController');
    
    Route::get('invoice/fetch/{id}','AccountController@get_sale_invoices');
    Route::post('Aging','AccountController@Aging_add');

    Route::resource('tax-rates', 'TaxRateController');

    Route::resource('units', 'UnitController');

    Route::get('/contacts/payments/{contact_id}', 'ContactController@getContactPayments');
    Route::get('/contacts/map', 'ContactController@contactMap');
    Route::get('/contacts/update-status/{id}', 'ContactController@updateStatus');
    Route::get('/contacts/stock-report/{supplier_id}', 'ContactController@getSupplierStockReport');
    Route::get('/contacts/ledger', 'ContactController@getLedger');
    Route::post('/contacts/send-ledger', 'ContactController@sendLedger');
    Route::get('/contacts/import', 'ContactController@getImportContacts')->name('contacts.import');
    Route::post('/contacts/import', 'ContactController@postImportContacts');
    Route::post('/contacts/check-contacts-id', 'ContactController@checkContactId');
    Route::get('/contacts/customers', 'ContactController@getCustomers');
    Route::resource('contacts', 'ContactController');

    Route::get('taxonomies-ajax-index-page', 'TaxonomyController@getTaxonomyIndexPage');
    Route::resource('taxonomies', 'TaxonomyController');

    Route::resource('variation-templates', 'VariationTemplateController');

    Route::get('/products/stock-history/{id}', 'ProductController@productStockHistory');
    Route::get('/delete-media/{media_id}', 'ProductController@deleteMedia');
    Route::post('/products/mass-deactivate', 'ProductController@massDeactivate');
    Route::get('/products/activate/{id}', 'ProductController@activate');
    Route::get('/products/view-product-group-price/{id}', 'ProductController@viewGroupPrice');
    Route::get('/products/add-selling-prices/{id}', 'ProductController@addSellingPrices');
    Route::post('/products/save-selling-prices', 'ProductController@saveSellingPrices');
    Route::post('/products/mass-delete', 'ProductController@massDestroy');
    Route::get('/products/view/{id}', 'ProductController@view');
    Route::get('/products/list', 'ProductController@getProducts');
    Route::get('/products/list-no-variation', 'ProductController@getProductsWithoutVariations');
    Route::post('/products/bulk-edit', 'ProductController@bulkEdit');
    Route::post('/products/bulk-update', 'ProductController@bulkUpdate');
    Route::post('/products/bulk-update-location', 'ProductController@updateProductLocation');
    Route::get('/products/get-product-to-edit/{product_id}', 'ProductController@getProductToEdit');
    
    Route::post('/products/get_sub_categories', 'ProductController@getSubCategories');
    Route::get('/products/get_sub_units', 'ProductController@getSubUnits');
    Route::post('/products/product_form_part', 'ProductController@getProductVariationFormPart');
    Route::post('/products/get_product_variation_row', 'ProductController@getProductVariationRow');
    Route::post('/products/get_variation_template', 'ProductController@getVariationTemplate');
    Route::get('/products/get_variation_value_row', 'ProductController@getVariationValueRow');
    Route::post('/products/check_product_sku', 'ProductController@checkProductSku');
    Route::get('/products/quick_add', 'ProductController@quickAdd');
    Route::post('/products/save_quick_product', 'ProductController@saveQuickProduct');
    Route::get('/products/get-combo-product-entry-row', 'ProductController@getComboProductEntryRow');
    Route::post('/products/toggle-woocommerce-sync', 'ProductController@toggleWooCommerceSync');
    
    Route::resource('products', 'ProductController');
    
    Route::get('/products/get_product_category/{id}', 'ProductController@get_product_category');
    Route::get('/products/get_product_subcategory/{id}', 'ProductController@get_product_subcategory');
    
// Type Routes
    Route::get('Type', 'TypeController@typeIndex')->name('type');
    Route::post('type-add', 'TypeController@store')->name('type.add');
    Route::get('/type_edit/{id}/edit', 'TypeController@edit');
    Route::post('/type_update/{id}', 'TypeController@update');
    Route::get('/delete_type/{id}', 'TypeController@delete')->name('Type.delete');

    Route::get('ProductType', 'TypeController@ProductIndex')->name('ProductIndex');
    
// End Type Routes

    Route::get('/addlessCharges/{id}', 'SellController@addless');


    Route::get('/default_account', 'AccountController@default_account');
    Route::post('/default_account', 'AccountController@default_acc_store');
    Route::post('/invoice', 'AccountController@defaultInvoice');
    
    
    Route::get('update_avg_price', 'ProductController@update_avg_price');

// Transporter Routes Start
Route::get('transporter', 'TransporterController@index')->name('transporter.index');
Route::post('transporter_add', 'TransporterController@store')->name('transporter.add');
Route::get('/transporter_edit/{id}/edit', 'TransporterController@edit')->name('transporter.add');
Route::post('/transporter_update/{id}', 'TransporterController@update')->name('transporter.add');
Route::get('/transporter_delete/{id}', 'TransporterController@delete')->name('transporter.delete');
// Transporter Routes End

// vehicle Routes Start
Route::get('vehicle', 'VehicleController@index')->name('vehicle.index');
Route::post('vehicle_add', 'VehicleController@store')->name('vehicle.add');
Route::get('/vehicle_edit/{id}/edit', 'VehicleController@edit')->name('vehicle.add');
Route::post('/vehicle_update/{id}', 'VehicleController@update')->name('vehicle.add');
Route::get('/vehicle_delete/{id}', 'VehicleController@delete')->name('vehicle.delete');
// vehicle Routes End


Route::get('get_Account_codes/{id}', 'AccountController@get_code')->name('get.code');

Route::get('get_transporter/{id}', 'TransporterController@get_transporter')->name('get.transporter');



Route::post('/manage_user/checkUserTransactionAccount', 'ManageUserController@checkUserTransactionAccount');


// Child Category

    Route::get('child-category', 'TaxonomyController@ChildIndex')->name('child.index');
    Route::post('child-add', 'TaxonomyController@cat_store')->name('child.add');
       Route::get('/child_edit/{id}/edit', 'TaxonomyController@edit_child');
    Route::post('/child_update/{id}', 'TaxonomyController@update_child');

    Route::get('/type_edit/{id}/edit', 'TypeController@edit');
    Route::post('/type_update/{id}', 'TypeController@update');
    Route::get('/delete/{id}', 'TypeController@delete')->name('Type.delete');



    // Route::get('/delete/{id}', 'TaxonomyController@delete')->name('child.delete');



// 

       Route::get('/child_delete/{id}', 'TaxonomyController@delete')->name('child.delete');



// 

    Route::get('get_child_cat/{id}', 'TaxonomyController@get_child')->name('child.index');
    Route::get('get_sub/{id}', 'TaxonomyController@get_sub')->name('sub.index');

    ///////////////////////////////////////////////////////////////////////////
    Route::get('/acc_contact', 'ContactController@acc_contact_list');
    Route::get('/acc_contact_update/{id1}/{id2}', 'ContactController@acc_contact_update');

// 

// Quick add


    Route::post('add-category', 'TaxonomyController@add_category')->name('cat.add');
    Route::post('add-subcategory', 'TaxonomyController@add_subcategory')->name('sub.add');
    Route::get('add-child', 'TaxonomyController@add_child')->name('child.add');


// 
    Route::post('prod_type/{id}', 'TypeController@product_type_f')->name('prod.fetch');
// 


    Route::post('/purchases/update-status', 'PurchaseController@updateStatus');
    Route::get('/purchases/get_products', 'PurchaseController@getProducts');
    Route::get('/purchases/getProductss', 'PurchaseController@getProductss');

    
    Route::get('/purchases/get_suppliers', 'PurchaseController@getSuppliers');
    Route::post('/purchases/get_purchase_entry_row', 'PurchaseController@getPurchaseEntryRow');
    Route::post('/purchases/get_purchase_entry_row_req', 'PurchaseController@getPurchaseEntryRowReq');

    


    Route::post('/purchases/check_ref_number', 'PurchaseController@checkRefNumber');
    Route::resource('purchases', 'PurchaseController')->except(['show']);

    Route::get('/toggle-subscription/{id}', 'SellPosController@toggleRecurringInvoices');
    Route::post('/sells/pos/get-types-of-service-details', 'SellPosController@getTypesOfServiceDetails');
    Route::get('Delivery_Note', 'SellController@deliverynoteindex');
    Route::get('Sale_Invoice', 'SellController@saleinvoiceindex');
    Route::get('SaleType', 'SalesOrderController@sale_type');
    Route::Post('/sale_store', 'SalesOrderController@sale_store');
    
    Route::get('SaleTypePartial','SalesOrderController@sale_type_partial');
    Route::Post('/sale_store_partial', 'SalesOrderController@sale_store_partial');

    
    Route::get('/sale_type_update/{id}/edit', 'SalesOrderController@update');
    Route::post('/sale_type_edit/{id}', 'SalesOrderController@edit');
    Route::get('/sale_type/{id}', 'SalesOrderController@delete_type');
    Route::get('/customer/get_ntncnic', 'SellController@getNtnCnic');



    Route::post('/saleinvoicestore', 'SellPosController@saleinvoicestore');


    Route::get('Sale_Return', 'SellController@salereturnindex');
    Route::get('/sells/subscriptions', 'SellPosController@listSubscriptions');
    Route::get('/sells/duplicate/{id}', 'SellController@duplicateSell');
    Route::get('/milling/create', 'SellController@Millingcreate');
    Route::get('/milling/{id}/edit', 'SellController@Millingedit');
    Route::get('/delivery_note/create', 'SellController@deliverynotecreate');
    Route::get('/delivery_note/{id}/edit', 'SellController@deliverynoteedit')->name('delivery_note.edit');
    Route::get('/sale_invoice/create', 'SellController@saleinvoicecreate');
    Route::get('/sale_invoice/{id}/edit', 'SellController@saleinvoiceedit')->name('sale_invoice.edit');
    Route::get('/sale_return_invoice/create', 'SellController@salereturncreate');
    Route::get('/sale_return_invoice/{id}/edit', 'SellController@salereturnedit');
    Route::get('/sells/drafts', 'SellController@getDrafts');
    Route::get('/sells/convert-to-draft/{id}', 'SellPosController@convertToInvoice');
    Route::get('/sells/convert-to-proforma/{id}', 'SellPosController@convertToProforma');
    Route::get('/sells/quotations', 'SellController@getQuotations');
    Route::get('/sells/draft-dt', 'SellController@getDraftDatables');
    Route::resource('sells', 'SellController')->except(['show']);
    Route::get('sale_activity','SaleActivityController@index')->name('sale_activity');
    Route::post('sale_activity/add','SaleActivityController@store')->name('sale_activity.add');
    Route::get('getDoctors','SaleActivityController@getDoctors')->name('getDoctors');
    Route::get('getCities','SaleActivityController@getDoctors')->name('getCities');
    Route::get('/import-sales', 'ImportSalesController@index');
    Route::post('/import-sales/preview', 'ImportSalesController@preview');
    Route::post('/import-sales', 'ImportSalesController@import');
    Route::get('/revert-sale-import/{batch}', 'ImportSalesController@revertSaleImport');

    Route::get('/sells/pos/get_product_row/{variation_id}/{location_id}', 'SellPosController@getProductRow');
    Route::post('/sells/pos/get_payment_row', 'SellPosController@getPaymentRow');
    Route::post('/sells/pos/get-reward-details', 'SellPosController@getRewardDetails');
    Route::get('/sells/pos/get-recent-transactions', 'SellPosController@getRecentTransactions');
    Route::get('/sells/pos/get-product-suggestion', 'SellPosController@getProductSuggestion');
    Route::get('/sells/pos/get-featured-products/{location_id}', 'SellPosController@getFeaturedProducts');
    Route::resource('pos', 'SellPosController');

    Route::resource('roles', 'RoleController');

    Route::resource('users', 'ManageUserController');

    Route::resource('group-taxes', 'GroupTaxController');

    Route::get('/barcodes/set_default/{id}', 'BarcodeController@setDefault');
    Route::resource('barcodes', 'BarcodeController');

    //Invoice schemes..
    Route::get('/invoice-schemes/set_default/{id}', 'InvoiceSchemeController@setDefault');
    Route::resource('invoice-schemes', 'InvoiceSchemeController');

    //Print Labels
    Route::get('/labels/show', 'LabelsController@show');
    Route::get('/labels/add-product-row', 'LabelsController@addProductRow');
    Route::get('/labels/preview', 'LabelsController@preview');
    
    
    
    // Contractor
    
    Route::get('contractors_rate', 'ContractorController@index');
    Route::get('contractor_rate/create','ContractorController@create');
    Route::post('Contractor_rate/add','ContractorController@store')->name('Contractor_rate.add');
    Route::get('/contractor_rate/edit/{id}', 'ContractorController@edit');
    Route::post('/contractor_rate_update/{id}', 'ContractorController@update');
    Route::get('/contractor_rate/show/{id}', 'ContractorController@show');
    Route::get('/contractor_rate_delete/{id}', 'ContractorController@delete');


    // VehicleRateController
    
    Route::get('vehicle_rate', 'VehicleRateController@index');
    Route::get('vehicle_rate/create','VehicleRateController@create');
    Route::post('vehicle_rate/add','VehicleRateController@store')->name('Vehicle_rate.add');
    Route::get('/vehicle_rate/edit/{id}', 'VehicleRateController@edit');
    Route::post('/vehicle_rate_update/{id}', 'VehicleRateController@update');
    Route::get('/vehicle_rate/show/{id}', 'VehicleRateController@show');
    Route::get('/vehicle_rate_delete/{id}', 'VehicleRateController@delete');
    
    //tank
    Route::get('tank', 'TankController@index')->name('tank.index');
    Route::post('tank/create', 'TankController@store');
    Route::get('tank/edit/{id}', 'TankController@edit');
    Route::post('tank/update', 'TankController@update');
    Route::get('tank/delete/{id}', 'TankController@destroy');
    
    // tank transaction
    Route::get('tank_transaction', 'TankTransactionController@index')->name('tank_transaction.index');
    Route::get('tank_transaction/create', 'TankTransactionController@create');
    Route::post('tank_transaction/create', 'TankTransactionController@store');
    Route::get('tank_transaction/show', 'TankTransactionController@show');
    Route::get('tank_transaction/edit/{id}', 'TankTransactionController@edit');
    Route::post('tank_transaction/update', 'TankTransactionController@update');
    Route::get('tank_transaction/delete/{id}', 'TankTransactionController@destroy');
    
    
    
    
    Route::post('/importstock', 'ReportController@importstock');
    

    //Reports...
    Route::get('/reports/get-stock-by-sell-price', 'ReportController@getStockBySellingPrice');
    Route::get('/reports/purchase-report', 'ReportController@purchaseReport');
    Route::get('/reports/sale-report', 'ReportController@saleReport');
    Route::get('/reports/service-staff-report', 'ReportController@getServiceStaffReport');
    Route::get('/reports/service-staff-line-orders', 'ReportController@serviceStaffLineOrders');
    Route::get('/reports/table-report', 'ReportController@getTableReport');
    Route::get('/reports/profit-loss', 'ReportController@getProfitLoss');
    Route::get('/reports/balance-sheet', 'ReportController@balance_sheet');
    Route::get('/reports/get-opening-stock', 'ReportController@getOpeningStock');
    Route::get('/reports/purchase-sell', 'ReportController@getPurchaseSell');
    Route::get('/reports/customer-supplier', 'ReportController@getCustomerSuppliers');
    Route::get('/reports/stock-report', 'ReportController@getStockReport');
    Route::get('/reports/valuation-report', 'ReportController@valuationReport');
    Route::get('/reports/valuation-history/{id}', 'ReportController@valuationHistory');
    Route::get('/reports/getValuationByDate', 'ReportController@getValuationByDate');
    Route::get('/reports/stock-details', 'ReportController@getStockDetails');
    Route::get('/reports/tax-report', 'ReportController@getTaxReport');
    Route::get('/reports/tax-details', 'ReportController@getTaxDetails');
    Route::get('/reports/trending-products', 'ReportController@getTrendingProducts');
    Route::get('/reports/expense-report', 'ReportController@getExpenseReport');
    Route::get('/reports/stock-adjustment-report', 'ReportController@getStockAdjustmentReport');
    Route::get('/reports/register-report', 'ReportController@getRegisterReport');
    Route::get('/reports/sales-representative-report', 'ReportController@getSalesRepresentativeReport');
    Route::get('/reports/sales-representative-total-expense', 'ReportController@getSalesRepresentativeTotalExpense');
    Route::get('/reports/sales-representative-total-sell', 'ReportController@getSalesRepresentativeTotalSell');
    Route::get('/reports/sales-representative-total-commission', 'ReportController@getSalesRepresentativeTotalCommission');
    Route::get('/reports/stock-expiry', 'ReportController@getStockExpiryReport');
    Route::get('/reports/stock-expiry-edit-modal/{purchase_line_id}', 'ReportController@getStockExpiryReportEditModal');
    Route::post('/reports/stock-expiry-update', 'ReportController@updateStockExpiryReport')->name('updateStockExpiryReport');
    Route::get('/reports/customer-group', 'ReportController@getCustomerGroup');
    Route::get('/reports/product-purchase-report', 'ReportController@getproductPurchaseReport');
    Route::get('/reports/product-sell-grouped-by', 'ReportController@productSellReportBy');
    Route::get('/reports/product-sell-report', 'ReportController@getproductSellReport');
    Route::get('/reports/product-sell-report-with-purchase', 'ReportController@getproductSellReportWithPurchase');
    Route::get('/reports/product-sell-grouped-report', 'ReportController@getproductSellGroupedReport');
    Route::get('/reports/lot-report', 'ReportController@getLotReport');
    Route::get('/reports/purchase-payment-report', 'ReportController@purchasePaymentReport');
    Route::get('/reports/sell-payment-report', 'ReportController@sellPaymentReport');
    Route::get('/reports/product-stock-details', 'ReportController@productStockDetails');
    Route::get('/reports/adjust-product-stock', 'ReportController@adjustProductStock');
    Route::get('/reports/get-profit/{by?}', 'ReportController@getProfit');
    Route::get('/reports/items-report', 'ReportController@itemsReport');
    Route::get('/reports/get-stock-value', 'ReportController@getStockValue');
    Route::get('/reports/aging-report', 'ReportController@Aging');
    
    
    Route::get('/reports/sales-summary-report', 'ReportController@salesSummaryReport');
    Route::get('/reports/saleProductWise', 'ReportController@saleProductWise');
    Route::get('/reports/saleReturnDetail', 'ReportController@saleReturnDetail');
    Route::get('/reports/saleReturnProductWise', 'ReportController@saleReturnProductWise');
    Route::get('/reports/purchaseReturnDetail', 'ReportController@purchaseReturnDetail');
    Route::get('/reports/purchaseReturnProductWise', 'ReportController@purchaseReturnProductWise');
    Route::get('/reports/recipeIngredient', 'ReportController@recipeIngredient');
    
    Route::get('/reports/purchaseReport', 'ReportController@purchaseReportDetail');
    Route::get('/reports/purchaseProductReport', 'ReportController@purchaseProductReport');
    Route::get('/reports/ledgerSearch', 'ReportController@ledgerSearch');
    Route::get('/reports/profitLoss', 'ReportController@profitLoss');
    Route::get('business-location/activate-deactivate/{location_id}', 'BusinessLocationController@activateDeactivateLocation');

    //Business Location Settings...
    Route::prefix('business-location/{location_id}')->name('location.')->group(function () {
        Route::get('settings', 'LocationSettingsController@index')->name('settings');
        Route::post('settings', 'LocationSettingsController@updateSettings')->name('settings_update');
    });

    //Business Locations...
    Route::post('business-location/check-location-id', 'BusinessLocationController@checkLocationId');
    Route::resource('business-location', 'BusinessLocationController');

    //Invoice layouts..
    Route::resource('invoice-layouts', 'InvoiceLayoutController');

    //Expense Categories...
    Route::resource('expense-categories', 'ExpenseCategoryController');

    //Expenses...
    Route::resource('expenses', 'ExpenseController');
    
    
    
    
    // Categories Work
    Route::get('Category', 'CategoryController@index')->name('Category_list');
    Route::post('store-category', 'CategoryController@store')->name('category.add');
    Route::get('/cat_edit/{id}/edit', 'CategoryController@edit');
    Route::post('/Cat_update/{id}', 'CategoryController@update');
    Route::get('/delete_cat/{id}', 'CategoryController@delete')->name('category.delete');

    Route::get('sub', 'CategoryController@subIndex')->name('sub.list');
    Route::post('store-sub', 'CategoryController@store_sub')->name('sub.add');
    Route::get('/sub_edit/{id}/edit', 'CategoryController@edit_sub');
    Route::post('/sub_update/{id}', 'CategoryController@update_sub');
    Route::get('/delete_sub/{id}', 'CategoryController@delete_sub')->name('sub.delete');





    
    
    

    //Transaction payments...
    // Route::get('/payments/opening-balance/{contact_id}', 'TransactionPaymentController@getOpeningBalancePayments');
    Route::get('/payments/show-child-payments/{payment_id}', 'TransactionPaymentController@showChildPayments');
    Route::get('/payments/view-payment/{payment_id}', 'TransactionPaymentController@viewPayment');
    Route::get('/payments/add_payment/{transaction_id}', 'TransactionPaymentController@addPayment');
    Route::get('/payments/pay-contact-due/{contact_id}', 'TransactionPaymentController@getPayContactDue');
    Route::post('/payments/pay-contact-due', 'TransactionPaymentController@postPayContactDue');
    Route::resource('payments', 'TransactionPaymentController');

    //Printers...
    Route::resource('printers', 'PrinterController');

    Route::get('/stock-adjustments/remove-expired-stock/{purchase_line_id}', 'StockAdjustmentController@removeExpiredStock');
    Route::post('/stock-adjustments/get_product_row', 'StockAdjustmentController@getProductRow');
    Route::resource('stock-adjustments', 'StockAdjustmentController');

    Route::get('/cash-register/register-details', 'CashRegisterController@getRegisterDetails');
    Route::get('/cash-register/close-register/{id?}', 'CashRegisterController@getCloseRegister');
    Route::post('/cash-register/close-register', 'CashRegisterController@postCloseRegister');
    Route::resource('cash-register', 'CashRegisterController');

    //Import products
    Route::get('/import-products', 'ImportProductsController@index');
    Route::post('/import-products/store', 'ImportProductsController@store');

    //Sales Commission Agent
    Route::resource('sales-commission-agents', 'SalesCommissionAgentController');

    //Stock Transfer
    Route::get('stock-transfers/print/{id}', 'StockTransferController@printInvoice');
    Route::post('stock-transfers/update-status/{id}', 'StockTransferController@updateStatus');
    Route::resource('stock-transfers', 'StockTransferController');
    
    Route::get('/opening-stock/add/{product_id}', 'OpeningStockController@add');
    Route::post('/opening-stock/save', 'OpeningStockController@save');
    
    
    Route::post('/opening-stock/addUpdateOpeningStock', 'OpeningStockController@addUpdateOpeningStock');

    //Customer Groups
    Route::resource('customer-group', 'CustomerGroupController');

    //Import opening stock
    Route::get('/import-opening-stock', 'ImportOpeningStockController@index');
    Route::post('/import-opening-stock/store', 'ImportOpeningStockController@store');

    //Sell return
    Route::resource('sell-return', 'SellReturnController');
    Route::get('sell-return/get-product-row', 'SellReturnController@getProductRow');
    Route::get('/sell-return/print/{id}', 'SellReturnController@printInvoice');
    Route::get('/sell-return/add/{id}', 'SellReturnController@add');
    
    //Backup
    Route::get('backup/download/{file_name}', 'BackUpController@download');
    Route::get('backup/delete/{file_name}', 'BackUpController@delete');
    Route::resource('backup', 'BackUpController', ['only' => [
        'index', 'create', 'store'
    ]]);

    Route::get('selling-price-group/activate-deactivate/{id}', 'SellingPriceGroupController@activateDeactivate');
    Route::get('export-selling-price-group', 'SellingPriceGroupController@export');
    Route::post('import-selling-price-group', 'SellingPriceGroupController@import');

    Route::resource('selling-price-group', 'SellingPriceGroupController');

    Route::resource('notification-templates', 'NotificationTemplateController')->only(['index', 'store']);
    Route::get('notification/get-template/{transaction_id}/{template_for}', 'NotificationController@getTemplate');
    Route::post('notification/send', 'NotificationController@send');

    Route::post('/purchase-return/update', 'CombinedPurchaseReturnController@update');
    Route::get('/purchase-return/edit/{id}', 'CombinedPurchaseReturnController@edit');
    Route::post('/purchase-return/save', 'CombinedPurchaseReturnController@save');
    Route::post('/purchase-return/get_product_row', 'CombinedPurchaseReturnController@getProductRow');
    Route::get('/purchase-return/create', 'CombinedPurchaseReturnController@create');
    Route::get('/purchase-return/add/{id}', 'PurchaseReturnController@add');
    Route::resource('/purchase-return', 'PurchaseReturnController', ['except' => ['create']]);

    Route::get('/discount/activate/{id}', 'DiscountController@activate');
    Route::post('/discount/mass-deactivate', 'DiscountController@massDeactivate');
    Route::resource('discount', 'DiscountController');

    Route::group(['prefix' => 'account'], function () {
        Route::resource('/account', 'AccountController');
        
        
        Route::get('/detail_show/{id}', 'AccountController@detail_show');
        Route::get('/account_book_partial/{id}', 'AccountController@account_book_partial');
        Route::get('/receipt_form', 'AccountController@invoice_check');
        Route::get('/all_receipts', 'AccountController@ShowReceipts');
        
        
        
        Route::get('/fund-transfer/{id}', 'AccountController@getFundTransfer');
        Route::post('/fund-transfer', 'AccountController@postFundTransfer');
        Route::get('/deposit/{id}', 'AccountController@getDeposit');
        Route::post('/deposit', 'AccountController@postDeposit');
        Route::get('/close/{id}', 'AccountController@close');
        
        
        Route::get('/closeAll/{any}', 'AccountController@closeAll');
        Route::get('/ActiveAll/{any}', 'AccountController@ActiveAll');
        Route::post('/del_control_acc', 'AccountTypeController@del_control_acc');
        
        //Route::get('/account_number/{any}', 'AccountController@account_number');
        //Route::get('/transaction_account_number/{any}', 'AccountController@transaction_account_number');
        Route::get('/account_number/{any}', 'AccountController@get_newCode');
        Route::get('/transaction_account_number/{any}', 'AccountController@transaction_account_number');

        //Assign Chart of Account
        Route::get('/assign_coa', 'AccountController@assign_coa');
        Route::post('/get_transaction_acc', 'AccountController@get_transaction_acc');
        Route::post('/assign_coa_store', 'AccountController@assign_coa_store');
        Route::get('/assign_coa_list', 'AccountController@assign_coa_list');
        Route::get('/assign_coa_edit/{id}', 'AccountController@assign_coa_edit');
        Route::post('/assign_coa_update', 'AccountController@assign_coa_update');
        Route::get('/assign_coa_delete/{id}', 'AccountController@assign_coa_delete');
        
        // Account Book voucher
        Route::get('/account_book_list', 'AccountController@account_book_list');
        Route::get('/account_book', 'AccountController@account_book');
        Route::post('/account_book', 'AccountController@account_book_create');
        Route::get('/edit_bank_book/{id}', 'AccountController@edit_bank_book')->name('edit_bank_book');
        Route::post('account_book_update','AccountController@account_book_update'); 
        Route::get('/account_book_delete/{reff_no}', 'AccountController@account_book_delete')->name('account_book_delete');
        
        Route::get('/invoice-prt-voucher/{id}','AccountController@print_voucher');
        
        // GENERAL VOUCHER || jv
        Route::get('/general_voucher_list', 'AccountController@general_voucher_list');
        Route::get('/general_voucher', 'AccountController@general_voucher');
        Route::post('/general_voucher', 'AccountController@general_voucher_create');
        Route::get('/jv_edit/{reff_no}', 'AccountController@jv_edit');
        Route::post('/jv_update', 'AccountController@jv_update');
        Route::get('/jv_delete/{reff_no}', 'AccountController@jv_delete');
        
        // Payment Voucher || Debit voucher
        Route::get('/debit_voucher_list', 'AccountController@debit_voucher_list');
        Route::get('/debit_voucher', 'AccountController@debit_voucher');
        Route::post('/debit_voucher', 'AccountController@debit_voucher_create');
        Route::get('/dv_edit/{reff_no}', 'AccountController@dv_edit');
        Route::post('/dv_update', 'AccountController@dv_update');
        Route::get('/dv_delete/{reff_no}', 'AccountController@dv_delete');
        
        // Recipt Voucher || Credit voucher
        Route::get('/credit_voucher_list', 'AccountController@credit_voucher_list');
        Route::get('/credit_voucher', 'AccountController@credit_voucher');
        Route::post('/credit_voucher', 'AccountController@credit_voucher_create');
        
        Route::get('/receipt_voucher_list', 'AccountController@receipt_list'); 
        Route::get('/receipt_voucher', 'AccountController@receipt_voucher');
        
        Route::get('/cv_edit/{reff_no}', 'AccountController@cv_edit');
        Route::post('/cv_update', 'AccountController@cv_update');
        Route::get('/cv_delete/{reff_no}', 'AccountController@cv_delete');
        
          Route::post('/save_invoice', 'AccountController@save_invoice');
        



        
        
        
         
        Route::get('/voucher-listing', 'AccountController@voucher_listing');
        
        Route::get('/invoice-prt/{id}','AccountController@print_pr')->name('show.Invoiceprt');
        
        Route::get('/invoice-edit/{id}','AccountController@edit_voucher')->name('show.edit');
        Route::get('/activate/{id}', 'AccountController@activate');
        Route::get('/delete-account-transaction/{id}', 'AccountController@destroyAccountTransaction');
        Route::get('/get-account-balance/{id}', 'AccountController@getAccountBalance');
        Route::get('/balance-sheet', 'AccountReportsController@balanceSheet');
        Route::get('/trial-balance', 'AccountReportsController@trialBalance');
        Route::get('/trial-transaction', 'AccountReportsController@Transaction_trial_balance');
        
        Route::get('/payment-account-report', 'AccountReportsController@paymentAccountReport');
        Route::get('/link-account/{id}', 'AccountReportsController@getLinkAccount');
        Route::post('/link-account', 'AccountReportsController@postLinkAccount');
        Route::get('/cash-flow', 'AccountController@cashFlow');
        
        Route::get('/trial_balacnce_data', 'AccountReportsController@trial_balacnce_data');
        Route::get('/transaction-trial-balance', 'AccountReportsController@date_wise')->name('transaction-trial-balance');
        
    });
    
    Route::resource('account-types', 'AccountTypeController');

    //Restaurant module
    Route::group(['prefix' => 'modules'], function () {
        Route::resource('tables', 'Restaurant\TableController');
        Route::resource('modifiers', 'Restaurant\ModifierSetsController');

        //Map modifier to products
        Route::get('/product-modifiers/{id}/edit', 'Restaurant\ProductModifierSetController@edit');
        Route::post('/product-modifiers/{id}/update', 'Restaurant\ProductModifierSetController@update');
        Route::get('/product-modifiers/product-row/{product_id}', 'Restaurant\ProductModifierSetController@product_row');

        Route::get('/add-selected-modifiers', 'Restaurant\ProductModifierSetController@add_selected_modifiers');

        Route::get('/kitchen', 'Restaurant\KitchenController@index');
        Route::get('/kitchen/mark-as-cooked/{id}', 'Restaurant\KitchenController@markAsCooked');
        Route::post('/refresh-orders-list', 'Restaurant\KitchenController@refreshOrdersList');
        Route::post('/refresh-line-orders-list', 'Restaurant\KitchenController@refreshLineOrdersList');

        Route::get('/orders', 'Restaurant\OrderController@index');
        Route::get('/orders/mark-as-served/{id}', 'Restaurant\OrderController@markAsServed');
        Route::get('/data/get-pos-details', 'Restaurant\DataController@getPosDetails');
        Route::get('/orders/mark-line-order-as-served/{id}', 'Restaurant\OrderController@markLineOrderAsServed');
        Route::get('/print-line-order', 'Restaurant\OrderController@printLineOrder');
    });

    Route::get('bookings/get-todays-bookings', 'Restaurant\BookingController@getTodaysBookings');
    Route::resource('bookings', 'Restaurant\BookingController');
    
    Route::resource('types-of-service', 'TypesOfServiceController');
    Route::get('sells/edit-shipping/{id}', 'SellController@editShipping');
    Route::put('sells/update-shipping/{id}', 'SellController@updateShipping');
    Route::get('shipments', 'SellController@shipments');


    // Soda ANd Miling Details

    // Booking
    Route::get('soda-details', 'sodaController@index')->name('soda.index');
    
    Route::get('/soda', 'sodaController@create')->name('soda.create');
    Route::post('soda-Add', 'sodaController@store')->name('soda.store');
    
    Route::get('miling-index', 'sodaController@millingdetail_index');
    Route::get('/milling_detail/create', 'sodaController@millingdetail_create');
    Route::post('/milling_detail/store', 'sodaController@millingdetail_store');


    Route::get('soda-report', 'sodaController@soda_report')->name('soda.report');
    Route::get('get_booking/{id}', 'sodaController@get_booking')->name('booking');

    Route::get('get_booking_id/{id}', 'sodaController@get_booking_id')->name('booking_id');


    

    // Route::get('Miling-details', 'sodaController@MilingIndex')->name('Miling.index');


    // Dispatch
    Route::get('dispatch-details', 'sodaController@index_dispatch')->name('dispatch.index');
    Route::get('/dispatch', 'sodaController@create_dispatch')->name('dispatch.create');
    Route::post('Add-dispacth', 'sodaController@store_dispatch')->name('dispacth.store');


    // End
    
    Route::get('/get_product_onchange/{id}', 'PurchaseController@get_type_product');
    
    // for get product type
    Route::get('/get_product_types/{id}', 'PurchaseController@get_product_types');


    Route::post('upload-module', 'Install\ModulesController@uploadModule');
    Route::resource('manage-modules', 'Install\ModulesController')
        ->only(['index', 'destroy', 'update']);
    Route::resource('warranties', 'WarrantyController');

    Route::resource('dashboard-configurator', 'DashboardConfiguratorController')
    ->only(['edit', 'update']);

    Route::get('view-media/{model_id}', 'SellController@viewMedia');

    //common controller for document & note
    Route::get('get-document-note-page', 'DocumentAndNoteController@getDocAndNoteIndexPage');
    Route::post('post-document-upload', 'DocumentAndNoteController@postMedia');
    Route::resource('note-documents', 'DocumentAndNoteController');
    Route::resource('purchase-order', 'PurchaseOrderController');
    Route::get('get-purchase-orders/{contact_id}', 'PurchaseOrderController@getPurchaseOrders');
    Route::get('get-purchase-order-lines/{purchase_order_id}', 'PurchaseController@getPurchaseOrderLines');
    Route::get('get-purchase-requisation/{purchase_order_id}', 'PurchaseController@get_prq');
    
    Route::get('get-delivery-note/{purchase_order_id}', 'PurchaseController@get_delivery_note');


    Route::get('get-purchase-grn/{purchase_order_id}', 'PurchaseController@get_grn');
    Route::get('po_entries/{purchase_order_id}', 'PurchaseController@po_entries');
    Route::get('convert_pr_po/{id}', 'PurchaseOrderController@convert_pr_to_po');
    Route::get('edit_invoice/{id}', 'PurchaseOrderController@edit_invoice');
    Route::get('convert_po_to_grn/{id}', 'PurchaseOrderController@convert_po_to_grn');
    Route::get('convert_grn_to_pi/{id}', 'PurchaseController@convert_grn_to_pi');
    Route::get('convert_so_to_dn/{id}', 'SellController@convert_so_to_dn');
    Route::get('convert_dn_to_si/{id}', 'SellController@convert_dn_to_si');


    Route::get('edit-purchase-orders/{id}/status', 'PurchaseOrderController@getEditPurchaseOrderStatus');
    Route::put('update-purchase-orders/{id}/status', 'PurchaseOrderController@postEditPurchaseOrderStatus');
    Route::resource('sales-order', 'SalesOrderController')->only(['index']);
    Route::get('get-sales-orders/{customer_id}', 'SalesOrderController@getSalesOrders');
    Route::get('get-sales-order-lines', 'SellPosController@getSalesOrderLines');
    Route::get('edit-sales-orders/{id}/status', 'SalesOrderController@getEditSalesOrderStatus');
    Route::put('update-sales-orders/{id}/status', 'SalesOrderController@postEditSalesOrderStatus');
    Route::get('reports/activity-log', 'ReportController@activityLog');

    Route::get('Purchase_invoice','PurchaseOrderController@index_invoice')->name('index_invoice');
    Route::get('/Purchase_invoice/create', 'PurchaseOrderController@create_invoice');
    Route::Post('/Purchase_invoice/store_invoice', 'PurchaseOrderController@store_invoice');

    // Store

    Route::get('Store','StoreController@index')->name('Store');
    Route::Post('/stores', 'StoreController@store')->name('add_store');
    Route::get('/store_delete/{id}', 'StoreController@store_delete')->name('store_delete');

    // create_requision
    Route::get('Purchase_requision','PurchaseOrderController@index_requision')->name('index_invoice');
    Route::get('/Purchase_Requision/Requision', 'PurchaseOrderController@create_requision');
    Route::Post('/Purchase_Requis/store_requisition', 'PurchaseOrderController@store_requision');
    Route::get('/DeleteRecords/{type}', 'PurchaseOrderController@View_DR');
    
    Route::get('/get_term/{id}', 'PurchaseOrderController@get_term')->name('get_term');


    Route::get('/purchase_req/{id}/edit', 'PurchaseOrderController@edit_req')->name('purchase_req_edit');
    Route::PUT('/purchase_requisition/{id}/edit', 'PurchaseOrderController@update_req')->name('purchase_req_update');
 

    
    Route::get('Purchasetype','PurchaseOrderController@Purchase_type')->name('Purchase_type');
    Route::get('PurchasetypePartial','PurchaseOrderController@Purchase_type_partial');
    Route::Post('/purchase_store_partial', 'PurchaseOrderController@purchase_store_partial');
    Route::Post('/purchase_store', 'PurchaseOrderController@purchase_store');
    Route::get('/purchase_type_update/{id}/edit', 'PurchaseOrderController@Purchase_type_update');
    Route::post('/purchase_type_edit/{id}', 'PurchaseOrderController@Purchase_type_edit');

    Route::post('/return_store', 'SellPosController@return_store')->name('return_store_n');

    
    Route::get('/purchase_type/{id}', 'PurchaseOrderController@delete_type')->name('type_delete');

 Route::get('Term_Conditions','PurchaseOrderController@Term_conditions')->name('Term_conditions');
    Route::Post('/Term_Conditions_store', 'PurchaseOrderController@Term_conditions_store');
    Route::get('/Term_Conditions_update/{id}/edit', 'PurchaseOrderController@Term_conditions_update');
    Route::post('/Term_Conditions_edit/{id}', 'PurchaseOrderController@Term_conditions_edit');
    Route::get('/Term_Conditions/{id}', 'PurchaseOrderController@Term_conditions_delete')->name('Term_conditions_delete');


    Route::get('get_account_head_data/{type}','ContactController@get_account_head_data');

    // Country city state

    Route::get('get_data','CountryController@index')->name('get_data');
    Route::post('insert_data','CountryController@store')->name('insert_data');

    // End

    Route::get('combine_data','CountryController@combineIndex')->name('get_combine');
    Route::post('store_combine','CountryController@store_combine')->name('store_combine');

    Route::get('get_country_data/{id}','CountryController@get_data')->name('get_country_data');

    Route::get('get_city/{id}','CountryController@get_city')->name('get_city');
    

});


Route::middleware(['EcomApi'])->prefix('api/ecom')->group(function () {
    Route::get('products/{id?}', 'ProductController@getProductsApi');
    Route::get('categories', 'CategoryController@getCategoriesApi');
    Route::get('brands', 'BrandController@getBrandsApi');
    Route::post('customers', 'ContactController@postCustomersApi');
    Route::get('settings', 'BusinessController@getEcomSettings');
    Route::get('variations', 'ProductController@getVariationsApi');
    Route::post('orders', 'SellPosController@placeOrdersApi');
});

//common route
Route::middleware(['auth'])->group(function () {
    Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
});

Route::middleware(['setData', 'auth', 'SetSessionData', 'language', 'timezone'])->group(function () {
    Route::get('/load-more-notifications', 'HomeController@loadMoreNotifications');
    Route::get('/get-total-unread', 'HomeController@getTotalUnreadNotifications');
    Route::get('/purchases/print/{id}', 'PurchaseController@printInvoice');

    
    
    
    
    Route::get('/purchases/{id}', 'PurchaseController@show');
    Route::get('/download-purchase-order/{id}/pdf', 'PurchaseOrderController@downloadPdf')->name('purchaseOrder.downloadPdf');
    Route::get('/sells/{id}', 'SellController@show');
    Route::get('/sells/{transaction_id}/print', 'SellPosController@printInvoice')->name('sell.printInvoice');
    Route::get('/download-sells/{transaction_id}/pdf', 'SellPosController@downloadPdf')->name('sell.downloadPdf');
    Route::get('/download-quotation/{id}/pdf', 'SellPosController@downloadQuotationPdf')
        ->name('quotation.downloadPdf');
    Route::get('/download-packing-list/{id}/pdf', 'SellPosController@downloadPackingListPdf')
        ->name('packing.downloadPdf');
    Route::get('/sells/invoice-url/{id}', 'SellPosController@showInvoiceUrl');
    Route::get('/show-notification/{id}', 'HomeController@showNotification');
});