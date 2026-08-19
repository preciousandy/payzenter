<?php

namespace App\Livewire;

use App\Livewire\Transfer\SwiftPay\User;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\UserTransaction;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateRangeFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class UsersTransactionTable extends DataTableComponent
{
    protected $model = UserTransaction::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');

        $this->setDefaultSort('id', 'desc');
        // $this->setFilterLayoutSlideDown();
        // $this->setFilterLayoutPopover();

    }

    public function builder(): Builder
    {

        return UserTransaction::query()
            ->select('user_transactions.*')
            ->with('user');
    }

    public function filters(): array
    {
        return [
            'status' => SelectFilter::make('Status')
                ->options([
                    '' => 'All',
                    UserTransaction::STATUS_PENDING => 'Pending',
                    UserTransaction::STATUS_SUCCESSFUL => 'Successful',
                    UserTransaction::STATUS_FAILED => 'Failed',
                ])->filter(function (Builder $builder, string $value) {
                    if ($value === '') return;
                    $builder->where('status', $value);
                }),
            'source' => SelectFilter::make('Source')
                ->options([
                    '' => 'All',
                    UserTransaction::SOURCE_WALLET_FUNDED => 'Wallet Funded',
                    UserTransaction::SOURCE_AIRTIME => 'Airtime',
                    UserTransaction::SOURCE_DATA => 'Data',
                    UserTransaction::SOURCE_CABLE => 'Cable',
                    UserTransaction::SOURCE_ELECTRICITY => 'Electricity',
                    UserTransaction::SOURCE_FOREIGN_TO_LOCAL_TRANSFER => 'Foreign to Local Transfer',
                    UserTransaction::SOURCE_LOCAL_TO_FOREIGN_TRANSFER => 'Local to Foreign Transfer',
                    UserTransaction::SOURCE_HOLDINGS => 'Holdings',
                    UserTransaction::SOURCE_INVESTMENTS => 'Investments',
                    UserTransaction::SOURCE_BUREAU_DA_CHANGE => 'Bureau Da Change',
                    UserTransaction::SOURCE_BUY_CRYPTO => 'Buy Crypto',
                    UserTransaction::SOURCE_SELL_CRYPTO => 'Sell Crypto',
                    UserTransaction::SOURCE_GIFT_CARD => 'Gift Card',
                    UserTransaction::SOURCE_BANK_TRANSFER => 'Bank Transfer',
                    UserTransaction::SOURCE_SWIFTPAY_TRANSFER => 'SwiftPay Transfer',
                    UserTransaction::SOURCE_SAVE_WITH_INTEREST => 'Save With Interest',
                    UserTransaction::SOURCE_GROUP_SAVINGS => 'Group Savings',
                    UserTransaction::SOURCE_AJO_SAVINGS => 'Ajo Savings',
                    UserTransaction::SOURCE_AJO_CONTRIBUTION => 'Ajo Contribution',
                    UserTransaction::SOURCE_INTERNATIONAL_TRANSFER => 'International Transfer',
                ])->filter(function (Builder $builder, string $value) {
                    if ($value === '') return;
                    $builder->where('source', $value);
                }),
            'type' => SelectFilter::make('Type')
                ->options([
                    '' => 'All',
                    UserTransaction::TYPE_DEBIT => 'Debit',
                    UserTransaction::TYPE_CREDIT => 'Credit',
                ])->filter(function (Builder $builder, string $value) {
                    if ($value === '') return;
                    $builder->where('type', $value);
                }),
            DateFilter::make('Date Created')
                ->filter(function (Builder $builder, string $value) {
                    $builder->whereDate('user_transactions.created_at', '=', $value);
                }),
            DateRangeFilter::make('Date Range')
                ->config([
                    'allowInput' => true,   // Allow manual input of dates
                    'altFormat' => 'F j, Y', // Date format that will be displayed once selected
                    'ariaDateFormat' => 'F j, Y', // An aria-friendly date format
                    'dateFormat' => 'Y-m-d', // Date format that will be received by the filter
                    'earliestDate' => '2020-01-01', // The earliest acceptable date
                    'latestDate' =>  now()->format('Y-m-d'), // The latest acceptable date
                    'placeholder' => 'Enter Date Range', // A placeholder value
                    'locale' => 'en',
                ])
                // ->setFilterPillValues([0 => 'minDate', 1 => 'maxDate']) 
                ->filter(function (Builder $builder, array $dateRange) { // Expects an array.
                    // dd($dateRange);
                    $builder->whereDate('user_transactions.created_at', '>=', $dateRange['minDate'])
                        ->whereDate('user_transactions.created_at', '<=', $dateRange['maxDate']);
                }),
        ];
    }


    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Name", "user.first_name")
                ->format(fn($value, $row, $col) => $row->user->first_name . ' ' . $row->user->last_name)
                ->searchable()
                ->sortable(),
            Column::make("Amount", "amount")
                ->format(fn($value) =>  number_format($value))
                ->sortable()->searchable(),
            Column::make("Type", "type")
                ->format(fn($value) => ucfirst($value))
                ->searchable()
                ->sortable(),
            Column::make("Source", "source")
                ->format(fn($value) => ucfirst(str_replace('_', ' ', $value)))
                ->searchable()
                ->sortable(),
            Column::make("Status", "status")
                ->format(fn($value) => ucfirst($value))
                ->searchable()
                ->sortable(),
            Column::make("Message", "message"),
            Column::make("Date", "created_at")
                ->format(fn($value) => $value->format('d M Y H:i:s'))
                ->sortable(),


        ];
    }
}
