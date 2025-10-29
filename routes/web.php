<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentStatusController;






/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    
    return view('welcome');
    
   });

// Route::post('/payment/initiate', [PaymentController::class, 'initiatePayment']);
Route::get('/test-payment', [PaymentController::class, 'initiatePayment']);



Route::get('/payform', [PaymentController::class, 'getPayForm']);
Route::get('/statusform', [PaymentController::class, 'getStatusForm']);
Route::post('/res', [PaymentController::class, 'cancelResponse']);
Route::post('/payment/cancel', [PaymentController::class, 'cancelResponse']);
//Route::post('/payment/response', [PaymentController::class, 'paymentResponse']);
Route::post('/payment/response', [PaymentController::class, 'paymentResponse']);
Route::post('/payment/redirect', [PaymentController::class, 'redirectResponse']);
//Route::post('/payment/cancel', [PaymentController::class, 'cancelResponse'])->name('payment.cancel');
//Route::get('/res', 'PaymentController@res')->name('res');
//Route::post('/payform', [PaymentController::class, 'getPayForm']);



/// nttdata - atom payment services
use App\Http\Controllers\NttDataPaymentController;
Route::get('/ninitiate', [NttDataPaymentController::class, 'ninitiate']);
Route::get('/ncallback', [NttDataPaymentController::class, 'ncallback']);

/// atom payment gateway
use App\Http\Controllers\AtomPaymentController;
Route::get('/atompayment', [AtomPaymentController::class, 'createPayment'])->name('atompayment.create');
Route::post('/atompayment/atomresponse', [AtomPaymentController::class, 'handleResponse'])->name('atompayment.response');
Route::get('/atompayform', [AtomPaymentController::class, 'atomPayForm']);
Route::get('/checkstatus', [PaymentController::class, 'checkStatus']);
Route::get('/check', [PaymentStatusController::class, 'check']);