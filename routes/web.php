<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::get('/', fn () => redirect()->route('tasks.index'));

Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');

Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');

Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');

Route::get('/tasks/{task}/row', [TaskController::class, 'row'])->name('tasks.row');
Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');

Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
Route::post('/tasks/{id}/restore', [TaskController::class, 'restore'])->whereNumber('id')->name('tasks.restore');

Route::delete('/tasks/completed', [TaskController::class, 'clearCompleted'])->name('tasks.clearCompleted');
