@extends('layouts.master')
<?php
if (\Illuminate\Support\Facades\Auth::user()->hasRole('admin')){
    $role = 'Admin';
} elseif (\Illuminate\Support\Facades\Auth::user()->hasRole('owner')){
    $role = 'Owner';
} elseif (\Illuminate\Support\Facades\Auth::user()->hasRole('agent')){
    $role = 'Agent';
} else {
    $role = 'Tenant';
}

?>
@section('title', $role .' Dashboard')
@section('content')
    <div id="app">
        @role('admin')
        <admin-dashboard></admin-dashboard>
        @endrole
        @role('owner')
        <owner-dashboard></owner-dashboard>
        @endrole
        @role('tenant')
        <tenant-dashboard></tenant-dashboard>
        @endrole
        @role('agent')
        <agent-dashboard></agent-dashboard>
        @endrole
    </div>
@endsection
