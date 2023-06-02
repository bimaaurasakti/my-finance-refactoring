<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $fillable = [
        'history_transaction_id',
        'history_transaction_total',
        'history_ending_balance',
        'history_source_balance',
        'history_type_id',
        'action',
        'history_source_id'
    ];

    protected $primaryKey = 'history_id';
}
