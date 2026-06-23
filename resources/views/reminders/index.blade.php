@extends('layouts.app')
@section('page-title', 'Reminder Sequences')
@section('topbar-actions')
    <a href="{{ route('reminders.create') }}" class="btn btn-primary"><i class="ti ti-plus"></i> New Sequence</a>
@endsection
@section('content')
    @livewire('reminder-sequence-list')
@endsection