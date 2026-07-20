<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cookie;

/**
 * SetRequestMiddleware
 * 设置应用语言中间件。
 * @package App\Http\Middleware
 */
class SetRequestMiddleware {
    /**
     * 处理请求。
     * 根据请求头、Cookie 或 Accept-Language 自动设置应用语言。
     * @param Request $request 当前请求
     * @param Closure $next 后续处理
     * @return Response 响应
     */
    public function handle( Request $request, Closure $next ): Response {
        // 设置请求的唯一标识符（RID）和应用语言
        $this->setRid( $request );
        $this->setLocale( $request );
        // 继续处理请求
        return $next( $request );
    }
    /**
     * 设置请求的唯一标识符（RID）。
     * 优先使用请求头中的 RID 参数，其次使用 Cookie 中的 RID 参数，若都不存在则生成新的 RID。
     * @param Request $request 当前请求
     * @return bool 是否成功设置 RID
     */
    private function setRid( Request $request ): bool {
        $headerRid = $request->header( 'rid', '' );
        if ( is_string( $headerRid ) && is_uuid( $headerRid ) ) {
            Cookie::queue( Cookie::forever( 'rid', $headerRid ) );
            $request->attributes->set( 'rid', $headerRid );
            return true;
        }
        $cookieRid = $request->cookie( 'rid', '' );
        if ( is_string( $cookieRid ) && is_uuid( $cookieRid ) ) {
            $request->attributes->set( 'rid', $cookieRid );
            return true;
        }
        $rid = uuid();
        Cookie::queue( Cookie::forever( 'rid', $rid ) );
        $request->attributes->set( 'rid', $rid );
        return true;
    }
    /**
     * 设置应用语言。
     * 优先使用请求头中的 Locale 参数，其次使用 Cookie 中的 locale 参数，若都不存在则使用 Accept-Language 自动设置，最后使用应用默认语言。
     * @param Request $request 当前请求
     * @return void
     */
    private function setLocale( Request $request ) {
        // 使用请求头中的 Locale 参数设置语言
        $headerLocale = $request->header( 'Locale' );
        if ( is_string( $headerLocale ) ) {
            $headerLocale = str_replace( '-', '_', $headerLocale );
            if ( is_string( $headerLocale ) && array_key_exists( $headerLocale, config( 'app.locales', [] ) ) ) {
                return app()->setLocale( $headerLocale );
            }
        }
        // 使用 Cookie 中的 locale 参数设置语言
        $cookieLocale = $request->cookie( 'locale' );
        if ( is_string( $cookieLocale ) ) {
            $cookieLocale = str_replace( '-', '_', $cookieLocale );
            if ( is_string( $cookieLocale ) && array_key_exists( $cookieLocale, config( 'app.locales', [] ) ) ) {
                return app()->setLocale( $cookieLocale );
            }
        }
        // 使用请求头中的 Accept-Language 参数设置语言
        $autoLocale = explode( ',', $request->header( 'Accept-Language', '' ) )[0] ?? null;
        if ( is_string( $autoLocale ) ) {
            $autoLocale = str_replace( '-', '_', $autoLocale );
            if ( is_string( $autoLocale ) && array_key_exists( $autoLocale, config( 'app.locales', [] ) ) ) {
                return app()->setLocale( $autoLocale );
            }
        }
        // 使用应用默认语言设置语言
        return app()->setLocale( config( 'app.locale' ) );
    }
}