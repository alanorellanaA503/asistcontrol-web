<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useStore } from 'vuex'
import { Code, Database, Smartphone, ArrowRight } from 'lucide-vue-next'
import AppLayout from '@/layouts/AppLayout.vue'
import { getGroups } from '@/services/groups'

const store = useStore()
const groups = ref([])

const iconMap = { code: Code, database: Database, smartphone: Smartphone }

onMounted(async () => {
  groups.value = await getGroups()
})
</script>

<template>
  <AppLayout>
    <h1 class="page-title">¡Bienvenido, {{ store.state.user?.name ?? 'Docente' }}!</h1>
    <p class="page-subtitle">
      Consulta y registra la información de asistencia académica desde un solo lugar.
    </p>

    <div class="group-grid">
      <RouterLink v-for="group in groups" :key="group.id" to="/asistencia" class="group-card">
        <div class="icon-circle">
          <component :is="iconMap[group.icon]" :size="28" />
        </div>
        <h2>{{ group.subject }}</h2>
        <p class="group-meta">{{ group.group }} · {{ group.schedule }}</p>
        <span class="group-link"> Ver asistencia <ArrowRight :size="16" /> </span>
      </RouterLink>
    </div>
  </AppLayout>
</template>

<style scoped>
.group-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 1.25rem;
  margin-top: 2rem;
}

.group-card {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  padding: 1.5rem;
  background: rgb(var(--v-theme-surface));
  border: 1px solid rgba(var(--v-theme-on-surface), 0.1);
  border-radius: var(--radius);
  transition:
    transform 0.2s ease,
    box-shadow 0.2s ease;
}

.group-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 24px rgba(0, 0, 0, 0.12);
}

.group-card h2 {
  font-size: 1.2rem;
  font-weight: 700;
  margin-top: 0.75rem;
}

.group-meta {
  color: rgb(var(--v-theme-muted));
  font-size: 0.95rem;
}

.group-link {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  margin-top: 0.75rem;
  color: rgb(var(--v-theme-primary));
  font-weight: 600;
}

.icon-circle {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: rgba(var(--v-theme-primary), 0.14);
  color: rgb(var(--v-theme-primary));
}
</style>
