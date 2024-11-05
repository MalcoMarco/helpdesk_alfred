<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Excel;
use App\Imports\TransaccionsImport;
use App\Models\Transaccion;
use Illuminate\Validation\ValidationException;
use App\Exports\TransaccionsExport;
use Illuminate\Validation\Rule;

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

    public function index(Request $request)
    {
        $this->validate($request, [
            'numero_de_cuenta' => ['nullable', 'string'],
            'codigo_de_banco' => ['nullable', 'string'],
            'tipo_de_cuenta' => ['nullable', Rule::in(['CC', 'CA', 'TJ', 'PR'])],
            'nombre_del_cliente' => ['nullable', 'string'],
            'tipo_de_movimiento' => ['nullable', Rule::in(['D', 'C'])],
            //'monto_de_transaccion' => ['required', 'regex:/^\d{1,15}(\.\d{1,2})?$/'],
            //'numero_de_referencia' => ['nullable', 'string', 'max:10'],
            'fecha_desde' => 'nullable|date_format:Y-m-d',
            'fecha_hasta' => 'nullable|date_format:Y-m-d|after_or_equal:fecha_desde',
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

        if (isset($request->tipo_de_cuenta)) {
            $transaccions = $transaccions->where(function($q) use($request){
                $q->orWhere('tipo_cuenta', 'LIKE', '%'.$request->tipo_de_cuenta.'%');
            });
        }

        if (isset($request->nombre_del_cliente)) {
            $transaccions = $transaccions->where(function($q) use($request){
                $q->orWhere('nombre_cliente', 'LIKE', '%'.$request->nombre_del_cliente.'%');
            });
        }

        if (isset($request->tipo_de_movimiento)) {
            $transaccions = $transaccions->where(function($q) use($request){
                $q->orWhere('tipo_movimiento', 'LIKE', '%'.$request->tipo_de_movimiento.'%');
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

        /*if (isset($request->numero_de_referencia)) {
            $transaccions = $transaccions->where(function($q) use($request){
                $q->orWhere('referencia', 'LIKE', '%'.$request->numero_de_referencia.'%');
            });
        }*/

        $transaccions = $transaccions->paginate(50);

        return view('transaccions.index', compact('transaccions'));
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
                $failure->values(); // The values of the row that has failed.
                $e_m = $failure->errors();
                array_unshift($e_m, 'Error en la fila '.($rowi).'.');
                $errors = [$attribute => $e_m];
            }
            throw ValidationException::withMessages($errors);
        }
        return redirect()->route('transaccion.index');
    }

    public function download(Request $request)
    {
        $this->validate($request, [
            'numero_de_cuenta' => ['nullable', 'string'],
            'codigo_de_banco' => ['nullable', 'string'],
            'tipo_de_cuenta' => ['nullable', Rule::in(['CC', 'CA', 'TJ', 'PR'])],
            'nombre_del_cliente' => ['nullable', 'string'],
            'tipo_de_movimiento' => ['nullable', Rule::in(['D', 'C'])],
            //'monto_de_transaccion' => ['required', 'regex:/^\d{1,15}(\.\d{1,2})?$/'],
            //'numero_de_referencia' => ['nullable', 'string', 'max:10'],
            'fecha_desde' => 'nullable|date_format:Y-m-d',
            'fecha_hasta' => 'nullable|date_format:Y-m-d|after_or_equal:fecha_desde',
        ]);

        $consultas = [
            'numero_de_cuenta' => $request->numero_de_cuenta ?? null,
            'codigo_de_banco' => $request->codigo_de_banco ?? null,
            'tipo_de_cuenta' => $request->tipo_de_cuenta ?? null,
            'nombre_del_cliente' => $request->nombre_del_cliente ?? null,
            'tipo_de_movimiento' => $request->tipo_de_movimiento ?? null,
            'fecha_desde' => $request->fecha_desde ?? null,
            'fecha_hasta' => $request->fecha_hasta ?? null,
        ];

        $export = new TransaccionsExport($consultas);

        return Excel::download($export, 'transacciones.xlsx');
    }

    public function notFound(Request $request)
    {
        return view('errors.404');
    }

}
