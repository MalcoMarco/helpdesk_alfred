<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TransaccionsImport;
use App\Models\Transaccion;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\Exports\TransaccionsExport;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class TransaccionController extends Controller
{
    
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if ($user->role->slug != 'admin') {
                abort(403);
            }
            return $next($request);
        });        
    }
    public $transaccionStatus = [
        1 => 'En Proceso',
        2 => 'Procesado',
        3 => 'Rechazado',
    ];
    public function index(Request $request)
    {
        $this->validate($request, [
            'numero_de_cuenta' => ['nullable', 'string'],
            'codigo_de_banco' => ['nullable', 'string'],
            'nombre_del_cliente' => ['nullable', 'string'],
            'numero_identificacion' => ['nullable', 'string'],
            'tipo_identificacion' => ['nullable', Rule::in(['P', 'C'])],
            'fecha_desde' => 'nullable|date_format:Y-m-d',
            'fecha_hasta' => 'nullable|date_format:Y-m-d|after_or_equal:fecha_desde',
            'status' => ['nullable', Rule::in(['procesada', 'rechazada', 'en proceso'])],
        ]);

        $transaccions = Transaccion::where('id', '>=', 1);

        if (isset($request->numero_de_cuenta)) {
            $transaccions = $transaccions->where(function($q) use($request){
                $q->orWhere('num_cuenta', 'LIKE', '%'.$request->numero_de_cuenta.'%');
            });
        }

        if (isset($request->codigo_de_banco)) {
            $transaccions = $transaccions->where(function($q) use($request){
                $q->orWhere('codigo_banco', 'LIKE', '%'.$request->codigo_de_banco.'%');
            });
        }

        if (isset($request->numero_identificacion)) {
            $transaccions = $transaccions->where(function($q) use($request){
                $q->orWhere('num_ident', 'LIKE', '%'.$request->numero_identificacion.'%');
            });
        }

        if (isset($request->tipo_identificacion)) {
            $transaccions = $transaccions->where(function($q) use($request){
                $q->orWhere('tipo_ident', $request->tipo_identificacion);
            });
        }

        if (isset($request->nombre_del_cliente)) {
            $transaccions = $transaccions->where(function($q) use($request){
                $q->orWhere('nombre_cliente', 'LIKE', '%'.$request->nombre_del_cliente.'%');
            });
        }

        if (isset($request->fecha_desde)) {
            $transaccions = $transaccions->where(function($q) use($request){
                $q->orWhereDate('created_at', '>=', $request->fecha_desde);
            });
        }

        if (isset($request->fecha_hasta)) {
            $transaccions = $transaccions->where(function($q) use($request){
                $q->orWhereDate('created_at', '<=', $request->fecha_hasta);
            });
        }
        
        if (isset($request->status)) {
            $transaccions = $transaccions->where(function($q) use($request){
                $q->orWhere('status', $request->status);
            });
        }

        $transaccions = $transaccions->paginate(50);
        $transaccionStatus = $this->transaccionStatus;
        return view('transaccions.index', compact('transaccions','transaccionStatus'));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'transaccion_file' => 'required|file|mimes:xls,xlsx,csv|max:102400',
        ]);
        
        try {

            $import = new TransaccionsImport();
            Excel::import($import, $request->file('transaccion_file'));

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            //dd($failures);
            $errors = [];
            foreach ($failures as $failure) {
                $rowi = $failure->row(); // row that went wrong
                $attribute = $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                //dd($failure->values()); // The values of the row that has failed.
                $e_m = $failure->errors();
                array_unshift($e_m, 'Error en la fila '.($rowi).'.');
                $errors = [$attribute => $e_m];
            }
            throw ValidationException::withMessages($errors);
        }
        return redirect()->route('transaccion.index')->with('success', 'Transacciones importadas correctamente');
    }

    public function download(Request $request)
    {
        $this->validate($request, [
            'numero_de_cuenta' => ['nullable', 'string'],
            'codigo_de_banco' => ['nullable', 'string'],
            'nombre_del_cliente' => ['nullable', 'string'],
            'numero_identificacion' => ['nullable', 'string'],
            'tipo_identificacion' => ['nullable', Rule::in(['P', 'C'])],
            'fecha_desde' => 'nullable|date_format:Y-m-d',
            'fecha_hasta' => 'nullable|date_format:Y-m-d|after_or_equal:fecha_desde',
            'status' => ['nullable', Rule::in(['procesada', 'rechazada', 'en proceso'])],
            'type_file' => ['required', Rule::in(['xlsx', 'pdf'])],
            'email_to'=> ['nullable', 'string', 'email'],
        ]);

        $consultas = [
            'numero_de_cuenta' => $request->numero_de_cuenta ?? null,
            'codigo_de_banco' => $request->codigo_de_banco ?? null,
            'numero_identificacion' => $request->numero_identificacion ?? null,
            'nombre_del_cliente' => $request->nombre_del_cliente ?? null,
            'tipo_identificacion' => $request->tipo_identificacion ?? null,
            'fecha_desde' => $request->fecha_desde ?? null,
            'fecha_hasta' => $request->fecha_hasta ?? null,
            'status' => $request->status ?? null,
        ];

        $time = time();
        if($request->type_file == 'xlsx'){
            // se desea exportar a excel y si exsiste $request->email_to se envia el archivo por correo electronico
            $export = new TransaccionsExport($consultas,$this->transaccionStatus);
            
            $name = "transacciones_$time.xlsx";
            if ($request->email_to) {
                $filePath = storage_path('app/public/' . $name);
                Excel::store($export, 'public/' . $name);
    
                Mail::raw('Adjunto encontrará el archivo con la exportación de transacciones solicitada.', function ($message) use ($request, $filePath, $name) {
                    $message->to($request->email_to)
                            ->subject('Exportación de Transacciones')
                            ->attach($filePath, [
                                'as' => $name,
                                'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ]);
                });
                // Eliminar el archivo temporal
                unlink($filePath);
            }
            return Excel::download($export, $name);

        }elseif($request->type_file == 'pdf'){

            $transaccions = Transaccion::select(
                'num_cuenta',
                'codigo_banco',
                'num_ident',
                'tipo_ident',
                'nombre_cliente',
                'valor',
                'email',
                'fecha',
                'status',
                DB::raw("DATE_FORMAT(created_at, '%d/%m/%Y %H:%i:%s') as formatted_date"),
            );

            if (isset($consultas['numero_de_cuenta'])) {
                $transaccions = $transaccions->where(function($q) use($consultas){
                    $q->orWhere('num_cuenta', 'LIKE', '%'.$consultas['numero_de_cuenta'].'%');
                });
            }

            if (isset($consultas['codigo_de_banco'])) {
                $transaccions = $transaccions->where(function($q) use($consultas){
                    $q->orWhere('codigo_banco', 'LIKE', '%'.$consultas['codigo_de_banco'].'%');
                });
            }

            if (isset($consultas['numero_identificacion'])) {
                $transaccions = $transaccions->where(function($q) use($consultas){
                    $q->orWhere('num_ident', 'LIKE', '%'.$consultas['numero_identificacion'].'%');
                });
            }

            if (isset($consultas['tipo_identificacion'])) {
                $transaccions = $transaccions->where(function($q) use($consultas){
                    $q->orWhere('tipo_ident', $consultas['tipo_identificacion']);
                });
            }

            if (isset($consultas['nombre_del_cliente'])) {
                $transaccions = $transaccions->where(function($q) use($consultas){
                    $q->orWhere('nombre_cliente', 'LIKE', '%'.$consultas['nombre_del_cliente'].'%');
                });
            }

            if (isset($consultas['fecha_desde'])) {
                $transaccions = $transaccions->where(function($q) use($consultas) {
                    $q->orWhereDate('created_at', '>=', $consultas['fecha_desde']);
                });
            }

            if (isset($consultas['fecha_hasta'])) {
                $transaccions = $transaccions->where(function($q) use($consultas){
                    $q->orWhereDate('created_at', '<=', $consultas['fecha_hasta']);
                });
            }

            if (isset($consultas['status'])) {
                $transaccions = $transaccions->where(function($q) use($consultas){
                    $q->orWhere('status', $consultas['status']);
                });
            }

            $transaccions = $transaccions->get();

            $fecha = date("d-m-Y H:i:s");

            $pdf = Pdf::loadView('transaccions.pdf.reporte_pdf', ['transaccions' => $transaccions, 'fecha' => $fecha])->setPaper('a4', 'landscape');

            $name2 = 'Reporte Transacciones_'.$time.'.pdf';
            if ($request->email_to) {

                $pdfOutput = $pdf->output();
                $pdfFilePath = storage_path('app/public/'.$name2);
                file_put_contents($pdfFilePath, $pdfOutput); // Guardar temporalmente el PDF
    
                Mail::raw('Adjunto encontrará el archivo con la exportación de transacciones solicitada.', function ($message) use ($request, $pdfFilePath, $name2) {
                    $message->to($request->email_to)
                            ->subject('Exportación de Transacciones (PDF)')
                            ->attach($pdfFilePath, [
                                'as' => $name2,
                                'mime' => 'application/pdf',
                            ]);
                });
                // Eliminar el archivo temporal
                unlink($pdfFilePath);
            }

            return $pdf->download($name2);
        } 

        return;

    }

    public function notFound(Request $request)
    {
        return view('errors.404');
    }

    public function destroy(Transaccion $transaccion)
    {
        $transaccion->delete();
        return redirect()->route('transaccion.index')->with('success', 'Transacción eliminada correctamente');
    }

    public function edit(Transaccion $transaccion)
    {
        $transaccionStatus = $this->transaccionStatus;
        return view('transaccions.edit', compact('transaccion', 'transaccionStatus'));
    }

    public function update(Request $request, Transaccion $transaccion)
    {
        $this->validate($request, [
            'num_cuenta' => ['required', 'regex:/^\d{1,34}$/'],
            'codigo_banco' => ['required', 'string', 'max:200'],
            'tipo_cuenta' => ['required', Rule::in(['CC', 'CA', 'TJ', 'PR'])],
            'nombre_cliente' => ['required', 'string', 'max:100'],
            'tipo_movimiento' => ['required', Rule::in(['D', 'C'])],
            'monto' => ['required', 'regex:/^\d{1,15}(\.\d{1,2})?$/'],
            'referencia' => ['nullable', 'alpha_num:ascii', 'max:15'],
            'descripcion' => ['nullable', 'string', 'max:80'],
            'email' => ['nullable', 'string', 'regex:/^([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4})(;[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4})*$/'],
            'fax' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::in([1, 2, 3])],
        ]);
    
        $transaccion->update($request->except(['_token', '_method']));
        return redirect()->route('transaccion.index')->with('success', 'Transacción actualizada correctamente');
    }

}
