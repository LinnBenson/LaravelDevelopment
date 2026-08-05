{{-- 插件申请的 Hook 列表 --}}
<div class="plugin-hook-request-list">
    @foreach ( $hooks as $name => $description )
        <div class="plugin-hook-request-item">
            <span class="plugin-hook-request-icon">
                <x-filament::icon icon="heroicon-o-link" />
            </span>
            <span>
                <strong>{{ $name }}</strong>
                <small>{{ $description }}</small>
            </span>
        </div>
    @endforeach
</div>

<style>
    /* 插件 Hook 申请列表 */
    .plugin-hook-request-list {
        display: flex;
        flex-direction: column;
        gap: 0.625rem;
    }
    .plugin-hook-request-item {
        display: flex;
        padding: 0.75rem;
        border: 1px solid rgba(245, 158, 11, 0.24);
        border-radius: 0.625rem;
        background: rgba(245, 158, 11, 0.06);
        align-items: center;
        gap: 0.75rem;
    }
    .plugin-hook-request-icon {
        display: inline-flex;
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 0.5rem;
        background: rgba(245, 158, 11, 0.14);
        color: #d97706;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
    }
    .plugin-hook-request-icon svg {
        width: 1.125rem;
        height: 1.125rem;
    }
    .plugin-hook-request-item > span:last-child {
        display: flex;
        min-width: 0;
        flex-direction: column;
    }
    .plugin-hook-request-item strong {
        overflow-wrap: anywhere;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.8125rem;
    }
    .plugin-hook-request-item small {
        margin-top: 0.2rem;
        color: #6b7280;
        font-size: 0.75rem;
    }
</style>
