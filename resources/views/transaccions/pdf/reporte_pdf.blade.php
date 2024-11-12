<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Reporte Transacciones</title>

	<style>
		.fwb{
			font-weight: bold;
		}

		@page {
            margin: 100px 25px; /* Margen superior para dejar espacio al encabezado */
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            position: fixed;
            top: -80px; /* Ajusta la posición para que esté dentro del margen superior */
            left: 0;
            right: 0;
            height: 60px;
            font-size: 12px;
            text-align: right;
            margin-bottom: 20px;
            padding: 0 25px;
            background-color: #fff; /* Fondo blanco para evitar solapamientos */
        }
        .header p {
            margin: 0;
        }
        .title {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
            padding-top: 20px;
        }
        .info-table, .transaction-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
            word-break: break-word;
        }
        .info-table th, .transaction-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
        }
        .info-table td, .transaction-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .transaction-table th, .transaction-table td {
            text-align: left;
            font-size: 10px;
        }
        .amount {
            text-align: right;
        }
        .footer {
            position: fixed;
            bottom: -30px; /* Ajusta según sea necesario */
            left: 0;
            right: 0;
            height: 30px;
            text-align: center;
            font-size: 10px;
            color: #888;
        }
        /* Fuerza el pie de página en cada página en dompdf */
        .page-break {
            page-break-after: always;
        }
	</style>
</head>

<body>
	{{-- EL HEADER Y FOOTER SE REPITE EN CADA PAGINA --}}
	<div class="header">
        <p>Fecha: {{$fecha}}</p>
        {{-- <p>Usuario: <b>{{Auth::user()->first_name}} {{Auth::user()->last_name}}</b></p> --}}
    </div>

    <div class="footer">
	    {{-- FOOTER DE TRANSACCIONES --}}
	</div>

	<div class="container">
	    <div class="title">
	        HISTÓRICO DE TRANSACCIONES
	    </div>

	    <table class="transaction-table">
	        <tr>
	            <th>No. Cuenta</th>
	            <th>Código de Banco</th>
	            <th>Tipo de Cuenta</th>
	            <th>Nombre del Cliente</th>
	            <th>Tipo de movimiento</th>
	            <th class="amount">Monto</th>
	            <th>Referencia</th>
	            <th>Descripción</th>
	            <th>Email</th>
	            <th>Fax</th>
	            <th>Status</th>
	            <th>Creación</th>
	        </tr>

	        <!-- BODY -->
	        @foreach($transaccions as $key => $t)
	        <tr>
	            <td>{{$t->num_cuenta}}</td>
	            <td>{{$t->codigo_banco}}</td>
	            <td>{{$t->tipo_cuenta}}</td>
	            <td>{{$t->nombre_cliente}}</td>
	            <td>{{$t->tipo_movimiento}}</td>
	            <td class="amount">{{$t->monto}}</td>
	            <td>{{$t->referencia}}</td>
	            <td>{{$t->descripcion}}</td>
	            <td>{{$t->email}}</td>
	            <td>{{$t->fax}}</td>
	            <td>{{$t->status2}}</td>
	            <td>{{$t->formatted_date}}</td>
	        </tr>
	        @endforeach	       
	    </table>


	</div>
	
	
</body>

</html>