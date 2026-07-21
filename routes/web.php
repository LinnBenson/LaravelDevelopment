<?php

use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

// 主页面
Route::get( '/', [IndexController::class, 'view'] )->name( 'view_index' );
// 调试页面
Route::any( '/debug', [IndexController::class, 'debug'] )->name( 'view_debug' );
