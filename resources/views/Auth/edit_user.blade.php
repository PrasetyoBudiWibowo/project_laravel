@extends('layout.app')

@section('title', 'ubah data user')

@section('content')
    <div id="app">
        <edit-user :encrypted-id="'{{ $encryptedId }}'"></edit-user>
    </div>

<script src="{{ asset('js/app.js') }}"></script>
@endsection