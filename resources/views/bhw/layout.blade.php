@extends('layouts.app')

@section('title', 'BHW Portal - ReproCare')

@include('includes.sidebar')

@section('content')
<main class="main-content">
    @yield('bhw-content')
</main>
@endsection
