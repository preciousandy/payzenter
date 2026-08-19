@if ($type === 'buy_fx_order')
<livewire:admin.buy-fx-order-modal :orderId="$id" :wire:key="uniqid()" />
@endif
@if ($type === 'sell_fx_order')
<livewire:admin.sell-fx-order-modal :orderId="$id" :wire:key="uniqid()" />
@endif
@if ($type === 'data_plans')
@livewire('data-plan.edit', ['id' => $id], key(uniqid()))
@endif
@if ($type === 'cable_packages')
@livewire('cable-package.edit', ['id' => $id], key(uniqid()))

@endif