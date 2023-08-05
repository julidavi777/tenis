<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inscripciones') }}
        </h2>
    </x-slot>
    <div class="py-12">
        @push('styles')
            <style>
                span {
                    color: red;
                }
            </style>
        @endpush
        @push('scripts')
            <script>
                async function createTBody(judadores)
                {
                    judadores.forEach(jugador => {
                        const tabla_tbody = document.getElementById('table-tbody');
                        const tbody_tr = document.createElement('tr');

                        tbody_tr.classList.add('bg-white', 'border-b', 'dark:bg-gray-800', 'dark:border-gray-700', 'hover:bg-gray-50', 'dark:hover:bg-gray-600');

                        // c15_jugador_id
                        const tbody_th_jugador_documento = document.createElement('th');

                        tbody_th_jugador_documento.classList.add('px-4', 'py-4', 'font-medium', 'text-gray-900', 'whitespace-nowrap', 'dark:text-white');
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

                        btnAcciones('Eliminar', jugador.documento, tbody_td_acciones);

                        tbody_tr.appendChild(tbody_td_acciones);

                        tabla_tbody.appendChild(tbody_tr);

                    });
                }

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
                            documento:  jugador.c15_jugador_id,
                            nombres:    jugador.c15_jugador_nombres,
                            apellidos:    jugador.c15_jugador_apellidos,
                            genero: jugador.c15_jugador_genero,
                            pais_residencia: jugador.c15_jugador_pais,
                            departamento_residencia: jugador.c15_jugador_departamento,
                            ciudad_residencia:   jugador.c15_jugador_municipio,
                            club: jugador.c15_club_nombre,
                            fecha_nacimiento:    jugador.c15_jugador_fecha_nacimiento
                        });
                    });

                    return lista_judadores;
                }

                async function getDataJugador(documento)
                {
                    const response = await fetch("{{ route('inscripciones.data.jugador') }}?" + new URLSearchParams({
                            documento: documento
                        }).toString());

                    if(response.ok)
                        return response.json().then( jugador => JSON.parse(jugador)[0] );

                    return response.json().then( mensaje => { throw new Error(mensaje.documento) } );
                }
            </script>
        @endpush

        <div class="max-w-max mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <!-- Modal toggle -->
                    <x-flowbite.button class="mb-6" data-modal-target="inscribirUsuario" data-modal-toggle="inscribirUsuario" type="button">
                        Inscribir jugador
                    </x-flowbite.button>
                    
                    <x-flowbite.modal :idModal="'inscribirUsuario'" data-modal-backdrop="static">
                        <x-slot name="tituloModal">
                            Inscribir jugador
                        </x-slot>

                        <x-slot name="cuerpoModal">
                            <form id="formulario-jugador" class="h-80 overflow-y-auto">
                                <div class="grid gap-6 mb-6 px-10 md:grid-cols-2">

                                    <div class="mb-2" x-data="documento">

                                        <x-flowbite.label for="numero-identificacion">
                                            Número de identificacion <span>*</span> 
                                        </x-flowbite.label>
                                        
                                        <x-flowbite.input type="number" x-bind="setDocumentoJugador" x-model.number.lazy="documento_jugador" :id="'documento'" required/>

                                        {{-- buscando a: <span x-bind="getDocumentoJugador"></span> --}}
                                    </div>

                                    @push('scripts')
                                        <script>
                                            document.addEventListener('alpine:init', () => {
                                                Alpine.data('documento', () => ({
                                                    documento_jugador: '',
                                        
                                                    setDocumentoJugador: 
                                                    {
                                                        ['@keyup.enter']()
                                                        {
                                                            getDataJugador(this.documento_jugador)
                                                            .then(jugador => {
                                                                organizarDatosModal(jugador);
                                                            })
                                                            .catch(error => {
                                                                alert(error);
                                                                console.error(error);
                                                            });
                                                        }
                                                    },

                                                    getDocumentoJugador: 
                                                    {
                                                        ['x-text']()
                                                        {
                                                            return this.documento_jugador;
                                                        }
                                                    }
                                                }))
                                            })
                                        </script>
                                    @endpush

                                    <div class="mb-2">
                                        <x-flowbite.label for="fecha-nacimiento">
                                            Fecha nacimiento <span>*</span> 
                                        </x-flowbite.label>
        
                                        <x-flowbite.input datepicker datepicker-format="dd/mm/yyyy" type="text" 
                                            :id="'fecha_nacimiento'" placeholder="dd/mm/aaaa" required/>
                                    </div>

                                    @push('scripts')
                                        <script src="{{ asset('js/datepicker.js') }}"></script>
                                    @endpush
        
                                    <div class="mb-2">
                                        <x-flowbite.label for="nombres">
                                            Nombres <span>*</span> 
                                        </x-flowbite.label>
                                        
                                        <x-flowbite.input type="text" :id="'nombres'" required/>
                                    </div>

                                    <div class="mb-2">
                                        <x-flowbite.label for="apellidos">
                                            Apellidos <span>*</span> 
                                        </x-flowbite.label>
                                        
                                        <x-flowbite.input type="text" :id="'apellidos'" required/>
                                    </div>

                                    <div class="mb-2">
                                        <x-flowbite.label for="genero">
                                            Género <span>*</span> 
                                        </x-flowbite.label>
        
                                        <x-flowbite.select :id="'genero'" required>
                                            <option value="0" selected>-- Seleccione --</option>
                                            <option value="M" >Masculino</option>
                                            <option value="F" >Femenino</option>
                                        </x-flowbite.select>                         
                                    </div>

                                    <div class="mb-2">
                                        <x-inscripcion.club />
                                    </div>

                                    <div class="residencia col-span-2 grid grid-cols-2 gap-6">

                                        <div class="mb-2">
                                            <x-flowbite.label for="nacionalidad">
                                                Nacionalidad <span>*</span> 
                                            </x-flowbite.label>
                                            
                                            <x-flowbite.input type="text" :id="'nacionalidad'" required/>
                                        </div>

                                        <div class="mb-2">
                                            <x-inscripcion.pais />

                                            @push('scripts')
                                                <script>
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
        
                                                </script>
                                            @endpush
                                        </div>

                                        <div id="solo-colombia" class="col-span-2 grid grid-cols-2 gap-6">

                                            <livewire:inscripciones.departamento />

                                            <livewire:inscripciones.municipio />

                                        </div>
                                    </div>

                                </div>
                            </form>
                        </x-slot>

                        <x-slot name="footerModal">
                            <x-flowbite.button data-modal-hide="inscribirUsuario" type="button">
                                Crear
                            </x-flowbite.button>

                            <x-flowbite.button class="close-modal" data-modal-hide="inscribirUsuario" type="button" :color="'gray'">
                                Cancelar
                            </x-flowbite.button>
                        </x-slot>
                    </x-flowbite.modal>

                    <div class="relative w-full overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-4 py-3">
                                        Documento de identidad
                                    </th>
                                    <th scope="col" class="px-4 py-3">
                                        Nombres
                                    </th>
                                    <th scope="col" class="px-4 py-3">
                                        Apellidos
                                    </th>
                                    <th scope="col" class="px-4 py-3">
                                        Género
                                    </th>
                                    <th scope="col" class="px-4 py-3" colspan="3">
                                        País y ciudad de residencia
                                    </th>
                                    <th scope="col" class="px-4 py-3">
                                        Club
                                    </th>
                                    <th scope="col" class="px-4 py-3">
                                        Fecha nacimiento
                                    </th>
                                    <th scope="col" class="px-4 py-3">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>

                            <tbody id="table-tbody">

                                @push('scripts')
                                    <script>
                                        lista_judadores();

                                        function lista_judadores()
                                        {
                                            let ruta = "{{ route('inscripciones.lista.jugadores') }}";

                                            getListaJuadores(ruta)
                                            .then(jugadores => {
                                                createTBody(jugadores);

                                                configBtnAcciones();
                                            });
                                        }

                                        function configBtnAcciones()
                                        {
                                            let btnEditar = document.getElementsByClassName('btnEditar');

                                            Array.from(btnEditar).forEach((elemento, key) => {

                                                elemento.addEventListener('click', () => {

                                                    getDataJugador(elemento.dataset.jugadorHref)
                                                    .then( jugador => {
                                                        organizarDatosModal(jugador);
                                                    })
                                                    .catch(error => {
                                                        alert(error);
                                                        console.error(error);
                                                    });
                                                });
                                            });

                                            let btnEliminar = document.getElementsByClassName('btnEliminar');

                                            Array.from(btnEliminar).forEach((elemento, key) => {

                                                elemento.addEventListener('click', () => {

                                                    let documento = elemento.dataset.jugadorHref;

                                                    if(!confirm(`¿Está seguro de eliminar el siguiente documento: ${ documento }?`))
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
                                    </script>
                                @endpush
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
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

                if(accion === 'Editar')
                    td_btn.classList.add('btn' + accion, 'font-medium', 'text-blue-600', 'dark:text-blue-500', 'hover:underline');
                else
                    td_btn.classList.add('btn' + accion, 'font-medium', 'text-red-600', 'dark:text-red-500', 'hover:underline');

                td_btn.textContent = accion;
                td_btn.setAttribute('data-jugador-href', documento);

                tbody_td_acciones.appendChild(td_btn);
            }
        </script>
    @endpush

    @push('scripts')
        <script>
            function organizarDatosModal(jugador)
            {
                let datos_jugador = organizarDatosJugador( jugador );

                // set the modal menu element
                const targetEl = document.getElementById('inscribirUsuario');
                //const formulario = document.getElementById('formulario-jugador');

                const lista_inputs = document.getElementsByClassName('formulario-input');
                const lista_selects = document.getElementsByClassName('formulario-select');

                const options = {
                    //placement: 'bottom-right',
                    backdrop: 'static',
                    //backdropClasses: 'bg-gray-900 bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-40',
                    closable: true,
                    onHide: () => {
                        Array.from(lista_inputs).forEach((elemento, key) => {
                            //console.log(elemento.value);
                            elemento.value = '';
                        });

                        Array.from(lista_selects).forEach((elemento, key) => {
                            if(elemento.id != 'pais_residencia')
                                elemento.value = 0;
                        });
                    },
                    onShow: () => {
                        Array.from(lista_inputs).forEach(function(elemento, key) {
                            elemento.value = datos_jugador[elemento.id];
                        });

                        Array.from(lista_selects).forEach((elemento, key) => {
                            elemento.value = (datos_jugador[elemento.id] || 0);
                        });
                    }
                };

                const modal = new Modal(targetEl, options);

                modal.show();

                const closeModal = document.getElementsByClassName('close-modal');

                Array.from(closeModal).forEach((elemento, key) => {
                    elemento.addEventListener('click', () => {
                        modal.hide();
                    });
                });
            }

            function organizarDatosJugador (jugador)
            {
                return {
                    documento:  jugador.c15_jugador_id,
                    nombres:    jugador.c15_jugador_nombres,
                    apellidos:    jugador.c15_jugador_apellidos,
                    genero: jugador.c15_jugador_genero,
                    nacionalidad:   jugador.c15_jugador_nacionalidad,
                    pais_residencia: jugador.c15_jugador_pais_id,
                    departamento_residencia: jugador.c15_jugador_departamento_id,
                    ciudad_residencia:   jugador.c15_jugador_municipio_id,
                    club: jugador.c15_jugador_club_id,
                    fecha_nacimiento:    jugador.c15_jugador_fecha_nacimiento
                }
            }
        </script>
    @endpush
</x-app-layout>