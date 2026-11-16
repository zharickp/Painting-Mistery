{{-- Chart.js y opciones comunes de las gráficas (inicio y reportes). --}}
@once
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
window.pmGrafica = (() => {
    const oscuro = () => document.documentElement.classList.contains('dark');
    const texto  = () => oscuro() ? '#94a3b8' : '#64748b';
    const rejilla = () => oscuro() ? 'rgba(148,163,184,0.15)' : 'rgba(148,163,184,0.2)';

    const pesos = v => '$' + Math.round(v).toLocaleString('es-CO');
    // Eje corto: $5 M, $500 mil
    const corto = v => v >= 1000000 ? '$' + (v / 1000000).toLocaleString('es-CO', { maximumFractionDigits: 1 }) + ' M'
                     : v >= 1000    ? '$' + Math.round(v / 1000) + ' mil'
                     : '$' + v;

    function opcionesBarras(conLeyenda = false) {
        return {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: conLeyenda, position: 'top', align: 'end', labels: { color: texto(), usePointStyle: true, pointStyle: 'circle', boxWidth: 8 } },
                tooltip: { callbacks: { label: c => ' ' + c.dataset.label + ': ' + pesos(c.parsed.y) } },
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: texto() } },
                y: { beginAtZero: true, grid: { color: rejilla() }, ticks: { color: texto(), callback: corto } },
            },
        };
    }

    return { pesos, corto, opcionesBarras };
})();
</script>
@endonce
