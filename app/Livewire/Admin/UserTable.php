<?php

namespace App\Livewire\Admin;

use App\Models\Kyc;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use illuminate\Support\Str;

class UserTable extends DataTableComponent
{
    protected $model = User::class;

    public $rolesFilter = [];

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
    }

    public function builder(): Builder
    {
        return User::query()
            ->with(['merchantApiKeys', 'merchantKyb'])
            ->select('users.*');
    }

    // Add bulk action for messaging
    public function bulkActions(): array
    {
        return [
            'openMessageModal' => 'Send Message',
            'openNotifcationsModal' => 'Send Notifications',
        ];
    }

    // Method to open message modal
    public function openMessageModal()
    {
        // Dispatch event to open modal with selected user IDs
        $this->dispatch('openBulkMessageModal', $this->getSelected());
    }

    public function openNotifcationsModal()
    {
        // Dispatch event to open modal with selected user IDs
        $this->dispatch('openBulkNotificationsModal', $this->getSelected());
    }

    public function filters(): array
    {
        return [
            'KYY Status' => SelectFilter::make('Status')
                ->options([
                    '' => 'All',
                    Kyc::STATUS_UNVERIFIED => 'Unverified',
                    Kyc::STATUS_PENDING => 'Pending',
                    Kyc::STATUS_VERIFIED => 'Verified',
                    Kyc::STATUS_REJECTED => 'Rejected',
                ])->filter(function (Builder $builder, string $value) {
                    if ($value === '') return;
                    $builder->where('kyc_status', $value);
                }),
            'Role' => SelectFilter::make('Role')
                ->options($this->rolesFilter)
                ->filter(function (Builder $builder, string $value) {
                    if ($value === '') return;
                    $builder->whereHas('roles', function ($query) use ($value) {
                        $query->where('name', $value);
                    });
                }),
            'Account Status' => SelectFilter::make('Account Status')
                ->options([
                    User::ACCOUNT_STATUS_ACTIVE => 'Active',
                    User::ACCOUNT_STATUS_BLOCKED => "Blocked",
                    User::ACCOUNT_STATUS_DELETED => 'Deleted',
                    User::ACCOUNT_STATUS_SUSPENDED => 'Suspended'
                ])->filter(function (Builder $builder, string $value) {
                    if ($value === '') return;
                    $builder->withTrashed()->where('account_status', $value);
                }),

        ];
    }



    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make('Name', 'first_name')
                ->format(fn($value, $row, $col) => $row->first_name . ' ' . $row->last_name)
                ->searchable()
                ->sortable(),
            Column::make("Email")
                ->searchable(),
            Column::make("Merchant ID", "id")
                ->format(fn($value, $row) => $row->merchantKyb ? $row->merchantKyb->id : 'N/A'),
            Column::make("API Key")
                ->label(fn($row) => $row->merchantApiKeys->first() ? $row->merchantApiKeys->first()->token_prefix . '...' : 'N/A'),
            Column::make("Website URL")
                ->label(fn($row) => $row->merchantKyb ? $row->merchantKyb->business_website : 'N/A'),
            Column::make("Username")
                ->searchable(),
            Column::make("Kyc Status")
                ->format(function ($value) {
                    $formattedValue = ucfirst(str_replace('_', ' ', $value));
                    $class = Str::slug($value);
                    return "<span class=' {$class}'>{$formattedValue}</span>";
                })
                ->html(),
            Column::make("Account Status", "account_status")
                ->format(function ($value) {

                    return "<span class=' {$value}'>{$value}</span>";
                })
                ->html(),
            Column::make("Wallet Balance", "wallet_balance")
                ->sortable()->format(fn($value) =>  '₦' . formatNumber($value)),
            Column::make("Roles")
                ->label(fn($row) => implode(", ", $row->roles->pluck('name')->toArray())),
            Column::make("Joined", "created_at")
                ->format(fn($value) => $value->diffForHumans())->sortable(),
            Column::make("Action")
                ->label(fn($row) => '<a href="' . route('admin.users.show', $row->hash_id) . '">
                            <button class=" px-4 py-2  dark:bg-primary border border-primary  text-primary font-semibold text-sm rounded-xl">
                                <span class=" fa fa-eye mr-2"></span>
                                View
                            </button>
                        </a>')->html(),
        ];
    }
}
