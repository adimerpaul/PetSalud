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
            margin-bottom: 10px;
            letter-spacing: .5px;
        }

        .row{ width:100%; clear:both; }
        .col{ float:left; }
        .col-40{ width:40%; }
        .col-60{ width:60%; }

        .bold{ font-weight:700; }
        .muted{ color:#666; }
        .right{ text-align:right; }

        .pet-photo{
            width:78px; height:78px;
            border:1px solid #ddd;
            border-radius:4px;
            overflow:hidden;
            background:#f5f5f5;
            margin-bottom:6px;
        }
        .pet-photo img{ width:100%; height:100%; object-fit:cover; }

        .info-table{ width:100%; border-collapse:collapse; }
        .info-table td{ padding:2px 0; vertical-align:top; }
        .label{ width:95px; font-weight:700; }

        .hr{ border-top:1px solid #ddd; margin:8px 0; }

        table.main{
            width:100%;
            border-collapse:collapse;
            font-size:10.5px;
            margin-top: 8px;
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
        }
        table.main tbody tr:nth-child(even){ background:#f5f6f8; }

        .w-num{ width:40px; }
        .w-money{ width:90px; }
        .clearfix::after{ content:""; display:block; clear:both; }

        .footer{ margin-top:18px; font-size:11px; }
    </style>
</head>
<body>
@php
    $t = $tratamiento;
    $h = $t->historial;
    $m = $h->mascota;
    $vet = $m->veterinaria ?? null;

    $v = fn($x) => ($x === null || $x === '' ? '-' : $x);

    $photoFile = public_path('uploads/' . ($m->photo ?? ''));
    $fallback  = public_path('defaultPet.jpg');

    $imgPath = is_file($photoFile) ? $photoFile : (is_file($fallback) ? $fallback : null);
    $imgData = null;

    if ($imgPath) {
      $ext = strtolower(pathinfo($imgPath, PATHINFO_EXTENSION));
      $mime = $ext === 'png' ? 'image/png' : 'image/jpeg';
      $imgData = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($imgPath));
    }

    $totalProductos = 0;
@endphp

<div class="title">TRATAMIENTO</div>

<div class="row clearfix">
    <div class="col col-40">
        <div class="pet-photo">
            @if($imgData)
                <img src="{{ $imgData }}">
            @endif
        </div>

        <table class="info-table">
            <tr><td class="label">Mascota:</td><td>{{ $v($m->nombre) }}</td></tr>
            <tr><td class="label">Propietario:</td><td>{{ $v($m->propietario_nombre) }}</td></tr>
            <tr><td class="label">Celular:</td><td>{{ $v($m->propietario_celular) }}</td></tr>
            <tr><td class="label">Fecha:</td><td>{{ \Carbon\Carbon::parse($t->fecha ?? now())->format('d/m/Y') }}</td></tr>
            <tr><td class="label">Pagado:</td><td>{{ $t->pagado ? 'Sí' : 'No' }}</td></tr>
        </table>
    </div>

    <div class="col col-60">
        <table class="info-table">
            <tr><td class="label">Comentario:</td><td>{{ $v($t->comentario) }}</td></tr>
            <tr><td class="label">Obs.:</td><td>{{ $v($t->observaciones) }}</td></tr>
        </table>

        <div class="hr"></div>

        <table class="info-table">
            <tr><td class="label">Diagnóstico:</td><td>{{ $v($h->diagnostico) }}</td></tr>
            <tr><td class="label">Anamnesis:</td><td>{{ $v($h->anamnesis) }}</td></tr>
        </table>
    </div>
</div>

<div style="margin-top:12px" class="bold">Productos</div>

<table class="main">
    <thead>
    <tr>
        <th class="w-num">#</th>
        <th>Producto</th>
        <th class="right w-money">Cantidad</th>
        <th class="right w-money">Precio</th>
        <th class="right w-money">Subtotal</th>
    </tr>
    </thead>
    <tbody>
    @forelse($t->productos as $i => $p)
        @php
            $cant = (float)($p->cantidad ?? 0);
            $prec = (float)($p->precio ?? 0);
            $sub  = $cant * $prec;
            $totalProductos += $sub;
        @endphp
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $v($p->producto->nombre ?? '-') }}</td>
            <td class="right">{{ number_format($cant, 2) }}</td>
            <td class="right">{{ number_format($prec, 2) }}</td>
            <td class="right">{{ number_format($sub, 2) }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="muted">Sin productos.</td>
        </tr>
    @endforelse
    </tbody>
</table>

<div class="row clearfix" style="margin-top:8px;">
    <div class="col col-60 muted">
        (Total calculado desde productos)
    </div>
    <div class="col col-40 right bold">
        TOTAL: {{ number_format($totalProductos, 2) }} Bs
    </div>
</div>

<div class="footer">
    <div><b>Veterinario:</b> {{ $t->user->name ?? ($h->user->name ?? '-') }}</div>
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
