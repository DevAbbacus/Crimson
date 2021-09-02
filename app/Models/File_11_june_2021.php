<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Exception;
use Log;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'target_id',
        'target_type'
    ];

    public function setPathAttribute($value)
    {
        if (gettype($value) === 'object') {
            try {
                list($usec, $sec) = explode(" ", microtime());
                $filename = str_replace('.', '', ((float)$usec + ((float)$sec))) . '.' . $value->extension();
                $path = '/' . $value->storeAs('public/files', $filename);
            } catch (Exception $e) {
                Log::error('Cant save file -- Omited ');
            }
        } else $path = $value;
        $this->attributes['path'] = $path;
    }

    /**
     * Get the owning target model.
     */
    public function target()
    {
        return $this->morphTo();
    }
}
