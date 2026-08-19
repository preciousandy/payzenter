@props([
'url',
'color' => 'red',
'align' => 'center',
])
<a href="{{ $url }}" class="button button-{{ $color }}" target="_blank" rel="noopener">
    {{ $slot }}
</a>