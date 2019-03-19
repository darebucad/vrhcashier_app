<?php

Auth::routes();

Route::get('/', 'DashboardController@index')->name('dashboard');

// Collections Outpatient
Route::get('collections/outpatient', 'CollectionsOutpatientController@index')->name('collections.outpatient');
Route::get('collections/outpatient/create/{id}', 'CollectionsOutpatientController@create')->name('collections.outpatient.create');
Route::get('collections/outpatient/create/load_data', 'CollectionsOutpatientController@loadData');
Route::post('collections/outpatient/create/post_data', 'CollectionsOutpatientController@postData');
Route::post('collections/outpatient/create/postajax','CollectionsOutpatientController@post');
Route::get('collections/outpatient/create/get_or_number', 'CollectionsOutpatientController@getORNumber');
Route::post('collections/outpatient/create/payment', 'CollectionsOutpatientController@store');
Route::post('collections/outpatient/create/apply_discount_all', 'CollectionsOutpatientController@applyDiscountAll');
Route::post('collections/outpatient/create/apply_discount_selected', 'CollectionsOutpatientController@applyDiscountSelected');
Route::post('collections/outpatient/create/clear_discount', 'CollectionsOutpatientController@clearDiscount');
Route::get('/collections/outpatient/print', 'CollectionsOutpatientController@pdfIndex');
Route::get('/collections/outpatient/print/pdf/{id}', 'CollectionsOutpatientController@showPDF')->name('collections.outpatient.print.pdf'); // print OR pdf
Route::get('/collections/outpatient/cancel/payment', 'CollectionsOutpatientController@cancelPayment');
Route::get('/collections/outpatient/payment/edit', 'CollectionsOutpatientController@edit');
Route::post('collections/outpatient/create', 'CollectionsOutpatientController@show')->name('collections.outpatient.create.show');
Route::get('collections/outpatient/getdata', 'CollectionsOutpatientController@getdata')->name('collections.outpatient.getdata');
Route::get('/collections/outpatient/get_outpatient_payment_data', 'CollectionsOutpatientController@getOutpatientPaymentData');
Route::post('/collections/outpatient/update_total', 'CollectionsOutpatientController@updateTotal');

// Collections Other
Route::get('/collections/other', 'CollectionsOtherController@index')->name('collections.other');
Route::get('collections/other/create/{id}', 'CollectionsOtherController@create')->name('collections.other.create');
Route::get('/collections/other/show_products', 'CollectionsOtherController@showProducts')->name('collections.other.show_products');
Route::get('collections/other/get_latest_price', 'CollectionsOtherController@getLatestPrice')->name('collections.get_latest_price');
Route::post('collections/other/store_payment', 'CollectionsOtherController@storePayment')->name('collections.store_payment');
Route::get('collections/other/get_patient_list', 'CollectionsOtherController@getPatientList');
Route::get('/collections/other/print/pdf/{id}', 'CollectionsOtherController@printPDF'); // print other collection OR pdf
Route::get('/collections/other/autocomplete-search', 'CollectionsOtherController@search');
Route::get('/collections/outpatient/get-other-collection-data', 'CollectionsOtherController@getOtherCollectionData');

// Collections Inpatient
Route::get('collections/inpatient', 'CollectionsInpatientController@index')->name('collections.inpatient');

// Collections Walkin
Route::get('collections/walkin', 'CollectionsWalkinController@index');
Route::get('collections/walkin/create', 'CollectionsWalkinController@create')->name('collections.walkin.create');

//Settings User Account
Route::get('settings/user_account', 'UsersController@index')->name('settings.user_account');
Route::get('settings/user_account/create', 'UsersController@create');

//Success
Route::get('/auth/success', ['as'   => 'auth.success','uses' => 'Auth\AuthController@success']);




// use JasperPHP\JasperPHP as JasperPHP;
//
// Route::get('/', function () {
//
//     $jasper = new JasperPHP;

	// Compile a JRXML to Jasper
    // $jasper->compile(__DIR__ . '/../../vendor/cossou/jasperphp/examples/hello_world.jrxml')->execute();

	// Process a Jasper file to PDF and RTF (you can use directly the .jrxml)
    // $jasper->process(
    //     __DIR__ . '/vendor/cossou/jasperphp/examples/hello_world.jasper',
    //     false,
    //     array("pdf", "rtf"),
    //     array("php_version" => "xxx")
    // )->execute();



	// List the parameters from a Jasper file.
    // $array = $jasper->list_parameters(
    //     __DIR__ . '/vendor/cossou/jasperphp/examples/hello_world.jasper'
    // )->execute();


//
//     return view('welcome');
// });
