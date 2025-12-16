@extends('layouts.app')

@section('content')
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <strong>There were some problems with your input.</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Edit Event: {{ $event->title }}
    </h2>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow p-6 rounded">
                @include('dashboard.events.form.event')
            </div>
        </div>
    </div>

@endsection