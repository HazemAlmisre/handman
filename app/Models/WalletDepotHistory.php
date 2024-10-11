<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletDepotHistory extends Model
{
    use HasFactory;
    protected $table = 'depotwallet_history';
    protected $fillable = [
        'datetime', 'user_id','amount_before', 'balance_adjustment', 'activity_message', 'activity_type'
    ];

    protected $casts = [
        'user_id'   => 'integer',
    ];

    public function depot(){
        return $this->belongsTo(User::class, 'user_id','id');
    }

    public function wallet(){
        return $this->belongsTo(Wallet::class, 'user_id','user_id');
    }
}
