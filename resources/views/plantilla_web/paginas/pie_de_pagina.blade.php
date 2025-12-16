  <footer style="background-color: #880000;" class="text-light pt-5 pb-3">
      <div class="container">
          <div class="row">
              <!-- Columna 1: Logo e información -->
              <div class="col-md-4 mb-4">

                  <img src="{{ asset('assets/logo_d.png') }}" alt="logo" style="max-width: 240px;" class="mb-3">

                  <p class="mb-1"><i class="bi bi-telephone"></i> +591 71234567</p>
                  <p class="mb-1"><i class="bi bi-envelope"></i> contacto@disbedc.org</p>
                  <p class="mb-0"><i class="bi bi-geo-alt"></i> El Alto, Bolivia</p>
              </div>

              <!-- Columna 2: Enlaces a Sedes -->
              <div class="col-md-4 mb-4">
                  <h5 class="fw-bold text-warning mb-3">Nuestras Sedes Académicas</h5>
                  <ul class="list-unstyled">
                     
                        @foreach ($sedes as $sede)
                            <li><a class="text-light text-decoration-none" href="/sedes/{{encrypt($sede->id)}}">{{ ucwords(strtolower($sede->nombre)) }}</a></li>
                        @endforeach    
                  </ul>
              </div>
              
              <!-- Columna 3: Universidad y redes -->
              <div class="col-md-4 mb-4">
                  <img src="{{ asset('assets/upea_logo.webp') }}" alt="logo upea" style="max-width: 180px;" class="mb-3">
                  <h5 class="fw-bold text-warning mb-3">Institución</h5>
                  <p class="mb-2 text-decoration-underline">
                      Universidad Pública de El Alto (UPEA)
                  </p>
                  <h6 class="fw-bold mt-4 mb-2 text-warning">Síguenos</h6>
                  <a href="#" class="text-light me-3"><i class="fab fa-facebook"></i></a>
                  <a href="#" class="text-light me-3"><i class="fab fa-twitter fa-lg"></i></a>
                  <a href="#" class="text-light"><i class="fab fa-youtube fa-lg"></i></a>
              </div>
          </div>

          <!-- Separador -->
          <hr class="border  my-3">

          <!-- Derechos reservados -->
          <div class="text-center small fw-semibold">
              &copy; 2025 Disbedc Inc. Todos los derechos reservados.
          </div>
      </div>
  </footer>
