<template>
  <div>
    <h1>Detalle del Box</h1>
    <p><strong>ID:</strong> {{ box.id }}</p>
    <p><strong>Número:</strong> {{ box.numero }}</p>
    <p><strong>Estado:</strong> {{ box.estado }}</p>
    <Link href="/boxes">Volver al listado</Link>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
const props = defineProps({ box: Object });
</script>