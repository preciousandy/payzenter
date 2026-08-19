<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\DomiciliaryBank;
use Illuminate\Database\Eloquent\Builder;

class DomiciliaryBanksTable extends DataTableComponent
{
    protected $model = DomiciliaryBank::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
    }

    public function builder(): Builder
    {
        return DomiciliaryBank::query()
            ->select('domiciliary_banks.*');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Bank name", "bank_name")
                ->searchable()
                ->sortable(),
            Column::make("Account name", "account_name")
                ->searchable()
                ->sortable(),
            Column::make("Account number", "account_number")
                ->searchable(),
            Column::make("Currencies")
                ->label(fn($row) => $row->currencies->pluck('code')->join(', ')),

            Column::make("Actions")
                ->label(fn($row) => view('admin.columns.edit-domiciliary-bank-button', ['bank' => $row])),
            Column::make("")
                ->label(fn($row) => view('admin.columns.delete-domiciliary-bank-button', ['bank' => $row]))

        ];
    }
}
