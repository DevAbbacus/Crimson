<?php

namespace App\Enums;

class CurrentRmsSyncProcess extends Plain
{
    const SETUP = 1;

    const SYNC = 2;

    const READY = 3;

    const NAMES = [
        0 => 'Setup',
        1 => 'Sync',
        2 => 'Ready'
    ];
}
