<!DOCTYPE html>
<html lang="es">

    <head>
        <title>@yield('title', '')</title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <link rel="shortcut icon" href="favicon.ico">

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Roboto|Roboto+Slab" rel="stylesheet">

        <!-- CSS Global Compulsory -->
        <link rel="stylesheet" href="{{asset("vendor/bootstrap/bootstrap.min.css") }}">
        <link rel="stylesheet" href="{{asset("vendor/bootstrap/offcanvas.css") }}">
        <script src="https://code.iconify.design/1/1.0.3/iconify.min.js"></script>

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

    </head>

    <body>
        <!--HEADER DE  LA PAGINA-->
        @include("theme.sivyc.header")
        <!--HEADER DE LA PAGINA FIN-->
        <!--MENU-->
        @include("theme.sivyc.menu")
        <!--MENU-->

        <!-- PAGINA -->
        @yield("content")
        <!-- PAGINA FIN -->


        <!-- FOOTER PORTAL DE GOBIERNO -->
        @include("theme.sivyc.footer")


        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
        <script src="{{asset("vendor/jquery/jquery.min.js")}}"></script>
        <script src="{{asset("vendor/jquery-migrate/jquery-migrate.min.js")}}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

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
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.js"></script>
            <script type="text/javascript">
                $(document).on('ready', function () {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.HSCore.components.HSHeader.init($('#js-header'));
						    	$.HSCore.helpers.HSHamburgers.init('.hamburger');

							    $('.js-mega-menu').HSMegaMenu({
							      event: 'hover',
							      pageContainer: $('.container'),
							      breakpoint: 991
							    });

							    $.HSCore.components.HSDropdown.init($('[data-dropdown-target]'), {
							      afterOpen: function () {
							        $(this).find('input[type="search"]').focus();
							      }
							    });

							    $.HSCore.components.HSPopup.init('.js-fancybox');
							    $.HSCore.components.HSCarousel.init('.js-carousel');
							    $.HSCore.components.HSGoTo.init('.js-go-to');
                                $("#search_").click(function(e){
                                    e.preventDefault();
                                    $.ajax({
                                        type:'POST',
                                        url:'/pago/fill',
                                        data: { numero_contrato: $('#numero_contrato').val()},
                                        success: function(data){
                                           // alert(data.nombre);
                                           nombre = data.nombre + " " + data.apellido_paterno + " " + data.apellido_materno
                                            $('#numero_control').val(data.id)
                                            $('#nombre_instructor').val(nombre)
                                        },
                                    });
                                });
                                $("#mod_").click(function(e){
                                    e.preventDefault();
                                    $.ajax({
                                        success: function(){
                                            $('#numero_control').prop("disabled", false)
                                            $('#nombre_curso').prop("disabled", false)
                                            $('#nombre_instructor').prop("disabled", false)
                                            $('#numero_contrato').prop("disabled",false)
                                            $('#clave_grupo').prop("disabled",false)
                                            $('#unidad_cap').prop("disabled",false)
                                            $('#tipo_pago').prop("disabled",false)
                                            $('#monto_pago').prop("disabled",false)
                                            $('#iva').prop("disabled",false)
                                            $('#numero_pago').prop("disabled",false)
                                            $('#fecha_pago').prop("disabled",false)
                                            $('#concepto').prop("disabled",false)
                                            $('#nombre_solicita').prop("disabled",false)
                                            $('#nombre_autoriza').prop("disabled",false)
                                            $('#reacd02').prop("disabled",false)
                                        }
                                    });
                                });

                });
            </script>
    </body>

</html>
