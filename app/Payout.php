<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $table = 'Payout';
    protected $primaryKey = 'id';
    use HasFactory;
    protected $fillable = [
        'user_id',
        'amount',
    ];
}
