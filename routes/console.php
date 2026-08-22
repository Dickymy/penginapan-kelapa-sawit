<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('booking:expire-pending')->everyMinute()->withoutOverlapping();
Schedule::command('payment:reconcile')->everyFiveMinutes()->withoutOverlapping();
Schedule::command('loyalty:expire-points')->daily()->withoutOverlapping();
Schedule::command('booking:send-checkin-reminders')->dailyAt('09:00')->withoutOverlapping();
Schedule::command('booking:send-post-checkout-emails')->dailyAt('10:00')->withoutOverlapping();
