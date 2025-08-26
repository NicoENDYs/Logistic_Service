@csrf
<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm text-gray-600">Placa</label>
        <input name="plate_number" value="{{ old('plate_number', $vehicle->plate_number ?? '') }}"
            class="w-full rounded-lg border-gray-300" required minlength="5" />
        <x-input-error :messages="$errors->get('plate_number')" class="mt-1" />
    </div>
    <div>
        <label class="block text-sm text-gray-600">Marca</label>
        <input name="brand" value="{{ old('brand', $vehicle->brand ?? '') }}" class="w-full rounded-lg border-gray-300"
            required />
        <x-input-error :messages="$errors->get('brand')" class="mt-1" />
    </div>
    <div>
        <label class="block text-sm text-gray-600">Modelo</label>
        <input name="model" value="{{ old('model', $vehicle->model ?? '') }}"
            class="w-full rounded-lg border-gray-300" required />
        <x-input-error :messages="$errors->get('model')" class="mt-1" />
    </div>
    <div>
        <label class="block text-sm text-gray-600">Capacidad (kg)</label>
        <input type="number" min="1" name="capacity" value="{{ old('capacity', $vehicle->capacity ?? '') }}"
            class="w-full rounded-lg border-gray-300" required />
        <x-input-error :messages="$errors->get('capacity')" class="mt-1" />
    </div>
    <div>
        <label class="block text-sm text-gray-600">Estado</label>
        <select name="status" class="w-full rounded-lg border-gray-300" required>
            @foreach (['activo', 'mantenimiento', 'inactivo'] as $opt)
                <option value="{{ $opt }}" @selected(old('status', $vehicle->status ?? 'activo') === $opt)>
                    {{ ucfirst($opt) }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-1" />
    </div>
</div>
<div class="mt-6 flex items-center gap-2">
    <a href="{{ route('vehicles.index') }}" class="px-3 py-2 rounded-lg border">Cancelar</a>
    <button class="px-3 py-2 rounded-lg bg-gray-900 text-white">Guardar</button>
</div>
