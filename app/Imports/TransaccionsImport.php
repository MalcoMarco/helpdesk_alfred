<?php

namespace App\Imports;

use App\Models\Transaccion;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;

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
            /*'*.0' => ['required', 'regex:/^\d{1,34}$/'],//'num_cuenta'
            '*.1' => ['required', 'string', 'max:200'],//'codigo_banco'
            '*.2' => ['required', Rule::in(['CC', 'CA', 'TJ', 'PR'])],//'tipo_cuenta'
            '*.3' => ['required', 'string', 'max:100'],//'nombre_cliente'
            '*.4' => ['required', Rule::in(['D', 'C'])],//'tipo_movimiento'
            '*.5' => ['required', 'regex:/^\d{1,15}(\.\d{1,2})?$/'],// monto
            '*.6' => ['nullable', 'alpha_num:ascii', 'max:15'],// referencia
            '*.7' => ['nullable', 'string', 'max:80'],// descripcion
            '*.8' => ['nullable', 'string', 'regex:/^([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4})(;[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4})*$/'],// email
            '*.9' => ['nullable', 'alpha_num', 'max:100'],// fax*/

            '*.0' => ['required', 'string', 'max:255'],//codigo_banco
            '*.1' => ['required', 'regex:/^\d{1,34}$/'], //num_cuenta
            '*.2' => ['required', 'alpha_num:ascii', 'max:30'], //num_ident
            '*.3' => ['required', Rule::in(['C', 'P'])], //tipo_ident
            '*.4' => ['required', 'string', 'max:100'], //nombre_cliente
            '*.5' => ['required', 'regex:/^\d{1,15}(\.\d{1,2})?$/'], //valor: hasta 15 digitos con 2 decimales
            '*.6' => ['nullable', 'string', 'regex:/^([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4})(;[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4})*$/'],
            '*.7' => ['required', 'date_format:Y-m-d'],
            '*.8' => ['required', Rule::in(['procesada', 'rechazada', 'en proceso'])],
        ];
    }

    public function customValidationAttributes()
    {
        return [
            /*'0' => 'Número de cuenta',
            '1' => 'Código de Banco',
            '2' => 'Tipo de Cuenta',
            '3' => 'Nombre del Cliente',
            '4' => 'Tipo de Movimiento',
            '5' => 'Monto de transacción',
            '6' => 'Número de referencia',
            '7' => 'Descripción',
            '8' => 'Email',
            '9' => 'Fax',*/

            '0' => 'Codigo Banco',
            '1' => 'Número de Cuenta',
            '2' => 'Número Identificación',
            '3' => 'Tipo Identificación',
            '4' => 'Nombre del Cliente',
            '5' => 'Valor Transacción',
            '6' => 'Email Beneficiario',
            '7' => 'Fecha',
            '8' => 'Estado',
         ];
    }

    public function model(array $row)
    {
        return new Transaccion([
            'codigo_banco' => $row[0],
            'num_cuenta' => $row[1],
            'num_ident' => $row[2],
            'tipo_ident' => $row[3],
            'nombre_cliente' => $row[4],
            'valor' => $row[5],
            'email' => $row[6],
            'fecha' => $row[7],
            'status' => strtolower($row[8]),

            /*'num_cuenta' => $row[0],
            'codigo_banco' => $row[1],
            'tipo_cuenta' => $row[2],
            'nombre_cliente' => $row[3],
            'tipo_movimiento' => $row[4],
            'monto' => $row[5],
            'referencia' => $row[6],
            'descripcion' => $row[7],
            'email' => $row[8],
            'fax' => $row[9]*/
        ]);
    }

    public function prepareForValidation($data, $index)
    {
        $data['8'] = strtolower($data['8']); // el status a minusculas
        $data['7'] = is_numeric($data['7']) ? Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data['7']))->format('Y-m-d') : $data['7']; // la fecha si es entera a fecha normal
        
        return $data;
    }

    public function startRow(): int
    {
        return 2;
    }
}
