function abrirModalAnulacion(tipo, id) {
    document.querySelector('#modalAnulacion input[name="tipo"]').value = tipo;
    document.querySelector('#modalAnulacion input[name="id"]').value = id;

    document.querySelector(
        '#modalAnulacion textarea[name="justificacion"]'
    ).value = "";

    const modalElement = document.getElementById("modalAnulacion");
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}
$(document).ready(function () {
    function limpiarError(input) {
        input.removeClass("is-invalid");
        input.next(".invalid-feedback").text("");
    }

    $("#formAnulacion").on("input change", "input, textarea", function () {
        limpiarError($(this));
    });

    $("#formAnulacion").on("submit", function (e) {
        e.preventDefault();

        const form = $(this);
        const formData = new FormData(this);

        const btn = form.find('button[type="submit"]');
        const btnText = btn.text();
        btn.prop("disabled", true).text("Procesando...");

        form.find(".invalid-feedback").text("");
        form.find(".is-invalid").removeClass("is-invalid");

        $.ajax({
            url: "/admin/anulaciones/store",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                btn.prop("disabled", false).text(btnText);

                Swal.fire({
                    icon: "success",
                    title: "¡Éxito!",
                    text: data.message || "Registro anulado correctamente",
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    position: "center",
                    ...swalStyles(),
                });

                const modalEl = document.getElementById("modalAnulacion");
                const modal = bootstrap.Modal.getInstance(modalEl);

                modalEl.addEventListener(
                    "hidden.bs.modal",
                    function () {
                        $("#buzon-table").DataTable().ajax.reload(null, false);
                    },
                    { once: true }
                );

                modal.hide();
            },
            error: function (xhr) {
                btn.prop("disabled", false).text(btnText);

                let message = "Error desconocido";
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $.each(errors, function (field, messages) {
                        const input = form.find('[name="' + field + '"]');
                        input.addClass("is-invalid");
                        if (input.next(".invalid-feedback").length === 0) {
                            input.after('<div class="invalid-feedback"></div>');
                        }
                        input.next(".invalid-feedback").text(messages[0]);
                    });
                    message = "Por favor corrige los errores en el formulario.";
                } else if (xhr.responseJSON?.message) {
                    message = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: "error",
                    title: "Error",
                    html: message.replace(/\n/g, "<br>"),
                    toast: true,
                    position: "top",
                    timer: 5000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    width: "400px",
                    ...swalStyles(),
                });

                console.error("Error completo del servidor:", xhr);
            },
        });
    });
});
function abrirModalVerAnulacion(tipo, id) {
    $.ajax({
        url: "/admin/anulaciones/show",
        type: "GET",
        data: { tipo: tipo, id: id },
        success: function (data) {
            $("#anulacion-tipo").text(data.tipo);
            $("#anulacion-justificacion").text(data.justificacion);
            $("#anulacion-usuario").text(data.usuario);
            $("#anulacion-fecha").text(data.fecha);
            const modalEl = document.getElementById("modalVerAnulacion");
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        },
        error: function (xhr) {
            let message = "Error desconocido";
            if (xhr.responseJSON?.message) message = xhr.responseJSON.message;

            Swal.fire({
                icon: "error",
                title: "Error",
                text: message,
            });
        },
    });
}
