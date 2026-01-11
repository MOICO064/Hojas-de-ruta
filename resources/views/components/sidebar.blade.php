<!-- Sidebar -->

<div class="navbar-vertical navbar nav-dashboard">
    <div class="h-100" data-simplebar>
        <!-- Brand logo -->
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <img src="{{ asset('assets/images/recurces/icono_sistema.png') }}" alt="logo" style="width: 180px; height: auto;"/>
        </a>

        <ul class="navbar-nav flex-column" id="sideNavbar">
            <li class="nav-item">
                <a class="nav-link has-arrow {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                    href="{{ route('dashboard') }}">
                    <i data-feather="home" class="nav-icon me-2 icon-xxs"></i>
                    Dashboard
                </a>
            </li>
            @can('admin.roles.permisos')
                <li class="nav-item">
                    <div class="navbar-heading">Seguridad</div>
                </li>
                <li class="nav-item">
                    <a class="nav-link has-arrow {{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permisos.*') ? '' : 'collapsed' }}"
                        href="#!" data-bs-toggle="collapse" data-bs-target="#navRolesPermisos"
                        aria-expanded="{{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permisos.*') ? 'true' : 'false' }}"
                        aria-controls="navRolesPermisos">

                        <i data-feather="shield" class="nav-icon me-2 icon-xxs"></i>
                        Roles & Permisos
                    </a>

                    <div id="navRolesPermisos"
                        class="collapse {{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permisos.*') ? 'show' : '' }}"
                        data-bs-parent="#sideNavbar">

                        <ul class="nav flex-column">

                            <!-- Roles -->
                            <li class="nav-item">
                                <a class="nav-link 
                                    {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}"
                                    href="{{ route('admin.roles.index') }}">
                                    Roles
                                </a>
                            </li>

                            <!-- Permisos -->
                            <li class="nav-item">
                                <a class="nav-link 
                                    {{ request()->routeIs('admin.permisos.*') ? 'active' : '' }}"
                                    href="{{ route('admin.permisos.index') }}">
                                    Permisos
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>
            @endcan
             @can('admin.backup.full')
                <li class="nav-item">
                    <button class="nav-link has-arrow btn btn-link text-start w-100" onclick="sniperBackup(this)"
                        type="button" title="Sacar Backup Completo">
                        <i data-feather="database" class="nav-icon me-2 icon-xxs"></i>
                        Crear Backup
                    </button>
                </li>
            @endcan
            @can('admin.usuarios')
                <li class="nav-item">
                    <div class="navbar-heading">ADMINISTRACION</div>
                </li>
                <li class="nav-item">
                    <a class="nav-link has-arrow {{ request()->is('admin/usuarios*') ? 'active' : '' }}"
                        href="{{ route('admin.usuarios.index') }}">
                        <i data-feather="user" class="nav-icon me-2 icon-xxs"></i>
                        Usuarios
                    </a>
                </li>
            @endcan
            @can('admin.unidades')
                <li class="nav-item">
                    <a class="nav-link has-arrow {{ request()->is('admin/unidades*') ? 'active' : '' }}"
                        href="{{ route('admin.unidades.index') }}">
                        <i data-feather="layers" class="nav-icon me-2 icon-xxs"></i>
                        Unidades
                    </a>
                </li>
            @endcan
            @can('admin.funcionarios')
                <li class="nav-item">
                    <a class="nav-link has-arrow {{ request()->is('admin/funcionarios*') ? 'active' : '' }}"
                        href="{{ route('admin.funcionarios.index') }}">
                        <i data-feather="users" class="nav-icon me-2 icon-xxs"></i>
                        Funcionarios
                    </a>
                </li>
            @endcan
            @can('admin.hojaruta.create')
                <li class="nav-item">
                    <div class="navbar-heading">NUEVA HOJA DE RUTA</div>
                </li>
                <li class="nav-item">
                    <a class="nav-link has-arrow {{ request()->is('admin/hojaruta/crear') ? 'active' : '' }}"
                        href="{{ route('admin.hojaruta.create') }}">
                        <i data-feather="file-text" class="nav-icon me-2 icon-xxs"></i>
                        Crear Hoja de Ruta
                    </a>
                </li>
            @endcan
            @can('admin.buzon')
                <li class="nav-item">
                    <div class="navbar-heading">BUZON</div>
                </li>
                <!-- Nav item: Buzón -->
                <li class="nav-item">
                    <a class="nav-link has-arrow {{ request()->routeIs('admin.buzon.*') ? '' : 'collapsed' }}" href="#!"
                        data-bs-toggle="collapse" data-bs-target="#navBuzon"
                        aria-expanded="{{ request()->routeIs('admin.buzon.*') ? 'true' : 'false' }}"
                        aria-controls="navBuzon">

                        <i data-feather="mail" class="nav-icon me-2 icon-xxs"></i>
                        Buzón
                    </a>

                    <div id="navBuzon" class="collapse {{ request()->routeIs('admin.buzon.*') ? 'show' : '' }}"
                        data-bs-parent="#sideNavbar">

                        <ul class="nav flex-column">

                            <!-- Entrada -->
                            <li class="nav-item">
                                <a class="nav-link 
                                        {{ request()->routeIs('admin.buzon.entrada') ? 'active' : '' }}"
                                    href="{{ route('admin.buzon.entrada') }}">
                                    Entrada
                                </a>
                            </li>

                            <!-- Salida -->
                            <li class="nav-item">
                                <a class="nav-link 
                                        {{ request()->routeIs('admin.buzon.salida') ? 'active' : '' }}"
                                    href="{{ route('admin.buzon.salida') }}">
                                    Salida
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>
            @endcan
            @can('admin.hojaruta.index')
             <li class="nav-item">
                        <div class="navbar-heading">GESTIONES</div>
                    </li>
                @foreach($gestiones as $gestion)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/hojaruta/' . $gestion . '/gestion') ? 'active' : '' }}"
                            href="{{ route('admin.hojaruta.index', $gestion) }}">
                            <i data-feather="file-text" class="nav-icon me-2 icon-xxs"></i>
                            Gestión {{ $gestion }}
                        </a>
                    </li>
                @endforeach

            @endcan
            @can('admin.reportes')
                <li class="nav-item">
                    <div class="navbar-heading">Reportes</div>
                </li>

                <li class="nav-item">
                    <a class="nav-link has-arrow
                            {{ request()->routeIs('admin.reportes.*') ? '' : 'collapsed' }}" href="#!"
                        data-bs-toggle="collapse" data-bs-target="#navReportes"
                        aria-expanded="{{ request()->routeIs('admin.reportes.*') ? 'true' : 'false' }}"
                        aria-controls="navReportes">

                        <i data-feather="bar-chart-2" class="nav-icon me-2 icon-xxs"></i>
                        Reportes
                    </a>

                    <div id="navReportes" class="collapse {{ request()->routeIs('admin.reportes.*') ? 'show' : '' }}"
                        data-bs-parent="#sideNavbar">

                        <ul class="nav flex-column">

                            @can('admin.reportes.consultar-hoja')
                                <li class="nav-item">
                                    <a class="nav-link
                                                            {{ request()->routeIs('admin.reportes.consultar-hoja') ? 'active' : '' }}"
                                        href="{{ route('admin.reportes.consultar-hoja') }}">
                                        Consultar Hoja de Ruta
                                    </a>
                                </li>
                            @endcan

                            @can('admin.reportes.hojas-por-unidad')
                                <li class="nav-item">
                                    <a class="nav-link
                                                            {{ request()->routeIs('admin.reportes.hojas-por-unidad') ? 'active' : '' }}"
                                        href="{{ route('admin.reportes.hojas-por-unidad') }}">
                                        Hojas por Unidad
                                    </a>
                                </li>
                            @endcan

                            @can('admin.reportes.total-unidades')
                                <li class="nav-item">
                                    <a class="nav-link
                                                            {{ request()->routeIs('admin.reportes.total-unidades') ? 'active' : '' }}"
                                        href="{{ route('admin.reportes.total-unidades') }}">
                                        Total de Unidades
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </div>
                </li>
            @endcan


           


        </ul>


    </div>
</div>