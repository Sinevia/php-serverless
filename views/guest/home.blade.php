@extends('guest.layout',['webpage_title'=>'Home'])

@section('content')
<section class="content">
    <div class="container text-center">
        <div class="title">
            Welcome
        </div>

        <div class="links">
            <a href="https://github.com/Sinevia/php-serverless">GitHub</a>
        </div>
    </div>
</section>

@endsection

@section('styles')
<style>
.title {
    font-size: 46px;
    margin: 80px 0px 40px 0px;
}

.links>a {
    color: #636b6f;
    padding: 0 25px;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: .1rem;
    text-decoration: none;
    text-transform: uppercase;
}
</style>
@endsection