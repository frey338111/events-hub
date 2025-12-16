@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        @if(session('success'))
            <div class="mb-4 rounded border border-green-300 bg-green-50 p-3 text-green-800">
                {{ session('success') }}
            </div>
    @endif

    @include('dashboard.config.type.model',['name'=>'General Config','data' => $configs['data'],'column' => $configs['column']])
    @include('dashboard.config.type.model',['name'=>'Event Type','data' => $eventTypes['data'],'column' => $eventTypes['column']])
    @include('dashboard.config.type.model',['name'=>'Event Location','data' => $eventLocations['data'],'column' => $eventLocations['column']])

@endsection
