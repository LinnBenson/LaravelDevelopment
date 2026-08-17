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
    <x-View::Card icon="bi-star" open="off">
        <form onsubmit="Core.submit( this ); return false;">
            <x-View::Input icon="bi-person" value="test" placeholder="请输入用户名" title="用户名" />
            <x-View::Input title="文本输入" type="text" name="text" placeholder="请输入一段文本" left="auto" />
            <x-View::Input title="密码输入" type="password" name="password" placeholder="请输入一段密码" left="auto" error />
            <x-View::Input title="邮箱输入" type="email" name="email" placeholder="请输入一段邮箱" left="auto" disabled />
            <x-View::Input title="手机号输入" type="phone" name="phone" value="" code="+1" placeholder="请输入手机号" left="auto" />
            <x-View::Input title="开关按钮" type="switch" name="switch" left="auto"  />
            <x-View::Input title="数字输入" type="number" name="number" placeholder="请输入一段数字" left="auto" step="0.01" min="0" max="100" />
            <x-View::Input title="完整日期" type="datetime-local" name="datetime-local" placeholder="请输入完整日期" value="2024.06.06 12:00:00" left="auto" />
            <x-View::Input title="日期输入" type="date" name="date" placeholder="请输入日期" left="auto" />
            <x-View::Input title="时间输入" type="time" name="time" placeholder="请输入时间" :tips="[
                '这是第一个提示',
                '这是第二个提示',
            ]" left="auto" />
            <x-View::Input title="单选输入" type="radio" name="radio" left="auto" :options="[
                'option1' => '选项 1',
                'option2' => '选项 2',
                'option3' => '选项 3'
            ]" required />
            <x-View::Input title="多选输入" type="checkbox" name="checkbox" left="auto" value="option1|option4" :options="[
                'option1' => '选项 1',
                'option2' => '选项 2',
                'option3' => '选项 3',
                'option4' => '选项 4',
                'option5' => '选项选项选项 5',
                'option6' => '选项 6',
            ]" required />
            <x-View::Input title="选择输入" type="select" name="select" left="auto" value="option2" :options="[
                'option1' => '选项 1',
                'option2' => '选项 2',
                'option3' => '选项 3'
            ]" />
            <x-View::Input title="长文本输入" type="textarea" name="textarea" placeholder="请输入一段文本" left="auto" />
            <x-View::Input title="代码输入" type="code" name="code" placeholder="请输入一段代码" :tips="[
                '这是第一个提示',
                '这是第二个提示',
            ]" left="auto" />
            <x-View::Input title="颜色输入" type="color" name="color" value="#ff0000" :tips="[
                '这是第一个提示',
                '这是第二个提示',
            ]" left="auto" />
            <x-View::Input type="button" left="auto">
                <x-View::Button icon="bi-star" type="submit">提交</x-View::Button>
            </x-View::Input>
        </form>
    </x-View::Card>
</div>
@endsection

@push( 'head' )
<style>

</style>
@endpush
