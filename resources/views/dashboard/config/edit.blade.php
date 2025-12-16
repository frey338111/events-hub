@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">Edit Config</h1>

        <form action="{{ route('dashboard.config.update', $config) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-medium mb-1">Name</label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $config->name) }}"
                       class="w-full border rounded px-3 py-2"
                       required>
            </div>
            <div>
                <label class="block font-medium mb-1">Value</label>
                <input type="text"
                       name="value"
                       value="{{ old('value', $config->value) }}"
                       class="w-full border rounded px-3 py-2"
                       required>
            </div>
            <div class="flex items-center space-x-3">
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Save
                </button>
                <a href="{{ route('dashboard.config.index') }}" class="text-gray-600 hover:underline">Cancel</a>
            </div>
        </form>
    </div>
@endsection
