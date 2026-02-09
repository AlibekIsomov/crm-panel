<?php

use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\CheckTaskReminders;

Schedule::command(CheckTaskReminders::class)->everyMinute();
