<!-- Creado por Daniel Méndez-->
@extends('theme.sivyc.layout')

@section('title', 'Inicio | Sivyc Icatech')

@section('content')
<div class="container g-pt-50">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card-deck">
                <div class="card">
                  <img class="card-img-top" src="{{asset("img/blade_icons/users.png") }}" alt="Perfil de Usuario" style="height: 170px;width: 100%;display: block;">
                  <div class="card-body">
                    <h5 class="card-title">Perfil de Usuario</h5>
                    <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                    <p class="card-text"><small class="text-muted">El perfil pertenece a {{ Auth::user()->name }}</small></p>
                  </div>
                </div>
                <div class="card">
                  <img class="card-img-top" src="..." alt="Card image cap">
                  <div class="card-body">
                    <h5 class="card-title">Card title</h5>
                    <p class="card-text">This card has supporting text below as a natural lead-in to additional content.</p>
                    <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
                  </div>
                </div>
                <div class="card">
                  <img class="card-img-top" src="..." alt="Card image cap">
                  <div class="card-body">
                    <h5 class="card-title">Card title</h5>
                    <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This card has even longer content than the first to show that equal height action.</p>
                    <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
                  </div>
                </div>
            </div>
        </div>
    </div>
    <br>
</div>
@endsection
