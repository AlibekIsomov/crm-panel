<?php

use App\Console\Commands\CheckTaskReminders;
use Illuminate\Support\Facades\Schedule;

Schedule::command(CheckTaskReminders::class)->everyMinute();
