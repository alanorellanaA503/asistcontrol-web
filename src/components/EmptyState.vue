<script setup>
import { Construction } from 'lucide-vue-next'

defineProps({
  title: { type: String, default: 'Pantalla en construcción' },
  description: { type: String, default: 'Esta sección se desarrollará en una rama propia.' },
})
</script>

<template>
  <div class="empty-state">
    <div class="icon-circle">
      <Construction :size="28" />
    </div>
    <h2>{{ title }}</h2>
    <p>{{ description }}</p>
  </div>
</template>

<style scoped>
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  padding: 3rem 1.5rem;
  text-align: center;
  background: rgb(var(--v-theme-surface));
  border: 1px dashed rgba(var(--v-theme-on-surface), 0.2);
  border-radius: var(--radius);
}

.empty-state h2 {
  font-size: 1.25rem;
  font-weight: 700;
  margin-top: 0.5rem;
}

.empty-state p {
  color: rgb(var(--v-theme-muted));
}

.icon-circle {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: rgba(var(--v-theme-primary), 0.14);
  color: rgb(var(--v-theme-primary));
}
</style>
