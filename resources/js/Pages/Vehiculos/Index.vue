<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, Head, router } from '@inertiajs/vue3'; // Asegúrate de importar router
import { ref } from 'vue'; // Importar ref si es necesario para alguna interactividad local

const props = defineProps({
    user: Object,
    vehiculos: Object, // Paginator instance
});

// Función para eliminar un vehículo
const deleteVehiculo = (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar este vehículo?')) {
        router.post(`/vehiculos/${id}`, {
            _method: 'delete', // spoofing del método
        }, {
            onSuccess: () => {
                router.visit(route('vehiculos.index'), {
                    method: 'get',
                    preserveState: true,
                    preserveScroll: true,
                });
            },
            onError: (errors) => {
                console.error('Error al eliminar vehículo:', errors);
            }
        });
    }
};




</script>

<template>
    <AppLayout title="Listado de Vehículos" :user="user">
        <template #head>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Listado de Vehículos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div v-if="$page.props.flash && $page.props.flash.success" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                        <p class="font-bold">Éxito</p>
                        <p>{{ $page.props.flash.success }}</p>
                    </div>
                    <div v-if="$page.props.flash && $page.props.flash.error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                        <p class="font-bold">Error</p>
                        <p>{{ $page.props.flash.error }}</p>
                    </div>

                    <div class="flex justify-end mb-4">
                        <Link :href="route('vehiculos.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Registrar Nuevo Vehículo </Link>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marca</th> <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modelo</th> <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Año</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="vehiculos.data.length === 0">
                                    <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">No hay vehículos registrados.</td>
                                </tr>
                                <tr v-for="vehiculo in vehiculos.data" :key="vehiculo.id">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ vehiculo.patente }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ vehiculo.marca_nombre }}</td> <td class="px-6 py-4 whitespace-nowrap">{{ vehiculo.modelo_nombre }}</td> <td class="px-6 py-4 whitespace-nowrap">{{ vehiculo.anio }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ vehiculo.tipo_nombre }} (${{ vehiculo.tipo_precio }})</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link :href="route('vehiculos.show', vehiculo.id)" class="text-indigo-600 hover:text-indigo-900 mr-3">Ver</Link>
                                        <Link :href="route('vehiculos.edit', vehiculo.id)" class="text-yellow-600 hover:text-yellow-900 mr-3">Editar</Link>
                                        <button @click="deleteVehiculo(vehiculo.id)" class="text-red-600 hover:text-red-900">Eliminar</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <nav class="flex justify-center" role="navigation" aria-label="Pagination">
                            <template v-for="(link, key) in vehiculos.links">
                                <div v-if="link.url === null" :key="key" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded" v-html="link.label" />
                                <Link
                                    v-else
                                    :key="`link-${key}`"
                                    class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500"
                                    :class="{ 'bg-blue-500 text-white': link.active }"
                                    :href="link.url"
                                    v-html="link.label"
                                />
                            </template>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>