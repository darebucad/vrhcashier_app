<?php


Auth::routes();

Route::get('/', 'DashboardController@index')->name('dashboard');


Route::get('collections/outpatient/getdata', 'CollectionsOutpatientController@getdata')->name('collections.outpatient.getdata');


Route::get('collections/outpatient/action', 'CollectionsOutpatientController@action')->name('collections.outpatient.action');


Route::get('collections/outpatient/getCustomFilterData', 'CollectionsOutpatientController@getCustomFilterData')->name('collections.outpatient.getCustomFilterData');


Route::post('collections/outpatient/create', 'CollectionsOutpatientController@show')->name('collections.outpatient.create.show');


Route::get('/collections/outpatient/create/showcharge/{id}', 'CollectionsOutpatientController@edit')->name('collections.outpatient.edit');


// Collections Outpatient Routes


Route::get('collections/outpatient', 'CollectionsOutpatientController@index')->name('collections.outpatient');


Route::get('collections/outpatient/create/{id}', 'CollectionsOutpatientController@create')->name('collections.outpatient.create');


Route::get('collections/outpatient/create/load_data', 'CollectionsOutpatientController@loadData');


Route::post('collections/outpatient/create/post_data', 'CollectionsOutpatientController@postData');


Route::post('collections/outpatient/create/postajax','CollectionsOutpatientController@post');


Route::get('collections/outpatient/create/get_or_number', 'CollectionsOutpatientController@getORNumber');


Route::post('collections/outpatient/create/payment', 'CollectionsOutpatientController@store');


Route::post('collections/outpatient/create/apply_discount_all', 'CollectionsOutpatientController@applyDiscountAll');


Route::post('collections/outpatient/create/apply_discount_selected', 'CollectionsOutpatientController@applyDiscountSelected');


Route::post('collections/outpatient/create/update_data', 'CollectionsOutpatientController@updateData');


Route::get('/collections/outpatient/print', 'CollectionsOutpatientController@pdfIndex');


Route::get('/collections/outpatient/print/pdf/{id}', 'CollectionsOutpatientController@showPDF')->name('collections.outpatient.print.pdf');


