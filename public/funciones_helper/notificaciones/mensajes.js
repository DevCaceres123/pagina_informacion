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
      //console.log("🔴 Errores recibidos:", obj);

      for (let key in obj) {
        let elementId;

        // Si el key tiene un índice (ej: 'planos.0', 'files.2')
        if (key.includes('.')) {
          // Tomamos solo la parte del array antes del punto
          const baseKey = key.split('.')[0];
          elementId = '_' + baseKey; // todos los errores del array van en el mismo div
        } else {
          elementId = '_' + key;
        }

        const element = document.getElementById(elementId);

        if (element) {
          console.log(`✅ Mostrando error en: ${elementId} (key: ${key})`);
          // Si ya hay errores anteriores, los concatenamos
          element.innerHTML += `<p class="text-danger">${obj[key]}</p>`;
        } else {
          console.warn(`⚠️ No se encontró el elemento con id para: ${key}`);
        }
      }
    } catch (error) {
      console.error("❌ Error procesando los errores:", error);
    }
  }
  // Puedes agregar más tipos según sea necesario
};


export function mensajeAlerta(mensaje = "", titulo = "") {

  if (notificaciones.hasOwnProperty(titulo)) {
    notificaciones[titulo](mensaje, titulo);
  }

}


