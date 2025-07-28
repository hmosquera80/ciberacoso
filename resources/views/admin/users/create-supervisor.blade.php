<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nuevo Supervisor para ') . ($adminColegio->nombre ?? 'Tu Colegio') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Formulario de Creación de Supervisor</h3>

                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">¡Error!</strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if ($adminColegio)
                        <form method="POST" action="{{ route('my-users.store-supervisor') }}">
                            @csrf

                            <div class="mb-4">
                                <x-input-label for="colegio_display" :value="__('Colegio Asignado')" />
                                <x-text-input id="colegio_display" class="block mt-1 w-full" type="text" value="{{ $adminColegio->nombre }} ({{ $adminColegio->municipio->nombre ?? 'N/A' }})" disabled />
                                <input type="hidden" name="colegio_id" value="{{ $adminColegio->id }}"> {{-- Campo oculto para enviar el ID del colegio --}}
                                <p class="text-sm text-gray-500 mt-1">Este supervisor se asignará automáticamente a este colegio.</p>
                            </div>

                            <div class="mb-4">
                                <x-input-label for="name" :value="__('Nombre del Supervisor')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="email" :value="__('Email del Supervisor')" />
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

                            <div class="flex items-center justify-end mt-4">
                                <a href="{{ route('dashboard') }}" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4">
                                    {{ __('Cancelar') }}
                                </a>
                                <x-primary-button>
                                    {{ __('Registrar Supervisor') }}
                                </x-primary-button>
                            </div>
                        </form>
                    @else
                        <p class="text-red-600">No puedes crear un supervisor porque tu cuenta de administrador no está asociada a un colegio.</p>
                        <p class="text-gray-600 mt-2">Por favor, contacta a un Super Administrador para que asocie tu cuenta a un colegio.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>