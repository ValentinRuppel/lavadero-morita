<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'; // Asumo que usas AppLayout en lugar de AuthenticatedLayout
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3'; // Importa useForm
import { computed } from 'vue';

// Define las props que el controlador nos pasará
const props = defineProps({
    boxes: Array, // Asegúrate de que esto sea Array, no Object, si estás pasando una colección
    user: Object, // Prop para el layout si lo necesita
    successMessage: { // Define la prop successMessage
        type: String,
        default: null
    },
    errorMessage: {   // Define la prop errorMessage
        type: String,
        default: null
    },
});

const deleteBox = (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar este box?')) {
    router.post(`/boxes/${id}`, {
          _method: 'delete', // spoofing del método
      }, {
          onSuccess: () => {
              router.visit(route('boxes.index'), {
                  method: 'get',
                  preserveState: true,
                  preserveScroll: true,
              });
          },
          onError: (errors) => {
              console.error('Error al eliminar box:', errors);
          }
      });
    }
};
</script>

<template>
    <Head title="Gestión de Boxes" />

    <AppLayout :user="user"> 
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestión de Boxes</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="successMessage" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p class="font-bold">Éxito</p>
                    <p>{{ successMessage }}</p>
                </div>
                <div v-if="errorMessage" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p class="font-bold">Error</p>
                    <p>{{ errorMessage }}</p>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Listado de Boxes</h3>
                            <Link :href="route('boxes.create')" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Crear Nuevo Box
                            </Link>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="box in props.boxes" :key="box.id" class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-200">
                                <h4 class="text-xl font-bold mb-2">{{ box.nombre_box }}</h4>
                                <p class="text-gray-600 mb-3">{{ box.descripcion }}</p>
                                
                                <div :class="{
                                    'px-3 py-1 rounded-full text-sm font-semibold inline-block': true,
                                    'bg-green-100 text-green-800': box.estado === 'activo', // Cambiado a 'activo'
                                    'bg-red-100 text-red-800': box.estado === 'ocupado',
                                    'bg-yellow-100 text-yellow-800': box.estado === 'mantenimiento',
                                    // Eliminamos 'limpieza' ya que lo quitamos de la migración
                                }">
                                    Estado: {{ box.estado.charAt(0).toUpperCase() + box.estado.slice(1) }}
                                </div>

                                <div v-if="box.servicio_en_curso" class="mt-4 p-3 bg-indigo-50 rounded-md border border-indigo-200">
                                    <p class="text-indigo-700 font-semibold">Servicio en Curso:</p>
                                    <p class="text-sm text-indigo-600">Vehículo: {{ box.servicio_en_curso.vehiculo?.marca }} {{ box.servicio_en_curso.vehiculo?.modelo }} ({{ box.servicio_en_curso.vehiculo?.patente }})</p>
                                    <p class="text-sm text-indigo-600">Tipo de Lavado: {{ box.servicio_en_curso.tipo_lavado?.nombre_lavado }}</p>
                                    <p class="text-sm text-indigo-600">Inició: {{ new Date(box.servicio_en_curso.fecha_hora_inicio).toLocaleString() }}</p>
                                    <p class="text-sm text-indigo-600">Asignado por: {{ box.servicio_en_curso.administrador?.name }}</p>
                                </div>
                                <div v-else class="mt-4 p-3 bg-gray-100 rounded-md border border-gray-200 text-gray-500 text-sm">
                                    No hay servicio en curso.
                                </div>

                                <div class="mt-4 flex space-x-2">
                                    <Link 
                                        :href="route('boxes.edit', box.id)" 
                                        :class="{'text-blue-600 hover:text-blue-900': box.estado !== 'ocupado', 'text-gray-400 cursor-not-allowed': box.estado === 'ocupado'}"
                                        :disabled="box.estado === 'ocupado'"
                                    >
                                        Editar
                                    </Link>
                                    
                                    <Link :href="route('boxes.show', box.id)" class="text-indigo-600 hover:text-indigo-900">
                                        Ver Detalles
                                    </Link>

                                    <button 
                                        @click="deleteBox(box.id)" 
                                        :class="{'text-red-600 hover:text-red-900': box.estado !== 'ocupado', 'text-gray-400 cursor-not-allowed': box.estado === 'ocupado'}"
                                        :disabled="box.estado === 'ocupado'"
                                    >
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>