<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Entregas</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 40px;
            font-size: 12px;
            color: #2d3748;
            background-color: #fff;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #4caf50;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }

        .header img {
            width: 80px;
            height: auto;
        }

        .header h1 {
            margin: 8px 0 0;
            font-size: 22px;
            color: #2e7d32;
            letter-spacing: 0.5px;
        }

        .sub-header {
            text-align: center;
            font-size: 13px;
            color: #555;
            margin-top: 4px;
        }

        .date {
            text-align: right;
            font-size: 12px;
            color: #666;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            border: 1px solid #ccc;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: center;
        }

        th {
            background-color: #e8f5e9;
            color: #1b5e20;
            font-weight: bold;
            font-size: 13px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f8e9;
        }

        td {
            font-size: 12px;
        }

        .status {
            font-weight: bold;
            border-radius: 4px;
            padding: 2px 6px;
            display: inline-block;
        }
        .status-entregada { color: #2e7d32; }
        .status-pendiente { color: #f9a825; }
        .status-fallida { color: #c62828; }

        .footer {
            text-align: center;
            font-size: 11px;
            color: #777;
            margin-top: 35px;
            border-top: 1px solid #ccc;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('img/logo.png') }}" alt="Logo">
        <h1>Reporte de Entregas</h1>
        <div class="sub-header">Logistic Service — División Agroindustrial</div>
    </div>

    <div class="date">
        Reporte generado el {{ now()->format('d/m/Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Viaje</th>
                <th>Cliente</th>
                <th>Dirección</th>
                <th>Estado</th>
                <th>Creado</th>
                <th>Actualizado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($deliveries as $d)
            <tr>
                <td>{{ $d->id }}</td>
                <td>{{ $d->trip_id }}</td>
                <td>{{ $d->customer_name }}</td>
                <td>{{ $d->delivery_address }}</td>
                <td>
                    @php
                        $statusClass = match(strtolower($d->status)) {
                            'entregada' => 'status-entregada',
                            'pendiente' => 'status-pendiente',
                            'fallida' => 'status-fallida',
                            default => ''
                        };
                    @endphp
                    <span class="status {{ $statusClass }}">{{ ucfirst($d->status) }}</span>
                </td>
                <td>{{ $d->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $d->updated_at->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        © {{ date('Y') }} Logistic Service — Reporte generado automáticamente.
    </div>

</body>
</html>
