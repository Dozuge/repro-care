@extends('layouts.app')

@section('title', 'BHW President Portal - ReproCare')

@include('includes.sidebar')

@section('content')
<main class="main-content">
    @yield('bhw-president-content')
</main>
@endsection
