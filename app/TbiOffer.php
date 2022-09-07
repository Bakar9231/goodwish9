<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbiOffer extends Model
{
    protected $table = 'tbl_offer';
    protected $primaryKey = 'id';
    use HasFactory;
    protected $fillable = [
        'title',
        'image',
        'description',
    ];
}
