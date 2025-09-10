<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evidencias extends Model
{
    use HasFactory;
    protected $table = 'evidencias';
    protected $fillable = [
        'name',
        'path',
        'original_name',
        'mime_type',
        'size',
        'user_id',
    ];
}
