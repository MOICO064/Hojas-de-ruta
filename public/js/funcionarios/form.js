 $(document).ready(function () {

    // Limpiar errores de un input o select
    function limpiarError(input) {
        input.removeClass('is-invalid');

        if (input.attr('id') === 'unidad_id' && input.hasClass('select2-hidden-accessible')) {
            input.parent().find('.invalid-feedback').text('');
        } else {
            input.next('.invalid-feedback').text('');
        }
    }

    // Limpiar errores al modificar inputs o selects
    $('#funcionarioForm').on('input change', 'input, select', function () {
        limpiarError($(this));
    });

    $('#funcionarioForm').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        const formData = new FormData(this);
        const funcionarioId = $('#funcionario_id').val();
        const url = funcionarioId ? `/admin/funcionarios/${funcionarioId}` : `/admin/funcionarios`;

        if (funcionarioId) {
            formData.append('_method', 'PUT');
        }

        const btn = $('#saveFuncionarioBtn');
        const spinner = btn.find('.spinner-border');
        const btnText = btn.find('.btn-text');

        // Limpiar errores anteriores
        form.find('.invalid-feedback').text('');
        form.find('.is-invalid').removeClass('is-invalid');

        // Mostrar spinner y deshabilitar botón
        btn.prop('disabled', true);
        spinner.removeClass('d-none');
        btnText.addClass('opacity-50');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },

            success: function (data) {
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                btnText.removeClass('opacity-50');

                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: data.message || (funcionarioId ? 'Funcionario actualizado correctamente' : 'Funcionario registrado con éxito'),
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    position: 'center',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    backdrop: true,
                    ...swalStyles()
                }).then(() => {
                    window.location.href = '/admin/funcionarios';
                });
            },
            error: function (xhr) {
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                btnText.removeClass('opacity-50');

                let message = 'Error desconocido';

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $.each(errors, function (field, messages) {
                        const input = form.find('[name="' + field + '"]');
                        input.addClass('is-invalid');

                        if (field === 'unidad_id' && input.hasClass('select2-hidden-accessible')) {
                            input.parent().find('.invalid-feedback').text(messages[0]);
                        } else {
                            input.next('.invalid-feedback').text(messages[0]);
                        }
                    });
                    message = 'Por favor corrige los errores en el formulario.';
                } else {
                    if (xhr.responseJSON?.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.responseJSON) {
                        message = JSON.stringify(xhr.responseJSON, null, 2);
                    } else {
                        message = xhr.status + ' - ' + xhr.statusText;
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: message.replace(/\n/g, '<br>'),
                    toast: true,
                    position: 'top',
                    timer: 5000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    width: '400px',
                    customClass: {
                        popup: 'swal2-toast-width'
                    },
                    ...swalStyles()
                });

                console.error('Error completo del servidor:', xhr);
            }
        });
    });
});
