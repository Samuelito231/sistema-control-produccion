@extends('components.panel')

@section('content')
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Mermas - Khaleesitas</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; margin: 0; padding: 20px; background: #fff; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #e7c095; padding-bottom: 15px; }
        .header h1 { font-size: 28px; margin: 0; color: #c29e75; }
        .header p { font-size: 14px; color: #666; }
        .periodo { text-align: center; font-size: 16px; background: #f9f2e6; padding: 8px; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .table th { background: #e7c095; color: #000; padding: 8px; border: 1px solid #c29e75; }
        .table td { border: 1px solid #ddd; padding: 6px; font-size: 12px; }
        .footer { font-size: 10px; text-align: center; margin-top: 30px; color: #999; }
        .metricas { display: flex; justify-content: space-between; margin: 20px 0; }
        .card { border: 1px solid #e7c095; padding: 10px; width: 23%; background: #fef8f0; }
    </style>
</head>
<body>
    <div class="header">
        <!-- Logo opcional: puedes poner tu logo en base64 -->
        <h1>SNACKS NUTRITIVOS PARA MASCOTAS</h1>
        <p>Certificado por Khaleesitas - Alimentos y Bebidas</p>
        <p><strong>Reporte de Control de Merma</strong></p>
    </div>
    <div class="periodo">
        Período: {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}
    </div>

    <div class="metricas">
        <div class="card"><strong>Total merma:</strong> {{ number_format($totalMermaPeriodo, 2) }} kg/uds</div>
        <div class="card"><strong>Costo estimado:</strong> ${{ number_format($costoMermaPeriodo, 2) }}</div>
        <div class="card"><strong>Eficiencia:</strong> {{ $eficienciaPeriodo }}%</div>
        <div class="card"><strong>Lotes cerrados:</strong> {{ $lotesCerradosPeriodo }}</div>
    </div>

    <h3>Detalle de mermas</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Fecha</th><th>Producto</th><th>Cantidad</th><th>Causa</th><th>Tipo</th><th>Lote</th><th>Registrado por</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mermas as $m)
            <tr>
                <td>{{ \Carbon\Carbon::parse($m->fecha)->format('d/m/Y') }}</td>
                <td>{{ $m->producto->nombre ?? 'Producto eliminado' }}</td>
                <td>{{ $m->cantidad }} {{ $m->unidad }}</td>
                <td>{{ $m->causa }}</td>
                <td>{{ ucfirst($m->tipo_merma) }}</td>
                <td>{{ $m->lote ?? '-' }}</td>
                <td>{{ $m->usuario->name ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">Generado el {{ now()->format('d/m/Y H:i:s') }} - Sistema Khaleesitas</div>
</body>
</html>
@endsection
