@extends('layouts.app')

@section('title', 'Portal - ReproCare')

@include('includes.sidebar')

@section('content')
<main class="main-content">
    @yield('user-content')
</main>
@endsection
