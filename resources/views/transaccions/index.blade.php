@extends('layouts.transaccions')
@section('content')
<div class="container mt-3">
    <h3 class="mb-3">TRANSACCIONES</h3>

    <form method="POST" action="{{ @route('transaccion.store') }}" class="d-inline-flex mb-3" enctype="multipart/form-data">
        @csrf
        <div class="input-group">
            <input type="file" name="transaccion_file" class="form-control" id="inputGroupFile04" aria-describedby="inputGroupFileAddon04" aria-label="Upload" accept=".xls,.xlsx,.csv" required>
            <button class="btn btn-outline-success" type="submit" id="inputGroupFileAddon04">SUBIR</button>
        </div>
    </form>

    <a class="ml-1 mb-3" href="/files/transacciones/plantilla.xls" target="_blank">Descargar plantilla de Ejemplo</a>

    <form class="w-100 d-flex flex-wrap mb-3">
        <div class="form-floating mb-1 mr-2">
            <input type="text" class="form-control" id="numero_de_cuenta" name="numero_de_cuenta" placeholder="" value="{{request('numero_de_cuenta')}}">
            <label for="numero_de_cuenta">Número de Cuenta</label>
        </div>

        <div class="form-floating mb-1 mr-2">
            <input type="text" class="form-control" id="codigo_de_banco" name="codigo_de_banco" placeholder="" value="{{request('codigo_de_banco')}}">
            <label for="codigo_de_banco">Código de Banco</label>
        </div>

        <div class="form-floating mr-2 mb-1">
            <select class="form-select" id="tipo_de_cuenta" name="tipo_de_cuenta" aria-label="Floating label select example">
                <option value="" {{request('tipo_de_cuenta') ? '' : 'selected'}}>-Ninguno-</option>
                <option value="CC" {{request('tipo_de_cuenta')=='CC' ? 'selected' : ''}}>CC</option>
                <option value="CA" {{request('tipo_de_cuenta')=='CA' ? 'selected' : ''}}>CA</option>
                <option value="TJ" {{request('tipo_de_cuenta')=='TJ' ? 'selected' : ''}}>TJ</option>
                <option value="PR" {{request('tipo_de_cuenta')=='PR' ? 'selected' : ''}}>PR</option>
            </select>
            <label for="tipo_de_cuenta">Tipo de Cuenta</label>
        </div>

        <div class="form-floating mb-1 mr-2">
            <input type="text" class="form-control" id="nombre_del_cliente" name="nombre_del_cliente" placeholder="" value="{{request('nombre_del_cliente')}}">
            <label for="nombre_del_cliente">Nombre del Cliente</label>
        </div>

        <div class="form-floating mr-2 mb-1">
            <select class="form-select" id="tipo_de_movimiento" name="tipo_de_movimiento" aria-label="Floating label select example" style="min-width: 200px;">
                <option value="" {{request('tipo_de_movimiento') ? '' : 'selected'}}>-Ninguno-</option>
                <option value="D" {{request('tipo_de_movimiento')=='D' ? 'selected' : ''}}>Débito</option>
                <option value="C" {{request('tipo_de_movimiento')=='C' ? 'selected' : ''}}>Crédito</option>
            </select>
            <label for="tipo_de_movimiento">Tipo de Movimiento</label>
        </div>

        <div class="form-floating mr-2 mb-1">
            <input type="date" class="form-control" id="fecha_desde" placeholder="Ingrese..." name="fecha_desde" value="{{request('fecha_desde')}}">
            <label for="fecha">Desde</label>
        </div>

        <div class="form-floating mr-2 mb-1">
            <input type="date" class="form-control" id="fecha_hasta" placeholder="Ingrese..." name="fecha_hasta" value="{{request('fecha_hasta')}}">
            <label for="fecha_hasta">Hasta</label>
        </div>

        <div class="d-block align-self-center">
            <button type="submit" class="btn btn-primary">Buscar</button>
            <a href="{{route('transaccion.index')}}" class="btn btn-danger">Limpiar</a>
        </div>
        
    </form>

    @if(sizeof($transaccions)>0)
        <div class="w-100 text-end mb-1">
            <a href="{{ @route('transaccion.download', ['numero_de_cuenta' => Request::get('numero_de_cuenta'), 'codigo_de_banco' => Request::get('codigo_de_banco'), 'tipo_de_cuenta' => Request::get('tipo_de_cuenta'), 'nombre_del_cliente' => Request::get('nombre_del_cliente'), 'tipo_de_movimiento' => Request::get('tipo_de_movimiento'), 'fecha_desde' => Request::get('fecha_desde'), 'fecha_hasta' => Request::get('fecha_hasta')]) }}" target="_blank" class="btn btn-success">Descargar</a>
        </div>
    @endif

    <div class="w-100">
        @if($errors->any())
            <div class="alert alert-danger col-12">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>  

    <div class="w-100 table-responsive mb-4">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Número de Cuenta</th>
                    <th>Banco</th>
                    <th>Tipo de Cuenta</th>
                    <th>Cliente</th>
                    <th>Tipo de Mov.</th>
                    <th>Monto</th>
                    <th>Referencia</th>
                    <th>Descripción</th>
                    <th>Email</th>
                    <th>Fax</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaccions as $key => $t)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$t->num_cuenta}}</td>
                    <td>{{$t->codigo_banco}}</td>
                    <td>{{$t->tipo_cuenta}}</td>
                    <td>{{$t->nombre_cliente}}</td>
                    <td>{{$t->tipo_movimiento}}</td>
                    <td>{{$t->monto}}</td>
                    <td>{{$t->referencia}}</td>
                    <td>{{$t->descripcion}}</td>
                    <td>{{$t->email}}</td>
                    <td>{{$t->fax}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $transaccions->links('pagination::bootstrap-5') }}
    </div> 
</div>
@endsection