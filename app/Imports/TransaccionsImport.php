<?php

namespace App\Imports;

use App\Models\Transaccion;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\WithStartRow;

class TransaccionsImport implements ToModel, WithValidation, WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function rules(): array
    {
        return [
            /*'*.0' => [
                'required',
                function ($attribute, $value, $onFailure) use($row) {
                    // Validar el formato si codigo_de_banco es 'BHD'
                    if ($row[1] === 'BHD') {
                        if (!preg_match('/^\d{8}-\d{3}-\d{1}$/', $value)) {
                            $onFailure('El formato debe ser ########-###-# para el banco BHD.');
                        }
                    } else {
                        if (strlen($value) > 34){
                            // Validar longitud máxima para otros BANCOS
                            $onFailure('El número de cuenta no puede tener más de 34 dígitos.');
                        }
                        if (!typeof($value) !== 'integer') {
                            $onFailure('El :attribute sólo acepta números.');
                        }
                    }
                },
            ],*/
            '*.0' => ['required', 'regex:/^\d{1,34}$/'],//'num_cuenta'
            '*.1' => ['required', 'string', 'max:200'],//'codigo_banco'
            '*.2' => ['required', Rule::in(['CC', 'CA', 'TJ', 'PR'])],//'tipo_cuenta'
            '*.3' => ['required', 'string', 'max:100'],//'nombre_cliente'
            '*.4' => ['required', Rule::in(['D', 'C'])],//'tipo_movimiento'
            '*.5' => ['required', 'regex:/^\d{1,15}(\.\d{1,2})?$/'],// monto
            '*.6' => ['nullable', 'string', 'max:10'],// referencia
            '*.7' => ['required', 'string', 'max:80'],// descripcion
            '*.8' => ['nullable', 'string', 'regex:/^([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4})(;[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4})*$/'],// email
            '*.9' => ['nullable', 'alpha_num', 'max:100'],// fax
        ];
    }

    public function customValidationAttributes()
    {
        return [
            '0' => 'Número de cuenta',
            '1' => 'Código de Banco',
            '2' => 'Tipo de Cuenta',
            '3' => 'Nombre del Cliente',
            '4' => 'Tipo de Movimiento',
            '5' => 'Monto de transacción',
            '6' => 'Número de referencia',
            '7' => 'Descripción',
            '8' => 'Email',
            '9' => 'Fax',
        ];
    }

    public function model(array $row)
    {
        return new Transaccion([
            'num_cuenta' => $row[0],
            'codigo_banco' => $row[1],
            'tipo_cuenta' => $row[2],
            'nombre_cliente' => $row[3],
            'tipo_movimiento' => $row[4],
            'monto' => $row[5],
            'referencia' => $row[6],
            'descripcion' => $row[7],
            'email' => $row[8],
            'fax' => $row[9]
        ]);
    }

    public function startRow(): int
    {
        return 2;
    }
}
