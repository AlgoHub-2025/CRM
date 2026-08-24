<div>
    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 border border-gray-100">
            <h2 class="text-2xl font-semibold text-[#2B333E] mb-6">Invoice Builder</h2>
            
            <div class="space-y-6">
                <!-- Header Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-[#2D3748]">Invoice Date</label>
                        <input type="date" wire:model="issueDate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#2376D6] focus:border-[#2376D6] sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#2D3748]">Due Date</label>
                        <input type="date" wire:model="dueDate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#2376D6] focus:border-[#2376D6] sm:text-sm">
                    </div>
                </div>

                <!-- Sortable Items -->
                <div>
                    <h3 class="text-lg font-medium text-[#2B333E] mb-4">Line Items</h3>
                    <ul x-data="{
                            initSortable() {
                                if (typeof Sortable !== 'undefined') {
                                    Sortable.create($el, {
                                        handle: '.drag-handle',
                                        animation: 150,
                                        onEnd: (evt) => {
                                            let order = Array.from($el.children).map(child => child.dataset.id);
                                            @this.updateOrder(order);
                                        }
                                    });
                                }
                            }
                        }" x-init="initSortable" class="space-y-3">
                        
                        @foreach($items as $index => $item)
                            <li data-id="{{ $index }}" class="flex items-center space-x-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <button type="button" class="drag-handle cursor-move text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                                </button>
                                
                                <div class="flex-1 grid grid-cols-12 gap-4">
                                    <div class="col-span-12 sm:col-span-5">
                                        <input type="text" wire:model="items.{{ $index }}.description" placeholder="Description" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#2376D6] focus:border-[#2376D6] sm:text-sm">
                                    </div>
                                    <div class="col-span-12 sm:col-span-2">
                                        <input type="number" wire:model="items.{{ $index }}.quantity" placeholder="Qty" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#2376D6] focus:border-[#2376D6] sm:text-sm">
                                    </div>
                                    <div class="col-span-12 sm:col-span-3">
                                        <input type="number" step="0.01" wire:model="items.{{ $index }}.unit_price" placeholder="Price" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#2376D6] focus:border-[#2376D6] sm:text-sm">
                                    </div>
                                    <div class="col-span-12 sm:col-span-2 flex items-center justify-end">
                                        <button type="button" wire:click="removeItem({{ $index }})" class="text-red-500 hover:text-red-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4">
                        <button type="button" wire:click="addItem" class="text-sm font-medium text-[#2376D6] hover:text-[#1d5ba5] flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Item
                        </button>
                    </div>
                </div>

                <!-- Tax, Discount, Totals -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="flex justify-end">
                        <div class="w-full sm:w-1/3 space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-[#2D3748]">Tax (%)</span>
                                <input type="number" step="0.1" wire:model="taxPercent" class="w-24 border-gray-300 rounded-md shadow-sm focus:ring-[#2376D6] focus:border-[#2376D6] sm:text-sm">
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-[#2D3748]">Discount (%)</span>
                                <input type="number" step="0.1" wire:model="discountPercent" class="w-24 border-gray-300 rounded-md shadow-sm focus:ring-[#2376D6] focus:border-[#2376D6] sm:text-sm">
                            </div>
                            
                            <div x-data="{
                                items: @entangle('items'),
                                taxPercent: @entangle('taxPercent'),
                                discountPercent: @entangle('discountPercent'),
                                get subtotal() {
                                    return (this.items || []).reduce((sum, item) => sum + (parseFloat(item.quantity || 0) * parseFloat(item.unit_price || 0)), 0);
                                },
                                get tax() {
                                    return this.subtotal * (parseFloat(this.taxPercent || 0) / 100);
                                },
                                get discount() {
                                    return this.subtotal * (parseFloat(this.discountPercent || 0) / 100);
                                },
                                get total() {
                                    return this.subtotal + this.tax - this.discount;
                                }
                            }" class="space-y-2 mt-4 pt-4 border-t border-gray-200">
                                <div class="flex justify-between text-sm text-[#2D3748]">
                                    <span>Subtotal:</span>
                                    <span x-text="'$' + subtotal.toFixed(2)">$0.00</span>
                                </div>
                                <div class="flex justify-between text-sm text-[#2D3748]">
                                    <span>Tax:</span>
                                    <span x-text="'$' + tax.toFixed(2)">$0.00</span>
                                </div>
                                <div class="flex justify-between text-sm text-[#2D3748]">
                                    <span>Discount:</span>
                                    <span x-text="'-$' + discount.toFixed(2)">$0.00</span>
                                </div>
                                <div class="flex justify-between text-lg font-bold text-[#2B333E]">
                                    <span>Total:</span>
                                    <span x-text="'$' + total.toFixed(2)">$0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="flex justify-end pt-6">
                    <button type="button" wire:click="save" class="inline-flex items-center px-4 py-2 bg-[#2376D6] border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-[#1d5ba5] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2376D6]">
                        Save Invoice
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
