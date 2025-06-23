<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
defineProps({
  user: Object,
})
</script>

<template>
  <AppLayout :user="user">
    <h2 class="text-2xl font-bold">Dashboard de Admin</h2>
    <p>Bienvenido, {{ user.name }}</p>
    <Link :href="route('servicios.index')"
        class="flex items-center justify-center p-6 bg-indigo-600 text-white rounded-lg shadow-md hover:bg-indigo-700 transition duration-300 ease-in-out transform hover:scale-105">
        <span class="text-lg font-bold">Ver Mis Servicios</span>
    </Link>
    <Link :href="route('boxes.index')"
          class="flex items-center justify-center p-6 bg-green-600 text-white rounded-lg shadow-md hover:bg-green-700 transition duration-300 ease-in-out transform hover:scale-105">
          <span class="text-lg font-bold">Administrar Boxes</span>
      </Link>
  </AppLayout>
</template>
