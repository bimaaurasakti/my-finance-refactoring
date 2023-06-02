<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    use HasFactory;

    protected $fillable = [
        'transaction_user_id',
        'transaction_source_id',
        'transaction_type_id',
        'transaction_date',
        'transaction_total',
        'transaction_description',
    ];

    protected $primaryKey = 'transaction_id';

    public function types() {
        return $this->belongsTo(Type::class, 'transaction_type_id');
    }
}
