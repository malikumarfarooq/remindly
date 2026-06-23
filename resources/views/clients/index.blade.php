@extends('layouts.app')

@section('page-title', 'Clients')

@section('topbar-actions')
    <a href="{{ route('clients.create') }}" class="btn btn-primary"><i class="ti ti-plus"></i> New Client</a>
@endsection

@section('content')
    @livewire('client-list')
@endsection