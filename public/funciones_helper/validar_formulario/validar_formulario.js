import { mensajeAlerta } from '../notificaciones/mensajes.js';
import { mensajeInputs } from '../vistas/formulario.js';

// Expresiones regulares

export const expresionesRegulares = {
    ci: {
        expresion: /^[a-zA-Z0-9]{6,10}$/,
        mensaje: 'El ci debe tener de 6 a 10 digitos no se permiten espacios ni simbolos',
    },
    nombres: {
        expresion: /^[a-zA-ZÀ-ÿ\s]{2,20}$/,
        mensaje: 'El nombre debe tener de 2 a 20 palabras no se permiten simbolos',
    },
    apellido: {
        expresion: /^[a-zA-ZÀ-ÿ]{4,20}$/,
        mensaje: 'El apellido debe tener de 4 a 20 palabras no se permiten espacios ni simbolos',
    },
    correo: {
        expresion: /^[a-zA-Z0-9_.+-]+@(gmail|hotmail)+\.(com|bo)+$/,
        mensaje: 'El correo escrito no es valido',
    },
    campoNormal: {
        expresion: /^[a-zA-ZÀ-ÿ]{4,20}$/,
        mensaje: 'El campo debe tener de 4 a 20 palabras no se permiten espacios ni simbolos',
    },
    detalle: {
        expresion: /^[a-zA-ZÀ-ÿ\s]{4,100}$/,
        mensaje: 'El detalle debe tener de 4 a 100 palabras no se permiten simbolos',
    },
    celular: {
        expresion: /^(?=.*[0-9]).{8}$/,
        mensaje: 'El celular deber tener 8 numeros no se permiten simbolos ni letras',
    },
    usuario: {
        expresion: /^(?=.*[a-z])(?=.*[-_])(?=.*[0-9]).{6,20}$/,
        mensaje: 'El usuario debe contener de 6-10 palabras incluyendo (- _) y numeros, no se permiten espacios',
    },
    contrasenia: {
        expresion: /^(?=.*[!@#$%^&*()_+{}\[\]:;<>,.?~\\-])(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{12,20}$/,
        mensaje: 'La contraseña debe tener una Mayuscula un Caracerter especial $ @ & _ y  un numero',
    },
    fecha: {
        expresion: /^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/,
        mensaje: 'La fecha no tiene un formato correcto Año-Mes-Dia (2024-01-12)',
    },
    horaFormato24: {
        expresion: /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/,
        mensaje: 'La hora no cumple con el formato establecido 24h',
    },
    horaFormato12: {
        expresion: /^(0[1-9]|1[0-2]):[0-5][0-9] [APap][Mm]$/,
        mensaje: 'La hora no cumple con el formato establecido 12h',
    },

}

export const validarCamposDelFormulario = (expresion, input, mensaje, campo) => {


    let elemento = document.getElementById(campo);

    let elemento_icono = document.querySelector(`#group_${campo} i`);

    console.log(elemento+elemento_icono);

    if (elemento == null || elemento_icono == null) {
        mensajeAlerta('Campo no encontrado ' + campo, "warning");
        return false;
    }

    if (expresion.test(input.value)) {

        mensajeInputs("Correcto puede continuar", "#4ecd8b", campo);

        // cambiar el color de los inputs
        elemento.classList.remove('errorInput');
        elemento.classList.add('exitoInput');

        elemento_icono.classList.remove("mdi-close-circle");
        elemento_icono.classList.add("mdi-check-circle");
        elemento_icono.style.color = "#4ecd8b";

    }
    else {

        mensajeInputs(mensaje, "#dd3333", campo);
        // cambiar el color de los inputs
        elemento.classList.remove('exitoInput');
        elemento.classList.add('errorInput');

        elemento_icono.classList.add("mdi-close-circle");
        elemento_icono.classList.remove("mdi-check-circle");
        elemento_icono.style.color = "#dd3333";
    }
}


export const verificarCamposDelFormulario = (camposDelFormulario) => {

    let contadorCamposCorrectos=0;


    camposDelFormulario.forEach(element => {

        let campo = element['campo'];
        let expresionDelCampo = element['expresion'];
        

        if (campo.tagName == "SELECT") {
            if (element.value == "Seleccione una opción" || element.value == "") {
                mensajeInputs("Debe seleccionar algun rol", "#dd3333",);
            }
            else {
                mensajeInputs("Correcto puede continuar", "#4ecd8b", "rol_usuario");
                contadorCamposCorrectos++;
            }
        }

        if (campo.tagName == "INPUT" || campo.tagName == "TEXTAREA") 
        {
            // obtenemos la expresion a validar
            let expresion=expresionesRegulares[expresionDelCampo].expresion;
            let expresionMensaje=expresionesRegulares[expresionDelCampo].expresion;
            
  
            if (expresion.test(campo.value)) {
                contadorCamposCorrectos++;
            }
    
            else {
    
                validarCamposDelFormulario(expresion,campo,expresionMensaje, campo.id);
            }
        }
        


        return contadorCamposCorrectos;

    });
}