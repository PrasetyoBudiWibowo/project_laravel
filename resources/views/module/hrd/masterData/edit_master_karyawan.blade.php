@extends('layout.app')

@section('title', 'EDIT KARYAWAN')

@section('content')
<edit-master-karyawan encrypted="{{ $encrypted }}"></edit-master-karyawan>
@endsection