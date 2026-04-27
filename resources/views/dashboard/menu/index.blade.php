@extends('layouts.app')

@section('content')
    <div class="container mx-auto max-w-5xl px-4 py-6">
        @if(session('success'))
            <div class="mb-4 rounded border border-green-300 bg-green-50 p-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Menu Builder</h1>
            <p class="mt-1 text-sm text-gray-600">Drag and reorder menu items with SortableJS.</p>
        </div>

        @php
            $renderMenuItems = function (array $items, int $level = 0) use (&$renderMenuItems) {
                $paddingClass = match ($level) {
                    0 => '',
                    1 => 'ps-6',
                    2 => 'ps-10',
                    default => 'ps-12',
                };

                $html = '<div class="js-menu-list space-y-3 '.trim($paddingClass).'" data-level="'.$level.'">';

                foreach ($items as $item) {
                    $id = e((string) ($item['id'] ?? ''));
                    $label = e((string) ($item['label'] ?? ''));
                    $url = e((string) ($item['url'] ?? ''));

                    $html .= '<div class="js-menu-item rounded-lg border border-gray-200 bg-gray-50 p-4" data-id="'.$id.'">';
                    $html .= '<div class="flex cursor-move items-center justify-between gap-3 rounded-md border border-transparent bg-white px-4 py-3 shadow-sm">';
                    $html .= '<div><p class="text-sm text-gray-700"><span class="js-menu-label font-medium text-gray-900">'.$label.'</span> (<span class="js-menu-url text-gray-500">'.$url.'</span>)</p></div>';
                    $html .= '<span class="text-sm text-gray-400">Drag</span>';
                    $html .= '</div>';
                    $html .= '<div class="mt-3 rounded-lg border border-dashed border-gray-200 bg-gray-100/70 p-3">';
                    $html .= '<p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-400">Children</p>';
                    $html .= $renderMenuItems(is_array($item['children'] ?? null) ? $item['children'] : [], $level + 1);
                    $html .= '</div>';
                    $html .= '</div>';
                }

                $html .= '</div>';

                return $html;
            };
        @endphp

        <form action="{{ route('dashboard.menu.store') }}" method="POST" class="rounded-lg bg-white p-6 shadow">
            @csrf

            <div class="mb-4 flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-gray-900">Menu Items</h2>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-500">Nested drag and drop only updates in the browser until you save.</span>
                    <button
                        id="open-menu-item-modal"
                        type="button"
                        class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700"
                    >
                        Add Menu Item
                    </button>
                </div>
            </div>

            <div id="menu-items">
                {!! $renderMenuItems($menuItems) !!}
            </div>

            <input id="menu-json" type="hidden" name="menu_json" value="">

            <div class="mt-6 flex justify-end">
                <button
                    type="submit"
                    class="rounded bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-black"
                >
                    Save Menu
                </button>
            </div>
        </form>
    </div>

    <div id="menu-item-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/50 px-4">
        <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-2xl">
            <div class="mb-4 flex items-start justify-between gap-3">
                <div>
                    <h2 id="menu-item-modal-title" class="text-lg font-semibold text-gray-900">Add Menu Item</h2>
                    <p id="menu-item-modal-description" class="mt-1 text-sm text-gray-500">Create a new item and place it in the root menu container.</p>
                </div>
                <button id="close-menu-item-modal" type="button" class="text-sm text-gray-400 hover:text-gray-600">
                    Close
                </button>
            </div>

            <form id="menu-item-form" class="space-y-4">
                <div>
                    <label for="menu-item-title" class="mb-1 block text-sm font-medium text-gray-700">Title</label>
                    <input
                        id="menu-item-title"
                        name="title"
                        type="text"
                        class="w-full rounded border border-gray-300 px-3 py-2"
                        required
                    >
                </div>

                <div>
                    <label for="menu-item-url" class="mb-1 block text-sm font-medium text-gray-700">URL</label>
                    <input
                        id="menu-item-url"
                        name="url"
                        type="text"
                        class="w-full rounded border border-gray-300 px-3 py-2"
                        placeholder="/example"
                        required
                    >
                </div>

                <div class="flex items-center justify-end gap-3">
                    <button
                        id="cancel-menu-item-modal"
                        type="button"
                        class="rounded border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                    >
                        Cancel
                    </button>
                    <button
                        id="save-menu-item-button"
                        type="submit"
                        class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700"
                    >
                        Add
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .sortable-ghost {
            opacity: 0.45;
        }

        .sortable-drag {
            transform: rotate(1deg);
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menuRoot = document.getElementById('menu-items');
            const menuJsonInput = document.getElementById('menu-json');
            const modal = document.getElementById('menu-item-modal');
            const openModalButton = document.getElementById('open-menu-item-modal');
            const closeModalButton = document.getElementById('close-menu-item-modal');
            const cancelModalButton = document.getElementById('cancel-menu-item-modal');
            const menuItemForm = document.getElementById('menu-item-form');
            const modalTitle = document.getElementById('menu-item-modal-title');
            const modalDescription = document.getElementById('menu-item-modal-description');
            const saveMenuItemButton = document.getElementById('save-menu-item-button');
            const titleInput = document.getElementById('menu-item-title');
            const urlInput = document.getElementById('menu-item-url');
            const rootList = menuRoot ? menuRoot.querySelector(':scope > .js-menu-list') : null;
            let editingMenuItem = null;
            let nextMenuItemId = Math.max(
                1,
                ...Array.from(menuRoot?.querySelectorAll('.js-menu-item') ?? []).map(function (item) {
                    return Number(item.dataset.id || 0) + 1;
                })
            );

            if (!menuRoot || !rootList || !menuJsonInput || typeof Sortable === 'undefined') {
                return;
            }

            const getPaddingClass = function (level) {
                if (level === 1) {
                    return 'ps-6';
                }

                if (level === 2) {
                    return 'ps-10';
                }

                if (level >= 3) {
                    return 'ps-12';
                }

                return '';
            };

            const applyLevelSpacing = function (listElement) {
                const level = Number(listElement.dataset.level || 0);

                listElement.classList.remove('ps-6', 'ps-10', 'ps-12');

                const paddingClass = getPaddingClass(level);

                if (paddingClass) {
                    listElement.classList.add(paddingClass);
                }
            };

            const renderOrder = function () {
                updateListLevels(rootList, 0);
                menuJsonInput.value = JSON.stringify(buildTree(rootList));
            };

            const isPointInsideMenuRoot = function (clientX, clientY) {
                const rect = menuRoot.getBoundingClientRect();

                return clientX >= rect.left
                    && clientX <= rect.right
                    && clientY >= rect.top
                    && clientY <= rect.bottom;
            };

            const ensureSortable = function (listElement) {
                if (!listElement || listElement.dataset.sortableBound === 'true') {
                    return;
                }

                applyLevelSpacing(listElement);

                new Sortable(listElement, {
                    group: 'nested-menu',
                    animation: 150,
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    ghostClass: 'sortable-ghost',
                    dragClass: 'sortable-drag',
                    onSort: renderOrder,
                    onAdd: renderOrder,
                    onUpdate: renderOrder,
                    onEnd: function (event) {
                        const originalEvent = event.originalEvent;

                        if (!originalEvent) {
                            renderOrder();

                            return;
                        }

                        if (!isPointInsideMenuRoot(originalEvent.clientX, originalEvent.clientY)) {
                            event.item.remove();
                        }

                        renderOrder();
                    },
                });

                listElement.dataset.sortableBound = 'true';
            };

            const createMenuItemElement = function (title, url) {
                const itemElement = document.createElement('div');
                const childrenWrapper = document.createElement('div');
                const childrenHeading = document.createElement('p');
                const childList = document.createElement('div');
                const header = document.createElement('div');
                const info = document.createElement('div');
                const summary = document.createElement('p');
                const dragText = document.createElement('span');

                itemElement.className = 'js-menu-item rounded-lg border border-gray-200 bg-gray-50 p-4';
                itemElement.dataset.id = String(nextMenuItemId++);

                header.className = 'flex cursor-move items-center justify-between gap-3 rounded-md border border-transparent bg-white px-4 py-3 shadow-sm';
                summary.className = 'text-sm text-gray-700';
                summary.innerHTML = '<span class="js-menu-label font-medium text-gray-900"></span> (<span class="js-menu-url text-gray-500"></span>)';
                summary.querySelector('.js-menu-label').textContent = title;
                summary.querySelector('.js-menu-url').textContent = url;
                dragText.className = 'text-sm text-gray-400';
                dragText.textContent = 'Drag';

                info.appendChild(summary);
                header.appendChild(info);
                header.appendChild(dragText);

                childrenWrapper.className = 'mt-3 rounded-lg border border-dashed border-gray-200 bg-gray-100/70 p-3';
                childrenHeading.className = 'mb-2 text-xs font-semibold uppercase tracking-wide text-gray-400';
                childrenHeading.textContent = 'Children';
                childList.className = 'js-menu-list space-y-3';
                childList.dataset.level = '1';

                childrenWrapper.appendChild(childrenHeading);
                childrenWrapper.appendChild(childList);

                itemElement.appendChild(header);
                itemElement.appendChild(childrenWrapper);

                ensureSortable(childList);

                return itemElement;
            };

            const updateMenuItemElement = function (itemElement, title, url) {
                itemElement.querySelector(':scope > div .js-menu-label').textContent = title;
                itemElement.querySelector(':scope > div .js-menu-url').textContent = url;
            };

            const updateListLevels = function (listElement, level) {
                listElement.dataset.level = String(level);
                applyLevelSpacing(listElement);

                Array.from(listElement.children)
                    .filter(function (element) {
                        return element.classList.contains('js-menu-item');
                    })
                    .forEach(function (itemElement) {
                        const childList = itemElement.querySelector(':scope > div:last-child > .js-menu-list');

                        if (childList) {
                            updateListLevels(childList, level + 1);
                        }
                    });
            };

            const buildTree = function (listElement) {
                return Array.from(listElement.children)
                    .filter(function (element) {
                        return element.classList.contains('js-menu-item');
                    })
                    .map(function (itemElement) {
                        const childList = itemElement.querySelector(':scope > div:last-child > .js-menu-list');

                        return {
                            id: Number(itemElement.dataset.id),
                            label: itemElement.querySelector(':scope > div .js-menu-label')?.textContent?.trim() || '',
                            url: itemElement.querySelector(':scope > div .js-menu-url')?.textContent?.trim() || '',
                            children: childList ? buildTree(childList) : [],
                        };
                    });
            };

            const setModalMode = function (mode) {
                if (mode === 'edit') {
                    modalTitle.textContent = 'Edit Menu Item';
                    modalDescription.textContent = 'Update the menu item title and URL.';
                    saveMenuItemButton.textContent = 'Update';

                    return;
                }

                modalTitle.textContent = 'Add Menu Item';
                modalDescription.textContent = 'Create a new item and place it in the root menu container.';
                saveMenuItemButton.textContent = 'Add';
            };

            const openAddModal = function () {
                editingMenuItem = null;
                setModalMode('add');
                menuItemForm.reset();
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                titleInput.focus();
            };

            const openEditModal = function (itemElement) {
                const label = itemElement.querySelector(':scope > div .js-menu-label')?.textContent?.trim() || '';
                const url = itemElement.querySelector(':scope > div .js-menu-url')?.textContent?.trim() || '';

                editingMenuItem = itemElement;
                setModalMode('edit');
                titleInput.value = label;
                urlInput.value = url;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                titleInput.focus();
                titleInput.select();
            };

            const applyEditFromModal = function () {
                if (!editingMenuItem) {
                    return true;
                }

                const title = titleInput.value.trim();
                const url = urlInput.value.trim();

                if (!title || !url) {
                    menuItemForm.reportValidity();

                    return false;
                }

                updateMenuItemElement(editingMenuItem, title, url);
                renderOrder();

                return true;
            };

            const closeModal = function (commitEdit = false) {
                if (commitEdit && !applyEditFromModal()) {
                    return;
                }

                modal.classList.add('hidden');
                modal.classList.remove('flex');
                menuItemForm.reset();
                editingMenuItem = null;
            };

            Array.from(menuRoot.querySelectorAll('.js-menu-list')).forEach(function (listElement) {
                ensureSortable(listElement);
            });

            openModalButton.addEventListener('click', openAddModal);
            closeModalButton.addEventListener('click', function () {
                closeModal(false);
            });
            cancelModalButton.addEventListener('click', function () {
                closeModal(false);
            });
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeModal(Boolean(editingMenuItem));
                }
            });
            menuRoot.addEventListener('dblclick', function (event) {
                const itemElement = event.target.closest('.js-menu-item');

                if (!itemElement || !menuRoot.contains(itemElement)) {
                    return;
                }

                event.preventDefault();
                openEditModal(itemElement);
            });
            menuItemForm.addEventListener('submit', function (event) {
                event.preventDefault();

                const title = titleInput.value.trim();
                const url = urlInput.value.trim();

                if (!title || !url) {
                    return;
                }

                if (editingMenuItem) {
                    updateMenuItemElement(editingMenuItem, title, url);
                } else {
                    rootList.appendChild(createMenuItemElement(title, url));
                }

                closeModal();
                renderOrder();
            });

            renderOrder();
        });
    </script>
@endsection
