@extends('theme.sivyc.layout')
    @section('title', 'Reporte Asistencia CrossChex | SIVyC Icatech')

    @section('content_script_css')
        <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
        <!-- Select2 CSS for better search interactivity -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

        <style>
            .report-wrap { max-width: 980px; margin: 0 auto; }
            .report-hero {
                background: linear-gradient(135deg, #0f172a 0%, #111827 55%, #0b1220 100%);
                border-radius: 16px;
                color: #fff;
                padding: 22px 22px;
                box-shadow: 0 10px 25px rgba(0,0,0,.12);
            }
            .report-hero .title { font-size: 18px; font-weight: 700; margin: 0; }
            .report-hero .subtitle { opacity: .85; margin-top: 6px; font-size: 13px; }

            .report-card {
                border-radius: 16px;
                border: 1px solid rgba(15, 23, 42, .10);
                box-shadow: 0 8px 18px rgba(2, 6, 23, .06);
            }
            .report-card .card-header {
                border-top-left-radius: 16px;
                border-top-right-radius: 16px;
                background: #fff;
                border-bottom: 1px solid rgba(15,23,42,.08);
                font-weight: 700;
            }

            .chip {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 6px 10px;
                border-radius: 999px;
                font-size: 12px;
                background: rgba(255,255,255,.10);
                border: 1px solid rgba(255,255,255,.18);
                color: #fff;
            }
            .chip b { font-weight: 700; }

            .help {
                font-size: 12px;
                color: #64748b;
                margin-top: 6px;
            }

            .btn-primary-soft {
                background: #0ea5e9;
                border: 1px solid #0ea5e9;
                color: #fff;
                font-weight: 700;
                border-radius: 12px;
                padding: 10px 14px;
            }
            .btn-primary-soft:hover { filter: brightness(.95); }

            .btn-outline-soft {
                border-radius: 12px;
                padding: 10px 14px;
                font-weight: 700;
            }

            .quick-range .btn {
                border-radius: 999px;
                padding: 6px 10px;
                font-size: 12px;
                margin-right: 6px;
                margin-bottom: 6px;
            }

            .form-control, .custom-select {
                border-radius: 12px !important;
                border: 1px solid rgba(15,23,42,.15);
            }

            .meta-box {
                background: #f8fafc;
                border: 1px dashed rgba(15,23,42,.20);
                border-radius: 14px;
                padding: 12px 14px;
                font-size: 13px;
                color: #0f172a;
            }
            .meta-box .k { color: #64748b; font-size: 12px; }
            .meta-box .v { font-weight: 700; }

            /* Select2 Custom Styles */
            .select2-container .select2-selection--single {
                height: 42px !important;
                border-radius: 12px !important;
                border: 1px solid rgba(15,23,42,.15) !important;
                display: flex;
                align-items: center;
            }
            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 40px !important;
                right: 10px !important;
            }
            .select2-dropdown {
                border-radius: 12px !important;
                border: 1px solid rgba(15,23,42,.15) !important;
                box-shadow: 0 10px 25px rgba(0,0,0,.1) !important;
                padding: 4px;
            }
            .select2-search__field {
                border-radius: 8px !important;
            }
            .select2-results__option {
                color: #0f172a !important; /* Forces dark text in dropdown to prevent white-on-white in dark mode */
            }
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                color: #0f172a !important;
                font-weight: 500;
            }

            /* Active State for Quick Ranges */
            .quick-range .btn.active {
                background-color: #0f172a;
                color: #fff;
                border-color: #0f172a;
                transform: scale(1.02);
            }
        </style>
    @endsection

    @section('content')
    <div class="report-wrap">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p class="mb-0">{{ $message }}</p>
            </div>
        @endif

        @if ($message = Session::get('error'))
            <div class="alert alert-danger">
                <p class="mb-0">{{ $message }}</p>
            </div>
        @endif

        <div class="card report-card">
            <div class="card-header">
                Generador de Reporte de Asistencia
            </div>

            <div class="card-body">
                <form
                    action="{{ route('reporte.asistencia.excel') }}"
                    method="GET"
                    id="formReporteAsistencia"
                    target="_blank"
                >
                    <div class="row">
                        <div class="col-md-6">
                            <label class="mb-1" for="unidad_id">Unidad</label>
                            <select name="unidad_id" id="unidad_id" class="form-control">
                                <option value="" selected>— Selecciona una unidad —</option>
                                <option value="ALL">Todas las unidades</option>
                                <option value="SIN_UNIDAD">Sin unidad / No encontrada</option>

                                @foreach($unidades as $u)
                                    <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                                @endforeach
                            </select>
                            <div class="help">
                                {{-- Se usa <b>workno</b> → <b>tbl_funcionario.clave_empleado</b> y luego <b>id_unidad</b> → <b>tbl_unidades</b>. --}}
                            </div>
                        </div>

                        <div class="col-md-3 mt-3 mt-md-0">
                            <label class="mb-1" for="from">Fecha inicio</label>
                            <input type="date" name="from" id="from" class="form-control"
                                value="{{ request('from') ?? now()->startOfMonth()->toDateString() }}">
                            <div class="help">Incluye desde las 00:00.</div>
                        </div>

                        <div class="col-md-3 mt-3 mt-md-0">
                            <label class="mb-1" for="to">Fecha término</label>
                            <input type="date" name="to" id="to" class="form-control"
                                value="{{ request('to') ?? now()->toDateString() }}">
                            <div class="help">Incluye hasta las 23:59.</div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="quick-range">
                                <div class="help mb-2">Rangos rápidos</div>
                                <button type="button" class="btn btn-outline-secondary" data-range="hoy">Hoy</button>
                                <button type="button" class="btn btn-outline-secondary" data-range="ayer">Ayer</button>
                                <button type="button" class="btn btn-outline-secondary" data-range="semana">Esta semana</button>
                                <button type="button" class="btn btn-outline-secondary" data-range="quincena">Últimos 15 días</button>
                                <button type="button" class="btn btn-outline-secondary" data-range="mes">Este mes</button>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-8">
                            <div class="meta-box">
                                <div class="k">Vista previa (lo que se aplicará)</div>
                                <div class="v" id="previewText">Selecciona una unidad y un rango de fechas.</div>
                                <div class="k mt-2">Nota</div>
                                <div>
                                    Si <b>adscripcion</b> está vacío, se mostrará la <b>unidad</b>. Si no existe funcionario/unidad: <b>SIN ADSCRIPCIÓN</b>.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mt-3 mt-md-0 d-flex align-items-stretch">
                            <div class="w-100 d-flex flex-column justify-content-end">
                                <div class="d-flex gap-2">
                                    <button type="submit" id="btnSubmit" class="btn btn-primary-soft w-100 d-flex align-items-center justify-content-center gap-2 border-0 shadow-sm" style="transition: all 0.2s ease-in-out;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                        <span id="btnSubmitText">Generar Excel</span>
                                    </button>
                                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary btn-outline-soft d-flex align-items-center justify-content-center gap-2 bg-white" style="transition: transform 0.15s ease-in-out;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        Limpiar
                                    </a>
                                </div>

                                <div class="help mt-2">
                                    Se abre en una nueva pestaña.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
    @endsection

    @section('script_content_js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        (function () {
            function fmtDate(d) {
                const pad = (n) => (n < 10 ? '0' + n : '' + n);
                return d.getFullYear() + '-' + pad(d.getMonth()+1) + '-' + pad(d.getDate());
            }

            function setRange(type) {
                const now = new Date();
                let start = new Date(now), end = new Date(now);

                if (type === 'hoy') {
                    // start/end = hoy
                } else if (type === 'ayer') {
                    start.setDate(start.getDate() - 1);
                    end.setDate(end.getDate() - 1);
                } else if (type === 'semana') {
                    // Lunes = 1
                    const day = now.getDay(); // dom=0
                    const diffToMonday = (day === 0 ? 6 : day - 1);
                    start.setDate(now.getDate() - diffToMonday);
                } else if (type === 'quincena') {
                    start.setDate(now.getDate() - 14);
                } else if (type === 'mes') {
                    start = new Date(now.getFullYear(), now.getMonth(), 1);
                }

                document.getElementById('from').value = fmtDate(start);
                document.getElementById('to').value   = fmtDate(end);
                updatePreview();
            }

            function updatePreview() {
                const unidadSel = document.getElementById('unidad_id');
                const unidadTxt = unidadSel.options[unidadSel.selectedIndex] ? unidadSel.options[unidadSel.selectedIndex].text : '';
                const from = document.getElementById('from').value;
                const to = document.getElementById('to').value;

                let u = unidadTxt || '—';
                if (!unidadSel.value) u = '— (sin seleccionar)';

                let warningHtml = '';
                if (from && to && from > to) {
                    warningHtml = '<div class="text-danger mt-2" style="font-size:12.5px; font-weight:600;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>Atención: La fecha de inicio no puede ser mayor a la fecha de término.</div>';
                }

                document.getElementById('previewText').innerHTML =
                    `Unidad: <b>${u}</b> <br> Rango: <b>${from || '—'}</b> al <b>${to || '—'}</b> ${warningHtml}`;

                // Deshabilitar el botón si las fechas son ilógicas
                const btnSubmit = document.getElementById('btnSubmit');
                if (btnSubmit) {
                    btnSubmit.disabled = (from && to && from > to);
                    if (btnSubmit.disabled) {
                        btnSubmit.style.opacity = '0.5';
                    } else {
                        btnSubmit.style.opacity = '1';
                    }
                }
            }

            document.querySelectorAll('[data-range]').forEach(btn => {
                btn.addEventListener('click', function () {
                    // Update Active Style
                    document.querySelectorAll('[data-range]').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    setRange(this.getAttribute('data-range'));
                });
            });

            // Initialize select2 if available
            if (window.$ && $.fn.select2) {
                $('#unidad_id').select2({
                    placeholder: "— Selecciona una unidad —",
                    allowClear: true,
                    width: '100%'
                });
                $('#unidad_id').on('change', function() {
                    updatePreview();
                });
            } else {
                const el = document.getElementById('unidad_id');
                if (el) el.addEventListener('change', updatePreview);
            }

            ['from','to'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.addEventListener('change', updatePreview);
            });

            updatePreview();

            // Validación simple (si ya tienes jquery.validate en tu layout, puedes activarla aquí)
            if (window.$ && $.fn && $.fn.validate) {
                $('#formReporteAsistencia').validate({
                    rules: {
                        unidad_id: { required: true },
                        from: { required: true },
                        to: { required: true }
                    },
                    messages: {
                        unidad_id: { required: 'Por favor selecciona una unidad' },
                        from: { required: 'Por favor ingresa fecha de inicio' },
                        to: { required: 'Por favor ingresa fecha de término' }
                    }
                });
            }

            // Animación de carga al generar excel
            const form = document.getElementById('formReporteAsistencia');
            if(form) {
                form.addEventListener('submit', function(e) {
                    const from = document.getElementById('from').value;
                    const to = document.getElementById('to').value;
                    if(from && to && from > to) {
                        e.preventDefault();
                        return false;
                    }

                    const btn = document.getElementById('btnSubmit');
                    const textSpan = document.getElementById('btnSubmitText');

                    if(btn && textSpan) {
                        const originalText = textSpan.innerText;
                        textSpan.innerText = 'Generando Excel...';
                        btn.style.opacity = '0.8';
                        btn.style.pointerEvents = 'none';

                        // Restauramos el botón después de 3 segundos
                        // (Ya que la descarga "target='_blank'" no recargará esta pestaña)
                        setTimeout(() => {
                            textSpan.innerText = originalText;
                            btn.style.opacity = '1';
                            btn.style.pointerEvents = 'auto';
                        }, 3000);
                    }
                });
            }
        })();
    </script>
@endsection
