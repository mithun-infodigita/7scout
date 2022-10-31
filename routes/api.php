<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
Auth::routes();

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/me', [App\Http\Controllers\Api\User\UserController::class, 'me'])->name('auth.me');
    Route::resource('/languages', 'App\Http\Controllers\Api\Language\LanguageController');
    Route::resource('/currencies', 'App\Http\Controllers\Api\Currency\CurrencyController');
    Route::resource('/countries', 'App\Http\Controllers\Api\Country\CountryController');
    Route::resource('/categories/{category}/drawings', 'App\Http\Controllers\Api\Category\CategoryDrawingController');
    Route::resource('/categories/{category}/images', 'App\Http\Controllers\Api\Category\CategoryImageController');
    Route::patch('/categories/{category}/updateOrder', 'App\Http\Controllers\Api\Category\CategoryController@updateOrder');
    Route::resource('/categories', 'App\Http\Controllers\Api\Category\CategoryController');
    Route::resource('/groups', 'App\Http\Controllers\Api\Group\GroupController');
    Route::patch('/groups/{group}/groupColumns/updateOrder', 'App\Http\Controllers\Api\Group\GroupGroupColumnController@updateOrder');
    Route::resource('/groups/{group}/groupColumns', 'App\Http\Controllers\Api\Group\GroupGroupColumnController');
    Route::patch('/facets/updateOrder', 'App\Http\Controllers\Api\Facet\FacetController@updateOrder');
    Route::resource('/facets', 'App\Http\Controllers\Api\Facet\FacetController');
    Route::resource('/producers/{producer}/partImages', 'App\Http\Controllers\Api\Producer\ProducerPartImageController');
    Route::resource('/producers/{producer}/pdfs', 'App\Http\Controllers\Api\Producer\ProducerPDFController');
    Route::resource('/producers/{producer}/imports', 'App\Http\Controllers\Api\Producer\ProducerImportController');
    Route::resource('/producers/{producer}/dispatchLocations', 'App\Http\Controllers\Api\Producer\ProducerDispatchLocationController');
    Route::resource('/producers', 'App\Http\Controllers\Api\Producer\ProducerController');
    Route::resource('/dispatchLocations', 'App\Http\Controllers\Api\DispatchLocation\DispatchLocationController');
    Route::patch('/columns/updateOrder', 'App\Http\Controllers\Api\Column\ColumnController@updateOrder');
    Route::get('/columns/updateGroupColumns', 'App\Http\Controllers\Api\Column\ColumnController@updateGroupColumns');
    Route::resource('/columns', 'App\Http\Controllers\Api\Column\ColumnController');

    Route::post('/customDuties/getDuties', 'App\Http\Controllers\Api\CustomDuty\CustomDutiesController@index');
    Route::post('/freightCosts/getCosts', 'App\Http\Controllers\Api\FreightCost\FreightCostsController@index');

    Route::post('/imports/{import}/duplicateImport', 'App\Http\Controllers\Api\Import\ImportController@duplicateImport');
    Route::delete('/imports/{import}/importPartsData/truncate', 'App\Http\Controllers\Api\Import\ImportPartDataController@truncate');
    Route::get('/imports/{import}/mergeImport', 'App\Http\Controllers\Api\Import\ImportMergeController@merge');
    Route::resource('/imports/{import}/importPartsData', 'App\Http\Controllers\Api\Import\ImportPartDataController');
    Route::resource('/imports/{import}/basicFiles', 'App\Http\Controllers\Api\Import\ImportBasicFileController');
    Route::resource('/imports/{import}/additionalFiles', 'App\Http\Controllers\Api\Import\ImportAdditionalFileController');
    Route::resource('/imports/{import}/priceFiles', 'App\Http\Controllers\Api\Import\ImportPriceFileController');
    Route::resource('/imports/{import}/partsImport', 'App\Http\Controllers\Api\Import\PartImportController');
    Route::resource('/imports/{import}/importRules', 'App\Http\Controllers\Api\Import\ImportImportRuleController');
    Route::resource('/imports/{import}/importPriceRules', 'App\Http\Controllers\Api\Import\ImportPriceRuleController');
    Route::resource('/imports', 'App\Http\Controllers\Api\Import\ImportController');

    Route::patch('/importRules/updateOrder', 'App\Http\Controllers\Api\ImportRule\ImportRuleController@updateOrder');
    Route::resource('/importRules', 'App\Http\Controllers\Api\ImportRule\ImportRuleController');
    Route::resource('/importPriceRules', 'App\Http\Controllers\Api\ImportPriceRule\ImportPriceRuleController');

    Route::resource('/partImportFiles', 'App\Http\Controllers\Api\PartImportFile\PartImportFileController');

    Route::resource('/files', 'App\Http\Controllers\Api\File\FileController');


    Route::delete('/partIndexes/{partIndex}/truncate', 'App\Http\Controllers\Api\PartIndex\PartIndexController@truncate');
    Route::get('/partIndexes/{partIndex}/importToAlgolia', 'App\Http\Controllers\Api\PartIndex\PartIndexController@importToAlgolia');
    Route::resource('/partIndexes', 'App\Http\Controllers\Api\PartIndex\PartIndexController');
    Route::resource('/partIndexes/{partIndex}/parts', 'App\Http\Controllers\Api\PartIndex\PartIndexPartController');
    Route::delete('/partIndexes/deletePartsFromIndex/{import}', 'App\Http\Controllers\Api\PartIndex\PartIndexPartController@deletePartsFromIndex');

    Route::get('/customsSettings/customsAreas', 'App\Http\Controllers\Api\CustomsSetting\CustomsAreaController@index');
    Route::resource('/customsSettings', 'App\Http\Controllers\Api\CustomsSetting\CustomsSettingController');

    Route::post('/translations/postmanTranslate','App\Http\Controllers\Api\Translation\TranslationController@postmanTranslate' );

    Route::resource('/tableFilters', 'App\Http\Controllers\Api\GlobalFeature\TableFilter\TableFilterController');


    Route::group(['prefix' => '/admin'], function () {
        Route::resource('/users', 'App\Http\Controllers\Api\Admin\User\UserController');
        Route::resource('/users/{user}/tokens', 'App\Http\Controllers\Api\Admin\User\UserTokenController');
        Route::put('/users/{user}/updatePassword', 'App\Http\Controllers\Api\Admin\User\UserController@updatePassword');
    });

    Route::group(['prefix' => '/ext'], function () {
        Route::resource('/groups', 'App\Http\Controllers\Api\Ext\Group\GroupController');
        Route::resource('/categories', 'App\Http\Controllers\Api\Ext\Category\CategoryController');
        Route::post('/customDuties/getDuties', 'App\Http\Controllers\Api\CustomDuty\CustomDutiesController@index');
        Route::resource('/globalFacets', 'App\Http\Controllers\Api\Ext\Facet\GlobalFacetController');
        Route::post('/freightCosts/getCosts', 'App\Http\Controllers\Api\Ext\FreightCosts\FreightCostsController@index');
        Route::post('/logisticsCosts/getCosts', 'App\Http\Controllers\Api\Ext\Logistics\LogisticsCostsController@index');
        Route::post('/shippingCosts/getCosts', 'App\Http\Controllers\Api\Ext\Shipping\ShippingCostsController@index');
    });
});
