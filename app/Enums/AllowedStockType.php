<?php

namespace App\Enums;

class AllowedStockType extends Plain
{
    const ALL = 0;

    const RENTAL = 1;

    const SALE = 2;

    const NAMES = [
        0 => 'All',
        1 => 'Rental',
        2 => 'Sale'
    ];
}
