<div
    id="product-quick-unit-modal"
    class="product-quick-unit-modal hidden"
    role="dialog"
    aria-modal="true"
    aria-labelledby="product-quick-unit-title"
>
    <div class="product-quick-unit-modal__panel">
        <div class="product-quick-unit-modal__header">
            <h2 id="product-quick-unit-title">Add Unit</h2>

            <button type="button" data-close-quick-unit class="product-quick-unit-modal__close" aria-label="Close">&times;</button>
        </div>

        <div class="product-quick-unit-modal__body">
            <div class="product-quick-unit-modal__field">
                <label for="product_quick_unit_name">Unit Name *</label>
                <input id="product_quick_unit_name" type="text" maxlength="100" placeholder="e.g. YARD">
                <p data-quick-unit-name-error class="product-quick-unit-modal__error hidden"></p>
            </div>

            <div class="product-quick-unit-modal__field">
                <label for="product_quick_unit_conversion">Conversion To Meter</label>
                <input id="product_quick_unit_conversion" type="number" min="0" step="0.000001" placeholder="e.g. 0.9144">
                <p class="product-quick-unit-modal__hint">Required only when this unit must convert to or from another length unit.</p>
                <p data-quick-unit-conversion-error class="product-quick-unit-modal__error hidden"></p>
            </div>
        </div>

        <div class="product-quick-unit-modal__footer">
            <button type="button" data-close-quick-unit class="secondary-button">Cancel</button>
            <button type="button" id="product_quick_unit_save" class="primary-button inline-flex min-w-[112px] items-center justify-center gap-2">
                <span class="icon-add text-base"></span>
                <span>Save Unit</span>
            </button>
        </div>
    </div>
</div>

@pushOnce('styles')
    <style>
        .product-quick-unit-modal {
            position: fixed;
            inset: 0;
            z-index: 100002;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(17, 24, 39, 0.55);
        }

        .product-quick-unit-modal.hidden {
            display: none !important;
        }

        .product-quick-unit-modal.flex {
            display: flex !important;
        }

        .product-quick-unit-modal__panel {
            width: min(640px, calc(100vw - 32px));
            max-height: calc(100vh - 48px);
            overflow: hidden;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.24);
        }

        .product-quick-unit-modal__header {
            display: flex;
            min-height: 72px;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .product-quick-unit-modal__header h2 {
            margin: 0;
            color: #111827;
            font-size: 22px;
            font-weight: 600;
            line-height: 1.3;
        }

        .product-quick-unit-modal__close {
            width: 40px;
            height: 40px;
            flex: 0 0 40px;
            cursor: pointer;
            border: 0;
            border-radius: 6px;
            background: transparent;
            color: #4b5563;
            font-size: 24px;
        }

        .product-quick-unit-modal__close:hover {
            background: #f3f4f6;
            color: #111827;
        }

        .product-quick-unit-modal__body {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 24px;
            padding: 24px;
        }

        .product-quick-unit-modal__field {
            min-width: 0;
        }

        .product-quick-unit-modal__field label {
            display: block;
            margin-bottom: 8px;
            color: #1f2937;
            font-size: 14px;
            font-weight: 600;
        }

        .product-quick-unit-modal__field input {
            display: block;
            width: 100%;
            min-height: 46px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background: #ffffff;
            padding: 10px 12px;
            color: #1f2937;
            font-size: 14px;
            outline: none;
        }

        .product-quick-unit-modal__field input:focus {
            border-color: #9ca3af;
            box-shadow: 0 0 0 3px rgba(156, 163, 175, 0.18);
        }

        .product-quick-unit-modal__hint,
        .product-quick-unit-modal__error {
            margin: 7px 0 0;
            font-size: 12px;
            line-height: 1.45;
        }

        .product-quick-unit-modal__hint {
            color: #6b7280;
        }

        .product-quick-unit-modal__error {
            color: #dc2626;
        }

        .product-quick-unit-modal__footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            padding: 16px 24px;
            border-top: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .consumption-row {
            position: relative;
            z-index: 0;
        }

        .consumption-row.product-unit-picker-open {
            z-index: 60;
        }

        .product-unit-lookup {
            position: relative;
        }

        .product-unit-popup {
            right: 0 !important;
            left: auto !important;
            z-index: 70 !important;
            width: 220px;
            max-width: min(220px, calc(100vw - 32px));
        }

        .product-unit-options {
            overflow-x: hidden;
        }

        .dark .product-quick-unit-modal__panel,
        .dark .product-quick-unit-modal__field input {
            background: #111827;
            color: #e5e7eb;
        }

        .dark .product-quick-unit-modal__header,
        .dark .product-quick-unit-modal__footer {
            border-color: #374151;
        }

        .dark .product-quick-unit-modal__footer {
            background: #030712;
        }

        .dark .product-quick-unit-modal__header h2,
        .dark .product-quick-unit-modal__field label {
            color: #f9fafb;
        }

        .dark .product-quick-unit-modal__field input {
            border-color: #4b5563;
        }

        @media (max-width: 640px) {
            .product-quick-unit-modal {
                padding: 16px;
            }

            .product-quick-unit-modal__body {
                grid-template-columns: 1fr;
                gap: 18px;
                padding: 20px;
            }

            .product-quick-unit-modal__header,
            .product-quick-unit-modal__footer {
                padding-right: 20px;
                padding-left: 20px;
            }
        }
    </style>
@endPushOnce

@pushOnce('scripts')
<script type="module">
    (function () {
        window.productUnitOptions = @json($unitOptions);

        function escapeHtml(value) {
            return String(value == null ? '' : value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        window.productUnitOptionsMarkup = function (selected) {
            var selectedName = String(selected || '').trim().toUpperCase();
            var html = '<option value="">Select Unit</option>';

            (window.productUnitOptions || []).forEach(function (unit) {
                var name = String(unit.name || '').trim();
                var isSelected = name.toUpperCase() === selectedName ? ' selected' : '';
                html += '<option value="' + escapeHtml(name) + '"' + isSelected + '>' + escapeHtml(name) + '</option>';
            });

            return html;
        };

        window.productUnitPickerMarkup = function (selected, index) {
            var value = String(selected || '').trim();

            return ''
                + '<div class="product-unit-lookup relative">'
                + '  <input type="hidden" name="consumptions[' + index + '][unit]" class="consumption-unit-input" value="' + escapeHtml(value) + '">'
                + '  <button type="button" class="product-unit-toggle flex min-h-[40px] w-full items-center justify-between gap-2 rounded border border-gray-200 bg-white px-3 py-2 text-left text-sm hover:border-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" aria-expanded="false">'
                + '    <span class="product-unit-selected truncate">' + escapeHtml(value || 'Select Unit') + '</span>'
                + '    <span class="icon-down-arrow shrink-0 text-base text-gray-500"></span>'
                + '  </button>'
                + '  <div class="product-unit-popup absolute left-0 right-0 top-full z-30 mt-1 hidden overflow-hidden rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900">'
                + '    <div class="border-b border-gray-200 p-2 dark:border-gray-700">'
                + '      <div class="relative">'
                + '        <span class="icon-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-lg text-gray-400"></span>'
                + '        <input type="search" class="product-unit-search min-h-[38px] w-full rounded border border-gray-200 py-2 pl-10 pr-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200" placeholder="Search unit...">'
                + '      </div>'
                + '    </div>'
                + '    <div class="product-unit-options max-h-44 overflow-y-auto p-1" style="scrollbar-width: thin;"></div>'
                + '  </div>'
                + '</div>';
        };

        window.setProductUnitPickerValue = function (row, value) {
            if (!row) return;

            var input = row.querySelector('.consumption-unit-input');
            var label = row.querySelector('.product-unit-selected');
            var normalized = String(value || '').trim();

            if (input) input.value = normalized;
            if (label) label.textContent = normalized || 'Select Unit';
        };

        window.bindProductUnitPicker = function (row) {
            var lookup = row ? row.querySelector('.product-unit-lookup') : null;
            if (!lookup || lookup.dataset.bound === '1') return;

            lookup.dataset.bound = '1';
            var toggle = lookup.querySelector('.product-unit-toggle');
            var popup = lookup.querySelector('.product-unit-popup');
            var search = lookup.querySelector('.product-unit-search');
            var options = lookup.querySelector('.product-unit-options');

            function setOpen(open) {
                popup.classList.toggle('hidden', !open);
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                row.classList.toggle('product-unit-picker-open', open);
            }

            function render(term) {
                var query = String(term || '').trim().toLowerCase();
                var filtered = (window.productUnitOptions || []).filter(function (unit) {
                    return !query || String(unit.name || '').toLowerCase().includes(query);
                });

                options.innerHTML = '';

                if (!filtered.length) {
                    options.innerHTML = '<p class="px-3 py-4 text-center text-sm text-gray-500">No units found.</p>';
                    return;
                }

                filtered.forEach(function (unit) {
                    var name = String(unit.name || '').trim();
                    options.innerHTML += '<button type="button" class="product-unit-option flex w-full items-center rounded px-3 py-2 text-left text-sm hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800" data-unit="' + escapeHtml(name) + '">' + escapeHtml(name) + '</button>';
                });
            }

            toggle.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();
                var willOpen = popup.classList.contains('hidden');
                setOpen(willOpen);

                if (willOpen) {
                    search.value = '';
                    render('');
                    setTimeout(function () { search.focus(); }, 0);
                }
            });

            search.addEventListener('input', function () { render(search.value); });
            search.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') setOpen(false);
            });

            options.addEventListener('click', function (event) {
                var option = event.target.closest('.product-unit-option');
                if (!option) return;

                window.setProductUnitPickerValue(row, option.getAttribute('data-unit'));
                setOpen(false);
            });

            document.addEventListener('click', function (event) {
                if (!lookup.contains(event.target)) setOpen(false);
            });

            window.addEventListener('product-unit-created', function () {
                if (document.body.contains(row)) render(search.value);
            });

            render('');
            window.setProductUnitPickerValue(row, row.querySelector('.consumption-unit-input')?.value || '');
        };

        var modal = document.getElementById('product-quick-unit-modal');

        // Keep this imperative modal outside Vue's mount root so Vue cannot replace
        // the node after these listeners and references have been initialized.
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }

        var nameInput = document.getElementById('product_quick_unit_name');
        var conversionInput = document.getElementById('product_quick_unit_conversion');
        var saveButton = document.getElementById('product_quick_unit_save');
        var nameError = modal.querySelector('[data-quick-unit-name-error]');
        var conversionError = modal.querySelector('[data-quick-unit-conversion-error]');

        function setError(element, message) {
            element.textContent = message || '';
            element.classList.toggle('hidden', !message);
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function openModal() {
            nameInput.value = '';
            conversionInput.value = '';
            setError(nameError, '');
            setError(conversionError, '');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(function () { nameInput.focus(); }, 0);
        }

        window.openProductQuickUnitModal = function (event) {
            event?.preventDefault();
            openModal();
        };

        document.querySelectorAll('[data-quick-add-unit]').forEach(function (button) {
            button.addEventListener('click', window.openProductQuickUnitModal);
        });

        modal.querySelectorAll('[data-close-quick-unit]').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                closeModal();
            });
        });

        modal.addEventListener('click', function (event) {
            if (event.target === modal) closeModal();
        });

        document.addEventListener('click', function (event) {
            if (event.target.closest('[data-quick-add-unit]')) {
                event.preventDefault();
                openModal();
                return;
            }

            if (event.target.closest('[data-close-quick-unit]')) {
                event.preventDefault();
                closeModal();
            }
        }, true);

        function addUnitToSelectors(unit) {
            var name = String(unit.name || '').trim();

            if (!(window.productUnitOptions || []).some(function (option) { return String(option.name).toUpperCase() === name.toUpperCase(); })) {
                window.productUnitOptions.push({ name: name, meter_conversion: unit.meter_conversion });
                window.productUnitOptions.sort(function (a, b) { return String(a.name).localeCompare(String(b.name)); });
            }

            document.querySelectorAll('select.product-unit-select').forEach(function (select) {
                var current = select.value;
                select.innerHTML = window.productUnitOptionsMarkup(current);
            });

            window.dispatchEvent(new CustomEvent('product-unit-created', { detail: unit }));
        }

        saveButton.addEventListener('click', function () {
            var name = nameInput.value.trim();
            var conversion = conversionInput.value.trim();

            setError(nameError, name ? '' : 'The unit name is required.');
            setError(conversionError, '');

            if (!name) return;

            saveButton.disabled = true;
            saveButton.classList.add('cursor-not-allowed', 'opacity-70');
            saveButton.querySelector('span:last-child').textContent = 'Saving...';

            fetch("{{ route('admin.settings.units.store') }}", {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    name: name,
                    meter_conversion: conversion || null
                })
            })
                .then(async function (response) {
                    var body = await response.json();
                    if (!response.ok) throw body;
                    return body;
                })
                .then(function (body) {
                    addUnitToSelectors(body.data);
                    closeModal();
                })
                .catch(function (error) {
                    var errors = error.errors || {};
                    setError(nameError, errors.name ? errors.name[0] : (error.message || 'Unable to save the unit.'));
                    setError(conversionError, errors.meter_conversion ? errors.meter_conversion[0] : '');
                })
                .finally(function () {
                    saveButton.disabled = false;
                    saveButton.classList.remove('cursor-not-allowed', 'opacity-70');
                    saveButton.querySelector('span:last-child').textContent = 'Save Unit';
                });
        });
    })();
</script>
@endPushOnce
