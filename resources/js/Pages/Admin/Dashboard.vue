<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
  user: Object,
});
</script>

<template>
  <AppLayout title="Dashboard de Admin" :user="user">
    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Tarjeta principal con glassmorphism -->
        <div class="bg-white/15 backdrop-blur-md rounded-2xl shadow-2xl border border-white/20 p-8 relative overflow-hidden">
          <!-- Partículas internas flotantes -->
          <div class="absolute top-4 right-4 w-2 h-2 bg-pink-300/40 rounded-full animate-pulse"></div>
          <div class="absolute top-8 left-8 w-1 h-1 bg-violet-400/50 rounded-full animate-bounce" style="animation-delay: 0.5s"></div>
          <div class="absolute bottom-6 right-12 w-3 h-3 bg-purple-200/30 rounded-full animate-pulse" style="animation-delay: 1s"></div>
          <div class="absolute bottom-12 left-6 w-2 h-2 bg-fuchsia-300/35 rounded-full animate-bounce" style="animation-delay: 1.5s"></div>

          <div class="text-center mb-8 relative">
            <!-- Elemento decorativo superior flotante -->
            <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
              <div class="w-12 h-12 bg-gradient-to-r from-purple-400/30 to-pink-400/30 rounded-full animate-float backdrop-blur-sm border border-white/20 flex items-center justify-center">
                <span class="text-lg">🛠️</span>
              </div>
            </div>

            <h2 class="text-3xl font-bold bg-gradient-to-r from-purple-200 to-pink-200 bg-clip-text text-transparent mb-4 pt-6">
              Dashboard de Admin
            </h2>
            <p class="text-purple-100 text-lg">Bienvenido, {{ user.name }}</p>

            <!-- Línea decorativa con gradiente -->
            <div class="flex justify-center items-center space-x-2 mb-4">
              <div class="w-8 h-0.5 bg-gradient-to-r from-transparent to-purple-400/50"></div>
              <div class="w-3 h-3 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full animate-pulse"></div>
              <div class="w-12 h-0.5 bg-gradient-to-r from-purple-400/50 to-pink-400/50"></div>
              <div class="w-3 h-3 bg-gradient-to-r from-pink-400 to-violet-400 rounded-full animate-pulse" style="animation-delay: 0.5s"></div>
              <div class="w-8 h-0.5 bg-gradient-to-r from-pink-400/50 to-transparent"></div>
            </div>
          </div>

          <div class="space-y-4">
            <Link
              :href="route('servicios.index')"
              class="group relative flex items-center justify-center p-4 bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-100 rounded-lg transition-all duration-300 transform hover:scale-105 border border-indigo-400/20 backdrop-blur-sm"
            >
              <span class="relative z-10 flex items-center gap-2 text-lg font-bold">
                📋 Ver Mis Servicios
              </span>
              <div class="absolute inset-0 bg-gradient-to-r from-indigo-400/20 to-blue-400/20 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </Link>

            <Link
              :href="route('boxes.index')"
              class="group relative flex items-center justify-center p-4 bg-green-500/20 hover:bg-green-500/30 text-green-100 rounded-lg transition-all duration-300 transform hover:scale-105 border border-green-400/20 backdrop-blur-sm"
            >
              <span class="relative z-10 flex items-center gap-2 text-lg font-bold">
                🏠 Administrar Boxes
              </span>
              <div class="absolute inset-0 bg-gradient-to-r from-green-400/20 to-emerald-400/20 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
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
