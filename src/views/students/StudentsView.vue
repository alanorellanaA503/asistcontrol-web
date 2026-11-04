<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import EmptyState from '@/components/EmptyState.vue'
</script>

<template>
  <AppLayout>
    <h1 class="page-title">Estudiantes</h1>
    <p class="page-subtitle">Gestión de estudiantes por grupo.</p>

    <EmptyState description="Aquí irán la tabla de estudiantes y el formulario para agregarlos." />
  </AppLayout>
</template>
