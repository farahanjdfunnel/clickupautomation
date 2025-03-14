<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AutoAuthController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CustomPaymentProviderController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\SPInController;
use App\Http\Controllers\Admin\SpinAdminController as AdminSpinController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClickUpWebhookController;

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

Auth::routes();
Route::get(
    '/custom-page',
    function () {
        return view('crm-custom-page');
    }
);

Route::post('decrypt-sso', [App\Http\Controllers\SettingController::class, 'decryptSSO'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::get('/', [App\Http\Controllers\HomeController::class, 'root'])->name('root');

//Update User Details

Route::post('/update-profile/{id}', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('updateProfile');

Route::post('/update-password/{id}', [App\Http\Controllers\HomeController::class, 'updatePassword'])->name('updatePassword');

//Language Translation
Route::get('index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);

Route::group(['as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['admin', 'auth']], function () {
    Route::post('/update-profile', [AdminController::class, 'update'])->name('update.profile');
    Route::get('setting', [SettingController::class, 'index'])->name('setting');
    Route::post('/setting/save', [SettingController::class, 'store'])->name('setting.save');

    Route::group(['as' => 'merchant.', 'prefix' => 'merchant'], function () {
        Route::get('index', [MerchantController::class, 'index'])->name('index');
        Route::post('/get-table-data', [MerchantController::class, 'getTableData'])->name('table-data');
        Route::group(['as' => 'spin.', 'prefix' => 'spin'], function () {
            Route::get('/', [AdminSpinController::class, 'index'])->name('index');
            Route::post('/update', [AdminSpinController::class, 'update'])->name('update');
            Route::post('/store', [AdminSpinController::class, 'store'])->name('store');
            Route::delete('/destroy/{id}', [AdminSpinController::class, 'destroy'])->name('destroy');
            Route::post('/get-table-data', [AdminSpinController::class, 'getTableData'])->name('table-data');
        });
        Route::group(['as' => 'payment-provider.', 'prefix' => 'payment-provider'], function () {
            Route::post('setup-hpp', [MerchantController::class, 'setupHpp'])->name('setup-hpp');
            Route::post('setup-crm-provider', [MerchantController::class, 'setupCRMProvider'])->name('crm');
        });
    });
});

Route::group(['as' => 'location.', 'prefix' => 'location', 'middleware' => ['location', 'auth']], function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');


    Route::post('/update-profile', [AdminController::class, 'update'])->name('update.profile');
    Route::get('setting', [SettingController::class, 'index'])->name('setting');
    Route::post('/setting/save', [SettingController::class, 'store'])->name('setting.save');

    Route::group(['as' => 'SPIn.', 'prefix' => 'spin'], function () {
        Route::get('/', [SPInController::class, 'index'])->name('index');
        Route::post('/update', [SPInController::class, 'update'])->name('update');
        Route::post('/store', [SPInController::class, 'store'])->name('store');
        Route::delete('/destroy/{id}', [SPInController::class, 'destroy'])->name('destroy');
        Route::post('/get-table-data', [SPInController::class, 'getTableData'])->name('table-data');
    });
});

Route::group(['as' => 'crm.payment.provider.', 'prefix' => 'crm-payment-provider'], function () {
    Route::any('paymenturl',  [CustomPaymentProviderController::class, 'paymentProdivderPaymentUrl'])->name('payment_url');
    Route::any('crm-payment-provider-queryurl', [CustomPaymentProviderController::class, 'paymentProdivderQueryUrl'])->name('query_url')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
});


Route::group(
    ['as' => 'crm.payment.ipospays.', 'prefix' => 'crm-payment-ipospays'],
    function () {
        Route::get('get-spin-terminals',  [CustomPaymentProviderController::class, 'getSpinTerminal'])->name('fetch-spin-terminals');
        Route::get('spin-terminal-status',  [CustomPaymentProviderController::class, 'spinTerminalStatus'])->name('spin-terminal-status');
        Route::post('get-external-payment-transaction-url',  [CustomPaymentProviderController::class, 'HPPExternalPaymentTransactionURL'])->name('HPP_payment_url');
        Route::post('hpp-post-response',  [CustomPaymentProviderController::class, 'hppPostResponse'])->name('post-response')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
        Route::any('hpp-return-url',  [CustomPaymentProviderController::class, 'hppReturnTrigger'])->name('return-url');
        Route::post('spin-submit',  [CustomPaymentProviderController::class, 'spinTrigger'])->name('spin-submit')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    }
);

Route::get('check/auth', [AutoAuthController::class, 'connect'])->name('auth.check');

Route::get('check/auth/error', [AutoAuthController::class, 'authError'])->name('error');

Route::get('checking/auth', [AutoAuthController::class, 'authChecking'])->name('admin.auth.checking');

Route::prefix('authorization')->name('crm.')->group(function () {
    Route::get('/{provider}/oauth/callback', [OAuthController::class, 'callback'])->name('oauth_callback');
});
Route::get('/auth/google', [OAuthController::class, 'redirectToClickUp'])->name('auth.clickup');
Route::post('/auth/clickup/disconnect', [OAuthController::class, 'disconnect'])
    ->name('auth.clickup.disconnect')
    ->middleware('auth');

    Route::post('/clickup/webhook', [ClickUpWebhookController::class, 'handleWebhook'])->name('clickup.webhook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);