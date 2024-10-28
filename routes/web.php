<?php

use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

use App\Livewire\TimeLogForm;
use App\Livewire\TimeLogList;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function (): void {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/time-logs', TimeLogList::class)->name('timelog.list');
});

Route::get('/time-logs/create', TimeLogForm::class)->name('timelog.create');

Route::get('/time-logs/{timeLogId}/edit', TimeLogForm::class)->name('timelog.edit');

Route::post('custom/livewire/update', function () {
    return Livewire::update();
});
