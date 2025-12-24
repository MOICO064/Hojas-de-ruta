@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <!-- Encabezado -->
        <div class="row mb-5">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Dashboard de Hojas de Ruta</h3>
                <span>Última actualización: 17/12/2025</span>
            </div>
        </div>

        <!-- Métricas principales -->
        <div class="row mb-5">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="fw-bold">Total de Hojas de Ruta</span>
                        <h3 class="mt-2">1,234</h3>
                        <small>Actualización mensual</small>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="fw-bold">Hojas de Ruta en Proceso</span>
                        <h3 class="mt-2">456</h3>
                        <small>Acciones pendientes</small>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="fw-bold">Hojas de Ruta Completadas</span>
                        <h3 class="mt-2">678</h3>
                        <small>Últimos 30 días</small>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="fw-bold">Hojas de Ruta Derivadas</span>
                        <h3 class="mt-2">100</h3>
                        <small>Acciones derivadas a funcionarios</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hojas de Ruta recientes -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card h-100">
                    <div class="card-header">
                        <h4>Últimas Hojas de Ruta Registradas</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item">Hoja de Ruta #1234 - Estado: En Proceso</li>
                            <li class="list-group-item">Hoja de Ruta #1233 - Estado: Completada</li>
                            <li class="list-group-item">Hoja de Ruta #1232 - Estado: Pendiente</li>
                            <li class="list-group-item">Hoja de Ruta #1231 - Estado: Derivada</li>
                            <li class="list-group-item">Hoja de Ruta #1230 - Estado: Completada</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribución por áreas -->
        <div class="row mb-5">
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h4>Hojas de Ruta por Área</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between">
                                Dirección Administrativa
                                <span>300</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                Secretaría General
                                <span>250</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                Área Técnica
                                <span>400</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                Otros
                                <span>284</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h4>Hojas de Ruta por Estado</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between">
                                En Proceso
                                <span>456</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                Completadas
                                <span>678</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                Pendientes
                                <span>100</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                Derivadas
                                <span>100</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas adicionales -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h4>Acciones y Notificaciones Recientes</h4>
                    </div>
                    <div class="card-body">
                        <p>- El ciudadano Juan Pérez ha consultado la Hoja de Ruta #1233.</p>
                        <p>- La Hoja de Ruta #1232 fue derivada al área técnica.</p>
                        <p>- Se completó la Hoja de Ruta #1231 y se notificó al interesado.</p>
                        <p>- Pendientes de revisión: 15 hojas de ruta.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection