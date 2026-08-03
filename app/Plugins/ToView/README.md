## To View
- To View 提供项目公共页面框架、主题变量、基础样式、Bootstrap Icons 以及全局 JavaScript 工具
- 继承 `View::Frame` 的 Blade 页面会自动加载 `Assets/js/Core.js`，无需再次引入
  - `@extends( 'View::Frame' )`

## 预设组件
- 卡片组件 [Views/Components/Card.blade.php]
  ```
    <x-View::Card title="卡片标题" icon="卡片图标" open="true:默认打开|false:默认关闭|off:禁用" padding="内边距设置">
        卡片内容
    </x-View::Card>
  ```
- 按钮组件 [Views/Components/Button.blade.php]
  ```
    <x-View::Button icon="按钮图标" size="small|default|big" color="r0|r1|r2|r3|r4|r5|r6" href="跳转链接" target="打开方式" border:线条按钮 stop:禁用按钮 loading:加载中>
        按钮文本
    </x-View::Button>
  ```
- 布局组件 [Views/Components/Layout.blade.php]
  ```
    // default: 间隔页面 gap="0px:间距"
    // row: 行均匀居中分布 gap="0px:间距"
    // list: 网格布局 gap="0px:间距" min="80px:元素最小宽度"
    <x-View::Layout row:横向布局 gap="间距设置">
        布局内容
    </x-View::Layout>
  ```

## 核心对象 [Assets/js/Core.js]
- 站点是否已初始化: Boolean `Core.initialized`
  - Core.js 加载完成后会自动执行 `Core.init()`，初始化完成后为 true
- 当前请求标识: String `Core.rid`
  - 当前请求的唯一标识，由公共页面框架注入
- 当前语言代码: String `Core.locale`
  - 当前页面语言代码，例如 `zh-CN`
- 页面宽度: Number|null `Core.width`
  - 当前浏览器视口宽度，初始化前为 null
- 页面高度: Number|null `Core.height`
  - 当前浏览器视口高度，初始化前为 null
- 系统信息: Object `Core.app`
  - `/api/index` 返回的应用信息，包含应用标题、调试状态、访问地址、图标和版权信息
- 用户信息: Object|null `Core.user`
  - `/api/index` 返回的当前用户信息，未登录或登录状态失效时为 null
- 缓存数据: Object `Core.cache`
  - 页面生命周期内可使用的公共缓存对象，`lang` 保存首次请求获取的语言包，`refreshSystemInfoInterval` 保存系统信息定时刷新器 ID
- 初始化站点
  - `Core.init()`
  - 获取浏览器视口尺寸并写入 `Core.width` 和 `Core.height`
  - 设置 CSS 变量 `--vw` 和 `--vh`，并在窗口尺寸变化时实时更新
  - 从页面注入数据和本地存储中初始化 RID、语言及系统信息，RID 无效时自动生成 UUID，语言无效时使用 `en`
  - 将 RID 写入 localStorage 和作用路径为 `/` 的 Cookie
  - 初始化系统信息，并每 8 秒调用一次 `Core.refreshSystemInfo()`
  - Core.js 加载完成后会自动执行，一般不需要手动调用
  - return [void]
- 获取请求头
  - `Core.headers()`
  - 返回包含当前 `rid` 和 `locale` 的 HTTP 请求头对象
  - localStorage 中存在非空 token 字符串时，追加 `Authorization: Bearer {token}` 请求头
  - return [object]HTTP 请求头对象
- 刷新系统信息
  - `Core.refreshSystemInfo( [string|null]语言包名称 = null )`
  - 携带 `Core.headers()` 返回的请求头向 `/api/index` 发起 GET 请求
  - 语言包名称使用竖线分隔，例如 `base|validation`；传入后通过 `langs` GET 参数请求对应语言包
  - 语言代码由 `Core.headers()` 的 `Locale` 请求头传递，并由服务端请求中间件校验和设置
  - 请求成功时更新 `Core.app` 和 `Core.user`，并将系统信息和有效用户信息写入 localStorage
  - 响应包含语言包时合并到 `Core.cache.lang`
  - 用户状态失效时清除本地用户信息；存在 token 时同时删除 token 并刷新页面
  - `Core.init()` 初始化时请求 `base|validation`，之后每 8 秒自动刷新时不重复请求语言包
  - return [void]
- 获取翻译文本
  - `t( [string]翻译键名, [object]占位符替换参数 = {} )`
  - 使用点号键从 `Core.cache.lang` 读取翻译，例如 `base.save` 或 `validation.required`
  - 支持 Laravel 风格的 `:name`、`:NAME` 和 `:Name` 占位符，并根据占位符形式转换替换文本大小写
  - 语言包未加载、翻译键不存在或目标内容不是字符串时返回原翻译键名，键名为空时返回空字符串
  - return [string]翻译文本或原翻译键名
- 判断字符串是否为 JSON
  - `is_json( [mixed]待判断的值 )`
  - 只接受合法的 JSON 对象或 JSON 数组字符串，JSON 基础值不会被视为符合条件
  - return [bool]是否为合法 JSON 对象或 JSON 数组字符串
- 判断值是否为空
  - `empty( [mixed]待判断的值 )`
  - `undefined`、`null`、`false`、`0`、`0n`、空字符串及字符串 `'0'` 返回 true
  - 空数组、空普通对象、空 Map 和空 Set 返回 true
  - return [bool]是否为空
- 生成 UUID
  - `uuid()`
  - 优先使用浏览器原生 `crypto.randomUUID()`，不支持时使用兼容实现生成 UUID v4
  - return [string]标准 UUID 字符串
- 判断字符串是否为 UUID
  - `is_uuid( [mixed]待判断的值 )`
  - 字母不区分大小写
  - return [bool]是否为标准 `8-4-4-4-12` UUID 字符串
- 获取本地存储数据
  - `get( [string]存储键名, [mixed]默认值 = null )`
  - 通过 `set()` 保存的对象、数组、数字、布尔值及 null 会恢复为原类型，普通字符串保持字符串类型
  - 键不存在、键名无效、数据损坏或浏览器拒绝访问存储时返回默认值
  - return [mixed]已保存的数据或默认值
- 保存本地存储数据
  - `set( [string]存储键名, [mixed]待保存的数据 )`
  - 字符串按原值保存，其它可序列化值使用带内部标识的 JSON 格式保存
  - 不支持保存 undefined、函数、Symbol、BigInt 或存在循环引用的数据
  - return [bool]写入成功返回 true，参数无效、序列化失败或存储失败返回 false
- 删除本地存储数据
  - `del( [string]存储键名 )`
  - 删除不存在的键也视为成功
  - return [bool]删除成功返回 true，键名无效或存储访问失败返回 false
- 获取 Cookie 数据
  - `cookieGet( [string]Cookie 键名, [mixed]默认值 = null )`
  - 通过 `cookieSet()` 保存的对象、数组、数字、布尔值及 null 会恢复为原类型，普通字符串保持字符串类型
  - Cookie 不存在、键名无效、数据损坏或读取失败时返回默认值
  - JavaScript 无法读取带有 HttpOnly 属性的 Cookie
  - return [mixed]已保存的数据或默认值
- 保存 Cookie 数据
  - `cookieSet( [string]Cookie 键名, [mixed]待保存的数据, [object]Cookie 选项 = {} )`
  - 字符串按原值保存，其它可序列化值使用带内部标识的 JSON 格式保存
  - Cookie 选项支持 `expires`、`path`、`domain` 和 `secure`，默认 path 为 `/`
  - 不支持保存 undefined、函数、Symbol、BigInt 或存在循环引用的数据
  - return [bool]写入并验证成功返回 true，参数无效、序列化失败或写入失败返回 false
- 删除 Cookie 数据
  - `cookieDel( [string]Cookie 键名, [object]Cookie 选项 = {} )`
  - Cookie 选项支持 `path` 和 `domain`，默认 path 为 `/`；删除时应传入与写入时相同的 path 和 domain
  - Cookie 不存在时也视为删除成功
  - return [bool]删除成功返回 true，参数无效或删除失败返回 false
