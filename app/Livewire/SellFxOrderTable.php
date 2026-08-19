<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\SellFxOrder;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateRangeFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class SellFxOrderTable extends DataTableComponent
{
    protected $model = SellFxOrder::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function builder(): Builder
    {

        return SellFxOrder::query()
            ->select('sell_fx_orders.*')
            ->with('user')->latest();
    }

    public function filters(): array
    {
        return [
            'status' => SelectFilter::make('Status')
                ->options([
                    '' => 'All',
                    SellFxOrder::STATUS_PENDING => 'Pending',
                    SellFxOrder::STATUS_CREATED => 'Created',
                    SellFxOrder::STATUS_COMPLETED => 'Completed',
                    SellFxOrder::STATUS_FAILED => 'Failed',
                    SellFxOrder::STATUS_EXPIRED => 'Expired',
                ])->filter(function (Builder $builder, string $value) {
                    if ($value === '') return;
                    $builder->where('status', $value);
                }),
            DateFilter::make('Date Submitted')
                ->filter(function (Builder $builder, string $value) {
                    $builder->whereDate('sell_fx_orders.created_at', '=', $value);
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
                    $builder->whereDate('sell_fx_orders.created_at', '>=', $dateRange['minDate'])
                        ->whereDate('sell_fx_orders.created_at', '<=', $dateRange['maxDate']);
                }),
        ];
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("User",)->label(fn($row) => $row->user->first_name . ' ' . $row->user->last_name),
            Column::make("Email",)->label(fn($row) => $row->user->email),
            Column::make("Currency", "currency_name")->sortable(),
            Column::make("Amount", "amount")->sortable()->format(fn($value) => formatNumber($value)),
            Column::make("Naira Amount", "naira_amount")->sortable()->format(fn($value) => formatNumber($value)),
            Column::make("Status", "status")->format(fn($value) => "<span class=\"{$value}\">{$value}</span>")->html(),
            Column::make("Date", "created_at")
                ->format(fn($value) => formatDate($value)),
            Column::make("Action")
                ->label(fn($row) => view('includes.table-action-btn', ['id' => $row->id, 'type' => 'sell_fx_order']))->html(),
        ];
    }
}
