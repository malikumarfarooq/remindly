@extends('layouts.app')
@section('title', 'Coming Soon')
@section('page-title', 'Coming Soon')

@section('content')
<div style="display:flex;align-items:center;justify-content:center;height:400px;flex-direction:column;gap:16px">
    <i class="ti ti-tools" style="font-size:64px;color:#dadce0"></i>
    <p style="font-size:18px;font-weight:500;color:#5f6368">This section is under development</p>
    <a href="{{ route('dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
</div>
@endsection