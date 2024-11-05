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
    protected $consultas;

    public function __construct(array $consultas)
    {
        $this->consultas = $consultas;
    }

    public function array(): array
    {
        return $this->invoices;
    }

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
        $transaccions = Transaccion::select(
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
        );

        if (isset($this->consultas['numero_de_cuenta'])) {
            $transaccions = $transaccions->where(function($q){
                $q->orWhere('num_cuenta', 'LIKE', '%'.$this->consultas['numero_de_cuenta'].'%');
            });
        }

        if (isset($this->consultas['codigo_de_banco'])) {
            $transaccions = $transaccions->where(function($q){
                $q->orWhere('codigo_banco', 'LIKE', '%'.$this->consultas['codigo_de_banco'].'%');
            });
        }

        if (isset($this->consultas['tipo_de_cuenta'])) {
            $transaccions = $transaccions->where(function($q){
                $q->orWhere('tipo_cuenta', 'LIKE', '%'.$this->consultas['tipo_de_cuenta'].'%');
            });
        }

        if (isset($this->consultas['nombre_del_cliente'])) {
            $transaccions = $transaccions->where(function($q){
                $q->orWhere('nombre_cliente', 'LIKE', '%'.$this->consultas['nombre_del_cliente'].'%');
            });
        }

        if (isset($this->consultas['tipo_de_movimiento'])) {
            $transaccions = $transaccions->where(function($q){
                $q->orWhere('tipo_movimiento', 'LIKE', '%'.$this->consultas['tipo_de_movimiento'].'%');
            });
        }

        if (isset($this->consultas['fecha_desde'])) {
            $transaccions = $transaccions->where(function($q) {
                $q->orWhereDate('created_at', '>=', $this->consultas['fecha_desde']);
            });
        }

        if (isset($this->consultas['fecha_hasta'])) {
            $transaccions = $transaccions->where(function($q){
                $q->orWhereDate('created_at', '<=', $this->consultas['fecha_hasta']);
            });
        }

        $transaccions = $transaccions->get();

        return $transaccions;
    }
}
