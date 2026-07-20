<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ServerCommand extends Command {
    /**
     * 命令名称及签名
     * @var string
     */
    protected $signature = 'server';

    /**
     * 命令描述内容
     * @var string
     */
    protected $description = '用于管理内置服务器的命令';

    /**
     * 处理命令
     * @return void
     */
    public function handle() {
        $this->info( 'Starting the built-in server...' );
    }
}
