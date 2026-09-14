<?php

use App\Livewire\Quizzes\Index as QuizzesIndex;
use App\Livewire\Quizzes\Manage as QuizzesManage;
use App\Livewire\Sessions\Create as SessionsCreate;
use App\Livewire\Sessions\HostLobby as SessionsHostLobby;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/quizzes', QuizzesIndex::class)
    ->middleware(['auth', 'verified'])
    ->name('quizzes.index');

Route::get('/quizzes/{quiz}', QuizzesManage::class)
    ->middleware(['auth', 'verified'])
    ->name('quizzes.manage');

Route::get('/quizzes/{quiz}/sessions/create', SessionsCreate::class)
    ->middleware(['auth', 'verified'])
    ->name('sessions.create');

Route::get('/sessions/{gameSession}/host', SessionsHostLobby::class)
    ->middleware(['auth', 'verified'])
    ->name('sessions.host');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';
