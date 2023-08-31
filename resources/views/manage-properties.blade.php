@extends('layouts.master')
@section('title', 'Manage Properties')
@section('content')
    @include('layouts.flash-message')
    <div id="app">
        <manage-properties></manage-properties>
    </div>
@endsection
