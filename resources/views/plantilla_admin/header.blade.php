<!-- Top Bar Start -->
<div class="topbar d-print-none">
    <div class="container">
        <nav class="topbar-custom d-flex justify-content-between" id="topbar-custom">


            <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">
                <li>
                    <button class="nav-link mobile-menu-btn nav-icon" id="togglemenu">
                        <i class="iconoir-menu-scale"></i>
                    </button>
                </li>
                <li class="mx-3 welcome-text">
                    <h3 class="mb-0 fw-bold text-truncate">SIST ADMIN {{ Auth::user()->nombres }}</h3>
                    <!-- <h6 class="mb-0 fw-normal text-muted text-truncate fs-14">Here's your overview this week.</h6> -->
                </li>
            </ul>
            <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">

                {{-- <li class="topbar-item">
                    <a class="nav-link nav-icon" href="javascript:void(0);" id="light-dark-mode">
                        <i class="icofont-moon dark-mode"></i>
                        <i class="icofont-sun light-mode"></i>
                    </a>
                </li> --}}


                <li class="dropdown topbar-item">
                    <a class="nav-link dropdown-toggle arrow-none nav-icon" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                          <i class="fas fa-user-cog text-success"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end py-0">
                        <div class="d-flex align-items-center dropdown-item py-2 bg-secondary-subtle">
                            <div class="flex-shrink-0">
                                   <i class="fas fa-user-cog text-success"></i>
                            </div>
                            <div class="flex-grow-1 ms-2 text-truncate align-self-center">
                                <h6 class="my-0 fw-medium text-dark fs-13">Admin</h6>
                                <small class="text-muted mb-0">Rol</small>
                            </div><!--end media-body-->
                        </div>
                        <div class="dropdown-divider mt-0"></div>
                        <small class="text-muted px-2 pb-1 d-block">Cuenta</small>
                        <a class="dropdown-item" href="{{ route('perfil') }}">
                            <i class="las la-user fs-18 me-1 align-text-bottom"></i>
                            Perfil
                        </a>

                        <div class="dropdown-divider mb-0"></div>
                        <a class="dropdown-item text-danger" href="javascript:void(0)" id="btn-cerrar-session"><i
                                class="las la-power-off fs-18 me-1 align-text-bottom"></i> Salir</a>
                    </div>
                    <form id="formulario_salir" method="POST">@csrf</form>
                </li>
            </ul><!--end topbar-nav-->
        </nav>
        <!-- end navbar-->
    </div>
</div>
<!-- Top Bar End -->
