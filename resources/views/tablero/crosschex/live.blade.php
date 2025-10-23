<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>CrossChex · Live</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <style>
    :root { color-scheme: light dark; }
    body {
      font-family: system-ui, -apple-system, Segoe UI, Roboto, Inter, sans-serif;
      margin:0; padding:16px;
      background: canvas;
    }
    .grid {
      display: grid;
      gap: 16px;
      grid-template-columns: 2fr 1fr;
    }
    .card {
      background: canvas;
      border: 1px solid color-mix(in oklab, CanvasText 15%, transparent);
      border-radius: 12px;
      padding: 16px;
    }

    /* Alturas fijas de los charts */
    .chart-card {
      height: 280px;
      display: flex;
      flex-direction: column;
    }
    .chart-wrap {
      flex: 1 1 auto;
      min-height: 0;
    }
    .chart-wrap > canvas {
      width: 100% !important;
      height: 100% !important;
    }

    .kpis { display: flex; gap: 12px; flex-wrap: wrap; }
    .kpi {
      flex: 1;
      min-width: 180px;
      padding: 12px;
      border-radius: 10px;
      background: color-mix(in oklab, CanvasText 6%, transparent);
    }
    .muted { opacity:.7; font-size:12px; }
    table { width: 100%; border-collapse: collapse; }
    th, td {
      padding: 8px 10px;
      border-bottom: 1px solid color-mix(in oklab, CanvasText 12%, transparent);
      font-size: 14px;
    }
    th { text-align: left; }
    .header {
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:12px;
      flex-wrap:wrap;
    }

    /* Contenedor de tabla con scroll */
    .table-wrap {
      max-height: 360px;
      overflow-y: auto;
    }
  </style>
</head>
<body>
  <div class="header">
    <div>
      <h2 style="margin:0;">CrossChex · Actividad en tiempo real</h2>
      <div class="muted">SSE para “push” + polling como respaldo. Ventana por defecto: últimos 60 minutos.</div>
    </div>
    <div>
      <select id="window-select">
        <option value="30">Últimos 30 min</option>
        <option value="60" selected>Últimos 60 min</option>
        <option value="180">Últimas 3 h</option>
      </select>
    </div>
  </div>

  <div class="kpis" style="margin:12px 0;">
    <div class="kpi">
      <div class="muted">Total del día (live)</div>
      <div id="kpi-total" style="font-size:24px;font-weight:700;">—</div>
    </div>
    <div class="kpi">
      <div class="muted">Últimos 5 minutos</div>
      <div id="kpi-5min" style="font-size:24px;font-weight:700;">—</div>
    </div>
    <div class="kpi">
      <div class="muted">Hora del servidor</div>
      <div id="kpi-time" style="font-size:24px;font-weight:700;">—</div>
    </div>
  </div>

  <div class="grid">
    <div class="card chart-card">
      <div style="display:flex;justify-content:space-between;align-items:center;">
        <h3 style="margin:0;">Eventos por minuto</h3>
      </div>
      <div class="chart-wrap">
        <canvas id="chartPerMinute"></canvas>
      </div>
    </div>

    <div class="card chart-card">
      <h3 style="margin:0;">Top 10 Unidades</h3>
      <div class="chart-wrap">
        <canvas id="chartPerUnidad"></canvas>
      </div>
    </div>
  </div>

  <div class="card" style="margin-top:16px;">
    <h3 style="margin:0 0 8px 0;">Últimos eventos</h3>
    <div class="table-wrap">
      <table id="tbl-recent">
        <thead>
          <tr>
            <th>Recibido</th>
            <th>Empleado</th>
            <th>Unidad</th>
            <th>Dispositivo</th>
            <th>Check time</th>
            <th>Tipo</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
    <div class="muted" id="last-refresh">—</div>
  </div>

<script>
const fmt = (n) => new Intl.NumberFormat().format(n);

let __rt_started = false;
let sseActive = false;
let sseRetryMs = 3000;

// === Inicialización Chart.js ===
const perMinuteCtx = document.getElementById('chartPerMinute').getContext('2d');
const perUnidadCtx  = document.getElementById('chartPerUnidad').getContext('2d');

let perMinuteChart = new Chart(perMinuteCtx, {
  type: 'line',
  data: { labels: [], datasets: [{ label: 'Eventos/min', data: [], tension: .25, fill: true }] },
  options: {
    animation: false, responsive: true, maintainAspectRatio: false,
    scales: { y: { beginAtZero: true, ticks: { precision:0 } } }
  }
});

let perUnidadChart = new Chart(perUnidadCtx, {
  type: 'bar',
  data: { labels: [], datasets: [{ label: 'Eventos', data: [] }] },
  options: {
    animation: false, indexAxis:'y', responsive: true, maintainAspectRatio: false,
    scales: { x: { beginAtZero: true, ticks: { precision:0 } } }
  }
});

// === Funciones de datos ===
async function loadMetrics() {
  const minutes = document.getElementById('window-select').value;
  const res = await fetch(`/api/crosschex/live/metrics?minutes=${minutes}`, { cache: 'no-store' });
  const json = await res.json();

  document.getElementById('kpi-total').textContent = fmt(json.totals?.total_all ?? 0);
  document.getElementById('kpi-5min').textContent  = fmt(json.totals?.total_5min ?? 0);
  document.getElementById('kpi-time').textContent  = new Date(json.serverTime).toLocaleTimeString();

  perMinuteChart.data.labels = json.perMinute.map(p => p.label);
  perMinuteChart.data.datasets[0].data = json.perMinute.map(p => Number(p.value));
  perMinuteChart.update('none');

  perUnidadChart.data.labels = json.perUnidad.map(p => p.unidad || '—');
  perUnidadChart.data.datasets[0].data = json.perUnidad.map(p => Number(p.total));
  perUnidadChart.update('none');
}

async function loadRecent() {
  const res = await fetch(`/api/crosschex/live/recent?limit=50`, { cache: 'no-store' });
  const json = await res.json();
  const tbody = document.querySelector('#tbl-recent tbody');
  tbody.innerHTML = '';
  for (const r of json.rows) {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${new Date(r.received_at).toLocaleTimeString()}</td>
      <td>${(r.workno ?? '')} ${(r.first_name ?? '')} ${(r.last_name ?? '')}</td>
      <td>${r.unidad ?? '—'}</td>
      <td>${(r.device_name ?? '—')} <span class="muted">${r.serial_number ?? ''}</span></td>
      <td>${r.check_time ?? '—'}</td>
      <td>${r.check_type ?? '—'}</td>`;
    tbody.appendChild(tr);
  }
  document.getElementById('last-refresh').textContent =
    `Actualizado: ${new Date(json.serverTime).toLocaleTimeString()}`;
}

async function refreshAll() {
  await Promise.all([loadMetrics(), loadRecent()]);
}

// === SSE + fallback ===
function attachRowsIntoTable(rowsAsc) {
  const tbody = document.querySelector('#tbl-recent tbody');
  for (const r of rowsAsc.reverse()) {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${new Date(r.received_at).toLocaleTimeString()}</td>
      <td>${(r.workno ?? '')} ${(r.first_name ?? '')} ${(r.last_name ?? '')}</td>
      <td>${r.unidad ?? '—'}</td>
      <td>${(r.device_name ?? '—')} <span class="muted">${r.serial_number ?? ''}</span></td>
      <td>${r.check_time ?? '—'}</td>
      <td>${r.check_type ?? '—'}</td>`;
    tbody.prepend(tr);
  }
  while (tbody.rows.length > 200) tbody.deleteRow(-1);
}

function startSSE() {
  if (typeof window.EventSource === 'undefined') return false;
  try {
    const es = new EventSource('{{ route('crosschex.live.stream') }}');
    es.addEventListener('open', () => {
      sseActive = true;
      console.debug('[SSE] Conectado');
    });
    es.addEventListener('batch', (e) => {
      const rows = JSON.parse(e.data || '[]');
      if (rows.length) {
        attachRowsIntoTable(rows);
        loadMetrics();
      }
    });
    es.addEventListener('error', () => {
      sseActive = false;
      console.warn('[SSE] Error / desconectado, reintentando…');
      es.close();
      setTimeout(startSSE, sseRetryMs);
    });
    return true;
  } catch {
    return false;
  }
}

async function startRealtime() {
  if (__rt_started) return;
  __rt_started = true;

  const ok = startSSE();
  if (!ok) console.warn('[SSE] No soportado, usando polling');

  const pollingMs = ok ? 15000 : 5000;
  refreshAll();

  setInterval(() => {
    if (!sseActive) {
      refreshAll();
    } else {
      loadMetrics();
    }
  }, pollingMs);
}

document.getElementById('window-select').addEventListener('change', refreshAll);
document.addEventListener('DOMContentLoaded', startRealtime);
</script>
</body>
</html>
