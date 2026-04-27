@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        @if(session('success'))
            <div class="mb-4 rounded border border-green-300 bg-green-50 p-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pages</h1>
                <p class="mt-1 text-sm text-gray-600">Manage static pages and page content.</p>
            </div>

            <a href="{{ route('dashboard.pages.create') }}"
               class="rounded bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700">
                + Add New Page
            </a>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">All Pages</h2>
            </div>

            <div class="p-6">
                @if($pages->isEmpty())
                    <div class="rounded border border-dashed border-gray-300 bg-gray-50 p-8 text-center">
                        <p class="text-sm text-gray-600">No pages available yet.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @foreach($pages as $page)
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-5 shadow-sm">
                                <div class="mb-3 flex items-start justify-between gap-3">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $page->title }}</h3>
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $page->published ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ $page->published ? 'Published' : 'Draft' }}
                                    </span>
                                </div>

                                <p class="mb-2 text-sm text-gray-600">
                                    <span class="font-medium text-gray-700">Slug:</span> {{ $page->slug }}
                                </p>

                                <p class="text-sm text-gray-500">
                                    Created {{ optional($page->created_at)->format('Y-m-d H:i') }}
                                </p>

                                <div class="mt-4 flex items-center gap-4">
                                    <a
                                        href="{{ route('dashboard.pages.edit', $page) }}"
                                        class="text-sm font-medium text-blue-600 hover:underline"
                                    >
                                        Edit page
                                    </a>

                                    <form
                                        action="{{ route('dashboard.pages.destroy', $page) }}"
                                        method="POST"
                                        onsubmit="return confirm('Delete this page? This action cannot be undone.');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="text-sm font-medium text-red-600 hover:underline"
                                        >
                                            Delete page
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $pages->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
