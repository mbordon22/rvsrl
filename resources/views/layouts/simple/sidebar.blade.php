<style>
    @media (min-width: 1024px) {
        .w-lg-50 {
            width: auto !important;
        }
    }
</style>

<!-- Page Sidebar Start-->
<div class="sidebar-wrapper" data-layout="stroke-svg">
    <div class="logo-wrapper"><a href="{{ route('admin.dashboard') }}"><img class="img-fluid"
                src="{{ asset('assets/images/logo/logo_sidebar.jpg') }}" style="50px" alt=""></a>
        <div class="back-btn"><i class="fa fa-angle-left"> </i></div>
        <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="grid"> </i>
        </div>
    </div>
    <div class="logo-icon-wrapper"><a href="{{ route('admin.dashboard') }}"><img class="img-fluid"
                src="{{ asset('assets/images/logo/logo-icon.png') }}" alt=""></a></div>
    <nav class="sidebar-main">
        <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
        <div id="sidebar-menu">
            <ul class="sidebar-links" id="simple-bar">
                <li class="back-btn"><a href="{{ route('admin.dashboard') }}"><img class="img-fluid"
                            src="{{ asset('assets/images/logo/logo-icon.png') }}" alt=""></a>
                    <div class="mobile-back text-end"> <span>Back </span><i class="fa fa-angle-right ps-2"
                            aria-hidden="true"></i></div>
                </li>
                <li class="pin-title sidebar-main-title">
                    <div>
                        <h6>Pinned</h6>
                    </div>
                </li>
                <li class="sidebar-main-title">
                    <div>
                        <h6 class="lan-1">General</h6>
                    </div>
                </li>
                <li class="sidebar-list"><i class="fa fa-thumb-tack"> </i><a class="sidebar-link sidebar-title link-nav"
                        href="{{ route('admin.dashboard') }}">
                        <svg class="stroke-icon">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                        </svg>
                        <svg class="fill-icon">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#fill-home') }}"></use>
                        </svg><span class="lan-3">Dashboard </span></a>
                </li>
                <li class="sidebar-list"><i class="fa fa-thumb-tack"></i><a class="sidebar-link sidebar-title"
                        href="javascript:void(0)">
                        <svg class="stroke-icon">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-layout') }}"></use>
                        </svg>
                        <svg class="fill-icon">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#fill-layout') }}"></use>
                        </svg><span class="lan-7-1">Usuarios</span></a>
                    <ul class="sidebar-submenu">
                        @can('role.index')
                        <li><a href="{{ route('admin.role.index') }}">Gestión de Roles</a></li>
                        @endcan
                        @can('user.index')
                        <li><a href="{{ route('admin.user.index') }}">Gestión de Usuarios</a></li>
                        @endcan
                    </ul>
                </li>
                @canany(['vehiculo.index', 'combustible.index'])
                    <li class="sidebar-list"><i class="fa fa-thumb-tack"></i><a class="sidebar-link sidebar-title"
                            href="javascript:void(0)">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-layout') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-layout') }}"></use>
                            </svg><span class="lan-7-1">Vehículos</span></a>
                        <ul class="sidebar-submenu">
                            @can('vehiculo.index')
                            <li><a href="{{ route('admin.vehiculo.index') }}">Gestión de Vehículos</a></li>
                            @endcan
                            @can('vehiculo.index')
                            <li><a href="{{ route('admin.vehiculo.tiposVehiculo.index') }}">Tipos de Vehiculos</a></li>
                            @endcan
                            @can('combustible.index')
                            <li><a href="{{ route('admin.cargas-combustible.index') }}">Cargas Combustible</a></li>
                            @endcan
                        </ul>
                    </li>             
                @endcanany
                @can('epp.index')     
                <li class="sidebar-list"><i class="fa fa-thumb-tack"></i><a class="sidebar-link sidebar-title"
                    href="javascript:void(0)">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-job-search') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-layout') }}"></use>
                    </svg><span class="lan-7-1">Elem. Protección Personal</span></a>
                    <ul class="sidebar-submenu">
                        @can('epp.index')
                        <li><a href="{{ route('admin.epp.entregas.index') }}">Entregas de EPP</a></li>
                        @endcan
                        @can('epp.index')
                        <li><a href="{{ route('admin.epp.elementos.index') }}">Stock de EPP</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan
                @canany(['egresos.index', 'ingresos.index', 'gestion_contable.index'])
                <li class="sidebar-list"><i class="fa fa-thumb-tack"></i><a class="sidebar-link sidebar-title"
                    href="javascript:void(0)">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-job-search') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-layout') }}"></use>
                    </svg><span class="lan-7-1">Gestión Contable</span></a>
                    <ul class="sidebar-submenu">
                        @can('egresos.index')
                        <li><a class="submenu-title" href="javascript:void(0)">Egresos<span class="sub-arrow"><i class="fa fa-angle-right"></i></span></a>
                            <ul class="nav-sub-childmenu submenu-content">
                                <li><a href="{{ route('admin.gestion-contable.egresos.comprobantes.index') }}">Comprobantes</a></li>
                                <li><a href="{{ route('admin.gestion-contable.egresos.proveedores.index') }}">Proveedores</a></li>
                                {{-- <li><a href="">Productos de Compra</a></li> --}}
                            </ul>
                        </li>
                        @endcan
                        @can('ingresos.index')
                        <li><a class="submenu-title" href="javascript:void(0)">Ingresos<span class="sub-arrow"><i class="fa fa-angle-right"></i></span></a>
                            <ul class="nav-sub-childmenu submenu-content">
                                <li><a href="{{ route('admin.gestion-contable.ingresos.comprobantes.index') }}">Comprobantes</a></li>
                                <li><a href="{{ route('admin.gestion-contable.ingresos.clientes.index') }}">Clientes</a></li>
                                {{-- <li><a href="">Productos de Venta</a></li> --}}
                            </ul>
                        </li>
                        @endcan
                        @can('gestion_contable.index')
                        <li><a href="{{ route('admin.gestion-contable.centro-costo.index') }}">Centro de Costo</a></li>
                        @endcan
                        @can('gestion_contable.index')
                        <li><a href="{{ route('admin.gestion-contable.parametros.index') }}">Parametros</a></li>
                        @endcan
                        @can('gestion_contable.index')
                        <li><a href="{{ route('admin.gestion-contable.medio-pago.index') }}">Medios de Pago</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan
                @canany(['gestion_stock.index', 'listado_materiales.index', 'listado_almacenes.index', 'listado_cuadrillas.index'])
                <li class="sidebar-list"><i class="fa fa-thumb-tack"></i><a class="sidebar-link sidebar-title"
                    href="javascript:void(0)">
                    <svg class="stroke-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-job-search') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                        <use href="{{ asset('assets/svg/icon-sprite.svg#fill-layout') }}"></use>
                    </svg><span class="lan-7-1">Inventarios y Stock</span></a>
                    <ul class="sidebar-submenu">
                        @can('gestion_stock.index')
                        <li><a href="{{ route('admin.inventarios.stock.index') }}">Stock Materiales</a></li>
                        @endcan
                        @can('listado_materiales.index')
                        <li><a href="{{ route('admin.inventarios.materiales.index') }}">List. Materiales</a></li>
                        @endcan
                        @can('listado_almacenes.index')
                        <li><a href="{{ route('admin.inventarios.almacenes.index') }}">List. Almacenes</a></li>
                        @endcan
                        @can('listado_cuadrillas.index')
                        <li><a href="{{ route('admin.inventarios.cuadrillas.index') }}">List. Cuadrillas</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan
            </ul>
        </div>
    </nav>
</div>
<!-- Page Sidebar Ends-->