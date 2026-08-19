<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Kyc;
use Illuminate\Database\Eloquent\Builder;

class KycTable extends DataTableComponent
{
    protected $model = Kyc::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
    }

    public function builder(): Builder
    {
        return Kyc::query()
            ->select('kycs.*');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make('First Name', 'first_name')
                ->sortable()
                ->searchable(),
            Column::make('Last Name', 'last_name')
                ->sortable()
                ->searchable(),
            Column::make('Other Names', 'other_names')
                ->sortable()
                ->searchable(),
            Column::make('Status', 'user.kyc_status')
                ->format(function ($value) {
                    return '<span class="' . $value . '">' . ucfirst($value) . '</span>';
                })->html(),

            Column::make("Created at", "created_at")
                ->sortable()->format(function ($value) {
                    return $value->format('d M Y');
                }),
            Column::make('Action')
                ->label(function ($row, Column $column) {
                    return view('admin.columns.verify-kyc-button', ['kyc' => $row]);
                }),

        ];
    }
}
