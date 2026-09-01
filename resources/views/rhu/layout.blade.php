@extends('layouts.app')

@section('title', 'RHU Portal - ReproCare')

@section('content')
<div class="layout-wrapper">
    @include('includes.sidebar')
    <main class="main-content">
        @yield('rhu-content')
    </main>
</div>
@endsection
