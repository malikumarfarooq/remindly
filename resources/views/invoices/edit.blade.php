@extends('layouts.app')

@section('page-title', 'Edit Invoice')

@section('content')
    @livewire('invoice-form', ['invoice' => $invoice])
@endsection