@php
    $record = $getRecord();
    $avatarUrl = filled( $record->avatar ) && Storage::disk( 'public' )->exists( $record->avatar )
        ? Storage::disk( 'public' )->url( $record->avatar )
        : null;
@endphp

<div style="display: inline-flex; align-items: center; gap: 0.5rem;">
    @if ( $avatarUrl )
        <img
            src="{{ $avatarUrl }}"
            alt="{{ $record->name ?? $record->nickname ?? '用户头像' }}"
            style="display: block; width: 1.25rem; height: 1.25rem; flex-shrink: 0; border-radius: 50%; object-fit: cover;"
        >
    @endif
    <span>{{ $record->name ?: '-' }}</span>
</div>
