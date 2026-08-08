<x-filament-panels::page>
    <div class="unified-dashboard">
        {{-- 管理员欢迎区域 --}}
        <section class="unified-dashboard-hero">
            <div class="unified-dashboard-profile">
                <div class="unified-dashboard-avatar">
                    @if ( $this->getAdminAvatarUrl() )
                        <img src="{{ $this->getAdminAvatarUrl() }}" alt="{{ $this->getAdminName() }}">
                    @else
                        <span>{{ mb_strtoupper( mb_substr( $this->getAdminName(), 0, 1 ) ) }}</span>
                    @endif
                </div>
                <div class="unified-dashboard-welcome">
                    <span class="unified-dashboard-date">{{ now()->translatedFormat( 'Y年m月d日 l' ) }}</span>
                    <h2>{{ $this->getGreeting() }}，{{ $this->getAdminName() }}</h2>
                    <p>{{ $this->getRoleDescription() }}</p>
                </div>
            </div>
            <a class="unified-dashboard-create" href="{{ \App\Filament\Resources\UserManagement\Users\UserResource::getUrl( 'create' ) }}">
                <x-filament::icon icon="heroicon-o-user-plus" />
                新增用户
            </a>
            <span class="unified-dashboard-orbit" aria-hidden="true"></span>
        </section>

        {{-- 权限范围内的数据统计 --}}
        <section class="unified-dashboard-stats" aria-label="用户统计">
            @foreach ( $this->getStats() as $stat )
                <article class="unified-dashboard-stat" data-tone="{{ $stat['tone'] }}">
                    <span class="unified-dashboard-stat-icon"><x-filament::icon :icon="$stat['icon']" /></span>
                    <div>
                        <span>{{ $stat['label'] }}</span>
                        <strong>{{ number_format( $stat['value'] ) }}</strong>
                        <small>{{ $stat['description'] }}</small>
                    </div>
                </article>
            @endforeach
        </section>

        <div class="unified-dashboard-layout">
            {{-- 最近用户 --}}
            <section class="unified-dashboard-card unified-dashboard-users">
                <header class="unified-dashboard-card-header">
                    <div>
                        <h3>{{ $this->isAgent() ? '我的最近用户' : '最近用户' }}</h3>
                        <p>按创建时间展示当前权限范围内的账号</p>
                    </div>
                    <a href="{{ \App\Filament\Resources\UserManagement\Users\UserResource::getUrl( 'index' ) }}">
                        查看全部<x-filament::icon icon="heroicon-o-arrow-right" />
                    </a>
                </header>
                <div class="unified-dashboard-user-list">
                    @forelse ( $this->getRecentUsers() as $user )
                        @php
                            $avatarUrl = filled( $user->avatar ) && Storage::disk( 'public' )->exists( $user->avatar )
                                ? Storage::disk( 'public' )->url( $user->avatar )
                                : null;
                            $canEdit = !$this->isAgent() || $user->level < \App\Models\User::LEVEL_USER;
                        @endphp
                        <{{ $canEdit ? 'a' : 'div' }}
                            class="unified-dashboard-user {{ !$canEdit ? 'is-readonly' : '' }}"
                            @if ( $canEdit ) href="{{ \App\Filament\Resources\UserManagement\Users\UserResource::getUrl( 'edit', ['record' => $user] ) }}" @endif
                        >
                            <span class="unified-dashboard-user-avatar">
                                @if ( $avatarUrl )
                                    <img src="{{ $avatarUrl }}" alt="{{ $user->name ?: '用户头像' }}">
                                @else
                                    {{ mb_strtoupper( mb_substr( $user->nickname ?: $user->name ?: 'U', 0, 1 ) ) }}
                                @endif
                            </span>
                            <span class="unified-dashboard-user-name">
                                <strong>{{ $user->nickname ?: $user->name ?: '未命名用户' }}</strong>
                                <small>{{ $user->email ?: \App\Models\User::formatPhoneForDisplay( $user->phone ) ?: '暂无联系方式' }}</small>
                            </span>
                            <span class="unified-dashboard-user-level">{{ $user->level }} · {{ \App\Models\User::getLevel( $user->level ) }}</span>
                            <span class="unified-dashboard-user-status {{ $user->status ? 'is-active' : '' }}">{{ $user->status ? '启用' : '停用' }}</span>
                            <time>{{ $user->created_at?->format( 'm.d H:i' ) }}</time>
                            @if ( $canEdit ) <x-filament::icon icon="heroicon-o-chevron-right" /> @endif
                        </{{ $canEdit ? 'a' : 'div' }}>
                    @empty
                        <div class="unified-dashboard-empty">
                            <x-filament::icon icon="heroicon-o-user-plus" />
                            <strong>当前还没有用户</strong>
                            <span>{{ $this->isAgent() ? '新增用户后将自动归属到您的名下。' : '创建第一个用户后会显示在这里。' }}</span>
                        </div>
                    @endforelse
                </div>
            </section>

            <aside class="unified-dashboard-side">
                {{-- 根据实际访问权限生成快捷入口 --}}
                <section class="unified-dashboard-card">
                    <header class="unified-dashboard-card-header">
                        <div><h3>快捷入口</h3><p>仅展示当前账号可访问的功能</p></div>
                    </header>
                    <nav class="unified-dashboard-links">
                        @foreach ( $this->getQuickLinks() as $link )
                            <a href="{{ $link['url'] }}">
                                <span><x-filament::icon :icon="$link['icon']" /></span>
                                <span><strong>{{ $link['label'] }}</strong><small>{{ $link['description'] }}</small></span>
                                <x-filament::icon icon="heroicon-o-arrow-up-right" />
                            </a>
                        @endforeach
                    </nav>
                </section>

                {{-- 当前账号权限提示 --}}
                <section class="unified-dashboard-scope">
                    <span><x-filament::icon :icon="$this->isAgent() ? 'heroicon-o-user' : 'heroicon-o-shield-check'" /></span>
                    <div>
                        <small>当前身份</small>
                        <strong>{{ $this->isAgent() ? '代理' : '高级管理员' }}</strong>
                        <p>{{ $this->isAgent() ? '用户统计、最近用户和通知数据均已限制为您名下的数据。' : '您可以查看全部用户数据，具体功能仍受菜单等级权限控制。' }}</p>
                    </div>
                </section>
            </aside>
        </div>
    </div>

    <style>
        /* 通用仪表盘基础布局 */
        .unified-dashboard {
            --dashboard-card: white;
            --dashboard-border: var(--gray-200);
            --dashboard-text: var(--gray-950);
            --dashboard-muted: var(--gray-500);
            display: grid;
            color: var(--dashboard-text);
            gap: 1.25rem;
        }
        .dark .unified-dashboard {
            --dashboard-card: var(--gray-900);
            --dashboard-border: var(--gray-700);
            --dashboard-text: white;
            --dashboard-muted: var(--gray-400);
        }

        /* 欢迎信息 */
        .unified-dashboard-hero {
            display: flex;
            min-height: 11rem;
            padding: 2rem;
            overflow: hidden;
            border: 1px solid color-mix(in srgb, var(--primary-500) 24%, transparent);
            border-radius: 1.25rem;
            background: linear-gradient(120deg, color-mix(in srgb, var(--primary-500) 9%, white), color-mix(in srgb, var(--primary-500) 16%, white));
            box-shadow: 0 1rem 2.5rem color-mix(in srgb, var(--primary-600) 8%, transparent);
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            position: relative;
        }
        .dark .unified-dashboard-hero { background: linear-gradient(120deg, color-mix(in srgb, var(--primary-500) 10%, var(--gray-900)), color-mix(in srgb, var(--primary-500) 18%, var(--gray-900))); }
        .unified-dashboard-profile { display: flex; min-width: 0; align-items: center; gap: 1.25rem; z-index: 1; }
        .unified-dashboard-avatar {
            display: grid;
            width: 4.5rem;
            height: 4.5rem;
            overflow: hidden;
            border: 0.25rem solid color-mix(in srgb, white 75%, transparent);
            border-radius: 1.3rem;
            background: linear-gradient(135deg, var(--primary-500), var(--primary-700));
            box-shadow: 0 0.8rem 1.8rem color-mix(in srgb, var(--primary-700) 22%, transparent);
            color: white;
            flex: 0 0 auto;
            font-size: 1.5rem;
            font-weight: 750;
            place-items: center;
        }
        .unified-dashboard-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .unified-dashboard-date { color: var(--primary-600); font-size: 0.72rem; font-weight: 750; letter-spacing: 0.06em; }
        .unified-dashboard-welcome h2 { margin: 0.3rem 0; font-size: clamp(1.45rem, 3vw, 2rem); font-weight: 800; line-height: 1.2; }
        .unified-dashboard-welcome p { margin: 0; color: var(--dashboard-muted); font-size: 0.85rem; }
        .unified-dashboard-create {
            display: inline-flex;
            padding: 0.8rem 1.1rem;
            border-radius: 0.75rem;
            background: var(--primary-600);
            box-shadow: 0 0.6rem 1.2rem color-mix(in srgb, var(--primary-700) 24%, transparent);
            color: white;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.82rem;
            font-weight: 700;
            z-index: 1;
        }
        .unified-dashboard-create:hover { background: var(--primary-700); }
        .unified-dashboard-create svg { width: 1.05rem; height: 1.05rem; }
        .unified-dashboard-orbit { width: 12rem; height: 12rem; border: 2.5rem solid color-mix(in srgb, var(--primary-500) 9%, transparent); border-radius: 50%; right: -3rem; bottom: -7rem; position: absolute; }

        /* 数据统计 */
        .unified-dashboard-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; }
        .unified-dashboard-stat {
            --stat-color: var(--primary-600);
            display: flex;
            min-width: 0;
            padding: 1.15rem;
            border: 1px solid var(--dashboard-border);
            border-radius: 1rem;
            background: var(--dashboard-card);
            box-shadow: 0 0.3rem 1rem color-mix(in srgb, var(--gray-950) 5%, transparent);
            align-items: center;
            gap: 0.9rem;
        }
        .unified-dashboard-stat[data-tone="success"] { --stat-color: var(--success-600); }
        .unified-dashboard-stat[data-tone="danger"] { --stat-color: var(--danger-600); }
        .unified-dashboard-stat[data-tone="warning"] { --stat-color: var(--warning-600); }
        .unified-dashboard-stat-icon { display: grid; width: 3rem; height: 3rem; border-radius: 0.85rem; background: color-mix(in srgb, var(--stat-color) 11%, transparent); color: var(--stat-color); flex: 0 0 auto; place-items: center; }
        .unified-dashboard-stat-icon svg { width: 1.45rem; height: 1.45rem; }
        .unified-dashboard-stat > div { display: grid; min-width: 0; }
        .unified-dashboard-stat span { color: var(--dashboard-muted); font-size: 0.74rem; font-weight: 650; }
        .unified-dashboard-stat strong { margin: 0.12rem 0; font-size: 1.6rem; line-height: 1.1; }
        .unified-dashboard-stat small { overflow: hidden; color: var(--dashboard-muted); font-size: 0.68rem; text-overflow: ellipsis; white-space: nowrap; }

        /* 内容卡片 */
        .unified-dashboard-layout { display: grid; grid-template-columns: minmax(0, 1.55fr) minmax(18rem, 0.75fr); align-items: start; gap: 1.25rem; }
        .unified-dashboard-side { display: grid; gap: 1.25rem; }
        .unified-dashboard-card { overflow: hidden; border: 1px solid var(--dashboard-border); border-radius: 1rem; background: var(--dashboard-card); box-shadow: 0 0.3rem 1rem color-mix(in srgb, var(--gray-950) 5%, transparent); }
        .unified-dashboard-card-header { display: flex; min-height: 4.75rem; padding: 1rem 1.25rem; border-bottom: 1px solid var(--dashboard-border); align-items: center; justify-content: space-between; gap: 1rem; }
        .unified-dashboard-card-header h3 { margin: 0; font-size: 1rem; font-weight: 750; }
        .unified-dashboard-card-header p { margin: 0.18rem 0 0; color: var(--dashboard-muted); font-size: 0.72rem; }
        .unified-dashboard-card-header > a { display: inline-flex; color: var(--primary-600); align-items: center; gap: 0.3rem; font-size: 0.75rem; font-weight: 650; white-space: nowrap; }
        .unified-dashboard-card-header > a svg { width: 0.9rem; height: 0.9rem; }

        /* 最近用户 */
        .unified-dashboard-user-list { display: grid; min-height: 26rem; align-content: start; }
        .unified-dashboard-user { display: grid; min-height: 4.4rem; padding: 0.7rem 1.25rem; border-bottom: 1px solid var(--dashboard-border); color: inherit; grid-template-columns: auto minmax(8rem, 1fr) auto auto auto auto; align-items: center; gap: 0.75rem; }
        .unified-dashboard-user:last-child { border-bottom: 0; }
        a.unified-dashboard-user:hover { background: color-mix(in srgb, var(--primary-500) 5%, transparent); }
        .unified-dashboard-user.is-readonly { cursor: default; }
        .unified-dashboard-user-avatar { display: grid; width: 2.5rem; height: 2.5rem; overflow: hidden; border-radius: 0.75rem; background: color-mix(in srgb, var(--primary-500) 14%, transparent); color: var(--primary-700); font-size: 0.82rem; font-weight: 750; place-items: center; }
        .unified-dashboard-user-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .unified-dashboard-user-name { display: grid; min-width: 0; }
        .unified-dashboard-user-name strong,
        .unified-dashboard-user-name small { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .unified-dashboard-user-name strong { font-size: 0.82rem; }
        .unified-dashboard-user-name small,
        .unified-dashboard-user time { color: var(--dashboard-muted); font-size: 0.68rem; }
        .unified-dashboard-user-level,
        .unified-dashboard-user-status { padding: 0.28rem 0.5rem; border-radius: 9999px; background: var(--gray-100); color: var(--gray-600); font-size: 0.66rem; font-weight: 650; white-space: nowrap; }
        .dark .unified-dashboard-user-level,
        .dark .unified-dashboard-user-status { background: var(--gray-800); color: var(--gray-300); }
        .unified-dashboard-user-status.is-active { background: color-mix(in srgb, var(--success-500) 13%, transparent); color: var(--success-700); }
        .unified-dashboard-user > svg { width: 0.9rem; height: 0.9rem; color: var(--gray-400); }
        .unified-dashboard-empty { display: grid; min-height: 25rem; padding: 2rem; color: var(--dashboard-muted); text-align: center; place-content: center; justify-items: center; gap: 0.4rem; }
        .unified-dashboard-empty svg { width: 2rem; height: 2rem; color: var(--primary-500); }
        .unified-dashboard-empty strong { color: var(--dashboard-text); font-size: 0.85rem; }
        .unified-dashboard-empty span { font-size: 0.72rem; }

        /* 快捷入口与身份范围 */
        .unified-dashboard-links { display: grid; padding: 1rem; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.75rem; }
        .unified-dashboard-links a { display: grid; min-width: 0; min-height: 5rem; padding: 0.85rem; border: 1px solid var(--dashboard-border); border-radius: 0.8rem; color: inherit; grid-template-columns: auto minmax(0, 1fr) auto; align-items: center; gap: 0.65rem; }
        .unified-dashboard-links a:hover { border-color: color-mix(in srgb, var(--primary-500) 35%, var(--dashboard-border)); background: color-mix(in srgb, var(--primary-500) 4%, transparent); }
        .unified-dashboard-links a > span:first-child { display: grid; width: 2.35rem; height: 2.35rem; border-radius: 0.7rem; background: color-mix(in srgb, var(--primary-500) 10%, transparent); color: var(--primary-600); place-items: center; }
        .unified-dashboard-links a > span:first-child svg { width: 1.15rem; height: 1.15rem; }
        .unified-dashboard-links a > span:nth-child(2) { display: grid; min-width: 0; }
        .unified-dashboard-links strong,
        .unified-dashboard-links small { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .unified-dashboard-links strong { font-size: 0.75rem; }
        .unified-dashboard-links small { color: var(--dashboard-muted); font-size: 0.64rem; }
        .unified-dashboard-links a > svg { width: 0.8rem; height: 0.8rem; color: var(--gray-400); }
        .unified-dashboard-scope { display: flex; padding: 1.25rem; border: 1px solid color-mix(in srgb, var(--primary-500) 20%, transparent); border-radius: 1rem; background: color-mix(in srgb, var(--primary-500) 6%, var(--dashboard-card)); gap: 0.9rem; }
        .unified-dashboard-scope > span { display: grid; width: 2.6rem; height: 2.6rem; border-radius: 0.8rem; background: var(--primary-600); color: white; flex: 0 0 auto; place-items: center; }
        .unified-dashboard-scope > span svg { width: 1.25rem; height: 1.25rem; }
        .unified-dashboard-scope div { display: grid; }
        .unified-dashboard-scope small { color: var(--dashboard-muted); font-size: 0.65rem; }
        .unified-dashboard-scope strong { margin-top: 0.05rem; font-size: 0.9rem; }
        .unified-dashboard-scope p { margin: 0.45rem 0 0; color: var(--dashboard-muted); font-size: 0.7rem; line-height: 1.6; }

        /* 响应式布局 */
        @media (max-width: 1200px) {
            .unified-dashboard-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .unified-dashboard-layout { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
            .unified-dashboard-hero { padding: 1.4rem; align-items: flex-start; flex-direction: column; }
            .unified-dashboard-create { align-self: stretch; justify-content: center; }
            .unified-dashboard-user { grid-template-columns: auto minmax(0, 1fr) auto; }
            .unified-dashboard-user-level,
            .unified-dashboard-user time { display: none; }
        }
        @media (max-width: 520px) {
            .unified-dashboard { gap: 1rem; }
            .unified-dashboard-profile { align-items: flex-start; }
            .unified-dashboard-avatar { width: 3.5rem; height: 3.5rem; border-radius: 1rem; }
            .unified-dashboard-stats { grid-template-columns: 1fr; }
            .unified-dashboard-links { grid-template-columns: 1fr; }
            .unified-dashboard-user { padding-inline: 1rem; }
            .unified-dashboard-user-status { display: none; }
        }
    </style>
</x-filament-panels::page>
