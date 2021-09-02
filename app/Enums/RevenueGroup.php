<?php

namespace App\Enums;

class RevenueGroup extends Plain
{
    const RENTAL = 1;

    const SALE = 2;

    const SURCHARGE = 3;

    const SERVICE = 4;

    const PART_INVOICE_CHARGES = 5;

    const NAMES = [
        1 => 'Rental',
        2 => 'Sale',
        3 => 'Surcharge',
        4 => 'Service',
        5 => 'Part Invoice Charges',
    ];
}
