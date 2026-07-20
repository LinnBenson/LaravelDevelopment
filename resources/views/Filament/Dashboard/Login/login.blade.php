<x-filament-panels::page.simple>
    {{-- 后台登录主体 --}}
    <div class="admin-login-shell">
        <section class="admin-login-brand" aria-label="后台管理系统介绍">
            <div class="admin-login-brand-glow admin-login-brand-glow-top"></div>
            <div class="admin-login-brand-glow admin-login-brand-glow-bottom"></div>

            <div class="admin-login-brand-content">
                <div class="admin-login-brand-mark">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 3 4.5 7.2v9.6L12 21l7.5-4.2V7.2L12 3Z" />
                        <path d="m8.5 12 2.2 2.2 4.8-5" />
                    </svg>
                </div>

                <div class="admin-login-brand-copy">
                    <span>ADMIN CONSOLE</span>
                    <h1>{{ config( 'app.name' ) }}</h1>
                    <p>专注、可靠的管理工作台，让每一次操作都清晰高效。</p>
                </div>

                <div class="admin-login-brand-meta">
                    <span><i></i> 系统运行正常</span>
                    <span>安全管理中心</span>
                </div>
            </div>
        </section>

        <section class="admin-login-form-panel">
            <div class="admin-login-form-heading">
                <div class="admin-login-mobile-mark">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 3 4.5 7.2v9.6L12 21l7.5-4.2V7.2L12 3Z" />
                        <path d="m8.5 12 2.2 2.2 4.8-5" />
                    </svg>
                </div>
                <span>欢迎回来</span>
                <h2>登录管理后台</h2>
                <p>请输入管理员账号信息以继续</p>
            </div>

            {{ $this->content }}

            <div class="admin-login-security-note">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 3 5 6v5c0 4.7 2.9 8.1 7 10 4.1-1.9 7-5.3 7-10V6l-7-3Z" />
                    <path d="m9.5 12 1.7 1.7 3.7-4" />
                </svg>
                <span>登录过程受安全保护</span>
            </div>
        </section>
    </div>

    <style>
        /* 登录页整体布局 */
        .fi-simple-layout:has(.admin-login-shell) {
            position: relative;
            overflow: hidden;
            min-height: 100vh;
            background:
                radial-gradient(circle at 10% 10%, rgba(245, 158, 11, 0.12), transparent 30rem),
                radial-gradient(circle at 90% 90%, rgba(251, 191, 36, 0.1), transparent 28rem),
                #f8f8f6;
        }
        .fi-simple-layout:has(.admin-login-shell)::before {
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0.28;
            pointer-events: none;
            background-image: radial-gradient(rgba(120, 113, 108, 0.35) 0.7px, transparent 0.7px);
            background-size: 22px 22px;
            mask-image: linear-gradient(to bottom right, black, transparent 65%);
        }
        .fi-simple-layout:has(.admin-login-shell) .fi-simple-main-ctn {
            position: relative;
            z-index: 1;
            padding: 32px;
        }
        .fi-simple-layout:has(.admin-login-shell) .fi-simple-main {
            width: min(100%, 1040px);
            max-width: 1040px;
            padding: 0;
        }
        .fi-simple-layout:has(.admin-login-shell) .fi-simple-page-content {
            margin-top: 0;
        }
        .admin-login-shell {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(400px, 0.95fr);
            overflow: hidden;
            min-height: 640px;
            border: 1px solid rgba(231, 229, 228, 0.9);
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 32px 80px rgba(28, 25, 23, 0.12), 0 4px 16px rgba(28, 25, 23, 0.05);
        }

        /* 左侧品牌区域 */
        .admin-login-brand {
            position: relative;
            overflow: hidden;
            padding: 64px;
            color: #fff;
            background: linear-gradient(145deg, #1c1917 0%, #29211b 52%, #3b2411 100%);
        }
        .admin-login-brand::before {
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0.18;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.12) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.12) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: linear-gradient(to bottom, black, transparent 82%);
        }
        .admin-login-brand-glow {
            position: absolute;
            width: 340px;
            height: 340px;
            border-radius: 999px;
            filter: blur(18px);
            background: rgba(245, 158, 11, 0.3);
        }
        .admin-login-brand-glow-top {
            top: -190px;
            right: -150px;
        }
        .admin-login-brand-glow-bottom {
            bottom: -220px;
            left: -180px;
        }
        .admin-login-brand-content {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            min-height: 100%;
        }
        .admin-login-brand-mark,
        .admin-login-mobile-mark {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            border-radius: 16px;
            color: #1c1917;
            background: linear-gradient(145deg, #fbbf24, #f59e0b);
            box-shadow: 0 12px 30px rgba(245, 158, 11, 0.25);
        }
        .admin-login-brand-mark svg,
        .admin-login-mobile-mark svg {
            width: 28px;
            height: 28px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
        .admin-login-brand-copy {
            margin: auto 0;
            padding: 72px 0;
        }
        .admin-login-brand-copy > span,
        .admin-login-form-heading > span {
            display: block;
            margin-bottom: 14px;
            color: #fbbf24;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.2em;
        }
        .admin-login-brand-copy h1 {
            margin: 0;
            color: #fff;
            font-size: clamp(38px, 4vw, 56px);
            font-weight: 750;
            line-height: 1.08;
            letter-spacing: -0.045em;
        }
        .admin-login-brand-copy p {
            max-width: 380px;
            margin: 24px 0 0;
            color: rgba(255, 255, 255, 0.62);
            font-size: 16px;
            line-height: 1.8;
        }
        .admin-login-brand-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
        }
        .admin-login-brand-meta span:first-child {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .admin-login-brand-meta i {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #34d399;
            box-shadow: 0 0 0 4px rgba(52, 211, 153, 0.12);
        }

        /* 右侧登录表单 */
        .admin-login-form-panel {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 64px;
            background: rgba(255, 255, 255, 0.96);
        }
        .admin-login-form-heading {
            margin-bottom: 34px;
        }
        .admin-login-form-heading > span {
            margin-bottom: 10px;
            color: #d97706;
            letter-spacing: 0.12em;
        }
        .admin-login-form-heading h2 {
            margin: 0;
            color: #1c1917;
            font-size: 30px;
            font-weight: 750;
            line-height: 1.25;
            letter-spacing: -0.035em;
        }
        .admin-login-form-heading p {
            margin: 10px 0 0;
            color: #78716c;
            font-size: 14px;
        }
        .admin-login-mobile-mark {
            display: none;
            margin-bottom: 28px;
        }
        .admin-login-form-panel .fi-fo-field-label-content {
            color: #44403c;
            font-weight: 650;
        }
        .admin-login-form-panel .fi-input-wrp {
            border-radius: 12px;
            background: #fafaf9;
            box-shadow: inset 0 0 0 1px #e7e5e4;
            transition: background 160ms ease, box-shadow 160ms ease, transform 160ms ease;
        }
        .admin-login-form-panel .fi-input-wrp-content-ctn {
            align-items: stretch;
        }
        .admin-login-form-panel .fi-input {
            min-height: 48px;
            padding-top: 13px;
            padding-bottom: 13px;
            line-height: 22px;
        }
        .admin-login-form-panel .fi-input:-webkit-autofill,
        .admin-login-form-panel .fi-input:-webkit-autofill:hover,
        .admin-login-form-panel .fi-input:-webkit-autofill:focus {
            -webkit-text-fill-color: #1c1917;
            box-shadow: 0 0 0 1000px #fafaf9 inset;
            transition: background-color 9999s ease-out;
        }
        .admin-login-form-panel .fi-input-wrp-suffix {
            align-self: stretch;
        }
        .admin-login-form-panel .fi-input-wrp:focus-within {
            background: #fff;
            box-shadow: inset 0 0 0 2px #f59e0b, 0 0 0 4px rgba(245, 158, 11, 0.1);
        }
        .admin-login-form-panel .fi-btn {
            min-height: 48px;
            border-radius: 12px;
            font-weight: 700;
            box-shadow: 0 10px 24px rgba(245, 158, 11, 0.2);
            transition: transform 160ms ease, box-shadow 160ms ease;
        }
        .admin-login-form-panel .fi-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(245, 158, 11, 0.26);
        }
        .admin-login-security-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 28px;
            color: #a8a29e;
            font-size: 12px;
        }
        .admin-login-security-note svg {
            width: 16px;
            height: 16px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* 登录页深色模式 */
        .dark .fi-simple-layout:has(.admin-login-shell) {
            background: #0c0a09;
        }
        .dark .admin-login-shell {
            border-color: rgba(68, 64, 60, 0.8);
            background: #1c1917;
            box-shadow: 0 32px 80px rgba(0, 0, 0, 0.4);
        }
        .dark .admin-login-form-panel {
            background: #1c1917;
        }
        .dark .admin-login-form-heading h2 {
            color: #fafaf9;
        }
        .dark .admin-login-form-heading p {
            color: #a8a29e;
        }
        .dark .admin-login-form-panel .fi-fo-field-label-content {
            color: #e7e5e4;
        }
        .dark .admin-login-form-panel .fi-input-wrp {
            background: #292524;
            box-shadow: inset 0 0 0 1px #44403c;
        }
        .dark .admin-login-form-panel .fi-input-wrp:focus-within {
            background: #292524;
            box-shadow: inset 0 0 0 2px #f59e0b, 0 0 0 4px rgba(245, 158, 11, 0.12);
        }
        .dark .admin-login-form-panel .fi-input:-webkit-autofill,
        .dark .admin-login-form-panel .fi-input:-webkit-autofill:hover,
        .dark .admin-login-form-panel .fi-input:-webkit-autofill:focus {
            -webkit-text-fill-color: #fafaf9;
            box-shadow: 0 0 0 1000px #292524 inset;
        }

        /* 登录页响应式适配 */
        @media (max-width: 820px) {
            .fi-simple-layout:has(.admin-login-shell) .fi-simple-main-ctn {
                padding: 20px;
            }
            .admin-login-shell {
                display: block;
                min-height: auto;
                border-radius: 24px;
            }
            .admin-login-brand {
                display: none;
            }
            .admin-login-form-panel {
                min-height: 600px;
                padding: 48px;
            }
            .admin-login-mobile-mark {
                display: flex;
            }
        }
        @media (max-width: 520px) {
            .fi-simple-layout:has(.admin-login-shell) .fi-simple-main-ctn {
                align-items: flex-start;
                padding: 12px;
            }
            .admin-login-shell {
                border-radius: 20px;
            }
            .admin-login-form-panel {
                min-height: calc(100vh - 24px);
                padding: 34px 24px;
            }
            .admin-login-form-heading {
                margin-bottom: 28px;
            }
            .admin-login-form-heading h2 {
                font-size: 26px;
            }
        }
    </style>
</x-filament-panels::page.simple>
