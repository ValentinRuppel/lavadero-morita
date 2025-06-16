<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  user: Object,
})
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 flex justify-between items-center">
        <h1 class="text-xl font-bold text-blue-700">Morita Lavadero</h1>
        <div class="space-x-4">
          <span class="text-sm text-gray-600">👤 {{ user?.name ?? 'Invitado' }}</span>
          <Link href="/perfil" class="text-blue-600 hover:underline">Perfil</Link>
          <Link href="/logout" method="post" as="button" class="text-red-500 hover:underline">Cerrar sesión</Link>
        </div>
      </div>
    </header>

    <main class="py-10 px-4 max-w-7xl mx-auto">
      <slot />
    </main>
  </div>
</template>
