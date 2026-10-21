@props(['name'])

@php
    $tablerNames = [
        'search' => 'search', 'eye' => 'eye', 'location' => 'map-pin', 'star' => 'star',
        'star-outline' => 'star', 'chat' => 'message-circle', 'package' => 'package',
        'alert' => 'alert-triangle', 'shield' => 'shield', 'check-circle' => 'circle-check',
        'x-circle' => 'circle-x', 'calendar' => 'calendar', 'printer' => 'printer',
        'arrow-left' => 'arrow-left', 'arrow-right' => 'arrow-right', 'trend-up' => 'trend-up',
        'trend-down' => 'trend-down',
    ];
    if (! isset($tablerNames[$name])) throw new InvalidArgumentException("Unknown UI icon [{$name}].");
@endphp

<x-public.home-icon :name="$tablerNames[$name]" {{ $attributes->merge(['class' => 'inline-block h-5 w-5 shrink-0']) }} />

