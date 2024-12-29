<?php

namespace App\Exports;

use App\Models\Datatransaccion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class TransaccionsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $consultas;
    protected $transaccionStatus;

    public function __construct(array $consultas, array $transaccionStatus)
    {
        $this->consultas = $consultas;
        $this->transaccionStatus = $transaccionStatus;
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
            'Código de Banco',
            'No. de Cuenta',
            'No. Identificación',
            'Tipo Identificación',
            'Nombre del cliente',
            'Valor de transacción',
            'Email beneficiario',
            'ID',
            'Status',
            'Fecha',
            'F. creación',
        ];
    }

    public function collection()
    {
        $transaccions = Datatransaccion::select(
            'codigo_banco',
            'no_cuenta',
            'numero_identificacion',
            'tipo_identificacion',
            'nombre_cliente',
            'valor_transaccion',
            'email_beneficiario',
            'transacctionid',
            'status_report',
            //DB::raw("CONCAT(UPPER(SUBSTRING(status, 1, 1)), LOWER(SUBSTRING(status, 2))) AS status"),
            'date_trasaction',
            DB::raw("DATE_FORMAT(created_at, '%d/%m/%Y %H:%i:%s') as formatted_date"),

        );

        if (isset($this->consultas['numero_de_cuenta'])) {
            $transaccions = $transaccions->where(function($q){
                $q->orWhere('no_cuenta', 'LIKE', '%'.$this->consultas['numero_de_cuenta'].'%');
            });
        }

        if (isset($this->consultas['codigo_de_banco'])) {
            $transaccions = $transaccions->where(function($q){
                $q->orWhere('codigo_banco', 'LIKE', '%'.$this->consultas['codigo_de_banco'].'%');
            });
        }

        if (isset($this->consultas['numero_identificacion'])) {
            $transaccions = $transaccions->where(function($q) use($request){
                $q->orWhere('numero_identificacion', 'LIKE', '%'.$this->consultas['numero_identificacion'].'%');
            });
        }

        if (isset($this->consultas['tipo_identificacion'])) {
            $transaccions = $transaccions->where(function($q) use($request){
                $q->orWhere('tipo_identificacion', $this->consultas['tipo_identificacion']);
            });
        }

        if (isset($this->consultas['nombre_del_cliente'])) {
            $transaccions = $transaccions->where(function($q){
                $q->orWhere('nombre_cliente', 'LIKE', '%'.$this->consultas['nombre_del_cliente'].'%');
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

        if (isset($this->consultas['status'])) {
            $transaccions = $transaccions->where(function($q){
                $q->orWhere('status_report', $this->consultas['status']);
            });
        }

        $transaccions = $transaccions->get();

        return $transaccions;
    }
}
