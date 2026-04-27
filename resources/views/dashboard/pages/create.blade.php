@extends('layouts.app')

@section('content')
    <div class="container mx-auto max-w-3xl px-4 py-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Create Page</h1>
            <p class="mt-1 text-sm text-gray-600">Add a new static page to the site.</p>
        </div>

        <div class="rounded-lg bg-white p-6 shadow">
            <form action="{{ route('dashboard.pages.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="title" class="mb-1 block font-medium text-gray-700">Title</label>
                    <input
                        id="title"
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                        class="w-full rounded border border-gray-300 px-3 py-2"
                        required
                    >
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="slug" class="mb-1 block font-medium text-gray-700">Slug</label>
                    <input
                        id="slug"
                        type="text"
                        name="slug"
                        value="{{ old('slug') }}"
                        class="w-full rounded border border-gray-300 px-3 py-2"
                        placeholder="leave blank to generate from title"
                    >
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="content" class="mb-1 block font-medium text-gray-700">Content</label>
                    <textarea
                        id="content"
                        name="content"
                        rows="10"
                        class="w-full rounded border border-gray-300 px-3 py-2"
                        required
                    >{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-3 text-sm text-gray-700">
                    <input
                        type="checkbox"
                        name="published"
                        value="1"
                        class="rounded border-gray-300"
                        {{ old('published') ? 'checked' : '' }}
                    >
                    Publish page immediately
                </label>

                <div class="flex items-center gap-3">
                    <button
                        type="submit"
                        class="rounded bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700"
                    >
                        Save Page
                    </button>

                    <a href="{{ route('dashboard.pages.index') }}" class="text-sm text-gray-600 hover:underline">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
