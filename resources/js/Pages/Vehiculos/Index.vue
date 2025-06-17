<script setup>
// *** CAMBIADO de AuthenticatedLayout a AppLayout para consistencia con tu ejemplo de Dashboard ***
import AppLayout from '@/Layouts/AppLayout.vue'; 
import { Head, Link, useForm } from '@inertiajs/vue3';

// Necesitas definir la prop 'user' aquí, ya que el controlador la está pasando
defineProps({
    vehiculos: Object,
    user: Object, // <-- ¡¡Añade esta línea!!
});

const form = useForm({});

const destroy = (id) => {
    if (confirm('¿Estás seguro de eliminar este vehículo? Esta acción no se puede deshacer.')) {
        form.delete(route('vehiculos.destroy', id), {
            onSuccess: () => {
                // Puedes agregar lógica para refrescar la lista o mostrar un mensaje
            },
            onError: (errors) => {
                console.error("Error al eliminar el vehículo:", errors);
                alert('Hubo un error al eliminar el vehículo.');
            }
        });
    }
};
</script>

<template>
    <Head title="Vehículos" />

    <AppLayout :user="user">
        <template #header>
            <div class="flex items-center space-x-4">
                <p class="text-sm text-gray-500">holaasaa</p>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestión de Vehículos</h2>
            </div>
        </template>
        
        <p>{{ vehiculos.data.length === 0 ? 'Vehículos vacíos, mostrando el mensaje de vacío' : 'Vehículos con datos, mostrando la tabla' }}</p>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-2xl font-bold text-gray-800">Listado de Vehículos</h3>
                            <Link :href="route('vehiculos.create')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Crear Nuevo Vehículo
                            </Link>
                        </div>

                        <div v-if="$page.props.flash && $page.props.flash.success" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p class="font-bold">Éxito</p>
                            <p>{{ $page.props.flash.success }}</p>
                        </div>
                        <div v-if="$page.props.flash && $page.props.flash.error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p class="font-bold">Error</p>
                            <p>{{ $page.props.flash.error }}</p>
                        </div>

                        <div class="overflow-x-auto shadow-md sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marca</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modelo</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patente</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo (Precio)</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-if="vehiculos.data.length === 0">
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">No hay vehículos registrados.</td>
                                    </tr>
                                    <tr v-for="vehiculo in vehiculos.data" :key="vehiculo.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ vehiculo.id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ vehiculo.marca }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ vehiculo.modelo }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ vehiculo.patente }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ vehiculo.tipo_nombre }} (${{ vehiculo.tipo_precio }})</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ vehiculo.cliente_nombre }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <Link :href="route('vehiculos.show', vehiculo.id)" class="text-blue-600 hover:text-blue-900 mr-3">Ver</Link>
                                            <Link :href="route('vehiculos.edit', vehiculo.id)" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</Link>
                                            <button @click="destroy(vehiculo.id)" class="text-red-600 hover:text-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 rounded-md">Eliminar</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-if="vehiculos.links.length > 3" class="flex flex-wrap justify-center mt-6">
                            <template v-for="(link, key) in vehiculos.links" :key="key">
                                <div
                                    v-if="link.url === null"
                                    class="mr-1 mb-1 px-4 py-2 text-sm leading-4 text-gray-400 border rounded-lg cursor-not-allowed"
                                    v-html="link.label"
                                ></div>
                                <Link
                                    v-else
                                    class="mr-1 mb-1 px-4 py-2 text-sm leading-4 border rounded-lg hover:bg-indigo-500 hover:text-white focus:border-indigo-500 focus:text-indigo-500"
                                    :class="{ 'bg-indigo-600 text-white': link.active, 'text-gray-700 bg-white border-gray-300': !link.active }"
                                    :href="link.url"
                                    v-html="link.label"
                                ></Link>
                            </template>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>