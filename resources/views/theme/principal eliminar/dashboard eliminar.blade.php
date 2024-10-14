<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <meta name="description" content="This is an example dashboard created using build-in elements and components.">
    <meta name="msapplication-tap-highlight" content="no">
    <title>@yield('title', '')</title>
    <!--LINKS CSS-->
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
    <link rel="stylesheet" href="{{asset("css/main.css") }}">
    @yield('csscontent')
</head>
<body>
    <div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
        <div class="app-header header-shadow">
            @include('theme.principal.shared.header')
        </div>
        <div class="app-main">
            <!--PRINCIPAL-->
                <!--SIDEBAR-->
                <div class="app-sidebar sidebar-shadow">
                    @include('theme.principal.shared.sidebar')
                </div>
                <!--SIDEBAR END-->
                <div class="app-main__outer">
                    <div class="app-main__inner">
                        <div class="app-page-title">
                            @include('theme.principal.shared.title-grapper')
                        </div>
                        <!--CONTENIDO PRINCIPAL-->
                        @yield('content')
                        <!--CONTENIDO PRINCIPAL END-->
                    </div>
                    <!--FOOTER-->
                    <div class="app-wrapper-footer">
                        <div class="app-footer">
                            @include('theme.principal.shared.footer-wrapper')
                        </div>
                    </div>
                    <!--FOOTER END-->
                </div>
            <!--PRINCIPAL END-->
        </div>
    </div>
    <script src="{{ asset("js/main.js") }}"></script>
    @yield('content_js')
</body>
</html>
