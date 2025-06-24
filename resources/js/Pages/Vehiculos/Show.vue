<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
    vehiculo: Object,
});
</script>

<template>
    <AppLayout title="Detalles del Vehículo" :user="user">
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-0">
                <!-- Tarjeta principal con glassmorphism -->
                <div class="bg-white/15 backdrop-blur-md rounded-2xl shadow-2xl border border-white/20 p-8 relative overflow-hidden">
                    <!-- Partículas internas flotantes -->
                    <div class="absolute top-4 right-4 w-2 h-2 bg-pink-500/40 rounded-full animate-pulse"></div>
                    <div class="absolute top-8 left-8 w-1 h-1 bg-violet-500/50 rounded-full animate-bounce" style="animation-delay: 0.5s"></div>
                    <div class="absolute bottom-6 right-12 w-3 h-3 bg-purple-400/30 rounded-full animate-pulse" style="animation-delay: 1s"></div>
                    <div class="absolute bottom-12 left-6 w-2 h-2 bg-fuchsia-400/35 rounded-full animate-bounce" style="animation-delay: 1.5s"></div>

                    <div class="text-center mb-8 relative">
                        <!-- Elemento decorativo superior flotante -->
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-400/30 to-pink-400/30 rounded-full animate-float backdrop-blur-sm border border-white/20 flex items-center justify-center">
                                <span class="text-lg">🚗</span>
                            </div>
                        </div>

                        <h2 class="text-3xl font-bold bg-gradient-to-r from-purple-200 to-pink-200 bg-clip-text text-transparent mb-4 pt-6">
                            Detalles del Vehículo
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

                    <div class="space-y-6">
                        <!-- Patente -->
                        <div class="group relative">
                            <label class="block text-purple-100 text-sm font-bold mb-2">Patente:</label>
                            <p class="text-purple-100 bg-white/10 border border-purple-300/30 rounded-lg py-2 px-3 backdrop-blur-sm transition-all duration-300 group-hover:scale-101">{{ vehiculo.patente }}</p>
                        </div>

                        <!-- Marca -->
                        <div class="group relative">
                            <label class="block text-purple-100 text-sm font-bold mb-2">Marca:</label>
                            <p class="text-purple-100 bg-white/10 border border-purple-300/30 rounded-lg py-2 px-3 backdrop-blur-sm transition-all duration-300 group-hover:scale-101">{{ vehiculo.marca_nombre }}</p>
                        </div>

                        <!-- Modelo -->
                        <div class="group relative">
                            <label class="block text-purple-100 text-sm font-bold mb-2">Modelo:</label>
                            <p class="text-purple-100 bg-white/10 border border-purple-300/30 rounded-lg py-2 px-3 backdrop-blur-sm transition-all duration-300 group-hover:scale-101">{{ vehiculo.modelo_nombre }}</p>
                        </div>

                        <!-- Año -->
                        <div class="group relative">
                            <label class="block text-purple-100 text-sm font-bold mb-2">Año:</label>
                            <p class="text-purple-100 bg-white/10 border border-purple-300/30 rounded-lg py-2 px-3 backdrop-blur-sm transition-all duration-300 group-hover:scale-101">{{ vehiculo.anio }}</p>
                        </div>

                        <!-- Tipo de Vehículo -->
                        <div class="group relative">
                            <label class="block text-purple-100 text-sm font-bold mb-2">Tipo de Vehículo:</label>
                            <p class="text-purple-100 bg-white/10 border border-purple-300/30 rounded-lg py-2 px-3 backdrop-blur-sm transition-all duration-300 group-hover:scale-101">{{ vehiculo.tipo_nombre }} (${{ vehiculo.tipo_precio }})</p>
                        </div>

                        <!-- Registrado el -->
                        <div class="group relative">
                            <label class="block text-purple-100 text-sm font-bold mb-2">Registrado el:</label>
                            <p class="text-purple-100 bg-white/10 border border-purple-300/30 rounded-lg py-2 px-3 backdrop-blur-sm transition-all duration-300 group-hover:scale-101">{{ vehiculo.created_at }}</p>
                        </div>

                        <!-- Última Actualización -->
                        <div class="group relative">
                            <label class="block text-purple-100 text-sm font-bold mb-2">Última Actualización:</label>
                            <p class="text-purple-100 bg-white/10 border border-purple-300/30 rounded-lg py-2 px-3 backdrop-blur-sm transition-all duration-300 group-hover:scale-101">{{ vehiculo.updated_at }}</p>
                        </div>

                        <!-- Botones -->
                        <div class="flex justify-end mt-6 space-x-4">
                            <Link
                                :href="route('vehiculos.index')"
                                class="group relative bg-purple-500/20 hover:bg-purple-500/30 text-purple-100 px-4 py-2 rounded-full transition-all duration-300 transform hover:scale-105 border border-purple-400/20 backdrop-blur-sm"
                            >
                                <span class="relative z-10 flex items-center gap-2">
                                    ⬅ Volver al Listado
                                </span>
                                <div class="absolute inset-0 bg-gradient-to-r from-purple-400/20 to-pink-400/20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </Link>
                        </div>
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
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

/* Efectos para los elementos */
.group:hover p {
    transform: scale(1.01);
    transition: transform 0.3s ease-in-out;
}

/* Scroll suave */
html {
    scroll-behavior: smooth;
}
</style>
