<?php

use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

// 获取系统基础信息
Route::get( '/index', [IndexController::class, 'index'] )->name( 'api.index' );
