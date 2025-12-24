$(document).ready(function () {

    /* =====================================================
       CARGAR FUNCIONARIOS SEGÚN UNIDAD DESTINO
    ===================================================== */
    $(document).on("change", ".unidad-destino", function () {
        const unidadId = $(this).val();
        const destinatario = $(this).closest(".destinatario-item");
        const funcionarioSelect = destinatario.find(".funcionario");

        funcionarioSelect
            .empty()
            .append('<option value="">-- Seleccione Funcionario --</option>');

        if (!unidadId) return;

        $.ajax({
            url: `/admin/hojaruta/${unidadId}/funcionarios`,
            type: "GET",
            success: function (data) {
                data.forEach((f) => {
                    funcionarioSelect.append(
                        `<option value="${f.id}">${f.nombre}</option>`
                    );
                });
            },
            error: function () {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "No se pudieron cargar los funcionarios",
                    toast: true,
                    position: "top",
                    timer: 4000,
                    showConfirmButton: false,
                    ...swalStyles(),
                });
            },
        });
    });

    /* =====================================================
       UTIL: LIMPIAR ERROR DE INPUT / SELECT / TEXTAREA
    ===================================================== */
    function limpiarError(input) {
        input.removeClass("is-invalid");

        let feedback = input.next(".invalid-feedback");
        if (feedback.length) {
            feedback.text("");
        }
    }

    /* =====================================================
       LIMPIAR ERRORES AL ESCRIBIR
    ===================================================== */
    $("#derivacionForm").on(
        "input change",
        "input, select, textarea",
        function () {
            limpiarError($(this));
        }
    );

    /* =====================================================
       SUBMIT FORM – CREAR DERIVACIÓN
    ===================================================== */
    $("#derivacionForm").on("submit", function (e) {
        e.preventDefault();

        const form = $(this);
        const formData = new FormData(this);

        /* =====================================================
           DEBUG – VER DATOS ENVIADOS
        ===================================================== */
        console.group("📦 Datos enviados al backend");
        for (const [key, value] of formData.entries()) {
            console.log(key, value);
        }
        console.groupEnd();

        const btn = form.find('button[type="submit"]');
        const spinner = btn.find(".spinner-border");
        const btnText = btn.find(".btn-text");

        // limpiar errores previos
        form.find(".is-invalid").removeClass("is-invalid");
        form.find(".invalid-feedback").remove();

        // UI loading
        btn.prop("disabled", true);
        spinner?.removeClass("d-none");
        btnText?.addClass("opacity-50");

        $.ajax({
            url: "/admin/hojaruta/1/derivaciones",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },

            /* =====================================================
               SUCCESS
            ===================================================== */
            success: function (data) {
                btn.prop("disabled", false);
                spinner?.addClass("d-none");
                btnText?.removeClass("opacity-50");

                Swal.fire({
                    icon: "success",
                    title: "¡Éxito!",
                    text: data.message || "Derivación creada correctamente",
                    timer: 2000,
                    showConfirmButton: false,
                    position: "center",
                    ...swalStyles(),
                }).then(() => {
                    window.location.href = `/admin/derivaciones/${data.derivacion.id}`;
                });
            },

            /* =====================================================
               ERROR – MOSTRAR VALIDACIONES
            ===================================================== */
            error: function (xhr) {
                btn.prop("disabled", false);
                spinner?.addClass("d-none");
                btnText?.removeClass("opacity-50");

                let message = "Error desconocido";

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;

                    $.each(errors, function (field, messages) {
                        const input = form.find(`[name="${field}"]`);

                        if (!input.length) return;

                        input.addClass("is-invalid");

                        let feedback = input.next(".invalid-feedback");
                        if (!feedback.length) {
                            feedback = $('<div class="invalid-feedback"></div>');
                            input.after(feedback);
                        }

                        feedback.text(messages[0]);
                    });

                    message = "Corrige los errores del formulario.";
                } else if (xhr.responseJSON?.message) {
                    message = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: "error",
                    title: "Error",
                    html: message,
                    toast: true,
                    position: "top",
                    timer: 5000,
                    showConfirmButton: false,
                    ...swalStyles(),
                });

                console.error("❌ Error servidor:", xhr);
            },
        });
    });
});
