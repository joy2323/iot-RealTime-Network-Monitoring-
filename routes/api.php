<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();

});

// for SGC application
Route::get('/getdeviceparameter', 'DeviceConfigController@getDeviceParameter');

Route::get('/getclient', 'DeviceConfigController@getClient');
Route::get('/getbu', 'DeviceConfigController@getClientUnit');
Route::get('/getut', 'DeviceConfigController@getClientSubUnit');

Route::get('/getsiteconfig', 'DeviceConfigController@getSites');

Route::post('/deviceconfig', 'DeviceConfigController@createSite');
Route::get('/getsitedata', 'DeviceConfigController@getsitebyname');

Route::post('/load_dashboard', 'HomeController@loaddashboard');
Route::get('/user_dashboard', 'HomeController@attachdashboardtouser');
Route::get('/cleansitereport', 'UtilsAPIController@cleanSitereport');

// Route::get('/cleanalarmtracker', 'UtilsAPIController@cleanalarmtracker');
Route::get('/cleanalarmtracker', 'DeviceApiController@cleanalarmtracker');

Route::get('/sendAlarmNotify', 'AlarmstatusController@sendAlarmsNotification');

// for DT device connection to server
Route::get('/connect', 'DeviceApiController@getDetails');

//cron job api for site scanpo
Route::get('/updowntime', 'DownLiveSiteController@scanAllSites');

Route::post('notify', 'PushNotifierController@notify');
Route::post('notifier', 'PushNotifierController@alarmnotify');
// for client access api
Route::group(['middleware' => ['auth.apikey']], function () {

    Route::get('/get-sites', 'ApiController@getClientSite');
    Route::get('/get-sites-with-data', 'ApiController@getAllClientSitesDetails');

    Route::get('/get-site-data-with-id', 'ApiController@getSiteDetailswithid');
    Route::get('/get-site-data-with-name', 'ApiController@getSiteDetailswithname');
    Route::get('/get-site-data-with-sitenum', 'ApiController@getSiteDetailswithsitenum');

});

// desktop api


//mobile api
// Route::group(['middleware' =>['auth']], function () {
        
    Route::post('/userlogin', 'MobileAppApiController@mobileLogin');
    Route::post('/overview', 'MobileAppApiController@userDeviceOverview');
    Route::post('/allsitealarm', 'MobileAppApiController@allSiteAlarmStatus');
    Route::post('/all-siteoverview', 'MobileAppApiController@siteOverview');
    Route::post('/get-siteuser', 'MobileAppApiController@getUserSiteData');
    Route::post('/site-detail/{id}', 'MobileAppApiController@singleSiteDetail');




// });
Route::get('/clientreviews', 'DesktopAppApiController@sitePreview');

Route::get('/clientsummary', 'DesktopAppApiController@siteSummary');

Route::post('/activateworkspace', 'DesktopAppApiController@activateWorkspace');

Route::post('/userauthenticate', 'DesktopAppApiController@loginAuth');
Route::post('/userlogout', 'DesktopAppApiController@logout');

Route::post('/checkAuth', 'DesktopAppApiController@checkAuth');

Route::get('/loadscada', 'DesktopAppApiController@loadScadaData');

Route::get('/control', 'DesktopAppApiController@CBControl');

Route::get('/BUdata', 'DesktopAppApiController@loadNodeDetails');

Route::get('/sitedata', 'DesktopAppApiController@loadSiteDetails');

//mobile api

Route::post('/userlogin', 'MobileAppApiController@mobileLogin');
Route::get('/overview', 'MobileAppApiController@userDeviceOverview');
Route::get('/v1/overview', 'MobileAppApiController@Overview');
Route::get('/v1/login', 'MobileAppApiController@mobileLogin');
Route::get('/v1/dashboard', 'MobileAppApiController@getDashboardData');
Route::get('/v1/site', 'MobileAppApiController@singleSiteDetail');
Route::get('/v1/pushmsg', 'DeviceApiController@testNotify');
