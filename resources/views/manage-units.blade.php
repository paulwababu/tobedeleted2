@extends('layouts.master')
@section('title', 'Manage Units')
@section('content')
    <div id="app">
        <manage-units :property='@json($property)'></manage-units>
    </div>
@endsection
