@extends('layouts.app')
@section('title', 'About')
@section('content')
    <p class="eyebrow">THE FOUNDATION BEHIND THE INTERFACE</p>
    <h1>Simple by design.</h1>
    <p class="lead">Counter POS is a beginner-friendly point-of-sale foundation for IT0049 Technical Formative Assessment 5.</p>
    <section class="panel prose"><span class="tag">PROJECT PURPOSE</span><h2>Learn the flow, build the foundation.</h2><p>This application introduces the structure of a POS management workspace through a landing page, an about page, a customer listing, and a user listing. It focuses on Laravel MVC and navigation. Checkout, account editing, and authentication are outside this activity's scope.</p><p>Customer and user records are fictional sample data defined in PHP arrays inside their controllers. The four pages do not query a database. Laravel's default model, migrations, factory, and seeder remain available for future lessons.</p></section>
    <div class="section-heading"><h2>How Laravel MVC fits together</h2></div>
    <div class="cards">
        <article class="card"><span class="card-number">01 / ROUTE</span><h3>Choose the destination</h3><p>A request reaches routes/web.php, which maps a URL to a controller method using a named route.</p></article>
        <article class="card"><span class="card-number">02 / CONTROLLER &amp; DATA</span><h3>Prepare the response</h3><p>The controller prepares data and selects a view. A model normally manages persistent data; this exercise uses static arrays instead.</p></article>
        <article class="card"><span class="card-number">03 / VIEW</span><h3>Render the page</h3><p>Blade templates inherit a shared layout and use foreach loops to display records as table rows.</p></article>
    </div>
    <section class="panel stack"><h2>Built with the essentials</h2><p>PHP 8.3+ · Laravel 13 · Blade · HTML5 · CSS3 · Composer · PHPUnit</p><p>No JavaScript framework or frontend build is needed for these pages.</p></section>
@endsection
