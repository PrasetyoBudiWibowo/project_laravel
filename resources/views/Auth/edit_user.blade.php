@extends('layout.app')

@section('title', 'ubah data user')

@section('content')
<edit-user :encrypted-id="'{{ $encryptedId }}'"></edit-user>
@endsection