@extends('layouts.app')

@section('page-title', 'Recurring Invoices')

@section('topbar-actions')
    <a href="{{ route('recurring.create') }}" class="btn btn-primary"><i class="ti ti-plus"></i> New Recurring Invoice</a>
@endsection

@section('content')
    @livewire('recurring-invoice-list')
@endsection