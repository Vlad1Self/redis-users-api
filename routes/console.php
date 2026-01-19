<?php

use Illuminate\Support\Facades\Schedule;
use App\Jobs\CleanupOldUsers;

Schedule::job(new CleanupOldUsers())
    ->dailyAt('03:00')
    ->timezone('Europe/Moscow')
    ->name('cleanup_old_users');
