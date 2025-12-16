@php
    $columns = [];
    if (is_array($column)) {
        $columns = array_keys($column);
        if (empty($columns) || array_keys($column) === range(0, count($column) - 1)) {
            $columns = $column;
        }
    }

    $nameField =  str_replace(' ', '', $name);
    $modelClassName = '';
    if (!empty($data)) {
        $firstItem = is_array($data)
            ? reset($data)
            : (is_object($data) && method_exists($data, 'first') ? $data->first() : null);

        if ($firstItem) {
            $classParts = explode('\\', get_class($firstItem));
            $modelClassName = end($classParts);
        }
    }
@endphp

<div class="flex items-center justify-between mt-8 mb-4">
    <button type="button"
            id="{{$nameField}}-toggle"
            class="flex items-center space-x-2 text-2xl font-bold focus:outline-none"
            onclick="toggle{{$nameField}}()">
        <span data-icon>&gt;</span>
        <span>Config {{ $name }}</span>
    </button>
</div>


<div id="{{$nameField}}-panel" class="hidden">
    <div class="flex items-center justify-end mb-3">
        <button
            type="button"
            onclick="open{{$nameField}}Modal()"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
            + Add {{ $name }}
        </button>
    </div>
    <div class="bg-white shadow rounded">
        <table class="min-w-full">
            <thead class="bg-gray-100 text-left">
            <tr>
                <th class="px-4 py-2">ID</th>
                @foreach($columns as $field)
                    <th class="px-4 py-2">{{ ucfirst(str_replace('_', ' ', $field)) }}</th>
                @endforeach
                <th class="px-4 py-2">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($data as $item)
                <tr class="border-b">
                    <td class="px-4 py-2">{{ $item->id }}</td>
                    @foreach($columns as $field)
                        <td class="px-4 py-2">{{ $item->$field }}</td>
                    @endforeach
                    <td class="px-4 py-2">
                        <button
                            type="button"
                            class="text-blue-600 hover:underline"
                            data-id="{{ $item->id }}"
                            @foreach($columns as $field)
                                data-field-{{ $field }}="{{ $item->$field }}"
                            @endforeach
                            onclick="open{{$nameField}}Modal(this)"
                        >
                            Edit
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) + 2 }}" class="px-4 py-4 text-center text-gray-600">
                        No records found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="{{$nameField}}-add-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black opacity-50" onclick="close{{$nameField}}Modal()"></div>
    <div class="relative bg-white rounded shadow-lg w-full max-w-lg mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">Add/Edit {{ $name }}</h2>
            <button class="text-gray-600 hover:text-gray-900" onclick="close{{$nameField}}Modal()">✕</button>
        </div>
        <form action="{{ route('dashboard.config.updatemodel') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="model_name" value="{{ $modelClassName }}">
            <input type="hidden" name="id" id="{{$nameField}}-id">
            @foreach($columns as $field)
                <div>
                    <label class="block font-medium mb-1" for="{{$nameField}}-{{ $field }}">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                    <input
                        id="{{$nameField}}-{{ $field }}"
                        name="{{ $field }}"
                        type="text"
                        class="w-full border rounded px-3 py-2"
                        required
                    >
                </div>
            @endforeach
            <div class="flex items-center justify-end space-x-2">
                <button type="button"
                        class="px-4 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-100"
                        onclick="close{{$nameField}}Modal()">
                    Cancel
                </button>
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const {{$nameField}}Panel = document.getElementById('{{$nameField}}-panel');
    const {{$nameField}}Toggle = document.getElementById('{{$nameField}}-toggle');
    const {{$nameField}}AddModal = document.getElementById('{{$nameField}}-add-modal');
    const {{$nameField}}IdInput = document.getElementById('{{$nameField}}-id');

    function toggle{{$nameField}}() {
        const willShow = {{$nameField}}Panel.classList.contains('hidden');
        {{$nameField}}Panel.classList.toggle('hidden');
        const icon = {{$nameField}}Toggle.querySelector('[data-icon]');
        if (icon) {
            icon.textContent = willShow ? 'v' : '>';
        }
    }

    function open{{$nameField}}Modal(button) {
        const isEdit = !!button;
        {{$nameField}}IdInput.value = isEdit ? (button.getAttribute('data-id') || '') : '';
        @foreach($columns as $field)
        (document.getElementById('{{$nameField}}-{{ $field }}') || {}).value = isEdit
            ? (button ? button.getAttribute('data-field-{{ $field }}') || '' : '')
            : '';
        @endforeach
        {{$nameField}}AddModal.classList.remove('hidden');
    }

    function close{{$nameField}}Modal() {
        {{$nameField}}AddModal.classList.add('hidden');
        {{$nameField}}IdInput.value = '';
        @foreach($columns as $field)
        const {{$nameField}}Field{{ $loop->index }} = document.getElementById('{{$nameField}}-{{ $field }}');
        if ({{$nameField}}Field{{ $loop->index }}) {
            {{$nameField}}Field{{ $loop->index }}.value = '';
        }
        @endforeach
    }
</script>
