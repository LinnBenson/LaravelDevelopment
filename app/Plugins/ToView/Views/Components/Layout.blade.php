@props([
    'gap' => '0px',
    'min' => '80px'
])

<div  {{$attributes
    ->merge([
        'style' => '--gap: '.$gap.'; --min: '.$min.';'
    ])
->class([ 'toview-button-layout' ])}}>
    {{$slot}}
</div>