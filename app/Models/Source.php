<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_name',
        'beginning_balance',
        'source_ending_balance',
        'source_user_id'
    ];

    protected $primaryKey = 'source_id';
}
