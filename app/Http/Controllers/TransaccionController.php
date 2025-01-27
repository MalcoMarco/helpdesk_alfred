<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TransaccionsImport;
use App\Models\Transaccion;
use App\Models\Datatransaccion;
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
        $this->middleware('auth')->except('indexApi', 'storeApi');
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if ($user->role->slug != 'admin') {
                abort(403);
            }
            return $next($request);
        })->except('indexApi', 'storeApi');        
    }
    public $transaccionStatus = ['PROCESSED', 'REJECTED', 'SENT','PENDING'];

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
            'status' => ['nullable', Rule::in($this->transaccionStatus)],
        ]);

        $transaccions = Datatransaccion::orderBy('id');

        if (isset($request->numero_de_cuenta)) {
            $transaccions = $transaccions->where(function($q) use($request){
                $q->orWhere('no_cuenta', 'LIKE', '%'.$request->numero_de_cuenta.'%');
            });
        }

        if (isset($request->codigo_de_banco)) {
            $transaccions = $transaccions->where(function($q) use($request){
                $q->orWhere('codigo_banco', 'LIKE', '%'.$request->codigo_de_banco.'%');
            });
        }

        if (isset($request->numero_identificacion)) {
            $transaccions = $transaccions->where(function($q) use($request){
                $q->orWhere('numero_identificacion', 'LIKE', '%'.$request->numero_identificacion.'%');
            });
        }

        if (isset($request->tipo_identificacion)) {
            $transaccions = $transaccions->where(function($q) use($request){
                $q->orWhere('tipo_identificacion', $request->tipo_identificacion);
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
                $q->orWhere('status_report', $request->status);
            });
        }

        $transaccions = $transaccions->paginate(50);
        $transaccionStatus = $this->transaccionStatus;
        return view('transaccions.index', compact('transaccions','transaccionStatus'));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'transaccion_file' => 'required|file|mimes:xls,xlsx,csv,txt|max:102400',
        ]);
        
        try {
            $import = new TransaccionsImport();
            Excel::import($import, $request->file('transaccion_file'));

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $index => $failure) {        
                $row = $failure->row(); // row that went wrong
                $attribute = $failure->attribute(); // either heading key (if using heading row concern) or column index
                $errorMessages = $failure->errors(); // Actual error messages from Laravel validator
                foreach ($errorMessages as $errorMessage) {
                    $errors[] = "Error en la fila $row, columna $attribute: $errorMessage valor: ".$failure->values()[$index];
                }
            }
            throw ValidationException::withMessages(['transaccion_file' => $errors]);
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
            'status' => ['nullable', Rule::in($this->transaccionStatus)],
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

            $transaccions = Datatransaccion::select(
                'no_cuenta',
                'codigo_banco',
                'numero_identificacion',
                'tipo_identificacion',
                'nombre_cliente',
                'valor_transaccion',
                'email_beneficiario',
                'transacctionid',
                'status_report',
                'date_trasaction',
                DB::raw("DATE_FORMAT(created_at, '%d/%m/%Y %H:%i:%s') as formatted_date"),
            );

            if (isset($consultas['numero_de_cuenta'])) {
                $transaccions = $transaccions->where(function($q) use($consultas){
                    $q->orWhere('no_cuenta', 'LIKE', '%'.$consultas['numero_de_cuenta'].'%');
                });
            }

            if (isset($consultas['codigo_de_banco'])) {
                $transaccions = $transaccions->where(function($q) use($consultas){
                    $q->orWhere('codigo_banco', 'LIKE', '%'.$consultas['codigo_de_banco'].'%');
                });
            }

            if (isset($consultas['numero_identificacion'])) {
                $transaccions = $transaccions->where(function($q) use($consultas){
                    $q->orWhere('numero_identificacion', 'LIKE', '%'.$consultas['numero_identificacion'].'%');
                });
            }

            if (isset($consultas['tipo_identificacion'])) {
                $transaccions = $transaccions->where(function($q) use($consultas){
                    $q->orWhere('tipo_identificacion', $consultas['tipo_identificacion']);
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
                    $q->orWhere('status_report', $consultas['status']);
                });
            }

            $transaccions = $transaccions->get();

            $fecha = date("d-m-Y H:i:s");

            $pdf = Pdf::loadView('transaccions.pdf.reporte_pdf', ['transaccions' => $transaccions, 'fecha' => $fecha])->setPaper('a4', 'landscape');

            $name2 = 'Transacciones_'.$time.'.pdf';
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

    public function destroy(Datatransaccion $transaccion)
    {
        $transaccion->delete();
        return redirect()->route('transaccion.index')->with('success', 'Transacción eliminada correctamente');
    }

    public function edit(Datatransaccion $transaccion)
    {
        $transaccionStatus = $this->transaccionStatus;
        return view('transaccions.edit', compact('transaccion', 'transaccionStatus'));
    }

    public function update(Request $request, Datatransaccion $transaccion)
    {
        $this->validate($request, [
            'withdrawid' => ['required', 'string'],
            'no_cuenta' => ['required', 'string'],
            'codigo_banco' => ['required', 'string'],
            'tipo_cuenta' => ['required', 'string'],
            'nombre_cliente' => ['nullable', 'string'],
            'tipo_movimiento' => ['nullable', 'string'],
            'valor_transaccion' => ['required', 'numeric'],
            'referencia_transaccion' => ['nullable', 'string'],
            'descripcion_transaccion' => ['nullable', 'string'],
            'email_beneficiario' => ['nullable', 'string', 'email'],
            'tipo_identificacion' => ['nullable','string'],
            'numero_identificacion' => ['nullable', 'string'],
            'status_report' => ['required', Rule::in($this->transaccionStatus)],
            'date_trasaction' => ['required', 'date_format:Y-m-d'],
            'transacctionid' => ['required', 'string'],
        ]);
        $transaccion->update($request->except(['_token', '_method']));
        return redirect()->route('transaccion.index')->with('success', 'Transacción actualizada correctamente');
    }

    function updatestatus(Request $request) {
        $this->validate($request, [
            'transaccionIds' => ['required', 'array'],
            //'transaccionIds.*' => ['integer', 'exists:datatransaccions,id'],
            'status_report' => ['required', Rule::in($this->transaccionStatus)],
        ]);
        //actualizar todos los registros con los ids enviados
        Datatransaccion::whereIn('id', $request->transaccionIds)->update(['status_report' => $request->status_report]);
        return response()->json(['message' => 'Transacciones actualizadas correctamente']);
    }

    /**********************APIII********************/

    /**
     * @OA\Get(
     *     path="/api/transacciones",
     *     summary="Obtener lista de transacciones",
     *     description="Devuelve una lista paginada de transacciones basadas en filtros opcionales.",
     *     operationId="getTransacciones",
     *     tags={"Transacciones"},
     *     @OA\Parameter(
     *         name="transacctionid",
     *         in="query",
     *         description="transacctionid (filtro opcional)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="numero_de_cuenta",
     *         in="query",
     *         description="Número de cuenta del cliente (filtro opcional)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="codigo_de_banco",
     *         in="query",
     *         description="Código del banco asociado (filtro opcional)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="nombre_del_cliente",
     *         in="query",
     *         description="Nombre del cliente (filtro opcional)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="numero_identificacion",
     *         in="query",
     *         description="Número de identificación del cliente (filtro opcional)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="tipo_identificacion",
     *         in="query",
     *         description="Tipo de identificación del cliente: 'P' para pasaporte, 'C' para cédula (filtro opcional)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"P", "C"})
     *     ),
     *     @OA\Parameter(
     *         name="fecha_desde",
     *         in="query",
     *         description="Fecha inicial para filtrar transacciones (formato: YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="fecha_hasta",
     *         in="query",
     *         description="Fecha final para filtrar transacciones (formato: YYYY-MM-DD). Debe ser igual o posterior a 'fecha_desde'",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Estado de la transacción: 'procesada', 'rechazada' o 'en proceso' (filtro opcional)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"PROCESSED", "REJECTED", "SENT","PENDING"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de transacciones obtenida correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="transacciones", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="codigo_banco", type="string"),
     *                     @OA\Property(property="no_cuenta", type="string"),
     *                     @OA\Property(property="numero_identificacion", type="string"),
     *                     @OA\Property(property="tipo_identificacion", type="string"),
     *                     @OA\Property(property="nombre_cliente", type="string"),
     *                     @OA\Property(property="valor_transaccion", type="number", format="float"),
     *                     @OA\Property(property="email_beneficiario", type="string"),
     *                     @OA\Property(property="transacctionid", type="string"),
     *                     @OA\Property(property="status_report", type="string"),
     *                     @OA\Property(property="date_trasaction", type="string", format="date-time"),
     *                     @OA\Property(property="created_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación en los parámetros de entrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function indexApi(Request $request)
    {
        $this->validate($request, [
            'numero_de_cuenta' => ['nullable', 'string'],
            'codigo_de_banco' => ['nullable', 'string'],
            'nombre_del_cliente' => ['nullable', 'string'],
            'numero_identificacion' => ['nullable', 'string'],
            'transacctionid' => ['nullable', 'string'],
            'tipo_identificacion' => ['nullable', Rule::in(['P', 'C'])],
            'fecha_desde' => 'nullable|date_format:Y-m-d',
            'fecha_hasta' => 'nullable|date_format:Y-m-d|after_or_equal:fecha_desde',
            'status' => ['nullable', Rule::in($this->transaccionStatus)],
        ]);

        $transaccions = Datatransaccion::select('codigo_banco', 'no_cuenta', 'numero_identificacion', 'tipo_identificacion', 'nombre_cliente', 'valor_transaccion', 'email_beneficiario', 'transacctionid', 'status_report', 'date_trasaction', 'created_at')->where('id', '>=', 1)->orderBy('id');

        if (isset($request->numero_de_cuenta)) {
            $transaccions = $transaccions->where(function($q) use($request){
                $q->orWhere('no_cuenta', 'LIKE', '%'.$request->numero_de_cuenta.'%');
            });
        }

        if (isset($request->codigo_de_banco)) {
            $transaccions = $transaccions->where(function($q) use($request){
                $q->orWhere('codigo_banco', 'LIKE', '%'.$request->codigo_de_banco.'%');
            });
        }

        if (isset($request->numero_identificacion)) {
            $transaccions = $transaccions->where(function($q) use($request){
                $q->orWhere('numero_identificacion', 'LIKE', '%'.$request->numero_identificacion.'%');
            });
        }
        
        if (isset($request->transacctionid)) {
            $transaccions = $transaccions->where(function($q) use($request){
                $q->orWhere('transacctionid', 'LIKE', '%'.$request->transacctionid.'%');
            });
        }

        if (isset($request->tipo_identificacion)) {
            $transaccions = $transaccions->where(function($q) use($request){
                $q->orWhere('tipo_identificacion', $request->tipo_identificacion);
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
            $status = $request->status;
            $transaccions = $transaccions->where(function($q) use($status){
                $q->orWhere('status_report', $status);
            });
        }

        $transaccions = $transaccions->paginate(50);
        //$transaccionStatus = $this->transaccionStatus;
        //return view('transaccions.index', compact('transaccions','transaccionStatus'));
        return response()->json(['transacciones' => $transaccions]);
    }

    /**
     * @OA\Post(
     *     path="/api/transacciones/store",
     *     summary="Importar transacciones desde un archivo",
     *     description="Permite cargar un archivo Excel o CSV que contiene las transacciones para ser procesadas e importadas a la base de datos.",
     *     operationId="importarTransacciones",
     *     tags={"Transacciones"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="transaccion_file",
     *                     type="string",
     *                     format="binary",
     *                     description="Archivo a importar (formatos permitidos: xls, xlsx, csv, txt; tamaño máximo: 100 MB)."
     *                 ),
     *                 required={"transaccion_file"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Archivo procesado e importado correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Importación exitosa.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Errores de validación en el archivo importado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Errores de importación."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 additionalProperties={
     *                     @OA\Property(
     *                         type="array",
     *                         @OA\Items(type="string"),
     *                         example={"Error en la fila 5.", "El campo 'valor' es requerido."}
     *                     )
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en el servidor durante la importación",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Error procesando el archivo.")
     *         )
     *     )
     * )
     */
    public function storeApi(Request $request)
    {
        $this->validate($request,[
            'transaccion_file' => 'required|file|mimes:xls,xlsx,csv,txt|max:102400',
        ]);
        
        try {

            $import = new TransaccionsImport();
            Excel::import($import, $request->file('transaccion_file'));

        }
        catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $index => $failure) {        
                $row = $failure->row(); // row that went wrong
                $attribute = $failure->attribute(); // either heading key (if using heading row concern) or column index
                $errorMessages = $failure->errors(); // Actual error messages from Laravel validator
                foreach ($errorMessages as $errorMessage) {
                    $errors[] = "Error en la fila $row, columna $attribute: $errorMessage valor: ".$failure->values()[$index];
                }
            }
            return response()->json(['message' => 'Errores de importación.', 'errors' => $errors], 422);
        }
        // catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
        //     $failures = $e->failures();
        //     //dd($failures);
        //     $errors = [];
        //     foreach ($failures as $failure) {
        //         $rowi = $failure->row(); // row that went wrong
        //         $attribute = $failure->attribute(); // either heading key (if using heading row concern) or column index
        //         $failure->errors(); // Actual error messages from Laravel validator
        //         //dd($failure->values()); // The values of the row that has failed.
        //         $e_m = $failure->errors();
        //         array_unshift($e_m, 'Error en la fila '.($rowi).'.');
        //         $errors = [$attribute => $e_m];
        //     }
        //     //throw ValidationException::withMessages($errors);

        //     return response()->json(['message' => 'Errores de importación.', 'errors' => $errors], 422);
        // }
        
        //return redirect()->route('transaccion.index')->with('success', 'Transacciones importadas correctamente');
        return response()->json(['message' => 'Importación exitosa.'], 200);
    }

}


