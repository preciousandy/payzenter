<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\User;
use App\Models\Kyc;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class KycTable extends DataTableComponent
{
    protected $model = User::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
    }

    public function builder(): Builder
    {
        return User::query()
            ->where(function ($q) {
                $q->whereHas('kyc')->orWhereHas('merchantKyb');
            })
            ->with(['kyc', 'merchantKyb'])
            ->select('users.*');
    }

    public function columns(): array
    {
        return [
            Column::make("ID", "id")
                ->sortable(),
            Column::make('Name', 'first_name')
                ->format(function ($value, $row) {
                    $business = $row->merchantKyb?->business_name;
                    $name = trim($row->first_name . ' ' . $row->last_name);
                    if ($business) {
                        return '<div><strong>' . e($business) . '</strong><div class="text-xs text-gray-500">' . e($name) . '</div></div>';
                    }
                    return e($name);
                })
                ->html()
                ->searchable()
                ->sortable(),
            Column::make('Email', 'email')
                ->searchable(),
            Column::make('KYC Type')
                ->label(function ($row) {
                    $badges = [];
                    if ($row->merchantKyb) {
                        $badges[] = '<span class="px-2 py-0.5 bg-indigo-100 text-indigo-800 text-xs font-medium rounded-full">Merchant KYB</span>';
                    }
                    if ($row->kyc) {
                        $badges[] = '<span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">Individual KYC</span>';
                    }
                    return implode(' ', $badges) ?: '<span class="text-gray-400">N/A</span>';
                })->html(),
            Column::make('Status', 'kyc_status')
                ->format(function ($value) {
                    $status = $value ?: Kyc::STATUS_UNVERIFIED;
                    $formattedValue = ucfirst(str_replace('_', ' ', $status));
                    $class = Str::slug($status);
                    return "<span class='{$class}'>{$formattedValue}</span>";
                })->html(),
            Column::make("Submitted", "created_at")
                ->sortable()->format(function ($value, $row) {
                    $date = $row->merchantKyb?->submitted_at ?? $row->kyc?->created_at ?? $value;
                    return $date ? $date->format('d M Y, H:i') : '-';
                }),
            Column::make('Action')
                ->label(function ($row, Column $column) {
                    return view('admin.columns.verify-kyc-button', ['row' => $row, 'kyc' => $row->kyc]);
                })
                ->html(),
        ];
    }
}
