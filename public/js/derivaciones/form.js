$(document).ready(function () {
    let isSubmitting = false;

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
            url: `/admin/derivaciones/${unidadId}/funcionarios`,
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
            feedback.remove();
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
       UTIL: CONVERTIR ERROR LARAVEL A NAME HTML
    ===================================================== */
    function laravelFieldToInputName(field) {
        // Campos normales: descripcion, fojas, etc.
        if (!field.includes(".")) {
            return field;
        }

        // Campos tipo array: destinatarios.0.unidad_destino_id
        return field.replace(/\.(\d+)\./g, "[$1][").replace(/\./g, "]") + "]";
    }

    /* =====================================================
       SUBMIT FORM – CREAR DERIVACIÓN
    ===================================================== */
    $("#derivacionForm").on("submit", function (e) {
        e.preventDefault();

        // ⛔ evitar doble submit
        if (isSubmitting) return;
        isSubmitting = true;

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

        /* =====================================================
           UI LOADING – BLOQUEO TOTAL
        ===================================================== */
        btn.prop("disabled", true);
        btn.css("pointer-events", "none");
        spinner.removeClass("d-none");
        btnText.addClass("opacity-50");
        const hojaId = $("input[name='hoja_id']").val();
        const url = `/admin/hojaruta/${hojaId}/derivaciones`;
        $.ajax({
            url: url,
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
                isSubmitting = false;
                btn.prop("disabled", false);
                btn.css("pointer-events", "auto");
                spinner.addClass("d-none");
                btnText.removeClass("opacity-50");

                // Abrir PDF en otra ventana
                if (data.derivaciones && data.derivaciones.length > 0) {
                    const derivacionesIds = data.derivaciones
                        .map((d) => d.id)
                        .join(",");
                    const url = `/admin/reportes/derivaciones?derivaciones=${derivacionesIds}`;
                    window.open(url, "_blank"); 
                }

                // Redirigir la página actual al buzón de salida
                Swal.fire({
                    icon: "success",
                    title: "¡Éxito!",
                    text: data.message || "Derivación creada correctamente",
                    timer: 2000,
                    showConfirmButton: false,
                    position: "center",
                    ...swalStyles(),
                }).then(() => {
                    window.location.href = "/admin/buzon/salida"; 
                });
            },

            /* =====================================================
               ERROR – MOSTRAR VALIDACIONES
            ===================================================== */
            error: function (xhr) {
                isSubmitting = false;
                btn.prop("disabled", false);
                btn.css("pointer-events", "auto");
                spinner.addClass("d-none");
                btnText.removeClass("opacity-50");

                let message = "Error desconocido";

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;

                    $.each(errors, function (field, messages) {
                        const inputName = laravelFieldToInputName(field);
                        const input = form.find(`[name="${inputName}"]`);

                        if (!input.length) return;

                        input.addClass("is-invalid");

                        let feedback = input.next(".invalid-feedback");
                        if (!feedback.length) {
                            feedback = $(
                                '<div class="invalid-feedback"></div>'
                            );
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
