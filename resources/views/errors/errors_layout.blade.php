<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error @yield('title')</title>
    <!-- CSS Global Compulsory -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{asset('css/icon-hs/style.css') }}">
    <link rel="stylesheet" href="{{asset('css/unify-globals.css') }}">
    <link rel="stylesheet" href="{{asset('css/errors/errors-card.css') }}">
</head>

<body class="d-flex flex-column min-vh-100">
    <header id="js-header" class="u-header u-header--sticky-top u-header--toggle-section" data-header-fix-moment="300">
        <div class="u-header__section u-header__section--light  g-transition-0_3" data-header-fix-moment-exclude=""
            data-header-fix-moment-classes="u-shadow-v18 g-py-0" style="background-color: #333">
            <nav class="navbar navbar-expand-lg navbar-dark g-pa-0 g-pt-4 g-pb-4">
                <div class="container">
                    <a target="_blank" href="https://chiapas.gob.mx/" class="navbar-brand g-text-underline--hover"><img
                            src="https://chiapas.gob.mx/assets/logo/escudo-icono.png" alt=""></a>
                    
                    <button
                        class="navbar-toggler ms-auto"
                        type="button" aria-label="Toggle navigation" aria-expanded="false" aria-controls="navBar"
                        data-bs-toggle="collapse" data-bs-target="#navBar">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    
                    <div class="collapse navbar-collapse" id="navBar">
                        <div class="d-lg-none mb-3">
                            <a target="_blank" href="https://chiapas.gob.mx/"
                                class="text-white text-decoration-none small">chiapas<span
                                    class="text-white-50">.gob.mx</span></a>
                        </div>
                        <div class="d-none d-lg-block me-auto">
                            <a target="_blank" href="https://chiapas.gob.mx/"
                                class="g-color-white-opacity-0_9 g-font-size-16 g-font-weight-300 g-font-secondary g-text-underline--hover text-decoration-none">chiapas<span
                                    class="g-color-white-opacity-0_6">.gob.mx</span></a>
                        </div>
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item"><a target="_blank"
                                    href="https://chiapas.gob.mx/servicios-por-entidad"
                                    class="nav-link text-white">Trámites</a>
                            </li>
                            <li class="nav-item"><a target="_blank" href="https://chiapas.gob.mx/gobierno"
                                    class="nav-link text-white">Gobierno</a>
                            </li>
                            <li class="nav-item"><a target="_blank" href="https://chiapas.gob.mx/participa"
                                    class="nav-link text-white">Participa</a>
                            </li>
                            <li class="nav-item"><a target="_blank"
                                    href="http://gubernatura.transparencia.chiapas.gob.mx/"
                                    class="nav-link text-white">Transparencia</a>
                            </li>
                            <li class="nav-item"><a target="_blank" href="https://chiapas.gob.mx/busquedas"
                                    class="nav-link text-white"><i class="fa fa-search"></i></a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>
    {{-- <section class="dzsparallaxer auto-init height-is-based-on-content use-loading mode-scroll dzsprx-readyall"
        data-options='{direction: "normal", settings_mode_oneelement_max_offset: "150"}' style="height: 50px">
    </section> --}}

    <main class="flex-grow-1">
        {{-- ? Contenido --}}
        @yield('content')
    </main>

    <div class="g-bg-black-opacity-0_8 g-color-white-opacity-0_8 g-py-20 mt-auto" style="background-color: #009884 !important;">
        <div class="container">
            <div class="row">
                <div class="col-lg-2 col-md-6 g-mb-40 g-mb-0--lg d-none d-sm-none d-md-none d-lg-block d-xl-block">
                    <a href="https://icatech.gob.mx" class="navbar-brand g-text-underline--hover">
                        <img class="img-fluid" src="{{asset('img/icatech-blanco.png') }}" alt="ICATECH">
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 g-mb-40 g-mb-0--lg d-none d-sm-none d-md-none d-lg-block d-xl-block">
                    <div class="u-heading-v3-1 g-brd-white-opacity-0_3 g-mb-25">
                        <h2 class="u-heading-v3__title h6 g-brd-primary g-font-size-14 g-color-white g-font-weight-400">
                            Portal del Gobierno</h2>
                    </div>
                    <nav class="text-uppercase1">
                        <ul class="list-unstyled g-mt-minus-10 mb-0">
                            <li class="g-pos-rel g-brd-bottom g-brd-white-opacity-0_1 g-py-3"><a
                                    class="g-color-white-opacity-0_8 g-color-white--hover">Gobernador Eduardo Ramirez
                                    Aguilar</a><i class="fa fa-angle-right g-absolute-centered--y g-right-0"></i></li>
                            <li class="g-pos-rel g-brd-bottom g-brd-white-opacity-0_1 g-py-3"><a target="_blank"
                                    class="g-color-white-opacity-0_8 g-color-white--hover"
                                    href="https://www.chiapas.gob.mx/organismos/">Dependencia y Organismos Público</a><i
                                    class="fa fa-angle-right g-absolute-centered--y g-right-0"></i></li>
                            <li class="g-pos-rel g-brd-bottom g-brd-white-opacity-0_1 g-py-3"><a target="_blank"
                                    class="g-color-white-opacity-0_8 g-color-white--hover"
                                    href="https://www.chiapas.gob.mx/gobierno/">Municipios del Estado</a><i
                                    class="fa fa-angle-right g-absolute-centered--y g-right-0"></i></li>
                        </ul>
                    </nav>
                </div>
                <div class="col-lg-3 col-md-6 g-mb-40 g-mb-0--lg d-none d-sm-none d-md-none d-lg-block d-xl-block">
                    <div class="u-heading-v3-1 g-brd-white-opacity-0_3 g-mb-25">
                        <h2 class="u-heading-v3__title h6 g-brd-primary">Recursos</h2>
                    </div>
                    <nav class="text-uppercase1">
                        <ul class="list-unstyled g-mt-minus-10 mb-0">
                            <li class="g-pos-rel g-brd-bottom g-brd-white-opacity-0_1 g-py-3"><a target="_blank"
                                    class="g-color-white-opacity-0_8 g-color-white--hover"
                                    href="https://icatech.gob.mx/normatividad">Normatividad</a><i
                                    class="fa fa-angle-right g-absolute-centered--y g-right-0"></i></li>
                            <li class="g-pos-rel g-brd-bottom g-brd-white-opacity-0_1 g-py-3"><a target="_blank"
                                    class="g-color-white-opacity-0_8 g-color-white--hover"
                                    href="https://icatech.transparencia.chiapas.gob.mx">Transparencia ICATECH</a><i
                                    class="fa fa-angle-right g-absolute-centered--y g-right-0"></i></li>
                            <li class="g-pos-rel g-brd-bottom g-brd-white-opacity-0_1 g-py-3"><a target="_blank"
                                    class="g-color-white-opacity-0_8 g-color-white--hover"
                                    href="https://icatech.gob.mx/avisos-de-privacidad">Avisos de Privacidad</a><i
                                    class="fa fa-angle-right g-absolute-centered--y g-right-0"></i></li>
                        </ul>
                    </nav>
                </div>
                <div class="col-lg-4 col-md-6 d-none d-sm-none d-md-none d-lg-block d-xl-block">
                    <div class="u-heading-v3-1 g-brd-white-opacity-0_3 g-mb-25">
                        <h2 class="u-heading-v3__title h6 g-brd-primary g-font-size-14 g-color-white g-font-weight-400">
                            Contáctanos</h2>
                    </div>
                    <address class="g-bg-no-repeat g-line-height-2 g-mt-minus-4">
                        Av. Barrio San Jacinto N° 154, entre calle Pájaros y calle Conejos, Fracc. El Diamante C.P.
                        29059 Tuxtla Gutiérrez<br>
                        Conmutador: (961) 612 1621 <br> Correo: icatech@icatech.chiapas.gob.mx
                    </address>
                    <div class="col-md-12 align-self-center">
                        <ul class="list-inline text-center text-md-end mb-0">
                            <li class="list-inline-item g-mr-10"><a
                                    class="u-icon-v3 u-icon-size--xs g-bg-white-opacity-0_1 g-bg-white-opacity-0_2--hover g-color-white-opacity-0_6"
                                    href="https://www.facebook.com/ICATchiapas" target="_blank"><i
                                        class="fab fa-facebook-f"></i></a></li>
                            <li class="list-inline-item g-mr-10"><a
                                    class="u-icon-v3 u-icon-size--xs g-bg-white-opacity-0_1 g-bg-white-opacity-0_2--hover g-color-white-opacity-0_6"
                                    href="https://www.instagram.com/icatechchiapas/" target="_blank"><i
                                        class="fab fa-instagram"></i></a></li>
                            <li class="list-inline-item g-mr-10"><a
                                    class="u-icon-v3 u-icon-size--xs g-bg-white-opacity-0_1 g-bg-white-opacity-0_2--hover g-color-white-opacity-0_6"
                                    href="https://twitter.com/IcatechOficial" target="_blank"><i
                                        class="fab fa-twitter"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <footer class="g-bg-black-opacity-0_9 g-color-white-opacity-0_8 g-py-20">
        <div class="container">
            <div class="row">
                <div class="col-md-8 text-center text-md-start g-mb-15 g-mb-0--md">
                    <div class="d-lg-flex">
                        <small class="d-block g-font-size-default g-mr-10 g-mb-10 g-mb-0--md">Gobierno del Estado de
                            Chiapas</small>
                    </div>
                </div>

                <div class="col-md-4 align-self-center">
                    <ul class="list-inline text-center text-md-right mb-0">
                        <li class="list-inline-item g-mx-10" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Facebook">
                            <a href="https://www.facebook.com/gobiernodechiapas"
                                class="g-color-white-opacity-0_5 g-color-white--hover"><i
                                    class="fab fa-facebook"></i></a>
                        </li>
                        <li class="list-inline-item g-mx-10" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Twitter">
                            <a href="https://x.com/ChiapasGobierno"
                                class="g-color-white-opacity-0_5 g-color-white--hover"><i
                                    class="fab fa-twitter"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JavaScript Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>