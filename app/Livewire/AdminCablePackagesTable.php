<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\CablePackage;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class AdminCablePackagesTable extends DataTableComponent
{
    protected $model = CablePackage::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function builder(): Builder
    {
        return CablePackage::query()
            ->select('cable_packages.*')
            ->with('cableProvider')->orderBy('price');
    }

    public function filters(): array
    {
        return [
            'status' => SelectFilter::make('Network')
                ->options(
                    ['' => 'All'] + CablePackage::with('cableProvider')->get()->pluck('cableProvider.name', 'provider_code')->toArray()
                )
                ->filter(function (Builder $builder, string $value) {
                    if ($value === '') return;
                    $builder->where('provider_code', $value);
                }),

        ];
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Provider", "cableProvider.name")
                ->searchable(),
            Column::make("Title", "name")
                ->searchable(),
            Column::make("Amount", "amount")
                ->sortable()
                ->format(fn($value) => formatNumber($value)),
            Column::make("Price", "price")
                ->sortable()
                ->format(fn($value) => formatNumber($value)),
            Column::make("Action")
                ->label(fn($row) => view('includes.table-action-btn', ['id' => $row->id, 'type' => 'cable_packages']))->html(),

        ];
    }
}
