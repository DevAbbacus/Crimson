<?php

namespace App\Enums;

class CostGroup extends Plain
{
    const CREW = 1;

    const TRANSPORT = 2;

    const OTHER = 3;

    const RENTAL = 4;

    const NAMES = [
        1 => 'Crew',
        2 => 'Transport',
        3 => 'Other',
        4 => 'Rental'
    ];
}
