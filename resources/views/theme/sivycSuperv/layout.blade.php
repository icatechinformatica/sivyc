<!DOCTYPE html>
<html lang="es">

    <head>
        <title>@yield('title', '')</title>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <link rel="shortcut icon" href="{{ URL::asset('favicon.ico') }}">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
        <!-- Google Fonts Roboto -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">

        <!-- CSS Global Compulsory -->
        <link rel="stylesheet" href="{{asset("vendor/bootstrap/bootstrap.min.css") }}">
        <link rel="stylesheet" href="{{asset("vendor/bootstrap/offcanvas.css") }}">


        <!-- CSS Implementing Plugins -->
        <link rel="stylesheet" href="{{asset("fonts/font-awesome/css/font-awesome.min.css") }}">
        <link rel="stylesheet" href="{{asset("vendor/icon-line-pro/style.css") }}">
        <link rel="stylesheet" href="{{asset("vendor/icon-line/css/simple-line-icons.css") }} ">
        <link rel="stylesheet" href="{{asset("vendor/icon-hs/style.css") }}">
        <link rel="stylesheet" href="{{asset("vendor/dzsparallaxer/dzsparallaxer.css") }}">
        <link rel="stylesheet" href="{{asset("vendor/dzsparallaxer/dzsscroller/scroller.css") }}">
        <link rel="stylesheet" href="{{asset("vendor/dzsparallaxer/advancedscroller/plugin.css") }}">
        <link rel="stylesheet" href="{{asset("vendor/animate.css") }}">
        <link rel="stylesheet" href="{{asset("vendor/hamburgers/hamburgers.min.css") }}">

        <link rel="stylesheet" href="{{asset("css/unify-core.css") }}">
        <link rel="stylesheet" href="{{asset("css/unify-components.css") }}">
        <link rel="stylesheet" href="{{asset("css/unify-globals.css") }}">

        <link rel="stylesheet" href="{{asset("css/custom.css") }}">
        <link rel="stylesheet" href="{{asset("css/mdb.min.css") }}">
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.css">


    </head>

    <body>
        <!--HEADER DE  LA PAGINA-->
        @include("theme.sivyc.header")
        <!--HEADER DE LA PAGINA FIN-->
        

        <!-- PAGINA -->
        @yield("content")
        <!-- PAGINA FIN -->


        <!-- FOOTER PORTAL DE GOBIERNO -->
        @include("theme.sivyc.footer")



        <script src="{{asset("vendor/jquery/jquery.min.js")}}"></script>
        <script src="{{asset("vendor/jquery-migrate/jquery-migrate.min.js")}}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

        <script src="{{asset("vendor/hs-megamenu/src/hs.megamenu.js") }}"></script>
        <script src="{{asset("vendor/dzsparallaxer/dzsparallaxer.js") }}"></script>
        <script src="{{asset("vendor/dzsparallaxer/dzsscroller/scroller.js") }}"></script>
        <script src="{{asset("vendor/dzsparallaxer/advancedscroller/plugin.js") }}"></script>

        <script src="{{asset("js/hs.core.js") }}"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="{{ asset("js/components/filter-table.js") }}"></script>
        <script src="{{ asset("js/validate/orlandoValidate.js") }}"></script>
        <script src="{{ asset("js/validate/danielValidate.js") }}"></script>
        <script src="{{ asset("js/validate/orlandoBotones.js") }}"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.js"></script>
        <script src="{{ asset("js/validate/adrianValidate.js") }}"></script>
        <script src="{{ asset("js/validate/validateConvenio.js") }}"></script>
        <script src="{{ asset("js/validate/metodos.js") }}"></script>
        <script src="{{ asset("js/mdb.min.js") }}"></script>
        <script src="https://code.iconify.design/1/1.0.3/iconify.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.js"></script>

        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <!--numeros a letras-->
        <script src="{{ asset("js/validate/conversor.js") }}"></script>
        <script src="{{ asset("js/validate/numberTostring.js") }}"></script>
        <script src="{{ asset("js/scripts/datepicker-es.js") }}"></script>
        <script src="{{ asset("js/validate/sid-mod.js") }}"></script>
    </body>

</html>
