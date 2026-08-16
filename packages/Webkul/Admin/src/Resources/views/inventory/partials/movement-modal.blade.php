@php
    $isEditingStock = isset($editingInventory) && $editingInventory;
    $isMaterialLocked = isset($lockedMaterial) && $lockedMaterial;
@endphp

<div id="inventory-movement-modal" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/50 p-4" role="dialog" aria-modal="true" aria-labelledby="inventory-movement-title">
    <div class="w-full max-w-2xl overflow-hidden rounded-lg bg-white shadow-xl dark:bg-gray-900">
        <form method="POST" action="{{ route('admin.inventory.movements.store') }}" data-loading-form>
            @csrf

            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                <h2 id="inventory-movement-title" class="text-lg font-semibold text-gray-900 dark:text-white">{{ $isEditingStock ? 'Edit Stock' : 'Record Inventory Movement' }}</h2>
                <button type="button" class="icon-cross-large rounded-md p-1.5 text-2xl text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800" aria-label="Close" onclick="window.closeInventoryMovementModal()"></button>
            </div>

            <div class="grid gap-x-5 gap-y-4 p-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-gray-200">Material *</label>
                    @if($isMaterialLocked)
                        <input type="hidden" name="material_reference_id" value="{{ $lockedMaterial->id }}">
                        <input type="text" value="{{ $lockedMaterial->name }} ({{ $lockedMaterial->unit }})" class="inventory-modal-control" disabled>
                    @else
                        <select name="material_reference_id" class="inventory-modal-control" required>
                            <option value="">Select material</option>
                            @foreach($materials as $materialOption)
                                <option value="{{ $materialOption->id }}" @selected(old('material_reference_id') == $materialOption->id)>{{ $materialOption->name }} ({{ $materialOption->unit }})</option>
                            @endforeach
                        </select>
                    @endif
                    @error('material_reference_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-gray-200">Movement *</label>
                    @if($isEditingStock)
                        <input type="hidden" name="type" id="inventory-movement-type" value="set_balance">
                        <input type="text" value="Update Quantity & Price" class="inventory-modal-control" disabled>
                    @else
                        <select name="type" id="inventory-movement-type" class="inventory-modal-control" required onchange="window.updateInventoryMovementFields()">
                            @foreach(\Webkul\PurchaseOrder\Services\MaterialInventoryService::MANUAL_TYPES as $value => $label)
                                <option value="{{ $value }}" @selected(old('type', 'stock_in') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    @endif
                    @error('type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label id="inventory-quantity-label" class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-gray-200">Quantity *</label>
                    <input type="number" name="quantity" value="{{ old('quantity', $isEditingStock ? $editingInventory->on_hand : '') }}" min="0" step="0.0001" class="inventory-modal-control" required>
                    @error('quantity')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div id="inventory-unit-cost-field">
                    <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-gray-200">Unit Cost *</label>
                    <input type="number" name="unit_cost" value="{{ old('unit_cost', $isEditingStock ? $editingInventory->average_unit_cost : '') }}" min="0" step="0.0001" class="inventory-modal-control">
                    @error('unit_cost')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-gray-200">Date *</label>
                    <input type="datetime-local" name="occurred_at" value="{{ old('occurred_at', now()->format('Y-m-d\TH:i')) }}" class="inventory-modal-control" required>
                    @error('occurred_at')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-gray-200">Notes</label>
                    <textarea name="notes" rows="3" class="inventory-modal-control inventory-modal-textarea">{{ old('notes') }}</textarea>
                    @error('notes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                @if($isMaterialLocked && ! $isEditingStock)
                    <input type="hidden" name="return_material_id" value="{{ $lockedMaterial->id }}">
                @endif
            </div>

            <div class="flex justify-end gap-2 border-t border-gray-200 bg-slate-50 px-5 py-4 dark:border-gray-800 dark:bg-gray-950">
                <button type="button" class="secondary-button" onclick="window.closeInventoryMovementModal()">Cancel</button>
                <button type="submit" class="primary-button inline-flex min-w-[150px] items-center justify-center gap-2" data-loading-submit data-loading-text="Saving...">
                    <span class="{{ $isEditingStock ? 'icon-edit' : 'icon-add' }}"></span>
                    {{ $isEditingStock ? 'Update Inventory' : 'Save Movement' }}
                </button>
            </div>
        </form>
    </div>
</div>

@pushOnce('styles')
    <style>
        .inventory-modal-control {
            box-sizing: border-box;
            display: block;
            width: 100%;
            min-height: 44px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background: #fff;
            padding: 9px 12px;
            color: #1f2937;
            font-size: 14px;
            line-height: 20px;
            outline: none;
        }

        .inventory-modal-control:focus {
            border-color: var(--brand-color, #be1e2d);
            box-shadow: 0 0 0 1px var(--brand-color, #be1e2d);
        }

        .inventory-modal-control:disabled {
            background: #f3f4f6;
            color: #4b5563;
        }

        .inventory-modal-textarea {
            min-height: 96px;
            resize: vertical;
        }

        .dark .inventory-modal-control {
            border-color: #374151;
            background: #030712;
            color: #fff;
        }

        .dark .inventory-modal-control:disabled {
            background: #1f2937;
            color: #d1d5db;
        }
    </style>
@endPushOnce

@pushOnce('scripts')
    <script>
        window.inventoryStockEditMode = @json((bool) $isEditingStock);

        window.openInventoryMovementModal = function () {
            const modal = document.getElementById('inventory-movement-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
            window.updateInventoryMovementFields();
        };

        window.closeInventoryMovementModal = function () {
            if (window.inventoryStockEditMode) {
                window.location.href = @json(route('admin.inventory.index'));
                return;
            }

            const modal = document.getElementById('inventory-movement-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        };

        window.updateInventoryMovementFields = function () {
            const type = document.getElementById('inventory-movement-type')?.value;
            const costField = document.getElementById('inventory-unit-cost-field');
            const costInput = costField?.querySelector('input');
            const quantityLabel = document.getElementById('inventory-quantity-label');

            const requiresCost = ['stock_in', 'set_balance'].includes(type);
            if (costField) costField.classList.toggle('hidden', ! requiresCost);
            if (costInput) costInput.required = requiresCost;
            if (quantityLabel) quantityLabel.textContent = type === 'set_balance' ? 'New Balance *' : 'Quantity *';
        };

        document.getElementById('inventory-movement-modal')?.addEventListener('click', function (event) {
            if (event.target === this) window.closeInventoryMovementModal();
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') window.closeInventoryMovementModal();
        });

        document.addEventListener('DOMContentLoaded', function () {
            window.updateInventoryMovementFields();
            @if($isEditingStock || $errors->hasAny(['material_reference_id', 'type', 'quantity', 'unit_cost', 'occurred_at', 'notes']))
                window.openInventoryMovementModal();
            @endif
        });
    </script>
@endPushOnce
