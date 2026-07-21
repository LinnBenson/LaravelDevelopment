<?php

namespace App\Providers;

use Composer\InstalledVersions;
use Composer\Semver\Semver;
use Composer\Semver\VersionParser;
use LogicException;
use Throwable;

class PluginProvider {

    /** @var array<string, PluginProvider> */
    private static array $cache = [];

    /** @var array<string, bool> */
    private static array $loading = [];

    private bool $enabled = false;

    public ?string $id = null;

    public ?string $path = null;

    public ?string $name = null;

    public ?string $version = null;

    public ?string $author = null;

    public ?string $description = null;

    /** @var array<string, string> */
    public array $relyComposer = [];

    /** @var array<string, string> */
    public array $relyPlugin = [];

    /**
     * 加载插件。
     * 验证插件目录、依赖和循环引用后启用插件。
     * @param string $id 插件标识
     * @return PluginProvider|null 插件实例
     */
    public static function load( string $id ): ?PluginProvider {
        // 安全检查
        if ( preg_match( '/^[A-Za-z][A-Za-z0-9_-]*$/', $id ) !== 1 ) { return null; }
        // 检查缓存
        if ( isset( self::$cache[$id] ) ) { return self::$cache[$id]; }
        // 检查插件是否处在加载中，防止循环依赖
        if ( isset( self::$loading[$id] ) ) {
            throw new LogicException(
                "Circular plugin dependency detected while loading {$id}."
            );
        }
        $pluginPath = self::resolvePluginPath( $id );
        if ( $pluginPath === null ) { return null; }
        self::$loading[$id] = true;
        try {
            $plugin = require "{$pluginPath}/index.php";
            if ( !$plugin instanceof self ) {
                throw new LogicException(
                    "Plugin {$id} must return a PluginProvider instance."
                );
            }
            self::validateComposerDependencies( $id, $plugin );
            self::validatePluginDependencies( $id, $plugin );
            $plugin->enable( $id, $pluginPath );
            self::$cache[$id] = $plugin;
            return $plugin;
        }finally {
            unset( self::$loading[$id] );
        }
    }

    /**
     * 启用插件。
     * 每个插件实例只能启用一次。
     * @param string $id 插件标识
     * @param string $path 插件路径
     * @return void
     */
    final public function enable( string $id, string $path ): void {
        if ( $this->enabled ) {
            throw new LogicException(
                "Plugin {$this->id} is already enabled; enable() cannot be called again."
            );
        }
        $this->enabled = true;
        $this->id = $id;
        $this->path = "{$path}/";

        try {
            $this->boot();
        }catch ( Throwable $throwable ) {
            $this->enabled = false;
            $this->id = null;
            $this->path = null;
            throw $throwable;
        }
    }

    /**
     * 判断插件是否已经启用。
     * @return bool 是否启用
     */
    final public function isEnabled(): bool { return $this->enabled; }

    /**
     * 插件启动入口。
     * 子插件可以覆盖此方法注册配置、事件及其他功能。
     * @return void
     */
    protected function boot(): void {}

    /**
     * 插件安装入口。
     * 子插件可以覆盖此方法执行安装逻辑，如创建数据库表、写入配置等。
     * @return bool 安装成功返回 true，失败返回 false
     */
    protected function install(): bool { return true; }

    /**
     * 插件卸载入口。
     * 子插件可以覆盖此方法执行卸载逻辑，如删除数据库表、清理配置等。
     * @return bool 卸载成功返回 true，失败返回 false
     */
    protected function uninstall(): bool { return true; }

    /**
     * 解析插件目录。
     * 确保插件真实路径位于 app/Plugins 目录内。
     * @param string $id 插件标识
     * @return string|null 插件目录
     */
    private static function resolvePluginPath( string $id ): ?string {
        $pluginRoot = realpath( app_path( 'Plugins' ) );
        if ( $pluginRoot === false ) { return null; }
        $pluginPath = realpath( "{$pluginRoot}/{$id}" );
        if (
            $pluginPath === false ||
            !is_dir( $pluginPath ) ||
            !str_starts_with(
                "{$pluginPath}/",
                rtrim( $pluginRoot, DIRECTORY_SEPARATOR ).DIRECTORY_SEPARATOR,
            )
        ) { return null; }
        $pluginIndex = "{$pluginPath}/index.php";
        if ( !is_file( $pluginIndex ) || !is_readable( $pluginIndex ) ) {
            return null;
        }
        return $pluginPath;
    }

    /**
     * 验证 Composer 依赖。
     * @param string $id 插件标识
     * @param PluginProvider $plugin 插件实例
     * @return void
     */
    private static function validateComposerDependencies( string $id, PluginProvider $plugin ): void {
        $versionParser = new VersionParser();
        foreach ( $plugin->relyComposer as $package => $constraint ) {
            if ( !InstalledVersions::isInstalled( $package ) ) {
                throw new LogicException(
                    "Plugin {$id} requires Composer package {$package} {$constraint}, but it is not installed."
                );
            }
            if ( InstalledVersions::satisfies( $versionParser, $package, $constraint ) ) {
                continue;
            }
            $installedVersion = InstalledVersions::getPrettyVersion( $package ) ?? 'unknown';
            throw new LogicException(
                "Plugin {$id} requires Composer package {$package} {$constraint}, installed version is {$installedVersion}."
            );
        }
    }

    /**
     * 验证插件依赖。
     * @param string $id 插件标识
     * @param PluginProvider $plugin 插件实例
     * @return void
     */
    private static function validatePluginDependencies( string $id, PluginProvider $plugin ): void {
        foreach ( $plugin->relyPlugin as $pluginId => $constraint ) {
            $relyPlugin = self::load( $pluginId );
            if ( $relyPlugin === null ) {
                throw new LogicException(
                    "Plugin {$id} requires plugin {$pluginId} {$constraint}, but it is not installed."
                );
            }
            $installedVersion = $relyPlugin->version;
            if (
                !is_string( $installedVersion ) ||
                !Semver::satisfies( $installedVersion, $constraint )
            ) {
                $installedVersion ??= 'unknown';
                throw new LogicException(
                    "Plugin {$id} requires plugin {$pluginId} {$constraint}, installed version is {$installedVersion}."
                );
            }
        }
    }
}