@extends('layouts.app')
@section('title', 'Overview')
@section('content')
    <p class="eyebrow">YOUR WORKSPACE, AT A GLANCE</p>
    <h1>Welcome to Counter.</h1>
    <p class="lead">A clear starting point for your point-of-sale system. Explore customer and team accounts, one page at a time.</p>
    <section class="hero" aria-labelledby="hero-title">
        <div><span class="hero-label">LARAVEL POS FOUNDATIONS</span><h2 id="hero-title">Good systems start<br>with the essentials.</h2><p>Four connected pages. A shared interface. A simple foundation for learning how a Laravel application works.</p><a class="button" href="{{ route('customers.index') }}">Explore customer accounts <span aria-hidden="true">↗</span></a></div>
        <div class="hero-art" aria-hidden="true"><span>COUNTER</span><div class="receipt"><i></i><i></i><i></i><b>POS</b><small>FOUNDATIONS</small></div><span>01 / 04</span></div>
    </section>
    <div class="section-heading"><h2>Explore your workspace</h2><span>Start with the essentials</span></div>
    <div class="cards">
        <a class="card" href="{{ route('customers.index') }}"><span class="card-number">01 <span>↗</span></span><h3>Customer Accounts</h3><p>Get to know the people behind every purchase. Browse sample customer contact details and statuses.</p><span class="card-link">View customers →</span></a>
        <a class="card" href="{{ route('users.index') }}"><span class="card-number">02 <span>↗</span></span><h3>User Accounts</h3><p>Meet the team behind the counter. Explore sample staff accounts, roles, and account statuses.</p><span class="card-link">View users →</span></a>
        <a class="card" href="{{ route('about') }}"><span class="card-number">03 <span>↗</span></span><h3>About the project</h3><p>See how routes, controllers, and Blade templates come together in this Laravel learning project.</p><span class="card-link">Learn more →</span></a>
    </div>
@endsection
