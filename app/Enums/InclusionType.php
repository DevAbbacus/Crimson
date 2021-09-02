<?php

namespace App\Enums;

class InclusionType extends Plain
{
    const DEAFULT = 0;

    const MANDATORY = 1;

    const OPTIONAL = 2;

    const NAMES = [
        0 => 'Default',
        1 => 'Mandatory',
        2 => 'Optional'
    ];
}
