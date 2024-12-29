<?php

namespace App\Imports;

use App\Models\Datatransaccion;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class TransaccionsImport implements ToModel, WithValidation, WithStartRow, SkipsEmptyRows
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function rules(): array
    {
        return [
            '*.0' => ['required', Rule::unique('datatransaccions', 'withdrawid')], // withdrawid
            '*.1' => 'required', // no_cuenta
            '*.2' => 'required', // codigo_banco
            '*.3' => ['required'], // tipo_cuenta
            '*.4' => 'required', // nombre_cliente
            '*.5' => 'required', // tipo_movimiento
            '*.6' => 'required', // valor_transaccion
            '*.7' => 'nullable', // referencia_transaccion
            '*.8' => 'nullable', // descripcion_transaccion
            '*.9' => 'nullable', // email_beneficiario
            '*.10' => ['required'], // tipo_identificacion    Rule::in(['C', 'P'])]
            '*.11' => 'required', // numero_identificacion
            '*.12' => ['required'], // status_report  , Rule::in(['PROCESSED', 'REJECTED', 'SENT'])
            '*.13' => 'required', // date_trasaction
            '*.14' => ['required', Rule::unique('datatransaccions', 'transacctionid')], // transacctionid
        ];
    }

    public function model(array $row)
    {
        return new Datatransaccion([
            'withdrawid' => $row[0],
            'no_cuenta' => $row[1],
            'codigo_banco' => $row[2],
            'tipo_cuenta' => $row[3],
            'nombre_cliente' => $row[4],
            'tipo_movimiento' => $row[5],
            'valor_transaccion' => $row[6],
            'referencia_transaccion' => $row[7],
            'descripcion_transaccion' => $row[8],
            'email_beneficiario' => $row[9],
            'tipo_identificacion' => $row[10],
            'numero_identificacion' => $row[11],
            'status_report' => $row[12],
            'date_trasaction' => Carbon::parse($row[13]),
            'transacctionid' => $row[14],
        ]);
    }

    public function customValidationAttributes()
    {
        return [
            '0' => 'withdrawid',
            '1' => 'no_cuenta',
            '2' => 'codigo_banco',
            '3' => 'tipo_cuenta',
            '4' => 'nombre_cliente',
            '5' => 'tipo_movimiento',
            '6' => 'valor_transaccion',
            '7' => 'referencia_transaccion',
            '8' => 'descripcion_transaccion',
            '9' => 'email_beneficiario',
            '10' => 'tipo_identificacion',
            '11' => 'numero_identificacion',
            '12' => 'status_report',
            '13' => 'date_trasaction',
            '14' => 'transacctionid',
        ];
    }

    public function startRow(): int
    {
        return 2;
    }
}
