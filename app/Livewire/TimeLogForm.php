<?php

namespace App\Livewire;

use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Livewire\Component;
use Carbon\Carbon;

use App\Models\Department;
use App\Models\Project;
use App\Models\Subproject;
use App\Models\TimeLog;

class TimeLogForm extends Component implements HasForms
{
	use InteractsWithForms;

	public $timeLogId;
	public $department_id, $project_id, $subproject_id, $date, $start_time, $end_time;

	public function mount($timeLogId = null)
	{
		$this->timeLogId = $timeLogId;

		if ($timeLogId) {
			$timeLog = TimeLog::findOrFail($timeLogId);
			$this->fill([
				'department_id' => $timeLog->subproject->project->department_id,
				'project_id' => $timeLog->subproject->project_id,
				'subproject_id' => $timeLog->subproject_id,
				'date' => $timeLog->date,
				'start_time' => $timeLog->start_time,
				'end_time' => $timeLog->end_time,
			]);
		}
	}

	protected function getFormSchema(): array
	{
		return [
			Select::make('department_id')
				->label('Department')
				->options(Department::pluck('name', 'id'))
				->reactive()
				->required(),

			Select::make('project_id')
				->label('Project')
				->options(function () {
					return Project::where('department_id', $this->department_id)->pluck('name', 'id');
				})
				->reactive()
				->required(),

			Select::make('subproject_id')
				->label('Subproject')
				->options(function () {
					return Subproject::where('project_id', $this->project_id)->pluck('name', 'id');
				})
				->required(),

			DatePicker::make('date')
				->label('Date')
				->required(),

			TimePicker::make('start_time')
				->label('Start Time')
				->required(),

			TimePicker::make('end_time')
				->label('End Time')
				->required(),
		];
	}

	public function submit()
	{
		$this->validate([
			'department_id' => 'required|exists:departments,id',
			'project_id' => 'required|exists:projects,id',
			'subproject_id' => 'required|exists:subprojects,id',
			'date' => 'required|date',
			'start_time' => 'required|date_format:H:i:s',
			'end_time' => 'required|date_format:H:i:s|after:start_time',
		], [
			'department_id.required' => 'Please select a department.',
			'project_id.required' => 'Please select a project.',
			'subproject_id.required' => 'Please select a subproject.',
			'date.required' => 'Please select a date.',
			'start_time.required' => 'Please select a start time.',
			'end_time.required' => 'Please select an end time.',
			'end_time.after' => 'End time must be later than the start time.',
		]);

		$data = [
			'user_id' => auth()->id(),
			'subproject_id' => $this->subproject_id,
			'date' => $this->date,
			'start_time' => $this->start_time,
			'end_time' => $this->end_time,
			'total_hours' => Carbon::parse($this->start_time)->diffInHours(Carbon::parse($this->end_time)),
		];

		if ($this->timeLogId) {
			TimeLog::find($this->timeLogId)->update($data);
			session()->flash('message', 'Time log updated successfully.');
		} else {
			TimeLog::create($data);
			session()->flash('message', 'Time log created successfully.');
		}

		return redirect()->route('timelog.list');
	}

	public function render()
	{
		return view('livewire.time-log-form')->layout('layouts.app');
	}
}
