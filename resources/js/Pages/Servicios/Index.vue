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
                <!-- Contenedor principal con glassmorphism y efectos -->
                <div class="bg-white/15 backdrop-blur-md rounded-2xl shadow-2xl border border-white/20 p-8 relative overflow-hidden">

                    <!-- Partículas decorativas internas -->
                    <div class="absolute top-4 right-4 w-2 h-2 bg-pink-300/40 rounded-full animate-pulse"></div>
                    <div class="absolute top-8 left-8 w-1 h-1 bg-violet-400/50 rounded-full animate-bounce" style="animation-delay: 0.5s"></div>
                    <div class="absolute bottom-6 right-12 w-3 h-3 bg-purple-200/30 rounded-full animate-pulse" style="animation-delay: 1s"></div>
                    <div class="absolute bottom-12 left-6 w-2 h-2 bg-fuchsia-300/35 rounded-full animate-bounce" style="animation-delay: 1.5s"></div>
                    <div class="absolute top-16 right-24 w-1 h-1 bg-violet-300/45 rounded-full animate-pulse" style="animation-delay: 0.3s"></div>
                    <div class="absolute bottom-20 right-32 w-2 h-2 bg-pink-400/30 rounded-full animate-bounce" style="animation-delay: 0.8s"></div>

                    <!-- Título con gradiente y efectos -->
                    <div class="text-center mb-8 relative">
                        <h2 class="text-3xl font-bold bg-gradient-to-r from-purple-200 to-pink-200 bg-clip-text text-transparent mb-4 pt-6">
                            {{ props.isAdmin ? 'Historial de Servicios de Lavado' : 'Mis Servicios de Lavado' }}
                        </h2>

                        <!-- Línea decorativa con gradiente -->
                        <div class="flex justify-center items-center space-x-2 mb-6">
                            <div class="w-8 h-0.5 bg-gradient-to-r from-transparent to-purple-400/50"></div>
                            <div class="w-3 h-3 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full animate-pulse"></div>
                            <div class="w-12 h-0.5 bg-gradient-to-r from-purple-400/50 to-pink-400/50"></div>
                            <div class="w-3 h-3 bg-gradient-to-r from-pink-400 to-violet-400 rounded-full animate-pulse" style="animation-delay: 0.5s"></div>
                            <div class="w-8 h-0.5 bg-gradient-to-r from-pink-400/50 to-transparent"></div>
                        </div>
                    </div>

                    <!-- Contenido condicional -->
                    <div v-if="props.servicios.data.length > 0" class="relative">
                        <!-- Contenedor de la tabla con glassmorphism -->
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl border border-white/20 overflow-hidden shadow-xl">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-purple-300/20">
                                    <thead class="bg-gradient-to-r from-purple-600/20 to-violet-600/20 backdrop-blur-sm">
                                        <tr>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-purple-100 uppercase tracking-wider border-r border-purple-300/20 last:border-r-0">
                                                <div class="flex items-center gap-2">
                                                    Fecha Inicio
                                                </div>
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-purple-100 uppercase tracking-wider border-r border-purple-300/20 last:border-r-0">
                                                <div class="flex items-center gap-2">
                                                    Fecha Fin
                                                </div>
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-purple-100 uppercase tracking-wider border-r border-purple-300/20 last:border-r-0">
                                                <div class="flex items-center gap-2">
                                                    Vehículo
                                                </div>
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-purple-100 uppercase tracking-wider border-r border-purple-300/20 last:border-r-0">
                                                <div class="flex items-center gap-2">
                                                    Tipo Lavado
                                                </div>
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-purple-100 uppercase tracking-wider border-r border-purple-300/20 last:border-r-0">
                                                <div class="flex items-center gap-2">
                                                    Precio Total
                                                </div>
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-purple-100 uppercase tracking-wider border-r border-purple-300/20 last:border-r-0">
                                                <div class="flex items-center gap-2">
                                                    Estado
                                                </div>
                                            </th>

                                            <th v-if="props.isAdmin" scope="col" class="px-6 py-4 text-left text-xs font-bold text-purple-100 uppercase tracking-wider border-r border-purple-300/20 last:border-r-0">
                                                <div class="flex items-center gap-2">
                                                    Cliente
                                                </div>
                                            </th>
                                            <th v-if="props.isAdmin" scope="col" class="px-6 py-4 text-left text-xs font-bold text-purple-100 uppercase tracking-wider border-r border-purple-300/20 last:border-r-0">
                                                <div class="flex items-center gap-2">
                                                    Admin Asignado
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-purple-300/10">
                                        <tr v-for="(servicio, index) in props.servicios.data" :key="servicio.id"
                                            class="group hover:bg-white/5 transition-all duration-300 relative"
                                            :class="index % 2 === 0 ? 'bg-white/5' : 'bg-purple-500/5'">

                                            <!-- Efecto de brillo en hover -->
                                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>

                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-purple-100 font-medium border-r border-purple-300/10 last:border-r-0 relative">
                                                <div class="bg-purple-500/20 backdrop-blur-sm rounded-lg px-3 py-2 border border-purple-400/20">
                                                    {{ servicio.fecha_inicio }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-purple-100 font-medium border-r border-purple-300/10 last:border-r-0 relative">
                                                <div class="bg-pink-500/20 backdrop-blur-sm rounded-lg px-3 py-2 border border-pink-400/20">
                                                    {{ servicio.fecha_fin }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-purple-100 font-medium border-r border-purple-300/10 last:border-r-0 relative">
                                                <div class="bg-violet-500/20 backdrop-blur-sm rounded-lg px-3 py-2 border border-violet-400/20">
                                                    <div class="font-bold text-violet-200">{{ servicio.vehiculo.patente }}</div>
                                                    <div class="text-xs text-violet-300">{{ servicio.vehiculo.marca }} {{ servicio.vehiculo.modelo }}</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-purple-100 font-medium border-r border-purple-300/10 last:border-r-0 relative">
                                                <div class="bg-fuchsia-500/20 backdrop-blur-sm rounded-lg px-3 py-2 border border-fuchsia-400/20">
                                                    {{ servicio.tipo_lavado.nombre }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm border-r border-purple-300/10 last:border-r-0 relative">
                                                <div class="bg-green-500/20 backdrop-blur-sm rounded-lg px-3 py-2 border border-green-400/20">
                                                    <span class="font-bold text-green-200 text-lg">${{ servicio.precio_total }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm border-r border-purple-300/10 last:border-r-0 relative">
                                                <div class="bg-yellow-500/20 backdrop-blur-sm rounded-lg px-3 py-2 border border-yellow-400/20">
                                                    <span class="font-semibold text-yellow-200">{{ servicio.estado }}</span>
                                                </div>
                                            </td>

                                            <td v-if="props.isAdmin" class="px-6 py-4 whitespace-nowrap text-sm text-purple-100 font-medium border-r border-purple-300/10 last:border-r-0 relative">
                                                <div class="bg-blue-500/20 backdrop-blur-sm rounded-lg px-3 py-2 border border-blue-400/20">
                                                    {{ servicio.cliente?.name ?? 'Cliente Eliminado' }}
                                                </div>
                                            </td>
                                            <td v-if="props.isAdmin" class="px-6 py-4 whitespace-nowrap text-sm text-purple-100 font-medium border-r border-purple-300/10 last:border-r-0 relative">
                                                <div class="bg-orange-500/20 backdrop-blur-sm rounded-lg px-3 py-2 border border-orange-400/20">
                                                    {{ servicio.administrador_nombre ?? 'Admin Eliminado' }}
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Paginación con glassmorphism -->
                        <div class="mt-8 flex justify-between items-center">
                            <Link v-if="props.servicios.prev_page_url" :href="props.servicios.prev_page_url"
                                  class="group relative bg-gradient-to-r from-indigo-500/80 to-purple-600/80 hover:from-indigo-600/90 hover:to-purple-700/90 text-white rounded-xl shadow-xl px-6 py-3 transform hover:scale-105 transition-all duration-300 ease-in-out backdrop-blur-md border border-white/20 overflow-hidden">

                                <!-- Efecto de brillo en movimiento -->
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>

                                <div class="relative z-10 flex items-center gap-2">
                                    <span>⬅️</span>
                                    <span class="font-medium">Anterior</span>
                                </div>
                            </Link>

                            <!-- Indicador de página con glassmorphism -->
                            <div class="bg-white/10 backdrop-blur-sm rounded-full px-6 py-3 border border-purple-300/20">
                                <span class="text-sm font-semibold bg-gradient-to-r from-purple-200 to-pink-200 bg-clip-text text-transparent">
                                    📄 Página {{ props.servicios.current_page }} de {{ props.servicios.last_page }}
                                </span>
                            </div>

                            <Link v-if="props.servicios.next_page_url" :href="props.servicios.next_page_url"
                                  class="group relative bg-gradient-to-r from-purple-600/80 to-pink-600/80 hover:from-purple-700/90 hover:to-pink-700/90 text-white rounded-xl shadow-xl px-6 py-3 transform hover:scale-105 transition-all duration-300 ease-in-out backdrop-blur-md border border-white/20 overflow-hidden">

                                <!-- Efecto de brillo en movimiento -->
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>

                                <div class="relative z-10 flex items-center gap-2">
                                    <span class="font-medium">Siguiente</span>
                                    <span>➡️</span>
                                </div>
                            </Link>
                        </div>

                    </div>

                    <!-- Estado vacío con glassmorphism -->
                    <div v-else class="text-center py-16 relative">
                        <!-- Elemento decorativo central -->
                        <div class="flex justify-center mb-6">
                            <div class="w-20 h-20 bg-gradient-to-r from-purple-400/30 to-pink-400/30 rounded-full animate-float backdrop-blur-sm border border-white/20 flex items-center justify-center">
                                <span class="text-3xl">📋</span>
                            </div>
                        </div>

                        <!-- Mensaje con glassmorphism -->
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-8 border border-white/20 max-w-md mx-auto">
                            <h3 class="text-xl font-bold bg-gradient-to-r from-purple-200 to-pink-200 bg-clip-text text-transparent mb-4">
                                Sin Servicios
                            </h3>
                            <p class="text-purple-200 leading-relaxed">
                                <span v-if="props.isAdmin">No hay servicios de lavado finalizados en el historial.</span>
                                <span v-else>Aún no tienes servicios de lavado finalizados.</span>
                            </p>
                        </div>

                        <!-- Decoración inferior -->
                        <div class="flex justify-center items-center space-x-3 mt-6 opacity-60">
                            <div class="w-2 h-2 bg-purple-400/60 rounded-full animate-pulse"></div>
                            <div class="w-1 h-1 bg-pink-400/50 rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
                            <div class="w-3 h-3 bg-violet-400/40 rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
                            <div class="w-1 h-1 bg-fuchsia-400/60 rounded-full animate-pulse" style="animation-delay: 0.6s"></div>
                            <div class="w-2 h-2 bg-purple-300/50 rounded-full animate-pulse" style="animation-delay: 0.8s"></div>
                        </div>

                    </div>
                    <!-- Botón de volver -->
                    <div class="flex justify-start mt-6">
                        <Link
                            :href="route('dashboard')"
                            class="group relative bg-purple-500/20 hover:bg-purple-500/30 text-purple-100 px-4 py-2 rounded-full transition-all duration-300 transform hover:scale-105 border border-purple-400/20 backdrop-blur-sm"
                        >
                            <span class="relative z-10 flex items-center gap-2">
                                ⬅ Volver
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-purple-400/20 to-pink-400/20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </Link>
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

/* Animaciones suaves para las celdas */
.group:hover .bg-purple-500\/20 {
    background-color: rgba(147, 51, 234, 0.3);
    transform: scale(1.02);
    transition: all 0.3s ease-in-out;
}

.group:hover .bg-pink-500\/20 {
    background-color: rgba(236, 72, 153, 0.3);
    transform: scale(1.02);
    transition: all 0.3s ease-in-out;
}

.group:hover .bg-violet-500\/20 {
    background-color: rgba(139, 92, 246, 0.3);
    transform: scale(1.02);
    transition: all 0.3s ease-in-out;
}

.group:hover .bg-fuchsia-500\/20 {
    background-color: rgba(217, 70, 239, 0.3);
    transform: scale(1.02);
    transition: all 0.3s ease-in-out;
}

.group:hover .bg-green-500\/20 {
    background-color: rgba(34, 197, 94, 0.3);
    transform: scale(1.02);
    transition: all 0.3s ease-in-out;
}

.group:hover .bg-yellow-500\/20 {
    background-color: rgba(234, 179, 8, 0.3);
    transform: scale(1.02);
    transition: all 0.3s ease-in-out;
}

.group:hover .bg-blue-500\/20 {
    background-color: rgba(59, 130, 246, 0.3);
    transform: scale(1.02);
    transition: all 0.3s ease-in-out;
}

.group:hover .bg-orange-500\/20 {
    background-color: rgba(249, 115, 22, 0.3);
    transform: scale(1.02);
    transition: all 0.3s ease-in-out;
}

/* Scroll suave */
html {
  scroll-behavior: smooth;
}

/* Efectos adicionales para los botones de paginación */
.group:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
</style>
