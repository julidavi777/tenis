<div class="{{ $clases }}">

    <x-flowbite.select :id="$torneoId" class="border-none" required>

        @if (count($lista_torneos) > 0)
            <option value="0">---  Seleccionar  ----</option>

            @foreach ($lista_torneos as $torneo)
                <option value="{{ $torneo->c20_torneo_id }}" @php ($torneoSeleccionado == $torneo->c20_torneo_id) ? 'selected' : '' @endphp >
                    {{ ucfirst( strtolower($torneo->c20_torneo_edicion) ) }}
                </option>
            @endforeach
        @else
            <option value="" selected>No hay torneos disponible por el momento.</option>
        @endif
        
    </x-flowbite.select>

</div>