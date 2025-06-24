<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    boxes: Array,
    user: Object,
    successMessage: {
        type: String,
        default: null
    },
    errorMessage: {
        type: String,
        default: null
    },
});

// Generar una clave única basada en los boxes para forzar reactividad
const boxesKey = computed(() => props.boxes.map(box => box.id + box.estado).join('-'));

const deleteBox = (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar este box?')) {
        router.post(`/boxes/${id}`, {
            _method: 'delete',
        }, {
            onSuccess: () => {
                router.get(route('boxes.index'), {}, {
                    preserveState: false,
                    preserveScroll: false,
                    replace: true,
                });
            },
            onError: (errors) => {
                console.error('Error al eliminar box:', errors);
            }
        });
    }
};

const viewBox = (id) => {
    router.visit(route('boxes.show', id));
};
</script>

<template>
    <Head title="Gestión de Boxes" />

    <AppLayout :user="user">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Tarjeta principal con glassmorphism -->
                <div class="bg-white/15 backdrop-blur-md rounded-2xl shadow-2xl border border-white/20 p-8 relative overflow-hidden">
                    <!-- Partículas internas flotantes -->
                    <div class="absolute top-4 right-4 w-2 h-2 bg-pink-300/40 rounded-full animate-pulse"></div>
                    <div class="absolute top-8 left-8 w-1 h-1 bg-violet-400/50 rounded-full animate-bounce" style="animation-delay: 0.5s"></div>
                    <div class="absolute bottom-6 right-12 w-3 h-3 bg-purple-200/30 rounded-full animate-pulse" style="animation-delay: 1s"></div>
                    <div class="absolute bottom-12 left-6 w-2 h-2 bg-fuchsia-300/35 rounded-full animate-bounce" style="animation-delay: 1.5s"></div>

                    <!-- Título con decoración -->
                    <div class="text-center mb-8 relative">
                        <h2 class="text-3xl font-bold bg-gradient-to-r from-purple-200 to-pink-200 bg-clip-text text-transparent mb-4 pt-6">
                            Gestión de Boxes
                        </h2>
                        <!-- Línea decorativa con gradiente -->
                        <div class="flex justify-center items-center space-x-2 mb-4">
                            <div class="w-8 h-0.5 bg-gradient-to-r from-transparent to-purple-400/50"></div>
                            <div class="w-3 h-3 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full animate-pulse"></div>
                            <div class="w-12 h-0.5 bg-gradient-to-r from-purple-400/50 to-pink-400/50"></div>
                            <div class="w-3 h-3 bg-gradient-to-r from-pink-400 to-violet-400 rounded-full animate-pulse" style="animation-delay: 0.5s"></div>
                            <div class="w-8 h-0.5 bg-gradient-to-r from-pink-400/50 to-transparent"></div>
                        </div>
                    </div>

                    <!-- Mensajes de éxito/error -->
                    <div v-if="successMessage" class="bg-green-500/20 border border-green-400/30 text-green-100 p-4 rounded-lg backdrop-blur-sm mb-6">
                        <p class="font-bold">Éxito</p>
                        <p>{{ successMessage }}</p>
                    </div>
                    <div v-if="errorMessage" class="bg-red-500/20 border border-red-400/30 text-red-100 p-4 rounded-lg backdrop-blur-sm mb-6">
                        <p class="font-bold">Error</p>
                        <p>{{ errorMessage }}</p>
                    </div>

                    <!-- Botón de crear -->
                    <div class="flex justify-end mb-6">
                        <Link
                            :href="route('boxes.create')"
                            class="group relative bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-bold py-2 px-6 rounded-full transition-all duration-300 transform hover:scale-105 border border-white/20 backdrop-blur-sm"
                        >
                            <span class="relative z-10 flex items-center gap-2">
                                ➕ Crear Nuevo Box
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                        </Link>
                    </div>

                    <!-- Grid de boxes -->
                    <div v-if="boxes.length === 0" class="text-center py-16">
                        <div class="text-purple-200">
                            <div class="text-4xl mb-4 animate-bounce">🚫</div>
                            <h3 class="text-xl font-bold text-purple-100 mb-2">No hay boxes registrados</h3>
                            <p class="text-purple-300">¡Comienza creando tu primer box!</p>
                        </div>
                    </div>
                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" :key="boxesKey">
                        <div
                            v-for="box in boxes"
                            :key="box.id"
                            @click="viewBox(box.id)"
                            class="bg-white/10 backdrop-blur-lg rounded-lg shadow-xl border border-purple-300/30 p-6 transition-all duration-300 group relative overflow-hidden hover:bg-white/15 cursor-pointer"
                        >
                            <!-- Burbuja decorativa interna -->
                            <div class="absolute top-2 right-2 w-2 h-2 bg-pink-300/40 rounded-full animate-pulse"></div>
                            <h4 class="text-xl font-bold text-purple-100 mb-2">{{ box.nombre_box }}</h4>
                            <p class="text-purple-200 mb-3 text-sm">{{ box.descripcion }}</p>
                            <div :class="{
                                'px-3 py-1 rounded-full text-sm font-semibold inline-block border': true,
                                'bg-green-500/20 text-green-100 border-green-400/30': box.estado === 'activo',
                                'bg-red-500/20 text-red-100 border-red-400/30': box.estado === 'ocupado',
                                'bg-yellow-500/20 text-yellow-100 border-yellow-400/30': box.estado === 'mantenimiento',
                            }">
                                Estado: {{ box.estado.charAt(0).toUpperCase() + box.estado.slice(1) }}
                            </div>
                            <div v-if="box.servicio_en_curso" class="mt-4 p-3 bg-indigo-500/10 rounded-md border border-indigo-400/20 text-sm">
                                <p class="text-indigo-100 font-semibold">Servicio en Curso:</p>
                                <p class="text-indigo-200">Vehículo: {{ box.servicio_en_curso.vehiculo?.marca }} {{ box.servicio_en_curso.vehiculo?.modelo }} ({{ box.servicio_en_curso.vehiculo?.patente }})</p>
                                <p class="text-indigo-200">Tipo de Lavado: {{ box.servicio_en_curso.tipo_lavado?.nombre_lavado }}</p>
                                <p class="text-indigo-200">Inició: {{ new Date(box.servicio_en_curso.fecha_hora_inicio).toLocaleString() }}</p>
                                <p class="text-indigo-200">Asignado por: {{ box.servicio_en_curso.administrador?.name }}</p>
                            </div>
                            <div v-else class="mt-4 p-3 bg-purple-500/10 rounded-md border border-purple-400/20 text-purple-200 text-sm">
                                No hay servicio en curso.
                            </div>
                            <div class="mt-4 flex space-x-2">
                                <Link
                                    :href="route('boxes.edit', box.id)"
                                    :class="{
                                        'group relative bg-yellow-500/20 hover:bg-yellow-500/30 text-yellow-200 hover:text-yellow-100 px-3 py-1 rounded-full transition-all duration-300 transform hover:scale-105 border border-yellow-400/20 backdrop-blur-sm': box.estado !== 'ocupado',
                                        'text-gray-400 cursor-not-allowed px-3 py-1': box.estado === 'ocupado'
                                    }"
                                    :disabled="box.estado === 'ocupado'"
                                    @click.stop
                                >
                                    <span v-if="box.estado !== 'ocupado'" class="relative z-10 flex items-center gap-1">✏️ Editar</span odel>
                                    <span v-else>✏️ Editar</span>
                                    <div v-if="box.estado !== 'ocupado'" class="absolute inset-0 bg-gradient-to-r from-yellow-400/20 to-orange-400/20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                </Link>
                                <button
                                    @click.stop="deleteBox(box.id)"
                                    :class="{
                                        'group relative bg-red-500/20 hover:bg-red-500/30 text-red-200 hover:text-red-100 px-3 py-1 rounded-full transition-all duration-300 transform hover:scale-105 border border-red-400/20 backdrop-blur-sm': box.estado !== 'ocupado',
                                        'text-gray-400 cursor-not-allowed px-3 py-1': box.estado === 'ocupado'
                                    }"
                                    :disabled="box.estado === 'ocupado'"
                                >
                                    <span v-if="box.estado !== 'ocupado'" class="relative z-10 flex items-center gap-1">🗑️ Eliminar</span>
                                    <span v-else>🗑️ Eliminar</span>
                                    <div v-if="box.estado !== 'ocupado'" class="absolute inset-0 bg-gradient-to-r from-red-400/20 to-pink-400/20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Botón de volver -->
                    <div class="flex justify-start mt-6">
                        <Link
                            :href="route('dashboard')"
                            class="group relative bg-purple-500/20 hover:bg-purple-500/30 text-purple-100 px-4 py-2 rounded-full transition-all duration-300 transform hover:scale-105 border border-purple-400/20 backdrop-blur-sm"
                        >
                            <span class="relative z-10 flex items-center gap-2">
                                ⬅ Volver al Inicio
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-purple-400/20 to-pink-400/20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </Link>
                    </div>

                    <!-- Decoración inferior con puntos animados -->
                    <div class="flex justify-center items-center space-x-3 opacity-60 mt-8">
                        <div class="w-2 h-2 bg-purple-400/60 rounded-full animate-pulse"></div>
                        <div class="w-1 h-1 bg-pink-400/50 rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
                        <div class="w-3 h-3 bg-violet-400/40 rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
                        <div class="w-1 h-1 bg-fuchsia-400/60 rounded-full animate-pulse" style="animation-delay: 0.6s"></div>
                        <div class="w-2 h-2 bg-purple-300/50 rounded-full animate-pulse" style="animation-delay: 0.8s"></div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

@keyframes shimmer {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
}

/* Scroll suave */
html {
    scroll-behavior: smooth;
}
</style>
