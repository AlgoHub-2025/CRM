<div>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <form wire:submit.prevent="save">
        <div class="space-y-12">
            <!-- Header section -->
            <div class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3">
                <div>
                    <h2 class="text-base font-semibold leading-7 text-brand-charcoal">Proposal Details</h2>
                    <p class="mt-1 text-sm leading-6 text-gray-600">Basic information about the proposal, including the client and validity period.</p>
                </div>

                <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                    <div class="sm:col-span-4">
                        <label for="title" class="block text-sm font-medium leading-6 text-brand-charcoal">Title</label>
                        <div class="mt-2">
                            <input type="text" wire:model="title" id="title" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand-blue sm:text-sm sm:leading-6">
                        </div>
                        @error('title') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-3">
                        <label for="client_id" class="block text-sm font-medium leading-6 text-brand-charcoal">Client</label>
                        <div class="mt-2">
                            <select wire:model="client_id" id="client_id" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-brand-blue sm:max-w-xs sm:text-sm sm:leading-6">
                                <option value="">Select a Client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('client_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-3">
                        <label for="opportunity_id" class="block text-sm font-medium leading-6 text-brand-charcoal">Opportunity</label>
                        <div class="mt-2">
                            <select wire:model="opportunity_id" id="opportunity_id" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-brand-blue sm:max-w-xs sm:text-sm sm:leading-6">
                                <option value="">Select an Opportunity</option>
                                @foreach($opportunities as $opp)
                                    <option value="{{ $opp->id }}">{{ $opp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('opportunity_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-3">
                        <label for="valid_until" class="block text-sm font-medium leading-6 text-brand-charcoal">Valid Until</label>
                        <div class="mt-2">
                            <input type="date" wire:model="valid_until" id="valid_until" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-brand-blue sm:max-w-xs sm:text-sm sm:leading-6">
                        </div>
                        @error('valid_until') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-6">
                        <label for="payment_terms" class="block text-sm font-medium leading-6 text-brand-charcoal">Payment Terms</label>
                        <div class="mt-2">
                            <textarea wire:model="payment_terms" id="payment_terms" rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand-blue sm:text-sm sm:leading-6"></textarea>
                        </div>
                        @error('payment_terms') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Line Items section -->
            <div class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3">
                <div>
                    <h2 class="text-base font-semibold leading-7 text-brand-charcoal">Line Items</h2>
                    <p class="mt-1 text-sm leading-6 text-gray-600">Add the products or services included in this proposal.</p>
                </div>

                <div class="grid max-w-3xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                    <div class="sm:col-span-6 bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                        <div class="px-4 py-6 sm:p-8" x-data="sortableBuilder()" x-init="initSortable()">
                            <div class="space-y-4 sortable-list" wire:ignore.self>
                                @foreach($items as $index => $item)
                                    <div class="flex items-start gap-4 sortable-item bg-white" data-index="{{ $index }}" wire:key="item-{{ $index }}">
                                        <div class="pt-2 cursor-move text-gray-400 hover:text-gray-500">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                                            </svg>
                                        </div>
                                        <div class="flex-grow">
                                            <label class="sr-only">Description</label>
                                            <input type="text" wire:model="items.{{ $index }}.description" placeholder="Description" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand-blue sm:text-sm sm:leading-6">
                                            @error('items.'.$index.'.description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="w-24">
                                            <label class="sr-only">Quantity</label>
                                            <input type="number" step="0.01" wire:model="items.{{ $index }}.quantity" placeholder="Qty" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand-blue sm:text-sm sm:leading-6">
                                            @error('items.'.$index.'.quantity') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="w-32">
                                            <label class="sr-only">Unit Price</label>
                                            <div class="relative rounded-md shadow-sm">
                                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="text-gray-500 sm:text-sm">$</span>
                                                </div>
                                                <input type="number" step="0.01" wire:model="items.{{ $index }}.unit_price" placeholder="Price" class="block w-full rounded-md border-0 py-1.5 pl-7 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand-blue sm:text-sm sm:leading-6">
                                            </div>
                                            @error('items.'.$index.'.unit_price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="w-24 text-right pt-2 font-medium text-brand-charcoal">
                                            ${{ number_format((float)($item['quantity'] ?? 0) * (float)($item['unit_price'] ?? 0), 2) }}
                                        </div>
                                        <div class="pt-1">
                                            <button type="button" wire:click="removeItem({{ $index }})" class="text-gray-400 hover:text-red-500">
                                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-4">
                                <button type="button" wire:click="addItem" class="inline-flex items-center text-sm font-medium text-brand-blue hover:text-brand-blue">
                                    <svg class="mr-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                                    </svg>
                                    Add Item
                                </button>
                            </div>

                            <div class="mt-8 border-t border-gray-200 pt-8">
                                <dl class="space-y-3 text-sm text-gray-600">
                                    @php
                                        $subtotal = collect($items)->sum(function($item) {
                                            return (float)($item['quantity'] ?? 0) * (float)($item['unit_price'] ?? 0);
                                        });
                                        $taxAmount = ($subtotal - (float)$discount) * ((float)$tax_rate / 100);
                                        $grandTotal = $subtotal - (float)$discount + $taxAmount;
                                    @endphp
                                    <div class="flex justify-between">
                                        <dt>Subtotal</dt>
                                        <dd class="text-gray-900">${{ number_format($subtotal, 2) }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <dt class="flex items-center gap-2">
                                            Discount
                                            <div class="relative rounded-md shadow-sm w-24">
                                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2">
                                                    <span class="text-gray-500 sm:text-xs">$</span>
                                                </div>
                                                <input type="number" step="0.01" wire:model.live="discount" class="block w-full rounded-md border-0 py-1 pl-6 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand-blue sm:text-xs sm:leading-6">
                                            </div>
                                        </dt>
                                        <dd class="text-gray-900">-${{ number_format((float)$discount, 2) }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <dt class="flex items-center gap-2">
                                            Tax Rate
                                            <div class="relative rounded-md shadow-sm w-24">
                                                <input type="number" step="0.01" wire:model.live="tax_rate" class="block w-full rounded-md border-0 py-1 pr-6 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand-blue sm:text-xs sm:leading-6">
                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                                                    <span class="text-gray-500 sm:text-xs">%</span>
                                                </div>
                                            </div>
                                        </dt>
                                        <dd class="text-gray-900">${{ number_format($taxAmount, 2) }}</dd>
                                    </div>
                                    <div class="flex justify-between border-t border-gray-200 pt-3 text-base font-medium text-brand-charcoal">
                                        <dt>Grand Total</dt>
                                        <dd>${{ number_format($grandTotal, 2) }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-x-6">
            <a href="{{ route('proposals.index') }}" class="text-sm font-semibold leading-6 text-gray-900 hover:text-gray-700">Cancel</a>
            <button type="submit" class="rounded-md bg-brand-blue px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-blue focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-blue">Save Proposal</button>
        </div>
    </form>

    <script>
        function sortableBuilder() {
            return {
                initSortable() {
                    const list = document.querySelector('.sortable-list');
                    if (list) {
                        new Sortable(list, {
                            animation: 150,
                            handle: '.cursor-move',
                            ghostClass: 'bg-gray-50',
                            onEnd: (evt) => {
                                const orderedIndexes = Array.from(list.querySelectorAll('.sortable-item'))
                                    .map(el => el.getAttribute('data-index'));
                                
                                @this.updateOrder(orderedIndexes);
                            }
                        });
                    }
                }
            }
        }
    </script>
</div>

