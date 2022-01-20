<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
 */
 
// php -S 127.0.0.1:8000 -t public

 $router->get('/', function () use ($router) {
    return $router->app->version();
});   
$router->post('/login', ['uses' => 'AuthController@login']);
$router->post('/register', ['uses' => 'AuthController@register']);
$router->post('/confirm-otp', ['uses' => 'AuthController@confirmOTP']);
$router->post('/get-user', ['uses' => 'MainController@getUser']);
$router->post('/edit-user', ['uses' => 'AuthController@editUser']);

$router->get('/get-kategori-pemasukan', ['uses' => 'MainController@getKategoriPemasukan']);
$router->get('/get-kategori-pengeluaran', ['uses' => 'MainController@getKategoriPengeluaran']);

$router->post('/set-transaksi', ['uses' => 'MainController@setTransaksi']);
$router->post('/set-transaksi-to-iuran', ['uses' => 'MainController@setTransaksiToIuran']);
$router->post('/get-transaksi-default', ['uses' => 'MainController@getTransaksiDefault']);
$router->post('/get-transaksi-by-filter', ['uses' => 'MainController@getTransaksiByFilter']);
$router->post('/get-transaksi-monthly', ['uses' => 'MainController@getTransaksiMonthly']);
$router->post('/get-transaksi-by-id', ['uses' => 'MainController@getTransaksiById']);
$router->post('/delete-transaksi', ['uses' => 'MainController@deleteTransaksi']);

$router->post('/invitation-from-komunitas', ['uses' => 'MainController@invitationFromKomunitas']);
$router->post('/join-komunitas', ['uses' => 'MainController@joinKomunitas']);
$router->post('/delete-invitation', ['uses' => 'MainController@deleteInvitation']);

$router->post('/get-message', ['uses' => 'MainController@getMessage']);
$router->post('/delete-message', ['uses' => 'MainController@deleteMessage']);

$router->post('/find-user', ['uses' => 'IuranController@findUser']);

$router->post('/get-is-admin', ['uses' => 'IuranController@getIsAdmin']);
$router->post('/create-komunitas', ['uses' => 'IuranController@createKomunitas']);
$router->post('/get-list-komunitas', ['uses' => 'IuranController@getListKomunitas']);
$router->post('/get-detail-komunitas', ['uses' => 'IuranController@getDetailKomunitas']);
$router->post('/update-komunitas', ['uses' => 'IuranController@updateKomunitas']);
$router->post('/delete-komunitas', ['uses' => 'IuranController@deleteKomunitas']);

$router->post('/create-iuran', ['uses' => 'IuranController@createIuran']);
$router->post('/get-detail-iuran', ['uses' => 'IuranController@getDetailIuran']);
$router->post('/get-list-iuran', ['uses' => 'IuranController@getListIuran']);
$router->post('/update-iuran', ['uses' => 'IuranController@updateIuran']);
$router->post('/delete-iuran', ['uses' => 'IuranController@deleteIuran']);

$router->post('/add-admin-to-komunitas', ['uses' => 'IuranController@addAdminToKomunitas']);
$router->post('/delete-admin-from-komunitas', ['uses' => 'IuranController@deleteAdminFromKomunitas']);
$router->post('/get-all-anggota-komunitas', ['uses' => 'IuranController@getAllAnggotaKomunitas']);
$router->post('/invite-anggota', ['uses' => 'IuranController@inviteAnggota']);
$router->post('/send-message-to-anggota', ['uses' => 'IuranController@sendMessageToAnggota']);

$router->post('/set-transaksi-iuran', ['uses' => 'IuranController@setTransaksiIuran']);
$router->post('/get-transaksi-iuran-default', ['uses' => 'IuranController@getTransaksiIuranDefault']);
$router->post('/get-transaksi-iuran-by-filter', ['uses' => 'IuranController@getTransaksiIuranByFilter']);
$router->post('/confirm-transaksi-iuran', ['uses' => 'IuranController@confirmTransaksiIuran']);
$router->post('/delete-transaksi-iuran', ['uses' => 'IuranController@deleteTransaksiIuran']); 

$router->post('get-tabungan', ['uses' => 'MainController@getTabungan']);
$router->post('/set-tabungan', ['uses' => 'MainController@setTabungan']);
$router->post('/edit-tabungan', ['uses' => 'MainController@editTabungan']);

$router->post('/get-notif', ['uses' => 'MainController@getNotif']);
$router->post('/set-notif', ['uses' => 'MainController@setNotif']);
$router->post('/get-statistik', ['uses' => 'MainController@getStatistik']);

$router->post('/manual-debug', ['uses' => 'MainController@manualDebug']);

 
