@php
    $isAdmin = $record->notifiable_type === \App\Models\AdminUser::class;
    $isRead = $record->read_at !== null;
    $additionalData = \Illuminate\Support\Arr::except( $record->data, ['title', 'body'] );
    if ( array_key_exists( 'actions', $additionalData ) ) {
        $actions = $additionalData['actions'];
        unset( $additionalData['actions'] );
        $additionalData['actions'] = $actions;
    }
    $dataLabels = [
        'actions' => '操作按钮',
        'color' => '整体颜色',
        'duration' => '持续时间',
        'icon' => '图标',
        'iconColor' => '图标颜色',
        'status' => '通知状态',
        'view' => '自定义视图',
        'viewData' => '视图数据',
        'format' => '数据格式',
    ];
    $formatDataValue = static function ( mixed $value ): string {
        if ( $value === null ) { return 'null'; }
        if ( is_bool( $value ) ) { return $value ? 'true' : 'false'; }
        if ( is_array( $value ) || is_object( $value ) ) {
            $json = json_encode( $value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
            return $json === false ? '[无法解析的数据]' : $json;
        }
        return (string) $value;
    };
@endphp

{{-- 通知信息详情 --}}
<article class="notification-information-details">
    {{-- 通知状态 --}}
    <header class="notification-information-details-header">
        <span class="notification-information-details-status {{ $isRead ? 'is-read' : 'is-unread' }}">
            <span></span>{{ $isRead ? '已读' : '未读' }}
        </span>
        <span class="notification-information-details-tag">{{ $isAdmin ? '管理员' : '用户' }}</span>
    </header>

    {{-- 接收及时间信息 --}}
    <dl class="notification-information-details-meta">
        <div>
            <dt>接收者</dt>
            <dd>{{ $notifiableName }}</dd>
        </div>
        <div>
            <dt>UID</dt>
            <dd>{{ $record->notifiable_id }}</dd>
        </div>
        <div>
            <dt>发送时间</dt>
            <dd>{{ $record->created_at?->format( 'Y.m.d H:i:s' ) ?? '--' }}</dd>
        </div>
        <div>
            <dt>阅读时间</dt>
            <dd>{{ $record->read_at?->format( 'Y.m.d H:i:s' ) ?? '--' }}</dd>
        </div>
    </dl>

    {{-- 通知正文 --}}
    <section class="notification-information-details-message">
        <pre>{{ $record->data['body'] ?? '--' }}</pre>
    </section>

    {{-- 通知附加数据 --}}
    <details class="notification-information-details-data">
        <summary>附加数据</summary>
        <div class="notification-information-details-data-content">
            @if ( $additionalData === [] )
                <p class="notification-information-details-empty">该通知没有附加数据。</p>
            @else
                <dl>
                    @foreach ( $additionalData as $key => $value )
                        <div class="{{ is_array( $value ) || is_object( $value ) ? 'is-wide' : '' }}">
                            <dt>{{ $dataLabels[$key] ?? $key }} <code>{{ $key }}</code></dt>
                            <dd><pre>{{ $formatDataValue( $value ) }}</pre></dd>
                        </div>
                    @endforeach
                </dl>
            @endif
        </div>
    </details>
</article>

<style>
    /* 通知信息详情弹窗 */
    .notification-information-details-modal {
        max-height: calc(100dvh - 2rem);
        overflow: hidden;
    }
    .notification-information-details-modal .fi-modal-content {
        flex: 1 1 auto;
        min-width: 0;
        min-height: 0;
        overflow-x: hidden;
        overflow-y: auto;
        overscroll-behavior: contain;
        -webkit-overflow-scrolling: touch;
    }
    .notification-information-details {
        display: flex;
        min-width: 0;
        flex-direction: column;
        gap: 1.25rem;
    }
    /* 通知状态 */
    .notification-information-details-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.4rem;
    }
    .notification-information-details-status,
    .notification-information-details-tag {
        display: inline-flex;
        min-height: 1.75rem;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .notification-information-details-status span {
        width: 0.42rem;
        height: 0.42rem;
        border-radius: 9999px;
        background: currentColor;
    }
    .notification-information-details-status.is-read {
        background: color-mix(in srgb, var(--success-500) 12%, transparent);
        color: var(--success-700);
    }
    .notification-information-details-status.is-unread {
        background: color-mix(in srgb, var(--danger-500) 12%, transparent);
        color: var(--danger-700);
    }
    .notification-information-details-tag {
        background: var(--gray-100);
        color: var(--gray-600);
    }
    .dark .notification-information-details-tag {
        background: var(--gray-800);
        color: var(--gray-300);
    }
    /* 通知正文 */
    .notification-information-details-message {
        min-width: 0;
        padding: 1.25rem;
        border: 1px solid var(--gray-200);
        border-radius: 0.875rem;
        background: var(--gray-50);
    }
    .dark .notification-information-details-message {
        border-color: var(--gray-700);
        background: var(--gray-900);
    }
    .notification-information-details-message pre {
        margin: 0;
        color: var(--gray-900);
        font-family: inherit;
        font-size: 0.95rem;
        line-height: 1.75;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }
    .dark .notification-information-details-message pre { color: var(--gray-100); }
    /* 通知附加数据 */
    .notification-information-details-data {
        min-width: 0;
        border: 1px solid var(--gray-200);
        border-radius: 0.875rem;
        background: white;
    }
    .dark .notification-information-details-data {
        border-color: var(--gray-700);
        background: var(--gray-900);
    }
    .notification-information-details-data summary {
        display: flex;
        padding: 1rem 1.25rem;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        color: var(--gray-900);
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        list-style: none;
    }
    .notification-information-details-data summary::-webkit-details-marker { display: none; }
    .notification-information-details-data summary::after {
        width: 0.5rem;
        height: 0.5rem;
        border-right: 2px solid var(--gray-500);
        border-bottom: 2px solid var(--gray-500);
        content: '';
        transform: rotate(45deg);
        transition: transform 150ms ease;
    }
    .notification-information-details-data[open] summary::after { transform: rotate(225deg); }
    .dark .notification-information-details-data summary { color: var(--gray-100); }
    .notification-information-details-data-content {
        padding: 1.25rem;
        border-top: 1px solid var(--gray-200);
    }
    .dark .notification-information-details-data-content { border-color: var(--gray-700); }
    .notification-information-details-data dl {
        display: grid;
        margin: 0;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.75rem;
    }
    .notification-information-details-data dl > div {
        min-width: 0;
        padding: 0.85rem;
        border: 1px solid var(--gray-200);
        border-radius: 0.75rem;
        background: var(--gray-50);
    }
    .notification-information-details-data dl > div.is-wide { grid-column: 1 / -1; }
    .dark .notification-information-details-data dl > div {
        border-color: var(--gray-700);
        background: var(--gray-900);
    }
    .notification-information-details-data dt {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 0.45rem;
        color: var(--gray-500);
        font-size: 0.75rem;
        font-weight: 600;
    }
    .notification-information-details-data dt code {
        padding: 0.1rem 0.35rem;
        border-radius: 0.3rem;
        background: var(--gray-200);
        color: var(--gray-600);
        font-size: 0.68rem;
        font-weight: 400;
        overflow-wrap: anywhere;
    }
    .dark .notification-information-details-data dt code {
        background: var(--gray-800);
        color: var(--gray-300);
    }
    .notification-information-details-data dd { margin: 0.45rem 0 0; }
    .notification-information-details-data pre {
        margin: 0;
        color: var(--gray-900);
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.75rem;
        line-height: 1.6;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }
    .dark .notification-information-details-data pre { color: var(--gray-100); }
    .notification-information-details-empty {
        margin: 0;
        color: var(--gray-500);
        font-size: 0.85rem;
    }
    /* 接收及时间信息 */
    .notification-information-details-meta {
        display: grid;
        padding: 1rem;
        border: 1px solid var(--gray-200);
        border-radius: 0.75rem;
        background: white;
        grid-template-columns: minmax(0, 2fr) minmax(5rem, 0.6fr) minmax(10rem, 1fr) minmax(10rem, 1fr);
        gap: 1rem;
    }
    .dark .notification-information-details-meta {
        border-color: var(--gray-700);
        background: var(--gray-900);
    }
    .notification-information-details-meta > div { min-width: 0; }
    .notification-information-details-meta dt {
        color: var(--gray-400);
        font-size: 0.7rem;
        font-weight: 600;
    }
    .notification-information-details-meta dd {
        margin-top: 0.3rem;
        color: var(--gray-600);
        font-size: 0.8rem;
        line-height: 1.45;
        overflow-wrap: anywhere;
    }
    .dark .notification-information-details-meta dd { color: var(--gray-300); }
    @media (max-width: 768px) {
        .notification-information-details-meta { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 640px) {
        .notification-information-details-modal { max-height: calc(100dvh - 1rem); }
        .notification-information-details-modal .fi-modal-header { padding-inline: 1rem; padding-top: 1rem; }
        .notification-information-details-modal .fi-modal-content,
        .notification-information-details-modal .fi-modal-footer { padding-inline: 1rem; }
        .notification-information-details { gap: 1rem; }
        .notification-information-details-message { padding: 1rem; }
        .notification-information-details-meta { grid-template-columns: 1fr; }
        .notification-information-details-data dl { grid-template-columns: 1fr; }
    }
</style>
