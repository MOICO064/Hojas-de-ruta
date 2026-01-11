$(document).ready(function () {
    let isSubmitting = false;
    let pdfFileId = $("#old_drive_file_id").val() || null; // inicializamos con el fileId existente

    const funcionarioInicial = "{{ $derivacion->funcionario_id ?? '' }}";

    /* =====================================================
       CARGAR FUNCIONARIOS SEGÚN UNIDAD
    ===================================================== */
    function cargarFuncionarios(unidadId, funcionarioSeleccionado = null) {
        const funcionarioSelect = $("#funcionario_id");
        funcionarioSelect.empty();
        if (!unidadId) return;

        $.ajax({
            url: `/admin/derivaciones/${unidadId}/funcionarios`,
            type: "GET",
            success: function (data) {
                data.forEach((f) => {
                    const selected =
                        funcionarioSeleccionado == f.id ? "selected" : "";
                    funcionarioSelect.append(
                        `<option value="${f.id}" ${selected}>${f.nombre}</option>`
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
    }

    $("#unidad_id").on("change", function () {
        cargarFuncionarios($(this).val());
    });

    const unidadInicial = $("#unidad_id").val();
    if (unidadInicial && funcionarioInicial) {
        cargarFuncionarios(unidadInicial, funcionarioInicial);
    }

    /* =====================================================
       LIMPIAR ERRORES
    ===================================================== */
    function limpiarError(input) {
        input.removeClass("is-invalid");
        input.next(".invalid-feedback").remove();
    }

    $("#derivacionForm").on(
        "input change",
        "input, select, textarea",
        function () {
            limpiarError($(this));
        }
    );

    function laravelFieldToInputName(field) {
        if (!field.includes(".")) return field;
        return field.replace(/\.(\d+)\./g, "[$1][").replace(/\./g, "]") + "]";
    }

    async function deleteFromDrive(fileId) {
        if (!fileId) return;
        try {
            const tk = await fetch("/token-drive");
            const tkData = await tk.json();
            await fetch(
                `https://www.googleapis.com/drive/v3/files/${fileId}?supportsAllDrives=true`,
                {
                    method: "DELETE",
                    headers: {
                        Authorization: "Bearer " + tkData.access_token,
                    },
                }
            );
        } catch (err) {
            console.warn("No se pudo eliminar archivo anterior:", err);
        }
    }

    /* =====================================================
       FILEPOND
    ===================================================== */
    const pond = FilePond.create(document.querySelector("#pdfFile"), {
        allowMultiple: false,
        acceptedFileTypes: ["application/pdf"],
        maxFileSize: "10MB",
        server: {
            process: async (
                fieldName,
                file,
                metadata,
                load,
                error,
                progress,
                abort
            ) => {
                try {
                    bloquearBoton();
                    const tokenRes = await fetch("/token-drive");
                    const { access_token } = await tokenRes.json();
                    const ROOT_FOLDER_ID = "0ACRvb9jzt2clUk9PVA";
                    const gestion = $("#gestion").text().trim();
                    const hojaRuta = $("#idgral").text().trim();

                    const getOrCreateFolder = async (name, parentId) => {
                        const query = `mimeType='application/vnd.google-apps.folder' and trashed=false and name='${name.replace(
                            /'/g,
                            "\\'"
                        )}' and '${parentId}' in parents`;
                        const res = await fetch(
                            `https://www.googleapis.com/drive/v3/files?q=${encodeURIComponent(
                                query
                            )}&fields=files(id,name)&supportsAllDrives=true&includeItemsFromAllDrives=true`,
                            {
                                headers: {
                                    Authorization: "Bearer " + access_token,
                                },
                            }
                        );
                        const data = await res.json();
                        if (data.files && data.files.length > 0)
                            return data.files[0].id;

                        const createRes = await fetch(
                            "https://www.googleapis.com/drive/v3/files?supportsAllDrives=true",
                            {
                                method: "POST",
                                headers: {
                                    Authorization: "Bearer " + access_token,
                                    "Content-Type": "application/json",
                                },
                                body: JSON.stringify({
                                    name,
                                    mimeType:
                                        "application/vnd.google-apps.folder",
                                    parents: [parentId],
                                }),
                            }
                        );
                        const folder = await createRes.json();
                        if (!folder.id)
                            throw new Error(
                                `No se pudo crear la carpeta ${name}`
                            );
                        return folder.id;
                    };

                    const gestionFolderId = await getOrCreateFolder(
                        gestion,
                        ROOT_FOLDER_ID
                    );
                    const hojaFolderId = await getOrCreateFolder(
                        hojaRuta,
                        gestionFolderId
                    );

                    const formData = new FormData();
                    formData.append(
                        "metadata",
                        new Blob(
                            [
                                JSON.stringify({
                                    name: `${Date.now()}_${file.name}`,
                                    parents: [hojaFolderId],
                                }),
                            ],
                            { type: "application/json" }
                        )
                    );
                    formData.append("file", file);

                    const xhr = new XMLHttpRequest();
                    xhr.open(
                        "POST",
                        "https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true"
                    );
                    xhr.setRequestHeader(
                        "Authorization",
                        "Bearer " + access_token
                    );

                    xhr.upload.onprogress = (e) => {
                        if (e.lengthComputable)
                            progress(e.lengthComputable, e.loaded, e.total);
                    };

                    xhr.onload = async () => {
                        if (xhr.status === 200 || xhr.status === 201) {
                            const fileData = JSON.parse(xhr.responseText);

                            await fetch(
                                `https://www.googleapis.com/drive/v3/files/${fileData.id}/permissions?supportsAllDrives=true`,
                                {
                                    method: "POST",
                                    headers: {
                                        Authorization: "Bearer " + access_token,
                                        "Content-Type": "application/json",
                                    },
                                    body: JSON.stringify({
                                        role: "reader",
                                        type: "anyone",
                                    }),
                                }
                            );

                            pdfFileId = fileData.id; // ✅ guardamos el fileId subido
                            desbloquearBoton();
                            load(fileData.id);
                        } else {
                            error("Error al subir archivo");
                            desbloquearBoton();
                        }
                    };

                    xhr.onerror = () => {
                        error("Error de red");
                        desbloquearBoton();
                    };
                    xhr.send(formData);

                    return {
                        abort: () => {
                            xhr.abort();
                            abort();
                            desbloquearBoton();
                        },
                    };
                } catch (err) {
                    console.error(err);
                    error(err.message);
                    desbloquearBoton();
                }
            },

            revert: async (fileId, load) => {
                if (!fileId) return load();
                try {
                    await deleteFromDrive(fileId);
                    pdfFileId = null; // ✅ reseteamos si se elimina
                    load();
                } catch (err) {
                    console.error(err);
                    load();
                }
            },
        },
    });

    /* =====================================================
       SUBMIT FORM
    ===================================================== */
    $("#derivacionForm").on("submit", async function (e) {
        e.preventDefault();
        if (isSubmitting) return;
        isSubmitting = true;

        const form = $(this);
        const formData = new FormData(this);
        formData.append("_method", "PUT");

        const oldFileId = $("#old_drive_file_id").val();

        // Adjuntamos el fileId al formulario
        if (pdfFileId) {
            formData.set("pdf", pdfFileId);
            if (oldFileId && oldFileId !== pdfFileId) {
                await deleteFromDrive(oldFileId);
            }
        } else {
            formData.set("pdf", oldFileId || null);
        }

        const btn = form.find('button[type="submit"]');
        const spinner = btn.find(".spinner-border");
        const btnText = btn.find(".btn-text");

        btn.prop("disabled", true);
        spinner.removeClass("d-none");
        btnText.addClass("opacity-50");

        form.find(".is-invalid").removeClass("is-invalid");
        form.find(".invalid-feedback").remove();

        try {
            const hojaId = $("input[name='hoja_id']").val();
            const derivacionId = $("input[name='derivacion_id']").val();
            const url = `/admin/hojaruta/${hojaId}/derivaciones/${derivacionId}`;

            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (data) {
                
                    if (data.derivacion_id) {
                        window.open(
                            "/admin/reportes/derivaciones?derivaciones=" +
                                data.derivacion_id,
                            "_blank"
                        );
                    }


                    Swal.fire({
                        icon: "success",
                        title: "¡Actualizado!",
                        text:
                            data.message ||
                            "Derivación actualizada correctamente",
                        timer: 2000,
                        showConfirmButton: false,
                        position: "center",
                    }).then(() => {
                        window.location.href = "/admin/buzon/salida";
                    });
                },
                error: function (xhr) {
                    let message = "Error inesperado";
                    if (xhr.responseJSON) {
                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            $.each(
                                xhr.responseJSON.errors,
                                function (field, messages) {
                                    const inputName =
                                        laravelFieldToInputName(field);
                                    const input = form.find(
                                        `[name="${inputName}"]`
                                    );
                                    if (!input.length) return;
                                    input.addClass("is-invalid");
                                    input.after(
                                        `<div class="invalid-feedback">${messages[0]}</div>`
                                    );
                                }
                            );
                        }
                        message = xhr.responseJSON.message || message;
                    }
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        html: message,
                        toast: true,
                        position: "top",
                        timer: 5000,
                        showConfirmButton: false,
                    });
                },
                complete: function () {
                    isSubmitting = false;
                    btn.prop("disabled", false);
                    spinner.addClass("d-none");
                    btnText.removeClass("opacity-50");
                },
            });
        } catch (err) {
            console.error(err);
            Swal.fire({
                icon: "error",
                title: "Error",
                html: err.message || "Ocurrió un error inesperado",
                toast: true,
                position: "top",
                timer: 5000,
                showConfirmButton: false,
            });
            isSubmitting = false;
            btn.prop("disabled", false);
            spinner.addClass("d-none");
            btnText.removeClass("opacity-50");
        }
    });
});
