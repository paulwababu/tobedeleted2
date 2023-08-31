@extends('layouts.master')
@section('title', 'Wallet Transactions')
@section('content')
    <div id="app">
        <wallet-transactions :company_uuid='@json($company_uuid)'></wallet-transactions>
    </div>
@endsection
