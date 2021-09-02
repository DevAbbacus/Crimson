<?php

namespace App\Enums;

class RateDefinition extends Plain
{
    const HOURLY_RATE = 1;

    const WEEKLY_RATE = 2;

    const DAILY_RATE = 3;

    const THREE_DAY_WEEK_RATE = 4;

    const CATERING = 5;

    const NAMES = [
        1 => 'Hourly Rate',
        2 => 'Weekly Rate',
        3 => 'Daily Rate',
        4 => '3 Day Week Rate',
        5 => 'Catering'
    ];
}
