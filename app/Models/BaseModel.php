<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $searcheable = [];
    protected $relationships = [];

    function scopeSearch($query, $search = '')
    {
        $condition = !!$search && count($this->searcheable) > 0;
        $query->when($condition, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                foreach ($this->searcheable as $i => $column) {
                    if ($i === 0)
                        $query->where($column, 'like', "%$search%");
                    else
                        $query->orwhere($column, 'like', "%$search%");
                }
            });
        });
    }

    function scopeBlanks($query, $columns = [])
    {
        $condition = count($columns) > 0;
        $query->when($condition, function ($q) use ($columns) {
            foreach ($columns as $column) {
                $q->where(function ($q) use ($column) {
                    $q->whereNull($column)
                        ->orwhere($column, '=', '');
                });
            }
        });
    }

    static function list($params = [])
    {
        $inst = new static;
        $search = isset($params['search']) ? $params['search'] : '';
        $blanks = isset($params['blanks']) ? $params['blanks'] : [];
        $sort = isset($params['sort']) ? $params['sort'] : null;
        $query = self::with($inst->relationships)
            ->search($search)
            ->blanks($blanks);

        if ($sort) {
            $column = isset($sort['column']) ? $sort['column'] : null;
            $dir = isset($sort['dir']) ? $sort['dir'] : 'asc';
            if ($column)
                $query = $query->orderBy($column, $dir);
        }

        return $query;
    }
}
