<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import EmptyState from '@/components/EmptyState.vue'
</script>

<template>
  <AppLayout>
    <h1 class="page-title">Asistencia</h1>
    <p class="page-subtitle">Registro y consulta de asistencia por grupo.</p>

    <EmptyState
      description="Aquí irán la lista de estudiantes, el registro diario y el historial."
    />
  </AppLayout>
</template>
