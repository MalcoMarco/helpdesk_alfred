@extends('layouts.transaccions')
@section('content')
@php
    $statusClasses = [
        'SENT' => 'text-bg-primary',
        'PROCESSED' => 'text-bg-success',
        'REJECTED' => 'text-bg-danger',
    ];
@endphp

<div class="container-fluid py-3 px-8">
    <div class="text-end">
        <a class="me-2" href="/files/transacciones/TemplateTransacciones.xls" target="_blank">Descargar plantilla</a>
        <a href="/api/documentation" class="btn-link btn-primary text-end" target="_blank">Swagger APIs</a>
    </div>

    <h4 class="text-center">Tabla de transacciones</h4>

    <div class="w-100 text-end mb-1">
        
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal">
            Exportar <svg style="width: 15px; height: 15px;" fill="currentcolor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 242.7-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7 288 32zM64 352c-35.3 0-64 28.7-64 64l0 32c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-32c0-35.3-28.7-64-64-64l-101.5 0-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352 64 352zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z"/></svg>
        </button> 
        <button type="button" class="btn btn-success" data-bs-toggle="collapse" href="#collapseImport" role="button" aria-expanded="false" aria-controls="collapseImport">
            Importar <svg style="width: 15px; height: 15px;" fill="currentcolor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M288 109.3L288 352c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-242.7-73.4 73.4c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l128-128c12.5-12.5 32.8-12.5 45.3 0l128 128c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L288 109.3zM64 352l128 0c0 35.3 28.7 64 64 64s64-28.7 64-64l128 0c35.3 0 64 28.7 64 64l0 32c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64l0-32c0-35.3 28.7-64 64-64zM432 456a24 24 0 1 0 0-48 24 24 0 1 0 0 48z"/></svg>
        </button>            
    </div>
    <div class="collapse {{ ($errors && $errors->any()) ? 'show' : '' }}" id="collapseImport">
        <div class="d-flex flex-wrap justify-content-center pt-3">
            <form method="POST" action="{{ route('transaccion.store') }}" class="d-inline-flex" enctype="multipart/form-data">
                @csrf
                <div>
                    <label for="inputGroupFile04" class="form-label">Seleccione el archivo a importar [.csv, .xls]</label>
                    <div class="input-group">
                        <input type="file" name="transaccion_file" class="form-control" id="inputGroupFile04" aria-describedby="inputGroupFileAddon04" aria-label="Upload" accept=".xls,.xlsx,.csv" required>
                        <button class="btn btn-outline-success" type="submit" id="inputGroupFileAddon04">SUBIR</button>
                    </div>
                </div>
            </form>
        </div>
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
   
    <div class="mb-2">
        <p class="d-inline-flex gap-1">
        <a class="btn-link btn-success text-decoration-none" data-bs-toggle="collapse" href="#collapseFilter" role="button" aria-expanded="false" aria-controls="collapseFilter">
            Filtros: <svg style="width: 15px; height: 15px;" fill="currentcolor"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z"/></svg>
        </a>
        </p>
        <div class="collapse" id="collapseFilter">
            <div class="card card-body">
                <form class="w-100 d-flex flex-wrap">

                    <div class="form-floating mb-1 me-2">
                        <input type="text" class="form-control form-control-sm" id="codigo_de_banco" name="codigo_de_banco" placeholder="" value="{{request('codigo_de_banco')}}">
                        <label for="codigo_de_banco">Código de Banco</label>
                    </div>
            
                    <div class="form-floating mb-1 me-2">
                        <input type="text" class="form-control form-control-sm" id="numero_de_cuenta" name="numero_de_cuenta" placeholder="" value="{{request('numero_de_cuenta')}}">
                        <label for="numero_de_cuenta">Número de Cuenta</label>
                    </div>
            
                    <div class="form-floating mb-1 me-2">
                        <input type="text" class="form-control form-control-sm" id="numero_identificacion" name="numero_identificacion" placeholder="" value="{{request('numero_identificacion')}}">
                        <label for="numero_identificacion">Número Identificación</label>
                    </div>
            
                    <div class="form-floating me-2 mb-1">
                        <select class="form-select" id="tipo_identificacion" name="tipo_identificacion" aria-label="Floating label select example">
                            <option value="" {{request('tipo_identificacion') ? '' : 'selected'}}>-Ninguno-</option>
                            <option value="C" {{request('tipo_identificacion')=='C' ? 'selected' : ''}}>C</option>
                            <option value="P" {{request('tipo_identificacion')=='P' ? 'selected' : ''}}>P</option>
                        </select>
                        <label for="tipo_identificacion">Identificación</label>
                    </div>
            
                    <div class="form-floating mb-1 me-2">
                        <input type="text" class="form-control form-control-sm" id="nombre_del_cliente" name="nombre_del_cliente" placeholder="" value="{{request('nombre_del_cliente')}}">
                        <label for="nombre_del_cliente">Nombre del Cliente</label>
                    </div>
            
                    <div class="form-floating me-2 mb-1">
                        <select class="form-select" id="status" name="status" aria-label="Floating label select example" style="min-width: 200px;">
                            <option value="" {{request('status') ? '' : 'selected'}}>-Ninguno-</option>
                            @foreach ($transaccionStatus as $item)
                                <option value="{{$item}}" {{request('status')==$item ? 'selected' : ''}}>{{$item}}</option>
                            @endforeach
                        </select>
                        <label for="status">Status</label>
                    </div>
            
                    <div class="form-floating me-2 mb-1">
                        <input type="date" class="form-control form-control-sm" id="fecha_desde" placeholder="Ingrese..." name="fecha_desde" value="{{request('fecha_desde')}}">
                        <label for="fecha">Desde</label>
                    </div>
            
                    <div class="form-floating me-2 mb-1">
                        <input type="date" class="form-control form-control-sm" id="fecha_hasta" placeholder="Ingrese..." name="fecha_hasta" value="{{request('fecha_hasta')}}">
                        <label for="fecha_hasta">Hasta</label>
                    </div>
                    <div class="w-100"></div>
                    <div class="d-block w-100 text-end">
                        <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                        <a href="{{route('transaccion.index')}}" class="btn btn-danger btn-sm">Limpiar</a>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>

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
                        <input type="email" name="email_to" id="email_to" class="form-control form-control-sm" placeholder="email@example.com">
                    </div>
                    <div class="text-end"><button class="btn btn-success" type="submit" >Exportar</button></div>
                </form>
            </div>
        </div>
        </div>
    </div>

    <div style="height: 43px;">
        <div id="dd-btnseleccion" class="dropdown mb-2 d-none">
            <button class="btn btn-dark dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
              seleccionados : <span id="selectedCount"></span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                <li><span class="dropdown-item-text fw-lighter">Cambiar status_report a:</span></li>
                @foreach ($transaccionStatus as $item)
                    <li><button onclick="changeStatusReport('{{$item}}')" class="dropdown-item"><span class="fw-normal">{{$item}}</span></button></li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="w-100 table-responsive mb-4" style="font-size: 12px;">
        <table class="table table-bordered table-sm table-striped align-middle">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"> #</th>
                    <th>withdrawid</th>
                    <th>no_cuenta</th>
                    <th>codigo_banco</th>
                    <th>tipo_cuenta</th>
                    <th>nombre_cliente</th>
                    <th>tipo_movimiento</th>
                    <th>valor_transaccion</th>
                    <th>referencia_transaccion</th>
                    <th>descripcion</th>
                    <th>email_beneficiario</th>
                    <th>tipo_identificacion</th>
                    <th>numero_identificacion</th>
                    <th>status_report</th>
                    <th>date_trasaction</th>
                    <th>transacctionid</th>
                    <th>created_at</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody class="table-group-divider text-center">
                @foreach($transaccions as $key => $t)
                <tr>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input row-checkbox" type="checkbox" data-id="{{$t->id}}" id="cb-{{$t->id}}">
                            <label class="form-check-label" for="cb-{{$t->id}}">
                              {{$transaccions->firstItem() + $key}}
                            </label>
                          </div>

                    </td>
                    <td>{{$t->withdrawid}}</td>
                    <td>{{$t->no_cuenta}}</td>
                    <td>{{$t->codigo_banco}}</td>
                    <td>{{$t->tipo_cuenta}}</td>
                    <td>{{$t->nombre_cliente}}</td>
                    <td>{{$t->tipo_movimiento}}</td>
                    <td>{{$t->valor_transaccion}}</td>
                    <td>{{$t->referencia_transaccion}}</td>
                    <td>{{$t->descripcion_transaccion}} </td>
                    <td>{{$t->email_beneficiario}}</td>
                    <td>{{$t->tipo_identificacion}}</td>
                    <td>{{$t->numero_identificacion}}</td>
                    <td>{{$t->status_report}}</td>
                    <td>{{$t->date_trasaction}}</td>
                    <td>{{$t->transacctionid}}</td>
                    <td>{{$t->created_at}}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-secondary btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg style="width: 15px; height: 15px;" fill="currentcolor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 512"><path d="M64 360a56 56 0 1 0 0 112 56 56 0 1 0 0-112zm0-160a56 56 0 1 0 0 112 56 56 0 1 0 0-112zM120 96A56 56 0 1 0 8 96a56 56 0 1 0 112 0z"/></svg>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{route('transaccion.edit',$t->id)}}" class="dropdown-item text-success">Editar</a>
                                </li>

                                <li>
                                    <button class="dropdown-item text-danger" type="button" onclick="confirmDelete(this);">Eliminar</button>
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
    <div class="d-flex justify-content-between pagination">
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

    // Array para almacenar los IDs seleccionados
    let selectedIds = [];
    const selectedCount = document.getElementById('selectedCount'); // Contador de filas seleccionadas

    // Función para actualizar el contador
    function updateSelectedCount() {
        selectedCount.textContent = `${selectedIds.length}`;
        if (selectedIds.length > 0) {
            document.getElementById('dd-btnseleccion').classList.remove('d-none');
        } else {
            document.getElementById('dd-btnseleccion').classList.add('d-none');
        }
    }
    // Seleccionar o deseleccionar todos los checkboxes
    document.getElementById('selectAll').addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        selectedIds = [];
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
            if (this.checked) {
                selectedIds.push(checkbox.getAttribute('data-id'));
            }
        });
        updateSelectedCount();
    });

    // Manejar clics en checkboxes individuales
    document.querySelectorAll('.row-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const id = this.getAttribute('data-id');
            if (this.checked) {
                selectedIds.push(id);
            } else {
                selectedIds = selectedIds.filter(item => item !== id);
                document.getElementById('selectAll').checked = false;
            }
            updateSelectedCount();
        });
    });

    // Enviar la selección
    function changeStatusReport(status) {
        console.log(status);
        console.log(selectedIds); // Imprimir los IDs seleccionados en la consola
        if (selectedIds.length > 0) {
            fetch("{{route('transaccion.updatestatus')}}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    transaccionIds: selectedIds,
                    status_report:status,
                    _token: "{{ csrf_token() }}",
                    _method: 'POST'
                }),
            })
            .then(response => response.json())
            .then((data) => {
                console.log('Respuesta del servidor:', data)
                window.location.reload();
            })
            .catch(error => console.error('Error:', error));
        } else {
            alert('Por favor, selecciona al menos un elemento.');
        }
    }
    window.onload = function() {
        // Asegurarse de que los checkboxes estén desmarcados al cargar la página
        var checkboxes = document.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = false;
        });
    };
</script>
@endsection