<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Settlement Accounts</h1>
                <p class="text-sm text-gray-600">Manage, edit, suspend, and delete merchant settlement accounts.</p>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="relative w-full md:w-96">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by name, email, account number, or bank..." class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-primary focus:border-primary">
                <i class="fa fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto">
                <label class="text-xs text-gray-500 uppercase font-bold tracking-wider">Status:</label>
                <select wire:model.live="status_filter" class="rounded-lg border border-gray-300 py-2 px-3 text-sm focus:ring-primary focus:border-primary">
                    <option value="all">All Accounts</option>
                    <option value="active">Active Only</option>
                    <option value="suspended">Suspended Only</option>
                    <option value="verified">Verified Only</option>
                    <option value="unverified">Unverified Only</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-500 uppercase tracking-wider">
                            <th class="py-3.5 px-4">Merchant</th>
                            <th class="py-3.5 px-4">Bank Name</th>
                            <th class="py-3.5 px-4">Account Number</th>
                            <th class="py-3.5 px-4">Account Name</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm">
                        @forelse($accounts as $kyb)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-4">
                                    <div class="font-bold text-gray-900">
                                        {{ $kyb->user?->name ?: ($kyb->surname . ' ' . $kyb->first_name) }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $kyb->user?->email ?: $kyb->email }}</div>
                                    @if($kyb->business_name)
                                        <span class="inline-block mt-1 text-[11px] bg-indigo-50 text-indigo-700 font-semibold px-2 py-0.5 rounded">
                                            {{ $kyb->business_name }}
                                        </span>
                                    @endif
                                </td>

                                <td class="py-4 px-4 font-semibold text-gray-800">
                                    {{ $kyb->settlement_bank_name ?: 'N/A' }}
                                    @if($kyb->settlement_bank_id)
                                        <span class="text-xs text-gray-400 block font-normal">Code: {{ $kyb->settlement_bank_id }}</span>
                                    @endif
                                </td>

                                <td class="py-4 px-4 font-mono font-bold text-primary">
                                    {{ $kyb->settlement_account_number ?: 'N/A' }}
                                </td>

                                <td class="py-4 px-4 text-gray-700">
                                    {{ $kyb->settlement_account_name ?: 'N/A' }}
                                </td>

                                <td class="py-4 px-4 space-y-1">
                                    @if($kyb->is_settlement_suspended)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                            <i class="fa fa-ban mr-1 text-red-600"></i> Suspended
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                            <i class="fa fa-check-circle mr-1 text-green-600"></i> Active
                                        </span>
                                    @endif

                                    @if($kyb->settlement_verified_at)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 block">
                                            Verified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-700 block">
                                            Unverified
                                        </span>
                                    @endif
                                </td>

                                <td class="py-4 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Edit Button -->
                                        <button wire:click="openEditModal({{ $kyb->id }})" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors" title="Edit Settlement Details">
                                            <i class="fa fa-edit"></i>
                                        </button>

                                        <!-- Suspend / Unsuspend Toggle -->
                                        @if($kyb->is_settlement_suspended)
                                            <button wire:click="toggleSuspend({{ $kyb->id }})" class="px-3 py-1.5 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 font-semibold text-xs transition-colors" title="Unsuspend Account">
                                                <i class="fa fa-play mr-1"></i> Unsuspend
                                            </button>
                                        @else
                                            <button wire:click="toggleSuspend({{ $kyb->id }})" class="px-3 py-1.5 bg-amber-50 text-amber-700 rounded-lg hover:bg-amber-100 font-semibold text-xs transition-colors" title="Suspend Account">
                                                <i class="fa fa-pause mr-1"></i> Suspend
                                            </button>
                                        @endif

                                        <!-- Delete Button -->
                                        <button wire:click="confirmDelete({{ $kyb->id }})" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors" title="Delete Settlement Details">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500">
                                    <i class="fa fa-folder-open text-3xl mb-2 block text-gray-300"></i>
                                    No settlement accounts found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($accounts->hasPages())
                <div class="p-4 border-t border-gray-200">
                    {{ $accounts->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Edit Modal -->
    @if($editingKybId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Edit Settlement Account</h3>
                    <button wire:click="closeEditModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="saveEdit" class="p-6 space-y-4">
                    <div>
                        <x-input-label value="Select Bank (Auto-fills details)" />
                        <select wire:model.live="edit_bank_id" class="w-full mt-1 border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm text-sm">
                            <option value="">-- Choose Bank --</option>
                            @foreach($banks as $b)
                                <option value="{{ $b['code'] }}">{{ $b['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label value="Bank Name" />
                        <x-text-input wire:model="edit_bank_name" type="text" class="w-full mt-1" required />
                        <x-input-error :messages="$errors->get('edit_bank_name')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label value="Bank Code / ID" />
                        <x-text-input wire:model="edit_bank_id" type="text" class="w-full mt-1" placeholder="e.g. 058" required />
                        <x-input-error :messages="$errors->get('edit_bank_id')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label value="Account Number" />
                        <x-text-input wire:model.defer="edit_account_number" type="text" maxlength="10" class="w-full mt-1 font-mono font-bold" required />
                        <x-input-error :messages="$errors->get('edit_account_number')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label value="Account Name" />
                        <x-text-input wire:model.defer="edit_account_name" type="text" class="w-full mt-1" required />
                        <x-input-error :messages="$errors->get('edit_account_name')" class="mt-1" />
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input wire:model.defer="edit_is_verified" type="checkbox" id="edit_is_verified" class="rounded border-gray-300 text-primary focus:ring-primary h-5 w-5">
                        <label for="edit_is_verified" class="text-sm font-semibold text-gray-700">Mark as Verified</label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" wire:click="closeEditModal" class="px-4 py-2 text-sm font-bold text-gray-600 hover:text-gray-800">
                            Cancel
                        </button>
                        <x-primary-button class="px-6 py-2.5 font-bold">
                            Save Changes
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Modal -->
    @if($deletingKybId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 text-center">
                <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Delete Settlement Account?</h3>
                <p class="text-sm text-gray-600 mb-6">Are you sure you want to clear/delete settlement account details for this merchant? This action cannot be undone.</p>

                <div class="flex items-center justify-center gap-3">
                    <button wire:click="closeDeleteModal" class="px-5 py-2.5 rounded-xl border border-gray-300 font-bold text-gray-700 hover:bg-gray-50 text-sm">
                        Cancel
                    </button>
                    <button wire:click="deleteSettlementAccount" class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-sm">
                        Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
