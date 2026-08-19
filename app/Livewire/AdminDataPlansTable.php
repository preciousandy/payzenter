<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\DataPlan;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class AdminDataPlansTable extends DataTableComponent
{
    protected $model = DataPlan::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function builder(): Builder
    {
        return DataPlan::query()
            ->select('data_plans.*')
            ->with('network')->orderBy('price');
    }

    public function filters(): array
    {
        return [
            'status' => SelectFilter::make('Network')
                ->options(
                    ['' => 'All'] + DataPlan::with('network')->get()->pluck('network.name', 'network_code')->toArray()
                )
                ->filter(function (Builder $builder, string $value) {
                    if ($value === '') return;
                    $builder->where('network_code', $value);
                }),

        ];
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Network", "network.name")
                ->searchable(),
            Column::make("Title", "title")
                ->searchable(),
            Column::make("Amount", "amount")
                ->sortable()
                ->format(fn($value) => formatNumber($value)),
            Column::make("Price", "price")
                ->sortable()
                ->format(fn($value) => formatNumber($value)),
            Column::make("Action")
                ->label(fn($row) => view('includes.table-action-btn', ['id' => $row->id, 'type' => 'data_plans']))->html(),
        ];
    }
}
