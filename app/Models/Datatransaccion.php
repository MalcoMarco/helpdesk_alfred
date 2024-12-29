<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Datatransaccion extends Model
{
    use HasFactory;
    protected $fillable = [
        'withdrawid',
        'no_cuenta',
        'codigo_banco',
        'tipo_cuenta',
        'nombre_cliente',
        'tipo_movimiento',
        'valor_transaccion',
        'referencia_transaccion',
        'descripcion_transaccion',
        'email_beneficiario',
        'tipo_identificacion',
        'numero_identificacion',
        'status_report',
        'date_trasaction',
        'transacctionid',
    ];

}
