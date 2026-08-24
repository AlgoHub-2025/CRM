<div>
    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <!-- Invoice Header -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 border border-gray-100 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-[#2B333E]">Invoice #{{ $invoice->invoice_number }}</h1>
                <p class="text-sm text-[#2D3748] mt-1">Project: {{ $invoice->project->name ?? 'N/A' }} | Client: {{ $invoice->client->name ?? 'N/A' }}</p>
                <div class="mt-2 text-sm text-[#2D3748] flex space-x-4">
                    <span><strong>Issue Date:</strong> {{ $invoice->issue_date ? \Carbon\Carbon::parse($invoice->issue_date)->format('M d, Y') : 'N/A' }}</span>
                    <span><strong>Due Date:</strong> {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') : 'N/A' }}</span>
                </div>
            </div>
            <div class="mt-4 md:mt-0 flex flex-col items-end">
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                    @if($invoice->status === 'paid') bg-green-100 text-green-800
                    @elseif($invoice->status === 'sent') bg-blue-100 text-blue-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($invoice->status) }}
                </span>
                @if($invoice->status !== 'paid')
                    <button type="button" wire:click="$set('showPaymentModal', true)" class="mt-4 inline-flex items-center px-4 py-2 bg-[#2376D6] border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-[#1d5ba5] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2376D6]">
                        Record Payment
                    </button>
                @endif
            </div>
        </div>

        <!-- Invoice Items -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 mb-6">
            <div class="p-6 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-medium text-[#2B333E]">Line Items</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#2D3748] uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-[#2D3748] uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-[#2D3748] uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-[#2D3748] uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($invoice->items ?? [] as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#2B333E]">{{ $item->description ?? ($item['description'] ?? '') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#2D3748] text-right">{{ $item->quantity ?? ($item['quantity'] ?? 0) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#2D3748] text-right">${{ number_format($item->unit_price ?? ($item['unit_price'] ?? 0), 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#2B333E] text-right">${{ number_format(($item->quantity ?? ($item['quantity'] ?? 0)) * ($item->unit_price ?? ($item['unit_price'] ?? 0)), 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No items found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Summary -->
            <div class="p-6 bg-gray-50 border-t border-gray-200 flex justify-end">
                <div class="w-full sm:w-1/3 space-y-3">
                    <div class="flex justify-between text-sm text-[#2D3748]">
                        <span>Subtotal:</span>
                        <span>${{ number_format($invoice->subtotal ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-[#2D3748]">
                        <span>Tax ({{ $invoice->tax_percent ?? 0 }}%):</span>
                        <span>${{ number_format($invoice->tax_amount ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-[#2D3748]">
                        <span>Discount ({{ $invoice->discount_percent ?? 0 }}%):</span>
                        <span>-${{ number_format($invoice->discount_amount ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-[#2B333E] pt-2 border-t border-gray-200">
                        <span>Total:</span>
                        <span>${{ number_format($invoice->total ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-green-600 font-medium pt-2">
                        <span>Paid Amount:</span>
                        <span>${{ number_format($invoice->paid_amount ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-red-600 border-t border-gray-200 pt-2">
                        <span>Balance Due:</span>
                        <span>${{ number_format(($invoice->total ?? 0) - ($invoice->paid_amount ?? 0), 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
            <div class="p-6 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h2 class="text-lg font-medium text-[#2B333E]">Payment History</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#2D3748] uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#2D3748] uppercase tracking-wider">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#2D3748] uppercase tracking-wider">Reference</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-[#2D3748] uppercase tracking-wider">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($invoice->payments ?? [] as $payment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#2B333E]">{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') : 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#2D3748]">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#2D3748]">{{ $payment->reference ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#2B333E] text-right">${{ number_format($payment->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No payments recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment Modal -->
        <div x-data="{ showModal: @entangle('showPaymentModal') }" x-show="showModal" class="fixed z-10 inset-0 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showModal" class="fixed inset-0 transition-opacity" aria-hidden="true" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showModal" @click.away="showModal = false" class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-[#2B333E]">Record Payment</h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-[#2D3748]">Amount</label>
                                <input type="number" step="0.01" wire:model="paymentAmount" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#2376D6] focus:border-[#2376D6] sm:text-sm">
                                @error('paymentAmount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[#2D3748]">Payment Method</label>
                                <select wire:model="paymentMethod" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#2376D6] focus:border-[#2376D6] sm:text-sm">
                                    <option value="">Select Method...</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cash">Cash</option>
                                    <option value="check">Check</option>
                                </select>
                                @error('paymentMethod') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[#2D3748]">Reference (Optional)</label>
                                <input type="text" wire:model="paymentReference" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#2376D6] focus:border-[#2376D6] sm:text-sm">
                                @error('paymentReference') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">
                        <button type="button" wire:click="recordPayment" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#2376D6] text-base font-medium text-white hover:bg-[#1d5ba5] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2376D6] sm:ml-3 sm:w-auto sm:text-sm">
                            Submit
                        </button>
                        <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2376D6] sm:mt-0 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
