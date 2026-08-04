<?php

namespace Tests\Feature;

use App\Filament\Resources\AdminControl\PluginManagement\Services\PluginInstaller;
use App\Providers\PluginProvider;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Tests\TestCase;
use ZipArchive;

/**
 * PluginInstallerTest
 * 验证插件安装成功与 install() 失败回滚流程。
 * @package Tests\Feature
 */
class PluginInstallerTest extends TestCase {
    private string $cachePath;

    /**
     * 初始化测试缓存目录。
     * @return void
     */
    protected function setUp(): void {
        parent::setUp();
        $this->cachePath = storage_path( 'framework/testing-plugins' );
        File::ensureDirectoryExists( $this->cachePath );
        config()->set( 'plugin.cache', $this->cachePath );
    }

    /**
     * 清理测试插件和缓存。
     * @return void
     */
    protected function tearDown(): void {
        foreach ( ['InstallSpec', 'RollbackSpec', 'VersionSpec'] as $pluginId ) {
            PluginProvider::forget( $pluginId );
            File::deleteDirectory( app_path( "Plugins/{$pluginId}" ) );
        }
        File::delete( storage_path( 'framework/plugin-uninstall-called' ) );
        File::deleteDirectory( $this->cachePath );
        parent::tearDown();
    }

    /**
     * 测试有效插件可以安装到正式目录。
     * @return void
     */
    public function test_installs_valid_plugin_archive(): void {
        $archive = $this->makeArchive( 'InstallSpec', <<<'PHP'
<?php

use App\Providers\PluginProvider;

return new class extends PluginProvider {
    public function __construct() {
        $this->name = 'Install Spec';
        $this->version = '1.2.3';
        $this->setType( 1 );
    }
};
PHP );
        $upload = new UploadedFile( $archive, 'InstallSpec.zip', 'application/zip', null, true );

        $result = app( PluginInstaller::class )->installFromUpload( $upload );

        $this->assertSame( 'InstallSpec', $result['id'] );
        $this->assertSame( '1.2.3', $result['version'] );
        $this->assertFileExists( app_path( 'Plugins/InstallSpec/index.php' ) );
    }

    /**
     * 测试 install() 返回 false 时删除已移入的插件目录。
     * @return void
     */
    public function test_rolls_back_when_install_returns_false(): void {
        $archive = $this->makeArchive( 'RollbackSpec', <<<'PHP'
<?php

use App\Providers\PluginProvider;

return new class extends PluginProvider {
    public function __construct() {
        $this->name = 'Rollback Spec';
        $this->version = '1.0.0';
        $this->setType( 1 );
    }

    public function install(): bool { return false; }

    public function uninstall(): bool {
        file_put_contents( storage_path( 'framework/plugin-uninstall-called' ), 'called' );
        return true;
    }
};
PHP );
        $upload = new UploadedFile( $archive, 'RollbackSpec.zip', 'application/zip', null, true );

        try {
            app( PluginInstaller::class )->installFromUpload( $upload );
            $this->fail( '安装失败应抛出异常。' );
        }catch ( RuntimeException $exception ) {
            $this->assertStringContainsString( 'install() 未返回 true', $exception->getMessage() );
        }
        $this->assertDirectoryDoesNotExist( app_path( 'Plugins/RollbackSpec' ) );
        $this->assertFileDoesNotExist( storage_path( 'framework/plugin-uninstall-called' ) );
    }

    /**
     * 测试安装时自动清理 macOS 压缩元数据。
     * @return void
     */
    public function test_discards_macos_metadata(): void {
        $archive = $this->makeArchive( 'InstallSpec', <<<'PHP'
<?php

use App\Providers\PluginProvider;

return new class extends PluginProvider {
    public function __construct() {
        $this->name = 'Install Spec';
        $this->version = '1.0.0';
    }
};
PHP, [
            '.DS_Store' => 'metadata',
            '__MACOSX/._InstallSpec' => 'metadata',
            'InstallSpec/.DS_Store' => 'metadata',
            'InstallSpec/._index.php' => 'metadata',
        ] );
        $upload = new UploadedFile( $archive, 'InstallSpec.zip', 'application/zip', null, true );

        app( PluginInstaller::class )->installFromUpload( $upload );

        $this->assertFileDoesNotExist( app_path( 'Plugins/InstallSpec/.DS_Store' ) );
        $this->assertFileDoesNotExist( app_path( 'Plugins/InstallSpec/._index.php' ) );
    }

    /**
     * 测试压缩包包含多个顶级目录时拒绝安装。
     * @return void
     */
    public function test_rejects_multiple_plugin_directories(): void {
        $archive = $this->makeArchive( 'InstallSpec', '<?php return null;', [
            'AnotherPlugin/index.php' => '<?php return null;',
        ] );
        $upload = new UploadedFile( $archive, 'InstallSpec.zip', 'application/zip', null, true );

        $this->expectException( RuntimeException::class );
        $this->expectExceptionMessage( '唯一的插件目录' );

        app( PluginInstaller::class )->installFromUpload( $upload );
    }

    /**
     * 测试插件代码直接位于 ZIP 根目录时拒绝安装。
     * @return void
     */
    public function test_rejects_plugin_files_at_archive_root(): void {
        $archivePath = "{$this->cachePath}/RootPlugin.zip";
        $zip = new ZipArchive();
        $this->assertTrue( $zip->open( $archivePath, ZipArchive::CREATE | ZipArchive::OVERWRITE ) );
        $this->assertTrue( $zip->addFromString( 'index.php', '<?php return null;' ) );
        $this->assertTrue( $zip->addFromString( 'config.php', '<?php return [];' ) );
        $this->assertTrue( $zip->close() );
        $upload = new UploadedFile( $archivePath, 'RootPlugin.zip', 'application/zip', null, true );

        $this->expectException( RuntimeException::class );
        $this->expectExceptionMessage( '不能直接存放插件代码' );

        app( PluginInstaller::class )->installFromUpload( $upload );
    }

    /**
     * 测试已安装插件不允许降级。
     * @return void
     */
    public function test_rejects_plugin_downgrade(): void {
        $installedPath = app_path( 'Plugins/VersionSpec' );
        File::ensureDirectoryExists( $installedPath );
        File::put( "{$installedPath}/index.php", <<<'PHP'
<?php

use App\Providers\PluginProvider;

return new class extends PluginProvider {
    public function __construct() {
        $this->name = 'Version Spec';
        $this->version = '2.0.0';
    }
};
PHP );
        $archive = $this->makeArchive( 'VersionSpec', <<<'PHP'
<?php

use App\Providers\PluginProvider;

return new class extends PluginProvider {
    public function __construct() {
        $this->name = 'Version Spec';
        $this->version = '1.0.0';
    }
};
PHP );
        $upload = new UploadedFile( $archive, 'VersionSpec.zip', 'application/zip', null, true );

        $this->expectException( RuntimeException::class );
        $this->expectExceptionMessage( '禁止将 VersionSpec 从 2.0.0 降级到 1.0.0' );

        app( PluginInstaller::class )->installFromUpload( $upload );
    }

    /**
     * 测试高版本插件可替换已安装版本。
     * @return void
     */
    public function test_updates_plugin_to_higher_version(): void {
        $this->makeInstalledVersionPlugin( '1.0.0' );
        $archive = $this->makeArchive( 'VersionSpec', $this->versionPluginIndex( '2.0.0' ), [
            'VersionSpec/new-version.txt' => '2.0.0',
        ] );
        $upload = new UploadedFile( $archive, 'VersionSpec.zip', 'application/zip', null, true );

        $result = app( PluginInstaller::class )->updateFromUpload( 'VersionSpec', $upload );

        $this->assertSame( '2.0.0', $result['version'] );
        $this->assertFileExists( app_path( 'Plugins/VersionSpec/new-version.txt' ) );
        $this->assertFileDoesNotExist( app_path( 'Plugins/VersionSpec/old-version.txt' ) );
    }

    /**
     * 测试新版本 install() 失败时恢复旧插件目录。
     * @return void
     */
    public function test_restores_previous_plugin_when_update_fails(): void {
        $this->makeInstalledVersionPlugin( '1.0.0' );
        $index = str_replace(
            '};',
            "    public function install(): bool { return false; }\n\n    public function uninstall(): bool {\n        file_put_contents( storage_path( 'framework/plugin-uninstall-called' ), 'called' );\n        return true;\n    }\n};",
            $this->versionPluginIndex( '2.0.0' ),
        );
        $archive = $this->makeArchive( 'VersionSpec', $index );
        $upload = new UploadedFile( $archive, 'VersionSpec.zip', 'application/zip', null, true );

        try {
            app( PluginInstaller::class )->updateFromUpload( 'VersionSpec', $upload );
            $this->fail( '更新失败应抛出异常。' );
        }catch ( RuntimeException $exception ) {
            $this->assertStringContainsString( 'install() 未返回 true', $exception->getMessage() );
        }
        PluginProvider::forget( 'VersionSpec' );
        $plugin = PluginProvider::load( 'VersionSpec' );
        $this->assertSame( '1.0.0', $plugin?->version );
        $this->assertFileExists( app_path( 'Plugins/VersionSpec/old-version.txt' ) );
        $this->assertFileDoesNotExist( storage_path( 'framework/plugin-uninstall-called' ) );
    }

    /**
     * 创建已安装的版本测试插件。
     * @param string $version 插件版本
     * @return void
     */
    private function makeInstalledVersionPlugin( string $version ): void {
        $installedPath = app_path( 'Plugins/VersionSpec' );
        File::ensureDirectoryExists( $installedPath );
        File::put( "{$installedPath}/index.php", $this->versionPluginIndex( $version ) );
        File::put( "{$installedPath}/old-version.txt", $version );
        PluginProvider::forget( 'VersionSpec' );
    }

    /**
     * 生成版本测试插件入口。
     * @param string $version 插件版本
     * @return string 插件入口内容
     */
    private function versionPluginIndex( string $version ): string {
        return <<<PHP
<?php

use App\Providers\PluginProvider;

return new class extends PluginProvider {
    public function __construct() {
        \$this->name = 'Version Spec';
        \$this->version = '{$version}';
    }
};
PHP;
    }

    /**
     * 创建测试插件 ZIP 压缩包。
     * @param string $pluginId 插件标识
     * @param string $index 插件入口内容
     * @param array<string, string> $extraFiles 额外压缩包文件
     * @return string ZIP 文件路径
     */
    private function makeArchive( string $pluginId, string $index, array $extraFiles = [] ): string {
        $archivePath = "{$this->cachePath}/{$pluginId}.zip";
        $zip = new ZipArchive();
        $this->assertTrue( $zip->open( $archivePath, ZipArchive::CREATE | ZipArchive::OVERWRITE ) );
        $this->assertTrue( $zip->addFromString( "{$pluginId}/index.php", $index ) );
        foreach ( $extraFiles as $path => $content ) {
            $this->assertTrue( $zip->addFromString( $path, $content ) );
        }
        $this->assertTrue( $zip->close() );
        return $archivePath;
    }
}
