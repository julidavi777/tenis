<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
    <x-flowbite.label for="club">
        Club <span>*</span> 
    </x-flowbite.label>

    <x-flowbite.select :id="'club'"  class="{{ $clases }}" required>

        <option value="0">-- Seleccione --</option>

        @foreach ($clubes as $club)
            <option value="{{ $club->c10_club_id }}">{{ ucfirst( strtolower( $club->c10_club_nombre ) ) }}</option>
        @endforeach

    </x-flowbite.select> 
</div>