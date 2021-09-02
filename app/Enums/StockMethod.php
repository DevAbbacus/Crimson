<?php

namespace App\Enums;

class StockMethod extends Plain
{
    const NONE = 0;

    const BULK = 1;

    const SERIALISED = 2;

    const NAMES = [
        0 => 'None',
        1 => 'Bulk',
        2 => 'Serialised'
    ];

}
