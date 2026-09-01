@extends('layouts.app')

@section('title', 'CHO Portal - ReproCare')

@section('content')
<div class="layout-wrapper">
    @include('includes.sidebar')
    <main class="main-content">
        @yield('cho-content')
    </main>
</div>
@endsection
