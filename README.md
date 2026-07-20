# 伪静态部署
```
location ^~ /livewire- {
    try_files $uri $uri/ /index.php?$query_string;
}
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## 预设命令集
- 启动服务
  - `php artisan server [服务标识] start`
  - 以守护进程模式启动 `config/workerman.php` 中指定的服务
- 重启服务
  - `php artisan server [服务标识] restart`
  - 以守护进程模式重启 `config/workerman.php` 中指定的服务
- 停止服务
  - `php artisan server [服务标识] stop`
  - 停止 `config/workerman.php` 中指定的服务
- 查看服务状态
  - `php artisan server [服务标识] status`
  - 输出 `config/workerman.php` 中指定服务的 Workerman 进程状态

## 自定义参数
- RID 请求唯一标识符
  - 通过 `SetRequestMiddleware` 中间件设置，优先使用请求头中的 `RID` 参数，其次使用 Cookie 中的 `rid` 参数，若都不存在则自动生成一个 UUID 作为 RID
  - 可通过 `$request->attributes->get('rid')` 获取当前请求的 RID

## 公共函数 [app/Helpers/Common.php]
- 判断字符串是否为 JSON
  - `is_json( [string]待判断的字符串 )`
  - return [bool]是否为合法 JSON 对象或 JSON 数组字符串
- 判断对象方法是否公开
  - `is_public( [object]待判断的对象, [string]待判断的方法名称 )`
  - return [bool]是否为对象上存在的公开方法
- 判断对象方法是否公开
  - `isPublic( [object]待判断的对象, [string]待判断的方法名称 )`
  - return [bool]是否为对象上存在的公开方法
- 判断字符串是否为 UUID
  - `is_uuid( [string]待判断的字符串 )`
  - return [bool]是否为标准 UUID 字符串
- 生成 UUID
  - `uuid()`
  - return [string]标准 UUID 字符串
- 生成随机字符串
  - `randomString( [int]字符串长度, [0|1|2]字符串类型 = 2 )`
  - 字符串类型: 0 仅数字，1 仅大小写字母，2 大小写字母加数字
  - return [string]随机字符串

## 系统函数 [app/Helpers/System.php]
- 读取系统配置
  - `setting( [string|null]配置键名 = null, [mixed]默认值 = null )`
  - 键名为 null 时返回全部配置；配置不存在时返回默认值
  - return [mixed]按配置类型转换后的配置值、全部配置或默认值
- 输出标准 JSON 响应
  - `echoJson( [bool|int]状态, [mixed]响应数据, [int|null]HTTP 状态码 = null, [array]响应头 = [] )`
  - 布尔状态或整数状态会转换为 `success`、`info`、`error`、`warning` 或 `unknown`
  - 响应数据可传入 `[翻译键, 替换参数数组]`，函数会通过 Laravel 多语言机制转换为对应文案
  - Web 环境返回包含 `status`、`code`、`time` 和 `data` 的 JSON 响应对象
  - CLI、Artisan 和 Workerman 环境返回不包含 HTTP 响应头的纯 JSON 字符串
  - return [\Illuminate\Http\JsonResponse|string]JSON 响应对象或 JSON 字符串

## 视图服务 [app/Services/ViewService.php]
- 获取框架视图数据
  - `ViewService::renderFrame()`
  - 用于公共 Blade 框架初始化，可在视图中通过 `$frame = \App\Services\ViewService::renderFrame()` 调用
  - return [object]包含当前主题配置 `theme` 和将下划线转换为连字符的当前语言环境 `locale`
- 获取当前主题配置
  - `ViewService::getTheme()`
  - 根据 `theme` Cookie 从 `setting( 'app.theme' )` 中选择当前主题；Cookie 对应的主题不存在或配置无效时使用 `Default` 主题
  - return [array]当前主题配置

## 用户模型 [app/Models/User.php]
- 获取用户等级
  - `User::getLevel( [int|string|null]用户等级 = null )`
  - 不传等级时返回全部等级配置；传入等级时返回最接近且不小于该数值的等级名称
  - 无效、负数或超过最高等级时返回 `Unknown`
  - return [array|string]等级配置列表或等级名称
- 组合电话号码存储格式
  - `User::formatPhoneForStorage( [string]国际区号, [string]本地号码 )`
  - return [string|null]不带加号的 `xx xxxxxxxxxxx` 格式
- 拆分电话号码
  - `User::splitPhone( [string]已存储的电话号码 )`
  - return [array]国际区号和本地号码
- 格式化电话号码显示
  - `User::formatPhoneForDisplay( [string]已存储的电话号码 )`
  - return [string|null]带加号的 `+xx xxxxxxxxxxx` 格式

## Workerman 服务 [app/Workerman/Server.php]
- 服务项名称: String `Server::$name`
- 服务配置: Array `Server::$config`
- 事件处理对象: Object|null `Server::$event`
- Workerman 对象: \Workerman\Worker|null `Server::$worker`
- Register 对象: \GatewayWorker\Register|null `Server::$register`
- Gateway 对象: \GatewayWorker\Gateway|null `Server::$gateway`
- BusinessWorker 对象: \GatewayWorker\BusinessWorker|null `Server::$business`
- 定时器列表: Array `Server::$timers`
- 构建并运行服务
  - `Server::build( [string]服务标识, [array]服务配置 )`
  - 根据配置中的 `type` 构建普通 Workerman 或 GatewayWorker 服务，并进入常驻运行状态
  - return [void]
- 获取服务状态
  - `Server::status( [string]服务标识 )`
  - 根据服务心跳判断服务是否正在运行
  - return [bool]服务是否正在运行
- 设置定时器
  - `Server::setTimer( [string]定时器名称, [float]时间间隔, [callable]回调函数, [array]回调参数 = [], [bool]是否持久执行 = true )`
  - 同一进程中定时器名称不可重复；名称已存在时不会重复创建
  - return [bool|int]成功返回定时器 ID，名称已存在时返回 false
- 删除定时器
  - `Server::delTimer( [string]定时器名称 )`
  - 根据名称删除由 `Server::setTimer()` 创建的定时器
  - return [bool]定时器存在并完成删除时返回 true，否则返回 false
