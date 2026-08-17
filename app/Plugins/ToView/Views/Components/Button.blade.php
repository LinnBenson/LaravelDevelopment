@props([
    'icon' => '',
    'color' => 'r3',
    'size' => 'default',
    'onClick' => null,
    'href' => null,
    'target' => '_self',
    'type' => 'button',
])

<div {{$attributes
    ->merge([
        'style' => '--background: var( --'.$color.' ); --color: var( --'.$color.'c );'
    ])
->class([ 'toview-button', 'toview-button-size-'.( $size ?? 'default' ), 'toview-button-icon'.( $icon ? '' : '-none' ), 'toview-button-text'.( $slot->isNotEmpty() ? '' : '-none' ) ])}}>
    <div class="toview-button-mask"></div>
    @if( $href )
        <a class="toview-button-content" href="{{$href}}" target="{{$target}}">
    @else
        <button class="toview-button-content" type="{{$type ?? 'button'}}" onclick="{{$onClick}}">
    @endif

        @if( $icon )
        <i class="{{ $icon }} block"></i>
        @endif
        @if( $slot->isNotEmpty() )
        <span>{{$slot}}</span>
        @endif

    @if( $href )
        </a>
    @else
        </button>
    @endif
</div>