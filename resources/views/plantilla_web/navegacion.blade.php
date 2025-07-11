 <div class=" py-3 d-none d-sm-block text-white fw-bold" style="background-color: #880000;" >

        <div class="container">
            <div class="row align-items-center gx-4">
                <div class="col-auto d-none d-lg-block fs--1"><span class="fas fa-map-marker-alt text-warning me-2"
                        data-fa-transform="grow-3"></span> Edificio Emblemático Av.Juan Pablo II - entre Av. Sucre A y B
                    Zona Villa Esperanza </div>
                <div class="col-auto ms-md-auto order-md-2 d-none d-sm-flex fs--1 align-items-center"><span
                        class="fas fa-clock text-warning me-2" data-fa-transform="grow-3"></span>Lunes a Viernes, de
                    8:30 a 12:30 y 14:00 a 18:00 </div>
                <div class="col-auto"><span class="fas fa-phone-alt text-warning" data-fa-transform="shrink-3"></span><a
                        class="ms-2 fs--1 d-inline text-white fw-bold" href="tel:2123865575">212 386 5575, 212 386
                        5576</a></div>
            </div>
        </div>
    </div>
    <div class="sticky-top navbar-elixir bg-light">
        <div class="container">
            <nav class="navbar navbar-expand-lg">
                <a class="navbar-brand" href="index.html">
                    <img src="{{ asset('assets/logo_d.png') }}" alt="logo" width="100px"/>
                </a><button class="navbar-toggler p-0" type="button" data-bs-toggle="collapse"
                    data-bs-target="#primaryNavbarCollapse" aria-controls="primaryNavbarCollapse" aria-expanded="false"
                    aria-label="Toggle navigation"><span class="hamburger hamburger--emphatic"><span
                            class="hamburger-box"><span class="hamburger-inner"></span></span></span></button>
                <div class="collapse navbar-collapse" id="primaryNavbarCollapse">
                    <ul class="navbar-nav py-3 py-lg-0 mt-1 mb-2 my-lg-0 ms-lg-n1">
                        <li class="nav-item dropdown"><a class="nav-link" href="/inicio"
                                role="button">Inicio</a>
                            <ul class="dropdown-menu">
                            
                            </ul>
                        </li>
                        <li class="nav-item dropdown"><a class="nav-link dropdown-toggle dropdown-indicator"
                                href="JavaScript:void(0)" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">Sedes</a>
                            <ul class="dropdown-menu">
                                @foreach ($sedes as $sede)
                                    <li><a class="dropdown-item" href="/sedes/{{$sede->id}}">{{$sede->nombre}}</a></li>
                                @endforeach
                                
                                
                            </ul>
                        </li>
                        <li class="nav-item dropdown"><a class="nav-link dropdown-toggle dropdown-indicator"
                                href="JavaScript:void(0)" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">Registro</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="news/newsroom.html">Docentes</a></li>
                                <li><a class="dropdown-item" href="news/news.html">Estudiantes</a></li>
                            </ul>
                        </li>

                        <li class="nav-item dropdown"><a class="nav-link" href="contact.html"
                                role="button">Contact</a></li>
                    </ul><a
                        class="btn btn-outline-danger rounded-pill btn-sm border-2 d-block d-lg-inline-block ms-auto my-3 my-lg-0"
                        href="/login" target="_blank">Ingresar</a>
                </div>
            </nav>
        </div>
    </div>