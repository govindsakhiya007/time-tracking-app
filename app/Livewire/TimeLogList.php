<?php

namespace App\Livewire;

use App\Models\TimeLog;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Livewire\Component;

class TimeLogList extends Component implements HasForms, HasTable
{
	use InteractsWithTable;
	use InteractsWithForms;

	public $employee_id;
	public $department_id;
	public $project_id;
	public $subproject_id;
	public $start_date;
	public $end_date;

	public function table(Table $table): Table
	{
		return $table
			->query($this->getFilteredLogs())
			->columns([
				TextColumn::make('user.name')->label('Name'),
				TextColumn::make('subproject.project.department.name')->label('Department'),
				TextColumn::make(name: 'subproject.project.name')->label('Project'),
				TextColumn::make('subproject.name')->label('Subproject'),
				TextColumn::make('date')->label('Date'),
				TextColumn::make('start_time')->label('Start Time'),
				TextColumn::make('end_time')->label('End Time'),
				TextColumn::make('total_hours')->label('Total Hours'),
			])
			->filters([
				SelectFilter::make('employee_id')
					->label('Employee')
					->relationship('user', 'name'), // This assumes your TimeLog model has a user relationship
				// ->visible(fn() => auth()->user()->role === 'manager'),
				// ->query(fn($query) => $query->whereHas('user', function ($query) {
				// 	$query->where('role', 'employee');
				// })),

				SelectFilter::make('department_id')
					->label('Department')
					->relationship('subproject.project.department', 'name')
					->visible(fn() => auth()->user()->role === 'manager'),

				SelectFilter::make('project_id')
					->label('Project')
					->relationship('subproject.project', 'name')
					->visible(fn() => auth()->user()->role === 'manager'),

				SelectFilter::make('subproject_id')
					->label('Subproject')
					->relationship('subproject', 'name')
					->visible(fn() => auth()->user()->role === 'manager'),
			])
			->actions([
				\Filament\Tables\Actions\Action::make('edit')
					->label('Edit')
					->url(fn($record): string => route('timelog.edit', $record->id))
					->visible(fn() => auth()->user()->role === 'manager'),

				\Filament\Tables\Actions\Action::make('delete')
					->label('Delete')
					->action(fn($record) => $this->delete($record->id))
					->requiresConfirmation()
					->visible(fn() => auth()->user()->role === 'manager'),
			])
			->bulkActions([
				// Add any bulk actions if needed
			]);
	}

	protected function getFilteredLogs()
	{
		// Start the base query with eager loading relationships for efficiency
		$query = TimeLog::with(['subproject.project.department', 'user']);

		// Role-based restriction: Only allow employees to see their own logs
		if (auth()->user()->role === 'employee') {
			$query->where('user_id', auth()->id());
		}

		// Filter by specific employee if manager and employee_id is set
		if ($this->employee_id && auth()->user()->role === 'manager') {
			$query->where('user_id', $this->employee_id);
		}

		// Department filter
		if ($this->department_id) {
			$query->whereHas('subproject.project.department', function ($q) {
				$q->where('id', $this->department_id);
			});
		}

		// Project filter
		if ($this->project_id) {
			$query->whereHas('subproject.project', function ($q) {
				$q->where('id', $this->project_id);
			});
		}

		// Subproject filter
		if ($this->subproject_id) {
			$query->where('subproject_id', $this->subproject_id);
		}

		// Date range filter
		if ($this->start_date && $this->end_date) {
			$query->whereBetween('date', [$this->start_date, $this->end_date]);
		}

		return $query;
	}

	public function delete($id)
	{
		$log = TimeLog::find($id);
		if ($log) {
			$log->delete();
			session()->flash('message', 'Time log deleted successfully.');
		}
	}

	public function render()
	{
		return view('livewire.time-log-list')->layout('layouts.app');
	}
}
