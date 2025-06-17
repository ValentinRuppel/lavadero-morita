<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, Head } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
    vehiculo: Object,
});
</script>

<template>
    <AppLayout title="Detalles del Vehículo">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detalles del Vehículo
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Patente:</label>
                        <p class="text-gray-900">{{ vehiculo.patente }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Marca:</label>
                        <p class="text-gray-900">{{ vehiculo.marca_nombre }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Modelo:</label>
                        <p class="text-gray-900">{{ vehiculo.modelo_nombre }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Año:</label>
                        <p class="text-gray-900">{{ vehiculo.anio }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Tipo de Vehículo:</label>
                        <p class="text-gray-900">{{ vehiculo.tipo_nombre }} (${{ vehiculo.tipo_precio }})</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Cliente:</label>
                        <p class="text-gray-900">{{ vehiculo.cliente_nombre }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Registrado el:</label>
                        <p class="text-gray-900">{{ vehiculo.created_at }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Última Actualización:</label>
                        <p class="text-gray-900">{{ vehiculo.updated_at }}</p>
                    </div>

                    <div class="flex justify-end mt-6">
                        <Link :href="route('vehiculos.edit', vehiculo.id)" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded mr-2">
                            Editar
                        </Link>
                        <Link :href="route('vehiculos.index')" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Volver al Listado
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>