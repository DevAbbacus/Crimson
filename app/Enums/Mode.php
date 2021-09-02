<?php

namespace App\Enums;

class Mode extends Plain
{
    const ACCESSORY = 0;

    const SAFETY = 1;

    const COMPONENT = 2;

    const NAMES = [
        0 => 'Accessory',
        1 => 'Safety',
        2 => 'Component'
    ];
}
