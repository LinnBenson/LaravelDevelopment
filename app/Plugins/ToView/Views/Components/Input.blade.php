@php
    $title = $title ?? $slot ?? null; // 默认标题为空
    $type = $type ?? 'text'; // 默认类型为文本输入框
    $name = $name ?? ''; // 默认名称为空
    $placeholder = $placeholder ?? ''; // 默认占位符为空
    $defaultLeft = '140px'; // 默认左侧距离
    // 类型相关
    $allowedTypes = [ 'text', 'password', 'email', 'number', 'datetime-local', 'date', 'time', 'select', 'textarea', 'code', 'color', 'switch' ]; // 允许的类型
    if( !in_array( $type, $allowedTypes ) ) {
        $type = 'text'; // 如果类型不在允许的类型中，则默认为文本输入框
    }
    // 图标相关
    $defaultIcon = [ // 默认图标
        'text' => 'bi-pen',
        'password' => 'bi-shield-check',
        'email' => 'bi-at',
        'number' => 'bi-123',
        'datetime-local' => 'bi-calendar-date',
        'date' => 'bi-calendar-date',
        'time' => 'bi-clock',
        'select' => 'bi-list-task',
        'color' => 'bi-palette',
    ];
    $disableIconTypes = [ 'textarea', 'code', 'switch' ]; // 禁用图标的类型
    $icon = $icon ?? $defaultIcon[$type] ?? null; // 默认图标为空
    $icon = in_array( $type, $disableIconTypes ) ? null : $icon; // 如果类型在禁用图标的类型中，则图标为空
    // 功能相关
    $rid = 'input-'.uniqid(); // 唯一标识符
    $defaultMethod = [
        'password' => "bi-eye|$( this ).siblings( '.toview-input-box' ).find( 'input' ).attr( 'type', $( this ).hasClass( 'bi-eye' ) ? 'text' : 'password' );$( this ).toggleClass( 'bi-eye bi-eye-slash' );"
    ];
    $disableMethodTypes = [ 'textarea', 'code', 'switch' ]; // 禁用方法的类型
    $method = $method ?? $defaultMethod[$type] ?? null; // 默认方法为空
    $method = in_array( $type, $disableMethodTypes ) ? null : $method; // 如果类型在禁用方法的类型中，则方法为空
    $method = $method ? explode( '|', $method ) : null; // 方法分割为数组
    // 值转换
    switch( $type ) {
        case 'datetime-local':
        case 'date':
            if( isset( $value ) && !empty( $value ) ) {
                $value = str_replace( '.', '-', $value );
                $value = str_replace( ' ', 'T', $value );
            }
            break;
        case 'switch':
            $value = isset( $value ) && !in_array( $value, [ '', 0, '0', false, 'false', 'off' ], true );
            break;
    }
    $value = !isset( $value ) ? '' : $value; // 默认值为空
@endphp

<div {{$attributes
    ->merge([
        'rid' => $rid,
        'type' => $type,
        'style' => "--left: ".( isset( $left ) && $left === 'auto' ? $defaultLeft : ( $left ?? $defaultLeft ) ).";",
    ])
    ->except([ 'title', 'icon', 'name', 'placeholder', 'value', 'left', 'method', 'step', 'min', 'max', 'tips', 'options' ])
->class([
    'toview-input',
    "toview-input-hasIcon" => $icon ?? false,
    "toview-input-hasMethod" => $method ?? false,
    "toview-input-hasTitle" => $title ?? false,
    "toview-input-hasLeft" => isset( $left ) || ( isset( $left ) && $left === 'auto' )
])}}>
    <div class="toview-input-title">
        @if( $title ?? false )
            <span class="more">{{$title}}</span>
        @endif
    </div>
    <div class="toview-input-content">
        @if( $icon ?? false )
            <i class="toview-input-icon {{$icon}} block"></i>
        @endif
        <div class="toview-input-box">
            @switch( $type )
                @case( 'select' )
                    <select
                        rid="{{$rid}}"
                        type="{{$type}}"
                        name="{{$name}}"
                        placeholder="{{$placeholder}}"
                        autocomplete="off"
                        step="{{$step ?? null}}"
                        min="{{$min ?? null}}"
                        max="{{$max ?? null}}"
                        input
                    >
                        @foreach( $options ?? [] as $key => $option )
                            <option value="{{$key}}" {{$key == $value ? 'selected' : ''}}>{{$option}}</option>
                        @endforeach
                    </select>
                    @break
                @case( 'textarea' )
                @case( 'code' )
                    <textarea
                        rid="{{$rid}}"
                        type="{{$type}}"
                        name="{{$name}}"
                        placeholder="{{$placeholder}}"
                        autocomplete="off"
                        step="{{$step ?? null}}"
                        min="{{$min ?? null}}"
                        max="{{$max ?? null}}"
                        input
                    >{{$value ?? ''}}</textarea>
                    @break
                @case( 'switch' )
                    @break
                @default
                    <input
                        rid="{{$rid}}"
                        type="{{$type}}"
                        name="{{$name}}"
                        value="{{$value ?? ''}}"
                        placeholder="{{$placeholder}}"
                        autocomplete="off"
                        step="{{$step ?? null}}"
                        min="{{$min ?? null}}"
                        max="{{$max ?? null}}"
                    input />
                    @break
            @endswitch
            @if( $tips ?? false )
                <ul class="toview-input-tips">
                    @foreach( $tips as $tip )
                        <li>{{$tip}}</li>
                    @endforeach
                </ul>
            @endif
        </div>
        @if( $method ?? false )
            <i class="toview-input-method {{$method[0] ?? ''}} block" onclick="{{$method[1] ?? ''}}"></i>
        @endif
    </div>
</div>