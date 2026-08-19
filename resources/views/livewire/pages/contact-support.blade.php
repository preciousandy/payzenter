<div class="min-h-[60vh] flex items-center justify-center bg-gray-50 p-4">
    <div class="max-w-xl w-full mx-auto bg-white rounded-xl  border border-gray-300 p-8 text-center">
        <div class="mb-4">
            <svg class="mx-auto h-12 w-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold mb-2 text-gray-800">Profile Editing Restricted</h2>
        <p class="text-gray-600 mb-6">
            For your security, direct profile editing is disabled. To update your information, please contact our support team.
        </p>
        <a href="{{ route('live-chat-welcome') }}" class="inline-block ">
            <button class=" pr-btn">
                Contact Support
            </button>
        </a>
    </div>
</div>