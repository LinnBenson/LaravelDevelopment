@extends( 'View::Frame' )

@section( 'title', 'Test' )

@section( 'body' )
<div id="main" class="padding32 sizing">
    <x-View::Card class="test" title="测试卡片">
        32323232
        <x-View::Button icon="bi-link-45deg" color="r2" onClick="Core.toast( 3, '通知标题通知标题通知标题通知标题通知标题通知标题', '通知内容' )">通知测试</x-View::Button>
            <x-View::Button icon="bi-link-45deg" color="r2" onClick="Core.loading( true, 3000 )">加载测试</x-View::Button>
            <x-View::Button icon="bi-link-45deg" color="r2" onClick="Core.boxLoading( $( '.test' ), true, 3000 )">卡片加载测试</x-View::Button>
    </x-View::Card>
    <x-View::Card title="按钮预览" icon="bi-star" open="false">
        <x-View::Layout class="bottom8" gap="8px">
            <x-View::Button icon="bi-link-45deg" color="r2">按钮 1</x-View::Button>
            <x-View::Button icon="bi-cart2" color="r3">按钮 2</x-View::Button>
            <x-View::Button icon="bi-check-circle" color="r4">按钮 3</x-View::Button>
            <x-View::Button icon="bi-exclamation-circle" color="r5">按钮 4</x-View::Button>
            <x-View::Button icon="bi-star" color="r3" loading>按钮 2</x-View::Button>
            <x-View::Button icon="bi-cart2" color="r3" size="big">按钮 2</x-View::Button>
            <x-View::Button icon="bi-cart2" color="r3" size="small">按钮 2</x-View::Button>
        </x-View::Layout>
        <x-View::Layout class="bottom8" gap="8px">
            <x-View::Button icon="bi-link-45deg" color="r2" border>按钮 1</x-View::Button>
            <x-View::Button icon="bi-cart2" color="r3" border>按钮 2</x-View::Button>
            <x-View::Button icon="bi-check-circle" color="r4" border>按钮 3</x-View::Button>
            <x-View::Button icon="bi-exclamation-circle" color="r5" border>按钮 4</x-View::Button>
            <x-View::Button icon="bi-star" color="r3" border loading>按钮 2</x-View::Button>
            <x-View::Button icon="bi-cart2" color="r3" size="big" border>按钮 2</x-View::Button>
            <x-View::Button icon="bi-cart2" color="r3" size="small" border>按钮 2</x-View::Button>
        </x-View::Layout>
    </x-View::Card>
    <x-View::Card title="演示表单" icon="bi-star" open="true">
        <form onsubmit="Core.submit( this, submitData ); return false;">
            <x-View::Input title="普通文本" type="text" name="text" placeholder="请输入一段文本" left="auto" :tips="[
                '这是第一个提示',
                '这是第二个提示',
            ]" value="text" required />
            <x-View::Input title="密码框" type="password" name="password" placeholder="请输入一段密码" left="auto" value="123456" required error />
            <x-View::Input title="邮箱输入" type="email" name="email" placeholder="请输入一段邮箱" left="auto" value="abc@adb.com" required />
            <x-View::Input title="手机号" type="phone" name="phone" placeholder="请输入手机号" left="auto" value="+1 123456" required />
            <x-View::Input title="开关按钮" type="switch" name="switch" left="auto" value="1" required />
            <x-View::Input title="数字输入" type="number" name="number" placeholder="请输入一段数字" left="auto" step="0.01" min="0" max="100" value="24" required />
            <x-View::Input title="完整日期" type="datetime-local" name="datetime-local" placeholder="请输入完整日期" left="auto" value="2024.06.06 12:00:00" required />
            <x-View::Input title="日期输入" type="date" name="date" placeholder="请输入日期" left="auto" value="2024.06.06" required />
            <x-View::Input title="时间输入" type="time" name="time" placeholder="请输入时间" left="auto" value="12:00:00" required />
            <x-View::Input title="单选输入" type="radio" name="radio" left="auto" :options="[
                'option1' => '选项 1',
                'option2' => '选项 2'
            ]" value="option2" required />
            <x-View::Input title="多选输入" type="checkbox" name="checkbox" left="auto" :options="[
                'option1' => '选项 1',
                'option2' => '选项 2'
            ]" value="option1|option2" required />
            <x-View::Input title="选择输入" type="select" name="select" left="auto" :options="[
                'option1' => '选项 1',
                'option2' => '选项 2'
            ]" value="option2" required />
            <x-View::Input title="长文本输入" type="textarea" name="textarea" placeholder="请输入一段文本" left="auto" />
            <x-View::Input title="代码输入" type="code" name="code" placeholder="请输入一段代码" left="auto" />
            <x-View::Input title="颜色输入" type="color" name="color" value="#2b2b2b" left="auto" required />
            <x-View::Input title="Markdown" type="markdown" name="markdown" left="auto" value="Markdown" required />
            <x-View::Input title="上传文件" type="upload" name="upload" max="2" exts="jpg,png" left="auto" value="" />
            <x-View::Input type="button" left="auto">
                <x-View::Button icon="bi-star" type="submit">提交</x-View::Button>
            </x-View::Input>
        </form>
    </x-View::Card>
    <x-View::Card icon="bi-star" open="off">
    </x-View::Card>
</div>
@endsection

@push( 'footer' )
<script>
    function submitData( el ) {
        Core.web( '/debug' )
            .method( 'POST' )
            .data( el )
            .loading(( s ) => { Core.loading( s ); })
            .success(( res ) => {
                console.table( res );
            })
            .request();
    }
</script>
@endpush

@push( 'head' )
<style>

</style>
@endpush
