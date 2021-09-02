<?php

namespace App\Enums;

class CRMSSyncStatus extends Plain
{
    const NON_EXISTS = -1;
    const WORKING = 0;
    const COMPLETED = 1;
    const ERROR = 2;

    const NAMES = [
        -1 => 'Non exists',
        0 => 'Working',
        1 => 'Completed',
        2 => 'Error'
    ];
}
