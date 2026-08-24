<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Top Actions (Hidden on Print) -->
        <div class="mb-6 flex justify-between items-center print:hidden">
            <div>
                <a href="{{ route('contracts.index') }}" class="text-brand-blue hover:text-brand-blue flex items-center text-sm font-medium">
                    <svg class="mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Contracts
                </a>
            </div>
            <div class="flex space-x-3">
                <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-blue">
                    Edit Contract
                </button>
                <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-brand-blue hover:bg-brand-blue focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-blue">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Print to PDF
                </button>
                @if($contract->projects->isEmpty())
                <form method="POST" action="{{ route('contracts.activate', $contract) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Activate &amp; Create Project
                    </button>
                </form>
                @else
                <a href="{{ route('projects.show', $contract->projects->first()) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-500 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    View Project
                </a>
                @endif
            </div>
        </div>

        <!-- Document Style Layout -->
        <div class="bg-white shadow-xl rounded-lg border border-gray-200 overflow-hidden print:shadow-none print:border-none print:m-0 print:p-0">
            <div class="p-8 sm:p-12 md:p-16">
                <!-- Header -->
                <div class="flex justify-between items-start border-b border-gray-200 pb-8 mb-8">
                    <div>
                        <div class="flex items-center">
                            <!-- AlgoHub Logo Placeholder -->
                            <div class="w-10 h-10 bg-brand-blue rounded-md flex items-center justify-center text-white font-bold text-xl mr-3">
                                A
                            </div>
                            <h1 class="text-2xl font-bold text-brand-charcoal tracking-tight">AlgoHub CRM</h1>
                        </div>
                        <div class="mt-4 text-sm text-gray-500">
                            <p>123 Innovation Drive</p>
                            <p>Tech District, TD 90210</p>
                            <p>contact@algohub.example.com</p>
                            <p>+1 (555) 123-4567</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <h2 class="text-3xl font-light text-gray-900 uppercase tracking-widest">Contract</h2>
                        <div class="mt-4">
                            <p class="text-sm font-medium text-gray-500">Contract Number</p>
                            <p class="text-lg font-semibold text-brand-charcoal">{{ $contract->contract_number ?? 'CTR-000000' }}</p>
                        </div>
                        <div class="mt-2">
                            <p class="text-sm font-medium text-gray-500">Date Issued</p>
                            <p class="text-md text-gray-900">{{ now()->format('F d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Parties Info -->
                <div class="grid grid-cols-2 gap-12 mb-10">
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Prepared For</h3>
                        <p class="text-lg font-bold text-brand-charcoal">{{ $contract->client->name ?? 'Client Name' }}</p>
                        <p class="text-md text-gray-700 mb-1">{{ $contract->client->company->name ?? 'Company Name' }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $contract->client->email ?? 'client@example.com' }}<br>
                            {{ $contract->client->phone ?? '+1 (555) 987-6543' }}
                        </p>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Contract Details</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Start Date</p>
                                <p class="text-sm font-medium text-gray-900">{{ $contract->start_date ? (is_string($contract->start_date) ? \Carbon\Carbon::parse($contract->start_date)->format('F d, Y') : $contract->start_date->format('F d, Y')) : 'TBD' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">End Date</p>
                                <p class="text-sm font-medium text-gray-900">{{ $contract->end_date ? (is_string($contract->end_date) ? \Carbon\Carbon::parse($contract->end_date)->format('F d, Y') : $contract->end_date->format('F d, Y')) : 'TBD' }}</p>
                            </div>
                            <div class="col-span-2 mt-2 p-3 bg-gray-50 rounded border border-gray-100">
                                <p class="text-sm text-gray-500">Total Contract Value</p>
                                <p class="text-xl font-bold text-brand-blue">${{ number_format($contract->value ?? 0, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Scope of Work -->
                <div class="mb-12">
                    <h3 class="text-lg font-semibold text-brand-charcoal border-b border-gray-200 pb-2 mb-4">Scope of Work</h3>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        @if($contract->scope)
                            {!! nl2br(e($contract->scope)) !!}
                        @else
                            <p class="italic text-gray-500">No scope of work defined for this contract.</p>
                        @endif
                    </div>
                </div>

                <!-- Terms & Conditions Snippet -->
                <div class="mb-16">
                    <h3 class="text-lg font-semibold text-brand-charcoal border-b border-gray-200 pb-2 mb-4">Terms and Conditions</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        This contract is subject to the general terms and conditions agreed upon between AlgoHub CRM and the Client. 
                        By signing below, both parties agree to the scope, timeline, and value defined in this document. 
                        Any changes to this scope of work will require a formal change request and may incur additional costs.
                    </p>
                </div>

                <!-- Signatures -->
                <div class="grid grid-cols-2 gap-16 mt-8 pt-8">
                    <div>
                        <div class="border-b-2 border-gray-300 h-16 relative">
                            <!-- Signature image could go here -->
                        </div>
                        <div class="pt-2">
                            <p class="font-bold text-brand-charcoal">AlgoHub Representative</p>
                            <p class="text-sm text-gray-500">Date: _______________</p>
                        </div>
                    </div>
                    <div>
                        <div class="border-b-2 border-gray-300 h-16 relative"></div>
                        <div class="pt-2">
                            <p class="font-bold text-brand-charcoal">{{ $contract->client->name ?? 'Client Representative' }}</p>
                            <p class="text-sm text-gray-500">Date: _______________</p>
                        </div>
                    </div>
                </div>

            </div>
            
            <!-- Footer -->
            <div class="bg-gray-50 p-6 border-t border-gray-200 text-center text-xs text-gray-500 print:hidden">
                Internal Document Reference: {{ $contract->id ?? 'NEW' }}-{{ now()->timestamp }}
            </div>
        </div>
    </div>
    
    @push('styles')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .max-w-5xl, .max-w-5xl * {
                visibility: visible;
            }
            .max-w-5xl {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0 !important;
                padding: 0 !important;
            }
            .print\:hidden {
                display: none !important;
            }
            .shadow-xl {
                box-shadow: none !important;
            }
        }
    </style>
    @endpush
</x-app-layout>

