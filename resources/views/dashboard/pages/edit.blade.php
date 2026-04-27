@extends('layouts.app')

@section('content')
    <div class="container mx-auto max-w-5xl px-4 py-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Edit Page</h1>
            <p class="mt-1 text-sm text-gray-600">Update the page title, slug, content, and publish state.</p>
        </div>

        <div class="rounded-lg bg-white p-6 shadow">
            <form action="{{ route('dashboard.pages.update', $page) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="title" class="mb-1 block font-medium text-gray-700">Title</label>
                    <input
                        id="title"
                        type="text"
                        name="title"
                        value="{{ old('title', $page->title) }}"
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
                        value="{{ old('slug', $page->slug) }}"
                        class="w-full rounded border border-gray-300 px-3 py-2"
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
                    >{{ old('content', $page->content) }}</textarea>
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
                        {{ old('published', $page->published) ? 'checked' : '' }}
                    >
                    Publish page
                </label>

                <div class="flex items-center gap-3">
                    <button
                        type="submit"
                        class="rounded bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700"
                    >
                        Update Page
                    </button>

                    <a href="{{ route('dashboard.pages.index') }}" class="text-sm text-gray-600 hover:underline">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <style>
        .ck-editor__editable_inline {
            min-height: 24rem;
        }
    </style>

    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editorElement = document.querySelector('#content');
            const uploadUrl = @json(route('dashboard.pages.upload-image'));
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            if (!editorElement || typeof ClassicEditor === 'undefined') {
                return;
            }

            function PageImageUploadAdapter(loader) {
                this.loader = loader;
            }

            PageImageUploadAdapter.prototype.upload = function () {
                return this.loader.file.then(function (file) {
                    return new Promise(function (resolve, reject) {
                        const formData = new FormData();
                        const xhr = new XMLHttpRequest();

                        formData.append('upload', file);

                        xhr.open('POST', uploadUrl, true);
                        xhr.responseType = 'json';

                        if (csrfToken) {
                            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                        }

                        xhr.addEventListener('error', function () {
                            reject('Image upload failed.');
                        });

                        xhr.addEventListener('abort', function () {
                            reject('Image upload aborted.');
                        });

                        xhr.addEventListener('load', function () {
                            const response = xhr.response;

                            if (xhr.status < 200 || xhr.status >= 300 || !response || !response.url) {
                                reject(response?.message || 'Image upload failed.');

                                return;
                            }

                            resolve({
                                default: response.url,
                            });
                        });

                        xhr.send(formData);
                    });
                });
            };

            PageImageUploadAdapter.prototype.abort = function () {
                return;
            };

            function PageImageUploadAdapterPlugin(editor) {
                editor.plugins.get('FileRepository').createUploadAdapter = function (loader) {
                    return new PageImageUploadAdapter(loader);
                };
            }

            ClassicEditor
                .create(editorElement, {
                    extraPlugins: [PageImageUploadAdapterPlugin],
                    toolbar: [
                        'heading', '|',
                        'bold', 'italic', 'link', '|',
                        'bulletedList', 'numberedList', '|',
                        'uploadImage', 'blockQuote', 'insertTable', '|',
                        'undo', 'redo'
                    ],
                    image: {
                        toolbar: [
                            'imageTextAlternative', '|',
                            'imageStyle:inline',
                            'imageStyle:block',
                            'imageStyle:side'
                        ]
                    }
                })
                .catch(function (error) {
                    console.error('CKEditor failed to initialize.', error);
                });
        });
    </script>
@endsection
