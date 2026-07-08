<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('booking:expire-pending')->everyMinute()->withoutOverlapping();
Schedule::command('payment:reconcile')->everyFiveMinutes()->withoutOverlapping();
Schedule::command('loyalty:expire-points')->daily()->withoutOverlapping();
