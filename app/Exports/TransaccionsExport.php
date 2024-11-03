<?php

namespace App\Exports;

use App\Models\Transaccion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransaccionsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function styles(Worksheet $sheet)
    {
        return [
            1  => ['font' => ['bold' => true]],
        ];
    }

     public function headings(): array
    {
        return [
            'No. de Cuenta',
            'Código de Banco',
            'Tipo de Cuenta',
            'Nombre del cliente',
            'Tipo de movimiento',
            'Valor de transacción',
            'Referencia de transacción',
            'Descripción de transacción',
            'Email beneficiario',
            'Fax',
        ];
    }

    public function collection()
    {
        return Transaccion::select(
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
        )->get();
    }
}
