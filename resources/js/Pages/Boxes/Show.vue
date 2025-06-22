<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    box: Object,
});
</script>

<template>
    <Head :title="'Detalle del Box: ' + props.box.nombre_box" />

    <AppLayout :user="user">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detalle del Box: {{ props.box.nombre_box }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-2xl font-bold mb-4">{{ props.box.nombre_box }}</h3>
                        <p class="text-gray-700 mb-2">Descripción: {{ props.box.descripcion || 'Sin descripción' }}</p>
                        <div class="mb-4">
                            <span :class="{
                                'px-3 py-1 rounded-full text-sm font-semibold inline-block': true,
                                'bg-green-100 text-green-800': props.box.estado === 'disponible',
                                'bg-red-100 text-red-800': props.box.estado === 'ocupado',
                                'bg-yellow-100 text-yellow-800': props.box.estado === 'mantenimiento',
                                'bg-blue-100 text-blue-800': props.box.estado === 'limpieza',
                            }">
                                Estado: {{ props.box.estado.charAt(0).toUpperCase() + props.box.estado.slice(1) }}
                            </span>
                        </div>

                        <div v-if="props.box.servicio_en_curso" class="mt-6 p-4 bg-indigo-100 rounded-lg border border-indigo-300">
                            <h4 class="text-lg font-semibold text-indigo-800 mb-2">Servicio en Curso:</h4>
                            <p class="text-indigo-700">Vehículo: {{ props.box.servicio_en_curso.vehiculo.marca }} {{ props.box.servicio_en_curso.vehiculo.modelo }} ({{ props.box.servicio_en_curso.vehiculo.patente }})</p>
                            <p class="text-indigo-700">Tipo de Lavado: {{ props.box.servicio_en_curso.tipo_lavado.nombre_lavado }}</p>
                            <p class="text-indigo-700">Precio del Lavado: ${{ props.box.servicio_en_curso.tipo_lavado.precio }}</p>
                            <p class="text-indigo-700">Inició: {{ new Date(props.box.servicio_en_curso.fecha_hora_inicio).toLocaleString() }}</p>
                            <p class="text-indigo-700">Asignado por: {{ props.box.servicio_en_curso.administrador.name }}</p>
                            </div>
                        <div v-else class="mt-6 p-4 bg-gray-100 rounded-lg border border-gray-300 text-gray-600">
                            No hay ningún servicio en curso en este box.
                        </div>

                        <div class="mt-6">
                            <Link :href="route('boxes.index')" class="text-blue-600 hover:text-blue-900 mr-4">Volver a la lista</Link>
                            <Link :href="route('boxes.edit', props.box.id)" class="text-indigo-600 hover:text-indigo-900">Editar Box</Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>