@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h3 class="mb-0 d-flex align-items-center gap-2">
            <i data-feather="edit"></i>
            Editar Derivación
        </h3>
        <h6 class="text-muted">N° Hoja: <span id="idgral">{{ $hoja->idgral }}</span></h6>
        <span id="gestion" class="d-none">{{ $hoja->gestion }}</span>
    </div>

    <a href="{{route('admin.buzon.salida')}}" class="btn btn-outline-primary d-flex align-items-center gap-2">
        <i data-feather="arrow-left"></i> Volver
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8 col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex align-items-center gap-2">
                <i data-feather="edit-3"></i>
                <h4 class="mb-0">Actualizar Derivación</h4>
            </div>

            <div class="card-body">
                <form id="derivacionForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="hoja_id" value="{{ $hoja->id }}">
                    <input type="hidden" name="derivacion_id" value="{{ $derivacion->id }}">
                    <input type="hidden" id="old_drive_file_id" value="{{ $derivacion->fileid }}">

                    {{-- UNIDAD --}}
                    <div class="mb-3">
                        <label class="form-label">Unidad <span class="text-danger">*</span></label>
                        <select name="unidad_id" id="unidad_id" class="form-select">
                            <option value="">-- Seleccione Unidad --</option>
                            @foreach($unidades as $u)
                            <option value="{{ $u->id }}"
                                {{ $derivacion->unidad_destino_id == $u->id ? 'selected' : '' }}>
                                {{ $u->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- FUNCIONARIO --}}
                    <div class="mb-3">
                        <label class="form-label">Funcionario <span class="text-danger">*</span></label>
                        <select name="funcionario_id" id="funcionario_id" class="form-select">
                            <option value="{{ $derivacion->funcionario_id }}" selected>
                                {{ $derivacion->funcionario->nombre }}
                            </option>
                        </select>
                    </div>

                    {{-- DESCRIPCIÓN --}}
                    <div class="mb-3">
                        <label class="form-label">Descripción <span class="text-danger">*</span></label>
                        <textarea name="descripcion" class="form-control" rows="3"
                            oninput="this.value=this.value.toUpperCase()">{{ $derivacion->descripcion }}</textarea>
                    </div>

                    {{-- FOJAS --}}
                    <div class="mb-3">
                        <label class="form-label">Fojas <small class="text-muted">(opcional)</small></label>
                        <input type="number" name="fojas" class="form-control" value="{{ $derivacion->fojas }}">
                    </div>

                    {{-- PDF --}}
                    <div class="mb-4">
                        <label class="form-label">Documento PDF<small class="text-muted">(opcional)</small></label>
                        @if($derivacion->pdf)
                        <div class="my-2">
                            <a href="{{ $derivacion->pdf }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                <i data-feather="eye"></i> Ver PDF actual
                            </a>
                        </div>
                        @endif
                        <div class="alert alert-info py-2 d-flex gap-2 align-items-start">
                            <i data-feather="info" class="mt-1"></i>

                            @if($derivacion->pdf)
                            <div>
                                <strong>Documento existente:</strong><br>
                                Si desea <b>reemplazar el PDF actual</b>, suba un nuevo archivo.
                                <br>
                                Si no selecciona ningún archivo, el documento actual se mantendrá.
                            </div>
                            @else
                            <div>
                                <strong>Sin documento adjunto:</strong><br>
                                Puede subir un <b>PDF</b> si desea adjuntar documentación a esta derivación.
                            </div>
                            @endif
                        </div>


                        <input type="file" id="pdfFile" class="filepond" accept="application/pdf">



                    </div>

                    <button type="submit" class="btn btn-primary ">
                        <span class="spinner-border spinner-border-sm d-none"></span>
                        <i data-feather="check-circle"></i>
                        <span class="btn-text">Actualizar Derivación</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
FilePond.registerPlugin(
    FilePondPluginFileValidateType,
    FilePondPluginFileValidateSize
);

let driveFileId = null;
let driveFileUrl = null;
let currentUploadXhr = null;
// Configuración global de FilePond en español
FilePond.setOptions({
    labelIdle: 'Arrastra un PDF o haz clic aquí',
    labelFileWaitingForSize: 'Esperando tamaño...',
    labelFileSizeNotAvailable: 'Tamaño no disponible',
    labelFileLoading: 'Cargando...',
    labelFileLoadError: 'Error al cargar el archivo',
    labelFileProcessing: 'Subiendo...',
    labelFileProcessingComplete: 'Subida completa',
    labelFileProcessingAborted: 'Subida cancelada',
    labelFileProcessingError: 'Error al subir',
    labelFileRemoveError: 'Error al eliminar',
    labelTapToCancel: 'Toca para cancelar',
    labelTapToRetry: 'Toca para reintentar',
    labelTapToUndo: 'Toca para deshacer',
    labelButtonRemoveItem: 'Eliminar',
    labelButtonAbortItemLoad: 'Cancelar',
    labelButtonRetryItemLoad: 'Reintentar',
    labelButtonAbortItemProcessing: 'Cancelar',
    labelButtonRetryItemProcessing: 'Reintentar',
    labelButtonProcessItem: 'Subir'
});
let isSubmitting = false;
const btnSubmit = document.querySelector('#derivacionForm button[type="submit"]');

function bloquearBoton() {
    isSubmitting = true;
    btnSubmit.disabled = true;
    btnSubmit.style.pointerEvents = "none";
    const spinner = btnSubmit.querySelector(".spinner-border");
    const btnText = btnSubmit.querySelector(".btn-text");
    if (spinner) spinner.classList.remove("d-none");
    if (btnText) btnText.classList.add("opacity-50");
}

function desbloquearBoton() {
    isSubmitting = false;
    btnSubmit.disabled = false;
    btnSubmit.style.pointerEvents = "auto";
    const spinner = btnSubmit.querySelector(".spinner-border");
    const btnText = btnSubmit.querySelector(".btn-text");
    if (spinner) spinner.classList.add("d-none");
    if (btnText) btnText.classList.remove("opacity-50");
}

const pond = FilePond.create(document.querySelector('#pdfFile'), {
    allowMultiple: false,
    acceptedFileTypes: ['application/pdf'],
    maxFileSize: '10MB',
    labelIdle: 'Arrastra el PDF o haz clic aquí',

    server: {
        process: async (fieldName, file, metadata, load, error, progress, abort) => {
            bloquearBoton();
            try {
                const tokenRes = await fetch('/token-drive');
                const {
                    access_token
                } = await tokenRes.json();
                const ROOT_FOLDER_ID = '0ACRvb9jzt2clUk9PVA';

                const gestion = document.querySelector('#gestion').textContent.trim();
                const hojaRuta = document.querySelector('#idgral').textContent.trim();

                const getOrCreateFolder = async (name, parentId) => {
                    try {
                        const query =
                            `mimeType='application/vnd.google-apps.folder' and trashed=false and name='${name.replace(/'/g, "\\'")}' and '${parentId}' in parents`;
                        const res = await fetch(
                            `https://www.googleapis.com/drive/v3/files?q=${encodeURIComponent(query)}&fields=files(id,name)&supportsAllDrives=true&includeItemsFromAllDrives=true`, {
                                headers: {
                                    'Authorization': 'Bearer ' + access_token
                                }
                            });
                        const data = await res.json();
                        if (data.files && data.files.length > 0) {
                            console.log(`Carpeta encontrada: ${name}`, data.files[0]);
                            return data.files[0].id;
                        }

                        const createRes = await fetch(
                            'https://www.googleapis.com/drive/v3/files?supportsAllDrives=true', {
                                method: 'POST',
                                headers: {
                                    'Authorization': 'Bearer ' + access_token,
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    name,
                                    mimeType: 'application/vnd.google-apps.folder',
                                    parents: [parentId]
                                })
                            });
                        const folder = await createRes.json();
                        if (!folder.id) throw new Error(`No se pudo crear la carpeta ${name}`);
                        console.log(`Carpeta creada: ${name}`, folder);
                        return folder.id;
                    } catch (err) {
                        console.error('Error al obtener/crear carpeta', name, err);
                        throw err;
                    }
                };

                const gestionFolderId = await getOrCreateFolder(gestion, ROOT_FOLDER_ID);
                const hojaFolderId = await getOrCreateFolder(hojaRuta, gestionFolderId);

                const formData = new FormData();
                formData.append('metadata', new Blob([JSON.stringify({
                    name: `${Date.now()}_${file.name}`,
                    parents: [hojaFolderId]
                })], {
                    type: 'application/json'
                }));
                formData.append('file', file);

                return new Promise((resolve, reject) => {
                    const xhr = new XMLHttpRequest();
                    currentUploadXhr = xhr;
                    xhr.open('POST',
                        'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true'
                    );
                    xhr.setRequestHeader('Authorization', 'Bearer ' + access_token);

                    xhr.upload.onprogress = e => {
                        if (e.lengthComputable) {
                            const percent = Math.round((e.loaded / e.total) * 100);
                            console.log(`Progreso: ${percent}%`);
                            progress(e.lengthComputable, e.loaded, e.total);
                        }
                    };

                    xhr.onload = async () => {
                        if (xhr.status === 200 || xhr.status === 201) {
                            const fileData = JSON.parse(xhr.responseText);

                            await fetch(
                                `https://www.googleapis.com/drive/v3/files/${fileData.id}/permissions?supportsAllDrives=true`, {
                                    method: 'POST',
                                    headers: {
                                        'Authorization': 'Bearer ' + access_token,
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        role: 'reader',
                                        type: 'anyone'
                                    })
                                });

                            driveFileId = fileData.id;
                            driveFileUrl =
                                `https://drive.google.com/file/d/${fileData.id}/view`;
                            console.log('Archivo subido:', {
                                id: driveFileId,
                                url: driveFileUrl
                            });

                            load(fileData.id);
                            resolve({
                                fileId: fileData.id,
                                filePath: driveFileUrl
                            });
                            desbloquearBoton();
                        } else {
                            const errResp = xhr.responseText ? JSON.parse(xhr
                                .responseText) : {};
                            console.error('Error al subir archivo', errResp);
                            Swal.fire('Error', 'Error al subir archivo a Drive', 'error');
                            reject(new Error('Error al subir archivo a Drive'));
                            error('Error al subir archivo');
                            desbloquearBoton();
                        }
                    };

                    xhr.onerror = () => {
                        console.error('Error de red en subida a Drive');
                        Swal.fire('Error', 'Error de red en subida a Drive', 'error');
                        reject(new Error('Error de red'));
                        error('Error de red');
                    };

                    xhr.send(formData);

                    return {
                        abort: () => {
                            xhr.abort();
                            abort();
                            desbloquearBoton();
                        }
                    };
                });

            } catch (err) {
                console.error('Error en proceso FilePond', err);
                Swal.fire('Error', err.message || 'Error inesperado', 'error');
                error(err.message);
                desbloquearBoton();
            }
        },

        revert: async (fileId, load) => {
            if (!fileId) return load();
            bloquearBoton()
            try {
                await deleteFromDrive(fileId);


                driveFileId = null;
                driveFileUrl = null;

                console.log('Archivo revertido correctamente:', fileId);
                load();
                desbloquearBoton();
            } catch (err) {
                console.error('Error al revertir archivo', err);
                Swal.fire('Error', 'No se pudo eliminar el archivo en Drive compartido', 'error');
                load();
                desbloquearBoton();
            }
        }


    }
});


async function deleteFromDrive(fileId) {
    if (!fileId) return;
    try {
        const tk = await fetch('/token-drive');
        const tkData = await tk.json();
        await fetch(`https://www.googleapis.com/drive/v3/files/${fileId}?supportsAllDrives=true`, {
            method: "DELETE",
            headers: {
                Authorization: "Bearer " + tkData.access_token
            }
        });
    } catch (err) {
        console.warn("No se pudo eliminar archivo anterior:", err);
    }
}


$('#unidad_destino_id, #funcionario_id').select2({
    placeholder: '-- Seleccione --',
    width: '100%',
    allowClear: true
});

feather.replace();
</script>
<script>
/* ===== SELECT2 ===== */
$('#unidad_id, #funcionario_id').select2({
    width: '100%'
});

feather.replace();
</script>
<script src="{{ asset('js/derivaciones/form-edit.js') }}">
</script>
@endsection