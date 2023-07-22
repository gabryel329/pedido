<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FormaPagamentoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'v1'], function () {
    Route::get('/', function () {
        return 'API Em Funcionamento!!';
    });
    Route::post('login', [AuthController::class, 'login']);
    Route::group(['middleware' => ['apiJwt']], function () {
        //USER
        Route::prefix('users')->group(function () {
            Route::get('/index', [UserController::class, 'index']);
            Route::post('/store', [UserController::class ,'store']);
            Route::put('/update/{id}', [UserController::class, 'update']);
            Route::get('/findById/{id}', [UserController::class, 'findById']);
            Route::delete('/destroy/{id}', [UserController::class, 'destroy']);
        });

        //PRODUTO
        Route::prefix('produtos')->group(function () {
            Route::get('/index', [ProdutoController::class, 'index']);
            Route::post('/store', [ProdutoController::class ,'store']);
            Route::put('/update/{id}', [ProdutoController::class, 'update']);
            Route::get('/findById/{id}', [ProdutoController::class, 'findById']);
            Route::delete('/destroy/{id}', [ProdutoController::class, 'destroy']);
        });

        //PEDIDO
        Route::prefix('pedidos')->group(function () {
            Route::get('/index', [PedidoController::class, 'index']);
            Route::post('/store', [PedidoController::class ,'store']);
            Route::put('/update/{id}', [PedidoController::class, 'update']);
            Route::get('/findById/{id}', [PedidoController::class, 'findById']);
            Route::delete('/destroy/{id}', [PedidoController::class, 'destroy']);
        });

        //STATUS
        Route::prefix('status')->group(function () {
            Route::get('/index', [StatusController::class, 'index']);
            Route::post('/store', [StatusController::class ,'store']);
            Route::put('/update/{id}', [StatusController::class, 'update']);
            Route::get('/findById/{id}', [StatusController::class, 'findById']);
            Route::delete('/destroy/{id}', [StatusController::class, 'destroy']);
        });

        //FORMA PAGAMENTO
        Route::prefix('formaPagamento')->group(function () {
            Route::get('/index', [FormaPagamentoController::class, 'index']);
            Route::post('/store', [FormaPagamentoController::class ,'store']);
            Route::put('/update/{id}', [FormaPagamentoController::class, 'update']);
            Route::get('/findById/{id}', [FormaPagamentoController::class, 'findById']);
            Route::delete('/destroy/{id}', [FormaPagamentoController::class, 'destroy']);
        });
    });
});
