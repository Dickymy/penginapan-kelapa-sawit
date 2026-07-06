<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('booking:expire-pending')->everyMinute();
