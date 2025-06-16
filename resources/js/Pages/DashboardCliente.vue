<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
  user: Object,
})
</script>

<template>
  <AppLayout :user="user">
    <h2 class="text-2xl font-bold">Dashboard de Cliente</h2>
    <p>Bienvenido, {{ user.name }}</p>
  </AppLayout>
</template>