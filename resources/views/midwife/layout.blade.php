@extends('layouts.app')

@section('title', 'Midwife Portal - ReproCare')

@section('content')
<div class="layout-wrapper">
    @include('includes.sidebar')
    <main class="main-content">
        @yield('midwife-content')
    </main>
</div>
@endsection
