<!DOCTYPE html>
<html lang="es" data-startbar="dark" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <title>LOGIN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/disbedc_logo.webp') }}">

    <!-- App css -->
    <link href="{{ asset('admin_template/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin_template/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin_template/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        body {
            background: url('assets/perfil3.jpeg') no-repeat center center;
            background-size: cover;
        }

        .fondo {
            width: 100%;
            height: 100vh;
            background-color: rgba(0, 0, 0, .8);
        }

        #logo {
            width: 75%;
            height: 70%;
            object-fit: contain;
        }

        #btn_vista {

            position: absolute;
            top: 50%;
            bottom: 50%;
            right: 10px;
            margin-left: -35px;
            margin-top: 5px;
            color: #110c09;
        }

        .captcha-box {
            width: 100%;
            height: 60px;
            display: flex;
            /* 🔑 clave */
            border: 1px solid #dee2e6;
            border-radius: 6px;
            overflow: hidden;
            background: #fff;
        }

        .captcha-img {

            width: 90%;
            /* 90% imagen */
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .captcha-refresh {
            width: 10%;
            /* 10% botón */
            height: 100%;
            border: none;
            border-left: 1px solid #dee2e6;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .captcha-refresh:hover {
            background: #e9ecef;
        }

        .captcha-refresh i {
            font-size: 14px;
        }
    </style>
</head>

<!-- Top Bar Start -->

<body>
    <div class="fondo">
        <div class="container-xxl ">
            <div class="row vh-100 d-flex justify-content-center">
                <div class="col-12 align-self-center">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 mx-auto">
                                <div class="card rouneded">
                                    <div class="card-body p-0 auth-header-box">

                                        <div class="bg-holder"
                                            style="background-image:url(pagina_template/assets/img/perfil3.jpeg);">
                                            <div class="text-center p-3" style="background-color:rgba(0, 0, 0, .1);">
                                                <img src="{{ asset('assets/logo_d.png') }}" alt="logo"
                                                    class="auth-logo"id="logo">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="card-body pt-0 border rounded-bottom">
                                        <div class="text-center py-2" id="mensaje_error"></div>
                                        <form id="formulario_login" autocomplete="off">
                                            @csrf
                                            <div class="form-group mb-2">
                                                <label class="form-label" for="usuario">Usuario</label>
                                                <input type="text" class="form-control" id="usuario" name="usuario"
                                                    placeholder="Ingrese usuario">
                                            </div><!--end form-group-->

                                            <div class="form-group" style="position: relative">
                                                <label class="form-label" for="password">Contraseña</label>
                                                <input type="password" class="form-control" name="password"
                                                    id="password" placeholder="Ingrese la contraseña">
                                                <a type="button" onclick="togglePassword()" id="btn_vista"><i
                                                        class="fas fa-eye-slash fs-18" id="icono_password"></i></a>
                                            </div>

                                            <div class="d-flex flex-column align-items-center mt-2">
                                                <p class="text-muted">Codigo de verificacion</p>
                                                <div class="captcha-box row">

                                                    <img src="{{ captcha_src() }}" alt="captcha" class="captcha-img">

                                                    <button type="button" class="captcha-refresh"
                                                        onclick="refreshCaptcha()" title="Recargar captcha">
                                                        <i class="fas fa-redo"></i>
                                                    </button>
                                                </div>


                                                <div class="form-floating input-placa mt-3">

                                                    <input type="text" id="captcha" name="captcha"
                                                        class="form-control form-control-lg fw-bolder text-center fs-2 border-0 border-bottom border-primary rounded-0 placa-input-estilo"
                                                        placeholder="Ingrese el código" required>

                                                    <label for="captcha" class="text-muted text-uppercase">Ingrese el codigo captcha</label>
                                                </div>
                                            </div> 



                                            <div class="form-group mb-0 row">
                                                <div class="col-12">
                                                    <div class="d-grid mt-3">
                                                        <button class="btn text-light btn-primary" type="submit"
                                                            id="btn_ingresar_usuario">INGRESAR <i
                                                                class="fas fa-sign-in-alt ms-1"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>

<script>
    // Seleccionar elementos del DOM
    let loginBtn = document.getElementById('btn_ingresar_usuario');
    let formularioLogin = document.getElementById('formulario_login');
    let mensajeError = document.getElementById('mensaje_error');

    // Función para crear y mostrar alertas
    function mostrarAlerta(tipo, mensaje) {
        let iconoClase = tipo === 'success' ? 'fa-check' : 'fa-xmark';
        let color = tipo === 'success' ? 'success' : 'danger';
        mensajeError.innerHTML = `
        <div class="alert alert-${color} shadow-sm border-theme-white-2" role="alert">
            <div class="d-inline-flex justify-content-center align-items-center thumb-xs bg-${color} rounded-circle mx-auto me-1">
                <i class="fas ${iconoClase} align-self-center mb-0 text-white"></i>
            </div>
            <strong>${mensaje}</strong>
        </div>
        `;
        // Configurar el temporizador para ocultar la alerta después de 5 segundos
        setTimeout(() => {
            mensajeError.innerHTML = '';
        }, 4000);
    }

    // Función para validar el botón
    function validarBoton(estaDeshabilitado, mensaje) {
        loginBtn.textContent = mensaje;
        loginBtn.disabled = estaDeshabilitado;
    }

    // Manejar el envío del formulario
    loginBtn.addEventListener('click', async (e) => {
        let datos = Object.fromEntries(new FormData(formularioLogin).entries());
        validarBoton(true, "Verificando datos...");
        try {
            let respuesta = await fetch("{{ route('log_ingresar') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(datos)
            });
            if (!respuesta.ok) {
                throw new Error(`HTTP error! status: ${respuesta.status}`);
            }
            let data = await respuesta.json();
            mostrarAlerta(data.tipo, data.mensaje);
            if (data.tipo === 'success') {
                validarBoton(true, 'Datos correctos...');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                validarBoton(false, 'INGRESAR');
                refreshCaptcha();
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarAlerta('error', 'Ocurrió un error al procesar la solicitud');
            validarBoton(false, 'INGRESAR');
        }
    });


    function togglePassword() {
        let passwordInput = document.getElementById("password");
        let iconPassword = document.getElementById("icono_password");

        // Cambiar el tipo de input primero
        let type = passwordInput.type === "password" ? "text" : "password";
        passwordInput.type = type;

        // Ahora cambiar el icono según el nuevo tipo
        if (type === "text") {
            iconPassword.classList.remove("fas", "fa-eye-slash", "fs-18"); // Remover clases por separado
            iconPassword.classList.add("fas", "fa-eye", "fs-18"); // Agregar clases por separado
        } else {
            iconPassword.classList.remove("fas", "fa-eye", "fs-18"); // Remover clases por separado
            iconPassword.classList.add("fas", "fa-eye-slash", "fs-18"); // Agregar clases por separado
        }
    }

    // actualizar capcha
    function refreshCaptcha() {
        fetch('/cambiar_capcha')
            .then(response => response.json())
            .then(data => {
                // Actualiza la URL de la imagen CAPTCHA en el DOM

                document.querySelector('img[alt="captcha"]').src = data.captcha;
            })
            .catch(error => console.error('Error al recargar el CAPTCHA:', error));
    }
</script>
