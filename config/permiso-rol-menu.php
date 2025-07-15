<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Layout por defecto del paquete
    |--------------------------------------------------------------------------
    |
    | Este es el layout que se utilizará por defecto para las vistas del paquete.
    | Puedes cambiarlo para usar tu propio layout personalizado.
    |
    */
    'layout' => 'theme.sivyc_admin.layout',

    /*
    |--------------------------------------------------------------------------
    | Vista por defecto del menú
    |--------------------------------------------------------------------------
    |
    | Esta es la vista que se utilizará por defecto para renderizar el menú
    | dinámico. Puedes cambiarla para usar tu propia vista personalizada.
    |
    */
    'navbar_view' => 'permiso-rol-menu::navbar',

    /*
    |--------------------------------------------------------------------------
    | Aplicar view composer automáticamente
    |--------------------------------------------------------------------------
    |
    | Si está habilitado, el paquete aplicará automáticamente el view composer
    | del menú a las vistas especificadas en 'auto_compose_views'.
    |
    */
    'auto_compose' => true,

    /*
    |--------------------------------------------------------------------------
    | Vistas que recibirán automáticamente el view composer
    |--------------------------------------------------------------------------
    |
    | Lista de vistas que recibirán automáticamente la variable $menuDinamico
    | a través del view composer.
    |
    */
    'auto_compose_views' => [
        'theme.sivyc.menuDinamico',
        'vendor.permiso-rol-menu.navbar',
        'layouts.navbar',
        'permiso-rol-menu::navbar'
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de la tabla de usuarios
    |--------------------------------------------------------------------------
    |
    | Configuración para la tabla de usuarios que se utilizará en las relaciones
    | con roles y permisos.
    |
    */
    'user_table' => 'tblz_usuarios',
    'user_model' => \App\Models\User::class,
];
