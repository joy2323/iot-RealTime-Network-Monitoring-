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

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

// Globally access routes
Route::get('/control', 'ControlController@viewcontrol')->name('index')->middleware('auth');
Route::get('/autocomplete', 'ControlController@autocomplete')->name('autocomplete')->middleware('auth');
Route::get('/getsite/{site_name}', 'ControlController@sitedata');
Route::get('/controlterminal/{id}', 'ControlController@control')->name('controlterminal')->middleware('auth');
Route::get('/getTabledata', 'ControlController@getTabledata');
Route::post('/validateControlpass', 'ControlController@validateControlPassword');
Route::post('/getBUUT', 'ControlController@getBU_UT');
Route::post('/getUT', 'ControlController@getUT');

Route::post('/dtctrl', 'CtrlFeedbackController@store');
Route::get('/getfeedbck/{serial_number}', 'CtrlFeedbackController@controlfeedback');

Route::get('/chart/{serial_number}', 'ChartController@chart')->middleware('auth');
Route::get('/chartapi/{serial_number}', 'ChartController@chartapi')->middleware('auth');

Route::get('/injection-view/{id}', 'ChartController@viewINJChart')->middleware('auth');
Route::get('/injection-data', 'ChartController@loaddata')->middleware('auth');
Route::get('/view-stations', 'InjStationController@viewstations')->middleware('auth');

Route::get('/add-communication', 'CommunicationController@addCommunication')->middleware('auth');
Route::get('/communication', 'CommunicationController@viewCommuncation')->middleware('auth');
Route::post('/save-communication', 'CommunicationController@storeCommunication')->middleware('auth');
Route::get('/get-communication/{id}', 'CommunicationController@getCommunication')->middleware('auth');
Route::patch('/edit-communication', 'CommunicationController@editCommunication')->middleware('auth');
Route::post('/delete-communication/{id}', 'CommunicationController@deleteCommunication')->middleware('auth');

Route::get('/add-substation-communication', 'CommunicationController@addINJCommunication')->middleware('auth');
Route::get('/substation-communication', 'CommunicationController@viewINJCommuncation')->middleware('auth');

Route::post('/add-admin', 'AdminController@create')->middleware('auth');
Route::get('/admin-list', 'AdminController@index')->middleware('auth');

Route::patch('/edit-admin', 'AdminController@editAdmin')->middleware('auth');

Route::get('/get-admin/{id}', 'AdminController@getAdmin')->middleware('auth');
Route::delete('/delete-admin', 'AdminController@deleteAdmin')->middleware('auth');

Route::get('/site/{id}', 'HomeController@getSite')->middleware('auth');
Route::patch('/edit-site', 'HomeController@editSite')->middleware('auth');

Route::get('/Unauthorized', 'HomeController@Unauthorized');

Route::get('/control-Activities', 'ControlLogsController@index')->middleware('auth');
Route::get('/getcontrolactivities', 'ControlLogsController@show')->middleware('auth');

Route::get('/login-Activities', 'LoginActivitiesController@index')->middleware('auth');
Route::get('/getloginactivities', 'LoginActivitiesController@show')->middleware('auth');

// Route::get('details', function () {

//     // $ip = '50.90.0.1';
//     $data = \Location::get($ip);
// });

Route::get('/password/{id}', 'HomeController@getAccountSetting')->middleware('auth');
Route::post('/password-update', 'HomeController@editAccountSetting')->middleware('auth');

Route::get('add-to-log', 'HomeController@myTestAddToLog');
Route::get('logActivity', 'HomeController@logActivity');

Route::get('/power', 'PowerController@powerView')->middleware('auth');
Route::get('/fetchpower', 'PowerController@fetchPower')->middleware('auth');
Route::get('/fetchsite', 'PowerController@fetchSiteQuery')->middleware('auth');
Route::get('/fetchsitereport', 'PowerController@fetchPowerQuery')->middleware('auth');

Route::get('/alarms', 'AlarmstatusController@viewAlarmReport')->middleware('auth');
Route::get('/fetchalarms', 'AlarmstatusController@fetchAlarmReport')->middleware('auth');
Route::get('/updatealarms', 'AlarmstatusController@alarmDurationUpdate')->middleware('auth');

// This Route is for Super Admin Only!!

Route::group(['middleware' => ['superadmin']], function () {
    Route::get('/admin', 'HomeController@index')->name('home');
    Route::get('/admin-injection-station', 'HomeController@indexhv')->name('homehv');
    Route::get('/admin-sites', 'HomeController@getdtsites')->name('dtsites');
    Route::get('/load-sites', 'HomeController@loadsites')->name('loadsites');
    Route::get('/alllocation', 'HomeController@getSiteLocation');
    Route::get('/alllocationhv', 'HomeController@getHVSiteLocation');
    Route::get('/scanallsites', 'DownLiveSiteController@scanAllSites');

    Route::delete('/delete-site', 'HomeController@deletesite');
    Route::delete('/delete-sites', 'HomeController@deletesites');
    Route::post('/delete-communications', 'CommunicationController@deleteCommunications');
    Route::get('/view-site', 'HomeController@viewsite');

    Route::get('/clients', 'HomeController@getClients');
    Route::get('/create-client', 'HomeController@viewcreateClient');

    Route::get('/create-bu', 'HomeController@viewcreateBU');
    Route::get('/create-injection-station', 'HomeController@viewcreateINJ');

    Route::post('/add-dashboard', 'DashboardController@create');
    Route::post('/add-dashnaming', 'DashboardController@Dashnamecreate');

    Route::post('/add-client', 'HomeController@createClient');
    Route::post('/add-bu', 'HomeController@createBU');

    Route::post('/add-inj', 'HomeController@createINJ');

    Route::patch('/edit-client', 'HomeController@editClient');
    Route::delete('/delete-client', 'HomeController@deleteClient');

    Route::get('/get-client/{id}', 'HomeController@getClient');
    Route::get('/sites/{id}', 'HomeController@clientSites');

    Route::get('/loadsites/{id}', 'HomeController@loadsite');

    Route::get('/sitereviews/{id}', 'HomeController@sitePreview');

    Route::get('/dashboardupdate', 'HomeController@dashboardSite');
    Route::get('/dashboardhvupdate', 'HomeController@dashboardHVSite');
    Route::get('/sitesummary', 'HomeController@siteSummary');
    Route::get('/clientsummary', 'HomeController@clientSummary');
    Route::get('/clientHVsummary', 'HomeController@clientHVSummary');
    Route::get('/injectionstation', 'HomeController@loadHV');

    Route::get('/getNotTransmit-sites', 'HomeController@NotTransmit');

    Route::get('/Not-Transmit', 'HomeController@viewNotTransmit');
    Route::get('/getFaulty-sites', 'HomeController@getFaultySites');

    Route::get('/Fault-Sites', 'HomeController@viewFaultsites');
   
    Route::get('/login_report', 'HomeController@loginReport');
    Route::get('/Login-report', 'HomeController@generateLogin');

    Route::get('/Alarm-report', 'AlarmstatusController@viewAlarmReport');
 
    Route::get('/notification_report', 'HomeController@notificationReport');

    Route::get('/Control-Sites', 'ControlController@controlTest');
    Route::get('/Control-Resp', 'ControlController@controlsites');
    Route::get('/Control-Unit', 'ControlController@getClientUnit');
    Route::get('/Control-Subunit', 'ControlController@getClientSubUnit');

    Route::post('/test-cmd', 'ControlController@sendTestCommand');

    Route::post('/global-com-settings', 'HomeController@globalCommunicationsetting');
    Route::get('/get-global-com-settings', 'HomeController@getglobalCommunicationsetting');

    Route::post('/client-activate-settings', 'HomeController@clientactivate');

    Route::get('/cb-trip-logs', 'HomeController@viewTripLog');
    Route::get('/fetchtriplogs', 'HomeController@loadTripLog');

    //Client API management
    Route::post('/generateapikey', 'ApiController@generateKey');
    Route::post('/regenerateapikey', 'ApiController@regenerateKey');
    Route::post('/deleteapikey', 'ApiController@DeleteApiKey');
    Route::post('/activateapikey', 'ApiController@ActivateApiKey');
    Route::post('/deactivateapikey', 'ApiController@DeactivateApiKey');
    Route::get('/getapikeylist', 'ApiController@ListApiKey');
    Route::get('/allclient', 'ApiController@getAllClient');
    Route::get('/getkey', 'ApiController@keyDetails');
    Route::get('/get-api-usage', 'ApiController@ApiUsageDetails');

});

//This Route is for Client Admin only
Route::group(['middleware' => ['clientadmin']], function () {
    Route::get('/clientsite', 'ClientAdminController@loadsite')->name('clientsite');
    Route::get('/client', 'ClientAdminController@index')->name('client');
    Route::get('/clientlocation/{id}', 'ClientAdminController@getSiteLocation');
    Route::get('/scanclientsites', 'DownLiveSiteController@scanClientSites');

    Route::get('/getclientsitesfault', 'ClientAdminController@FaultySiteAnalysis')->name('faultdetails');
    Route::get('/getclientsitesdown', 'ClientAdminController@DownSiteAnalysis')->name('downdetails');

    Route::get('/clientsitereviews/{id}', 'ClientAdminController@sitePreview');

    Route::get('/getclientsitesummary/{id}', 'ClientAdminController@siteSummary');

    Route::get('/injection-stations', 'InjStationController@indexclient')->name('Injclientdashboard');

    Route::get('/viewall-bu', 'ClientAdminController@getAllBu');
    Route::get('/buadmin/{id}', 'ClientAdminController@getBUAdminDetails');
    Route::get('/get-bu/{id}', 'ClientAdminController@getSingleBUAdmin');
    Route::patch('/edit-buinfo', 'ClientAdminController@editBUAdminInfo');

    Route::get('/client-sites', 'ClientAdminController@allSitesPage');
    Route::get('/get-client-sites', 'ClientAdminController@viewAllSites');
    Route::get('/get-bu-ut/{id}', 'ClientAdminController@loadBUUTs');
    Route::get('/get-ut-bu/{id}', 'ClientAdminController@loadUTBU');

});

//This Route is for Injection station  Admin only
Route::group(['middleware' => ['injadmin']], function () {
    Route::get('/injection-station', 'InjStationController@index')->name('Injdashboard');
    Route::get('/Injection-report', 'InjStationController@viewAlarmReport');
    Route::get('/fetchinjreport', 'InjStationController@fetchAlarmReport');
    Route::get('/updateinjreport', 'InjStationController@alarmDurationUpdate');

});

// This Route is for BU Admin Only
Route::group(['middleware' => ['buadmin']], function () {
    Route::get('/bu', 'BUAdminController@viewBu')->name('buadmin');
    Route::get('/create-ut', 'BUAdminController@createUt');
    Route::post('/add-ut', 'BUAdminController@storeUtInfo');
    Route::get('/add-site-user', 'BUAdminController@getSiteUser')->name('addsite-user');
    Route::post('/addutsite', 'BUAdminController@addSiteUser');
    Route::get('/sitereviews', 'BUAdminController@sitePreview');
    Route::get('/scansites', 'DownLiveSiteController@scanSites');
    Route::get('/view-ut', 'BUAdminController@getAllUt');
    Route::get('/get-ut/{id}', 'BUAdminController@getUt');
    Route::patch('/edit-ut', 'BUAdminController@editUtInfo');
    Route::get('/ut/{id}', 'BUAdminController@getUtDetails');
    Route::delete('/delete-ut/{id}', 'BUAdminController@deleteUt');
    Route::get('/sites', 'BUAdminController@viewAllSites');

    Route::get('/resetalarm', 'BUAdminController@resetAlarm');
    Route::get('/channelconfig/{id}', 'BUAdminController@channelConfig');
    Route::get('/location', 'BUAdminController@getSiteLocation');

});

//This Route is for UT Admin Only
Route::group(['middleware' => ['utadmin']], function () {
    Route::get('/ut', 'UTAdminController@viewDashboard')->name('utadmin');
    Route::get('/ut_sitereviews', 'UTAdminController@sitePreview');
    Route::get('/utresetalarm', 'UTAdminController@resetAlarm');
    Route::get('/utlocation', 'UTAdminController@getUtSiteLocation');
    // Route::get('/utchart/{serial_number}', 'UTAdminController@chart');
    Route::get('/allsiteuser', 'UTAdminController@getAllSiteUser');
    Route::get('/getsite-siteuser/{id}', 'UTAdminController@getSingleSiteUserSite');
    Route::patch('/edit-site-siteuser', 'UTAdminController@editSiteUserSiteInfo');
    Route::get('/create-siteuser', 'UTAdminController@createSiteUser');
    Route::post('/add-siteuser', 'UTAdminController@addSiteUser');
    Route::get('/get-siteuser/{id}', 'UTAdminController@getSingleSiteUser');
    Route::patch('/edit-siteuser', 'UTAdminController@editSiteUserInfo');
    Route::delete('/delete-siteuser/{id}', 'UTAdminController@deleteSiteUser');
    Route::get('/siteuser/{id}', 'UTAdminController@getSiteUserDetail');
    Route::get('/utsites', 'UTAdminController@viewAllUtSites');
    Route::get('/addsites_siteuser', 'UTAdminController@getSitesAndSiteUser');

    Route::get('/utscansites', 'DownLiveSiteController@scanUTSites');
});

// This Route is for Site Admin Only
Route::group(['middleware' => ['siteadmin']], function () {
    Route::get('/dashboard', 'SiteUserAdminController@viewDashboard')->name('siteadmin');

    Route::get('/siteuserlocation', 'SiteUserAdminController@getSiteUserSiteLocation');
    Route::get('/siteuser_sitereviews', 'SiteUserAdminController@sitePreview');
    Route::get('/siteuser_resetalarm', 'UTAdminController@resetAlarm');

});