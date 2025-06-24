<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  user: Object,
})
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-900 via-violet-800 to-indigo-900 relative overflow-hidden">
    <!-- Elementos de fondo animados - EXACTO COMO EL LAYOUT -->
    <div class="absolute inset-0 overflow-hidden">
      <!-- Burbujas flotantes con colores mora - TODAS LAS DEL LAYOUT -->
      <div class="absolute top-20 left-10 w-4 h-4 bg-pink-300/30 rounded-full animate-bounce"></div>
      <div class="absolute top-40 right-20 w-6 h-6 bg-purple-200/25 rounded-full animate-pulse"></div>
      <div class="absolute bottom-40 left-20 w-5 h-5 bg-violet-300/35 rounded-full animate-bounce"
        style="animation-delay: 0.5s"></div>
      <div class="absolute top-60 right-40 w-3 h-3 bg-fuchsia-200/30 rounded-full animate-pulse"
        style="animation-delay: 1s"></div>
      <div class="absolute top-80 left-40 w-4 h-4 bg-purple-300/20 rounded-full animate-bounce"
        style="animation-delay: 1.5s"></div>
      <div class="absolute top-32 right-16 w-3 h-3 bg-violet-200/25 rounded-full animate-pulse"
        style="animation-delay: 0.3s"></div>
      <div class="absolute bottom-60 right-32 w-5 h-5 bg-pink-400/30 rounded-full animate-bounce"
        style="animation-delay: 0.8s"></div>
      <div class="absolute top-16 left-32 w-2 h-2 bg-purple-400/35 rounded-full animate-pulse"
        style="animation-delay: 1.2s"></div>
      <div class="absolute bottom-80 left-16 w-6 h-6 bg-fuchsia-300/20 rounded-full animate-bounce"
        style="animation-delay: 0.2s"></div>
      <div class="absolute top-72 right-24 w-4 h-4 bg-violet-400/30 rounded-full animate-pulse"
        style="animation-delay: 1.8s"></div>
      <div class="absolute bottom-24 right-48 w-3 h-3 bg-purple-200/40 rounded-full animate-bounce"
        style="animation-delay: 0.7s"></div>
      <div class="absolute top-48 left-24 w-5 h-5 bg-pink-200/25 rounded-full animate-pulse"
        style="animation-delay: 1.4s"></div>
      <div class="absolute bottom-72 right-12 w-2 h-2 bg-violet-300/45 rounded-full animate-bounce"
        style="animation-delay: 0.4s"></div>
      <div class="absolute top-24 right-52 w-4 h-4 bg-fuchsia-400/25 rounded-full animate-pulse"
        style="animation-delay: 1.6s"></div>
      <div class="absolute bottom-48 left-48 w-6 h-6 bg-purple-300/30 rounded-full animate-bounce"
        style="animation-delay: 0.9s"></div>
      <div class="absolute top-88 left-28 w-3 h-3 bg-pink-400/35 rounded-full animate-pulse"
        style="animation-delay: 2s"></div>
      <div class="absolute bottom-16 right-28 w-5 h-5 bg-violet-200/30 rounded-full animate-bounce"
        style="animation-delay: 0.6s"></div>
      <div class="absolute top-52 right-36 w-2 h-2 bg-purple-400/25 rounded-full animate-pulse"
        style="animation-delay: 1.3s"></div>
      <div class="absolute bottom-88 left-36 w-4 h-4 bg-fuchsia-200/40 rounded-full animate-bounce"
        style="animation-delay: 0.1s"></div>
      <div class="absolute top-36 left-52 w-3 h-3 bg-pink-300/20 rounded-full animate-pulse"
        style="animation-delay: 1.7s"></div>

      <!-- Ondas de agua con tonos mora - EXACTO COMO EL LAYOUT -->
      <div class="absolute bottom-0 left-0 w-full h-32 bg-gradient-to-t from-purple-800/40 to-transparent">
        <div class="absolute bottom-0 w-full h-16 bg-gradient-to-r from-violet-600/20 via-purple-400/20 to-violet-600/20 animate-pulse">
        </div>
      </div>
    </div>

    <!-- Header con glassmorphism - EXACTO COMO EL LAYOUT -->
    <header class="relative z-20 bg-white/10 backdrop-blur-md shadow-2xl border-b border-purple-300/20">
      <div class="max-w-7xl mx-auto py-6 px-4 flex justify-between items-center">
        <!-- Logo con efectos - EXACTO COMO EL LAYOUT -->
        <Link href="/dashboard" class="group relative">
          <div class="absolute inset-0 bg-purple-300/20 rounded-lg blur-sm group-hover:blur-md transition-all duration-300"></div>
          <div class="relative bg-gradient-to-r from-purple-200 to-pink-200 bg-clip-text text-transparent text-2xl font-black tracking-wide hover:scale-105 transition-transform duration-300">
            Morita Lavadero
          </div>
        </Link>

        <!-- Navegación con efectos - EXACTO COMO EL LAYOUT -->
        <div class="flex items-center space-x-6">
          <!-- Usuario info con estilo - EXACTO COMO EL LAYOUT -->
          <div class="bg-white/10 backdrop-blur-sm rounded-full px-4 py-2 border border-purple-300/20">
            <span class="text-sm text-purple-100 flex items-center gap-2">
              <div class="w-8 h-8 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold text-xs">
                {{ user?.name?.[0]?.toUpperCase() ?? '👤' }}
              </div>
              <span class="font-medium">{{ user?.name ?? 'Invitado' }}</span>
            </span>
          </div>

          <Link href="/logout" method="post" as="button"
            class="group relative bg-red-500/20 hover:bg-red-500/30 text-red-200 hover:text-red-100 px-4 py-2 rounded-full transition-all duration-300 transform hover:scale-105 border border-red-400/20 backdrop-blur-sm">
            <span class="relative z-10 flex items-center gap-2">
              🚪 Cerrar sesión
            </span>
            <div class="absolute inset-0 bg-gradient-to-r from-red-400/20 to-pink-400/20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          </Link>
        </div>
      </div>
    </header>

    <!-- Contenido principal - CON EL CONTENEDOR DEL LAYOUT -->
    <main class="relative z-10 py-10 px-4 max-w-7xl mx-auto">
      <div class="w-full max-w-4xl mx-auto">
        <!-- Tarjeta principal del dashboard con glassmorphism mejorado -->
        <div class="bg-white/15 backdrop-blur-md rounded-2xl shadow-2xl border border-white/20 p-8 mb-8 relative overflow-hidden">
          <!-- Efectos de partículas internas -->
          <div class="absolute top-4 right-4 w-2 h-2 bg-pink-300/40 rounded-full animate-pulse"></div>
          <div class="absolute top-8 left-8 w-1 h-1 bg-violet-400/50 rounded-full animate-bounce" style="animation-delay: 0.5s"></div>
          <div class="absolute bottom-6 right-12 w-3 h-3 bg-purple-200/30 rounded-full animate-pulse" style="animation-delay: 1s"></div>
          <div class="absolute bottom-12 left-6 w-2 h-2 bg-fuchsia-300/35 rounded-full animate-bounce" style="animation-delay: 1.5s"></div>

          <div class="text-center mb-8 relative">
            <!-- Elemento decorativo superior flotante -->
            <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
              <div class="w-12 h-12 bg-gradient-to-r from-purple-400/30 to-pink-400/30 rounded-full animate-float backdrop-blur-sm border border-white/20 flex items-center justify-center">
                <span class="text-lg">🏠</span>
              </div>
            </div>

            <h2 class="text-4xl font-bold bg-gradient-to-r from-purple-200 to-pink-200 bg-clip-text text-transparent mb-4 pt-6">
              Bienvenido {{ user.name }}
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

          <!-- Grid de opciones principales con efectos glassmorphism -->
          <div class="grid md:grid-cols-2 gap-8 mb-8">
            <!-- Servicios con glassmorphism -->
            <Link :href="route('servicios.index')"
                  class="group relative bg-gradient-to-r from-indigo-500/80 to-purple-600/80 hover:from-indigo-600/90 hover:to-purple-700/90 text-white rounded-xl shadow-2xl p-6 transform hover:scale-105 transition-all duration-300 ease-in-out backdrop-blur-md border border-white/20 overflow-hidden">

              <!-- Partículas internas flotantes -->
              <div class="absolute top-2 right-2 w-1 h-1 bg-white/60 rounded-full animate-pulse"></div>
              <div class="absolute top-4 left-4 w-1 h-1 bg-yellow-200/50 rounded-full animate-bounce" style="animation-delay: 0.3s"></div>
              <div class="absolute bottom-3 right-6 w-1 h-1 bg-pink-200/60 rounded-full animate-pulse" style="animation-delay: 0.8s"></div>
              <div class="absolute bottom-6 left-3 w-1 h-1 bg-white/50 rounded-full animate-bounce" style="animation-delay: 1.2s"></div>

              <!-- Efecto de brillo en movimiento -->
              <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>

              <div class="relative z-10">
                <div class="flex items-center justify-center mb-4">
                  <!-- Contenedor del ícono con glassmorphism -->
                  <div class="relative bg-white/20 backdrop-blur-sm rounded-full p-3 border border-white/30 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                  </div>
                </div>
                <h3 class="text-xl font-bold text-center mb-2 group-hover:text-yellow-200 transition-colors duration-300">Mis Servicios</h3>
                <p class="text-center text-white/90 group-hover:text-white transition-colors duration-300 text-sm">Ver historial y servicios activos</p>
              </div>
            </Link>

            <!-- Vehículos con glassmorphism -->
            <Link :href="route('vehiculos.index')"
                  class="group relative bg-gradient-to-r from-green-500/80 to-teal-600/80 hover:from-green-600/90 hover:to-teal-700/90 text-white rounded-xl shadow-2xl p-6 transform hover:scale-105 transition-all duration-300 ease-in-out backdrop-blur-md border border-white/20 overflow-hidden">

              <!-- Partículas internas flotantes -->
              <div class="absolute top-2 right-2 w-1 h-1 bg-white/60 rounded-full animate-pulse"></div>
              <div class="absolute top-4 left-4 w-1 h-1 bg-yellow-200/50 rounded-full animate-bounce" style="animation-delay: 0.3s"></div>
              <div class="absolute bottom-3 right-6 w-1 h-1 bg-pink-200/60 rounded-full animate-pulse" style="animation-delay: 0.8s"></div>
              <div class="absolute bottom-6 left-3 w-1 h-1 bg-white/50 rounded-full animate-bounce" style="animation-delay: 1.2s"></div>

              <!-- Efecto de brillo en movimiento -->
              <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>

              <div class="relative z-10">
                <div class="flex items-center justify-center mb-4">
                  <!-- Contenedor del ícono con glassmorphism -->
                  <div class="relative bg-white/20 backdrop-blur-sm rounded-full p-3 border border-white/30 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                  </div>
                </div>
                <h3 class="text-xl font-bold text-center mb-2 group-hover:text-yellow-200 transition-colors duration-300">Mis Vehículos</h3>
                <p class="text-center text-white/90 group-hover:text-white transition-colors duration-300 text-sm">Administrar vehículos registrados</p>
              </div>
            </Link>
          </div>

          <!-- Decoración inferior con puntos animados -->
          <div class="flex justify-center items-center space-x-3 opacity-60">
            <div class="w-2 h-2 bg-purple-400/60 rounded-full animate-pulse"></div>
            <div class="w-1 h-1 bg-pink-400/50 rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
            <div class="w-3 h-3 bg-violet-400/40 rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
            <div class="w-1 h-1 bg-fuchsia-400/60 rounded-full animate-pulse" style="animation-delay: 0.6s"></div>
            <div class="w-2 h-2 bg-purple-300/50 rounded-full animate-pulse" style="animation-delay: 0.8s"></div>
          </div>
        </div>
      </div>
    </main>
  </div>
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

/* Animación personalizada para las burbujas */
@keyframes bubble {
  0% {
    transform: translateY(0) scale(1);
    opacity: 0.7;
  }
  50% {
    transform: translateY(-20px) scale(1.1);
    opacity: 1;
  }
  100% {
    transform: translateY(-40px) scale(0.9);
    opacity: 0.3;
  }
}

/* Efectos adicionales para las tarjetas */
.group:hover svg {
    transform: scale(1.1) rotate(12deg);
    transition: transform 0.3s ease-in-out;
}

/* Scroll suave */
html {
  scroll-behavior: smooth;
}
</style>
