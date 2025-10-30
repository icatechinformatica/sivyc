<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Checador · Dashboard Ejecutivo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <!-- Números animados -->
    <script src="https://cdn.jsdelivr.net/npm/animejs@3.2.1/lib/anime.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/tablero/checador.css') }}">
    <script>
        window.CROSSCHEX = {
            stream: "{{ route('crosschex.live.stream') }}",
            punctuality: "{{ route('crosschex.live.punctuality') }}",
            punctualityList: "{{ route('crosschex.live.punctuality.list') }}"
        };
    </script>
</head>
<body>
  <header>
    <h1>Checado de Funcionarios · Actividad en tiempo real</h1>
    {{-- <div class="muted">SSE “push” + polling de respaldo. Ventana por defecto: últimos 60 minutos.</div> --}}
    <div class="info-row">
    <div class="info-chip"><span class="dot dot-ok"></span>A tiempo</div>
    <div class="info-chip"><span class="dot dot-late"></span>Retardo</div>
    <div class="info-chip"><span class="dot dot-miss"></span>Faltan</div>
</div>
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


<script src="{{ asset('js/tablero/checador.js') }}"></script>
</body>
</html>
