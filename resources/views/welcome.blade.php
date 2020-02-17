@extends("theme.sivyc.layout")
<!--llamar la plantilla -->
@section('title', 'Indice Principal | Sivyc Icatech')
<!--tituto de la pestaña -->
@section('content')
<section class="container g-pt-150 g-pb-50">
    <div class="row">
        <hr class="g-brd-gray-light-v4">
    </div>
    <div class="row">
        <div class="col-lg-12 g-mb-10">
            <form class="g-brd-around g-brd-gray-light-v4 g-pa-30 g-mb-30 g-bg-secondary">
                <div class="form-group">
                    <div class="input-group g-brd-gray-light-v2">
                        <input type="text" class="form-control form-control-md rounded-0">
                        <div class="input-group-append">
                            <button class="btn btn-md u-btn-primary rounded-0">
                                BUSQUEDAS
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@stop
