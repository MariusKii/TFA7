<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="A Laravel POS foundation demonstrating routes, controllers, and Blade views.">
    <title>@yield('title') · Counter POS</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>
    <aside class="sidebar">
        <a class="brand" href="{{ route('home') }}"><span class="brand-mark" aria-hidden="true">C</span> Counter<span class="brand-sub">POS</span></a>
        <p class="nav-label">WORKSPACE</p>
        <nav aria-label="Main navigation">
            <a href="{{ route('home') }}" @class(['nav-link', 'active' => request()->routeIs('home')]) @if(request()->routeIs('home')) aria-current="page" @endif><span aria-hidden="true">01</span> Overview</a>
            <a href="{{ route('customers.index') }}" @class(['nav-link', 'active' => request()->routeIs('customers.index')]) @if(request()->routeIs('customers.index')) aria-current="page" @endif><span aria-hidden="true">02</span> Customer Accounts</a>
            <a href="{{ route('users.index') }}" @class(['nav-link', 'active' => request()->routeIs('users.index')]) @if(request()->routeIs('users.index')) aria-current="page" @endif><span aria-hidden="true">03</span> User Accounts</a>
            <a href="{{ route('about') }}" @class(['nav-link', 'active' => request()->routeIs('about')]) @if(request()->routeIs('about')) aria-current="page" @endif><span aria-hidden="true">04</span> About</a>
        </nav>
        <div class="sidebar-note"><span class="dot"></span> Learning workspace<p>IT0049 · Laravel foundations</p></div>
    </aside>
    <div class="workspace">
        <header class="topbar"><span>Counter / <strong>@yield('title')</strong></span><span class="tag">POS FOUNDATION</span></header>
        <main id="main" tabindex="-1">@yield('content')</main>
        <footer>Counter POS <span>Built with Laravel &amp; Blade · IT0049 TFA6</span></footer>
    </div>
</body>
</html>
