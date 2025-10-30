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
    const es = new EventSource(window.CROSSCHEX.stream);
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
      const r = await fetch(window.CROSSCHEX.punctuality, { cache: 'no-store' });
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

  const LIST_URL = window.CROSSCHEX.punctualityList;

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
        const prettyType = ({ ontime: 'A tiempo', late: 'Retardo', missing: 'Faltan' })[json.type] ?? json.type;
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
