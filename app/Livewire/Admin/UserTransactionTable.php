<?php

namespace App\Livewire\Admin;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\UserTransaction;
use Illuminate\Database\Eloquent\Builder;

class UserTransactionTable extends DataTableComponent
{
    protected $model = UserTransaction::class;

    public $userId;

    public function mount($userId)
    {
        $this->userId = $userId;
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');

        $this->setDefaultSort('id', 'desc');
    }

    public function builder(): Builder
    {
        return UserTransaction::query()
            ->where('user_id', $this->userId);
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Amount", "amount")->sortable()->format(fn ($value) => '₦' . formatNumber($value)),
            Column::make("Type", "type"),
            Column::make("Source", "source"),
            Column::make("Status", "status"),
            Column::make("Message"),
            Column::make("Created at", "created_at")
                ->sortable()->format(fn ($value) => $value->format('d M Y H:i:s')),

        ];
    }
}
