<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
* @OA\Info(
*             title="API para Transacciones", 
*             version="1.0",
*             description="Descripcion"
* )
*
*/

class Controller extends BaseController {
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;
}
