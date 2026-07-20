<x-filament-panels::page>
    @php
        $iconData = $this->getIconData();
    @endphp

    <link rel="stylesheet" href="{{ asset( 'css/BootstrapIcons.css' ) }}">

    {{-- 图标搜索和分类 --}}
    <x-filament::section
        heading="Bootstrap Icons"
        description="动态显示 public/css/BootstrapIcons.css 中提供的全部可用图标。"
        icon="heroicon-o-squares-2x2"
    >
        <div class="bootstrap-icons-toolbar">
            <div class="bootstrap-icons-toolbar-search">
                <x-filament::icon icon="heroicon-o-magnifying-glass" />
                <input
                    type="search"
                    placeholder="搜索图标名称或类名"
                    wire:model.live.debounce.300ms="search"
                >
            </div>
            <div class="bootstrap-icons-toolbar-filters">
                @foreach ( ['all' => '全部', 'regular' => '常规', 'fill' => '实心'] as $style => $label )
                    <button
                        type="button"
                        class="bootstrap-icons-toolbar-filter{{ $iconStyle === $style ? ' is-active' : '' }}"
                        wire:click="setIconStyle('{{ $style }}')"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            <div class="bootstrap-icons-toolbar-count">
                共 {{ $iconData['total'] }} 个图标
            </div>
        </div>
    </x-filament::section>

    {{-- 图标展示网格 --}}
    @if ( $iconData['total'] === 0 )
        <x-filament::section>
            <div class="bootstrap-icons-empty">没有找到匹配的图标</div>
        </x-filament::section>
    @else
        <div
            class="bootstrap-icons-grid"
            x-data="{
                copyFallback(value) {
                    const input = document.createElement('textarea')
                    input.value = value
                    input.setAttribute('readonly', '')
                    input.style.position = 'fixed'
                    input.style.opacity = '0'
                    document.body.appendChild(input)
                    input.select()
                    document.execCommand('copy')
                    input.remove()
                },
                copyIcon(value) {
                    if (! window.navigator.clipboard) {
                        this.copyFallback(value)
                        return
                    }
                    window.navigator.clipboard.writeText(value).catch(() => this.copyFallback(value))
                },
            }"
        >
            @foreach ( $iconData['icons'] as $icon )
                <div wire:key="bootstrap-icon-{{ $icon['name'] }}" class="bootstrap-icons-grid-item">
                    <div class="bootstrap-icons-grid-item-preview">
                        <i class="{{ $icon['class'] }}" aria-hidden="true"></i>
                    </div>
                    <div class="bootstrap-icons-grid-item-name" title="{{ $icon['name'] }}">
                        {{ $icon['name'] }}
                    </div>
                    <button
                        type="button"
                        class="bootstrap-icons-grid-item-code fi-copyable"
                        title="点击复制 bi-{{ $icon['name'] }}"
                        x-on:click="
                            copyIcon({{ Illuminate\Support\Js::from( "bi-{$icon['name']}" ) }})
                            $tooltip('已复制', {
                                theme: $store.theme,
                                timeout: 2000,
                            })
                        "
                        x-on:keydown.enter.prevent="$el.click()"
                        x-on:keydown.space.prevent="$el.click()"
                    >
                        <code>bi-{{ $icon['name'] }}</code>
                    </button>
                    <span class="bootstrap-icons-grid-item-style">{{ $icon['style'] }}</span>
                </div>
            @endforeach
        </div>

    @endif

    <style>
        /* 图标页面搜索工具栏 */
        .bootstrap-icons-toolbar {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .bootstrap-icons-toolbar-search {
            display: flex;
            min-width: 16rem;
            padding: 0.625rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            align-items: center;
            gap: 0.5rem;
            flex: 1;
        }
        .bootstrap-icons-toolbar-search:focus-within {
            border-color: var(--primary-500);
            box-shadow: 0 0 0 1px var(--primary-500);
        }
        .bootstrap-icons-toolbar-search svg {
            width: 1.25rem;
            height: 1.25rem;
            color: #6b7280;
        }
        .bootstrap-icons-toolbar-search input {
            width: 100%;
            border: 0;
            outline: 0;
            background: transparent;
        }
        .bootstrap-icons-toolbar-filters {
            display: flex;
            padding: 0.25rem;
            border-radius: 0.5rem;
            background: rgba(107, 114, 128, 0.1);
            gap: 0.25rem;
        }
        .bootstrap-icons-toolbar-filter {
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            color: #6b7280;
            cursor: pointer;
        }
        .bootstrap-icons-toolbar-filter.is-active {
            background: var(--primary-500);
            color: #fff;
            font-weight: 600;
        }
        .bootstrap-icons-toolbar-count {
            color: #6b7280;
            white-space: nowrap;
        }
        /* 图标预览网格 */
        .bootstrap-icons-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(13rem, 1fr));
            gap: 0.75rem;
        }
        .bootstrap-icons-grid-item {
            display: flex;
            min-width: 0;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            background: rgba(255, 255, 255, 0.02);
            align-items: center;
            flex-direction: column;
            gap: 0.5rem;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .bootstrap-icons-grid-item:hover {
            border-color: var(--primary-500);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .bootstrap-icons-grid-item-preview {
            display: flex;
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 0.75rem;
            background: color-mix(in srgb, var(--primary-500) 12%, transparent);
            color: var(--primary-600);
            align-items: center;
            justify-content: center;
        }
        .bootstrap-icons-grid-item-preview i {
            font-size: 2rem;
            line-height: 1;
        }
        .bootstrap-icons-grid-item-name {
            width: 100%;
            overflow: hidden;
            font-weight: 600;
            text-align: center;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .bootstrap-icons-grid-item-code {
            display: block;
            width: 100%;
            overflow: hidden;
            color: #6b7280;
            font-size: 0.7rem;
            text-align: center;
            text-overflow: ellipsis;
            white-space: nowrap;
            cursor: pointer;
            transition: color 0.15s ease;
        }
        .bootstrap-icons-grid-item-code:hover {
            color: var(--primary-600);
        }
        .bootstrap-icons-grid-item-code code {
            font-size: inherit;
        }
        .bootstrap-icons-grid-item-style {
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            background: rgba(107, 114, 128, 0.1);
            color: #6b7280;
            font-size: 0.7rem;
        }
        .bootstrap-icons-empty {
            padding: 4rem 1rem;
            color: #6b7280;
            text-align: center;
        }
        @media (max-width: 640px) {
            .bootstrap-icons-toolbar-search {
                min-width: 100%;
            }
            .bootstrap-icons-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>
</x-filament-panels::page>
