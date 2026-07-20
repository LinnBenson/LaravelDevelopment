<x-filament-panels::page>
    @php
        $services = $this->getServices();
        $serviceCount = count( $services );
        $runningCount = count( array_filter( $services, fn ( array $service ): bool => $service['running'] ) );
        $stoppedCount = $serviceCount - $runningCount;
    @endphp

    {{-- 服务状态汇总 --}}
    <div class="service-management-summary">
        <div class="service-management-summary-item is-total">
            <span class="service-management-summary-icon">
                <x-filament::icon icon="heroicon-o-server-stack" />
            </span>
            <span class="service-management-summary-content">
                <span>服务总数</span>
                <strong>{{ $serviceCount }}</strong>
            </span>
        </div>
        <div class="service-management-summary-item is-running">
            <span class="service-management-summary-icon">
                <x-filament::icon icon="heroicon-o-check-circle" />
            </span>
            <span class="service-management-summary-content">
                <span>运行中</span>
                <strong>{{ $runningCount }}</strong>
            </span>
        </div>
        <div class="service-management-summary-item is-stopped">
            <span class="service-management-summary-icon">
                <x-filament::icon icon="heroicon-o-stop-circle" />
            </span>
            <span class="service-management-summary-content">
                <span>已停止</span>
                <strong>{{ $stoppedCount }}</strong>
            </span>
        </div>
    </div>

    {{-- 服务项状态列表 --}}
    <div class="service-management-grid" wire:poll.2s>
        @forelse ( $services as $service )
            <x-filament::section
                wire:key="service-{{ $service['key'] }}"
                :heading="$service['name']"
                :description="$service['key'].' · '.$service['type']"
                icon="heroicon-o-server-stack"
            >
                <x-slot name="afterHeader">
                    <x-filament::icon-button
                        icon="heroicon-o-arrow-path"
                        color="gray"
                        :label="'刷新 '.$service['name'].' 状态'"
                        wire:click="refreshService('{{ $service['key'] }}')"
                        wire:loading.attr="disabled"
                        wire:target="refreshService('{{ $service['key'] }}')"
                    />
                </x-slot>
                <div class="service-management-card">
                    <div class="service-management-status">
                        <span class="service-management-status-dot {{ $service['running'] ? 'is-running' : 'is-stopped' }}"></span>
                        <span class="service-management-status-text {{ $service['running'] ? 'is-running' : 'is-stopped' }}">
                            {{ $service['running'] ? '运行中' : '已停止' }} [{{ $service['port'] ?? '-' }}]
                        </span>
                    </div>
                    <dl class="service-management-details">
                        <div>
                            <dt>监听地址</dt>
                            <dd>{{ $service['protocol'] }}://{{ $service['host'] }}{{ $service['port'] !== null ? ':'.$service['port'] : '' }}</dd>
                        </div>
                        <div>
                            <dt>进程数</dt>
                            <dd>{{ $service['threads'] }}</dd>
                        </div>
                        <div>
                            <dt>PID</dt>
                            <dd>{{ $service['pid'] ?? '-' }}</dd>
                        </div>
                    </dl>
                    <div class="service-management-actions">
                        <x-filament::button
                            size="sm"
                            color="success"
                            icon="heroicon-o-play"
                            wire:click="startService('{{ $service['key'] }}')"
                            :disabled="$service['running']"
                        >
                            启动
                        </x-filament::button>
                        <x-filament::button
                            size="sm"
                            color="warning"
                            icon="heroicon-o-arrow-path"
                            wire:click="restartService('{{ $service['key'] }}')"
                            :disabled="!$service['running']"
                        >
                            重启
                        </x-filament::button>
                        <x-filament::button
                            size="sm"
                            color="danger"
                            icon="heroicon-o-stop"
                            wire:click="stopService('{{ $service['key'] }}')"
                            :disabled="!$service['running']"
                        >
                            停止
                        </x-filament::button>
                        <x-filament::button
                            size="sm"
                            color="info"
                            icon="heroicon-o-command-line"
                            wire:click="showStatus('{{ $service['key'] }}')"
                        >
                            状态
                        </x-filament::button>
                        <x-filament::button
                            size="sm"
                            color="gray"
                            icon="heroicon-o-document-text"
                            wire:click="toggleLog('{{ $service['key'] }}')"
                            style="width: 100%;"
                        >
                            {{ ( $expandedLogs[$service['key']] ?? false ) ? '收起日志' : '查看日志' }}
                        </x-filament::button>
                    </div>
                    @if ( $expandedLogs[$service['key']] ?? false )
                        <div class="service-management-log-box" wire:key="service-log-{{ $service['key'] }}">
                            <div class="service-management-log-heading">
                                <span>{{ $service['key'] }}.log</span>
                                <span>最后 100 行</span>
                            </div>
                            <pre class="service-management-log">{{ $logContents[$service['key']] ?? '正在读取日志……' }}</pre>
                        </div>
                    @endif
                </div>
            </x-filament::section>
        @empty
            <x-filament::section heading="暂无服务项" icon="heroicon-o-server-stack">
                请先在 config/workerman.php 中添加服务配置。
            </x-filament::section>
        @endforelse
    </div>

    <style>
        /* 服务状态汇总信息 */
        .service-management-summary {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }
        .service-management-summary-item {
            display: flex;
            min-width: 0;
            padding: 1rem 1.25rem;
            border: 1px solid rgba(107, 114, 128, 0.2);
            border-radius: 0.75rem;
            background: rgba(255, 255, 255, 0.7);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            align-items: center;
            gap: 0.875rem;
        }
        .dark .service-management-summary-item {
            background: var(--gray-900);
        }
        .service-management-summary-icon {
            display: inline-flex;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.625rem;
            align-items: center;
            justify-content: center;
        }
        .service-management-summary-icon svg {
            width: 1.25rem;
            height: 1.25rem;
        }
        .service-management-summary-content {
            display: flex;
            min-width: 0;
            flex-direction: column;
        }
        .service-management-summary-content span {
            color: #6b7280;
            font-size: 0.75rem;
        }
        .service-management-summary-content strong {
            font-size: 1.5rem;
            line-height: 1.3;
        }
        .service-management-summary-item.is-total .service-management-summary-icon {
            background: rgba(59, 130, 246, 0.12);
            color: #3b82f6;
        }
        .service-management-summary-item.is-running .service-management-summary-icon {
            background: rgba(34, 197, 94, 0.12);
            color: #16a34a;
        }
        .service-management-summary-item.is-stopped .service-management-summary-icon {
            background: rgba(239, 68, 68, 0.12);
            color: #dc2626;
        }
        /* 服务管理卡片网格 */
        .service-management-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(min(100%, 22rem), 1fr));
            gap: 1rem;
            align-items: start;
        }
        .service-management-grid > section {
            width: 100%;
            max-width: 32rem;
        }
        .service-management-card {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        /* 服务状态及基础信息 */
        .service-management-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .service-management-status-dot {
            width: 0.625rem;
            height: 0.625rem;
            border-radius: 9999px;
        }
        .service-management-status-dot.is-running {
            background: #22c55e;
            box-shadow: 0 0 0 0.25rem rgba(34, 197, 94, 0.12);
        }
        .service-management-status-dot.is-stopped {
            background: #ef4444;
            box-shadow: 0 0 0 0.25rem rgba(239, 68, 68, 0.12);
        }
        .service-management-status-text {
            font-size: 0.875rem;
            font-weight: 600;
        }
        .service-management-status-text.is-running {
            color: #16a34a;
        }
        .service-management-status-text.is-stopped {
            color: #dc2626;
        }
        .service-management-details {
            display: grid;
            grid-template-columns: minmax(0, 2fr) repeat(2, minmax(4rem, 1fr));
            gap: 0.75rem;
        }
        .service-management-details div {
            min-width: 0;
            padding: 0.75rem;
            border-radius: 0.5rem;
            background: rgba(107, 114, 128, 0.08);
        }
        .service-management-details dt {
            margin-bottom: 0.25rem;
            color: #6b7280;
            font-size: 0.75rem;
        }
        .service-management-details dd {
            overflow: hidden;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.8125rem;
            font-weight: 600;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .service-management-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        /* 单个服务的折叠日志 */
        .service-management-log-box {
            padding-top: 1rem;
            border-top: 1px solid rgba(107, 114, 128, 0.2);
        }
        .service-management-log-heading {
            display: flex;
            margin-bottom: 0.5rem;
            color: #6b7280;
            font-size: 0.75rem;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .service-management-log {
            height: 12rem;
            margin: 0;
            padding: 1rem;
            overflow: auto;
            border-radius: 0.5rem;
            background: #111827;
            color: #e5e7eb;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.8125rem;
            line-height: 1.6;
            white-space: pre-wrap;
            word-break: break-word;
        }
        @media (max-width: 640px) {
            .service-management-summary {
                grid-template-columns: minmax(0, 1fr);
            }
            .service-management-details {
                grid-template-columns: minmax(0, 1fr);
            }
        }
    </style>
</x-filament-panels::page>
