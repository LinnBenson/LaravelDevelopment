/**
 * 站点核心对象
 * 保存服务端注入的公共运行参数，并提供全站统一初始化入口。
 * @property {string} rid 当前请求的唯一标识
 * @property {string} locale 当前页面语言代码
 * @property {Function} init 站点初始化方法
 */
window['Core'] = {
    // 站点是否已初始化
    initialized: false,
    // 当前请求标识，由公共页面框架注入。
    rid: null,
    // 当前页面语言，由公共页面框架注入。
    locale: null,
    // 页面宽度 / 页面高度
    width: null, height: null,
    // 系统信息
    app: {},
    // 用户信息
    user: null,
    // 缓存数据
    cache: { lang: {} },

    /**
     * 初始化站点
     * 作为全站公共 JavaScript 的统一启动入口，在核心对象创建后执行。
     * @returns {void}
     */
    init: function() {
        // 监听窗口尺寸变化事件，更新全局宽高属性。
        const resize = () => {
            Core.width = window.innerWidth;
            Core.height = window.innerHeight;
            document.documentElement.style.setProperty( '--vw', `${Core.width}px` );
            document.documentElement.style.setProperty( '--vh', `${Core.height}px` );
        };
        resize();
        window.addEventListener( 'resize', resize );
        // 数据刷新
        const values = [ 'rid', 'locale', 'app' ];
        for ( const key of values ) {
            if ( !empty( setting[key] ) ) {
                this[key] = setting[key];
            }else if ( get( key ) ) {
                this[key] = get( key );
            }
        }
        if ( !is_uuid( this.rid ) ) { this.rid = uuid(); }
        if ( typeof this.locale !== 'string' || this.locale === '' ) { this.locale = 'en'; }
        set( 'rid', this.rid ); cookieSet( 'rid', this.rid, { path: '/' } );
        // 初始化系统信息
        this.refreshSystemInfo();
        this.cache['refreshSystemInfoInterval'] = setInterval( this.refreshSystemInfo, 10000 );
    },

    /**
     * 获取请求头
     * 返回包含当前请求标识和语言的 HTTP 请求头对象，若存在有效的 Bearer Token 则附加 Authorization。
     * @returns {object} HTTP 请求头对象
     */
    headers: function() {
        const headers = {
            'rid': this.rid,
            'locale': this.locale
        };
        const token = get( 'token' );
        if ( typeof token === 'string' && token !== '' ) { headers['Authorization'] = `Bearer ${token}`; }
        return headers;
    },

    /**
     * 刷新系统信息
     * 通过 Ajax 请求获取最新的系统信息并更新全局属性。
     * @param {string|null} langs 使用竖线分隔的语言包名称
     * @returns {void}
     */
    refreshSystemInfo: function( langs = null ) {
        if ( !Core.initialized ) {
            langs = `base${setting.langs && typeof setting.langs === 'string' && setting.langs !== '' ? `|${setting.langs}` : ''}`;
        }
        $.ajax({
            url: `/api/index${typeof langs === 'string' && langs !== '' ? `?langs=${encodeURIComponent( langs )}` : ''}`,
            method: 'GET',
            headers: Core.headers(),
            dataType: 'json',
            success: ( response ) => {
                if ( response && typeof response === 'object' && response.status === 'success' && response.data && typeof response.data === 'object' ) {
                    // 更新系统信息
                    Core.app = response.data.app || {}; set( 'app', Core.app );
                    // 更新用户信息
                    Core.user = response.data.user || null;
                    if ( Core.user && typeof Core.user === 'object' ) {
                        set( 'user', Core.user );
                    }else {
                        // 登录状态失效
                        Core.user = null; del( 'user' );
                        if ( get( 'token' ) ) {
                            del( 'token' );
                            location.reload();
                        }
                    }
                    // 更新语言包
                    if ( response.data.lang && typeof response.data.lang === 'object' ) {
                        Core.cache.lang = { ...Core.cache.lang, ...response.data.lang };
                    }
                    Core.initialized = true;
                }
            }
        });
    },
    /**
     * 显示通知消息
     * @param {number} status 通知状态，0=成功、1=消息、2=错误、3=警告
     * @param {string} title 通知标题
     * @param {string} message 通知内容
     * @param {number} timeout 显示时长，单位毫秒，0 表示不自动关闭
     * @returns {void}
     */
    toast: function( status, title = '', message = '', timeout = 8000 ) {
        const closeToast = () => {
            clearTimeout( Core.cache['ToastTimeout'] );
            const $box = $( 'div#toview-unit-box div.toview-unit-toast-box' );
            $box.addClass( 'toview-unit-toast-box-close' ).one( 'animationend', function() {
                $box.removeClass( 'active' );
                $( this ).removeClass( 'toview-unit-toast-box-close' );
            });
        }
        if ( status === false ) {
            return closeToast();
        }
        // 数据整理
        const iconMap = {
            0: [ 'r4', 'bi-check-circle' ],
            1: [ 'r3', 'bi-info-circle' ],
            2: [ 'r5', 'bi-exclamation-circle' ],
            3: [ 'r5', 'bi-shield-x' ]
        };
        const icon = iconMap[parseInt( status )] || iconMap[0];
        title = typeof title === 'string' && title !== '' ? title.trim() : '';
        message = typeof message === 'string' && message !== '' ? message.trim() : '';
        timeout = parseInt( timeout ); if ( timeout < 0 ) { timeout = 0; }
        // 填充数据
        const $box = $( 'div#toview-unit-box div.toview-unit-toast-box' );
        $box.find( 'div.toview-unit-toast-item.toview-unit-toast-icon i' ).attr( 'class', `bi block ${icon[1]}` ).css( 'color', `rgb( var( --${icon[0]} ) )` );
        $box.find( 'div.toview-unit-toast-item.toview-unit-toast-text div.toview-unit-toast-text-title' ).text( title );
        $box.find( 'div.toview-unit-toast-item.toview-unit-toast-text div.toview-unit-toast-text-content' ).text( message );
        // 显示通知
        $box.addClass( 'active' );
        $box.addClass( 'toview-unit-toast-box-open' ).one( 'animationend', function() {
            $( this ).removeClass( 'toview-unit-toast-box-open' );
        });
        clearTimeout( Core.cache['ToastTimeout'] );
        if ( timeout > 0 ) {
            Core.cache['ToastTimeout'] = setTimeout( () => {
                closeToast();
            }, timeout );
        }
    },
    /**
     * 控制全屏加载状态
     * 显示或关闭页面全屏加载遮罩，并支持在指定时间后自动关闭。
     * @param {boolean} show 是否显示加载遮罩
     * @param {number} timeout 自动关闭时间，单位毫秒，0 表示不自动关闭
     * @returns {void}
     */
    loading: function( show = true, timeout = 0 ) {
        const closeLoading = () => {
            clearTimeout( Core.cache['LoadingTimeout'] );
            const $box = $( 'div#toview-unit-box div.toview-unit-loading-box' );
            $box.removeClass( 'active' );
        }
        if ( show ) {
            const $box = $( 'div#toview-unit-box div.toview-unit-loading-box' );
            $box.addClass( 'active' );
            if ( timeout > 0 ) {
                Core.cache['LoadingTimeout'] = setTimeout( () => {
                    closeLoading();
                }, timeout );
            }
        }else {
            closeLoading();
        }
    },
    /**
     * 控制元素加载状态
     * 在指定的 jQuery 元素内部显示或关闭加载遮罩，并支持在指定时间后自动关闭。
     * @param {jQuery} $card 需要显示加载遮罩的 jQuery 元素
     * @param {boolean} show 是否显示加载遮罩
     * @param {number} timeout 自动关闭时间，单位毫秒，0 表示不自动关闭
     * @returns {void}
     */
    boxLoading: function( $card, show = true, timeout = 0 ) {
        const closeBoxLoading = () => {
            $card.find( 'div.toview-unit-loading-box' ).removeClass( 'active' );
            setTimeout(() => {
                $card.find( 'div.toview-unit-loading-box' ).remove();
                $card.css({ 'overflow': '', 'position': '' });
            }, 300 );
        }
        const code = $box = $( 'div#toview-unit-box div.toview-unit-loading-box' ).html();
        if ( show ) {
            if ( $card.find( 'div.toview-unit-loading-box' ).length === 0 ) {
                $card.css({ 'overflow': 'hidden', 'position': 'relative' });
                $card.append( `
                    <div class="toview-unit-loading-box center" style="
                        width: 100%;
                        height: 100%;
                        background-color: rgb( var( --r6 ), 0.75 );
                        position: absolute;
                        top: 0;
                        left: 0;
                    ">${code}</div>
                ` );
            }
            setTimeout(() => {
                $card.find( 'div.toview-unit-loading-box' ).addClass( 'active' );
            }, 10 );
            if ( timeout > 0 ) {
                setTimeout( () => {
                    closeBoxLoading();
                }, timeout );
            }
        }else {
            closeBoxLoading();
        }
    }
};

/**
 * 获取翻译文本
 * 使用点号键从已加载的语言包读取文本，并替换 Laravel 风格的占位符。
 * @param {string} key 翻译键名，例如 base.save 或 validation.required
 * @param {object} replacements 占位符替换参数
 * @returns {string} 翻译文本，无法获取时返回原键名
 */
function t( key, replacements = {} ) {
    if ( typeof key !== 'string' || key === '' ) { return ''; }
    if ( Object.prototype.toString.call( replacements ) !== '[object Object]' ) { replacements = {}; }
    const lang = Core.cache.lang;
    if ( !lang || Object.prototype.toString.call( lang ) !== '[object Object]' ) { return key; }
    const segments = key.split( '.' );
    if ( segments.some( ( segment ) => segment === '' || ['__proto__', 'prototype', 'constructor'].includes( segment ) ) ) {
        return key;
    }
    let translation = lang;
    for ( const segment of segments ) {
        if ( translation === null || typeof translation !== 'object' || !Object.prototype.hasOwnProperty.call( translation, segment ) ) {
            return key;
        }
        translation = translation[segment];
    }
    if ( typeof translation !== 'string' ) { return key; }
    for ( const [name, value] of Object.entries( replacements ) ) {
        if ( name === '' || !['string', 'number', 'boolean', 'bigint'].includes( typeof value ) ) { continue; }
        const content = String( value );
        const upperName = name.toUpperCase();
        const titleName = name.charAt( 0 ).toUpperCase() + name.slice( 1 );
        translation = translation.split( `:${upperName}` ).join( content.toUpperCase() );
        translation = translation.split( `:${titleName}` ).join( content.charAt( 0 ).toUpperCase() + content.slice( 1 ) );
        translation = translation.split( `:${name}` ).join( content );
    }
    return translation;
}

/**
 * 判断字符串是否为 JSON
 * 判断传入值是否为合法的 JSON 对象或 JSON 数组字符串。
 * @param {unknown} value 待判断的值
 * @returns {boolean} 是否为合法 JSON 字符串
 */
function is_json( value ) {
    if ( typeof value !== 'string' ) { return false; }
    const content = value.trim();
    if ( content === '' || !['{', '['].includes( content[0] ) ) { return false; }
    try {
        JSON.parse( content );
        return true;
    }catch ( error ) {
        return false;
    }
}

/**
 * 判断值是否为空
 * 支持空字符串、零值、布尔值、数组、普通对象、Map 及 Set。
 * @param {unknown} value 待判断的值
 * @returns {boolean} 是否为空
 */
function empty( value ) {
    if ( value === undefined || value === null || value === false ) { return true; }
    if ( value === 0 || value === 0n || value === '' || value === '0' ) { return true; }
    if ( Array.isArray( value ) ) { return value.length === 0; }
    if ( value instanceof Map || value instanceof Set ) { return value.size === 0; }
    if ( Object.prototype.toString.call( value ) === '[object Object]' ) {
        return Object.keys( value ).length === 0;
    }
    return false;
}

/**
 * 生成 UUID
 * 优先使用浏览器原生 UUID 方法，否则生成符合 RFC 4122 格式的 UUID v4。
 * @returns {string} UUID 字符串
 */
function uuid() {
    if ( typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function' ) {
        return crypto.randomUUID();
    }
    const bytes = new Uint8Array( 16 );
    if ( typeof crypto !== 'undefined' && typeof crypto.getRandomValues === 'function' ) {
        crypto.getRandomValues( bytes );
    }else {
        for ( let index = 0; index < bytes.length; index++ ) {
            bytes[index] = Math.floor( Math.random() * 256 );
        }
    }
    bytes[6] = ( bytes[6] & 0x0f ) | 0x40;
    bytes[8] = ( bytes[8] & 0x3f ) | 0x80;
    const value = Array.from( bytes, ( byte ) => byte.toString( 16 ).padStart( 2, '0' ) ).join( '' );
    return `${value.slice( 0, 8 )}-${value.slice( 8, 12 )}-${value.slice( 12, 16 )}-${value.slice( 16, 20 )}-${value.slice( 20 )}`;
}

/**
 * 判断字符串是否为 UUID
 * 判断传入值是否符合标准的 8-4-4-4-12 UUID 格式。
 * @param {unknown} value 待判断的值
 * @returns {boolean} 是否为 UUID 字符串
 */
function is_uuid( value ) {
    if ( typeof value !== 'string' ) { return false; }
    return /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i.test( value );
}

/**
 * 获取本地存储数据
 * 读取并还原通过 set 保存的数据，存储不可用或键不存在时返回默认值。
 * @param {string} key 存储键名
 * @param {unknown} defaultValue 键不存在时返回的默认值
 * @returns {unknown} 已保存的数据或默认值
 */
function get( key, defaultValue = null ) {
    if ( typeof key !== 'string' || key === '' ) { return defaultValue; }
    let value = null;
    try {
        value = localStorage.getItem( key );
    }catch ( error ) {
        return defaultValue;
    }
    if ( value === null ) { return defaultValue; }
    const prefix = '__core_json__:';
    if ( value.startsWith( prefix ) ) {
        try {
            return JSON.parse( value.slice( prefix.length ) );
        }catch ( error ) {
            return defaultValue;
        }
    }
    // 兼容此前直接保存的 JSON 对象和数组。
    if ( is_json( value ) ) {
        try {
            return JSON.parse( value );
        }catch ( error ) {
            return defaultValue;
        }
    }
    return value;
}

/**
 * 保存本地存储数据
 * 字符串保持原值，其他可序列化值使用带标识的 JSON 保存。
 * @param {string} key 存储键名
 * @param {unknown} value 待保存的数据
 * @returns {boolean} 是否成功写入持久化存储
 */
function set( key, value ) {
    if ( typeof key !== 'string' || key === '' ) { return false; }
    if ( ['undefined', 'function', 'symbol'].includes( typeof value ) ) { return false; }
    let storageValue = value;
    try {
        if ( typeof value !== 'string' ) {
            storageValue = `__core_json__:${JSON.stringify( value )}`;
        }
    }catch ( error ) {
        return false;
    }
    try {
        localStorage.setItem( key, storageValue );
        return true;
    }catch ( error ) {
        return false;
    }
}

/**
 * 删除本地存储数据
 * 根据键名清除对应的持久化存储数据。
 * @param {string} key 存储键名
 * @returns {boolean} 是否成功从持久化存储删除
 */
function del( key ) {
    if ( typeof key !== 'string' || key === '' ) { return false; }
    try {
        localStorage.removeItem( key );
        return true;
    }catch ( error ) {
        return false;
    }
}

/**
 * 获取 Cookie 数据
 * 读取并还原通过 cookieSet 保存的数据，Cookie 不存在或读取失败时返回默认值。
 * @param {string} key Cookie 键名
 * @param {unknown} defaultValue Cookie 不存在时返回的默认值
 * @returns {unknown} 已保存的数据或默认值
 */
function cookieGet( key, defaultValue = null ) {
    if ( typeof key !== 'string' || key === '' ) { return defaultValue; }
    try {
        const value = $.cookie( key );
        if ( value === undefined ) { return defaultValue; }
        const prefix = '__core_json__:';
        if ( !value.startsWith( prefix ) ) { return value; }
        try {
            return JSON.parse( value.slice( prefix.length ) );
        }catch ( error ) {
            return defaultValue;
        }
    }catch ( error ) {
        return defaultValue;
    }
}

/**
 * 保存 Cookie 数据
 * 字符串保持原值，其他可序列化值使用带标识的 JSON 保存，默认作用于整个站点。
 * @param {string} key Cookie 键名
 * @param {unknown} value 待保存的数据
 * @param {object} options Cookie 选项，支持 expires、path、domain 和 secure
 * @returns {boolean} 是否成功写入 Cookie
 */
function cookieSet( key, value, options = {} ) {
    if ( typeof key !== 'string' || key === '' ) { return false; }
    if ( ['undefined', 'function', 'symbol'].includes( typeof value ) ) { return false; }
    if ( Object.prototype.toString.call( options ) !== '[object Object]' ) { return false; }
    let cookieValue = value;
    try {
        if ( typeof value !== 'string' ) {
            cookieValue = `__core_json__:${JSON.stringify( value )}`;
        }
        const cookieOptions = Object.assign( { path: '/' }, options );
        $.cookie( key, cookieValue, cookieOptions );
        return $.cookie( key ) === cookieValue;
    }catch ( error ) {
        return false;
    }
}

/**
 * 删除 Cookie 数据
 * 使用与写入时相同的 path 和 domain 删除 Cookie，默认作用路径为整个站点。
 * @param {string} key Cookie 键名
 * @param {object} options Cookie 选项，支持 path 和 domain
 * @returns {boolean} 是否成功删除 Cookie
 */
function cookieDel( key, options = {} ) {
    if ( typeof key !== 'string' || key === '' ) { return false; }
    if ( Object.prototype.toString.call( options ) !== '[object Object]' ) { return false; }
    try {
        const cookieOptions = Object.assign( { path: '/' }, options );
        if ( $.cookie( key ) === undefined ) { return true; }
        return $.removeCookie( key, cookieOptions );
    }catch ( error ) {
        return false;
    }
}

// 页面脚本加载完成后执行站点初始化。
Core.init();
