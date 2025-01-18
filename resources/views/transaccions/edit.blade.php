@extends('layouts.transaccions')
@section('content')
<div class="container py-3 px-5">
    <h3 class="mb-3 text-center">EDITAR TRANSACCIÓN</h3>
<p>
    <a href="{{route('transaccion.index')}}">Volver</a>
</p>
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="card">
                <div class="card-body">
                    <div class="w-100 mt-3">
                        @if (session('success'))
                            <div class="alert alert-success col-12">
                                {{ session('success') }}
                            </div>            
                        @endif
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
                    <form method="POST" action="{{ @route('transaccion.update', $transaccion->id) }}">
                        @csrf
                        @method('PUT')                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="withdrawid" class="form-label">withdrawid</label>
                            <input type="number" class="form-control" id="withdrawid" name="withdrawid" value="{{ $transaccion->withdrawid}}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="no_cuenta" class="form-label">no_cuenta</label>
                            <input type="text" class="form-control" id="no_cuenta" name="no_cuenta" value="{{ $transaccion->no_cuenta }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="codigo_banco" class="form-label">codigo_banco</label>
                            <input type="text" class="form-control" id="codigo_banco" name="codigo_banco" value="{{ $transaccion->codigo_banco }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="tipo_cuenta" class="form-label">tipo_cuenta</label>
                            <input type="text" class="form-control" id="tipo_cuenta" name="tipo_cuenta" value="{{ $transaccion->tipo_cuenta }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="nombre_cliente" class="form-label">nombre_cliente</label>
                            <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" value="{{ $transaccion->nombre_cliente }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="tipo_movimiento" class="form-label">tipo_movimiento</label>
                            <input type="text" class="form-control" id="tipo_movimiento" name="tipo_movimiento" value="{{ $transaccion->tipo_movimiento }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="valor_transaccion" class="form-label">valor_transaccion</label>
                            <input type="number" class="form-control" id="valor_transaccion" name="valor_transaccion" value="{{ $transaccion->valor_transaccion }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="referencia_transaccion" class="form-label">referencia_transaccion</label>
                            <input type="text" class="form-control" id="referencia_transaccion" name="referencia_transaccion" value="{{ $transaccion->referencia_transaccion }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="descripcion_transaccion" class="form-label">descripcion_transaccion</label>
                            <input type="text" class="form-control" id="descripcion_transaccion" name="descripcion_transaccion" value="{{ $transaccion->descripcion_transaccion }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="email_beneficiario" class="form-label">email_beneficiario</label>
                            <input type="email" class="form-control" id="email_beneficiario" name="email_beneficiario" value="{{ $transaccion->email_beneficiario }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="tipo_identificacion" class="form-label">tipo_identificacion</label>
                            <input type="text" class="form-control" id="tipo_identificacion" name="tipo_identificacion" value="{{ $transaccion->tipo_identificacion }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="numero_identificacion" class="form-label">numero_identificacion</label>
                            <input type="text" class="form-control" id="numero_identificacion" name="numero_identificacion" value="{{ $transaccion->numero_identificacion }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="status_report" class="form-label">status_report</label>
                            <select class="form-select" id="status_report" name="status_report" required>
                                @foreach ($transaccionStatus as $item)
                                    <option value="{{$item}}" {{  $transaccion->status_report == $item ? 'selected' : '' }}>{{$item}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="date_trasaction" class="form-label">date_trasaction</label>
                            <input type="date" class="form-control" id="date_trasaction" name="date_trasaction" value="{{ $transaccion->date_trasaction }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="transacctionid" class="form-label">transacctionid</label>
                            <input type="text" class="form-control" id="transacctionid" name="transacctionid" value="{{ $transaccion->transacctionid }}">
                        </div>
                    </div>
                    
                        <div class="text-center pt-3">
                            <a href="{{route('transaccion.index')}}" class="btn btn-dark">Cancelar</a>

                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@endsection