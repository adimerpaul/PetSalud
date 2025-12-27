<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 18px 22px; }
        *{ box-sizing:border-box; }

        body{
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color:#111;
            margin:0;
        }

        .title{
            text-align:center;
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: .5px;
        }

        .row{ width:100%; clear:both; }
        .col{ float:left; }
        .col-40{ width:40%; }
        .col-60{ width:60%; }
        .col-50{ width:50%; }

        .bold{ font-weight:700; }
        .muted{ color:#666; }

        .pet-photo{
            width:78px;
            height:78px;
            border:1px solid #ddd;
            border-radius:4px;
            overflow:hidden;
            background:#f5f5f5;
            margin-bottom:6px;
        }
        .pet-photo img{
            width:100%;
            height:100%;
            object-fit:cover;
        }

        .info-table{
            width:100%;
            border-collapse:collapse;
        }
        .info-table td{
            padding:2px 0;
            vertical-align:top;
        }
        .label{
            width:85px;
            font-weight:700;
        }

        .hr{
            border-top:1px solid #ddd;
            margin:8px 0;
        }

        .vital td{
            padding:2px 6px 2px 0;
            vertical-align:top;
        }

        .section-title{
            font-weight:800;
            font-size:12px;
            margin:12px 0 6px;
        }

        table.main{
            width:100%;
            border-collapse:collapse;
            font-size:10.5px;
        }
        table.main thead th{
            background:#0B66C3;
            color:#fff;
            padding:7px;
            text-align:left;
        }
        table.main tbody td{
            padding:6px;
            border-bottom:1px solid #eee;
            vertical-align: top;
        }
        table.main tbody tr:nth-child(even){
            background:#f5f6f8;
        }

        .w-fecha{ width:90px; }
        .w-costo{ width:90px; text-align:right; }

        /* ✅ lista comprimida de productos */
        .prods{
            font-size: 9.6px;
            line-height: 1.15;
            margin: 0;
            padding: 0;
        }
        .prods div{
            margin: 0;
            padding: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 320px; /* ajusta si quieres más/menos */
        }

        .footer{
            margin-top:20px;
            font-size:11px;
        }

        .clearfix::after{
            content:"";
            display:block;
            clear:both;
        }
    </style>
</head>

<body>
@php
    // ========= FOTO (BASE64) =========
    $photoFile = public_path('uploads/' . ($historial->mascota->photo ?? ''));
    $fallback  = public_path('defaultPet.jpg'); // opcional

    $imgPath = is_file($photoFile) ? $photoFile : (is_file($fallback) ? $fallback : null);
    $imgData = null;

    if ($imgPath) {
        $ext = strtolower(pathinfo($imgPath, PATHINFO_EXTENSION));
        $mime = $ext === 'png' ? 'image/png' : 'image/jpeg';
        $imgData = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($imgPath));
    }

    $m   = $historial->mascota;
    $vet = $m->veterinaria ?? null;

    $v = fn($x) => ($x === null || $x === '' ? '-' : $x);

    // helper: formatear productos comprimidos
    $fmtProds = function($t) {
        // soporta snake_case y camelCase
        $pps = $t->tratamiento_productos ?? $t->tratamientoProductos ?? [];

        if (!$pps || count($pps) === 0) {
            // compatibilidad (si antes usabas medicamento)
            if (!empty($t->medicamento)) return [$t->medicamento];
            return [];
        }

        $out = [];
        foreach ($pps as $pp) {
            $name = $pp->producto->nombre ?? '-';
            $cant = isset($pp->cantidad) ? (float)$pp->cantidad : 0;
            $prec = isset($pp->precio) ? (float)$pp->precio : 0;

            // formato comprimido
            // Ej: "Ankofen 50 ml (1 x 0.00)"
            $out[] = trim($name) . ' (' . rtrim(rtrim(number_format($cant,2,'.',''), '0'), '.') . ' x ' . number_format($prec,2,'.','') . ')';
        }
        return $out;
    };
@endphp

<div class="title">HISTORIAL CLÍNICO</div>

<div class="row clearfix">
    <div class="col col-40">
        <div class="pet-photo">
            @if($imgData)
                <img src="{{ $imgData }}">
            @endif
        </div>

        <table class="info-table">
            <tr><td class="label">Nombre:</td><td>{{ $v($m->nombre) }}</td></tr>
            <tr><td class="label">Dirección:</td><td>{{ $v($m->propietario_direccion) }}</td></tr>
            <tr><td class="label">Especie:</td><td>{{ $v($m->especie) }}</td></tr>
            <tr><td class="label">Edad:</td><td>{{ $v($m->edad) }}</td></tr>
            <tr><td class="label">Peso:</td><td>{{ $v($historial->peso) }} {{ $historial->peso ? 'kg' : '' }}</td></tr>
            <tr><td class="label">Anamnesis:</td><td>{{ $v($historial->anamnesis) }}</td></tr>
            <tr><td class="label">Observaciones:</td><td>{{ $v($historial->observaciones) }}</td></tr>
        </table>
    </div>

    <div class="col col-60">
        <table class="info-table">
            <tr><td class="label">Propietario:</td><td>{{ $v($m->propietario_nombre) }}</td></tr>
            <tr><td class="label">Celular:</td><td>{{ $v($m->propietario_celular) }}</td></tr>
            <tr><td class="label">Raza:</td><td>{{ $v($m->raza) }}</td></tr>
            <tr><td class="label">Sexo:</td><td>{{ $v($m->sexo) }}</td></tr>
            <tr><td class="label">Color:</td><td>{{ $v($m->color) }}</td></tr>
        </table>

        <div class="hr"></div>

        <table class="vital">
            <tr>
                <td class="bold">TR:</td><td>{{ $v($historial->tr) }}</td>
                <td class="bold">FC:</td><td>{{ $v($historial->fc) }}</td>
                <td class="bold">FR:</td><td>{{ $v($historial->fr) }}</td>
                <td class="bold">Pulso:</td><td>{{ $v($historial->pulso) }}</td>
            </tr>
            <tr>
                <td class="bold">TLLC:</td><td>{{ $v($historial->tllc) }}</td>
                <td class="bold">THC:</td><td>{{ $v($historial->thc) }}</td>
                <td class="bold">CF:</td><td>{{ $v($historial->cf) }}</td>
                <td class="bold">Mucosa:</td><td>{{ $v($historial->mucosidad) }}</td>
            </tr>
        </table>

        <div style="margin-top:4px">
            <span class="bold">VACUNAS:</span>
            Parvo: {{ $v($historial->parvo) }} ·
            Hexa: {{ $v($historial->hexa) }} ·
            Octa: {{ $v($historial->octa) }} ·
            Rábica: {{ $v($historial->rabica) }} ·
            Triple: {{ $v($historial->triple) }}
        </div>

        <table style="margin-top:6px">
            <tr><td class="label">Esterilizado:</td><td>{{ $v($historial->esterilizado) }}</td></tr>
            <tr><td class="label">Desparasitación:</td><td>{{ $v($historial->desparasitacion) }}</td></tr>
            <tr><td class="label">Ecografía:</td><td>{{ $v($historial->ecografia) }}</td></tr>
            <tr><td class="label">Rayos X:</td><td>{{ $v($historial->rayox) }}</td></tr>
            <tr><td class="label">Laboratorio:</td><td>{{ $v($historial->laboratorio) }}</td></tr>
            <tr><td class="label">Pronóstico:</td><td>{{ $v($historial->pronostico) }}</td></tr>
            <tr><td class="label">Diagnóstico:</td><td>{{ $v($historial->diagnostico) }}</td></tr>
        </table>
    </div>
</div>

<div class="section-title">Tratamientos Realizados</div>

<table class="main">
    <thead>
    <tr>
        <th class="w-fecha">Fecha</th>
        <th>Observaciones</th>
        <th>Comentario</th>
        <th>Productos</th>
        <th class="w-costo">Costo</th>
    </tr>
    </thead>
    <tbody>
    @forelse($historial->tratamientos as $t)
        @php
            $lines = $fmtProds($t);
        @endphp
        <tr>
            <td>{{ \Carbon\Carbon::parse($t->fecha ?? $historial->fecha)->format('d/m/Y') }}</td>
            <td>{{ $v($t->observaciones ?? $t->indicaciones) }}</td>
            <td>{{ $v($t->comentario) }}</td>
            <td>
                @if(count($lines))
                    <div class="prods">
                        @foreach($lines as $ln)
                            <div>• {{ $ln }}</div>
                        @endforeach
                    </div>
                @else
                    <span class="muted">-</span>
                @endif
            </td>
            <td class="w-costo">
                {{ $t->costo !== null ? number_format((float)$t->costo,2).' Bs' : '-' }}
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="muted">Sin tratamientos registrados.</td>
        </tr>
    @endforelse
    </tbody>
</table>

<div class="footer">
    <div><b>Veterinario:</b> {{ $historial->user->name }}</div>
    @if($vet)
        <div class="muted">
            {{ $vet->nombre }}
            @if($vet->direccion) · {{ $vet->direccion }} @endif
            @if($vet->telefono) · {{ $vet->telefono }} @endif
        </div>
    @endif
</div>

</body>
</html>
