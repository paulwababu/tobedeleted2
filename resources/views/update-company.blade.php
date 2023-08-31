@extends('layouts.master')
@section('title', 'Update Company')
@section('content')
    <div id="app">
        <update-company :company_uuid='@json($company_uuid)'></update-company>
    </div>
@endsection
