let destinatarioIndex = 1;

function renderUnidadesOptions() {
    let options = '<option value="">-- Seleccione Unidad --</option>';

    window.unidades.forEach(u => {
        options += `<option value="${u.id}">${u.nombre}</option>`;
    });

    return options;
}

function initSelects(container) {
    container.find(".unidad-destino, .funcionario").select2({
              theme:"bootstrap-5",
        placeholder: "-- Seleccione --",
        width: "100%",
        allowClear: true,
    });
}

initSelects($("#destinatarios-wrapper"));

$("#addDestinatario").on("click", function () {

    let html = `
        <div class="destinatario-item border rounded p-3 mb-3">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Unidad Destino <small
                                                class="text-danger">*</small></label>
                    <select name="destinatarios[${destinatarioIndex}][unidad_destino_id]"
                            class="form-select unidad-destino">
                        ${renderUnidadesOptions()}
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Funcionario Responsable <small
                                                class="text-danger">*</small></label>
                    <select name="destinatarios[${destinatarioIndex}][funcionario_id]"
                            class="form-select funcionario">
                        <option value="">-- Seleccione Funcionario --</option>
                    </select>
                </div>
            </div>

            <div class="text-end">
                <button type="button"
                        class="btn btn-sm btn-outline-danger btn-remove-destinatario">
                    <i data-feather="trash-2"></i> Quitar
                </button>
            </div>
        </div>
    `;

    $("#destinatarios-wrapper").append(html);

    let newItem = $("#destinatarios-wrapper .destinatario-item").last();
    initSelects(newItem);
    feather.replace();

    destinatarioIndex++;
});

$(document).on("click", ".btn-remove-destinatario", function () {
    $(this).closest(".destinatario-item").remove();
});
