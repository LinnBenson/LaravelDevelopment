<x-filament-panels::page>
    <div class="home-dashboard">
        {{-- 欢迎区域 --}}
        <section class="home-dashboard-hero">
            <div class="home-dashboard-hero-content">
                <div class="home-dashboard-avatar">
                    @if ( $this->getAdminAvatarUrl() )
                        <img src="{{ $this->getAdminAvatarUrl() }}" alt="{{ $this->getAdminName() }}">
                    @else
                        <span>{{ mb_strtoupper( mb_substr( $this->getAdminName(), 0, 1 ) ) }}</span>
                    @endif
                </div>
                <div>
                    <p class="home-dashboard-eyebrow">{{ now()->translatedFormat( 'Y年m月d日 l' ) }}</p>
                    <h2>{{ $this->getGreeting() }}，{{ $this->getAdminName() }}</h2>
                    <p>欢迎回来，今天也一起把后台管理得井井有条。</p>
                </div>
            </div>
            <a class="home-dashboard-primary-action" href="{{ \App\Filament\Resources\UserManagement\Users\UserResource::getUrl( 'create' ) }}">
                <x-filament::icon icon="heroicon-o-plus" />
                <span>新增用户</span>
            </a>
            <div class="home-dashboard-hero-decoration" aria-hidden="true"></div>
        </section>

        {{-- 核心数据统计 --}}
        <section class="home-dashboard-stats">
            @foreach ( $this->getStats() as $stat )
                <article class="home-dashboard-stat-card">
                    <div class="home-dashboard-stat-icon">
                        <x-filament::icon :icon="$stat['icon']" />
                    </div>
                    <div class="home-dashboard-stat-copy">
                        <span>{{ $stat['label'] }}</span>
                        <strong>{{ number_format( $stat['value'] ) }}</strong>
                        <small>{{ $stat['description'] }}</small>
                    </div>
                </article>
            @endforeach
        </section>

        <div class="home-dashboard-content-grid">
            <div class="home-dashboard-main-column">
                {{-- 服务项汇总 --}}
                @php $serviceSummary = $this->getServiceSummary(); @endphp
                <section class="home-dashboard-panel home-dashboard-service-panel">
                    <header class="home-dashboard-panel-header">
                        <div>
                            <h3>服务项汇总</h3>
                            <p>当前 Workerman 服务运行概况</p>
                        </div>
                        <a href="{{ \App\Filament\Resources\SystemSettings\ServiceManagement\ServiceManagement::getUrl() }}">
                            查看服务
                            <x-filament::icon icon="heroicon-o-arrow-right" />
                        </a>
                    </header>
                    <dl class="home-dashboard-service-summary">
                        <div>
                            <dt>服务总数</dt>
                            <dd>{{ $serviceSummary['total'] }}</dd>
                        </div>
                        <div class="is-running">
                            <dt>运行中</dt>
                            <dd>{{ $serviceSummary['running'] }}</dd>
                        </div>
                        <div class="is-stopped">
                            <dt>已停止</dt>
                            <dd>{{ $serviceSummary['stopped'] }}</dd>
                        </div>
                    </dl>
                </section>

                {{-- 最近用户 --}}
                <section class="home-dashboard-panel home-dashboard-recent-users" style="min-height: 435px;">
                    <header class="home-dashboard-panel-header">
                        <div>
                            <h3>最近用户</h3>
                            <p>最近创建的用户账号</p>
                        </div>
                        <a href="{{ \App\Filament\Resources\UserManagement\Users\UserResource::getUrl( 'index' ) }}">
                            查看全部
                            <x-filament::icon icon="heroicon-o-arrow-right" />
                        </a>
                    </header>

                    <div class="home-dashboard-user-list">
                        @forelse ( $this->getRecentUsers() as $user )
                            @php
                                $avatarUrl = filled( $user->avatar ) && Storage::disk( 'public' )->exists( $user->avatar )
                                    ? Storage::disk( 'public' )->url( $user->avatar )
                                    : null;
                            @endphp
                            <a
                                class="home-dashboard-user-row"
                                href="{{ \App\Filament\Resources\UserManagement\Users\UserResource::getUrl( 'edit', ['record' => $user] ) }}"
                            >
                                <div class="home-dashboard-user-avatar">
                                    @if ( $avatarUrl )
                                        <img src="{{ $avatarUrl }}" alt="{{ $user->name ?: '用户头像' }}">
                                    @else
                                        <span>{{ mb_strtoupper( mb_substr( $user->name ?: $user->nickname ?: 'U', 0, 1 ) ) }}</span>
                                    @endif
                                </div>
                                <div class="home-dashboard-user-main">
                                    <strong>{{ $user->nickname ?: $user->name ?: '未命名用户' }}</strong>
                                    <span>{{ $user->email ?: \App\Models\User::formatPhoneForDisplay( $user->phone ) ?: '暂无联系方式' }}</span>
                                </div>
                                <span class="home-dashboard-user-level">{{ $user->level }} · {{ \App\Models\User::getLevel( $user->level ) }}</span>
                                <span class="home-dashboard-user-status {{ $user->status ? 'is-active' : '' }}">
                                    {{ $user->status ? '启用' : '停用' }}
                                </span>
                                <time>{{ $user->created_at?->format( 'm.d H:i' ) }}</time>
                                <x-filament::icon icon="heroicon-o-chevron-right" />
                            </a>
                        @empty
                            <div class="home-dashboard-empty">
                                <x-filament::icon icon="heroicon-o-user-plus" />
                                <strong>还没有用户</strong>
                                <span>创建第一个用户后会显示在这里。</span>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>

            <aside class="home-dashboard-side-column">
                {{-- 快捷入口 --}}
                <section class="home-dashboard-panel">
                    <header class="home-dashboard-panel-header">
                        <div>
                            <h3>快捷入口</h3>
                            <p>快速前往常用功能</p>
                        </div>
                    </header>
                    <div class="home-dashboard-quick-links">
                        @foreach ( $this->getQuickLinks() as $link )
                            <a href="{{ $link['url'] }}">
                                <span class="home-dashboard-quick-icon">
                                    <x-filament::icon :icon="$link['icon']" />
                                </span>
                                <span>
                                    <strong>{{ $link['label'] }}</strong>
                                    <small>{{ $link['description'] }}</small>
                                </span>
                                <x-filament::icon icon="heroicon-o-arrow-up-right" />
                            </a>
                        @endforeach
                    </div>
                </section>

                {{-- 系统信息 --}}
                <section class="home-dashboard-panel home-dashboard-system-panel">
                    <header class="home-dashboard-panel-header">
                        <div>
                            <h3>系统信息</h3>
                            <p>当前应用运行环境</p>
                        </div>
                        <span class="home-dashboard-online"><i></i>运行正常</span>
                    </header>
                    <dl class="home-dashboard-system-list">
                        @foreach ( $this->getSystemInformation() as $label => $value )
                            <div>
                                <dt>{{ $label }}</dt>
                                <dd>{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                    <div class="home-dashboard-developer-links">
                        @foreach ( $this->getDeveloperLinks() as $link )
                            <a href="{{ $link['url'] }}">{{ $link['label'] }}</a>
                        @endforeach
                    </div>
                </section>
            </aside>
        </div>
    </div>

    <style>
        /* 仪表板整体布局 */
        .home-dashboard {
            --home-card: #ffffff;
            --home-border: #e5e7eb;
            --home-muted: #6b7280;
            --home-text: #111827;
            display: grid;
            color: var(--home-text);
            gap: 1.5rem;
        }
        .dark .home-dashboard {
            --home-card: #18181b;
            --home-border: #27272a;
            --home-muted: #a1a1aa;
            --home-text: #f4f4f5;
        }

        /* 欢迎区域 */
        .home-dashboard-hero {
            display: flex;
            min-height: 10rem;
            padding: 2rem;
            overflow: hidden;
            border: 1px solid color-mix(in srgb, var(--primary-500) 20%, transparent);
            border-radius: 1.25rem;
            background: linear-gradient(120deg, var(--primary-50) 0%, var(--primary-50) 50%, var(--primary-100) 100%);
            box-shadow: 0 1rem 2.5rem color-mix(in srgb, var(--primary-600) 8%, transparent);
            align-items: center;
            justify-content: space-between;
            gap: 2rem;
            position: relative;
        }
        .dark .home-dashboard-hero {
            border-color: color-mix(in srgb, var(--primary-500) 18%, transparent);
            background: linear-gradient(
                120deg,
                color-mix(in srgb, var(--primary-500) 12%, var(--home-card)) 0%,
                color-mix(in srgb, var(--primary-500) 8%, var(--home-card)) 55%,
                color-mix(in srgb, var(--primary-500) 16%, var(--home-card)) 100%
            );
        }
        .home-dashboard-hero-content {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            position: relative;
            z-index: 1;
        }
        .home-dashboard-avatar {
            display: grid;
            width: 4.5rem;
            height: 4.5rem;
            overflow: hidden;
            border: 0.25rem solid rgba(255, 255, 255, 0.8);
            border-radius: 1.25rem;
            background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
            box-shadow: 0 0.75rem 1.5rem color-mix(in srgb, var(--primary-600) 18%, transparent);
            color: #ffffff;
            font-size: 1.5rem;
            font-weight: 700;
            place-items: center;
        }
        .home-dashboard-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .home-dashboard-eyebrow {
            margin: 0 0 0.35rem;
            color: var(--primary-700);
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .home-dashboard-hero h2 {
            margin: 0;
            font-size: clamp(1.4rem, 2.5vw, 2rem);
            font-weight: 800;
            line-height: 1.2;
        }
        .home-dashboard-hero-content p:last-child {
            margin: 0.5rem 0 0;
            color: var(--home-muted);
            font-size: 0.9rem;
        }
        .home-dashboard-primary-action {
            display: inline-flex;
            padding: 0.8rem 1.1rem;
            border-radius: 0.75rem;
            background: var(--primary-600);
            box-shadow: 0 0.5rem 1rem color-mix(in srgb, var(--primary-600) 20%, transparent);
            color: #ffffff;
            font-size: 0.875rem;
            font-weight: 700;
            text-decoration: none;
            align-items: center;
            gap: 0.5rem;
            position: relative;
            z-index: 1;
        }
        .home-dashboard-primary-action:hover {
            background: var(--primary-700);
        }
        .home-dashboard-primary-action svg {
            width: 1.1rem;
            height: 1.1rem;
        }
        .home-dashboard-hero-decoration {
            width: 13rem;
            height: 13rem;
            border: 2.5rem solid color-mix(in srgb, var(--primary-500) 8%, transparent);
            border-radius: 50%;
            right: -3rem;
            bottom: -7rem;
            position: absolute;
        }

        /* 数据统计卡片 */
        .home-dashboard-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
        }
        .home-dashboard-stat-card {
            display: flex;
            padding: 1.25rem;
            border: 1px solid var(--home-border);
            border-radius: 1rem;
            background: var(--home-card);
            box-shadow: 0 0.25rem 1rem rgba(15, 23, 42, 0.04);
            align-items: center;
            gap: 1rem;
        }
        .home-dashboard-stat-icon {
            display: grid;
            width: 3rem;
            height: 3rem;
            flex: 0 0 3rem;
            border-radius: 0.875rem;
            background: var(--primary-50);
            color: var(--primary-600);
            place-items: center;
        }
        .dark .home-dashboard-stat-icon {
            background: color-mix(in srgb, var(--primary-500) 12%, transparent);
        }
        .home-dashboard-stat-card:nth-child(2) .home-dashboard-stat-icon {
            background: #ecfdf5;
            color: #059669;
        }
        .dark .home-dashboard-stat-card:nth-child(2) .home-dashboard-stat-icon {
            background: rgba(16, 185, 129, 0.12);
        }
        .home-dashboard-stat-card:nth-child(3) .home-dashboard-stat-icon {
            background: #eff6ff;
            color: #2563eb;
        }
        .dark .home-dashboard-stat-card:nth-child(3) .home-dashboard-stat-icon {
            background: rgba(59, 130, 246, 0.12);
        }
        .home-dashboard-stat-card:nth-child(4) .home-dashboard-stat-icon {
            background: #faf5ff;
            color: #9333ea;
        }
        .dark .home-dashboard-stat-card:nth-child(4) .home-dashboard-stat-icon {
            background: rgba(168, 85, 247, 0.12);
        }
        .home-dashboard-stat-icon svg {
            width: 1.5rem;
            height: 1.5rem;
        }
        .home-dashboard-stat-copy {
            display: grid;
            min-width: 0;
        }
        .home-dashboard-stat-copy span {
            color: var(--home-muted);
            font-size: 0.78rem;
            font-weight: 600;
        }
        .home-dashboard-stat-copy strong {
            margin: 0.15rem 0;
            font-size: 1.65rem;
            line-height: 1.1;
        }
        .home-dashboard-stat-copy small {
            overflow: hidden;
            color: var(--home-muted);
            font-size: 0.7rem;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* 主内容面板 */
        .home-dashboard-content-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.55fr) minmax(20rem, 0.85fr);
            align-items: start;
            gap: 1.25rem;
        }
        .home-dashboard-side-column {
            display: grid;
            gap: 1.25rem;
        }
        .home-dashboard-main-column {
            display: grid;
            gap: 1.25rem;
        }
        .home-dashboard-panel {
            overflow: hidden;
            border: 1px solid var(--home-border);
            border-radius: 1rem;
            background: var(--home-card);
            box-shadow: 0 0.25rem 1rem rgba(15, 23, 42, 0.04);
        }
        .home-dashboard-panel-header {
            display: flex;
            min-height: 4.75rem;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--home-border);
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .home-dashboard-panel-header h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 750;
        }
        .home-dashboard-panel-header p {
            margin: 0.2rem 0 0;
            color: var(--home-muted);
            font-size: 0.75rem;
        }
        .home-dashboard-panel-header > a {
            display: inline-flex;
            color: var(--primary-600);
            font-size: 0.78rem;
            font-weight: 650;
            text-decoration: none;
            align-items: center;
            gap: 0.3rem;
        }
        .home-dashboard-panel-header > a svg {
            width: 0.9rem;
            height: 0.9rem;
        }
        .home-dashboard-service-summary {
            display: grid;
            margin: 0;
            padding: 1rem 1.25rem;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.75rem;
        }
        .home-dashboard-service-summary div {
            display: flex;
            min-height: 3.5rem;
            padding: 0.7rem 0.85rem;
            border: 1px solid var(--home-border);
            border-radius: 0.65rem;
            background: color-mix(in srgb, var(--primary-500) 4%, var(--home-card));
            align-items: center;
            justify-content: space-between;
            gap: 0.6rem;
        }
        .home-dashboard-service-summary dt {
            color: var(--home-muted);
            font-size: 0.68rem;
        }
        .home-dashboard-service-summary dd {
            margin: 0;
            color: var(--home-text);
            font-size: 0.85rem;
            font-weight: 750;
        }
        .home-dashboard-service-summary .is-running dd {
            color: #059669;
        }
        .home-dashboard-service-summary .is-stopped dd {
            color: #dc2626;
        }

        /* 最近用户列表 */
        .home-dashboard-user-list {
            display: grid;
        }
        .home-dashboard-user-row {
            display: grid;
            min-height: 4.65rem;
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid var(--home-border);
            color: inherit;
            text-decoration: none;
            grid-template-columns: auto minmax(8rem, 1fr) auto auto auto auto;
            align-items: center;
            gap: 0.8rem;
        }
        .home-dashboard-user-row:last-child {
            border-bottom: 0;
        }
        .home-dashboard-user-row:hover {
            background: color-mix(in srgb, var(--primary-500) 4.5%, transparent);
        }
        .home-dashboard-user-avatar {
            display: grid;
            width: 2.5rem;
            height: 2.5rem;
            overflow: hidden;
            border-radius: 0.75rem;
            background: linear-gradient(135deg, var(--primary-100), var(--primary-200));
            color: var(--primary-700);
            font-size: 0.85rem;
            font-weight: 700;
            place-items: center;
        }
        .home-dashboard-user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .home-dashboard-user-main {
            display: grid;
            min-width: 0;
        }
        .home-dashboard-user-main strong,
        .home-dashboard-user-main span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .home-dashboard-user-main strong {
            font-size: 0.85rem;
        }
        .home-dashboard-user-main span,
        .home-dashboard-user-row time {
            color: var(--home-muted);
            font-size: 0.72rem;
        }
        .home-dashboard-user-level,
        .home-dashboard-user-status {
            padding: 0.3rem 0.5rem;
            border-radius: 999px;
            background: #f4f4f5;
            color: #71717a;
            font-size: 0.68rem;
            font-weight: 650;
            white-space: nowrap;
        }
        .dark .home-dashboard-user-level,
        .dark .home-dashboard-user-status {
            background: #27272a;
            color: #a1a1aa;
        }
        .home-dashboard-user-status.is-active {
            background: #ecfdf5;
            color: #047857;
        }
        .dark .home-dashboard-user-status.is-active {
            background: rgba(16, 185, 129, 0.12);
            color: #34d399;
        }
        .home-dashboard-user-row > svg {
            width: 1rem;
            height: 1rem;
            color: #a1a1aa;
        }
        .home-dashboard-empty {
            display: grid;
            min-height: 18rem;
            padding: 2rem;
            color: var(--home-muted);
            text-align: center;
            place-content: center;
            justify-items: center;
            gap: 0.4rem;
        }
        .home-dashboard-empty svg {
            width: 2rem;
            height: 2rem;
            margin-bottom: 0.35rem;
            color: var(--primary-600);
        }
        .home-dashboard-empty strong {
            color: var(--home-text);
        }
        .home-dashboard-empty span {
            font-size: 0.78rem;
        }

        /* 快捷入口 */
        .home-dashboard-quick-links {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
            padding: 1rem;
        }
        .home-dashboard-quick-links > a {
            display: grid;
            min-height: 5.4rem;
            padding: 0.8rem;
            border: 1px solid var(--home-border);
            border-radius: 0.8rem;
            color: inherit;
            text-decoration: none;
            grid-template-columns: auto minmax(0, 1fr) auto;
            align-items: center;
            gap: 0.65rem;
        }
        .home-dashboard-quick-links > a:hover {
            border-color: color-mix(in srgb, var(--primary-600) 50%, transparent);
            background: color-mix(in srgb, var(--primary-500) 4%, transparent);
            transform: translateY(-1px);
        }
        .home-dashboard-quick-icon {
            display: grid;
            width: 2.3rem;
            height: 2.3rem;
            border-radius: 0.65rem;
            background: var(--primary-50);
            color: var(--primary-600);
            place-items: center;
        }
        .dark .home-dashboard-quick-icon {
            background: color-mix(in srgb, var(--primary-500) 12%, transparent);
        }
        .home-dashboard-quick-icon svg {
            width: 1.2rem;
            height: 1.2rem;
        }
        .home-dashboard-quick-links strong,
        .home-dashboard-quick-links small {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .home-dashboard-quick-links strong {
            font-size: 0.78rem;
        }
        .home-dashboard-quick-links small {
            margin-top: 0.2rem;
            color: var(--home-muted);
            font-size: 0.65rem;
        }
        .home-dashboard-quick-links > a > svg {
            width: 0.9rem;
            height: 0.9rem;
            color: #a1a1aa;
        }

        /* 系统信息 */
        .home-dashboard-online {
            display: inline-flex;
            color: #059669;
            font-size: 0.7rem;
            font-weight: 650;
            align-items: center;
            gap: 0.35rem;
        }
        .home-dashboard-online i {
            width: 0.45rem;
            height: 0.45rem;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.12);
        }
        .home-dashboard-system-list {
            display: grid;
            margin: 0;
            padding: 0.75rem 1.25rem;
        }
        .home-dashboard-system-list div {
            display: flex;
            min-height: 2.4rem;
            border-bottom: 1px dashed var(--home-border);
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .home-dashboard-system-list div:last-child {
            border-bottom: 0;
        }
        .home-dashboard-system-list dt {
            color: var(--home-muted);
            font-size: 0.75rem;
        }
        .home-dashboard-system-list dd {
            margin: 0;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: 0.72rem;
            font-weight: 650;
        }
        .home-dashboard-developer-links {
            display: flex;
            padding: 0.85rem 1.25rem;
            border-top: 1px solid var(--home-border);
            gap: 1rem;
        }
        .home-dashboard-developer-links a {
            color: var(--primary-600);
            font-size: 0.72rem;
            font-weight: 650;
            text-decoration: none;
        }

        /* 响应式布局 */
        @media (max-width: 80rem) {
            .home-dashboard-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .home-dashboard-content-grid {
                grid-template-columns: 1fr;
            }
            .home-dashboard-side-column {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 48rem) {
            .home-dashboard-hero {
                padding: 1.35rem;
                align-items: flex-start;
                flex-direction: column;
            }
            .home-dashboard-avatar {
                width: 3.5rem;
                height: 3.5rem;
                border-radius: 1rem;
            }
            .home-dashboard-stats,
            .home-dashboard-side-column {
                grid-template-columns: 1fr;
            }
            .home-dashboard-user-level,
            .home-dashboard-user-row time {
                display: none;
            }
            .home-dashboard-user-row {
                grid-template-columns: auto minmax(0, 1fr) auto auto;
            }
            .home-dashboard-service-summary {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 32rem) {
            .home-dashboard-quick-links {
                grid-template-columns: 1fr;
            }
            .home-dashboard-user-status {
                display: none;
            }
            .home-dashboard-user-row {
                grid-template-columns: auto minmax(0, 1fr) auto;
            }
        }
    </style>
</x-filament-panels::page>
