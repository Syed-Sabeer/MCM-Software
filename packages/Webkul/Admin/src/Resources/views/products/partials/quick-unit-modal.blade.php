<div
    id="product-quick-unit-modal"
    class="fixed inset-0 z-[100002] hidden items-center justify-center bg-black/50 p-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="product-quick-unit-title"
>
    <div class="w-full max-w-lg overflow-hidden rounded-lg bg-white shadow-xl dark:bg-gray-900">
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-800">
            <h2 id="product-quick-unit-title" class="text-xl font-semibold text-gray-900 dark:text-white">Add Unit</h2>

            <button type="button" data-close-quick-unit class="icon-cross cursor-pointer text-2xl text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white" aria-label="Close"></button>
        </div>

        <div class="space-y-5 px-6 py-5">
            <div>
                <label for="product_quick_unit_name" class="mb-2 block text-sm font-medium text-gray-800 dark:text-gray-200">Unit Name *</label>
                <input id="product_quick_unit_name" type="text" maxlength="100" class="w-full rounded border border-gray-300 px-3 py-2.5 text-sm uppercase dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200" placeholder="e.g. YARD">
                <p data-quick-unit-name-error class="mt-1 hidden text-xs text-red-600"></p>
            </div>

            <div>
                <label for="product_quick_unit_conversion" class="mb-2 block text-sm font-medium text-gray-800 dark:text-gray-200">Conversion To Meter</label>
                <input id="product_quick_unit_conversion" type="number" min="0" step="0.000001" class="w-full rounded border border-gray-300 px-3 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200" placeholder="e.g. 0.9144">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Required only when this unit must convert to or from another length unit.</p>
                <p data-quick-unit-conversion-error class="mt-1 hidden text-xs text-red-600"></p>
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-800 dark:bg-gray-950">
            <button type="button" data-close-quick-unit class="secondary-button">Cancel</button>
            <button type="button" id="product_quick_unit_save" class="primary-button inline-flex min-w-[112px] items-center justify-center gap-2">
                <span class="icon-add text-base"></span>
                <span>Save Unit</span>
            </button>
        </div>
    </div>
</div>

<script>
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

        var modal = document.getElementById('product-quick-unit-modal');
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

        document.addEventListener('click', function (event) {
            if (event.target.closest('[data-quick-add-unit]')) {
                event.preventDefault();
                openModal();
                return;
            }

            if (event.target.closest('[data-close-quick-unit]') || event.target === modal) {
                event.preventDefault();
                closeModal();
            }
        });

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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
