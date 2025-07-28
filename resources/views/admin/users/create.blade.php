<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Usuario') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Formulario de Creación de Usuario</h3>

                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Nombre')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="password" :value="__('Contraseña')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="role" :value="__('Rol')" />
                            <select id="role" name="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Seleccione un rol</option>
                                <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Administrador</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                <option value="supervisor" {{ old('role') == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <div class="mb-4" id="colegio_assignment_div" style="{{ old('role') == 'admin' || old('role') == 'supervisor' ? 'display:block;' : 'display:none;' }}">
                            <x-input-label for="colegio_id" :value="__('Asignar Colegio')" />
                            <select id="colegio_id" name="colegio_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">No asignar</option>
                                @foreach($colegios as $colegio)
                                    <option value="{{ $colegio->id }}" {{ old('colegio_id') == $colegio->id ? 'selected' : '' }}>{{ $colegio->nombre }} ({{ $colegio->municipio->nombre ?? 'N/A' }})</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('colegio_id')" class="mt-2" />
                            <p class="text-sm text-gray-500 mt-1">Los roles 'Administrador' y 'Supervisor' deben tener un colegio asignado.</p>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('users.index') }}" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button>
                                {{ __('Registrar Usuario') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const colegioAssignmentDiv = document.getElementById('colegio_assignment_div');
            const colegioSelect = document.getElementById('colegio_id');

            function toggleColegioAssignment() {
                if (roleSelect.value === 'admin' || roleSelect.value === 'supervisor') {
                    colegioAssignmentDiv.style.display = 'block';
                    colegioSelect.setAttribute('required', 'required'); // Hacer el select de colegio obligatorio
                } else {
                    colegioAssignmentDiv.style.display = 'none';
                    colegioSelect.removeAttribute('required'); // No obligatorio
                    colegioSelect.value = ''; // Limpiar selección
                }
            }

            roleSelect.addEventListener('change', toggleColegioAssignment);

            // Ejecutar al cargar la página para reflejar old() values
            toggleColegioAssignment();
        });
    </script>
</x-app-layout>