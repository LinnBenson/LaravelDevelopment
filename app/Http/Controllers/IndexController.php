<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller {
    /**
     * 首页数据接口
     * 返回首页所需的应用信息。
     * @return \Illuminate\Http\JsonResponse JSON 响应
     */
    public function index(): \Illuminate\Http\JsonResponse {
        return echoJson( 0, [
            'app' => [
                'title' => setting( 'app.title' ),
                'debug' => setting( 'app.debug' ),
                'host' => setting( 'app.host' ),
                'icon' => setting( 'app.icon' ),
                'copyright' => setting( 'app.copyright' ),
            ]
        ]);
    }
    /**
     * 首页
     * 显示网站首页。
     * @return \Illuminate\View\View
     */
    public function view(): \Illuminate\View\View {
        return view( 'Test' );
    }
    /**
     * 调试
     * 用于调试的测试方法。
     * @return any
     */
    public function debug() {
        print_r( plugin( 'Test' ) );
        exit();
        return '';
    }
}
