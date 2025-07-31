// Define las funciones de notificación utilizando SweetAlert
const notificaciones = {
  'exito': (mensaje = "", titulo = "") => {
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
      icon: "success",
      title: mensaje,
    })
  },
  'error': (mensaje = "", titulo = "") => {
    Swal.fire({
      position: "top-end",
      icon: 'error',
      title: titulo,
      text: mensaje,
      showConfirmButton: false,
      timer: 1800,
    });
  },
  'warning': (mensaje = "", titulo = "") => {
    Swal.fire({
      position: "top-end",
      icon: 'warning',
      title: titulo,
      text: mensaje,
      showConfirmButton: false,
      timer: 1800,
    });
  },

  'error_validacion': (mensaje = "", titulo = "") => {

    Command: toastr["error"](mensaje);
  },
  'errores': (obj) => {

    try {
      // console.log(obj);
      for (let key in obj) {
        // console.log(key);
        document.getElementById('_' + key).innerHTML = `<p class="text-danger">${obj[key]}</p>`;
      }
    } catch (error) {
     console.log(error)
    }

  }
  // Puedes agregar más tipos según sea necesario
};


export function mensajeAlerta(mensaje = "", titulo = "") {

  if (notificaciones.hasOwnProperty(titulo)) {
    notificaciones[titulo](mensaje, titulo);
  }

}


