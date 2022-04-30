<?php

use App\Http\Controllers\Api\V1\ClientController;
use App\Http\Controllers\Api\V1\TransactionController;
use App\Http\Controllers\Api\V1\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*Client V1*/
Route::group(["prefix" => "client", "middleware" => ["api-epayco"]], function(){

    Route::get("getClient/{id?}", [ClientController::class, "getClient"]);
    Route::post("clientRegistration", [ClientController::class, "clientRegistration"]);

});

/*Wallet V1*/
Route::group(["prefix" => "wallet", "middleware" => ["api-epayco"]], function(){

    Route::post("createWallet", [WalletController::class, "createWallet"]);
    Route::post("rechargeWallet", [WalletController::class, "rechargeWallet"]);
    Route::post("checkBalance", [WalletController::class, "checkBalance"]);


});

/*Transaction V1*/
Route::group(["prefix" => "transaction", "middleware" => ["api-epayco"]], function(){

    Route::post("payment", [TransactionController::class, "payment"]);
    Route::post("paymentConfirm", [TransactionController::class, "paymentConfirm"]);

});
