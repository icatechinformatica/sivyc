{{-- Carga de archivos de requisitos de instructor --}}
<div>
    <h3 class="font-weight-bold">Requisitos</h3>
    <table class="table table-borderless table-responsive-md" id="table-perfprof2">
        <tbody>
            <tr >
                <td id="center" width="200px">
                    <H5><small><small>Comprobante Domicilio</small></small></H5>
                </td>
                <td id="center" width="50px">
                    <i  class="far fa-file-pdf  fa-2x fa-lg text-danger"></i>
                </td>
                <td id="center" width="160px">
                    <label class='onpoint' for="arch_domicilio" required>
                        <a class="btn mr-sm-4 mt-3 btn-sm">
                            Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                        </a>
                        <input style='display:none;' type="file" accept="application/pdf" id="arch_domicilio" name="arch_domicilio" placeholder="Archivo PDF">
                        <br><span id="imageName"></span>
                    </label>
                </td>
                <td id="center" width="60px">
                    <H5><small><small>CURP</small></small></H5>
                </td>
                <td id="center" width="50px">
                    <i  class="far fa-file-pdf  fa-2x fa-lg text-danger"></i>
                </td>
                <td id="center" width="160px">
                    <label class='onpoint' for="arch_curp">
                        <a class="btn mr-sm-4 mt-3 btn-sm">
                            Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                        </a>
                        <input style='display:none;' type="file" accept="application/pdf" class="form-control" id="arch_curp" name="arch_curp" placeholder="Archivo PDF">
                        <br><span id="imageName2"></span>
                    </label>
                </td>
                <td id="center" width="180px">
                    <H5><small><small>Comprobante Bancario</small></small></H5>
                </td>
                <td id="center" width="50px">
                    <i  class="far fa-file-pdf  fa-2x fa-lg text-danger"></i>
                </td>
                <td id="center" width="160px">
                    <label class='onpoint' for="arch_banco">
                        <a class="btn mr-sm-4 mt-3 btn-sm">
                            Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                        </a>
                        <input style='display:none;' type="file" accept="application/pdf" class="form-control" id="arch_banco" name="arch_banco" placeholder="Archivo PDF">
                        <br><span id="imageName3"></span>
                    </label>
                </td>
                <td id="center" width="100px">
                    <H5><small><small>Fotografía</small></small></H5>
                </td>
                <td id="center" width="50px">
                    <i  class="far fa-file-pdf  fa-2x fa-lg text-danger"></i>
                </td>
                <td id="center" width="160px">
                    <label class='onpoint' for="arch_foto">
                        <a class="btn mr-sm-4 mt-3 btn-sm">
                            Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                        </a>
                        <input style='display:none;' type="file" accept="image/jpeg" class="form-control" id="arch_foto" name="arch_foto" placeholder="Archivo PDF">
                        <br><span id="imageName4"></span>
                    </label>
                </td>
            </tr>
            <tr >
                <td id="center" width="200px">
                    <H5><small><small>Acta de nacimiento</small></small></H5>
                </td>
                <td id="center" width="50px">
                    <i  class="far fa-file-pdf  fa-2x fa-lg text-danger"></i>
                </td>
                <td id="center" width="160px">
                    <label class='onpoint' for="arch_id">
                        <a class="btn mr-sm-4 mt-3 btn-sm">
                            Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                        </a>
                        <input style='display:none;' type="file" accept="application/pdf" class="form-control" id="arch_id" name="arch_id" placeholder="Archivo PDF">
                        <br><span id="imageName5"></span>
                    </label>
                </td>
                <td id="center" width="50px">
                    <H5><small><small>RFC</small></small></H5>
                </td>
                <td id="center" width="50px">
                    <i  class="far fa-file-pdf  fa-2x fa-lg text-danger"></i>
                </td>
                <td id="center" width="160px">
                    <label class='onpoint' for="arch_rfc">
                        <a class="btn mr-sm-4 mt-3 btn-sm">
                            Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                        </a>
                        <input style='display:none;' type="file" accept="application/pdf" class="form-control" id="arch_rfc" name="arch_rfc" placeholder="Archivo PDF">
                        <br><span id="imageName6"></span>
                    </label>
                </td>
                <td id="center" width="180px">
                    <H5><small><small>Comprobante Estudios</small></small></H5>
                </td>
                <td id="center" width="50px">
                    <i  class="far fa-file-pdf  fa-2x fa-lg text-danger"></i>
                </td>
                <td id="center" width="160px">
                    <label class='onpoint' for="arch_estudio">
                        <a class="btn mr-sm-4 mt-3 btn-sm">
                            Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                        </a>
                        <input style='display:none;' type="file" accept="application/pdf" class="form-control" id="arch_estudio" name="arch_estudio" placeholder="Archivo PDF">
                        <br><span id="imageName7"></span>
                    </label>
                </td>
                <td id="center" width="100px">
                    <H5><small><small>Curriculum</small></small></H5>
                </td>
                <td id="center" width="50px">
                    <i  class="far fa-file-pdf  fa-2x fa-lg text-danger"></i>
                </td>
                <td id="center" width="160px">
                    <label class='onpoint' for="arch_curriculum_personal">
                        <a class="btn mr-sm-4 mt-3 btn-sm">
                            Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                        </a>
                        <input style='display:none;' type="file" accept="application/pdf" class="form-control" id="arch_curriculum_personal" name="arch_curriculum_personal" placeholder="Archivo PDF">
                        <br><span id="imageName8"></span>
                    </label>
                </td>
            </tr>
        </tbody>
    </table>
</div>
