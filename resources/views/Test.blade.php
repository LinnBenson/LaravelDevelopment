@extends( 'Frame' )
@section( 'title', '登录' )

@push( 'head' )
    <style>
        /* 登录页面整体布局 */
        .login-page {
            position: relative;
            display: flex;
            min-height: 100vh;
            padding: 24px;
            overflow: hidden;
            box-sizing: border-box;
            align-items: center;
            justify-content: center;
        }
        .login-page::before,
        .login-page::after {
            content: '';
            position: absolute;
            width: 360px;
            height: 360px;
            border-radius: 50%;
            opacity: 0.16;
            filter: blur(4px);
            background: rgb( var( --r2 ) );
            pointer-events: none;
        }
        .login-page::before {
            top: -190px;
            right: -120px;
        }
        .login-page::after {
            bottom: -220px;
            left: -150px;
            background: rgb( var( --r4 ) );
        }

        /* 登录卡片 */
        .login-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            padding: 42px;
            border: 1px solid rgb( var( --r1 ), 0.12 );
            border-radius: calc( var( --radius ) * 4 );
            box-sizing: border-box;
            background: rgb( var( --r6 ) );
            box-shadow: 0 28px 70px rgb( var( --r1 ), 0.14 );
        }
        .login-card-brand {
            display: flex;
            width: 52px;
            height: 52px;
            margin-bottom: 28px;
            border-radius: calc( var( --radius ) * 3 );
            background: rgb( var( --r2 ) );
            color: rgb( var( --r2c ) );
            align-items: center;
            justify-content: center;
        }
        .login-card-brand svg {
            width: 27px;
            height: 27px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
        .login-card-heading {
            margin-bottom: 30px;
        }
        .login-card-heading h1 {
            margin-bottom: 8px;
            color: rgb( var( --r1 ) );
            letter-spacing: -0.03em;
        }
        .login-card-heading p {
            color: rgb( var( --r1 ), 0.6 );
            line-height: 1.6;
        }

        /* 登录表单 */
        .login-form {
            display: grid;
            gap: 20px;
        }
        .login-form-field {
            display: grid;
            gap: 8px;
        }
        .login-form-field > span {
            font-size: 13px;
            font-weight: bold;
        }
        .login-form-input {
            display: flex;
            min-height: 48px;
            overflow: hidden;
            border: 1px solid rgb( var( --r1 ), 0.18 );
            border-radius: calc( var( --radius ) * 2 );
            background: rgb( var( --r0 ), 0.45 );
            align-items: stretch;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
        }
        .login-form-input:focus-within {
            border-color: rgb( var( --r2 ) );
            background: rgb( var( --r6 ) );
            box-shadow: 0 0 0 3px rgb( var( --r2 ), 0.14 );
        }
        .login-form-input > svg {
            width: 19px;
            margin: 0 14px;
            color: rgb( var( --r1 ), 0.5 );
            fill: none;
            stroke: currentColor;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
        .login-form-input input {
            min-width: 0;
            border: 0;
            background: transparent;
            color: rgb( var( --r1 ) );
            flex: 1;
        }
        .login-form-input input::placeholder {
            color: rgb( var( --r1 ), 0.42 );
        }
        .login-form-password-toggle {
            display: flex;
            width: 48px;
            border: 0;
            background: transparent;
            color: rgb( var( --r1 ), 0.5 );
            cursor: pointer;
            align-items: center;
            justify-content: center;
        }
        .login-form-password-toggle:hover {
            color: rgb( var( --r2 ) );
        }
        .login-form-password-toggle svg {
            width: 20px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
        .login-form-options {
            display: flex;
            margin-top: -2px;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .login-form-remember {
            display: flex;
            color: rgb( var( --r1 ), 0.72 );
            font-size: 13px;
            cursor: pointer;
            align-items: center;
            gap: 8px;
        }
        .login-form-remember input {
            width: 16px;
            height: 16px;
            accent-color: rgb( var( --r2 ) );
        }
        .login-form-submit {
            min-height: 48px;
            border: 0;
            border-radius: calc( var( --radius ) * 2 );
            background: rgb( var( --r2 ) );
            color: rgb( var( --r2c ) );
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: opacity 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
        }
        .login-form-submit:hover {
            opacity: 0.92;
            transform: translateY( -1px );
            box-shadow: 0 10px 24px rgb( var( --r2 ), 0.24 );
        }
        .login-form-submit:active {
            transform: translateY( 0 );
        }
        .login-card-footer {
            margin-top: 28px;
            color: rgb( var( --r1 ), 0.42 );
            font-size: 12px;
            text-align: center;
        }
        @media ( max-width: 520px ) {
            .login-page {
                padding: 14px;
            }
            .login-card {
                padding: 32px 24px;
                border-radius: calc( var( --radius ) * 3 );
            }
        }
    </style>
@endpush

@section( 'body' )
    {{-- 基础登录页面，仅提供界面展示 --}}
    <main class="login-page">
        <section class="login-card" aria-labelledby="login-title">
            <div class="login-card-brand">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 3 4.5 7.2v9.6L12 21l7.5-4.2V7.2L12 3Z" />
                    <path d="m8.5 12 2.2 2.2 4.8-5" />
                </svg>
            </div>

            <header class="login-card-heading">
                <h1 id="login-title">欢迎登录</h1>
                <p>请输入账号信息以继续访问 {{ setting( 'app.title' ) }}</p>
            </header>

            <form class="login-form" id="login-form" autocomplete="on">
                <label class="login-form-field">
                    <span>邮箱地址</span>
                    <span class="login-form-input">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="3" y="5" width="18" height="14" rx="2" />
                            <path d="m4 7 8 6 8-6" />
                        </svg>
                        <input type="email" name="email" placeholder="name@example.com" autocomplete="email">
                    </span>
                </label>

                <label class="login-form-field">
                    <span>密码</span>
                    <span class="login-form-input">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="5" y="10" width="14" height="10" rx="2" />
                            <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                        </svg>
                        <input id="login-password" type="password" name="password" placeholder="请输入密码" autocomplete="current-password">
                        <button class="login-form-password-toggle" id="password-toggle" type="button" aria-label="显示密码">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" />
                                <circle cx="12" cy="12" r="2.5" />
                            </svg>
                        </button>
                    </span>
                </label>

                <div class="login-form-options">
                    <label class="login-form-remember">
                        <input type="checkbox" name="remember">
                        <span>保持登录状态</span>
                    </label>
                </div>

                <button class="login-form-submit" type="submit">登录</button>
            </form>

            <footer class="login-card-footer">安全访问 · 请妥善保管账号信息</footer>
        </section>
    </main>
@endsection

@push( 'script' )
    // 登录页基础交互，不发送接口请求
    const loginForm = document.getElementById( 'login-form' );
    const passwordInput = document.getElementById( 'login-password' );
    const passwordToggle = document.getElementById( 'password-toggle' );

    loginForm?.addEventListener( 'submit', function ( event ) {
        event.preventDefault();
    } );

    passwordToggle?.addEventListener( 'click', function () {
        const showPassword = passwordInput.type === 'password';
        passwordInput.type = showPassword ? 'text' : 'password';
        passwordToggle.setAttribute( 'aria-label', showPassword ? '隐藏密码' : '显示密码' );
    } );
@endpush
