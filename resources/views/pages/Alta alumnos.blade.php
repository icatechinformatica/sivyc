<section class="container g-py-40 g-pt-40 g-pb-0">
      
    <div class="content mt-3">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-lg-8">
                    <form action="{{ url('/noticias-save') }}" method="post" id="registercomunicado" enctype="multipart/form-data">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Agregar Nuevo</strong>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="cc-payment" class="control-label mb-1">Titulo</label>
                                    <input id="titulo" name="titulo" type="text" class="form-control" aria-required="true">
                                </div>
                                <div class="form-group">
                                    <label for="cc-payment" class="control-label mb-1">Localidad</label>
                                    <input id="localizacion" name="localizacion" type="text" class="form-control" aria-required="true">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fa fa-paper-plane"></i> Agregar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
  </section>
  <br> 
  
  <section class="container g-py-40 g-pt-40 g-pb-0">
  
  <form class="needs-validation" novalidate>
    <div class="form-row">
      <div class="col-md-4 mb-3">
        <label for="validationCustom01">Nombre (s)</label>
        <input type="text" class="form-control" id="validationCustom01" placeholder="First name" value="Mark" required>
        <div class="valid-feedback">
          Looks good!
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <label for="validationCustom02">Apellidos</label>
        <input type="text" class="form-control" id="validationCustom02" placeholder="Last name" value="Otto" required>
        <div class="valid-feedback">
          Looks good!
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <label for="validationCustomUsername">Correo Electronico</label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text" id="inputGroupPrepend">@</span>
          </div>
          <input type="text" class="form-control" id="validationCustomUsername" placeholder="Username" aria-describedby="inputGroupPrepend" required>
          <div class="invalid-feedback">
            Please choose a username.
          </div>
        </div>
      </div>
    </div>
    <div class="form-row">
      <div class="col-md-6 mb-3">
        <label for="validationCustom03">Direccion</label>
        <input type="text" class="form-control" id="validationCustom03" placeholder="City" required>
        <div class="invalid-feedback">
          Please provide a valid city.
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <label for="validationCustom04">Telefono</label>
        <input type="text" class="form-control" id="validationCustom04" placeholder="State" required>
        
      </div>
      <div class="col-md-3 mb-3">
        <label for="validationCustom05">Zip</label>
        <input type="text" class="form-control" id="validationCustom05" placeholder="Zip" required>
        <div class="invalid-feedback">
          Please provide a valid zip.
        </div>
      </div>
    </div>
    <div class="form-group">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="" id="invalidCheck" required>
        <label class="form-check-label" for="invalidCheck">
          Agree to terms and conditions
        </label>
        <div class="invalid-feedback">
          You must agree before submitting.
        </div>
      </div>
    </div>
    <button class="btn btn-primary" type="submit">Submit form</button>
  </form>
  
  <script>
  // Example starter JavaScript for disabling form submissions if there are invalid fields
  (function() {
    'use strict';
    window.addEventListener('load', function() {
      // Fetch all the forms we want to apply custom Bootstrap validation styles to
      var forms = document.getElementsByClassName('needs-validation');
      // Loop over them and prevent submission
      var validation = Array.prototype.filter.call(forms, function(form) {
        form.addEventListener('submit', function(event) {
          if (form.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
          }
          form.classList.add('was-validated');
        }, false);
      });
    }, false);
  })();
  </script>
  </section>
  @stop
  
  