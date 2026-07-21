{{-- 插件自定义管理页面容器 --}}
<div class="plugin-admin-content">
    {!! $content !!}
</div>

<style>
    /* 插件管理弹窗 */
    .plugin-admin-modal {
        max-height: calc(100dvh - 2rem);
        overflow: hidden;
    }
    .plugin-admin-modal .fi-modal-content {
        flex: 1 1 auto;
        min-width: 0;
        min-height: 0;
        overflow-x: hidden;
        overflow-y: auto;
        overscroll-behavior: contain;
        -webkit-overflow-scrolling: touch;
    }
    .plugin-admin-content {
        width: 100%;
        min-width: 0;
    }
    @media (max-width: 640px) {
        .plugin-admin-modal {
            max-height: calc(100dvh - 1rem);
        }
        .plugin-admin-modal .fi-modal-header {
            padding-inline: 1rem;
            padding-top: 1rem;
        }
        .plugin-admin-modal .fi-modal-content,
        .plugin-admin-modal .fi-modal-footer {
            padding-inline: 1rem;
        }
    }
</style>
