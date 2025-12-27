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
        .col-35{ width:35%; }
        .col-65{ width:65%; }
        .col-50{ width:50%; }

        .bold{ font-weight:700; }
        .muted{ color:#666; }

        .box{
            border:1px solid #e6e6e6;
            border-radius:6px;
            padding:10px;
            margin-bottom:10px;
        }

        .pet-photo{
            width:96px;
            height:96px;
            border:1px solid #ddd;
            border-radius:6px;
            overflow:hidden;
            background:#f5f5f5;
            margin-bottom:8px;
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
            padding:3px 0;
            vertical-align:top;
        }
        .label{
            width:120px;
            font-weight:700;
        }

        .hr{
            border-top:1px solid #ddd;
            margin:10px 0;
        }

        .badge{
            display:inline-block;
            padding:3px 8px;
            border-radius:999px;
            font-size:10px;
            background:#eef5ff;
            color:#0B66C3;
            font-weight:700;
        }

        .footer{
            margin-top:14px;
            font-size:10.5px;
            color:#444;
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
    $m = $mascota;
    $vet = $m->veterinaria ?? null;

    $v = fn($x) => ($x === null || $x === '' ? '-' : $x);

    // ========= FOTO (BASE64) =========
    $photoFile = public_path('uploads/' . ($m->photo ?? ''));
    $fallback  = public_path('defaultPet.jpg');

    $imgPath = is_file($photoFile) ? $photoFile : (is_file($fallback) ? $fallback : null);
    $imgData = null;

    if ($imgPath) {
        $ext = strtolower(pathinfo($imgPath, PATHINFO_EXTENSION));
        $mime = $ext === 'png' ? 'image/png' : 'image/jpeg';
        $imgData = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($imgPath));
    }
@endphp

<div class="title">FICHA GENERAL DE MASCOTA</div>

<div class="row clearfix">
    <!-- IZQUIERDA: FOTO + DATOS MASCOTA -->
    <div class="col col-35">
        <div class="box">
            <div class="pet-photo">
                @if($imgData)
                    <img src="{{ $imgData }}">
                @endif
            </div>

            <div class="badge">MASCOTA</div>

            <table class="info-table" style="margin-top:8px">
                <tr><td class="label">Nombre:</td><td>{{ $v($m->nombre) }} {{ $m->apellido ? (' ' . $m->apellido) : '' }}</td></tr>
                <tr><td class="label">Especie:</td><td>{{ $v($m->especie) }}</td></tr>
                <tr><td class="label">Raza:</td><td>{{ $v($m->raza) }}</td></tr>
                <tr><td class="label">Sexo:</td><td>{{ $v($m->sexo) }}</td></tr>
                <tr><td class="label">Color:</td><td>{{ $v($m->color) }}</td></tr>
                <tr><td class="label">Fecha Nac.:</td><td>{{ $m->fecha_nac ? \Carbon\Carbon::parse($m->fecha_nac)->format('d/m/Y') : '-' }}</td></tr>
                <tr><td class="label">Edad:</td><td>{{ $v($m->edad) }}</td></tr>
                <tr><td class="label">Señas:</td><td>{{ $v($m->senas_particulares) }}</td></tr>
            </table>
        </div>
    </div>

    <!-- DERECHA: PROPIETARIO + VETERINARIA -->
    <div class="col col-65" style="padding-left:10px">

        <div class="box">
            <div class="badge">PROPIETARIO</div>

            <table class="info-table" style="margin-top:8px">
                <tr><td class="label">Nombre:</td><td>{{ $v($m->propietario_nombre) }}</td></tr>
                <tr><td class="label">CI:</td><td>{{ $v($m->propietario_ci) }}</td></tr>
                <tr><td class="label">Celular:</td><td>{{ $v($m->propietario_celular) }}</td></tr>
                <tr><td class="label">Teléfono:</td><td>{{ $v($m->propietario_telefono) }}</td></tr>
                <tr><td class="label">Ciudad:</td><td>{{ $v($m->propietario_ciudad) }}</td></tr>
                <tr><td class="label">Dirección:</td><td>{{ $v($m->propietario_direccion) }}</td></tr>
            </table>
        </div>

        <div class="box">
            <div class="badge">VETERINARIA</div>

            <table class="info-table" style="margin-top:8px">
                <tr><td class="label">Nombre:</td><td>{{ $vet ? $v($vet->nombre) : '-' }}</td></tr>
                <tr><td class="label">Dirección:</td><td>{{ $vet ? $v($vet->direccion) : '-' }}</td></tr>
                <tr><td class="label">Teléfono:</td><td>{{ $vet ? $v($vet->telefono) : '-' }}</td></tr>
                <tr><td class="label">Email:</td><td>{{ $vet ? $v($vet->email) : '-' }}</td></tr>
                <tr><td class="label">Estado:</td><td>{{ $vet ? $v($vet->estado) : '-' }}</td></tr>
            </table>
        </div>

        <div class="footer">
            <div><span class="bold">Impreso:</span> {{ now()->format('d/m/Y H:i') }}</div>
        </div>

    </div>
</div>

</body>
</html>
