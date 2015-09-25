<?php
//Home
Route::get('/', ['as' => 'front_home', 'uses' => 'HomeController@index']);

//Location
Route::post('/search-location', ['as' => 'front_search_location', 'uses' => 'HomeController@ajaxSearchLocation']);
Route::get('/select-location/{id}', ['as' => 'front_select_location', 'uses' => 'HomeController@selectLocation']);

//Logout
Route::get('logout', ['as' => 'front_logout', 'uses' => 'AuthController@logout']);

Route::group(['middleware' => 'guest'], function($route){
    $route->match(['get', 'post'], 'login', ['as' => 'front_login', 'uses' => 'AuthController@authenticate']);
    $route->match(['get', 'post'], 'register', ['as' => 'front_register', 'uses' => 'AuthController@register']);
});

Route::group(['middleware' => 'auth'], function(){
    Route::group(['prefix' => 'setting'], function($route){

        //Setting account
        $route->get('account', ['as' => 'front_setting_account', 'uses' => 'SettingController@index']);
        $route->post('account/change-basic', ['as' => 'front_setting_acc_basic', 'uses' => 'SettingController@ajaxSaveBasicInfo']);
        $route->post('account/change-password', ['as' => 'front_setting_change_pass', 'uses' => 'SettingController@ajaxChangePassword']);
        $route->post('account/change-avatar', ['as' => 'front_setting_change_avatar', 'uses' => 'SettingController@ajaxChangeAvatar']);

        //Setting store
        $route->get('store', ['as' => 'front_setting_store', 'uses' => 'SettingController@store']);
        $route->get('store/get-district/{id}', ['as' => 'front_setting_get_district', 'uses' => 'SettingController@ajaxGetDistrictByCityId']);
        $route->get('store/get-ward/{id}', ['as' => 'front_setting_get_ward', 'uses' => 'SettingController@ajaxGetWardByCityId']);
        $route->post('store/change-info', ['as' => 'front_setting_store_change', 'uses' => 'SettingController@ajaxSaveStoreInfo']);
        $route->post('store/check-slug-unique', ['as' => 'front_setting_store_slug_unique', 'uses' => 'SettingController@ajaxCheckStoreSlugUnique']);

        Route::group(['middleware' => 'storer'], function($route){
            //Setting store
            $route->post('store/change-cover', ['as' => 'front_setting_change_cover', 'uses' => 'SettingController@ajaxChangeCover']);

            //Store
            //$route->get('my-store', ['as' => 'front_my_store', 'uses' => 'StoreController@index']);
            $route->post('save-product', ['as' => 'front_save_product', 'uses' => 'StoreController@ajaxSaveProduct']);
            $route->post('upload-product-image', ['as' => 'front_product_image', 'uses' => 'StoreController@ajaxUploadProductImage']);
            $route->get('product-del-temp-img', ['as' => 'front_product_del_temp_img', 'uses' => 'StoreController@ajaxDeleteProductTempImg']);
            $route->get('p/{id}', ['as' => 'front_find_product_by_id', 'uses' => 'StoreController@ajaxFindProductById']);
            $route->delete('delete-product', ['as' => 'front_delete_product', 'uses' => 'StoreController@ajaxDeleteProduct']);
        });
        
        Route::group(['prefix' => 'product'], function($route) {
            $route->post('pin', ['as' => 'front_product_pin', 'uses' => 'StoreController@ajaxPinProduct']);
            $route->post('comments/{product_id}/add', ['as' => 'front_comments_add', 'uses' => 'StoreController@ajaxProductAddComment']);
            $route->delete('comments/{product_id}/{store_slug}/delete/{comment_id}', ['as' => 'front_comments_delete', 'uses' => 'StoreController@ajaxProductDeleteComment']);
        });
    });
});

Route::get('p/{id}/{store_slug}', ['as' => 'front_product_quick_view', 'uses' => 'StoreController@ajaxGetQuickViewProduct']);
Route::post('comments/{product_id}/load-more', ['as' => 'front_comments_load_more', 'uses' => 'StoreController@ajaxLoadMoreComments']);

Route::get('/{slug}', ['as' => 'front_astore', 'uses' => 'StoreController@store']);