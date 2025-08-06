<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de Entrega de EPP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .details {
            margin-bottom: 20px;
        }

        .details p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .signatures {
            margin-top: 40px;
            display: table;
            width: 100%;
        }

        .signature {
            display: table-cell;
            text-align: center;
            padding: 20px;
        }

        .signature p {
            margin: 40px 0 0;
        }

        .signature-line {
            border-top: 1px solid black;
            width: 80%;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <h2>Reporte de Entrega de EPP</h2>

    <div class="details">
        <p><strong>Fecha:</strong> {{ $entrega->fecha }}</p>
        <p><strong>Usuario:</strong> {{ $entrega->empleado->first_name . " " . $entrega->empleado->last_name }}</p>
    </div>

    <h4>Listado de EPP Entregados:</h4>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Tipo/Modelo</th>
                <th>Marca</th>
                <th>Cantidad Entregada</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($entrega->filas as $key => $fila)
            <tr>
                <td>{{ $fila->elemento->producto }}</td>
                <td>{{ $fila->elemento->tipo }}</td>
                <td>{{ $fila->elemento->marca }}</td>
                <td>{{ $fila->cantidad }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signatures">
        <div class="signature">
            <div class="signature-line"></div>
            <p>Firma de quien Entrega</p>
        </div>
        <div class="signature">
            <div class="signature-line"></div>
            <p>Firma de quien Recibe</p>
        </div>
    </div>
</body>

</html>