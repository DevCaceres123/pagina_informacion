
// Esta funcion asignara valores a los campos correspondientes
export function asingarValoresACampos(campos)
{
     // Utilizar Object.entries() y forEach para asignar valores a los inputs
     Object.entries(campos).forEach(([propiedad, valor]) => {
        document.getElementById(propiedad).value = valor;
    });

}

// Esta funcion desabilitara campos para que nose puedan utilizar
export function desabilitarCampos(campos){

    campos.forEach(element => {
        $(`#${element}`).prop("disabled", true);

    });
}

export function habilitarCampos(campos){

    campos.forEach(element => {
        $(`#${element}`).prop("disabled", false);

    });
}





