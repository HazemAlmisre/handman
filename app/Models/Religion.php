<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Religion extends Model
{
    use HasFactory;
    protected $table = 'religion';
    protected $fillable = [
        'name'
    ];
    public function scopeList($query)
    {
        return $query->orderBy('updated_at', 'desc');
    }
}
