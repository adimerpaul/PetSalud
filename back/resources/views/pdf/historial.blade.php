<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 5px; }
        .table { width:100%; border-collapse: collapse; }
        .table td, .table th { border:1px solid #333; padding:6px; }
        .section { margin-top: 10px; }
    </style>
</head>
<body>

<h2>{{ $historial->mascota->veterinaria->nombre }}</h2>
<p style="text-align:center">
    {{ $historial->mascota->veterinaria->direccion }} |
    {{ $historial->mascota->veterinaria->telefono }}
</p>

<hr>

<b>Mascota:</b> {{ $historial->mascota->nombre }} <br>
<b>Especie:</b> {{ $historial->mascota->especie }} |
<b>Raza:</b> {{ $historial->mascota->raza }} <br>
<b>Propietario:</b> {{ $historial->mascota->propietario_nombre }}

<div class="section">
    <b>Motivo de consulta</b>
    <p>{{ $historial->motivo_consulta }}</p>

    <b>Diagnóstico</b>
    <p>{{ $historial->diagnostico }}</p>
</div>

<div class="section">
    <b>Tratamientos</b>
    <table class="table">
        <thead>
        <tr>
            <th>Medicamento</th>
            <th>Dosis</th>
            <th>Frecuencia</th>
            <th>Duración</th>
        </tr>
        </thead>
        <tbody>
        @foreach($historial->tratamientos as $t)
            <tr>
                <td>{{ $t->medicamento }}</td>
                <td>{{ $t->dosis }}</td>
                <td>{{ $t->frecuencia }}</td>
                <td>{{ $t->duracion }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<p class="section">
    <b>Médico:</b> {{ $historial->user->name }} <br>
    <b>Fecha:</b> {{ $historial->fecha->format('d/m/Y') }}
</p>

</body>
</html>
