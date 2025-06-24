<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  user: Object,
})
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-900 via-violet-800 to-indigo-900 relative overflow-hidden">
    <!-- Elementos de fondo animados -->
    <div class="absolute inset-0 overflow-hidden">
      <!-- Burbujas flotantes con colores mora -->
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

      <!-- Ondas de agua con tonos mora -->
      <div class="absolute bottom-0 left-0 w-full h-32 bg-gradient-to-t from-purple-800/40 to-transparent">
        <div class="absolute bottom-0 w-full h-16 bg-gradient-to-r from-violet-600/20 via-purple-400/20 to-violet-600/20 animate-pulse">
        </div>
      </div>
    </div>

    <!-- Header con glassmorphism -->
    <header class="relative z-20 bg-white/10 backdrop-blur-md shadow-2xl border-b border-purple-300/20">
      <div class="max-w-7xl mx-auto py-6 px-4 flex justify-between items-center">
        <!-- Logo con efectos -->
        <Link href="/dashboard" class="group relative">
          <div class="absolute inset-0 bg-purple-300/20 rounded-lg blur-sm group-hover:blur-md transition-all duration-300"></div>
          <div class="relative bg-gradient-to-r from-purple-200 to-pink-200 bg-clip-text text-transparent text-2xl font-black tracking-wide hover:scale-105 transition-transform duration-300">
            Morita Lavadero
          </div>
        </Link>

        <!-- Navegación con efectos -->
        <div class="flex items-center space-x-6">
          <!-- Usuario info con estilo -->
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

    <!-- Main content con overlay -->
    <main class="relative z-10 py-10 px-4 max-w-7xl mx-auto">
      <slot />
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

/* Scroll suave */
html {
  scroll-behavior: smooth;
}
</style>
