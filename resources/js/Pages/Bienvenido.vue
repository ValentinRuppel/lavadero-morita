<template>
  <div class="min-h-screen bg-gradient-to-b from-blue-50 to-blue-200 flex flex-col items-center justify-center text-center p-6">
    <img src="/images/morita-logo.png" alt="Morita Logo" class="w-32 h-32 mb-4" v-if="logoDisponible" />
    <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
      Bienvenidos a <span class="text-blue-600">Morita Lavadero</span>
    </h1>
    <p class="text-lg md:text-xl text-gray-700 mb-6">
      Cuidamos tu vehículo como si fuera nuestro
    </p>
    <div class="flex gap-4">
      <Link href="/login" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow transition">
        Iniciar Sesión
      </Link>
      <Link href="/precios" class="bg-white border border-blue-600 text-blue-600 hover:bg-blue-50 px-6 py-3 rounded-lg transition">
        Ver Precios
      </Link>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

// Opcional: mostrar logo si existe
const logoDisponible = true
</script>