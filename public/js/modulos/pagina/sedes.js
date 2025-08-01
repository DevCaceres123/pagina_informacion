
    document.addEventListener('DOMContentLoaded', () => {

        const inputBuscar = document.getElementById('buscadorCarreras');
        const contenedor = document.getElementById('contenedorCarreras');
        const botonListarTodo = document.getElementById('listarTodo');

        let temporizador = null;

        inputBuscar.addEventListener('input', function () {
            const valor = this.value.trim();

            clearTimeout(temporizador);

            if (valor.length >= 4) {
                temporizador = setTimeout(() => {
                    buscarCarreras(valor);
                }, 500);
            } else {
                contenedor.innerHTML = '';
            }
        });

        botonListarTodo.addEventListener('click', async () => {
            await buscarCarreras('');
        });

        async function buscarCarreras(termino) {
            try {
                const res = await fetch('/buscarCarrera', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ query: termino })
                });

                const data = await res.json();

                if (data.tipo !== 'exito') {
                    contenedor.innerHTML = `<div class="col-12"><div class="alert alert-warning">${data.mensaje}</div></div>`;
                    return;
                }

                renderizarCarreras(data.datos);

            } catch (err) {
                console.error('Error al buscar carreras:', err);
                contenedor.innerHTML = `<div class="col-12"><div class="alert alert-danger">Ocurrió un error al buscar.</div></div>`;
            }
        }

        function renderizarCarreras(carreras) {
            contenedor.innerHTML = '';

            carreras.forEach(carrera => {
                contenedor.innerHTML += `
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <h5 class="card-title">
                                    <i class="fas fa-laptop-code me-2 text-primary"></i> ${carrera.nombre}
                                </h5>
                                <div class="mt-3">
                                    <a href="/mallas/${carrera.archivo_malla}" class="btn btn-danger btn-sm mb-2 w-100 d-flex align-items-center justify-content-center gap-2" download>
                                        <i class="fas fa-download"></i> Descargar Malla
                                    </a>
                                    <a href="${carrera.url}" target="_blank" class="btn btn-outline-dark btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                                        <i class="fas fa-globe"></i> Ver Página
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        }

    });

