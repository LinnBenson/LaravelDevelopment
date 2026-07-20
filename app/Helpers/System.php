<?php

use Illuminate\Support\Facades\Lang;

if ( !function_exists( 'setting' ) ) {
    /**
     * 读取系统配置
     * 根据键名读取 system_config 表中的配置值，并按配置类型转换数据。
     * @param string|null $key 配置键名，为 null 时返回全部配置
     * @param mixed $default 配置不存在时的默认值
     * @return mixed 配置值、全部配置或默认值
     */
    function setting( ?string $key = null, mixed $default = null ): mixed {
        static $settings = null;
        if ( $settings === null ) {
            $settings = App\Models\SystemConfig::query()
                ->get( ['key', 'type', 'value'] )
                ->mapWithKeys( function ( App\Models\SystemConfig $config ): array {
                    $value = $config->value;
                    if ( $config->type === 'boolean' ) {
                        $value = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
                    }elseif ( $config->type === 'number' ) {
                        $value = $value === null ? null : (int) $value;
                    }elseif ( $config->type === 'decimal' ) {
                        $value = $value === null ? null : (float) $value;
                    }elseif ( $config->type === 'json' ) {
                        $value = $value === null ? null : json_decode( $value, true );
                    }
                    return [$config->key => $value];
                } )
                ->all();
        }
        if ( $key === null ) { return $settings; }
        return array_key_exists( $key, $settings ) ? $settings[$key] : value( $default );
    }
}
if ( !function_exists( 'echoJson' ) ) {
    /**
     * 输出 JSON 响应
     * 根据状态码和数据输出标准化的 JSON 响应。
     * @param bool|int $status 状态码，布尔值表示成功或失败，整数表示自定义状态码
     * @param mixed $data 响应数据
     * @param int|null $code HTTP 状态码，为 null 时根据响应状态自动生成
     * @param array<string, string> $headers HTTP 响应头
     * @return \Illuminate\Http\JsonResponse|string JSON 响应对象或字符串
     */
    function echoJson( bool|int $status, mixed $data, ?int $code = null, array $headers = [] ): \Illuminate\Http\JsonResponse|string {
        if ( is_bool( $status ) ) {
            if ( is_array( $data ) && count( $data ) === 1 && isset( $data[0] ) && is_string( $data[0] ) && Lang::has( $data[0] ) ) {
                $dataStatus = $status ? 'base.true' : 'base.false';
                $data = __( $data[0] ).__( $dataStatus );
            }
            if ( $code === null ) { $code = $status ? 200 : 400; }
            $status = $status ? 0 : 2;
        }
        $statusMap = [
            0 => 'success',
            1 => 'info',
            2 => 'error',
            3 => 'warning',
        ];
        $statusText = $statusMap[$status] ?? 'unknown';
        if (
            is_array( $data ) &&
            array_is_list( $data ) &&
            isset( $data[0] ) &&
            is_string( $data[0] ) &&
            ( ! isset( $data[1] ) || is_array( $data[1] ) ) &&
            count( $data ) <= 2 &&
            Lang::has( $data[0] )
        ) {
            $data = __( $data[0], $data[1] ?? [] );
        }
        if ( $code === null ) { $code = 200; }
        $data = [
            'status' => $statusText,
            'code' => $code,
            'time' => time(),
            'data' => $data,
        ];
        $response = response()->json( $data, $code, $headers, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES |JSON_INVALID_UTF8_SUBSTITUTE );
        if ( PHP_SAPI === 'cli' ) { return (string) $response->getContent(); }
        return $response;
    }
}