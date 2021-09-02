<?php

namespace App\Collections;

use Illuminate\Support\Collection;

class PlainModelCollection extends Collection
{
    public function find($id)
    {
        return $this->first->is($id);
    }
}
