<button {{ $attributes->merge(['type' => 'button', 'class' => 'sec-btn']) }}>
    {{ $slot }}
</button>