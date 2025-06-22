<template>
  <div>
    <h1>Listado de Boxes</h1>
    <Link href="/boxes/create">Crear nuevo box</Link>
    <table border="1" cellpadding="5" class="mt-4">
      <thead>
        <tr>
          <th>ID</th>
          <th>Número</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="box in boxes" :key="box.id">
          <td>{{ box.id }}</td>
          <td>{{ box.numero }}</td>
          <td>{{ box.estado }}</td>
          <td>
            <Link :href="`/boxes/${box.id}`">Ver</Link> |
            <Link :href="`/boxes/${box.id}/edit`">Editar</Link> |
            <button @click="eliminar(box.id)" style="color: red;">Eliminar</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { router, Link } from '@inertiajs/vue3';

const props = defineProps({
  boxes: Array
});

const eliminar = (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar este Box?')) {
        router.post(`/boxes/${id}`, {
            _method: 'delete', // spoofing del método
        }, {
            onSuccess: () => {
                router.visit(route('boxes.index'), {
                    method: 'get',
                    preserveState: true,
                    preserveScroll: true,
                });
            },
            onError: (errors) => {
                console.error('Error al eliminar el box:', errors);
            }
        });
    }
};
</script>