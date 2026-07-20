<?php

use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

// 主页面
Route::get( '/', [IndexController::class, 'view'] );
// 调试页面
Route::any( '/debug', [IndexController::class, 'debug'] );
