{{-- Workerman 命令行状态输出 --}}
<pre class="service-management-status-output">{{ $output }}</pre>

<style>
    /* 服务状态弹窗命令行内容 */
    .service-management-status-output {
        min-height: 16rem;
        max-height: 60vh;
        margin: 0;
        padding: 1rem;
        overflow: auto;
        border-radius: 0.5rem;
        background: #111827;
        color: #e5e7eb;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.8125rem;
        line-height: 1.6;
        white-space: pre;
    }
</style>
