@extends('layouts.app')
@section('page-title', 'Edit Reminder Sequence')
@section('content')
    @livewire('reminder-builder', ['sequence' => $sequence])
@endsection