@props([
    'wireModel',
    'existingPath' => null,
    'label' => 'Upload Document',
    'hint' => 'PDF, JPG or PNG (up to 10MB)',
    'accept' => '.pdf,.png,.jpg,.jpeg',
    'sampleUrl' => null,
    'required' => false,
])

@php
    $id = 'file_' . md5($wireModel);
    $hasExisting = !empty($existingPath);
    $isPdf = $hasExisting && str_ends_with(strtolower($existingPath), '.pdf');
    $existingUrl = $hasExisting ? asset('storage/' . $existingPath) : null;
@endphp

<div 
    x-data="{
        isUploading: false,
        progress: 0,
        dragOver: false,
        attachedName: '',
        attachedSize: '',
        hasNewFile: false,
        handleDrop(e) {
            let files = e.dataTransfer.files;
            if (files.length > 0) {
                this.$refs.fileInput.files = files;
                this.$refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        },
        handleSelect(e) {
            let files = e.target.files;
            if (files.length > 0) {
                this.attachedName = files[0].name;
                let bytes = files[0].size;
                if (bytes < 1024 * 1024) {
                    this.attachedSize = (bytes / 1024).toFixed(1) + ' KB';
                } else {
                    this.attachedSize = (bytes / (1024 * 1024)).toFixed(1) + ' MB';
                }
                this.hasNewFile = true;
            }
        }
    }"
    x-on:livewire-upload-start="isUploading = true; $dispatch('uploading-state-change', { delta: 1 })"
    x-on:livewire-upload-finish="isUploading = false; $dispatch('uploading-state-change', { delta: -1 })"
    x-on:livewire-upload-error="isUploading = false; $dispatch('uploading-state-change', { delta: -1 })"
    x-on:livewire-upload-progress="progress = $event.detail.progress"
    class="w-full"
>
    <div class="flex items-center justify-between mb-1.5">
        <label for="{{ $id }}" class="text-sm font-medium text-gray-800 flex items-center gap-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
        @if($sampleUrl)
            <button 
                type="button" 
                class="text-xs font-semibold text-[#1400FB] hover:text-blue-700 hover:underline flex items-center gap-1"
                @click="$dispatch('open-sample', { url: '{{ $sampleUrl }}', title: '{{ $label }}' })"
            >
                <i class="fa fa-eye text-[11px]"></i> See sample
            </button>
        @endif
    </div>

    <!-- Hidden native file input -->
    <input 
        type="file" 
        wire:model="{{ $wireModel }}" 
        id="{{ $id }}"
        x-ref="fileInput"
        accept="{{ $accept }}"
        @change="handleSelect($event)"
        class="sr-only"
    />

    <!-- Palate Container -->
    <div>
        <!-- 1. Uploading State -->
        <div 
            x-show="isUploading" 
            x-cloak
            class="border-2 border-blue-400 bg-blue-50/70 rounded-2xl p-5 text-center transition-all animate-pulse"
        >
            <div class="flex items-center justify-center gap-3 text-[#1400FB] mb-3">
                <i class="fa fa-circle-notch fa-spin text-2xl"></i>
                <div class="text-left">
                    <div class="text-sm font-bold text-gray-900">Attaching file to PayZenter...</div>
                    <div class="text-xs text-gray-600" x-text="attachedName ? attachedName + ' (' + attachedSize + ')' : 'Please wait while file transfers'"></div>
                </div>
                <div class="ml-auto font-mono text-sm font-extrabold text-[#1400FB]" x-text="progress + '%'"></div>
            </div>
            <div class="w-full bg-blue-200/80 rounded-full h-2.5 overflow-hidden">
                <div 
                    class="bg-[#1400FB] h-2.5 rounded-full transition-all duration-300" 
                    :style="'width: ' + progress + '%'"
                ></div>
            </div>
            <p class="text-[11px] text-blue-700 mt-2 font-medium">
                Hold on, syncing with your form. The Next button will be ready as soon as this finishes.
            </p>
        </div>

        <!-- 2. Attached State (Either freshly selected or previously uploaded) -->
        <div 
            x-show="!isUploading && (hasNewFile || {{ $hasExisting ? 'true' : 'false' }})"
            class="border border-emerald-300 bg-emerald-50/50 hover:bg-emerald-50 rounded-2xl p-4 transition-all duration-200 shadow-sm"
        >
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0 text-xl shadow-inner">
                        <i class="fa {{ $isPdf ? 'fa-file-pdf text-red-600' : 'fa-file-lines text-emerald-700' }}"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-gray-900 truncate" x-text="attachedName || '{{ basename($existingPath ?? '') }}' || 'Document Attached'"></span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                <i class="fa fa-check-circle mr-1 text-emerald-600"></i> Attached
                            </span>
                        </div>
                        <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-2">
                            <span x-show="attachedSize" x-text="attachedSize"></span>
                            @if($hasExisting)
                                <a 
                                    href="{{ $existingUrl }}" 
                                    target="_blank" 
                                    class="text-[#1400FB] hover:underline font-medium inline-flex items-center gap-1"
                                >
                                    <i class="fa fa-arrow-up-right-from-square text-[10px]"></i> View current file
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    <button 
                        type="button" 
                        @click="$refs.fileInput.click()"
                        class="px-3 py-1.5 text-xs font-semibold text-[#1400FB] bg-white border border-blue-200 hover:bg-blue-50 rounded-xl transition duration-150 shadow-sm flex items-center gap-1.5"
                    >
                        <i class="fa fa-arrows-rotate text-[11px]"></i>
                        <span>Replace</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 3. Empty / Dropzone State -->
        <div 
            x-show="!isUploading && !hasNewFile && !{{ $hasExisting ? 'true' : 'false' }}"
            @dragover.prevent="dragOver = true"
            @dragleave.prevent="dragOver = false"
            @drop.prevent="dragOver = false; handleDrop($event)"
            @click="$refs.fileInput.click()"
            :class="dragOver ? 'border-[#1400FB] bg-blue-50/50 scale-[1.01]' : 'border-gray-300 hover:border-[#1400FB] bg-white hover:bg-blue-50/20'"
            class="border-2 border-dashed rounded-2xl p-6 text-center cursor-pointer transition-all duration-200 group relative"
        >
            <div class="w-12 h-12 mx-auto rounded-2xl bg-blue-50 text-[#1400FB] group-hover:bg-[#1400FB] group-hover:text-white flex items-center justify-center text-xl transition-all duration-200 shadow-sm mb-3">
                <i class="fa fa-cloud-arrow-up"></i>
            </div>
            <div class="text-sm font-semibold text-gray-800 group-hover:text-[#1400FB] transition-colors">
                <span>Click to browse</span> or drag and drop
            </div>
            <p class="text-xs text-gray-500 mt-1">
                {{ $hint }}
            </p>
        </div>
    </div>

    <!-- Error message display -->
    <x-input-error :messages="$errors->get($wireModel)" class="mt-2" />
</div>
