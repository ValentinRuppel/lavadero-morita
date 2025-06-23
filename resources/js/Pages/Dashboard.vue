<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3' // Importa el componente Link de Inertia

defineProps({
  user: Object, 
})
</script>

<template>
  <AppLayout :user="user">
    <h2 class="text-2xl font-bold">Dashboard de Cliente</h2>
    <p class="mt-2 text-lg">Bienvenido, {{ user.name }}</p>

    <div class="mt-8">
      <h3 class="text-xl font-semibold mb-4">Mis Vehículos</h3>

      <p class="mb-4">
        Gestiona los vehículos asociados a tu cuenta:
        <Link :href="route('vehiculos.index')" class="text-indigo-600 hover:text-indigo-800 font-medium ml-2">
          Ver y Administrar Vehículos
        </Link>
      </p>

      <Link :href="route('servicios.index')"
          class="flex items-center justify-center p-6 bg-indigo-600 text-white rounded-lg shadow-md hover:bg-indigo-700 transition duration-300 ease-in-out transform hover:scale-105">
          <span class="text-lg font-bold">Ver Mis Servicios</span>
      </Link>

      <div class="mt-6 p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
        <h4 class="text-lg font-medium text-gray-900 mb-3">Gestión de Vehículos</h4>
        <p class="text-gray-700 mb-4">Aquí puedes añadir, editar o eliminar los vehículos que tienes registrados con nosotros.</p>
        <Link :href="route('vehiculos.index')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
          Administrar Vehículos
          </Link>
      </div>

    </div>
  </AppLayout>
</template>