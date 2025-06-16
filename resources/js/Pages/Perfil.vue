<template>
  <div>
    <h2 class="text-2xl font-bold">Mi Perfil</h2>
    <p>Nombre: {{ $page.props.auth.user.name }}</p>
    <p>Email: {{ $page.props.auth.user.email }}</p>
    <p>Rol: {{ $page.props.auth.user.role }}</p>
  </div>
</template>

<script setup>
defineOptions({
  layout: () => import('@/Layouts/AppLayout.vue'),
})
</script>
