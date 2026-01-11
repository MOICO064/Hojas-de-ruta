<div class="modal fade" id="modalAnulacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header ">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i data-feather="alert-triangle"></i>
                    Anular Registro
                </h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="formAnulacion">
                    @csrf

                    <!-- Campos ocultos -->
                    <input type="hidden" name="tipo">
                    <input type="hidden" name="id">

                    <div class=" mb-3">
                        <label class="form-label fw-bold">
                            Justificación de la anulación <span class="text-danger">*</span>
                        </label>

                        <textarea name="justificacion" class="form-control" rows="4"
                            placeholder="Explique claramente el motivo de la anulación..."
                            oninput="this.value=this.value.toUpperCase()"></textarea>
                    </div>

                    <div class="alert alert-warning mb-0">
                        ⚠️ Esta acción es irreversible y quedará registrada.
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button type="submit" form="formAnulacion" class="btn btn-danger">
                    Confirmar anulación
                </button>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="modalVerAnulacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header ">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i data-feather="alert-triangle"></i>
                    Detalle de Anulación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <p><strong>Tipo de registro:</strong> <span id="anulacion-tipo"></span></p>
                <p><strong>Motivo:</strong> <span id="anulacion-justificacion"></span></p>
                <p><strong>Usuario:</strong> <span id="anulacion-usuario"></span></p>
                <p><strong>Fecha:</strong> <span id="anulacion-fecha"></span></p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cerrar
                </button>
            </div>

        </div>
    </div>
</div>