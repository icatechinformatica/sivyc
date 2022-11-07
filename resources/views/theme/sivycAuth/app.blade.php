<!DOCTYPE html>
<html lang="es">

<head>
    <title>@yield('title', '')</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link rel="shortcut icon" href="{{ URL::asset('favicon.ico') }}">

    <!-- Google Fonts -->
    <link href="{{asset("css/roboto.css") }}" rel="stylesheet">

    <!-- CSS Global Compulsory -->
    <link rel="stylesheet" href="{{asset("vendor/bootstrap/bootstrap.min.css") }}">
    <link rel="stylesheet" href="{{asset("vendor/bootstrap/offcanvas.css") }}">
    <script src="{{ asset("js/components/iconify.min.js") }}"></script>

    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{asset("fonts/font-awesome/css/font-awesome.min.css") }}">
    <link rel="stylesheet" href="{{asset("vendor/icon-line-pro/style.css") }}">
    <link rel="stylesheet" href="{{asset("vendor/icon-line/css/simple-line-icons.css") }} ">
    <link rel="stylesheet" href="{{asset("vendor/icon-hs/style.css") }}">
    <link rel="stylesheet" href="{{asset("vendor/dzsparallaxer/dzsparallaxer.css") }}">
    <link rel="stylesheet" href="{{asset("vendor/dzsparallaxer/dzsscroller/scroller.css") }}">
    <link rel="stylesheet" href="{{asset("vendor/dzsparallaxer/advancedscroller/plugin.css") }}">
    <link rel="stylesheet" href="{{asset("vendor/animate.css") }}">
    <link rel="stylesheet" href="{{asset("vendor/typedjs/typed.css") }}">
    <link rel="stylesheet" href="{{asset("vendor/hamburgers/hamburgers.min.css") }}">
    <link rel="stylesheet" href="{{asset("vendor/fancybox/jquery.fancybox.css") }}">
    <link rel="stylesheet" href="{{asset("vendor/slick-carousel/slick/slick.css") }}">

    <link rel="stylesheet" href="{{asset("css/unify-core.css") }}">
    <link rel="stylesheet" href="{{asset("css/unify-components.css") }}">
    <link rel="stylesheet" href="{{asset("css/unify-globals.css") }}">

    <link rel="stylesheet" href="{{asset("css/custom.css") }}">
    <link rel="stylesheet" href="{{asset("css/paqueterias.css") }}">

 

</head>

<body>
    <!--HEADER DE  LA PAGINA-->
    @include("theme.sivycAuth.header")
    <!--HEADER DE LA PAGINA FIN-->
    <!--MENU-->
    @include("theme.sivycAuth.menu")
    <!--MENU-->

    <!-- PAGINA -->
    @yield("content")
    <!-- PAGINA FIN -->



    <script src="{{asset("vendor/jquery/jquery.min.js")}}"></script>
    <script src="{{asset("vendor/jquery/jquery.min.js")}}"></script>
    <script src="{{asset("vendor/jquery-migrate/jquery-migrate.min.js")}}"></script>
    <script src="{{asset("js/components/popper.min.js")}}"></script>
    <script src="{{asset("js/components/bootstrap.min.js")}}"></script>

    <script src="{{asset("vendor/hs-megamenu/src/hs.megamenu.js") }}"></script>
    <script src="{{asset("vendor/dzsparallaxer/dzsparallaxer.js") }}"></script>
    <script src="{{asset("vendor/dzsparallaxer/dzsscroller/scroller.js") }}"></script>
    <script src="{{asset("vendor/dzsparallaxer/advancedscroller/plugin.js") }}"></script>
    <script src="{{asset("vendor/fancybox/jquery.fancybox.min.js") }}"></script>
    <script src="{{asset("vendor/slick-carousel/slick/slick.js") }}"></script>
    

    <script src="{{asset("js/hs.core.js") }}"></script>
    <script src="{{asset("vendor/typedjs/typed.min.js") }}"></script>
    <script src="{{asset("js/components/hs.header.js") }}"></script>
    <script src="{{asset("js/helpers/hs.hamburgers.js") }}"></script>
    <script src="{{asset("js/components/hs.dropdown.js") }}"></script>
    <script src="{{asset("js/components/hs.popup.js") }}"></script>
    <script src="{{asset("js/components/hs.carousel.js") }}"></script>
    <script src="{{asset("js/components/hs.go-to.js") }}"></script>
    <script src="{{ asset("js/components/jquery.validate.js") }}"></script>
    <script src="{{ asset("js/components/jquery-ui.js") }}"></script>
    <link rel="stylesheet" href="{{asset("css/jquery-ui.css") }}">
</body>

</html>