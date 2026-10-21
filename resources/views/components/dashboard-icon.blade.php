@props(['name', 'class' => 'h-5 w-5'])

@php
    $tablerNames = [
        'dashboard' => 'dashboard', 'category' => 'category', 'users' => 'users',
        'product' => 'package', 'package' => 'package', 'add' => 'plus',
        'chat' => 'message-circle', 'bell' => 'bell', 'chart' => 'chart-bar',
        'user' => 'user', 'settings' => 'settings', 'logout' => 'logout',
        'clock' => 'clock', 'shield' => 'shield', 'activity' => 'activity',
        'check' => 'check', 'check-circle' => 'circle-check', 'eye' => 'eye',
        'edit' => 'edit', 'info' => 'info-circle', 'arrow-right' => 'arrow-right',
        'arrow-left' => 'arrow-left', 'alert' => 'alert-triangle', 'search' => 'search',
        'calendar' => 'calendar', 'printer' => 'printer', 'trend-up' => 'trend-up',
        'trend-down' => 'trend-down', 'trash' => 'trash', 'image' => 'photo',
        'menu' => 'menu-2', 'phone' => 'phone', 'x-circle' => 'circle-x',
        'star' => 'star', 'star-outline' => 'star',
    ];
    if (! isset($tablerNames[$name])) throw new InvalidArgumentException("Unknown dashboard icon: {$name}");
@endphp

<x-public.home-icon :name="$tablerNames[$name]" {{ $attributes->class(['dashboard-icon', $class]) }} />

