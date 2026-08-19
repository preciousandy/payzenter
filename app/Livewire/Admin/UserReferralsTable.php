<?php

namespace App\Livewire\Admin;

use App\Models\UserReferral;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class UserReferralsTable extends DataTableComponent
{
    protected $model = UserReferral::class;

    public $userId;

    public function mount($userId)
    {
        $this->userId = $userId;
    }

    public function builder(): Builder
    {
        return UserReferral::query()
            ->where('referred_by', $this->userId)->with('user', 'referredBy');
    }


    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("User", "user.first_name")
                ->searchable()
                ->sortable(),
            Column::make("User", "user.last_name")
                ->searchable()
                ->sortable(),
            Column::make('Amount')->sortable()->format(fn ($value, $row) => '₦' . formatNumber($row->amount)),
            Column::make("Status", "status"),
            Column::make("Created at", "created_at")
                ->sortable()->format(fn ($value) => $value->format('d M Y H:i:s')),

        ];
    }
}
