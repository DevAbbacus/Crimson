<?php

namespace App\Enums;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Collections\PlainModelCollection;
use Illuminate\Contracts\Support\Arrayable;

class Plain implements Arrayable
{
    const NAMES = [];

    protected $id;

    public function __construct(int $id)
    {
        if (! static::nameExists($id)) {
            throw new Exception("ID ($id) does not exist.");
        }

        $this->id = $id;
    }

    public static function find($id)
    {
        if (static::nameExists($id)) {
            return new static($id);
        }
    }

    public static function all()
    {
        return PlainModelCollection::make(static::NAMES)->keys()->mapInto(static::class);
    }

    public function id()
    {
        return $this->id;
    }

    public function name()
    {
        return static::NAMES[$this->id];
    }

    public function getForeignKey()
    {
        return Str::snake(class_basename($this)).'_id';
    }

    public function getKey()
    {
        return $this->id;
    }

    public function is($id)
    {
        if ($id instanceof static) {
            $id = $id->id();
        }

        if (is_numeric($id)) {
            return $this->id() === (new static($id))->id();
        }

        return false;
    }

    public function isNot($id)
    {
        return ! $this->is($id);
    }

    protected static function nameExists($id)
    {
        return array_key_exists($id, static::NAMES);
    }

    public function only($keys)
    {
        return Arr::only($this->toArray(), $keys);
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name(),
        ];
    }

    public function __toString()
    {
        return $this->name();
    }
}
