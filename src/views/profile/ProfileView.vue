<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import EmptyState from '@/components/EmptyState.vue'
</script>

<template>
  <AppLayout>
    <h1 class="page-title">Mi perfil</h1>
    <p class="page-subtitle">Información de su cuenta.</p>

    <EmptyState description="Aquí irán los datos como el nombre y rol del docente" />
  </AppLayout>
</template>
