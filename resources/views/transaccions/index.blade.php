@extends('layouts.transaccions')
@section('content')
<div class="container mt-3">
    <h3>TRANSACCIONES</h3>

    <form method="POST" action="{{ @route('transaccion.store') }}" class="d-inline-flex mb-4" enctype="multipart/form-data">
        @csrf
        <div class="input-group">
            <input type="file" name="transaccion_file" class="form-control" id="inputGroupFile04" aria-describedby="inputGroupFileAddon04" aria-label="Upload" accept=".xls,.xlsx,.csv" required>
            <button class="btn btn-outline-success" type="submit" id="inputGroupFileAddon04">SUBIR</button>
        </div>
    </form>

    @if(sizeof($transaccions)>0)
        <div class="w-100 text-end mb-1">
            <a href="{{ @route('transaccion.download') }}" target="_blank" class="btn btn-success">Descargar</a>
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
</div>
@endsection