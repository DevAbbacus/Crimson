<?php

namespace App\Enums;

class PostRentUnavailability extends Plain
{
    const HOUR = 0;

    const QUARTER_DAY = 2;

    const HALF_DAY = 3;

    const DAY = 4;

    const NAMES = [
        1 => 'Hour',
        2 => '1/4 Day',
        3 => '1/2 Day',
        4 => 'Day'
    ];
}
