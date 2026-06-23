@extends('layouts.app')

@section('page-title', 'Edit Recurring Invoice')

@section('content')
    @livewire('recurring-invoice-form', ['recurring' => $recurring])
@endsection