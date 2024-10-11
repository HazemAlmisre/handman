<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Tax extends Model
{
    use HasFactory;
    protected $table = 'taxes';
    protected $fillable = [
        'title', 'type', 'value', 'status'
    ];
    protected $casts = [
        'value' => 'integer',
        'status' => 'integer',
    ];
    public function scopeList($query)
    {
        return $query->orderBy('updated_at', 'desc');
    }
}
