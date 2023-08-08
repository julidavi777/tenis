import Jugador from './DataJugador';
/** 
 * Función que se usa dos veces para hacer lo mismo, se puede optimizar:
 * 
 *  1) Se usa para obtener los datos cuando da click en el botó de editar.
 *  2) Se usa cuando se consulta el jugador en el input de documento en el modal.
*/
function getDataJugador(documento)
{
    const getDatosJugador = new Jugador(documento);

    getDatosJugador.datosJugador;
}

/** 
 * INICIO - Funciones que solo se usa dentro de la función lista_judadores
*/

async function createTBody(judadores) 
{
    judadores.forEach(jugador => {

        const tabla_tbody = document.getElementById('table-tbody');
        const tbody_tr = document.createElement('tr');

        tbody_tr.classList.add('bg-white', 'border-b', 'dark:bg-gray-800', 'dark:border-gray-700', 'hover:bg-gray-50', 'dark:hover:bg-gray-600');

        // c15_jugador_id
        const tbody_th_jugador_documento = document.createElement('th');

        tbody_th_jugador_documento.classList.add('text-right', 'px-4', 'py-4', 'font-medium', 'text-gray-900', 'whitespace-nowrap', 'dark:text-white');
        tbody_th_jugador_documento.setAttribute('scope', 'row');
        tbody_th_jugador_documento.textContent = jugador.documento;


        tbody_tr.appendChild(tbody_th_jugador_documento);

        // c15_jugador_nombres
        tbodyTD(jugador.nombres, tbody_tr);

        // c15_jugador_apellidos
        tbodyTD(jugador.apellidos, tbody_tr);

        // c15_jugador_genero
        tbodyTD(jugador.genero, tbody_tr);

        // c15_jugador_pais_residencia
        tbodyTD(jugador.pais_residencia, tbody_tr);

        // c15_jugador_departamento_residencia
        tbodyTD(jugador.departamento_residencia, tbody_tr);

        // c15_jugador_ciudad_residencia
        tbodyTD(jugador.ciudad_residencia, tbody_tr);

        // c10_club_nombre
        tbodyTD(jugador.club, tbody_tr);

        //c15_jugador_fecha_nacimiento
        tbodyTD(jugador.fecha_nacimiento, tbody_tr);

        // Actions
        const tbody_td_acciones = document.createElement('td');

        tbody_td_acciones.classList.add('flex', 'flex-col', 'px-5', 'py-4');

        // Editar
        btnAcciones('Editar', jugador.documento, tbody_td_acciones);
        // Inscribir
        btnAcciones('Inscribir', jugador.documento, tbody_td_acciones);
        // Eliminar
        btnAcciones('Eliminar', jugador.documento, tbody_td_acciones);

        tbody_tr.appendChild(tbody_td_acciones);

        tabla_tbody.appendChild(tbody_tr);

    });
}

    /** 
     * INICIO - Funciones que solo se usa dentro de la función createTBody
    */
    function tbodyTD(dato_judador = '', tbody_tr)
    {
        const tbody_td = document.createElement('td');

        tbody_td.classList.add('px-4', 'py-4');
        tbody_td.textContent = dato_judador;

        tbody_tr.appendChild(tbody_td);
    }

    function btnAcciones(accion = '', documento, tbody_td_acciones)
    {
        const td_btn = document.createElement('button');

        if (accion === 'Editar')
            td_btn.classList.add('btn' + accion, 'mb-1', 'font-medium', 'text-blue-600', 'dark:text-blue-500', 'hover:underline');
        else if (accion === 'Inscribir')
            td_btn.classList.add('btn' + accion, 'mb-1', 'font-medium', 'text-green-600', 'dark:text-green-500', 'hover:underline');
        else if (accion === 'Eliminar')
            td_btn.classList.add('btn' + accion, 'mb-1', 'font-medium', 'text-red-600', 'dark:text-red-500', 'hover:underline');

        td_btn.textContent = accion;
        td_btn.setAttribute('data-jugador-href', documento);

        tbody_td_acciones.appendChild(td_btn);
    }

    /** 
     * FIN - Funciones que solo se usa dentro del create tbody
    */

async function getListaJuadores(ruta = "") 
{
    /*
        const options = {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
        };
    */
    const response = await fetch(ruta);
    let lista_judadores = [];
    let jugadores = await response.json();

    jugadores = JSON.parse(jugadores);

    jugadores.forEach(jugador => {
        lista_judadores.push({
            documento: jugador.c15_jugador_id,
            nombres: jugador.c15_jugador_nombres,
            apellidos: jugador.c15_jugador_apellidos,
            genero: jugador.c15_jugador_genero,
            pais_residencia: jugador.c15_jugador_pais,
            departamento_residencia: jugador.c15_jugador_departamento,
            ciudad_residencia: jugador.c15_jugador_municipio,
            club: jugador.c15_club_nombre,
            fecha_nacimiento: jugador.c15_jugador_fecha_nacimiento
        });
    });

    return lista_judadores;
}

function configBtnAcciones()
{
    let btnEditar = document.getElementsByClassName('btnEditar');

    Array.from(btnEditar).forEach((elemento, key) => {

        elemento.addEventListener('click', () => {
            /**
             * Se repite por primera vez.
             */
            getDataJugador(elemento.dataset.jugadorHref);
        });
    });

    let btnInscribir = document.getElementsByClassName('btnInscribir');

    Array.from(btnInscribir).forEach((elemento, key) => {

        elemento.addEventListener('click', () => {

            let documento = elemento.dataset.jugadorHref;

            alert(`jugador con documento : ${documento} inscrito correctamente.`);
            /*
                getDataJugador()
                .then( jugador => {
                    organizarDatosModal(jugador);
                })
                .catch(error => {
                    alert(error);
                    console.error(error);
                });
            */
        });
    });

    let btnEliminar = document.getElementsByClassName('btnEliminar');

    Array.from(btnEliminar).forEach((elemento, key) => {

        elemento.addEventListener('click', () => {

            let documento = elemento.dataset.jugadorHref;

            if (!confirm(`¿Está seguro de eliminar al jugador con el siguiente documento: ${documento}?`))
                return;

            alert('eliminado');
            /*
                getDataJugador()
                .then( jugador => {
                    organizarDatosModal(jugador);
                })
                .catch(error => {
                    alert(error);
                    console.error(error);
                });
            */
        });

    });
}

function lista_judadores() {
    let ruta = route('inscripciones.lista.jugadores');
    console.log(ruta);

    getListaJuadores(ruta)
        .then(jugadores => {
            // Función asíncrona
            createTBody(jugadores);
            configBtnAcciones();
        });
}

lista_judadores();

/** 
 * FIN - Funciones que solo se usa dentro de la función lista_judadores
*/

/**
 * INICIO - Campos del modal
 */

// Fecha nacimiento

import 'flowbite/dist/datepicker';

// Documento

document.addEventListener('alpine:init', () => {
    Alpine.data('documento', () => ({
        documento_jugador: '',

        setDocumentoJugador: 
        {
            ['@keyup.enter']()
            {
                getDataJugador(this.documento_jugador);
            }
        },
        /*
        getDocumentoJugador: 
        {
            ['x-text']()
            {
                return this.documento_jugador;
            }
        }
        */
    }))
})

// País

//const prefijo_pais = "+";
let pais = document.getElementById('pais_residencia'); 
let selected = pais.selectedOptions;
let pais_defecto = selected[0].label;

//getSoloColombia(pais_defecto, (prefijo_pais + selected[0].dataset.phoneCode));
getSoloColombia(pais_defecto);

pais.addEventListener('change', () => {
    //getSoloColombia(selected[0].label, (prefijo_pais + selected[0].dataset.phoneCode));
    getSoloColombia(selected[0].label);
});

function getSoloColombia(pais)
{
    let div_class_property = (pais == 'Colombia') ? 'visible' : 'invisible';
    let solo_colombia = document.getElementById('solo-colombia');

    //document.getElementById('codigo_pais').innerText = codigo_pais;
    //document.getElementById('codigo_tel').value = codigo_pais;

    ( solo_colombia.classList.remove('visible') || solo_colombia.classList.remove('invisible') );

    solo_colombia.classList.add(div_class_property);

    console.log(pais, solo_colombia.className);
}