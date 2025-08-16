<!-- Core Libraries -->
<script src="{{ asset('admin_template/libs/jquery/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('admin_template/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('admin_template/libs/simplebar/simplebar.min.js') }}"></script>

<!-- Data & Map Libraries -->
<script src="{{ asset('admin_template/data/stock-prices.js') }}"></script>
<script src="{{ asset('admin_template/libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
<script src="{{ asset('admin_template/libs/jsvectormap/maps/world.js') }}"></script>

<!-- DataTables -->
<script src="{{ asset('admin_template/libs/datatables/js/datatables.min.js') }}"></script>

<!-- SweetAlert -->
<script src="{{ asset('admin_template/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('admin_template/js/pages/sweet-alert.init.js') }}"></script>

<!-- SlimSelect -->
<script src="{{ asset('admin_template/libs/slimselect/slimselect.min.js') }}"></script>

<!-- Pikaday Date Picker -->
<script src="{{ asset('admin_template/libs/pikaday/pikaday.js') }}"></script>

<!-- App Main Script -->
<script src="{{ asset('admin_template/js/app.js') }}"></script>

{{-- Para dar funcionalidad al selector js --}}
<script src="{{ asset('admin_template/libs/mobius1-selectr/selectr.min.js') }}"></script>

<script>
    //token
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');


    window.addEventListener('load', function() {
        // Deshabilitar el scroll de la ventana
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            var loader = document.getElementById('loading-wrapper');
            loader.style.display = 'none';

            // Habilitar el scroll después de ocultar el elemento
            document.body.style.overflow = 'auto';
        }, 500); // Reduje el tiempo a 1000 ms (1 segundo) para pruebas, puedes ajustarlo según sea necesario
    });

    //MIXIN alerta
    function alerta_top(tipo, mensaje) {
        Swal.mixin({
            toast: !0,
            position: "top-end",
            showConfirmButton: !1,
            timer: 1500,
            timerProgressBar: !0,
            didOpen: e => {
                e.addEventListener("mouseenter", Swal.stopTimer), e.addEventListener("mouseleave", Swal
                    .resumeTimer)
            }
        }).fire({
            icon: tipo,
            title: mensaje,
        })
    }

    //para cerrar la session
    let btn_cerrar_session = document.getElementById("btn-cerrar-session");
    btn_cerrar_session.addEventListener("click", async () => {
        await cerrar_session_cam();
    });

    //PARA CERRAR LA CESSION DESPUES DE HABER CAMBIADO
    async function cerrar_session_cam() {
        let datos = Object.fromEntries(new FormData(document.getElementById('formulario_salir')).entries());
        try {
            let respuesta = await fetch("{{ route('salir') }}", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(datos)
            });
            let dato = await respuesta.json();
            alerta_top(dato.tipo, dato.mensaje);
            setTimeout(() => {
                location.reload();
            }, 1500);
        } catch (error) {
            console.log('Ocurrio un error: ' + error);
        }
    }

    //para validar el boton
    function validar_boton(valor, msj, boton) {
        let boton_env = boton;
        boton_env.disabled = valor;
        boton_env.innerHTML = '';
        if (valor) {
            boton_env.innerHTML = `<span class="spinner-border spinner-border-sm"></span>&nbsp;${msj}`;
        } else {
            boton_env.innerHTML = msj;
        }
    }

    //para vaciar los errores
    function vaciar_errores(array) {
        let nuevo_array = array;
        nuevo_array.forEach(element => {
            document.getElementById(element).innerHTML = '';
        });
    }

    //para mostrar los errores/ iterramos
    function mostrarErrores(obj) {
        for (let key in obj) {
            document.getElementById('_' + key).innerHTML = `<p class="text-danger">${obj[key]}</p>`;
        }
    }

    //para vaciar los input, textrarea y select
    function vaciar_formulario(formulario) {
        Array.from(formulario.elements).forEach(elemento => {
            // Verifica si es un campo de formulario (input, textarea, select)
            if (['input', 'textarea', 'select'].includes(elemento.tagName.toLowerCase())) {
                // Limpia el valor del campo
                elemento.value = '';
            }
        });
    }

    //para desabilitar el enter
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('input[type=text]').forEach(node => node.addEventListener('keypress', e => {
            if (e.keyCode == 13) {
                e.preventDefault();
            }
        }))
    });

    new SlimSelect({
        select: '#selectInp'
    })
</script>
