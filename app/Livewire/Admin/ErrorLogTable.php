<?php

namespace App\Livewire\Admin;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\ErrorLog;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class ErrorLogTable extends DataTableComponent
{
  protected $model = ErrorLog::class;

  public function configure(): void
  {
    $this->setPrimaryKey('id');
    $this->setDefaultSort('id', 'desc');
  }

  public function builder(): Builder
  {
    return ErrorLog::query()
      ->with(['user'])
      ->select('error_logs.*');
  }

  public function filters(): array
  {
    return [
      'Status' => SelectFilter::make('Status')
        ->options([
          '' => 'All',
          ErrorLog::STATUS_OPEN => 'Open',
          ErrorLog::STATUS_IN_PROGRESS => 'In Progress',
          ErrorLog::STATUS_RESOLVED => 'Resolved',
          ErrorLog::STATUS_CLOSED => 'Closed',
        ])->filter(function (Builder $builder, string $value) {
          if ($value === '') return;
          $builder->where('status', $value);
        }),
      'Error Source' => SelectFilter::make('Error Source')
        ->options([
          '' => 'All',
          ...ErrorLog::getErrorSources()
        ])->filter(function (Builder $builder, string $value) {
          if ($value === '') return;
          $builder->where('error_source', $value);
        }),
      'Severity' => SelectFilter::make('Severity')
        ->options([
          '' => 'All',
          'low' => 'Low',
          'medium' => 'Medium',
          'high' => 'High',
          'critical' => 'Critical',
        ])->filter(function (Builder $builder, string $value) {
          if ($value === '') return;
          $builder->where('severity', $value);
        }),
    ];
  }

  public function columns(): array
  {
    return [
      Column::make("Id", "id")
        ->sortable(),
      Column::make('User', 'user.first_name')
        ->format(fn($value, $row, $col) => $row->user ? $row->user->first_name . ' ' . $row->user->last_name : 'System')
        ->searchable()
        ->sortable(),
      Column::make('Error Source', 'error_source')
        ->format(function ($value) {
          return ucfirst(str_replace('_', ' ', $value));
        })
        ->sortable(),
      Column::make('Error Message', 'error_message')
        ->format(function ($value) {
          return strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value;
        })
        ->searchable(),
      Column::make('Severity', 'severity')
        ->format(function ($value) {
          return '<span class="' . $value . '">' . ucfirst($value) . '</span>';
        })
        ->html()
        ->sortable(),
      Column::make('Status', 'status')
        ->format(function ($value) {
          $displayValue = ucfirst(str_replace('_', ' ', $value));
          return '<span class="' . str_replace('_', '-', $value) . '">' . $displayValue . '</span>';
        })
        ->html()
        ->sortable(),
      Column::make("Date", "created_at")
        ->sortable()->format(function ($value) {
          return $value->format('d M Y, H:i');
        }),
      Column::make('Action')
        ->label(function ($row, Column $column) {
          return view('admin.columns.error-log-button', ['errorLog' => $row]);
        }),
    ];
  }
}
