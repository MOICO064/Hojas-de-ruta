@extends('layouts.app')

@section('content')


</style>
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h3 class="mb-0">Mis Notificaciones</h3>

        <div class="d-flex gap-2">
            <!-- Botón Marcar Todas Como Leídas -->
            <button type="button" id="marcarTodoLeido" class="btn btn-primary d-flex align-items-center"
                title="Marcar todas como leídas">
                <i data-feather="check-square" class="me-2 icon-xxs d-none d-md-inline"></i>
                <span class="d-none d-md-inline">Marcar todas como leídas</span>
                <i data-feather="check-square" class="d-inline d-md-none"></i>
            </button>

            <!-- Botón Actualizar -->
            <button type="button" id="refresh-table" class="btn btn-secondary d-flex align-items-center"
                title="Actualizar Notificaciones">
                <i data-feather="refresh-cw" class="me-2 icon-xxs d-none d-md-inline"></i>
                <span class="d-none d-md-inline">Actualizar</span>
                <i data-feather="refresh-cw" class="d-inline d-md-none"></i>
            </button>
        </div>
    </div>

    <section class="section">
        <div class="card shadow rounded-4">
            <div class="card-body">
                <!-- Tabla de notificaciones -->
                <table id="tablaNotificaciones" class="table table-striped table-hover w-100">
                    <thead class="table-light">
                        <tr>
                            <th>Mensaje</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Se llena vía DataTables AJAX --}}
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

@section('scripts')

    <script src="{{ asset('js/notificaciones/index.js') }}"></script>

@endsection