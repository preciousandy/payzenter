<div class="p-4 lg:p-6 max-w-7xl mx-auto" x-data="{ showToken: false, copyText: '' }">
    <x-loading-spinner wire:target="createKey,revokeKey" />

    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">API Keys</h1>
        <p class="text-gray-500 mt-2">Manage your API keys to authenticate your integration with our platform</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Create API Key Card -->
        <div class="lg:col-span-4">
            <div class="bg-gradient-to-br from-primary/5 to-primary/10 border border-primary/20 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-3 bg-primary/20 rounded-xl">
                        <span class="fa fa-key text-primary text-xl"></span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Create New Key</h3>
                        <p class="text-xs text-gray-500">Generate a new API key</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <x-input-label value="Key Name (Optional)" />
                        <x-text-input wire:model.defer="name" placeholder="e.g., Production Server" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <x-primary-button wire:click="createKey" class="w-full rounded-xl py-3 font-semibold">
                        <span class="fa fa-plus-circle mr-2"></span>
                        Create API Key
                    </x-primary-button>
                </div>

                @if($createdToken)
                    <div class="mt-6 bg-green-50 border border-green-200 rounded-2xl p-5" x-data="{ copied: false }">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="fa fa-check-circle text-green-600 text-lg"></span>
                            <h4 class="font-bold text-green-800">Key Generated Successfully!</h4>
                        </div>
                        <p class="text-sm text-green-700 mb-3">Copy this key now - you won't be able to see it again!</p>
                        
                        <div class="relative">
                            <div class="font-mono text-sm bg-white border border-green-200 rounded-xl p-4 break-all select-all">
                                {{ $createdToken }}
                            </div>
                            <button 
                                @click="navigator.clipboard.writeText('{{ $createdToken }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                class="absolute top-3 right-3 p-2 bg-green-100 hover:bg-green-200 rounded-lg text-green-700 transition duration-200"
                            >
                                <span class="fa" :class="copied ? 'fa-check' : 'fa-copy'"></span>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- API Keys List -->
        <div class="lg:col-span-8">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <span class="fa fa-list text-gray-500"></span>
                        Your API Keys
                    </h3>
                    <span class="text-xs text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-200">
                        {{ count($keys) }} Key(s)
                    </span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase bg-gray-50 text-gray-700 font-semibold">
                            <tr>
                                <th class="px-6 py-4">Name</th>
                                <th class="px-6 py-4">Prefix</th>
                                <th class="px-6 py-4">Created</th>
                                <th class="px-6 py-4">Last Used</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($keys as $key)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary font-semibold text-xs">
                                                {{ substr($key->name ?? 'K', 0, 1) }}
                                            </div>
                                            <span class="font-medium text-gray-900">{{ $key->name ?? 'Untitled Key' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <code class="font-mono text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded-lg">
                                            {{ $key->token_prefix }}
                                        </code>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $key->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $key->last_used_at ? $key->last_used_at->format('d M Y, H:i') : 'Never' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($key->revoked_at)
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                                <span class="fa fa-times-circle"></span>
                                                Revoked
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                                <span class="fa fa-check-circle"></span>
                                                Active
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if(!$key->revoked_at)
                                            <button 
                                                wire:click="revokeKey({{ $key->id }})"
                                                wire:confirm="Are you sure you want to revoke this API key?"
                                                class="inline-flex items-center gap-1 text-sm text-red-600 hover:text-red-800 hover:bg-red-50 px-3 py-2 rounded-lg transition duration-150"
                                            >
                                                <span class="fa fa-ban"></span>
                                                Revoke
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                <span class="fa fa-key text-gray-400 text-3xl"></span>
                                            </div>
                                            <p class="text-gray-600 font-medium mb-2">No API keys yet</p>
                                            <p class="text-sm text-gray-400">Create your first API key to start integrating</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <x-slot:headerTitle>
        API Keys
    </x-slot:headerTitle>
</div>

