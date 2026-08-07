@php
    $queueWorkerRunning = getQueueWorkerStatus();
@endphp

{{-- Laravel Tool 管理页面示例 --}}
<div class="laravel-tool-demo">
    <section class="laravel-tool-hero">
        <div>
            <span class="laravel-tool-label">Laravel 开发工具</span>
            <h2>{{ $plugin->name ?? 'Laravel Tool' }}</h2>
            <p>集中查看项目环境和常用命令。</p>
        </div>
        <span class="laravel-tool-version">v{{ $plugin->version ?? '1.0.0' }}</span>
    </section>

    {{-- 环境信息 --}}
    <section class="laravel-tool-section">
        <div class="laravel-tool-heading">
            <h3>运行环境</h3>
            <span class="laravel-tool-status" data-status="{{ $queueWorkerRunning ? 'running' : 'stopped' }}">
                <i></i>
                {{ $queueWorkerRunning ? '队列工作进程运行中' : '队列工作进程未运行' }}
            </span>
        </div>
        <div class="laravel-tool-stats">
            <article>
                <span>Laravel</span>
                <strong>{{ app()->version() }}</strong>
            </article>
            <article>
                <span>PHP</span>
                <strong>{{ PHP_VERSION }}</strong>
            </article>
            <article>
                <span>当前环境</span>
                <strong>{{ app()->environment() }}</strong>
            </article>
        </div>
    </section>

    {{-- 常用命令 --}}
    @php
        $commandGroups = [
            '系统信息' => [
                ['key' => 'environment', 'command' => 'env', 'description' => '查看当前运行环境'],
                ['key' => 'database-show', 'command' => 'db:show', 'description' => '查看数据库概况'],
                ['key' => 'event-list', 'command' => 'event:list', 'description' => '查看事件与监听器'],
                ['key' => 'route-list', 'command' => 'route:list', 'description' => '查看路由列表'],
            ],
            '缓存与优化' => [
                ['key' => 'optimize', 'command' => 'optimize', 'description' => '生成框架优化缓存'],
                ['key' => 'clear-cache', 'command' => 'optimize:clear', 'description' => '清除全部框架缓存'],
                ['key' => 'application-cache-clear', 'command' => 'cache:clear', 'description' => '清除应用缓存'],
                ['key' => 'config-cache', 'command' => 'config:cache', 'description' => '生成配置缓存'],
                ['key' => 'config-clear', 'command' => 'config:clear', 'description' => '清除配置缓存'],
                ['key' => 'event-cache', 'command' => 'event:cache', 'description' => '生成事件缓存'],
                ['key' => 'event-clear', 'command' => 'event:clear', 'description' => '清除事件缓存'],
                ['key' => 'route-cache', 'command' => 'route:cache', 'description' => '生成路由缓存'],
                ['key' => 'route-clear', 'command' => 'route:clear', 'description' => '清除路由缓存'],
                ['key' => 'view-cache', 'command' => 'view:cache', 'description' => '预编译 Blade 视图'],
                ['key' => 'view-clear', 'command' => 'view:clear', 'description' => '清除视图缓存'],
            ],
            '数据库' => [
                ['key' => 'migrate-status', 'command' => 'migrate:status', 'description' => '查看数据库迁移状态'],
                ['key' => 'migrate', 'command' => 'migrate', 'description' => '运行数据库迁移'],
                ['key' => 'migrate-rollback', 'command' => 'migrate:rollback', 'description' => '回滚上一批迁移', 'confirm' => '确认回滚上一批数据库迁移？'],
                ['key' => 'migrate-fresh', 'command' => 'migrate:fresh', 'description' => '重建全部数据表', 'confirm' => '此操作会删除全部数据表并重建，确认继续？'],
                ['key' => 'database-seed', 'command' => 'db:seed', 'description' => '运行数据填充'],
            ],
            '队列与计划任务' => [
                ['key' => 'queue-failed', 'command' => 'queue:failed', 'description' => '查看失败队列任务'],
                ['key' => 'queue-restart', 'command' => 'queue:restart', 'description' => '通知队列进程重启'],
                ['key' => 'schedule-list', 'command' => 'schedule:list', 'description' => '查看计划任务列表'],
                ['key' => 'queue-work', 'command' => 'queue:work', 'description' => '启动队列处理进程'],
                ['key' => 'queue-listen', 'command' => 'queue:listen', 'description' => '监听队列任务'],
                ['key' => 'schedule-run', 'command' => 'schedule:run', 'description' => '运行到期计划任务'],
                ['key' => 'schedule-work', 'command' => 'schedule:work', 'description' => '启动计划任务进程'],
            ],
            '代码生成' => [
                ['key' => 'make-service', 'command' => 'make:service', 'description' => '生成 Service 类', 'input' => '例如 UserService'],
                ['key' => 'make-controller', 'command' => 'make:controller', 'description' => '生成控制器', 'input' => '例如 Admin/UserController'],
                ['key' => 'make-model', 'command' => 'make:model', 'description' => '生成模型', 'input' => '例如 UserProfile'],
                ['key' => 'make-migration', 'command' => 'make:migration', 'description' => '生成数据库迁移', 'input' => '例如 create_users_table'],
                ['key' => 'make-seeder', 'command' => 'make:seeder', 'description' => '生成数据填充器', 'input' => '例如 UserSeeder'],
                ['key' => 'make-factory', 'command' => 'make:factory', 'description' => '生成模型工厂', 'input' => '例如 UserFactory'],
                ['key' => 'make-middleware', 'command' => 'make:middleware', 'description' => '生成中间件', 'input' => '例如 EnsureUserActive'],
                ['key' => 'make-request', 'command' => 'make:request', 'description' => '生成表单请求', 'input' => '例如 StoreUserRequest'],
                ['key' => 'make-resource', 'command' => 'make:resource', 'description' => '生成 API 资源', 'input' => '例如 UserResource'],
                ['key' => 'make-command', 'command' => 'make:command', 'description' => '生成 Artisan 命令', 'input' => '例如 SendReport'],
                ['key' => 'make-job', 'command' => 'make:job', 'description' => '生成队列任务', 'input' => '例如 ProcessOrder'],
                ['key' => 'make-event', 'command' => 'make:event', 'description' => '生成事件类', 'input' => '例如 OrderCreated'],
                ['key' => 'make-listener', 'command' => 'make:listener', 'description' => '生成事件监听器', 'input' => '例如 SendOrderNotice'],
                ['key' => 'make-mail', 'command' => 'make:mail', 'description' => '生成邮件类', 'input' => '例如 WelcomeMail'],
                ['key' => 'make-notification', 'command' => 'make:notification', 'description' => '生成通知类', 'input' => '例如 InvoicePaid'],
                ['key' => 'make-policy', 'command' => 'make:policy', 'description' => '生成授权策略', 'input' => '例如 UserPolicy'],
                ['key' => 'make-provider', 'command' => 'make:provider', 'description' => '生成服务提供者', 'input' => '例如 PaymentServiceProvider'],
                ['key' => 'make-rule', 'command' => 'make:rule', 'description' => '生成验证规则', 'input' => '例如 Uppercase'],
                ['key' => 'make-test', 'command' => 'make:test', 'description' => '生成测试类', 'input' => '例如 UserTest'],
            ],
            '开发与维护' => [
                ['key' => 'test', 'command' => 'test', 'description' => '运行项目测试'],
                ['key' => 'tinker', 'command' => 'tinker', 'description' => '启动交互式终端'],
                ['key' => 'storage-link', 'command' => 'storage:link', 'description' => '创建公开存储链接'],
                ['key' => 'down', 'command' => 'down', 'description' => '进入维护模式', 'confirm' => '确认让应用进入维护模式？'],
                ['key' => 'up', 'command' => 'up', 'description' => '退出维护模式'],
            ],
        ];
    @endphp
    <section class="laravel-tool-section">
        <div class="laravel-tool-heading">
            <h3>常用命令</h3>
            <span>调试模式 | {{ config('app.debug') ? '开启' : '关闭' }}</span>
        </div>
        <div
            class="laravel-tool-commands"
            x-data="{
                running: null,
                result: '',
                status: '',
                resultOpen: false,
                async run( command, name = '' ) {
                    if ( this.running !== null ) { return; }
                    if ( name !== null && name.trim() === '' ) {
                        this.status = 'error';
                        this.result = '请先填写要生成的名称。';
                        this.resultOpen = true;
                        return;
                    }
                    this.running = command;
                    this.result = '';
                    this.status = '';
                    try {
                        const response = await fetch( '{{ route( 'plugins.laravel-tool.run-command' ) }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.$refs.token.value,
                            },
                            body: JSON.stringify( { command, name } ),
                        } );
                        const data = await response.json();
                        this.status = response.ok ? 'success' : 'error';
                        this.result = data.data?.output || data.data?.message || data.message || '操作已完成。';
                        this.resultOpen = true;
                    }catch ( error ) {
                        this.status = 'error';
                        this.result = '请求失败，请稍后重试。';
                        this.resultOpen = true;
                    }finally {
                        this.running = null;
                    }
                },
            }"
        >
            <input x-ref="token" type="hidden" value="{{ csrf_token() }}">
            @foreach ( $commandGroups as $group => $commands )
                <h4 class="laravel-tool-command-group">{{ $group }}</h4>
                @foreach ( $commands as $item )
                    <div class="laravel-tool-command-row {{ $loop->first ? 'is-first' : '' }}">
                        <code>php artisan {{ $item['command'] }}</code>
                        <span>{{ $item['description'] }}</span>
                        @if ( isset( $item['input'] ) )
                            <input
                                x-ref="name-{{ $item['key'] }}"
                                type="text"
                                maxlength="160"
                                placeholder="{{ $item['input'] }}"
                                autocomplete="off"
                            >
                        @endif
                        <button
                            type="button"
                            x-on:click="{{ isset( $item['confirm'] ) ? 'if ( ! window.confirm( '.Illuminate\Support\Js::from( $item['confirm'] ).' ) ) return; ' : '' }}run( '{{ $item['key'] }}', {{ isset( $item['input'] ) ? '$refs[\'name-'.$item['key'].'\'].value' : 'null' }} )"
                            x-bind:disabled="running !== null"
                            x-text="running === '{{ $item['key'] }}' ? '运行中…' : '运行'"
                        >运行</button>
                    </div>
                @endforeach
            @endforeach
            {{-- 命令执行结果弹窗 --}}
            <template x-teleport="body">
                <div
                    class="laravel-tool-result-overlay"
                    x-cloak
                    x-show="resultOpen"
                    x-transition.opacity
                    x-on:keydown.escape.window="resultOpen = false"
                    role="dialog"
                    aria-modal="true"
                >
                    <div class="laravel-tool-result-modal" x-on:click.outside="resultOpen = false">
                        <header>
                            <div>
                                <span class="laravel-tool-result-icon" x-bind:data-status="status" x-text="status === 'success' ? '✓' : '!'">✓</span>
                                <h3 x-text="status === 'success' ? '命令执行完成' : '操作失败'">命令执行完成</h3>
                            </div>
                            <button type="button" x-on:click="resultOpen = false" aria-label="关闭结果弹窗">×</button>
                        </header>
                        <pre x-text="result"></pre>
                        <footer>
                            <button type="button" x-on:click="resultOpen = false">关闭</button>
                        </footer>
                    </div>
                </div>
            </template>
        </div>
    </section>

</div>

<style>
    /* Laravel Tool 页面布局 */
    .laravel-tool-demo {
        display: flex;
        width: 100%;
        flex-direction: column;
        gap: 1rem;
        color: #111827;
    }
    .laravel-tool-hero {
        display: flex;
        padding: 1.25rem;
        border: 1px solid color-mix(in srgb, var(--primary-500) 20%, transparent);
        border-radius: 0.875rem;
        background: linear-gradient(135deg, color-mix(in srgb, var(--primary-500) 12%, transparent), color-mix(in srgb, var(--primary-400) 4%, transparent));
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
    }
    .laravel-tool-label {
        color: var(--primary-600);
        font-size: 0.6875rem;
        font-weight: 700;
        letter-spacing: 0.08em;
    }
    .laravel-tool-hero h2 {
        margin-top: 0.25rem;
        font-size: 1.25rem;
        font-weight: 700;
    }
    .laravel-tool-hero p {
        margin-top: 0.35rem;
        color: #6b7280;
        font-size: 0.8125rem;
    }
    .laravel-tool-version {
        padding: 0.3rem 0.55rem;
        border-radius: 999px;
        background: color-mix(in srgb, var(--primary-500) 12%, transparent);
        color: var(--primary-700);
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }
    /* 信息区块 */
    .laravel-tool-section {
        padding: 1rem;
        border: 1px solid rgba(107, 114, 128, 0.16);
        border-radius: 0.75rem;
    }
    .laravel-tool-heading {
        display: flex;
        margin-bottom: 0.875rem;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }
    .laravel-tool-heading h3 {
        font-size: 0.875rem;
        font-weight: 700;
    }
    .laravel-tool-heading > span {
        color: #9ca3af;
        font-size: 0.6875rem;
    }
    .laravel-tool-heading .laravel-tool-status {
        display: inline-flex;
        color: #059669;
        align-items: center;
        gap: 0.35rem;
    }
    .laravel-tool-status i {
        width: 0.45rem;
        height: 0.45rem;
        border-radius: 999px;
        background: #10b981;
    }
    .laravel-tool-heading .laravel-tool-status[data-status="stopped"] {
        color: #dc2626;
    }
    .laravel-tool-status[data-status="stopped"] i {
        background: #ef4444;
    }
    .laravel-tool-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.75rem;
    }
    .laravel-tool-stats article {
        padding: 0.875rem;
        border-radius: 0.625rem;
        background: rgba(107, 114, 128, 0.07);
    }
    .laravel-tool-stats span,
    .laravel-tool-stats strong {
        display: block;
    }
    .laravel-tool-stats span {
        color: #9ca3af;
        font-size: 0.6875rem;
    }
    .laravel-tool-stats strong {
        overflow: hidden;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    /* 命令列表 */
    .laravel-tool-commands {
        display: flex;
        flex-direction: column;
    }
    .laravel-tool-command-group {
        margin-top: 1rem;
        margin-bottom: 0.5rem;
        padding: 0.5rem 0.625rem;
        border-radius: 0.5rem;
        background: color-mix(in srgb, var(--primary-500) 8%, transparent);
        color: var(--primary-700);
        font-size: 0.75rem;
        font-weight: 700;
    }
    .laravel-tool-command-group:first-of-type {
        margin-top: 0;
    }
    .laravel-tool-command-row {
        display: flex;
        min-width: 0;
        padding: 0.75rem 0;
        border-top: 1px solid rgba(107, 114, 128, 0.12);
        align-items: center;
        gap: 0.75rem;
    }
    .laravel-tool-command-row.is-first {
        padding-top: 0;
        border-top: 0;
    }
    .laravel-tool-commands code {
        padding: 0.3rem 0.45rem;
        border-radius: 0.35rem;
        background: rgba(107, 114, 128, 0.1);
        color: var(--primary-600);
        font-size: 0.75rem;
        overflow-wrap: anywhere;
    }
    .laravel-tool-commands span {
        margin-left: auto;
        color: #6b7280;
        font-size: 0.75rem;
        text-align: right;
    }
    .laravel-tool-commands input {
        width: 11rem;
        padding: 0.4rem 0.55rem;
        border: 1px solid rgba(107, 114, 128, 0.22);
        border-radius: 0.5rem;
        background: transparent;
        color: inherit;
        font-size: 0.75rem;
        outline: none;
    }
    .laravel-tool-commands input:focus {
        border-color: var(--primary-500);
        box-shadow: 0 0 0 2px color-mix(in srgb, var(--primary-500) 12%, transparent);
    }
    .laravel-tool-commands button {
        padding: 0.4rem 0.75rem;
        border-radius: 0.5rem;
        background: var(--primary-600);
        color: #ffffff;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        flex: 0 0 auto;
    }
    .laravel-tool-commands button:disabled {
        cursor: wait;
        opacity: 0.6;
    }
    .laravel-tool-commands button:not(:disabled):hover {
        background: var(--primary-700);
    }
    /* 命令执行结果弹窗 */
    .laravel-tool-result-overlay {
        display: flex;
        padding: 1rem;
        background: rgba(17, 24, 39, 0.55);
        align-items: center;
        justify-content: center;
        position: fixed;
        inset: 0;
        z-index: 100;
    }
    .laravel-tool-result-modal {
        width: min(42rem, 100%);
        max-height: min(36rem, calc(100dvh - 2rem));
        overflow: hidden;
        border-radius: 0.875rem;
        background: #ffffff;
        color: #111827;
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.25);
    }
    .laravel-tool-result-modal header,
    .laravel-tool-result-modal footer {
        display: flex;
        padding: 1rem 1.25rem;
        align-items: center;
    }
    .laravel-tool-result-modal header {
        border-bottom: 1px solid rgba(107, 114, 128, 0.16);
        justify-content: space-between;
    }
    .laravel-tool-result-modal header > div {
        display: flex;
        align-items: center;
        gap: 0.625rem;
    }
    .laravel-tool-result-modal header h3 {
        font-size: 0.9375rem;
        font-weight: 700;
    }
    .laravel-tool-result-modal header > button {
        color: #6b7280;
        font-size: 1.5rem;
        line-height: 1;
        cursor: pointer;
    }
    .laravel-tool-result-icon {
        display: inline-flex;
        width: 1.75rem;
        height: 1.75rem;
        border-radius: 999px;
        background: rgba(16, 185, 129, 0.12);
        color: #059669;
        font-weight: 700;
        align-items: center;
        justify-content: center;
    }
    .laravel-tool-result-icon[data-status="error"] {
        background: rgba(239, 68, 68, 0.12);
        color: #dc2626;
    }
    .laravel-tool-result-modal pre {
        max-width: calc(100% - 2.5rem);
        max-height: 24rem;
        overflow: auto;
        margin: 1rem 1.25rem 1rem;
        padding: 1.25rem;
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 0.625rem;
        background: #0f172a;
        color: #e2e8f0;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.75rem;
        line-height: 1.6;
        white-space: pre;
        overflow-wrap: normal;
        word-break: normal;
    }
    .laravel-tool-result-modal pre::selection {
        background: color-mix(in srgb, var(--primary-500) 55%, transparent);
        color: #ffffff;
    }
    .laravel-tool-result-modal footer {
        border-top: 1px solid rgba(107, 114, 128, 0.16);
        justify-content: flex-end;
    }
    .laravel-tool-result-modal footer button {
        padding: 0.45rem 0.9rem;
        border: 1px solid var(--primary-600);
        border-radius: 0.5rem;
        background: var(--primary-600);
        color: #ffffff;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
    }
    .laravel-tool-result-modal footer button:hover {
        background: var(--primary-700);
    }
    /* 深色模式与移动端 */
    .dark .laravel-tool-demo {
        color: #f3f4f6;
    }
    .dark .laravel-tool-hero p,
    .dark .laravel-tool-commands span {
        color: #9ca3af;
    }
    .dark .laravel-tool-result-modal {
        background: #18181b;
        color: #f4f4f5;
    }
    @media (max-width: 640px) {
        .laravel-tool-stats {
            grid-template-columns: 1fr;
        }
        .laravel-tool-command-row {
            align-items: flex-start;
            flex-direction: column;
        }
        .laravel-tool-commands span {
            margin-left: 0;
            text-align: left;
        }
        .laravel-tool-commands input {
            width: 100%;
        }
        .laravel-tool-result-modal pre {
            max-width: calc(100% - 1.5rem);
            margin: 0.75rem 0.75rem 0;
            padding: 1rem;
        }
    }
</style>
