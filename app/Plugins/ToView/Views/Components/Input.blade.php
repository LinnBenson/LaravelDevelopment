@php
    $title = $title ?? $slot ?? null; // 默认标题为空
    $type = $type ?? 'text'; // 默认类型为文本输入框
    $name = $name ?? ''; // 默认名称为空
    $placeholder = $placeholder ?? ''; // 默认占位符为空
    $defaultLeft = '140px'; // 默认左侧距离
    // 类型相关
    $allowedTypes = [ 'text', 'password', 'email', 'phone', 'number', 'datetime-local', 'date', 'time', 'select', 'textarea', 'code', 'color', 'switch', 'radio', 'checkbox', 'markdown', 'send', 'verify', 'agree', 'upload', 'button' ]; // 允许的类型
    if( !in_array( $type, $allowedTypes ) ) {
        $type = 'text'; // 如果类型不在允许的类型中，则默认为文本输入框
    }
    $verifyLink = $type === 'verify' ? ( $link ?? route( 'plugins.to-view.verify' ) ) : null; // 验证码请求地址
    $sendLink = $type === 'send' ? ( $link ?? null ) : null; // 发送代码请求地址
    $sendBind = $type === 'send' ? ( $bind ?? '' ) : null; // 发送代码绑定字段
    $sendMax = $type === 'send' ? (int) ( $max ?? 60 ) : 0; // 发送代码倒计时
    // 图标相关
    $defaultIcon = [ // 默认图标
        'text' => 'bi-pen',
        'password' => 'bi-shield-check',
        'email' => 'bi-at',
        'phone' => 'bi-telephone',
        'number' => 'bi-123',
        'datetime-local' => 'bi-calendar-date',
        'date' => 'bi-calendar-date',
        'time' => 'bi-clock',
        'select' => 'bi-list-task',
        'color' => 'bi-palette',
        'send' => 'bi-clock-history',
        'verify' => 'bi-exclamation-circle',
    ];
    $disableIconTypes = [ 'textarea', 'code', 'switch', 'radio', 'checkbox', 'markdown', 'agree', 'upload', 'button' ]; // 禁用图标的类型
    $icon = $icon ?? $defaultIcon[$type] ?? null; // 默认图标为空
    $icon = in_array( $type, $disableIconTypes ) ? null : $icon; // 如果类型在禁用图标的类型中，则图标为空
    // 功能相关
    $rid = 'input-'.uniqid(); // 唯一标识符
    $defaultMethod = [
        'password' => "bi-eye|$( this ).siblings( '.toview-input-box' ).find( 'input' ).attr( 'type', $( this ).hasClass( 'bi-eye' ) ? 'text' : 'password' );$( this ).toggleClass( 'bi-eye bi-eye-slash' );"
    ];
    $disableMethodTypes = [ 'textarea', 'code', 'switch', 'radio', 'checkbox', 'markdown', 'send', 'verify', 'agree', 'upload', 'button' ]; // 禁用方法的类型
    $method = $method ?? $defaultMethod[$type] ?? null; // 默认方法为空
    $method = in_array( $type, $disableMethodTypes ) ? null : $method; // 如果类型在禁用方法的类型中，则方法为空
    $method = $method ? explode( '|', $method ) : null; // 方法分割为数组
    // 值转换
    switch( $type ) {
        case 'phone':
            $phoneCodes = $options ?? [
                '+1' => '+1',
                '+7' => '+7',
                '+20' => '+20',
                '+27' => '+27',
                '+30' => '+30',
                '+31' => '+31',
                '+32' => '+32',
                '+33' => '+33',
                '+34' => '+34',
                '+36' => '+36',
                '+39' => '+39',
                '+40' => '+40',
                '+41' => '+41',
                '+43' => '+43',
                '+44' => '+44',
                '+45' => '+45',
                '+46' => '+46',
                '+47' => '+47',
                '+48' => '+48',
                '+49' => '+49',
                '+51' => '+51',
                '+52' => '+52',
                '+54' => '+54',
                '+55' => '+55',
                '+56' => '+56',
                '+57' => '+57',
                '+60' => '+60',
                '+61' => '+61',
                '+62' => '+62',
                '+63' => '+63',
                '+64' => '+64',
                '+65' => '+65',
                '+66' => '+66',
                '+81' => '+81',
                '+82' => '+82',
                '+84' => '+84',
                '+86' => '+86',
                '+90' => '+90',
                '+91' => '+91',
                '+92' => '+92',
                '+94' => '+94',
                '+98' => '+98',
                '+212' => '+212',
                '+234' => '+234',
                '+351' => '+351',
                '+852' => '+852',
                '+853' => '+853',
                '+886' => '+886',
                '+971' => '+971',
                '+972' => '+972',
            ];
            $phoneCode = $code ?? array_key_first( $phoneCodes ) ?? '+1';
            if ( isset( $value ) && preg_match( '/^(\+\d{1,4})\s*(.*)$/', trim( (string) $value ), $phoneValue ) ) {
                $phoneCode = $phoneValue[1];
                $value = $phoneValue[2];
            }
            if ( !array_key_exists( $phoneCode, $phoneCodes ) ) { $phoneCodes = [ $phoneCode => $phoneCode ] + $phoneCodes; }
            break;
        case 'datetime-local':
        case 'date':
            if( isset( $value ) && !empty( $value ) ) {
                $value = str_replace( '.', '-', $value );
                $value = str_replace( ' ', 'T', $value );
            }
            break;
        case 'switch':
        case 'agree':
            $value = isset( $value ) && !in_array( $value, [ '', 0, '0', false, 'false', 'off' ], true );
            break;
        case 'checkbox':
            $value = !isset( $value ) || $value === '' ? [] : ( is_array( $value ) ? $value : explode( '|', (string) $value ) );
            $checkboxName = str_ends_with( $name, '[]' ) ? $name : "{$name}[]";
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
    ->except([ 'title', 'icon', 'name', 'placeholder', 'value', 'left', 'method', 'step', 'min', 'max', 'exts', 'tips', 'options', 'code', 'bind', 'link' ])
->class([
    'toview-input',
    "toview-input-hasIcon" => $icon ?? false,
    "toview-input-hasMethod" => $method ?? false,
    "toview-input-hasTitle" => $title ?? false,
    "toview-input-hasLeft" => isset( $left ) || ( isset( $left ) && $left === 'auto' )
])}}>
    <div class="toview-input-title">
        @if( $type !== 'button' && $title ?? false )
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
                @case( 'phone' )
                    <div class="toview-input-phone">
                        <select phone-code aria-label="手机号区号" autocomplete="tel-country-code">
                            @foreach( $phoneCodes as $key => $option )
                                <option value="{{$key}}" {{$key == $phoneCode ? 'selected' : ''}}>{{$option}}</option>
                            @endforeach
                        </select>
                        <input
                            rid="{{$rid}}"
                            type="tel"
                            name="{{$name}}"
                            value="{{$value ?? ''}}"
                            placeholder="{{$placeholder}}"
                            autocomplete="tel-national"
                            inputmode="tel"
                            input
                        />
                    </div>
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
                    <label class="toview-input-switch">
                        <input type="hidden" name="{{$name}}" value="0" />
                        <input
                            rid="{{$rid}}"
                            type="checkbox"
                            name="{{$name}}"
                            value="1"
                            autocomplete="off"
                            {{$value ? 'checked' : ''}}
                            input
                        />
                        <span class="toview-input-switch-track">
                            <span class="toview-input-switch-handle"></span>
                        </span>
                    </label>
                    @break
                @case( 'radio' )
                    <div class="toview-input-choice-list">
                        @foreach( $options ?? [] as $key => $option )
                            <label class="toview-input-choice">
                                <input rid="{{$rid}}" type="radio" name="{{$name}}" value="{{$key}}" {{$key == $value ? 'checked' : ''}} input />
                                <span class="toview-input-choice-mark"></span>
                                <span>{{$option}}</span>
                            </label>
                        @endforeach
                    </div>
                    @break
                @case( 'checkbox' )
                    <div class="toview-input-choice-list">
                        @foreach( $options ?? [] as $key => $option )
                            <label class="toview-input-choice">
                                <input rid="{{$rid}}" type="checkbox" name="{{$checkboxName}}" value="{{$key}}" {{in_array( $key, $value ) ? 'checked' : ''}} input />
                                <span class="toview-input-choice-mark"></span>
                                <span>{{$option}}</span>
                            </label>
                        @endforeach
                    </div>
                    @break
                @case( 'agree' )
                    <label class="toview-input-agree">
                        <input
                            rid="{{$rid}}"
                            type="checkbox"
                            name="{{$name}}"
                            value="1"
                            autocomplete="off"
                            {{$value ? 'checked' : ''}}
                            input
                        />
                        <span class="toview-input-agree-mark"></span>
                        <span class="toview-input-agree-text">{{$placeholder}}</span>
                    </label>
                    @break
                @case( 'markdown' )
                    <x-View::Markdown rid="{{$rid}}" name="{{$name}}" :value="$value ?? ''" placeholder="{{$placeholder}}" />
                    @break
                @case( 'upload' )
                    <x-View::Upload
                        rid="{{$rid}}"
                        name="{{$name}}"
                        :value="$value ?? ''"
                        placeholder="{{$placeholder}}"
                        :min="$min ?? null"
                        :max="$max ?? null"
                        :exts="$exts ?? ''"
                    />
                    @break
                @case( 'send' )
                    <div class="toview-input-send" data-send="{{$rid}}">
                        <input
                            rid="{{$rid}}"
                            type="text"
                            name="{{$name}}"
                            value="{{$value ?? ''}}"
                            placeholder="{{$placeholder}}"
                            autocomplete="one-time-code"
                            inputmode="numeric"
                            input
                        />
                        <button type="button" data-send-button @disabled( $sendLink === null || $sendLink === '' )>{{__( 'base.send' )}}</button>
                    </div>
                    @break
                @case( 'verify' )
                    <div class="toview-input-verify">
                        <input
                            rid="{{$rid}}"
                            type="text"
                            name="{{$name}}"
                            value="{{$value ?? ''}}"
                            placeholder="{{$placeholder}}"
                            autocomplete="off"
                            inputmode="text"
                            input
                        />
                        <button
                            type="button"
                            class="toview-input-verify-image"
                            aria-label="{{__( 'base.refresh' )}}"
                            title="{{__( 'base.refresh' )}}"
                            onclick="const image = this.querySelector( 'img' ); image.src = image.dataset.link + ( image.dataset.link.includes( '?' ) ? '&' : '?' ) + '_=' + Date.now();"
                        >
                            <img src="{{$verifyLink}}" data-link="{{$verifyLink}}" alt="" draggable="false" />
                        </button>
                    </div>
                    @break
                @case( 'button' )
                    {{$title ?? ''}}
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

@if( $type === 'send' )
<script>
/* Send code input interactions */
(() => {
    const sendId = @json((string) $rid);
    const root = Array.from( document.querySelectorAll( '[data-send]' ) ).find( ( element ) => element.dataset.send === sendId );
    if ( !root || root.dataset.ready === 'true' ) { return; }
    root.dataset.ready = 'true';
    const button = root.querySelector( '[data-send-button]' );
    const form = root.closest( 'form' );
    const link = @json((string) $sendLink);
    const bindName = @json((string) $sendBind);
    const maxSeconds = @json($sendMax);
    const sendText = @json(__( 'base.send' ));
    let sending = false; let countdown = null;

    const boundField = () => Array.from( form?.querySelectorAll( '[name]' ) ?? [] ).find( ( field ) => field.name === bindName );
    const boundValue = ( field ) => {
        const inputBox = field.closest( 'div.toview-input' );
        if ( inputBox?.getAttribute( 'type' ) !== 'phone' ) { return field.value.trim(); }
        const code = inputBox.querySelector( '[phone-code]' )?.value.replace( /\D/g, '' ) ?? '';
        const number = field.value.replace( /\D/g, '' );
        return code && number ? `+${code} ${number}` : '';
    };
    const showRequired = ( field ) => {
        const inputBox = field?.closest( 'div.toview-input' );
        const title = inputBox?.querySelector( 'div.toview-input-title span' )?.textContent.trim() || bindName;
        Core.toast( 2, t( 'base.error.s2' ), t( 'base.error.required', { attribute: title } ) );
        inputBox?.setAttribute( 'error', '' );
        setTimeout(() => inputBox?.removeAttribute( 'error' ), 3000 );
    };
    const startCountdown = () => {
        let seconds = maxSeconds;
        if ( seconds <= 0 ) { sending = false; button.disabled = false; return; }
        button.textContent = `${seconds}s`;
        countdown = setInterval(() => {
            seconds--;
            if ( seconds > 0 ) { button.textContent = `${seconds}s`; return; }
            clearInterval( countdown ); countdown = null; sending = false; button.disabled = false; button.textContent = sendText;
        }, 1000 );
    };
    button.addEventListener( 'click', () => {
        if ( sending || countdown !== null ) { return; }
        const field = boundField();
        const value = field ? boundValue( field ) : '';
        if ( !field || value === '' ) { showRequired( field ); return; }
        sending = true; button.disabled = true;
        Core.web( link ).method( 'POST' ).data( { [bindName]: value } )
            .success( startCountdown )
            .request()
            .always(() => {
                if ( countdown !== null ) { return; }
                sending = false; button.disabled = false; button.textContent = sendText;
            });
    });
})();
</script>
@endif
