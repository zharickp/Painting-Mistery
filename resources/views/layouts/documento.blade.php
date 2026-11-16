{{-- Plantilla para los documentos que se imprimen o se guardan como PDF
     (orden de venta, comprobante de curso y reportes).
     Usa estilos propios, sin Tailwind, para que el PDF salga igual en cualquier navegador.

     Secciones: titulo (pestaña), tipo (ej. "Orden de venta"), numero, fecha, estado (opcional),
     volver (url), contenido. --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo') - Painting Mistery</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <style>
        @page { size: letter; margin: 14mm 14mm 16mm; }

        * { box-sizing: border-box; }
        html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        body {
            margin: 0; background: #eef0f3; color: #1f2937;
            font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 13px; line-height: 1.45;
        }

        /* Barra de acciones (no se imprime) */
        .acciones { max-width: 816px; margin: 24px auto 12px; padding: 0 16px; display: flex; justify-content: space-between; align-items: center; gap: 12px; }
        .acciones a { color: #4b5563; text-decoration: none; font-weight: 600; font-size: 14px; }
        .acciones a:hover { color: #dc2626; }
        .btn { display: inline-flex; align-items: center; gap: 8px; background: #dc2626; color: #fff; border: 0; border-radius: 10px; padding: 10px 18px; font-weight: 700; font-size: 14px; cursor: pointer; font-family: inherit; }
        .btn:hover { background: #b91c1c; }

        /* Hoja */
        .hoja { position: relative; max-width: 816px; margin: 0 auto 40px; background: #fff; border-radius: 6px; box-shadow: 0 4px 24px rgba(15, 23, 42, .10); overflow: hidden; }
        .franja { height: 6px; background: #dc2626; }
        .interior { padding: 36px 44px 32px; }

        /* Encabezado */
        .encabezado { display: flex; justify-content: space-between; align-items: flex-start; gap: 24px; padding-bottom: 20px; border-bottom: 2px solid #111827; }
        .empresa { display: flex; gap: 14px; align-items: center; }
        .empresa img { width: 58px; height: 58px; border-radius: 50%; object-fit: cover; }
        .empresa .nombre { font-size: 20px; font-weight: 800; color: #111827; margin: 0; letter-spacing: -.2px; }
        .empresa .nombre span { color: #dc2626; }
        .empresa .dato { margin: 1px 0 0; font-size: 11.5px; color: #6b7280; }
        .doc { text-align: right; }
        .doc .tipo { margin: 0; font-size: 11px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: #dc2626; }
        .doc .numero { margin: 2px 0 0; font-size: 19px; font-weight: 800; color: #111827; font-family: "Consolas", "Courier New", monospace; }
        .doc .fecha { margin: 2px 0 0; font-size: 12px; color: #6b7280; }

        .estado { display: inline-block; margin-top: 8px; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; }
        .estado.verde { background: #dcfce7; color: #166534; }
        .estado.amarillo { background: #fef3c7; color: #92400e; }
        .estado.rojo { background: #fee2e2; color: #991b1b; }
        .estado.azul { background: #dbeafe; color: #1e40af; }

        /* Bloques de datos */
        .datos { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin: 22px 0; }
        .caja { border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px 14px; }
        .caja h3, .seccion-titulo { margin: 0 0 6px; font-size: 10.5px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: #6b7280; }
        .caja p { margin: 2px 0; }
        .caja .fuerte { font-weight: 700; color: #111827; font-size: 14px; }
        .suave { color: #6b7280; }

        /* Tablas */
        table { width: 100%; border-collapse: collapse; margin: 6px 0 4px; }
        thead th { background: #111827; color: #fff; font-size: 10.5px; font-weight: 700; letter-spacing: .8px; text-transform: uppercase; padding: 8px 10px; text-align: left; }
        thead th:first-child { border-radius: 6px 0 0 6px; }
        thead th:last-child { border-radius: 0 6px 6px 0; }
        tbody td { padding: 9px 10px; border-bottom: 1px solid #eef0f3; vertical-align: top; }
        tbody tr:nth-child(even) td { background: #f9fafb; }
        tfoot td { padding: 9px 10px; font-weight: 700; border-top: 2px solid #111827; }
        .der { text-align: right; } .centro { text-align: center; }
        .num { font-variant-numeric: tabular-nums; white-space: nowrap; }

        /* Totales */
        .totales { margin-left: auto; width: 290px; margin-top: 10px; }
        .totales .fila { display: flex; justify-content: space-between; padding: 5px 2px; color: #4b5563; }
        .totales .total { margin-top: 4px; padding: 10px 12px; background: #111827; color: #fff; border-radius: 8px; font-size: 15px; font-weight: 800; }

        /* Indicadores (reportes) */
        .kpis { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin: 20px 0; }
        .kpi { border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px 12px; }
        .kpi .etiqueta { margin: 0; font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #6b7280; }
        .kpi .valor { margin: 4px 0 0; font-size: 17px; font-weight: 800; color: #111827; }
        .kpi .detalle { margin: 1px 0 0; font-size: 11px; color: #6b7280; }

        /* Barras simples para los reportes (se imprimen sin depender de JavaScript) */
        .barra { height: 8px; background: #f3f4f6; border-radius: 4px; overflow: hidden; min-width: 60px; }
        .barra span { display: block; height: 100%; background: #dc2626; border-radius: 4px; }
        .barra.gris span { background: #475569; }

        .seccion { margin-top: 24px; page-break-inside: avoid; }
        .nota { margin-top: 24px; padding: 10px 14px; background: #f9fafb; border-left: 3px solid #d1d5db; color: #6b7280; font-size: 11px; border-radius: 0 6px 6px 0; }
        .pie { margin-top: 18px; text-align: center; color: #9ca3af; font-size: 11px; }

        .sello { position: absolute; top: 46%; left: 50%; transform: translate(-50%, -50%) rotate(-14deg); font-size: 88px; font-weight: 900; letter-spacing: 8px; color: rgba(220, 38, 38, .10); border: 8px solid rgba(220, 38, 38, .10); border-radius: 16px; padding: 4px 24px; pointer-events: none; }

        @media (max-width: 640px) {
            .interior { padding: 24px 18px; }
            .encabezado { flex-direction: column; }
            .doc { text-align: left; }
            .datos, .kpis { grid-template-columns: 1fr 1fr; }
            .totales { width: 100%; }
        }

        @media print {
            body { background: #fff; font-size: 12px; }
            .no-imprimir { display: none !important; }
            .hoja { box-shadow: none; border-radius: 0; margin: 0; max-width: none; }
            .interior { padding: 18px 0 0; }
            .franja { display: none; }
            thead { display: table-header-group; }
            tr { page-break-inside: avoid; }
            .encabezado { padding-bottom: 14px; }
            .datos, .kpis { margin: 14px 0; }
            thead th { padding: 6px 8px; }
            tbody td, tfoot td { padding: 5px 8px; }
            .seccion { margin-top: 16px; }
            .nota { margin-top: 16px; }
            .datos > div { page-break-inside: avoid; }
        }
    </style>
    @stack('estilos')
</head>
<body>

<div class="acciones no-imprimir">
    <a href="@yield('volver', url()->previous())">&larr; Volver</a>
    <button type="button" class="btn" onclick="window.print()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Descargar PDF
    </button>
</div>

<div class="hoja">
    <div class="franja"></div>
    @hasSection('sello')<div class="sello">@yield('sello')</div>@endif
    <div class="interior">
        <div class="encabezado">
            <div class="empresa">
                <img src="{{ asset('images/logo-painting-mistery.png') }}" alt="Painting Mistery">
                <div>
                    <p class="nombre">Painting <span>Mistery</span></p>
                    <p class="dato">Cl. 4 # 35-42 casa 13, Sicomoro · Melgar, Tolima</p>
                    <p class="dato">WhatsApp +57 314 455 7602 · paintingmistery20@gmail.com</p>
                </div>
            </div>
            <div class="doc">
                <p class="tipo">@yield('tipo')</p>
                <p class="numero">@yield('numero')</p>
                <p class="fecha">@yield('fecha')</p>
                @yield('estado')
            </div>
        </div>

        @yield('contenido')
    </div>
</div>

</body>
</html>
