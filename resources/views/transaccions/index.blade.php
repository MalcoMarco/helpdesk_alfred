@extends('layouts.transaccions')
@section('content')
@php
    $statusClasses = [
        'en proceso' => 'text-bg-primary',
        'procesada' => 'text-bg-success',
        'rechazada' => 'text-bg-danger',
    ];
@endphp

<div class="container-fluid py-3 px-8">
    <h3 class="mb-3 text-center">TRANSACCIONES</h3>
    <hr>

    <div class="d-flex flex-wrap justify-content-between pt-3">
        <form method="POST" action="{{ @route('transaccion.store') }}" class="d-inline-flex" enctype="multipart/form-data">
            @csrf
            <div class="">
                <label for="inputGroupFile04" class="form-label">Seleccione el archivo .xls</label>
                <div class="input-group">
                    <input type="file" name="transaccion_file" class="form-control" id="inputGroupFile04" aria-describedby="inputGroupFileAddon04" aria-label="Upload" accept=".xls,.xlsx,.csv" required>
                    <button class="btn btn-outline-success" type="submit" id="inputGroupFileAddon04">SUBIR</button>
                </div>
            </div>
        </form>
        <a class="ml-1" href="/files/transacciones/plantilla.xls" target="_blank">Descargar plantilla</a>
    </div>
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
    <hr>
    <h4>Filtros: </h4>
    <form class="w-100 d-flex flex-wrap mb-3">

        <div class="form-floating mb-1 mr-2">
            <input type="text" class="form-control" id="codigo_de_banco" name="codigo_de_banco" placeholder="" value="{{request('codigo_de_banco')}}">
            <label for="codigo_de_banco">Código de Banco</label>
        </div>

        <div class="form-floating mb-1 mr-2">
            <input type="text" class="form-control" id="numero_de_cuenta" name="numero_de_cuenta" placeholder="" value="{{request('numero_de_cuenta')}}">
            <label for="numero_de_cuenta">Número de Cuenta</label>
        </div>

        <div class="form-floating mb-1 mr-2">
            <input type="text" class="form-control" id="numero_identificacion" name="numero_identificacion" placeholder="" value="{{request('numero_identificacion')}}">
            <label for="numero_identificacion">Número Identificación</label>
        </div>

        <div class="form-floating mr-2 mb-1">
            <select class="form-select" id="tipo_identificacion" name="tipo_identificacion" aria-label="Floating label select example">
                <option value="" {{request('tipo_identificacion') ? '' : 'selected'}}>-Ninguno-</option>
                <option value="C" {{request('tipo_identificacion')=='C' ? 'selected' : ''}}>C</option>
                <option value="P" {{request('tipo_identificacion')=='P' ? 'selected' : ''}}>P</option>
            </select>
            <label for="tipo_identificacion">Identificación</label>
        </div>

        <div class="form-floating mb-1 mr-2">
            <input type="text" class="form-control" id="nombre_del_cliente" name="nombre_del_cliente" placeholder="" value="{{request('nombre_del_cliente')}}">
            <label for="nombre_del_cliente">Nombre del Cliente</label>
        </div>

        <div class="form-floating mr-2 mb-1">
            <select class="form-select" id="status" name="status" aria-label="Floating label select example" style="min-width: 200px;">
                <option value="" {{request('status') ? '' : 'selected'}}>-Ninguno-</option>
                <option value="en proceso" {{request('status')=='en proceso' ? 'selected' : ''}}>En proceso</option>
                <option value="procesada" {{request('status')=='procesada' ? 'selected' : ''}}>Procesada</option>
                <option value="rechazada" {{request('status')=='rechazada' ? 'selected' : ''}}>Rechazada</option>
            </select>
            <label for="status">Status</label>
        </div>

        <div class="form-floating mr-2 mb-1">
            <input type="date" class="form-control" id="fecha_desde" placeholder="Ingrese..." name="fecha_desde" value="{{request('fecha_desde')}}">
            <label for="fecha">Desde</label>
        </div>

        <div class="form-floating mr-2 mb-1">
            <input type="date" class="form-control" id="fecha_hasta" placeholder="Ingrese..." name="fecha_hasta" value="{{request('fecha_hasta')}}">
            <label for="fecha_hasta">Hasta</label>
        </div>
        <div class="w-100"></div>
        <div class="d-block w-100 text-end">
            <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
            <a href="{{route('transaccion.index')}}" class="btn btn-danger btn-sm">Limpiar</a>
        </div>
        
    </form>
    <hr>
    <h4 class="text-center">Tabla de transacciones</h4>
    @if(sizeof($transaccions)>0)
    <div class="w-100 text-end mb-1">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Exportar
            </button>            
        </div>
    @endif
    <!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Exportar Transacciones</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="{{ @route('transaccion.download')}}" method="GET">
                @csrf
                <input hidden type="text" name="numero_de_cuenta" value="{{Request::get('numero_de_cuenta')}}">
                <input hidden type="text" name="codigo_de_banco" value="{{Request::get('codigo_de_banco')}}">
                <input hidden type="text" name="tipo_de_cuenta" value="{{Request::get('tipo_de_cuenta')}}">
                <input hidden type="text" name="nombre_del_cliente" value="{{Request::get('nombre_del_cliente')}}">
                <input hidden type="text" name="tipo_de_movimiento" value="{{Request::get('tipo_de_movimiento')}}">
                <input hidden type="text" name="fecha_desde" value="{{Request::get('fecha_desde')}}">
                <input hidden type="text" name="fecha_hasta" value="{{Request::get('fecha_hasta')}}">
                <input hidden type="text" name="status" value="{{Request::get('status')}}">
                <label class="form-label">Tipo de Archivo:</label>
                <div class="mb-4">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="type_file" value="xlsx" id="flexRadioDefault1" checked>
                        <label class="form-check-label" for="flexRadioDefault1" > 
                            <img src="/images/svg/excel.png" style="width: 50px; height: 50px">
                             en EXCEL </label>
                    </div>
                    <div class="form-check mb-3 form-check-inline">
                        <input class="form-check-input" type="radio" name="type_file" value="pdf" id="flexRadioDefault2">
                        <label class="form-check-label" for="flexRadioDefault2"> 
                            <img src="/images/svg/pdf.png" style="width: 50px; height: 50px"> 
                            en PDF</label>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="email_to" class="form-label">Enviar por correo a: (opcional)</label>
                    <input type="email" name="email_to" id="email_to" class="form-control" placeholder="email@example.com">
                </div>
                <div class="text-end"><button class="btn btn-success" type="submit" >Exportar</button></div>
            </form>
        </div>
      </div>
    </div>
  </div>

    <div class="w-100 table-responsive mb-4" style="font-size: 14px;">
        <table class="table table-bordered table-sm table-striped align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Código Banco</th>
                    <th>Nro. de Cuenta</th>
                    <th>Nro. Identificación</th>
                    <th>Tipo Identificación</th>
                    <th>Nombre de Cliente.</th>
                    <th>Valor</th>
                    <th>Email</th>
                    <th>Fecha</th>
                    <th>Status</th>
                    <th>created_at</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody class="table-group-divider text-center">
                @foreach($transaccions as $key => $t)
                <tr>
                    <td>{{$transaccions->firstItem() + $key}}</td>
                    <td>{{$t->codigo_banco}}</td>
                    <td>{{$t->num_cuenta}}</td>
                    <td>{{$t->num_ident}}</td>
                    <td>{{$t->tipo_ident}}</td>
                    <td>{{$t->nombre_cliente}}</td>
                    <td>{{$t->valor}}</td>
                    <td>{{$t->email}}</td>
                    <td>{{$t->fecha}}</td>
                    <td>
                        <span class="badge {{ $statusClasses[$t->status] ?? 'text-bg-secondary' }}">
                            {{ ucfirst($t->status) }}
                        </span>
                    </td>
                    <td>{{$t->created_at}}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-secondary btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg style="width: 15px; height: 15px;" fill="currentcolor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 512"><path d="M64 360a56 56 0 1 0 0 112 56 56 0 1 0 0-112zm0-160a56 56 0 1 0 0 112 56 56 0 1 0 0-112zM120 96A56 56 0 1 0 8 96a56 56 0 1 0 112 0z"/></svg>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a href="{{route('transaccion.edit',$t->id)}}" class="dropdown-item">Editar</a></li>
                                <li><hr class="dropdown-divider"></li>

                                <li>
                                    <button class="dropdown-item" type="button" onclick="confirmDelete(this);">Eliminar</button>
                                    <form action="{{ route('transaccion.destroy', $t->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>                               
                                </li>
                            </ul>
                          </div>
                    </td>
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
@section('scripts')
<script>
    function confirmDelete(button) {
        if (confirm('¿Estás seguro de que deseas eliminar esta transacción?')) {
            button.nextElementSibling.submit();
        }
    }
</script>
@endsection