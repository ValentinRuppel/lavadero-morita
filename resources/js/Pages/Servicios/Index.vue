<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    servicios: Object, // Inertia paginator object (data, links, current_page, etc.)
    isAdmin: Boolean,
    user: Object,
});
</script>

<template>
    <AppLayout :title="props.isAdmin ? 'Historial de Servicios (Admin)' : 'Mis Servicios de Lavado'" :user="user">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ props.isAdmin ? 'Historial de Servicios de Lavado' : 'Mis Servicios de Lavado' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                    <div v-if="props.servicios.data.length > 0">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Fecha Inicio
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Fecha Fin
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Vehículo
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tipo Lavado
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Precio Total
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estado
                                    </th>

                                    <th v-if="props.isAdmin" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cliente
                                    </th>
                                    <th v-if="props.isAdmin" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Admin Asignado
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="servicio in props.servicios.data" :key="servicio.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ servicio.fecha_inicio }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ servicio.fecha_fin }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ servicio.vehiculo.patente }} ({{ servicio.vehiculo.marca }} {{ servicio.vehiculo.modelo }})
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ servicio.tipo_lavado.nombre }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                        ${{ servicio.precio_total }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ servicio.estado }}
                                    </td>

                                    <td v-if="props.isAdmin" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ servicio.cliente?.name ?? 'Cliente Eliminado' }}
                                    </td>
                                    <td v-if="props.isAdmin" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ servicio.administrador_nombre ?? 'Admin Eliminado' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="mt-4 flex justify-between items-center">
                            <Link v-if="props.servicios.prev_page_url" :href="props.servicios.prev_page_url" class="px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-100 rounded-md hover:bg-indigo-200">
                                Anterior
                            </Link>
                            <span class="text-sm text-gray-700">Página {{ props.servicios.current_page }} de {{ props.servicios.last_page }}</span>
                            <Link v-if="props.servicios.next_page_url" :href="props.servicios.next_page_url" class="px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-100 rounded-md hover:bg-indigo-200">
                                Siguiente
                            </Link>
                        </div>

                    </div>
                    <div v-else class="text-center text-gray-500 py-8">
                        <p v-if="props.isAdmin">No hay servicios de lavado finalizados en el historial.</p>
                        <p v-else>Aún no tienes servicios de lavado finalizados.</p>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>