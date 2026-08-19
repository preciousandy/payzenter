<button {{ $attributes->merge(['type' => 'submit', 'class' => 'pr-btn  ']) }}>
    {{ $slot }}
</button>