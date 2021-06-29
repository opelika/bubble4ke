<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Auth::routes();

/*
 *
 * No auth
 *
 */

// Home
Route::get('/', function() {
    return view('welcome');
});
Route::get('/home', function() {
    return redirect('/');
});

// Assets
Route::any('/asset', 'AssetController@getAsset');

// Body colors
Route::any('/user/getbodycolors/{user}', 'BodyColorController@getBodyColorsFromId');
Route::any('/user/getbodycolorsfromname/{user}', 'BodyColorController@getBodyColorsFromName');

// Character appearance
Route::post('/user/th/douploadthumbnail', 'UserController@uploadThumbnail');
Route::any('/user/th/thumbqueueremove', 'UserController@removeFromQueue');
Route::any('/user/th/getthumbnailqueue', 'UserController@getThumbnailQueue');
Route::any('/user/th/getthumbasset/{id}', 'UserController@getAssetCharAppThumb');
Route::any('/user/th/getthumbcharapp/{user}', 'UserController@getCharAppThumb');

Route::any('/user/getcharapp/{user}', 'UserController@getCharApp');
Route::any('/user/getshirt/{id}', 'AssetController@getShirt');
Route::any('/user/gettshirt/{id}', 'AssetController@getTShirt');
Route::any('/user/getpants/{id}', 'AssetController@getPants');
Route::any('/user/getface/{id}', 'AssetController@getFace');

// Places and auth
Route::get('/place/ping/{place}+{online}+{key}', 'GamesController@placePing');
Route::get('/user/checkuser/{player}+{place}', 'UserController@checkUser');
//Route::get('/user/checktoken/{key}+{user}', 'UserController@checkToken');
Route::get('/user/checktoken/{place}+{key}', 'UserController@checkToken');
Route::any('/client/join/{place}+{key}', 'GamesController@getClientScript');
Route::any('/server/getscript/{place}+{key}', 'GamesController@getServerScript');
Route::any('/client/callbackv1', function( ) {
    return "OK";
});

/*
 *
 * End no auth
 *
 */

// Misc. client pages
Route::any('/Asset/GetScriptState.ashx', function () { return; });
Route::any('/game/help.aspx', function () { return view('gameclient.help'); });
Route::any('ide/landing.aspx', function () { return view('gameclient.landing'); });
Route::any('/uploadmedia/postimage.aspx', function() { return view('gameclient.postimage'); });
Route::any('/abusereport/ingamechat.aspx', function() { return '<img src="https://i.ytimg.com/vi/LO9vzDyLpB4/maxresdefault.jpg" width="100%" height="100%">'; });

Route::group(['middleware' => ['auth']], function () {
    // Not-a-downloads page
    Route::get('/downloads', function () {
        return view('downloads');
    });

    // Server list stuff
    Route::get('/games', 'GamesController@showList');
    Route::get('/games/new', function () {
        return view('games.new');
    });
    Route::post('/games/new', 'GamesController@newGame');
    Route::get('/game/{id}', 'GamesController@showGame');
    Route::get('/game/{id}/server', 'GamesController@showGameWithServer');
    Route::get('/game/delete/{id}', 'GamesController@deleteGame');
    Route::post('/game/delete/{id}', 'GamesController@deleteGameConfirm');

    // Auth token stuff
    Route::get('/user/gettoken/{id}', 'UserController@makeToken');

    // Body colors
    Route::post('/user/changecolor', 'BodyColorController@changeBodyColors');
    Route::get('/character', 'BodyColorController@showColorsUI');
    Route::get('/character/{type}', 'UserController@showOwnedAssets');
    Route::post('/character/toggle/{id}', 'UserController@toggleUserItem');

    // Catalog
    Route::get('/catalog/new', function() { return view('catalog.newasset'); });//'CatalogController@showUploadAssetForm');
    Route::post('/catalog/new', 'CatalogController@uploadAsset');
    Route::get('/catalog/{type}', 'CatalogController@showCatalog');
    Route::get('/catalog', 'CatalogController@showCatalog');
    Route::get('/catalog/getthumb/{id}', 'AssetController@getAssetThumbnail');
    Route::get('/user/currentthumb', 'UserController@getThumbnail');
    Route::get('/user/getthumb/{id}', 'UserController@getUserThumbnail');
    Route::post('/user/regenthumb', 'UserController@regenThumbnail');

    Route::get('/item/{id}', 'CatalogController@showItemPage');
    Route::post('/item/{id}', 'CatalogController@buyItemFromPage');
    Route::get('/item/{id}/settings', 'CatalogController@showItemSettingsPage');
    Route::post('/item/{id}/settings', 'CatalogController@updateItemSettings');

    Route::get('/user/{id}', 'UserController@showUserProfile');
    Route::get('/users', 'UserController@showUserList');
    Route::get('/settings', function () {
        return view('user.settings');
    });
    Route::post('/settings/blurb', 'UserController@changeBlurb');
});

Route::group(['middleware' => ['auth', 'adminonly']], function () {
    // Admin
    Route::get('/admin', 'AdminController@showHome');

    Route::get('/admin/ipbans', 'AdminController@showIpBans');
    Route::get('/admin/ipbans/new', 'AdminController@newIpBan');
    Route::post('/admin/ipbans/new', 'AdminController@addNewIpBan');
    Route::get('/admin/ipbans/delete', 'AdminController@deleteIpBan');

    Route::get('/admin/yesno', 'AdminController@showYesNoDecider');

    Route::get('/admin/users', 'AdminController@showUserList');
    Route::post('/admin/users/toggleban', 'AdminController@banUserToggle');

    Route::get('/admin/assets', 'AdminController@showAssetModPage');
    Route::post('/admin/assets/changestatus', 'AdminController@changeAssetModStatus');

    //Route::get('/admin/regenallthumbs', 'UserController@regenAllThumbnailsDebug');
});

