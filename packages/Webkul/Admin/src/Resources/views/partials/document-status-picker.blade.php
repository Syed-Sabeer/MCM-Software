@php
    $renderControl = $renderControl ?? true;
    $includeManager = $includeManager ?? true;

    if ($renderControl) {
        $pickerId = $pickerId ?? 'document-status-picker-'.md5($type.'-'.$name.'-'.uniqid('', true));
        $selectedStatus = old($name, $selected ?? null);
        $statusOptions = \Webkul\Admin\Support\DocumentStatusOptions::all($type);
        $selectedStatusExists = collect($statusOptions)->contains(
            fn ($status) => $status['value'] === $selectedStatus
        );
        $selectClass = $selectClass ?? 'custom-select';
        $labelClass = $labelClass ?? 'mb-1 block text-sm font-medium dark:text-white';
        $selectAttributes = $selectAttributes ?? '';
    }
@endphp

@if ($renderControl)
    <div
        class="document-status-picker"
        id="{{ $pickerId }}"
        data-type="{{ $type }}"
        data-index-url="{{ route('admin.document_statuses.index', $type) }}"
        data-store-url="{{ route('admin.document_statuses.store', $type) }}"
        data-update-url="{{ route('admin.document_statuses.update', [$type, '__ID__']) }}"
        data-delete-url="{{ route('admin.document_statuses.delete', [$type, '__ID__']) }}"
        data-view-url="{{ route('admin.document_statuses.index', $type) }}"
        data-token="{{ csrf_token() }}"
    >
        <div class="mb-1 flex items-center justify-between gap-3">
            <label class="{{ $labelClass }} !mb-0">{{ $label ?? 'Status' }}</label>
            <div class="flex shrink-0 items-center gap-3 text-sm">
                <a href="#" class="document-status-picker-add text-brandColor hover:underline" title="Add status" onclick="event.preventDefault(); var picker = this.closest('.document-status-picker'); var modal = document.getElementById('document-status-modal'); var input = document.getElementById('document-status-modal-name'); var error = document.getElementById('document-status-modal-error'); var title = document.getElementById('document-status-modal-title'); var save = document.getElementById('document-status-modal-save'); if (picker && modal) { modal.dataset.activePickerId = picker.id; modal.dataset.activeMode = 'create'; modal.dataset.activeStatusId = ''; if (title) title.textContent = 'Add Status'; if (save) save.textContent = 'Save Status'; if (input) input.value = ''; if (error) { error.textContent = ''; error.classList.add('hidden'); } modal.style.display = 'flex'; modal.classList.remove('hidden'); modal.classList.add('flex'); if (input) setTimeout(function () { input.focus(); }, 0); }">Add Status</a>
                <a href="{{ route('admin.document_statuses.index', $type) }}" class="text-brandColor hover:underline">View Statuses</a>
            </div>
        </div>

        <select name="{{ $name }}" class="{{ $selectClass }} document-status-picker-select" {!! $selectAttributes !!}>
            @if (! $selectedStatusExists)
                <option value="" selected disabled>Select Status</option>
            @endif

            @foreach ($statusOptions as $status)
                <option
                    value="{{ $status['value'] }}"
                    data-id="{{ $status['id'] }}"
                    @selected($selectedStatus === $status['value'])
                >
                    {{ $status['name'] }}
                </option>
            @endforeach
        </select>

        @error($name)
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
@endif

@if ($includeManager)
@once
    <div id="document-status-modal" class="hidden" style="display: none; position: fixed; inset: 0; z-index: 100000; align-items: center; justify-content: center; padding: 24px; background: rgba(15, 23, 42, 0.45);">
        <div class="w-full max-w-md rounded bg-white shadow-xl dark:bg-gray-900" style="max-width: 460px;">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                <h3 id="document-status-modal-title" class="text-lg font-semibold text-gray-800 dark:text-white">Add Status</h3>
                <button
                    type="button"
                    id="document-status-modal-close"
                    class="icon-cross-large cursor-pointer rounded-md p-1 text-2xl text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800"
                    aria-label="Close"
                    title="Close"
                    onclick="event.preventDefault(); var modal = document.getElementById('document-status-modal'); if (modal) { modal.style.display = 'none'; modal.classList.add('hidden'); modal.classList.remove('flex'); modal.dataset.activePickerId = ''; modal.dataset.activeMode = ''; modal.dataset.activeStatusId = ''; document.body.style.overflow = 'auto'; }"
                ></button>
            </div>

            <div class="px-5 py-4">
                <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-white">Status Name</label>
                <input
                    type="text"
                    id="document-status-modal-name"
                    class="w-full rounded-md border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-800 outline-none transition focus:border-brandColor focus:ring-1 focus:ring-brandColor dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    placeholder="Enter status name"
                    autocomplete="off"
                >
                <p id="document-status-modal-error" class="mt-2 hidden text-xs text-red-600"></p>
            </div>

            <div class="flex justify-end gap-2 border-t border-gray-200 px-5 py-4 dark:border-gray-800">
                <button type="button" id="document-status-modal-cancel" class="secondary-button" onclick="event.preventDefault(); var modal = document.getElementById('document-status-modal'); if (modal) { modal.style.display = 'none'; modal.classList.add('hidden'); modal.classList.remove('flex'); modal.dataset.activePickerId = ''; modal.dataset.activeMode = ''; modal.dataset.activeStatusId = ''; document.body.style.overflow = 'auto'; }">Cancel</button>
                <button type="button" id="document-status-modal-save" class="primary-button" onclick="event.preventDefault(); var modal = document.getElementById('document-status-modal'); var input = document.getElementById('document-status-modal-name'); var error = document.getElementById('document-status-modal-error'); var button = this; var picker = modal && modal.dataset.activePickerId ? document.getElementById(modal.dataset.activePickerId) : null; var name = input ? input.value.trim() : ''; if (!picker || !modal) return; if (!name) { if (error) { error.textContent = 'Status name is required.'; error.classList.remove('hidden'); } return; } button.disabled = true; button.textContent = 'Saving...'; if (error) { error.textContent = ''; error.classList.add('hidden'); } fetch(picker.dataset.storeUrl, { method: 'POST', headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': picker.dataset.token, 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ name: name }) }).then(function (response) { return response.json().catch(function () { return {}; }).then(function (json) { if (!response.ok) throw new Error(json.message || 'Unable to save status.'); return json; }); }).then(function (json) { var select = picker.querySelector('.document-status-picker-select'); if (select && json.statuses) { select.innerHTML = ''; json.statuses.forEach(function (status) { var option = document.createElement('option'); option.value = status.value; option.textContent = status.name; if (status.id) option.dataset.id = status.id; select.appendChild(option); }); if (json.status) select.value = json.status.value; select.dispatchEvent(new Event('change', { bubbles: true })); } modal.style.display = 'none'; modal.classList.add('hidden'); modal.classList.remove('flex'); modal.dataset.activePickerId = ''; if (input) input.value = ''; }).catch(function (exception) { if (error) { error.textContent = exception.message; error.classList.remove('hidden'); } }).finally(function () { button.disabled = false; button.textContent = 'Save Status'; });">Save Status</button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            if (window.documentStatusPickerReady) {
                return;
            }

            window.documentStatusPickerReady = true;

            var modal = document.getElementById('document-status-modal');
            var title = document.getElementById('document-status-modal-title');
            var input = document.getElementById('document-status-modal-name');
            var error = document.getElementById('document-status-modal-error');
            var saveButton = document.getElementById('document-status-modal-save');
            var activePicker = null;
            var activeMode = 'create';
            var activeStatusId = null;

            function currentOption(picker) {
                var select = picker.querySelector('.document-status-picker-select');

                return select ? select.options[select.selectedIndex] : null;
            }

            function setError(message) {
                error.textContent = message || '';
                error.classList.toggle('hidden', !message);
            }

            function openModal(picker, mode) {
                if (!picker || !modal) {
                    return;
                }

                activePicker = picker;
                activeMode = mode;
                var option = currentOption(picker);

                activeStatusId = mode === 'edit' && option ? option.dataset.id : null;

                if (mode === 'edit' && !activeStatusId) {
                    setError('');
                    openModal(picker, 'create');
                    setError('Please save this status before editing it.');
                    return;
                }

                title.textContent = mode === 'edit' ? 'Edit Status' : 'Add Status';
                saveButton.textContent = mode === 'edit' ? 'Update Status' : 'Save Status';
                input.value = mode === 'edit' && option ? option.textContent.trim() : '';
                modal.dataset.activePickerId = picker.id;
                modal.dataset.activeMode = mode;
                modal.dataset.activeStatusId = activeStatusId || '';
                setError('');

                modal.style.display = 'flex';
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(function () { input.focus(); }, 0);
            }

            window.openDocumentStatusCreate = function (trigger) {
                var picker = trigger ? trigger.closest('.document-status-picker') : null;

                if (!picker) {
                    return;
                }

                openModal(picker, 'create');
            };

            function closeModal() {
                modal.style.display = 'none';
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                modal.dataset.activePickerId = '';
                modal.dataset.activeMode = '';
                modal.dataset.activeStatusId = '';
                activePicker = null;
                activeStatusId = null;
            }

            function request(url, method, payload, token) {
                return fetch(url, {
                    method: method,
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: payload ? JSON.stringify(payload) : null
                }).then(function (response) {
                    return response.json().catch(function () {
                        return {};
                    }).then(function (json) {
                        if (!response.ok) {
                            throw new Error(json.message || 'Unable to save status.');
                        }

                        return json;
                    });
                });
            }

            function refreshPicker(picker, statuses, selectedValue) {
                var select = picker.querySelector('.document-status-picker-select');

                if (!select) {
                    return;
                }

                select.innerHTML = '';

                statuses.forEach(function (status) {
                    var option = document.createElement('option');
                    option.value = status.value;
                    option.textContent = status.name;

                    if (status.id) {
                        option.dataset.id = status.id;
                    }

                    select.appendChild(option);
                });

                if (selectedValue) {
                    select.value = selectedValue;
                }

                select.dispatchEvent(new Event('change', { bubbles: true }));
            }

            function saveStatus() {
                if (!activePicker && modal.dataset.activePickerId) {
                    activePicker = document.getElementById(modal.dataset.activePickerId);
                    activeMode = modal.dataset.activeMode || 'create';
                    activeStatusId = modal.dataset.activeStatusId || null;
                }

                if (!activePicker) {
                    return;
                }

                var name = input.value.trim();

                if (!name) {
                    setError('Status name is required.');
                    return;
                }

                var token = activePicker.dataset.token;
                var isEdit = activeMode === 'edit';
                var url = isEdit
                    ? activePicker.dataset.updateUrl.replace('__ID__', activeStatusId)
                    : activePicker.dataset.storeUrl;

                saveButton.disabled = true;
                saveButton.textContent = isEdit ? 'Updating...' : 'Saving...';
                setError('');

                request(url, isEdit ? 'PUT' : 'POST', { name: name }, token)
                    .then(function (json) {
                        var selectedValue = isEdit ? currentOption(activePicker).value : (json.status ? json.status.value : null);
                        refreshPicker(activePicker, json.statuses || [], selectedValue);
                        closeModal();
                    })
                    .catch(function (exception) {
                        setError(exception.message);
                    })
                    .finally(function () {
                        saveButton.disabled = false;
                        saveButton.textContent = isEdit ? 'Update Status' : 'Save Status';
                    });
            }

            function deleteStatus(picker) {
                var option = currentOption(picker);
                var statusId = option ? option.dataset.id : null;

                if (!statusId) {
                    openModal(picker, 'create');
                    setError('Please save this status before deleting it.');
                    return;
                }

                var runDelete = function () {
                    request(picker.dataset.deleteUrl.replace('__ID__', statusId), 'DELETE', null, picker.dataset.token)
                        .then(function (json) {
                            refreshPicker(picker, json.statuses || [], (json.statuses && json.statuses[0]) ? json.statuses[0].value : null);
                        })
                        .catch(function (exception) {
                            alert(exception.message);
                        });
                };

                if (window.emitter) {
                    window.emitter.emit('open-confirm-modal', {
                        title: 'Delete status',
                        message: 'Are you sure you want to delete this status?',
                        options: {
                            btnDisagree: 'Cancel',
                            btnAgree: 'Delete',
                        },
                        agree: runDelete,
                    });

                    return;
                }

                if (confirm('Delete this status?')) {
                    runDelete();
                }
            }

            document.addEventListener('click', function (event) {
                var add = event.target.closest('.document-status-picker-add');

                if (add) {
                    var picker = event.target.closest('.document-status-picker');

                    if (!picker) {
                        return;
                    }

                    window.openDocumentStatusCreate(add);
                }
            });

            saveButton.addEventListener('click', saveStatus);
            document.getElementById('document-status-modal-close').addEventListener('click', closeModal);
            document.getElementById('document-status-modal-cancel').addEventListener('click', closeModal);
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeModal();
                }
            });
            input.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    saveStatus();
                }
            });
        })();
    </script>
@endonce
@endif
