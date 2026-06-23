@extends('layouts.app')

@section('page-title', 'Edit Client')

@section('content')
    @livewire('client-form', ['client' => $client])
@endsection