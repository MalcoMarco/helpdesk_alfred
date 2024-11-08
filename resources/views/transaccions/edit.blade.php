@extends('layouts.transaccions')
@section('content')
<div class="container py-3 px-5">
    <h3 class="mb-3 text-center">EDITAR TRANSACCIÓN</h3>

    <div class="row justify-content-center">
        <div class="col-lg-9">
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
                                <label for="num_cuenta" class="form-label">Número de Cuenta</label>
                                <input type="text" class="form-control" id="num_cuenta" name="num_cuenta" value="{{ old('num_cuenta', $transaccion->num_cuenta) }}" required>
                            </div>
                    
                            <div class="col-md-4 mb-3">
                                <label for="codigo_banco" class="form-label">Código del Banco</label>
                                <input type="text" class="form-control" id="codigo_banco" name="codigo_banco" value="{{ old('codigo_banco', $transaccion->codigo_banco) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="tipo_cuenta" class="form-label">Tipo de Cuenta</label>
                                <select class="form-select" id="tipo_cuenta" name="tipo_cuenta" required>
                                    <option value="CC" {{ old('tipo_cuenta', $transaccion->tipo_cuenta) == 'CC' ? 'selected' : '' }}>CC: Cuenta Corriente</option>
                                    <option value="CA" {{ old('tipo_cuenta', $transaccion->tipo_cuenta) == 'CA' ? 'selected' : '' }}>CA: Cuenta de Ahorros</option>
                                    <option value="TJ" {{ old('tipo_cuenta', $transaccion->tipo_cuenta) == 'TJ' ? 'selected' : '' }}>TJ: Tarjeta</option>
                                    <option value="PR" {{ old('tipo_cuenta', $transaccion->tipo_cuenta) == 'PR' ? 'selected' : '' }}>PR: Préstamo</option>
                                </select>
                            </div>
                        </div>
                    
                        <div class="mb-3">
                            <label for="nombre_cliente" class="form-label">Nombre del Cliente</label>
                            <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" value="{{ old('nombre_cliente', $transaccion->nombre_cliente) }}" required>
                        </div>
                    
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="tipo_movimiento" class="form-label">Tipo de Movimiento</label>
                                <select class="form-select" id="tipo_movimiento" name="tipo_movimiento" required>
                                    <option value="D" {{ old('tipo_movimiento', $transaccion->tipo_movimiento) == 'D' ? 'selected' : '' }}>D: Débito</option>
                                    <option value="C" {{ old('tipo_movimiento', $transaccion->tipo_movimiento) == 'C' ? 'selected' : '' }}>C: Crédito</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="monto" class="form-label">Monto</label>
                                <input type="number" class="form-control" id="monto" name="monto" value="{{ old('monto', $transaccion->monto) }}" required>
                            </div>
                    
                            <div class="col-md-4 mb-3">
                                <label for="referencia" class="form-label">Referencia</label>
                                <input type="text" class="form-control" id="referencia" name="referencia" value="{{ old('referencia', $transaccion->referencia) }}" required>
                            </div>
                        </div>
                    
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="2" required>{{ old('descripcion', $transaccion->descripcion) }}</textarea>
                        </div>
                    
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" name="email" value="{{ old('email', $transaccion->email) }}" required>
                            </div>
                    
                            <div class="col-md-4 mb-3">
                                <label for="fax" class="form-label">Fax</label>
                                <input type="text" class="form-control" id="fax" name="fax" value="{{ old('fax', $transaccion->fax) }}">
                            </div>
                    
                            <div class="col-md-4 mb-3">
                                <label for="status" class="form-label">Estado</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="1" {{ old('status', $transaccion->status) == 1 ? 'selected' : '' }}>{{$transaccionStatus[1]}} </option>
                                    <option value="2" {{ old('status', $transaccion->status) == 2 ? 'selected' : '' }}>{{$transaccionStatus[2]}}</option>
                                    <option value="3" {{ old('status', $transaccion->status) == 3 ? 'selected' : '' }}>{{$transaccionStatus[3]}}</option>
                                </select>
                            </div>
                        </div>
                    
                        <div class="text-center pt-3">
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