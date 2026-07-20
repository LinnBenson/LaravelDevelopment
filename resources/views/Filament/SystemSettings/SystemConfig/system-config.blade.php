<x-filament-panels::page>
    @php
        $systemUploadConfig = config( 'system_uploads.system_config', [] );
        $imageAccept = collect( $systemUploadConfig['image_extensions'] ?? [] )->map( fn ( $extension ) => ".{$extension}" )->implode( ',' );
        $fileAccept = collect( $systemUploadConfig['file_extensions'] ?? [] )->map( fn ( $extension ) => ".{$extension}" )->implode( ',' );
        $groupedConfigs = $this->getGroupedConfigs();
        $categoryIcons = [
            'app' => 'heroicon-o-squares-2x2',
            'system' => 'heroicon-o-cpu-chip',
            'other' => 'heroicon-o-adjustments-horizontal',
        ];
        $typeIcons = [
            'text' => 'heroicon-o-pencil',
            'boolean' => 'heroicon-o-check-circle',
            'url' => 'heroicon-o-link',
            'image' => 'heroicon-o-photo',
            'file' => 'heroicon-o-paper-clip',
            'number' => 'heroicon-o-hashtag',
            'decimal' => 'heroicon-o-calculator',
            'json' => 'heroicon-o-code-bracket-square',
        ];
    @endphp

    <div
        class="system-config-page"
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
            copySetting(value) {
                if (! window.navigator.clipboard) {
                    this.copyFallback(value)
                    return
                }
                window.navigator.clipboard.writeText(value).catch(() => this.copyFallback(value))
            },
        }"
    >
        {{-- 页面操作栏 --}}
        <section class="system-config-toolbar">
            <div>
                <h2>配置中心</h2>
                <p>按类别集中维护应用配置，不同类型会自动使用对应的编辑控件。</p>
            </div>
            <button
                type="button"
                class="system-config-primary-button"
                wire:click="$toggle('showCreateForm')"
            >
                <x-filament::icon :icon="$showCreateForm ? 'heroicon-o-x-mark' : 'heroicon-o-plus'" />
                <span>{{ $showCreateForm ? '取消新增' : '添加配置键' }}</span>
            </button>
        </section>

        {{-- 新增配置键 --}}
        @if ( $showCreateForm )
            <form class="system-config-create-panel" wire:submit="createConfig">
                <header>
                    <div class="system-config-section-icon">
                        <x-filament::icon icon="heroicon-o-plus-circle" />
                    </div>
                    <div>
                        <h3>添加配置键</h3>
                        <p>键名创建后不可在此页面修改，值会按所选类型保存。</p>
                    </div>
                </header>
                <div class="system-config-create-grid">
                    <label>
                        <span>类别</span>
                        <select wire:model="newConfig.category">
                            @foreach ( \App\Models\SystemConfig::CATEGORIES as $value => $label )
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error( 'newConfig.category' ) <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>类型</span>
                        <select wire:model.live="newConfig.type">
                            @foreach ( \App\Models\SystemConfig::TYPES as $value => $label )
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error( 'newConfig.type' ) <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>名称</span>
                        <input
                            type="text"
                            wire:model="newConfig.name"
                            placeholder="例如 应用名称"
                            autocomplete="off"
                        >
                        @error( 'newConfig.name' ) <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>键名</span>
                        <input
                            type="text"
                            wire:model="newConfig.key"
                            placeholder="例如 app.name"
                            autocomplete="off"
                        >
                        @error( 'newConfig.key' ) <small>{{ $message }}</small> @enderror
                    </label>
                    <label>
                        <span>排序</span>
                        <input type="number" min="0" step="1" wire:model="newConfig.index" placeholder="255">
                        @error( 'newConfig.index' ) <small>{{ $message }}</small> @enderror
                    </label>
                    <label class="system-config-create-value">
                        <span>初始值</span>
                        @switch( $newConfig['type'] )
                            @case( 'boolean' )
                                <span class="system-config-switch-row">
                                    <input type="checkbox" wire:model="newConfig.value">
                                    <b>{{ $newConfig['value'] ? '开启' : '关闭' }}</b>
                                </span>
                                @break
                            @case( 'url' )
                                <input type="url" wire:model="newConfig.value" placeholder="https://example.com">
                                @break
                            @case( 'image' )
                                @php $newImagePreview = $this->getNewUploadPreviewUrl(); @endphp
                                <div class="system-config-image-uploader is-create">
                                    <div class="system-config-image-preview {{ $newImagePreview ? 'has-image' : '' }}">
                                        @if ( $newImagePreview )
                                            <img src="{{ $newImagePreview }}" alt="待上传图片预览">
                                        @else
                                            <x-filament::icon icon="heroicon-o-photo" />
                                            <span>选择图片后可在此预览</span>
                                        @endif
                                    </div>
                                    <div class="system-config-image-actions">
                                        <label class="system-config-upload-button">
                                            <x-filament::icon icon="heroicon-o-arrow-up-tray" />
                                            <span>{{ $newImagePreview ? '重新选择' : '选择图片' }}</span>
                                            <input type="file" wire:model="newUpload" accept="{{ $imageAccept }}">
                                        </label>
                                        @if ( $newImagePreview )
                                            <button type="button" class="system-config-remove-button" wire:click="clearNewUpload">
                                                <x-filament::icon icon="heroicon-o-trash" />
                                                移除所选
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                @break
                            @case( 'file' )
                                @php $newFileName = $this->getUploadFileName( $newUpload ); @endphp
                                <div class="system-config-file-uploader is-create">
                                    <span class="system-config-current-file {{ $newFileName ? '' : 'is-missing' }}">
                                        <x-filament::icon icon="heroicon-o-paper-clip" />
                                        <span>{{ $newFileName ?: '暂无文件' }}</span>
                                    </span>
                                    <div class="system-config-image-actions">
                                        <label class="system-config-upload-button">
                                            <x-filament::icon icon="heroicon-o-arrow-up-tray" />
                                            <span>{{ $newFileName ? '重新选择' : '选择文件' }}</span>
                                            <input type="file" wire:model="newUpload" accept="{{ $fileAccept }}">
                                        </label>
                                        @if ( $newFileName )
                                            <button type="button" class="system-config-remove-button" wire:click="clearNewUpload">
                                                <x-filament::icon icon="heroicon-o-trash" />
                                                移除所选
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                @break
                            @case( 'number' )
                                <input type="number" step="1" wire:model="newConfig.value" placeholder="0">
                                @break
                            @case( 'decimal' )
                                <input type="number" step="any" wire:model="newConfig.value" placeholder="0.00">
                                @break
                            @case( 'textarea' )
                                <textarea wire:model="newConfig.value" rows="5" placeholder="请输入长文本内容"></textarea>
                                @break
                            @case( 'json' )
                                <textarea wire:model="newConfig.value" rows="5" placeholder='{"key": "value"}'></textarea>
                                @break
                            @default
                                <input type="text" wire:model="newConfig.value" placeholder="请输入配置值">
                        @endswitch
                        @error( 'newConfig.value' ) <small>{{ $message }}</small> @enderror
                        @error( 'newUpload' ) <small>{{ $message }}</small> @enderror
                    </label>
                    <label class="system-config-create-description">
                        <span>描述</span>
                        <textarea wire:model="newConfig.description" rows="3" placeholder="请输入此配置项的用途说明"></textarea>
                        @error( 'newConfig.description' ) <small>{{ $message }}</small> @enderror
                    </label>
                </div>
                <footer>
                    <span wire:loading wire:target="newUpload">文件上传中，请稍候…</span>
                    <button type="submit" wire:loading.attr="disabled">
                        <x-filament::icon icon="heroicon-o-check" />
                        创建配置键
                    </button>
                </footer>
            </form>
        @endif

        {{-- 配置类别切换栏 --}}
        <nav class="system-config-category-tabs" aria-label="配置类别">
            @foreach ( \App\Models\SystemConfig::CATEGORIES as $category => $categoryLabel )
                <button
                    type="button"
                    class="system-config-category-tab {{ $activeCategory === $category ? 'is-active' : '' }}"
                    wire:click="selectCategory('{{ $category }}')"
                    wire:loading.attr="disabled"
                    wire:target="selectCategory"
                    aria-pressed="{{ $activeCategory === $category ? 'true' : 'false' }}"
                >
                    <span class="system-config-category-tab-icon">
                        <x-filament::icon :icon="$categoryIcons[$category]" />
                    </span>
                    <span>
                        <strong>{{ $categoryLabel }}</strong>
                        <small>{{ $groupedConfigs[$category]->count() }} 个配置项</small>
                    </span>
                </button>
            @endforeach
        </nav>

        {{-- 当前类别编辑区 --}}
        @php
            $category = $activeCategory;
            $categoryLabel = \App\Models\SystemConfig::CATEGORIES[$category];
            $configs = $groupedConfigs[$category];
        @endphp
            <section class="system-config-category" wire:key="active-category-{{ $category }}">
                <header class="system-config-category-header">
                    <div class="system-config-category-heading">
                        <span class="system-config-section-icon">
                            <x-filament::icon :icon="$categoryIcons[$category]" />
                        </span>
                        <div>
                            <h3>{{ $categoryLabel }}</h3>
                            <p>{{ $configs->count() }} 个配置项</p>
                        </div>
                    </div>
                    @if ( $configs->isNotEmpty() )
                        <button
                            type="button"
                            class="system-config-save-button"
                            wire:click="saveCategory('{{ $category }}')"
                            wire:loading.attr="disabled"
                            wire:target="saveCategory('{{ $category }}')"
                        >
                            <x-filament::icon icon="heroicon-o-check-circle" />
                            <span wire:loading.remove wire:target="saveCategory('{{ $category }}')">保存此类别</span>
                            <span wire:loading wire:target="saveCategory('{{ $category }}')">保存中…</span>
                        </button>
                    @endif
                </header>

                @if ( $configs->isEmpty() )
                    <div class="system-config-empty">
                        <x-filament::icon icon="heroicon-o-inbox" />
                        <strong>暂无{{ $categoryLabel }}</strong>
                        <span>点击“添加配置键”创建第一项配置。</span>
                    </div>
                @else
                    <div class="system-config-list">
                        @foreach ( $configs as $config )
                            @php $hideTypeIcon = in_array( $config->type, ['textarea', 'boolean', 'image', 'file', 'json'], true ); @endphp
                            <article class="system-config-item" wire:key="config-{{ $config->id }}">
                                <div class="system-config-meta">
                                    <button
                                        type="button"
                                        class="system-config-meta-copy fi-copyable"
                                        title="点击复制 setting( '{{ $config->key }}' )"
                                        x-on:click="
                                            copySetting({{ Illuminate\Support\Js::from( "setting( '{$config->key}' )" ) }})
                                            $tooltip('已复制', {
                                                theme: $store.theme,
                                                timeout: 2000,
                                            })
                                        "
                                    >
                                        {{ $config->name ?: $config->key }}
                                    </button>
                                </div>
                                <div class="system-config-control">
                                    <div class="system-config-value-control {{ $hideTypeIcon ? 'no-leading-icon' : '' }}">
                                        @if ( ! $hideTypeIcon )
                                            <span class="system-config-value-icon">
                                                <x-filament::icon :icon="$typeIcons[$config->type] ?? 'heroicon-o-pencil'" />
                                            </span>
                                        @endif
                                        <div class="system-config-value-field">
                                            @switch( $config->type )
                                        @case( 'boolean' )
                                            <label class="system-config-boolean-control">
                                                <input type="checkbox" wire:model="values.{{ $config->id }}">
                                                <span class="system-config-toggle"><i></i></span>
                                                <b>{{ ! empty( $values[$config->id] ) ? '开启' : '关闭' }}</b>
                                            </label>
                                            @break
                                        @case( 'url' )
                                            <div class="system-config-input-action">
                                                <input
                                                    type="url"
                                                    wire:model.blur="values.{{ $config->id }}"
                                                    placeholder="https://example.com"
                                                >
                                                @if ( filled( $values[$config->id] ?? null ) )
                                                    <a href="{{ $values[$config->id] }}" target="_blank" rel="noopener noreferrer" aria-label="打开链接">
                                                        <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" />
                                                    </a>
                                                @endif
                                            </div>
                                            @break
                                        @case( 'image' )
                                            @php
                                                $temporaryImageUrl = $this->getUploadPreviewUrl( $config->id );
                                                $currentImageUrl = $this->getConfigFileUrl( $config->value );
                                                $hasStoredImage = filled( $config->value );
                                                $isRemovingImage = $removeFiles[$config->id] ?? false;
                                                $imagePreviewUrl = $temporaryImageUrl ?: ( $isRemovingImage ? null : $currentImageUrl );
                                            @endphp
                                            <div class="system-config-image-uploader">
                                                <div class="system-config-image-preview {{ $imagePreviewUrl ? 'has-image' : '' }} {{ $isRemovingImage ? 'is-removing' : '' }}">
                                                    @if ( $imagePreviewUrl )
                                                        <img src="{{ $imagePreviewUrl }}" alt="{{ $config->name ?: $config->key }}">
                                                        @if ( ! $temporaryImageUrl && $currentImageUrl )
                                                            <a href="{{ $currentImageUrl }}" target="_blank" rel="noopener noreferrer" aria-label="查看原图">
                                                                <x-filament::icon icon="heroicon-o-arrows-pointing-out" />
                                                            </a>
                                                        @endif
                                                    @else
                                                        <x-filament::icon icon="heroicon-o-photo" />
                                                        <span>{{ $isRemovingImage ? '保存后删除图片' : ( $hasStoredImage ? '图片文件不存在' : '暂无图片' ) }}</span>
                                                    @endif
                                                </div>
                                                <div class="system-config-image-actions">
                                                    <label class="system-config-upload-button">
                                                        <x-filament::icon icon="heroicon-o-arrow-up-tray" />
                                                        <span>{{ $imagePreviewUrl ? '更换图片' : '选择图片' }}</span>
                                                        <input type="file" wire:model="uploads.{{ $config->id }}" accept="{{ $imageAccept }}">
                                                    </label>
                                                    @if ( $temporaryImageUrl )
                                                        <button type="button" class="system-config-remove-button" wire:click="clearConfigUpload({{ $config->id }})">
                                                            <x-filament::icon icon="heroicon-o-x-mark" />
                                                            取消更换
                                                        </button>
                                                    @elseif ( $hasStoredImage && ! $isRemovingImage )
                                                        <button type="button" class="system-config-remove-button" wire:click="toggleFileRemoval({{ $config->id }})">
                                                            <x-filament::icon icon="heroicon-o-trash" />
                                                            删除图片
                                                        </button>
                                                    @elseif ( $isRemovingImage )
                                                        <button type="button" class="system-config-undo-button" wire:click="toggleFileRemoval({{ $config->id }})">
                                                            <x-filament::icon icon="heroicon-o-arrow-uturn-left" />
                                                            撤销删除
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                            @break
                                        @case( 'file' )
                                            @php
                                                $pendingFileName = $this->getUploadFileName( $uploads[$config->id] ?? null );
                                                $currentFileUrl = $this->getConfigFileUrl( $config->value );
                                                $hasStoredFile = filled( $config->value );
                                                $isRemovingFile = $removeFiles[$config->id] ?? false;
                                            @endphp
                                            <div class="system-config-file-uploader {{ $isRemovingFile ? 'is-removing' : '' }}">
                                                @if ( $currentFileUrl && ! $pendingFileName && ! $isRemovingFile )
                                                    <a class="system-config-current-file" href="{{ $currentFileUrl }}" target="_blank" rel="noopener noreferrer">
                                                        <x-filament::icon icon="heroicon-o-paper-clip" />
                                                        <span>{{ basename( $config->value ) }}</span>
                                                    </a>
                                                @else
                                                    <span class="system-config-current-file {{ $pendingFileName ? '' : 'is-missing' }}">
                                                        <x-filament::icon icon="heroicon-o-paper-clip" />
                                                        <span>{{ $pendingFileName ?: ( $isRemovingFile ? '保存后删除文件' : ( $hasStoredFile ? '文件不存在' : '暂无文件' ) ) }}</span>
                                                    </span>
                                                @endif
                                                <div class="system-config-image-actions">
                                                    <label class="system-config-upload-button">
                                                        <x-filament::icon icon="heroicon-o-arrow-up-tray" />
                                                        <span>{{ $pendingFileName ? '重新选择' : ( $hasStoredFile ? '更换文件' : '选择文件' ) }}</span>
                                                        <input type="file" wire:model="uploads.{{ $config->id }}" accept="{{ $fileAccept }}">
                                                    </label>
                                                    @if ( $pendingFileName )
                                                        <button type="button" class="system-config-remove-button" wire:click="clearConfigUpload({{ $config->id }})">
                                                            <x-filament::icon icon="heroicon-o-x-mark" />
                                                            取消更换
                                                        </button>
                                                    @elseif ( $hasStoredFile && ! $isRemovingFile )
                                                        <button type="button" class="system-config-remove-button" wire:click="toggleFileRemoval({{ $config->id }})">
                                                            <x-filament::icon icon="heroicon-o-trash" />
                                                            删除文件
                                                        </button>
                                                    @elseif ( $isRemovingFile )
                                                        <button type="button" class="system-config-undo-button" wire:click="toggleFileRemoval({{ $config->id }})">
                                                            <x-filament::icon icon="heroicon-o-arrow-uturn-left" />
                                                            撤销删除
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                            @break
                                        @case( 'number' )
                                            <input type="number" step="1" wire:model.blur="values.{{ $config->id }}" placeholder="0">
                                            @break
                                        @case( 'decimal' )
                                            <input type="number" step="any" wire:model.blur="values.{{ $config->id }}" placeholder="0.00">
                                            @break
                                        @case( 'textarea' )
                                            <textarea
                                                class="system-config-longtext-editor"
                                                wire:model.blur="values.{{ $config->id }}"
                                                rows="7"
                                                placeholder="请输入长文本内容"
                                            ></textarea>
                                            @break
                                        @case( 'json' )
                                            <textarea
                                                class="system-config-json-editor"
                                                wire:model.blur="values.{{ $config->id }}"
                                                rows="7"
                                                spellcheck="false"
                                                placeholder='{"key": "value"}'
                                            ></textarea>
                                            @break
                                        @default
                                            <input type="text" wire:model.blur="values.{{ $config->id }}" placeholder="请输入配置值">
                                            @endswitch
                                        </div>
                                    </div>

                                    @error( 'values.' . $config->id )
                                        <small class="system-config-error">{{ $message }}</small>
                                    @enderror
                                    @error( 'uploads.' . $config->id )
                                        <small class="system-config-error">{{ $message }}</small>
                                    @enderror
                                    @if ( filled( $config->description ) )
                                        <p class="system-config-description">{{ $config->description }}</p>
                                    @endif
                                    <span class="system-config-uploading" wire:loading wire:target="uploads.{{ $config->id }}">
                                        文件上传中，请稍候…
                                    </span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
    </div>

    <style>
        /* 系统配置页面基础变量和布局 */
        .system-config-page {
            --config-card: #ffffff;
            --config-border: #e5e7eb;
            --config-field: #ffffff;
            --config-muted: #6b7280;
            --config-text: #111827;
            display: grid;
            color: var(--config-text);
            gap: 1.25rem;
        }
        .dark .system-config-page {
            --config-card: #18181b;
            --config-border: #27272a;
            --config-field: #09090b;
            --config-muted: #a1a1aa;
            --config-text: #f4f4f5;
        }
        .system-config-page button,
        .system-config-page input,
        .system-config-page select,
        .system-config-page textarea {
            font: inherit;
        }
        .system-config-page input[type="text"],
        .system-config-page input[type="url"],
        .system-config-page input[type="number"],
        .system-config-page input[type="file"],
        .system-config-page select,
        .system-config-page textarea {
            width: 100%;
            padding: 0.7rem 0.85rem;
            border: 1px solid var(--config-border);
            border-radius: 0.65rem;
            outline: none;
            background: var(--config-field);
            color: var(--config-text);
            font-size: 0.82rem;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .system-config-page input:focus,
        .system-config-page select:focus,
        .system-config-page textarea:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 0.2rem rgba(245, 158, 11, 0.12);
        }

        /* 页面顶部操作栏 */
        .system-config-toolbar {
            display: flex;
            padding: 1.25rem 1.4rem;
            border: 1px solid rgba(245, 158, 11, 0.22);
            border-radius: 1rem;
            background: linear-gradient(120deg, #fff7ed, #fffbeb);
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .dark .system-config-toolbar {
            background: linear-gradient(120deg, #292017, #252015);
        }
        .system-config-toolbar h2,
        .system-config-toolbar p {
            margin: 0;
        }
        .system-config-toolbar h2 {
            font-size: 1.1rem;
            font-weight: 750;
        }
        .system-config-toolbar p {
            margin-top: 0.25rem;
            color: var(--config-muted);
            font-size: 0.78rem;
        }
        .system-config-primary-button,
        .system-config-save-button,
        .system-config-create-panel footer button {
            display: inline-flex;
            padding: 0.7rem 0.95rem;
            border-radius: 0.65rem;
            background: #d97706;
            box-shadow: 0 0.35rem 0.8rem rgba(217, 119, 6, 0.16);
            color: #ffffff;
            font-size: 0.78rem;
            font-weight: 700;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            cursor: pointer;
        }
        .system-config-primary-button:hover,
        .system-config-save-button:hover,
        .system-config-create-panel footer button:hover {
            background: #b45309;
        }
        .system-config-primary-button:disabled,
        .system-config-save-button:disabled,
        .system-config-create-panel footer button:disabled {
            opacity: 0.55;
            cursor: wait;
        }
        .system-config-primary-button svg,
        .system-config-save-button svg,
        .system-config-create-panel footer button svg {
            width: 1rem;
            height: 1rem;
        }

        /* 新增配置表单 */
        .system-config-create-panel,
        .system-config-category {
            overflow: hidden;
            border: 1px solid var(--config-border);
            border-radius: 1rem;
            background: var(--config-card);
            box-shadow: 0 0.25rem 1rem rgba(15, 23, 42, 0.04);
        }
        .system-config-create-panel > header,
        .system-config-category-header {
            display: flex;
            min-height: 4.75rem;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--config-border);
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .system-config-create-panel > header {
            justify-content: flex-start;
        }
        .system-config-create-panel h3,
        .system-config-create-panel p,
        .system-config-category h3,
        .system-config-category p {
            margin: 0;
        }
        .system-config-create-panel h3,
        .system-config-category h3 {
            font-size: 0.95rem;
            font-weight: 750;
        }
        .system-config-create-panel p,
        .system-config-category p {
            margin-top: 0.18rem;
            color: var(--config-muted);
            font-size: 0.72rem;
        }
        .system-config-create-grid {
            display: grid;
            padding: 1.25rem;
            grid-template-columns: minmax(9rem, 0.6fr) minmax(9rem, 0.6fr) minmax(12rem, 1fr) minmax(12rem, 1fr) minmax(7rem, 0.4fr);
            gap: 1rem;
        }
        .system-config-create-grid label {
            display: grid;
            align-content: start;
            gap: 0.4rem;
        }
        .system-config-create-grid label > span:first-child {
            font-size: 0.75rem;
            font-weight: 650;
        }
        .system-config-create-grid label small,
        .system-config-error {
            color: #dc2626;
            font-size: 0.7rem;
        }
        .system-config-create-value,
        .system-config-create-description {
            grid-column: 1 / -1;
        }
        .system-config-create-panel footer {
            display: flex;
            min-height: 4rem;
            padding: 0.75rem 1.25rem;
            border-top: 1px solid var(--config-border);
            align-items: center;
            justify-content: flex-end;
            gap: 1rem;
        }
        .system-config-create-panel footer > span {
            color: var(--config-muted);
            font-size: 0.72rem;
        }
        .system-config-switch-row {
            display: flex;
            min-height: 2.75rem;
            padding: 0 0.85rem;
            border: 1px solid var(--config-border);
            border-radius: 0.65rem;
            align-items: center;
            gap: 0.6rem;
        }

        /* 配置类别切换栏 */
        .system-config-category-tabs {
            display: grid;
            padding: 0.4rem;
            overflow: hidden;
            border: 1px solid var(--config-border);
            border-radius: 1rem;
            background: var(--config-card);
            box-shadow: 0 0.25rem 1rem rgba(15, 23, 42, 0.04);
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.4rem;
        }
        .system-config-category-tab {
            display: flex;
            min-width: 0;
            padding: 0.8rem 1rem;
            border: 0;
            border-radius: 0.75rem;
            background: transparent;
            color: var(--config-muted);
            text-align: left;
            align-items: center;
            gap: 0.7rem;
            cursor: pointer;
            transition: background 0.15s ease, color 0.15s ease, box-shadow 0.15s ease;
        }
        .system-config-category-tab:hover {
            background: #fafafa;
            color: var(--config-text);
        }
        .system-config-category-tab.is-active {
            background: #fff7ed;
            box-shadow: inset 0 0 0 1px rgba(245, 158, 11, 0.28);
            color: #b45309;
        }
        .dark .system-config-category-tab:hover {
            background: #27272a;
        }
        .dark .system-config-category-tab.is-active {
            background: rgba(245, 158, 11, 0.12);
            color: #fbbf24;
        }
        .system-config-category-tab-icon {
            display: grid;
            width: 2.2rem;
            height: 2.2rem;
            flex: 0 0 2.2rem;
            border-radius: 0.65rem;
            background: #f4f4f5;
            color: #71717a;
            place-items: center;
        }
        .system-config-category-tab.is-active .system-config-category-tab-icon {
            background: #ffffff;
            color: #d97706;
        }
        .dark .system-config-category-tab-icon,
        .dark .system-config-category-tab.is-active .system-config-category-tab-icon {
            background: #18181b;
        }
        .system-config-category-tab-icon svg {
            width: 1.1rem;
            height: 1.1rem;
        }
        .system-config-category-tab > span:last-child {
            display: grid;
            min-width: 0;
            gap: 0.1rem;
        }
        .system-config-category-tab strong {
            overflow: hidden;
            color: inherit;
            font-size: 0.82rem;
            font-weight: 750;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .system-config-category-tab small {
            color: var(--config-muted);
            font-size: 0.68rem;
        }

        /* 当前分类标题和配置列表 */
        .system-config-category-heading {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .system-config-section-icon {
            display: grid;
            width: 2.5rem;
            height: 2.5rem;
            flex: 0 0 2.5rem;
            border-radius: 0.7rem;
            background: #fff7ed;
            color: #d97706;
            place-items: center;
        }
        .dark .system-config-section-icon {
            background: rgba(245, 158, 11, 0.12);
        }
        .system-config-section-icon svg {
            width: 1.25rem;
            height: 1.25rem;
        }
        .system-config-save-button {
            padding: 0.6rem 0.8rem;
        }
        .system-config-list {
            display: grid;
        }
        .system-config-item {
            display: grid;
            min-height: 7rem;
            padding: 1.35rem 1.5rem;
            grid-template-columns: minmax(11rem, 15rem) minmax(18rem, 1fr);
            align-items: start;
            gap: 2rem;
        }
        .system-config-item:last-child {
            border-bottom: 0;
        }
        .system-config-meta {
            display: flex;
            min-width: 0;
            min-height: 2.75rem;
            padding-top: 0.7rem;
            justify-content: flex-end;
            text-align: right;
            align-items: center;
        }
        .system-config-meta-copy {
            color: #475569;
            font-size: 0.88rem;
            font-weight: 750;
            cursor: pointer;
            transition: color 0.15s ease;
        }
        .system-config-meta-copy:hover {
            color: rgb(217, 119, 6);
        }
        .system-config-control {
            display: grid;
            min-width: 0;
            gap: 0.35rem;
        }
        .system-config-value-control {
            display: flex;
            min-width: 0;
            align-items: stretch;
        }
        .system-config-value-icon {
            display: grid;
            width: 2.75rem;
            min-height: 2.75rem;
            flex: 0 0 2.75rem;
            border: 1px solid var(--config-border);
            border-right: 0;
            border-radius: 0.65rem 0 0 0.65rem;
            background: var(--config-field);
            color: #52525b;
            place-items: center;
        }
        .system-config-value-icon svg {
            width: 1.05rem;
            height: 1.05rem;
        }
        .system-config-value-field {
            min-width: 0;
            flex: 1;
        }
        .system-config-value-field > input,
        .system-config-value-field > textarea,
        .system-config-value-field > .system-config-input-action > input {
            border-radius: 0 0.65rem 0.65rem 0 !important;
        }
        .system-config-value-control.no-leading-icon .system-config-value-field > input,
        .system-config-value-control.no-leading-icon .system-config-value-field > textarea {
            border-radius: 0.65rem !important;
        }
        .system-config-description {
            margin: 0.15rem 0 0 2.75rem !important;
            color: var(--config-muted);
            font-size: 0.76rem !important;
            line-height: 1.5;
            white-space: pre-wrap;
        }
        .system-config-value-control.no-leading-icon ~ .system-config-description {
            margin-left: 0 !important;
        }
        .system-config-longtext-editor,
        .system-config-json-editor {
            min-height: 9rem;
            resize: vertical;
        }
        .system-config-json-editor {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
            line-height: 1.55;
        }
        .system-config-input-action {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .system-config-input-action a {
            display: grid;
            width: 2.65rem;
            height: 2.65rem;
            flex: 0 0 2.65rem;
            border: 1px solid var(--config-border);
            border-radius: 0.65rem;
            color: #d97706;
            place-items: center;
        }
        .system-config-input-action a svg {
            width: 1rem;
            height: 1rem;
        }

        /* 布尔开关和文件控件 */
        .system-config-boolean-control {
            display: inline-flex;
            width: max-content;
            margin-top: 14px;
            align-items: center;
            gap: 0.6rem;
            cursor: pointer;
        }
        .system-config-boolean-control > input {
            width: 1px;
            height: 1px;
            opacity: 0;
            position: absolute;
        }
        .system-config-toggle {
            width: 2.7rem;
            height: 1.5rem;
            padding: 0.18rem;
            border-radius: 999px;
            background: #d4d4d8;
            transition: background 0.15s ease;
        }
        .system-config-toggle i {
            display: block;
            width: 1.14rem;
            height: 1.14rem;
            border-radius: 50%;
            background: #ffffff;
            box-shadow: 0 0.1rem 0.25rem rgba(0, 0, 0, 0.18);
            transition: transform 0.15s ease;
        }
        .system-config-boolean-control input:checked + .system-config-toggle {
            background: #d97706;
        }
        .system-config-boolean-control input:checked + .system-config-toggle i {
            transform: translateX(1.2rem);
        }
        .system-config-boolean-control b {
            font-size: 0.75rem;
        }
        .system-config-image-uploader {
            display: flex;
            min-width: 0;
            padding: 0.75rem;
            border: 1px solid var(--config-border);
            border-radius: 0.8rem;
            background: #fafafa;
            align-items: center;
            gap: 0.85rem;
            flex-wrap: wrap;
        }
        .dark .system-config-image-uploader {
            background: #18181b;
        }
        .system-config-image-preview {
            display: grid;
            width: 6.5rem;
            height: 6.5rem;
            overflow: hidden;
            border: 1px dashed #d4d4d8;
            border-radius: 0.85rem;
            background: #f4f4f5;
            color: var(--config-muted);
            position: relative;
            place-items: center;
        }
        .dark .system-config-image-preview {
            border-color: #52525b;
            background: #27272a;
        }
        .system-config-image-preview.has-image {
            border-style: solid;
            background: #ffffff;
        }
        .system-config-image-preview.is-removing {
            border-color: #fca5a5;
            background: #fef2f2;
            color: #dc2626;
        }
        .dark .system-config-image-preview.is-removing {
            border-color: #7f1d1d;
            background: #450a0a;
            color: #fca5a5;
        }
        .system-config-image-preview > img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .system-config-image-preview > svg {
            width: 1.55rem;
            height: 1.55rem;
        }
        .system-config-image-preview > span {
            padding: 0.5rem;
            font-size: 0.7rem;
            text-align: center;
        }
        .system-config-image-preview > a {
            display: grid;
            width: 1.8rem;
            height: 1.8rem;
            border-radius: 0.5rem;
            background: rgba(24, 24, 27, 0.82);
            color: #ffffff;
            position: absolute;
            right: 0.4rem;
            bottom: 0.4rem;
            place-items: center;
        }
        .system-config-image-preview > a svg {
            width: 0.95rem;
            height: 0.95rem;
        }
        .system-config-image-actions {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: 0.55rem;
            flex-wrap: wrap;
        }
        .system-config-upload-button,
        .system-config-remove-button,
        .system-config-undo-button {
            display: inline-flex;
            min-height: 2.5rem;
            padding: 0.55rem 0.75rem;
            border-radius: 0.6rem;
            background: #ffffff;
            font-size: 0.72rem !important;
            font-weight: 650;
            align-items: center;
            gap: 0.4rem;
            cursor: pointer;
        }
        .dark .system-config-upload-button,
        .dark .system-config-remove-button,
        .dark .system-config-undo-button {
            background: #18181b;
        }
        .system-config-upload-button {
            border: 1px dashed #d97706;
            color: #b45309;
        }
        .system-config-remove-button {
            border: 1px solid #fecaca;
            color: #dc2626;
        }
        .system-config-undo-button {
            border: 1px solid #bbf7d0;
            color: #15803d;
        }
        .system-config-upload-button input {
            display: none;
        }
        .system-config-upload-button svg,
        .system-config-remove-button svg,
        .system-config-undo-button svg {
            width: 1rem;
            height: 1rem;
        }
        .system-config-file-uploader {
            display: flex;
            min-width: 0;
            padding: 0.75rem;
            border: 1px solid var(--config-border);
            border-radius: 0.8rem;
            background: #fafafa;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        .dark .system-config-file-uploader {
            background: #18181b;
        }
        .system-config-file-uploader.is-removing {
            border-color: #fecaca;
            background: #fef2f2;
        }
        .dark .system-config-file-uploader.is-removing {
            border-color: #7f1d1d;
            background: #450a0a;
        }
        .system-config-upload-control {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        .system-config-upload-control > a > img,
        .system-config-file-placeholder {
            display: grid;
            width: 3.5rem;
            height: 3.5rem;
            overflow: hidden;
            border: 1px solid var(--config-border);
            border-radius: 0.7rem;
            background: #f4f4f5;
            object-fit: cover;
            color: var(--config-muted);
            place-items: center;
        }
        .system-config-file-placeholder svg {
            width: 1.25rem;
            height: 1.25rem;
        }
        .system-config-upload-control > label {
            display: inline-flex;
            min-height: 2.5rem;
            padding: 0.55rem 0.75rem;
            border: 1px dashed #d97706;
            border-radius: 0.6rem;
            color: #b45309;
            font-size: 0.72rem;
            font-weight: 650;
            align-items: center;
            gap: 0.4rem;
            cursor: pointer;
        }
        .system-config-upload-control > label svg,
        .system-config-current-file svg {
            width: 1rem;
            height: 1rem;
        }
        .system-config-upload-control > label input {
            display: none;
        }
        .system-config-current-file {
            display: inline-flex;
            max-width: 18rem;
            padding: 0.55rem 0.7rem;
            border-radius: 0.6rem;
            background: #f4f4f5;
            color: #52525b;
            font-size: 0.72rem;
            text-decoration: none;
            align-items: center;
            gap: 0.4rem;
        }
        .dark .system-config-current-file {
            background: #27272a;
            color: #d4d4d8;
        }
        .system-config-current-file span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .system-config-current-file.is-missing {
            color: var(--config-muted);
        }
        .system-config-uploading {
            color: var(--config-muted);
            font-size: 0.68rem;
        }

        /* 空状态和响应式布局 */
        .system-config-empty {
            display: grid;
            min-height: 10rem;
            padding: 2rem;
            color: var(--config-muted);
            text-align: center;
            place-content: center;
            justify-items: center;
            gap: 0.35rem;
        }
        .system-config-empty svg {
            width: 1.75rem;
            height: 1.75rem;
            margin-bottom: 0.25rem;
        }
        .system-config-empty strong {
            color: var(--config-text);
            font-size: 0.82rem;
        }
        .system-config-empty span {
            font-size: 0.72rem;
        }
        @media (max-width: 64rem) {
            .system-config-create-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .system-config-item {
                grid-template-columns: 1fr;
                gap: 0.8rem;
            }
            .system-config-meta {
                padding-top: 0;
                justify-content: flex-start;
                text-align: left;
            }
        }
        @media (max-width: 40rem) {
            .system-config-category-tabs {
                overflow-x: auto;
                grid-template-columns: repeat(3, minmax(9rem, 1fr));
            }
            .system-config-toolbar,
            .system-config-category-header {
                align-items: stretch;
                flex-direction: column;
            }
            .system-config-create-grid {
                grid-template-columns: 1fr;
            }
            .system-config-create-value,
            .system-config-create-description {
                grid-column: auto;
            }
            .system-config-primary-button,
            .system-config-save-button {
                width: 100%;
            }
        }
    </style>
</x-filament-panels::page>
