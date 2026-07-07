<?php

namespace App\Support;

class ArrivalTimeSlots
{
    /**
     * Generate arrival time slot options based on booking config.
     *
     * @return array<string, string> value => label pairs
     */
    public static function generate(): array
    {
        $start = config('booking.check_in_time', '14:00');
        $end = config('booking.latest_arrival_time', '23:30');

        $slots = [];
        $current = \Carbon\Carbon::createFromFormat('H:i', $start);
        $endTime = \Carbon\Carbon::createFromFormat('H:i', $end);

        while ($current->lte($endTime)) {
            $value = $current->format('H:i');
            $slots[$value] = $value;
            $current->addMinutes(30);
        }

        // Add "unknown" option for guests who are not sure
        $slots['unknown'] = 'Belum pasti — akan konfirmasi melalui WhatsApp';

        return $slots;
    }

    /**
     * Get valid arrival time values for validation.
     *
     * @return array<string>
     */
    public static function validValues(): array
    {
        return array_keys(self::generate());
    }
}
