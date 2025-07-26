@extends('layout.app')

@section('title', 'User Register')

@section('content')
<div id="app">
    <user-register></user-register>
</div>

<script src="{{ mix('js/app.js') }}"></script>
@endsection