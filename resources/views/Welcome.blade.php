@extends( 'View::Frame' )

@section( 'title', 'Welcome' )

@section( 'body' )
<div id="main" class="padding32 sizing">
    <x-View::Card title="测试卡片">
        32323232
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
        32323232
    </x-View::Card>
</div>
@endsection

@push( 'head' )
<style>

</style>
@endpush
