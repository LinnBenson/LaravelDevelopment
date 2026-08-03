@extends( 'View::Frame' )

@section( 'title', '欢迎' )

@section( 'body' )
    {{-- 欢迎页面 --}}
    <div class="welcome-page">
        <div class="welcome-background" aria-hidden="true">
            <span class="welcome-orb welcome-orb-one"></span>
            <span class="welcome-orb welcome-orb-two"></span>
            <span class="welcome-grid"></span>
        </div>

        <header class="welcome-header">
            <a class="welcome-brand" href="{{ route( 'view.index' ) }}" aria-label="返回首页">
                <span class="welcome-brand-mark">
                    @if ( setting( 'app.icon' ) )
                        <img src="{{ setting( 'app.icon' ) }}" alt="{{ setting( 'app.title' ) }}">
                    @else
                        <i class="bi bi-hexagon-fill"></i>
                    @endif
                </span>
                <strong>{{ setting( 'app.title' ) }}</strong>
            </a>

            <a class="welcome-admin-link" href="{{ url( config( 'filament.path', 'admin' ) ) }}">
                <span>管理后台</span>
                <i class="bi bi-arrow-up-right"></i>
            </a>
        </header>

        <main class="welcome-main">
            <section class="welcome-hero">
                <div class="welcome-badge">
                    <i class="bi bi-stars"></i>
                    <span>欢迎访问</span>
                </div>

                <h1>一切准备就绪，<br><em>从这里开始。</em></h1>
                <p>{{ setting( 'app.title' ) }} 已成功运行。简洁、可靠且高效地承载你的下一次创造。</p>

                <div class="welcome-actions">
                    <a class="welcome-primary-button" href="{{ url( config( 'filament.path', 'admin' ) ) }}">
                        <span>进入管理后台</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    <a class="welcome-secondary-button" href="https://laravel.com/docs" target="_blank" rel="noopener noreferrer">
                        <i class="bi bi-book"></i>
                        <span>Laravel 文档</span>
                    </a>
                </div>
            </section>

            <section class="welcome-features" aria-label="应用特性">
                <article>
                    <span class="welcome-feature-icon"><i class="bi bi-lightning-charge"></i></span>
                    <div>
                        <h2>高效构建</h2>
                        <p>基于成熟的应用架构，快速实现你的业务构想。</p>
                    </div>
                </article>
                <article>
                    <span class="welcome-feature-icon"><i class="bi bi-shield-check"></i></span>
                    <div>
                        <h2>安全可靠</h2>
                        <p>清晰的权限与配置体系，让每一次操作更安心。</p>
                    </div>
                </article>
                <article>
                    <span class="welcome-feature-icon"><i class="bi bi-sliders"></i></span>
                    <div>
                        <h2>灵活管理</h2>
                        <p>通过统一管理后台，轻松维护应用与系统配置。</p>
                    </div>
                </article>
            </section>
        </main>

        <footer class="welcome-footer">
            <span>© {{ date( 'Y' ) }} {{ setting( 'app.title' ) }}</span>
            @if ( setting( 'app.copyright' ) )
                <span>{{ setting( 'app.copyright' ) }}</span>
            @endif
        </footer>
    </div>
@endsection

@push( 'head' )
    <style>
        /* 欢迎页基础布局 */
        .welcome-page {
            display: flex;
            width: min( 100%, 1280px );
            min-height: 100vh;
            margin: 0 auto;
            padding: 0 48px;
            overflow: hidden;
            flex-direction: column;
            position: relative;
        }
        .welcome-background {
            overflow: hidden;
            pointer-events: none;
            position: fixed;
            inset: 0;
            z-index: -1;
        }
        .welcome-grid {
            opacity: 0.2;
            background-image:
                linear-gradient( rgb( var( --r3 ), 0.14 ) 1px, transparent 1px ),
                linear-gradient( 90deg, rgb( var( --r3 ), 0.14 ) 1px, transparent 1px );
            background-size: 56px 56px;
            mask-image: linear-gradient( to bottom, black, transparent 72% );
            position: absolute;
            inset: 0;
        }
        .welcome-orb {
            display: block;
            width: 520px;
            height: 520px;
            background: rgb( var( --r3 ), 0.16 );
            border-radius: 50%;
            filter: blur( 90px );
            position: absolute;
        }
        .welcome-orb-one {
            top: -300px;
            right: -160px;
        }
        .welcome-orb-two {
            bottom: -360px;
            left: -220px;
            background: rgb( var( --r2 ), 0.12 );
        }

        /* 顶部导航 */
        .welcome-header {
            display: flex;
            min-height: 92px;
            border-bottom: 1px solid rgb( var( --r1 ), 0.1 );
            align-items: center;
            justify-content: space-between;
        }
        .welcome-brand {
            display: inline-flex;
            color: rgb( var( --r1 ) );
            font-size: 18px;
            align-items: center;
            gap: 12px;
        }
        .welcome-brand-mark {
            display: inline-flex;
            width: 42px;
            height: 42px;
            overflow: hidden;
            background: linear-gradient( 145deg, rgb( var( --r3 ) ), rgb( var( --r2 ) ) );
            border-radius: 13px;
            box-shadow: 0 10px 24px rgb( var( --r3 ), 0.2 );
            color: rgb( var( --r3c ) );
            align-items: center;
            justify-content: center;
        }
        .welcome-brand-mark img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .welcome-brand-mark i {
            font-size: 19px;
        }
        .welcome-admin-link {
            display: inline-flex;
            padding: 10px 14px;
            border: 1px solid rgb( var( --r1 ), 0.12 );
            border-radius: 10px;
            color: rgb( var( --r1 ) );
            font-size: 13px;
            font-weight: 700;
            align-items: center;
            gap: 8px;
            transition: border-color 160ms ease, color 160ms ease, transform 160ms ease;
        }
        .welcome-admin-link:hover {
            border-color: rgb( var( --r3 ), 0.4 );
            color: rgb( var( --r3 ) );
            transform: translateY( -1px );
        }

        /* 欢迎页主体 */
        .welcome-main {
            display: flex;
            padding: 84px 0 64px;
            flex: 1;
            flex-direction: column;
            justify-content: center;
        }
        .welcome-hero {
            max-width: 820px;
        }
        .welcome-badge {
            display: inline-flex;
            padding: 7px 12px;
            background: rgb( var( --r3 ), 0.09 );
            border: 1px solid rgb( var( --r3 ), 0.16 );
            border-radius: 999px;
            color: rgb( var( --r3 ) );
            font-size: 12px;
            font-weight: 700;
            align-items: center;
            gap: 7px;
        }
        .welcome-hero h1 {
            margin: 24px 0 0;
            color: rgb( var( --r1 ) );
            font-size: clamp( 48px, 7vw, 82px );
            font-weight: 800;
            line-height: 1.08;
            letter-spacing: -0.065em;
        }
        .welcome-hero h1 em {
            background: linear-gradient( 100deg, rgb( var( --r3 ) ), rgb( var( --r2 ) ) );
            background-clip: text;
            color: transparent;
            font-style: normal;
        }
        .welcome-hero > p {
            max-width: 620px;
            margin: 26px 0 0;
            color: rgb( var( --r1 ), 0.64 );
            font-size: 17px;
            line-height: 1.8;
        }
        .welcome-actions {
            display: flex;
            margin-top: 34px;
            align-items: center;
            gap: 12px;
        }
        .welcome-primary-button,
        .welcome-secondary-button {
            display: inline-flex;
            min-height: 48px;
            padding: 0 18px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            align-items: center;
            justify-content: center;
            gap: 9px;
            transition: box-shadow 160ms ease, transform 160ms ease;
        }
        .welcome-primary-button {
            background: linear-gradient( 135deg, rgb( var( --r3 ) ), rgb( var( --r2 ) ) );
            box-shadow: 0 12px 28px rgb( var( --r3 ), 0.2 );
            color: rgb( var( --r3c ) );
        }
        .welcome-secondary-button {
            background: rgb( var( --r6 ), 0.75 );
            border: 1px solid rgb( var( --r1 ), 0.12 );
            color: rgb( var( --r1 ) );
        }
        .welcome-primary-button:hover,
        .welcome-secondary-button:hover {
            box-shadow: 0 15px 32px rgb( var( --r3 ), 0.2 );
            transform: translateY( -2px );
        }

        /* 特性卡片 */
        .welcome-features {
            display: grid;
            margin-top: 80px;
            grid-template-columns: repeat( 3, minmax( 0, 1fr ) );
            gap: 14px;
        }
        .welcome-features article {
            display: flex;
            padding: 20px;
            background: rgb( var( --r6 ), 0.78 );
            border: 1px solid rgb( var( --r1 ), 0.1 );
            border-radius: 16px;
            box-shadow: 0 10px 32px rgb( var( --r1 ), 0.035 );
            backdrop-filter: blur( 12px );
            gap: 14px;
            transition: border-color 160ms ease, transform 160ms ease;
        }
        .welcome-features article:hover {
            border-color: rgb( var( --r3 ), 0.22 );
            transform: translateY( -3px );
        }
        .welcome-feature-icon {
            display: inline-flex;
            width: 42px;
            height: 42px;
            flex: 0 0 auto;
            background: rgb( var( --r3 ), 0.09 );
            border-radius: 12px;
            color: rgb( var( --r3 ) );
            align-items: center;
            justify-content: center;
        }
        .welcome-feature-icon i {
            font-size: 18px;
        }
        .welcome-features h2 {
            color: rgb( var( --r1 ) );
            font-size: 15px;
        }
        .welcome-features p {
            margin-top: 7px;
            color: rgb( var( --r1 ), 0.58 );
            font-size: 12px;
            line-height: 1.65;
        }

        /* 页脚与响应式布局 */
        .welcome-footer {
            display: flex;
            min-height: 68px;
            border-top: 1px solid rgb( var( --r1 ), 0.1 );
            color: rgb( var( --r1 ), 0.42 );
            font-size: 11px;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }
        @media ( max-width: 760px ) {
            .welcome-page {
                padding: 0 22px;
            }
            .welcome-header {
                min-height: 76px;
            }
            .welcome-main {
                padding: 62px 0 48px;
            }
            .welcome-hero h1 {
                font-size: clamp( 42px, 13vw, 62px );
            }
            .welcome-features {
                margin-top: 56px;
                grid-template-columns: 1fr;
            }
        }
        @media ( max-width: 480px ) {
            .welcome-brand strong {
                max-width: 150px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            .welcome-admin-link span {
                display: none;
            }
            .welcome-admin-link {
                width: 42px;
                height: 42px;
                padding: 0;
                justify-content: center;
            }
            .welcome-actions {
                align-items: stretch;
                flex-direction: column;
            }
            .welcome-primary-button,
            .welcome-secondary-button {
                width: 100%;
            }
            .welcome-footer {
                padding: 20px 0;
                align-items: flex-start;
                flex-direction: column;
                justify-content: center;
                gap: 5px;
            }
        }
        @media ( prefers-reduced-motion: reduce ) {
            .welcome-admin-link,
            .welcome-primary-button,
            .welcome-secondary-button,
            .welcome-features article {
                transition: none;
            }
        }
    </style>
@endpush
