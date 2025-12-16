@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">Create Event</h1>

        @include('dashboard.events.form.event')
    </div>
@endsection
