@props([
'type'
])
<div>

    @switch($type)
    @case('wallet_funded')
    <x-lucide-wallet class="h-5 text-primary" />
    @break
    @case('airtime')
    <x-lucide-phone class="h-5 text-primary" />
    @break
    @case('data')
    <x-lucide-globe class="h-5 text-primary" />
    @break
    @case('cable')
    <x-lucide-tv class="h-5 text-primary" />
    @break
    @case('electricity')
    <x-lucide-zap class="h-5 text-primary" />
    @break
    @case('foreign_to_local_transfer')
    <x-lucide-arrow-right-left class="h-5 text-primary" />
    @break
    @case('local_to_foreign_transfer')
    <x-lucide-arrow-left-right class="h-5 text-primary" />
    @break
    @case('holdings')
    <x-lucide-hand-coins class="h-5 text-primary" />
    @break
    @case('investments')
    <x-lucide-trending-up class="h-5 text-primary" />
    @break
    @case('bureau_de_change')
    <x-lucide-badge-dollar-sign class="h-5 text-primary" />
    @break
    @case('buy-crypto')
    <x-lucide-bitcoin class="h-5 text-primary" />
    @break
    @case('sell_crypto')
    <x-lucide-bitcoin class="h-5 text-primary" />
    @break
    @case('gift_card')
    <x-lucide-gift class="h-5 text-primary" />
    @break
    @case('bank_transfer')
    <x-lucide-banknote class="h-5 text-primary" />
    @break
    @case('swiftpay_transfer')
    <x-lucide-send class="h-5 text-primary" />
    @break
    @case('save_with_interest')
    <x-lucide-piggy-bank class="h-5 text-primary" />
    @break
    @case('group_savings')
    <x-lucide-users class="h-5 text-primary" />
    @break
    @case('ajo_savings')
    <x-lucide-coins class="h-5 text-primary" />
    @break
    @case('ajo_contribution')
    <x-lucide-hand-coins class="h-5 text-primary" />
    @break
    @case('international_transfer')
    <x-lucide-globe class="h-5 text-primary" />
    @break
    @case('message')
    <x-lucide-message-circle class="h-5 text-primary" />
    @break
    @default
    <x-lucide-bell class="h-5 text-primary" />
    @break
    @endswitch
</div>