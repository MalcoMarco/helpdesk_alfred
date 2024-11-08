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
        'tipo_cuenta', //["CC", "CA", "TJ", "PR"]
        'nombre_cliente',
        'tipo_movimiento',// ["D", "C"]
        'monto',
        'referencia',
        'descripcion',
        'email',
        'fax',
        'status',//[1, 2, 3]
    ];
}
