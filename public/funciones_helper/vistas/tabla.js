// esta funcion nos dibujara los datos obtenidos en la tabla

export function listarDatosEntabla(arrayDatos, nombreTabla) {
    let cont = 1;
    let nuevoElemento = "";
    if (arrayDatos.mensaje != "no hay datos") {
        arrayDatos.forEach(element => {
            nuevoElemento +=
                `<tr id='${element.id}'>
            <td>${cont++}</td>
            <td class='editar'>${element.titulo}</td>
            <td>${element.descripcion}</td>
			<td>${element.create_date}</td>
            <td><button class='eliminar btn btn-danger' >Eliminar</button>
            <button class='editar btn btn-warning' >Editar</button>
            </td>
            
            </tr>`;
        });
    }
    else {
        nuevoElemento = `            
            <td class="text-danger p-0  " style=" position:absolute ">Ningun dato encontrado  </td>
        `
    }
    nombreTabla.innerHTML = nuevoElemento;
}