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
                                <label for="codigo_banco" class="form-label">Código del Banco</label>
                                <input type="text" class="form-control" id="codigo_banco" name="codigo_banco" value="{{ old('codigo_banco', $transaccion->codigo_banco) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="num_cuenta" class="form-label">Número de Cuenta</label>
                                <input type="text" class="form-control" id="num_cuenta" name="num_cuenta" value="{{ old('num_cuenta', $transaccion->num_cuenta) }}" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="num_ident" class="form-label">Número de Identificación</label>
                                <input type="text" class="form-control" id="num_ident" name="num_ident" value="{{ old('num_ident', $transaccion->num_ident) }}" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="tipo_ident" class="form-label">Tipo de Identificación</label>
                                <select class="form-select" id="tipo_ident" name="tipo_ident" required>
                                    <option value="C" {{ old('tipo_ident', $transaccion->tipo_ident) == 'C' ? 'selected' : '' }}>C</option>
                                    <option value="P" {{ old('tipo_ident', $transaccion->tipo_ident) == 'P' ? 'selected' : '' }}>P</option>
                                </select>
                            </div>

                            <div class="mb-3 col-md-8">
                                <label for="nombre_cliente" class="form-label">Nombre del Cliente</label>
                                <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" value="{{ old('nombre_cliente', $transaccion->nombre_cliente) }}" required>
                            </div>
                        </div>
                    
                        
                    
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="valor" class="form-label">Valor</label>
                                <input type="number" class="form-control" id="valor" name="valor" value="{{ old('valor', $transaccion->valor) }}" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" name="email" value="{{ old('email', $transaccion->email) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="fecha" class="form-label">Fecha</label>
                                <input type="date" class="form-control" id="fecha" name="fecha" value="{{ old('fecha', $transaccion->fecha) }}" required>
                            </div>

                        </div>
                    
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="id" class="form-label">ID</label>
                                <input type="number" class="form-control" id="id" name="id" value="{{ old('id', $transaccion->id_t) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="status" class="form-label">Estado</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="SENT" {{ old('status', $transaccion->status) == 'SENT' ? 'selected' : '' }}>SENT</option>
                                    <option value="PROCESSED" {{ old('status', $transaccion->status) == 'PROCESSED' ? 'selected' : '' }}>PROCESSED</option>
                                    <option value="REJECTED" {{ old('status', $transaccion->status) == 'REJECTED' ? 'selected' : '' }}>REJECTED</option>
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