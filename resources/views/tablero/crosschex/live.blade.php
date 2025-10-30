<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>CrossChex · Dashboard Ejecutivo</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Charts -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <!-- Números animados -->
  <script src="https://cdn.jsdelivr.net/npm/animejs@3.2.1/lib/anime.min.js"></script>

  <style>
    :root { color-scheme: light dark; }
    * { box-sizing: border-box; }
    body {
      font-family: system-ui,-apple-system,"Segoe UI",Roboto,Inter,sans-serif;
      margin:0; padding:20px;
      background: canvas;
      color: CanvasText;
    }
    h1 { margin:0 0 4px 0; font-size: clamp(22px, 2.2vw, 34px); }
    .muted { opacity:.7; font-size:.9rem; }

    /* GRID principal */
    .grid-3 {
      display: grid;
      grid-template-columns: 1.2fr 1fr;
      gap: 16px;
    }
    .card {
      background: canvas;
      border: 1px solid color-mix(in oklab, CanvasText 18%, transparent);
      border-radius: 14px;
      padding: 14px 16px;
      box-shadow: 0 0 0 1px color-mix(in oklab, CanvasText 5%, transparent) inset;
    }
    .kpis { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin: 10px 0 16px; }
    .kpi { padding: 12px; border-radius: 12px; background: color-mix(in oklab, CanvasText 6%, transparent); }
    .kpi h4 { margin: 0 0 6px 0; font-weight:500; }
    .kpi .val { font-weight:800; font-size: clamp(20px, 2vw, 32px); letter-spacing: 0.5px; }

    /* Charts contenedores */
    .chart-card { height: 320px; display:flex; flex-direction:column; }
    .chart-wrap { flex: 1 1 auto; min-height: 0; }
    .chart-wrap canvas { width:100% !important; height:100% !important; }

    /* Panel ejecutivo por unidad */
    .exec {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 12px;
    }
    .exec-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
        gap: 14px;
    }
    .exec-item {
        border: 1px dashed rgba(255,255,255,.12);
        border-radius: 12px;
        padding: 14px;
        background: rgba(255,255,255,.02);
    }
    .exec-item:hover { border-color: color-mix(in oklab, CanvasText 35%, transparent) }
    .exec-head {
        display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;
    }
    .exec-title { font-weight:700; text-transform:uppercase; }
    .exec-bars { display:flex; flex-direction: column; gap: 8px; }
    .bar-row { display:flex; align-items:center; gap:10px; }
    .bar-label { width: 72px; text-align: right; font-size:12px; opacity:.8; }

    /* Track sin huecos entre segmentos */
    .bar-track{
    flex: 1 1 auto;              /* <<< clave */
    display:flex;                /* apila segmentos sin huecos */
    position:relative;
    height:16px;
    border-radius:999px;
    background:rgba(255,255,255,.08);
    overflow:hidden;
    }

    /* Cada tramo se anima con flex-basis (ancho) */
    .bar-fill{
    height:100%;
    flex: 0 0 0%;
    transition: none;            /* anime.js controla la animación */
    }

    .bar-fill.ok   { background:#1db954; } /* a tiempo */
    .bar-fill.late { background:#f2b01e; } /* retardo */
    .bar-fill.miss { background:#e53935; } /* faltan */

    /* bordes solo en extremos visibles */
    .bar-fill.first{ border-top-left-radius:999px;  border-bottom-left-radius:999px; }
    .bar-fill.last { border-top-right-radius:999px; border-bottom-right-radius:999px; }


    .exec-foot { display:flex; justify-content:flex-end; gap:14px; font-size:12px; opacity:.8; }

    /* Feed de actividad (sin tablas) */
    .feed { display:flex; flex-direction:column; gap:8px; }
    .feed-item {
      display:flex; align-items:center; gap:12px;
      padding: 10px 12px; border-radius: 10px;
      background: color-mix(in oklab, CanvasText 6%, transparent);
    }
    .badge-time {
      min-width:62px; text-align:center; font-weight:700;
      padding: 4px 8px; border-radius: 8px;
      background: color-mix(in oklab, CanvasText 8%, transparent);
    }
    .feed-body { display:flex; gap:12px; flex-wrap: wrap; align-items:baseline; }
    .feed-strong { font-weight:700; }
    .feed-subtle { opacity:.7; }
    .feed-wrap { max-height: 430px; overflow:auto; }
    .section-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; }
    .pill { padding: 2px 10px; border-radius: 999px; background: color-mix(in oklab, CanvasText 8%, transparent); font-size: 12px; }
    @media (max-width: 1100px) {
      .grid-3 { grid-template-columns: 1fr; }
      .exec { grid-template-columns: 1fr; }
      .kpis { grid-template-columns: 1fr; }
    }

    /* Contenedor raíz del modal */
    #peopleModal{
    position:fixed; inset:0;
    display:none;                /* controlado por data-state */
    align-items:center; justify-content:center;
    z-index:9998;
    }

    /* Estados de visualización */
    #peopleModal[data-state="open"]   { display:flex; }
    #peopleModal[data-state="closed"] { display:none; }

    /* Fondo */
    #peopleModal .modal-backdrop{
    position:absolute; inset:0;
    background:rgba(0,0,0,.5);
    opacity:0;                   /* animado */
    }

    /* Panel */
    #peopleModal .modal-panel{
    position:relative;
    width:min(720px,92vw);
    max-height:80vh; overflow:auto;
    background:#111; color:#eee;
    border:1px solid rgba(255,255,255,.1);
    border-radius:14px;
    padding:16px 18px;
    box-shadow:0 10px 40px rgba(0,0,0,.55);
    opacity:0;                   /* animado */
    transform:translateY(16px) scale(.985); /* animado */
    }

    /* Header */
    #peopleModal .modal-header{
    display:flex; align-items:center; gap:10px; margin-bottom:10px;
    }
    #peopleModal .modal-header h3{
    margin:0; font-weight:700; font-size:1.1rem;
    }
    #peopleModal .btn-close{
    margin-left:auto;
    border:0; background:#222; color:#ddd;
    padding:6px 10px; border-radius:8px; cursor:pointer;
    }
    #peopleModal .btn-close:hover{ background:#2a2a2a; }

    /* Body */
    #peopleModal .modal-body { gap: 6px; }


  </style>
</head>
<body>
  <header>
    <h1>CrossChex · Actividad en tiempo real</h1>
    <div class="muted">SSE “push” + polling de respaldo. Ventana por defecto: últimos 60 minutos.</div>
  </header>

  <!-- KPIs -->
    <section class="kpis">
        <div class="kpi">
        <h4>Total del día (live)</h4>
        <div class="val" id="kpi-total">—</div>
        </div>
        <div class="kpi">
        <h4>Últimos 5 minutos</h4>
        <div class="val" id="kpi-5min">—</div>
        </div>
        <div class="kpi">
        <h4>Hora del servidor</h4>
        <div class="val" id="kpi-time">—</div>
        </div>
    </section>

  <!-- 🆕 Panel ejecutivo por unidad -->
    <div class="card" style="margin-bottom:16px;">
        <div class="section-head" style="align-items:center; gap:12px;">
            <h3 style="margin:0;">Puntualidad por unidad</h3>
            <span class="pill" id="punctuality-updated">—</span>
        </div>

        <div id="exec-container" class="exec exec-grid">
            <!-- Aquí se inyectan las tarjetas por unidad -->
        </div>
    </div>

  <!-- Charts + Panel ejecutivo -->
    <section class="grid-3">
        <!-- Eventos por minuto -->
        <div class="card chart-card">
        <div class="section-head">
            <h3 style="margin:0;">Eventos por minuto</h3>
            <select id="window-select">
            <option value="30">Últimos 30 min</option>
            <option value="60" selected>Últimos 60 min</option>
            <option value="180">Últimas 3 h</option>
            </select>
        </div>
        <div class="chart-wrap"><canvas id="chartPerMinute"></canvas></div>
        </div>

        <!-- Top 10 unidades -->
        <div class="card chart-card">
        <div class="section-head"><h3 style="margin:0;">Top 10 Unidades</h3></div>
        <div class="chart-wrap"><canvas id="chartPerUnidad"></canvas></div>
        </div>
    </section>

  <!-- Feed/Últimos eventos (sin tabla) -->
  <section class="card" style="margin-top:16px;">
    <div class="section-head">
      <h3 style="margin:0;">Últimos eventos</h3>
      <span class="pill" id="last-refresh">—</span>
    </div>
    <div class="feed-wrap">
      <div id="feed" class="feed"></div>
    </div>
  </section>

  <!-- Tooltip flotante -->
    <div id="stackTooltip" style="
        position:fixed; left:0; top:0; transform:translate(-9999px,-9999px);
        background:rgba(0,0,0,.85); color:#fff; padding:6px 10px; border-radius:8px;
        font-size:.85rem; pointer-events:none; z-index:9999; white-space:nowrap;
    "></div>

    <!-- Modal animado -->
    <div id="peopleModal" data-state="closed">
    <div class="modal-backdrop"></div>
    <div class="modal-panel">
        <div class="modal-header">
        <h3 id="peopleModalTitle">Listado</h3>
        <button id="peopleModalClose" class="btn-close" type="button">Cerrar</button>
        </div>
        <div id="peopleModalBody" class="modal-body"></div>
    </div>
    </div>


<script>
/* ========== Estado global ========== */
let sseActive = false;
let __rt_started = false;
let perMinuteChart, perUnidadChart;

/* ========== Helpers ========== */
const fmt = (n) => new Intl.NumberFormat().format(n);

/** Crea card ejecutiva por unidad */
function buildExecCard(item) {
  const root = document.createElement('div');
  root.className = 'exec-item';
  root.dataset.unidad = item.unidad;

  root.innerHTML = `
    <div class="exec-head">
      <div class="exec-title">${item.unidad}</div>
      <div class="muted">Total: <strong class="exec-total">${fmt(item.total)}</strong></div>
    </div>
    <div class="exec-bars">
      <div class="bar-row">
        <div class="bar-label">A tiempo</div>
        <div class="bar-track">
          <div class="bar-fill ontime" style="width:0%"></div>
        </div>
        <div class="muted"><span class="num-ontime">${fmt(item.ontime)}</span></div>
      </div>
      <div class="bar-row">
        <div class="bar-label">Retardo</div>
        <div class="bar-track">
          <div class="bar-fill late" style="width:0%"></div>
        </div>
        <div class="muted"><span class="num-late">${fmt(item.late)}</span></div>
      </div>
    </div>
    <div class="exec-foot">
      <span class="muted">08:00–08:15 · 09:00–09:15</span>
      <span class="muted">08:16–08:30 · 09:16–09:30</span>
    </div>
  `;
  return root;
}

/** Actualiza/crea cards ejecutivas con animación */
function renderExec(items) {
  const container = document.getElementById('exec-container');
  const max = Math.max(1, ...items.map(i => i.total)); // evita /0
  const map = new Map(items.map(i => [i.unidad, i]));

  // Actualiza existentes o crea nuevos
  for (const [unidad, item] of map) {
    let card = container.querySelector(`.exec-item[data-unidad="${CSS.escape(unidad)}"]`);
    if (!card) {
      card = buildExecCard(item);
      container.appendChild(card);
    }
    const pctOntime = Math.round((item.ontime / max) * 100);
    const pctLate   = Math.round((item.late   / max) * 100);

    // Animación por CSS (width con transición)
    card.querySelector('.bar-fill.ontime').style.width = pctOntime + '%';
    card.querySelector('.bar-fill.late').style.width   = pctLate + '%';

    // Números con anime.js
    animateNumber(card.querySelector('.num-ontime'), item.ontime);
    animateNumber(card.querySelector('.num-late'),   item.late);
    card.querySelector('.exec-total').textContent = fmt(item.total);
  }

  // Elimina tarjetas de unidades que ya no vienen
  container.querySelectorAll('.exec-item').forEach(el => {
    if (!map.has(el.dataset.unidad)) el.remove();
  });
}

/* ========== Charts ========== */
function initCharts() {
  const perMinuteCtx = document.getElementById('chartPerMinute').getContext('2d');
  perMinuteChart = new Chart(perMinuteCtx, {
    type: 'line',
    data: { labels: [], datasets: [{ label: 'Eventos/min', data: [], tension: .25, fill: true }] },
    options: {
      animation: { duration: 600, easing: 'easeOutQuart' },
      responsive: true, maintainAspectRatio: false,
      scales: { y: { beginAtZero: true, ticks: { precision:0 } } }
    }
  });

  const perUnidadCtx = document.getElementById('chartPerUnidad').getContext('2d');
  perUnidadChart = new Chart(perUnidadCtx, {
    type: 'bar',
    data: { labels: [], datasets: [{ label: 'Eventos', data: [] }] },
    options: {
      animation: { duration: 600, easing: 'easeOutQuart' },
      indexAxis: 'y', responsive: true, maintainAspectRatio: false,
      scales: { x: { beginAtZero: true, ticks: { precision:0 } } }
    }
  });
}

/* ========== Fetchers ========== */
async function loadMetrics() {
  const minutes = document.getElementById('window-select').value;
  const r = await fetch(`/api/crosschex/live/metrics?minutes=${minutes}`, { cache: 'no-store' });
  const json = await r.json();

  animateNumber(document.getElementById('kpi-total'), json.totals?.total_all ?? 0);
  animateNumber(document.getElementById('kpi-5min'),  json.totals?.total_5min ?? 0);
  document.getElementById('kpi-time').textContent = json.serverTimeLocal;

  perMinuteChart.data.labels = json.perMinute.map(p => p.label);
  perMinuteChart.data.datasets[0].data = json.perMinute.map(p => Number(p.value));
  perMinuteChart.update();

  perUnidadChart.data.labels = json.perUnidad.map(p => p.unidad || '—');
  perUnidadChart.data.datasets[0].data = json.perUnidad.map(p => Number(p.total));
  perUnidadChart.update();
}

async function loadRecent() {
  const r = await fetch(`/api/crosschex/live/recent?limit=40`, { cache: 'no-store' });
  const json = await r.json();

  const feed = document.getElementById('feed');
  feed.innerHTML = '';
  for (const row of json.rows) {
    const el = document.createElement('div');
    el.className = 'feed-item';
    el.innerHTML = `
      <div class="badge-time">${row.received_time ?? '—'}</div>
      <div class="feed-body">
        <div class="feed-strong">${(row.first_name ?? '').toUpperCase()} ${(row.last_name ?? '').toUpperCase()}</div>
        <div class="feed-subtle">· ${row.workno ?? '—'}</div>
        <div class="feed-subtle">· ${row.unidad ?? '—'}</div>
        <div class="feed-subtle">· ${row.device_name ?? '—'} <span style="opacity:.6">${row.serial_number ?? ''}</span></div>
        <div class="feed-subtle">· Check: ${row.check_time_local ?? '—'}</div>
        <div class="feed-subtle">· Tipo: ${row.check_type ?? '—'}</div>
      </div>
    `;
    feed.appendChild(el);
  }
  document.getElementById('last-refresh').textContent = `Actualizado: ${json.serverTimeLocal}`;
}

/** 🆕 Carga panel ejecutivo */
async function loadPunctuality() {
  const r = await fetch('/api/crosschex/live/punctuality', { cache: 'no-store' });
    if (!r.ok) {
    const text = await r.text();
    console.error('punctuality failed', text);
    return; // evita intentar parsear HTML como JSON
    }
    const json = await r.json();
  renderExec(json.items || []);
  document.getElementById('punctuality-updated').textContent = `Actualizado: ${json.serverTimeLocal}`;
}

/* ========== Realtime (SSE + fallback) ========== */
function startSSE() {
  if (typeof EventSource === 'undefined') return false;
  try {
    const es = new EventSource(`{{ route('crosschex.live.stream') }}`);
    es.addEventListener('open', () => { sseActive = true; });
    es.addEventListener('batch', () => {
      // Llega un lote nuevo → refrescamos panel ejecutivo y KPIs rápido
      loadPunctuality();
      loadMetrics();
      // el feed lo refrescamos de forma incremental si quieres; por simplicidad:
      loadRecent();
    });
    es.addEventListener('error', () => { sseActive = false; es.close(); setTimeout(startSSE, 3000); });
    return true;
  } catch { return false; }
}

async function refreshAll() {
  await Promise.all([loadMetrics(), loadPunctuality(), loadRecent()]);
}

function boot() {
  if (__rt_started) return; __rt_started = true;
  initCharts();
  const ok = startSSE();
  refreshAll();
  // Polling de respaldo (si SSE cae, mantenemos animación suave)
  setInterval(() => {
    if (!sseActive) refreshAll();
    else loadMetrics(); // refresco ligero para animación del chart
  }, 15000);
}

document.addEventListener('DOMContentLoaded', boot);
document.getElementById('window-select').addEventListener('change', refreshAll);

 // helpers
  // const fmt = (n) => new Intl.NumberFormat('es-MX').format(n);
  const hasAnime = typeof window.anime === 'function';

 /** Anima números con anime.js */
    function animateNumber(el, to) {
        const from = parseInt(el.dataset.val || '0', 10);
        el.dataset.val = String(to);
        anime({
            targets: { val: from },
            val: to,
            easing: 'easeOutExpo',
            round: 1,
            duration: 900,
            update: (a) => el.textContent = fmt(a.animations[0].currentValue)
        });
    }

    // const hasAnime = typeof window.anime === 'function';

    /** Calcula y normaliza porcentajes (cierra exacto a 100) */
    function calcPercents(item){
    const total = Math.max(1, item.total);
    const ok   = (item.ontime || 0) * 100 / total;
    const late = (item.late   || 0) * 100 / total;
    const miss = Math.max(0, 100 - ok - late);
    // 4 decimales para evitar acumulación de redondeo
    return {
        ok  : +ok.toFixed(4),
        late: +late.toFixed(4),
        miss: +miss.toFixed(4),
    };
    }

    /** Marca qué segmentos deben redondear extremos */
    function updateCaps(card, p){
    const okEl   = card.querySelector('.bar-fill.ok');
    const lateEl = card.querySelector('.bar-fill.late');
    const missEl = card.querySelector('.bar-fill.miss');
    [okEl, lateEl, missEl].forEach(el => el.classList.remove('first','last'));
    const nonZero = [];
    if (p.ok   > 0.0001) nonZero.push(okEl);
    if (p.late > 0.0001) nonZero.push(lateEl);
    if (p.miss > 0.0001) nonZero.push(missEl);
    if (nonZero.length){
        nonZero[0].classList.add('first');
        nonZero[nonZero.length-1].classList.add('last');
    }
    }

    /** 🟢 Animación con anime.js (fallback a set style si no hay anime) */
    function animateStackTo(card, p){
    const okEl   = card.querySelector('.bar-fill.ok');
    const lateEl = card.querySelector('.bar-fill.late');
    const missEl = card.querySelector('.bar-fill.miss');

    if (!hasAnime){
        okEl.style.flexBasis   = `${p.ok}%`;
        lateEl.style.flexBasis = `${p.late}%`;
        missEl.style.flexBasis = `${p.miss}%`;
        okEl.style.width   = `${p.ok}%`;     // respaldo por si hay CSS viejo
        lateEl.style.width = `${p.late}%`;
        missEl.style.width = `${p.miss}%`;
        updateCaps(card, p);
        return;
    }

    // Guarda el valor actual para animar desde ahí
    const cur = {
        ok  : parseFloat(okEl.style.flexBasis)   || 0,
        late: parseFloat(lateEl.style.flexBasis) || 0,
        miss: parseFloat(missEl.style.flexBasis) || 0,
    };

    anime.timeline({ duration: 700, easing: 'easeOutExpo' })
        .add({
        targets: cur,
        ok: p.ok, late: p.late, miss: p.miss,
        update: () => {
            okEl.style.flexBasis   = `${cur.ok}%`;
            lateEl.style.flexBasis = `${cur.late}%`;
            missEl.style.flexBasis = `${cur.miss}%`;
            // (opcional) compat: width igual a flex-basis
            okEl.style.width   = okEl.style.flexBasis;
            lateEl.style.width = lateEl.style.flexBasis;
            missEl.style.width = missEl.style.flexBasis;
        },
        complete: () => updateCaps(card, p),
        });
    }


    function buildStackCard(item){
        const root = document.createElement('div');
        root.className = 'exec-item';
        root.dataset.unidad = item.unidad;
        root.innerHTML = `
            <div class="exec-head">
            <div class="exec-title">${item.unidad}</div>
            <div class="muted">Total: <strong class="exec-total">${fmt(item.total)}</strong></div>
            </div>

            <div class="bar-row">
            <div class="bar-label muted" style="width:auto;">Asistencia</div>
            <div class="bar-track">
                <div class="bar-fill ok"   style="flex-basis:0%"></div>
                <div class="bar-fill late" style="flex-basis:0%"></div>
                <!-- ⬇️ inicia al 100% en rojo para que se vea desde el primer render -->
                <div class="bar-fill miss first last" style="flex-basis:100%"></div>
            </div>
            <div class="muted" style="width:84px; text-align:right;">
                <span class="num-checked">0</span>/<span class="num-total">${fmt(item.total)}</span>
            </div>
            </div>

            <div class="exec-foot" style="display:flex; gap:16px; margin-top:8px;">
            <span class="muted">Verde: A tiempo</span>
            <span class="muted">Amarillo: Retardo</span>
            <span class="muted">Rojo: Falta</span>
            </div>`;
        return root;
    }



    function renderStacked(items){
        const container = document.getElementById('exec-container');
        const map = new Map(items.map(i => [i.unidad, i]));

        for (const [unidad, item] of map){
        let card = container.querySelector(`.exec-item[data-unidad="${CSS.escape(unidad)}"]`);
        if (!card){
            card = buildStackCard(item);
            container.appendChild(card);
        }

        // 1) Calcula porcentajes (verde/amarillo/rojo)
        const p = calcPercents(item);

        // 2) Anima barras
        animateStackTo(card, p);

        // 3) Conteo checados = a tiempo + retardos
        const checked = (item.ontime || 0) + (item.late || 0);
        animateNumber(card.querySelector('.num-checked'), checked);
        card.querySelector('.num-total').textContent = fmt(item.total);
        card.querySelector('.exec-total').textContent = fmt(item.total);

        // 4) **IMPRESCINDIBLE**: setear counts + enlazar tooltip/clicks
        const okEl   = card.querySelector('.bar-fill.ok');
        const lateEl = card.querySelector('.bar-fill.late');
        const missEl = card.querySelector('.bar-fill.miss');
        okEl.dataset.count   = String(item.ontime || 0);
        lateEl.dataset.count = String(item.late   || 0);
        const missing = Math.max(0, (item.total||0) - (item.ontime||0) - (item.late||0));
        missEl.dataset.count = String(missing);

        // cursor “clicable” en verde y amarillo
        okEl.style.cursor = 'pointer';
        lateEl.style.cursor = 'pointer';

        // Enlaza handlers (una sola vez por tarjeta)
        bindSegmentHandlers(card);
        }

        // Elimina tarjetas que ya no vienen en la respuesta
        container.querySelectorAll('.exec-item').forEach(el => {
        if (!map.has(el.dataset.unidad)) el.remove();
        });
    }


  async function loadPunctuality() {
    try {
      const r = await fetch(`{{ route('crosschex.live.punctuality') }}`, { cache: 'no-store' });
      if (!r.ok) { console.warn('punctuality fetch failed', r.status); return; }
      const json = await r.json();
      renderStacked(json.items || []);
      document.getElementById('punctuality-updated').textContent = `Actualizado: ${json.serverTimeLocal}`;
    } catch (e) {
      console.error('punctuality error', e);
    }
  }

  // =========== Integración con tu dashboard ===========

  // 1) Primera carga
  loadPunctuality();

  // 2) Polling fallback (cada 15s) — ajusta si quieres menos
  let punctualityTimer = setInterval(loadPunctuality, 15000);

  // 3) Hook a tu SSE (si ya tienes EventSource abierto):
  //    Llama a loadPunctuality cuando llegue un batch nuevo
  //    (Solo si ya usas un EventSource; si no, ignora esto)
  try {
    if (window.CROSSCHEX_SSE) {
      window.CROSSCHEX_SSE.addEventListener('batch', () => {
        // Debounce simple para no saturar en ráfagas
        clearTimeout(window.__PCT_DEBOUNCE);
        window.__PCT_DEBOUNCE = setTimeout(loadPunctuality, 500);
      });
    }
  } catch(_) {}

  const LIST_URL = "{{ route('crosschex.live.punctuality.list') }}";

  // ---------- Tooltip ----------
  const tip = document.getElementById('stackTooltip');
  function showTip(text, x, y){
    tip.textContent = text;
    tip.style.transform = 'translate(0,0)';
    // margen para que no tape el cursor
    const pad = 12;
    tip.style.left = (x + pad) + 'px';
    tip.style.top  = (y + pad) + 'px';
  }
  function hideTip(){
    tip.style.transform = 'translate(-9999px,-9999px)';
  }

  // ---------- Modal ----------
    const modal = document.getElementById('peopleModal');
    const modalTitle = document.getElementById('peopleModalTitle');
    const modalBody  = document.getElementById('peopleModalBody');

    document.getElementById('peopleModalClose').onclick = (e) => {
        e.preventDefault();
        hidePeopleModal();       // <-- usa la animación
    };

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            hidePeopleModal();     // <-- también animado
        }
    });

    function renderPeopleList(json){
        const prettyType = json.type === 'late' ? 'Retardo' : 'A tiempo';
        modalTitle.textContent = `${json.unidad} · ${prettyType} (${json.items.length})`;
        modalBody.innerHTML = '';

        if (!json.items.length){
            modalBody.innerHTML = `<div style="opacity:.8;">Sin registros.</div>`;
            return;
        }

        // Lista simple: Nombre — HH:MM:SS
        const list = document.createElement('div');
        list.style.display = 'grid';
        list.style.gap = '8px';

        for (const it of json.items){
            const row = document.createElement('div');
            row.style.cssText = `
            display:flex; align-items:center; gap:10px;
            padding:8px 10px; border-radius:8px;
            background:rgba(255,255,255,.03);
            border:1px solid rgba(255,255,255,.08);
            `;

            // Nombre
            const name = document.createElement('div');
            name.style.flex = '1 1 auto';
            name.style.fontWeight = '700';
            name.textContent = it.full_name || '—';

            // Hora
            const time = document.createElement('div');
            time.style.opacity = '.9';
            time.style.fontVariantNumeric = 'tabular-nums';
            time.textContent = onlyTime(it.check_time_local);

            row.appendChild(name);
            row.appendChild(time);
            list.appendChild(row);
        }

        modalBody.appendChild(list);
    }


    async function openPeopleModal(unidad, type){
        showPeopleModal(); // <-- en lugar de modal.style.display = 'flex'
        modalTitle.textContent = `${unidad} · cargando...`;
        modalBody.innerHTML = `<div style="opacity:.8;">Cargando…</div>`;

        try{
            const r = await fetch(`${LIST_URL}?unidad=${encodeURIComponent(unidad)}&type=${encodeURIComponent(type)}`, { cache: 'no-store' });
            if(!r.ok) throw new Error('HTTP '+r.status);
            const json = await r.json();
            renderPeopleList(json); // <-- esta función ya la tienes arriba
        }catch(err){
            modalBody.innerHTML = `<div style="color:#ff6b6b;">Error al cargar: ${String(err)}</div>`;
        }
    }

  // ---------- Enlazar handlers a cada tarjeta ----------
  function bindSegmentHandlers(card){
    if (card.dataset.bound === '1') return; // una sola vez
    card.dataset.bound = '1';

    const unidad = card.dataset.unidad;
    const okEl   = card.querySelector('.bar-fill.ok');
    const lateEl = card.querySelector('.bar-fill.late');
    const missEl = card.querySelector('.bar-fill.miss');

    // tooltips (mouse)
    const onEnter = (el, label) => (ev) => {
      const count = Number(el.dataset.count || 0);
      showTip(`${label}: ${count}`, ev.clientX, ev.clientY);
    };
    const onMove = (ev) => showTip(tip.textContent, ev.clientX, ev.clientY);
    const onLeave = () => hideTip();

    okEl.addEventListener('mouseenter', onEnter(okEl, 'A tiempo'));
    okEl.addEventListener('mousemove',  onMove);
    okEl.addEventListener('mouseleave', onLeave);

    lateEl.addEventListener('mouseenter', onEnter(lateEl, 'Retardo'));
    lateEl.addEventListener('mousemove',  onMove);
    lateEl.addEventListener('mouseleave', onLeave);

    missEl.addEventListener('mouseenter', onEnter(missEl, 'Faltan'));
    missEl.addEventListener('mousemove',  onMove);
    missEl.addEventListener('mouseleave', onLeave);

    // clics para abrir modal (verde/amarillo)
    okEl.style.cursor = 'pointer';
    lateEl.style.cursor = 'pointer';
    missEl.style.cursor = 'pointer';
    okEl.addEventListener('click',  () => openPeopleModal(unidad, 'ontime'));
    lateEl.addEventListener('click',() => openPeopleModal(unidad, 'late'));
    missEl.addEventListener('click', () => openPeopleModal(card.dataset.unidad, 'missing'));
  }

  // ---------- Integra con tu render existente ----------
  // En tu buildStackCard(...) añade data-unidad en root (ya lo tienes).
  // En renderStacked(...) cuando pintes, añade:
  //   - dataset.count en cada segmento para el tooltip
  //   - bindSegmentHandlers(card) (una sola vez)

  // Dentro de renderStacked(items), después de calcular p = {ok, late, miss}:
  // (esto complementa lo que ya tienes)
  function afterPaintPerCard(card, item, p){
    const okEl   = card.querySelector('.bar-fill.ok');
    const lateEl = card.querySelector('.bar-fill.late');
    const missEl = card.querySelector('.bar-fill.miss');
    okEl.dataset.count   = String(item.ontime || 0);
    lateEl.dataset.count = String(item.late   || 0);
    // faltantes:
    const missing = Math.max(0, (item.total||0) - (item.ontime||0) - (item.late||0));
    missEl.dataset.count = String(missing);

    bindSegmentHandlers(card);
  }

  const modalEl     = document.getElementById('peopleModal');
  const modalPanel  = modalEl.querySelector('.modal-panel');
  const modalBack   = modalEl.querySelector('.modal-backdrop');
  const modalClose  = document.getElementById('peopleModalClose');

  // Abre modal con animación
  function showPeopleModal(){
    if (modalEl.dataset.state === 'open') return;
    modalEl.dataset.state = 'open'; // display:flex
    // estado inicial (por si venimos de cerrado sin estilos)
    modalPanel.style.opacity = 0;
    modalPanel.style.transform = 'translateY(16px) scale(.985)';
    modalBack.style.opacity = 0;

    const tl = anime.timeline({ autoplay: true });
    tl.add({
      targets: modalBack,
      opacity: [0, 1],
      duration: 180,
      easing: 'easeOutQuad'
    }).add({
      targets: modalPanel,
      opacity: [0, 1],
      translateY: [16, 0],
      scale: [.985, 1],
      duration: 220,
      easing: 'easeOutCubic'
    }, '-=80'); // solapar
  }

  // Cierra modal con animación
  function hidePeopleModal(){
    if (modalEl.dataset.state !== 'open') return;
    const tl = anime.timeline({ autoplay: true });
    tl.add({
      targets: modalPanel,
      opacity: [1, 0],
      translateY: [0, 10],
      scale: [1, .99],
      duration: 180,
      easing: 'easeInQuad'
    }).add({
      targets: modalBack,
      opacity: [1, 0],
      duration: 160,
      easing: 'easeInQuad',
      complete: () => {
        modalEl.dataset.state = 'closed'; // display:none
      }
    }, '-=80');
  }

  // Clic en backdrop / botón cierra
  modalBack.addEventListener('click', hidePeopleModal);
  modalClose.addEventListener('click', hidePeopleModal);
  // ESC
  window.addEventListener('keydown', (e)=>{ if(e.key === 'Escape') hidePeopleModal(); });

  function onlyTime(str) {
  // espera "YYYY-MM-DD HH:MM:SS" del backend
  if (!str) return '—';
  const parts = str.split(' ');
  return parts.length > 1 ? parts[1] : str;
}
</script>
</body>
</html>
