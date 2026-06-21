@extends('layouts.app')

@section('page-title', 'Invoices')

@section('topbar-actions')
    <button class="btn btn-outline"><i class="ti ti-download"></i> Export</button>
    <a href="{{ route('invoices.create') }}" class="btn btn-primary"><i class="ti ti-plus"></i> New Invoice</a>
@endsection

@section('content')
    @livewire('invoice-list')
@endsection