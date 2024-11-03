<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Excel;
use App\Imports\TransaccionsImport;
use App\Models\Transaccion;
use Illuminate\Validation\ValidationException;
use App\Exports\TransaccionsExport;

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
        $transaccions = Transaccion::all();

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
        return Excel::download(new TransaccionsExport, 'transacciones.xlsx');
    }

    public function notFound(Request $request)
    {
        return view('errors.404');
    }

}
