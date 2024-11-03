<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    use HasFactory;

    protected $fillable = [
        'num_cuenta',
        'codigo_banco',
        'tipo_cuenta',
        'nombre_cliente',
        'tipo_movimiento',
        'monto',
        'referencia',
        'descripcion',
        'email',
        'fax'
    ];
}
