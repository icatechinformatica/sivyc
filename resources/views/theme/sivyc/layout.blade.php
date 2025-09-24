<!DOCTYPE html>
<html lang="es">
    <head>
        <title>@yield('title', '')</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="shortcut icon" href="{{ asset('icon/favicon.ico') }}" type="image/x-icon">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{asset('css/all.css') }}">
        <!-- Google Fonts Roboto -->
        <link rel="stylesheet" href="{{asset('css/roboto.css') }}">

        <!-- CSS Global Compulsory -->
        <link rel="stylesheet" href="{{asset('vendor/bootstrap/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{asset('css/icon-hs/style.css') }}">


        <link rel="stylesheet" href="{{asset('css/unify-core.css') }}">
        <link rel="stylesheet" href="{{asset('css/unify-components.css') }}">
        <link rel="stylesheet" href="{{asset('css/unify-globals.css') }}">
        <link rel="stylesheet" href="{{asset('css/custom.css') }}">
        <link rel="stylesheet" href="{{asset('css/mdb.min.css') }}">
        <link rel="stylesheet" href="{{asset('css/jquery-ui.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.dataTables.min.css') }}">

        @yield('content_script_css')
        @stack('content_css_sign')
    </head>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-NMXJKNRN');</script>
    <!-- End Google Tag Manager -->

    <body>
        <!--HEADER DE  LA PAGINA-->
        @include('theme.sivyc.header')
        <!--HEADER DE LA PAGINA FIN-->
        <!--MENU-->
        @include('theme.sivyc.menu')
        <!--MENU-->

        <!-- PAGINA -->
        @yield('content')
        <!-- PAGINA FIN -->


        <!-- FOOTER PORTAL DE GOBIERNO -->
        @include('theme.sivyc.footer')



        <script src="{{asset('vendor/jquery/jquery.min.js')}}"></script>
        <script src="{{asset('vendor/jquery-migrate/jquery-migrate.min.js')}}"></script>
        <script src="{{asset('js/components/popper.min.js')}}"></script>
        <script src="{{asset('js/components/bootstrap.min.js')}}"></script>
        {{--EN DEPURACION --}}
        <script src="{{asset('vendor/hs-megamenu/src/hs.megamenu.js') }}"></script>
        <script src="{{asset('vendor/dzsparallaxer/dzsparallaxer.js') }}"></script>
        <script src="{{asset('vendor/dzsparallaxer/dzsscroller/scroller.js') }}"></script>
        <script src="{{asset('vendor/dzsparallaxer/advancedscroller/plugin.js') }}"></script>

        <script src="{{asset('js/hs.core.js') }}"></script>

        <script src="{{ asset('js/components/jquery.validate.js') }}"></script>
        <script src="{{ asset('js/components/jquery-ui.js') }}"></script>
        <script src="{{ asset('js/components/filter-table.js') }}"></script>
        <script src="{{ asset('js/components/additional-methods.js') }}"></script>
        <script src="{{ asset('js/validate/metodos.js') }}"></script>
        <script src="{{ asset('js/mdb.min.js') }}"></script>
        <script src="{{ asset('js/components/iconify.min.js') }}"></script>
        <script type="text/javascript" charset="utf8" src="{{ asset('js/components/jquery.dataTables.js') }}"></script>

        <script type="text/javascript" src="{{ asset('js/components/jquery-ui.min.js') }}"></script>

        <script src="{{ asset('js/validate/conversor.js') }}"></script>
        <script src="{{ asset('js/validate/numberTostring.js') }}"></script>
        <script src="{{ asset('js/scripts/datepicker-es.js') }}"></script>


        @yield('script_content_js')
        @stack('script_sign')
    </body>

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NMXJKNRN"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

</html>
