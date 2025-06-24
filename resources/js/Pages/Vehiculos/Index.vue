<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    user: Object,
    vehiculos: Object,
});

const deleteVehiculo = (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar este vehículo?')) {
        router.post(`/vehiculos/${id}`, {
            _method: 'delete',
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
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Tarjeta principal con glassmorphism -->
                <div class="bg-white/15 backdrop-blur-md rounded-2xl shadow-2xl border border-white/20 p-8 relative overflow-hidden">
                    <!-- Partículas internas flotantes -->
                    <div class="absolute top-4 right-4 w-2 h-2 bg-pink-300/40 rounded-full animate-pulse"></div>
                    <div class="absolute top-8 left-8 w-1 h-1 bg-violet-400/50 rounded-full animate-bounce" style="animation-delay: 0.5s"></div>
                    <div class="absolute bottom-6 right-12 w-3 h-3 bg-purple-200/30 rounded-full animate-pulse" style="animation-delay: 1s"></div>
                    <div class="absolute bottom-12 left-6 w-2 h-2 bg-fuchsia-300/35 rounded-full animate-bounce" style="animation-delay: 1.5s"></div>

                    <div class="text-center mb-8 relative">

                        <h2 class="text-3xl font-bold bg-gradient-to-r from-purple-200 to-pink-200 bg-clip-text text-transparent mb-4 pt-6">
                            Mis Vehículos
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

                    <!-- Flash messages -->
                    <div v-if="$page.props.flash && $page.props.flash.success" class="bg-green-500/20 border border-green-400/30 text-green-100 p-4 rounded-lg backdrop-blur-sm mb-6">
                        <p class="font-bold">Éxito</p>
                        <p>{{ $page.props.flash.success }}</p>
                    </div>
                    <div v-if="$page.props.flash && $page.props.flash.error" class="bg-red-500/20 border border-red-400/30 text-red-100 p-4 rounded-lg backdrop-blur-sm mb-6">
                        <p class="font-bold">Error</p>
                        <p>{{ $page.props.flash.error }}</p>
                    </div>

                    <!-- Botón de registrar nuevo -->
                    <div class="flex justify-end mb-6">
                        <Link
                            :href="route('vehiculos.create')"
                            class="group relative bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-bold py-2 px-6 rounded-full transition-all duration-300 transform hover:scale-105 border border-white/20 backdrop-blur-sm"
                        >
                            <span class="relative z-10 flex items-center gap-2">
                                ➕ Registrar Nuevo Vehículo
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                        </Link>
                    </div>

                    <!-- Tabla -->
                    <div class="overflow-x-auto">
                        <div class="bg-white/5 backdrop-blur-lg rounded-2xl border border-purple-300/30 overflow-hidden shadow-2xl">
                            <table class="min-w-full divide-y divide-purple-300/20">
                                <thead class="bg-gradient-to-r from-purple-800/70 via-violet-800/70 to-purple-800/70 backdrop-blur-sm">
                                    <tr>
                                        <th class="px-4 py-4 text-left text-sm font-bold text-purple-100 uppercase tracking-wider">
                                            <span class="flex items-center gap-2">🏷️ Patente</span>
                                        </th>
                                        <th class="px-4 py-4 text-left text-sm font-bold text-purple-100 uppercase tracking-wider">
                                            <span class="flex items-center gap-2">🏭 Marca</span>
                                        </th>
                                        <th class="px-4 py-4 text-left text-sm font-bold text-purple-100 uppercase tracking-wider">
                                            <span class="flex items-center gap-2">🚙 Modelo</span>
                                        </th>
                                        <th class="px-4 py-4 text-left text-sm font-bold text-purple-100 uppercase tracking-wider">
                                            <span class="flex items-center gap-2">📅 Año</span>
                                        </th>
                                        <th class="px-4 py-4 text-left text-sm font-bold text-purple-100 uppercase tracking-wider">
                                            <span class="flex items-center gap-2">🚗 Tipo</span>
                                        </th>
                                        <th class="px-4 py-4 text-right text-sm font-bold text-purple-100 uppercase tracking-wider">
                                            <span class="flex items-center justify-end gap-2">⚙️ Acciones</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white/5 backdrop-blur-sm divide-y divide-purple-300/10">
                                    <tr v-if="vehiculos.data.length === 0">
                                        <td colspan="6" class="px-4 py-16 text-center">
                                            <div class="text-purple-200">
                                                <div class="text-4xl mb-4 animate-bounce">🚫</div>
                                                <h3 class="text-xl font-bold text-purple-100 mb-2">No hay vehículos registrados</h3>
                                                <p class="text-purple-300">¡Comienza registrando tu primer vehículo!</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-for="vehiculo in vehiculos.data" :key="vehiculo.id" class="hover:bg-white/10 transition-all duration-300 group">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="text-purple-100 bg-gradient-to-r from-purple-500/30 to-violet-500/30 px-3 py-1 rounded-full border border-purple-400/30 backdrop-blur-sm">
                                                {{ vehiculo.patente }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-purple-200">{{ vehiculo.marca_nombre }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-purple-200">{{ vehiculo.modelo_nombre }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-purple-200">{{ vehiculo.anio }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="text-purple-100 bg-gradient-to-r from-violet-500/30 to-purple-500/30 px-3 py-1 rounded-full border border-violet-400/30 backdrop-blur-sm">
                                                {{ vehiculo.tipo_nombre }} (${{ vehiculo.tipo_precio }})
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-right">
                                            <div class="flex justify-end gap-2">
                                                <Link
                                                    :href="route('vehiculos.show', vehiculo.id)"
                                                    class="group relative bg-blue-500/20 hover:bg-blue-500/30 text-blue-200 hover:text-blue-100 px-3 py-1 rounded-full transition-all duration-300 transform hover:scale-105 border border-blue-400/20 backdrop-blur-sm"
                                                >
                                                    <span class="relative z-10 flex items-center gap-1">👁️ Ver</span>
                                                    <div class="absolute inset-0 bg-gradient-to-r from-blue-400/20 to-cyan-400/20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                                </Link>
                                                <Link
                                                    :href="route('vehiculos.edit', vehiculo.id)"
                                                    class="group relative bg-yellow-500/20 hover:bg-yellow-500/30 text-yellow-200 hover:text-yellow-100 px-3 py-1 rounded-full transition-all duration-300 transform hover:scale-105 border border-yellow-400/20 backdrop-blur-sm"
                                                >
                                                    <span class="relative z-10 flex items-center gap-1">✏️ Editar</span>
                                                    <div class="absolute inset-0 bg-gradient-to-r from-yellow-400/20 to-orange-400/20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                                </Link>
                                                <button
                                                    @click="deleteVehiculo(vehiculo.id)"
                                                    class="group relative bg-red-500/20 hover:bg-red-500/30 text-red-200 hover:text-red-100 px-3 py-1 rounded-full transition-all duration-300 transform hover:scale-105 border border-red-400/20 backdrop-blur-sm"
                                                >
                                                    <span class="relative z-10 flex items-center gap-1">🗑️ Eliminar</span>
                                                    <div class="absolute inset-0 bg-gradient-to-r from-red-400/20 to-pink-400/20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-6 flex justify-center">
                        <nav class="flex gap-2">
                            <template v-for="(link, key) in vehiculos.links" :key="key">
                                <span
                                    v-if="link.url === null"
                                    class="px-3 py-2 text-sm text-purple-400 bg-purple-800/20 border border-purple-400/20 rounded-full backdrop-blur-sm"
                                    v-html="link.label"
                                />
                                <Link
                                    v-else
                                    :href="link.url"
                                    class="px-3 py-2 text-sm border border-purple-400/20 rounded-full backdrop-blur-sm transition-all duration-300 transform hover:scale-105"
                                    :class="{
                                        'bg-gradient-to-r from-purple-500 to-violet-500 text-white border-purple-400': link.active,
                                        'text-purple-200 bg-purple-800/20 hover:bg-purple-700/30': !link.active
                                    }"
                                    v-html="link.label"
                                />
                            </template>
                        </nav>
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

/* Efectos de hover */
.group:hover .shimmer {
    animation: shimmer 1.5s infinite;
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
