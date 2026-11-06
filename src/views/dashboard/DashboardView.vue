<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useStore } from 'vuex'
import { Code, Database, Smartphone, ArrowRight } from 'lucide-vue-next'
import AppLayout from '@/layouts/AppLayout.vue'
import { getGroups } from '@/services/groups'
import { getGroupAttendance, getGroupAverage } from '@/services/attendance'

const store = useStore()
const groups = ref([])

const iconMap = { code: Code, database: Database, smartphone: Smartphone }

// Resumen de cada grupo: cantidad de estudiantes y promedio de asistencia
const totalStudents = computed(() => groups.value.reduce((acc, g) => acc + g.students, 0))
const generalAverage = computed(() => {
  if (!groups.value.length) return 0
  const sum = groups.value.reduce((acc, g) => acc + g.average, 0)
  return Math.round((sum / groups.value.length) * 10) / 10
})

onMounted(async () => {
  const list = await getGroups()
  groups.value = await Promise.all(
    list.map(async (group) => {
      const { students } = await getGroupAttendance(group.id)
      return { ...group, students: students.length, average: getGroupAverage(students) }
    }),
  )
})
</script>

<template>
  <AppLayout>
    <h1 class="page-title">¡Bienvenido, {{ store.state.user?.name ?? 'Docente' }}!</h1>
    <p class="page-subtitle">
      Consulta y registra la información de asistencia académica desde un solo lugar.
    </p>

    <div class="stats">
      <v-card class="stat-card" variant="outlined">
        <span class="stat-value">{{ groups.length }}</span>
        <span class="stat-label">Grupos asignados</span>
      </v-card>
      <v-card class="stat-card" variant="outlined">
        <span class="stat-value">{{ totalStudents }}</span>
        <span class="stat-label">Estudiantes</span>
      </v-card>
      <v-card class="stat-card" variant="outlined">
        <span class="stat-value">{{ generalAverage }}%</span>
        <span class="stat-label">Asistencia promedio</span>
      </v-card>
    </div>

    <h2 class="section-title">Mis grupos</h2>

    <div class="group-grid">
      <RouterLink
        v-for="group in groups"
        :key="group.id"
        :to="{ name: 'attendance', query: { grupo: group.id } }"
        class="group-card"
      >
        <div class="icon-circle">
          <component :is="iconMap[group.icon]" :size="28" />
        </div>
        <h3>{{ group.subject }}</h3>
        <p class="group-meta">{{ group.group }} · {{ group.schedule }}</p>
        <div class="group-chips">
          <v-chip size="small" variant="tonal">{{ group.students }} estudiantes</v-chip>
          <v-chip size="small" variant="tonal" :color="group.average < 70 ? 'error' : 'success'">
            {{ group.average }}% asistencia
          </v-chip>
        </div>
        <span class="group-link"> Ver asistencia <ArrowRight :size="16" /> </span>
      </RouterLink>
    </div>
  </AppLayout>
</template>

<style scoped>
.stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 1rem;
  margin-top: 1.75rem;
}

.stat-card {
  display: flex;
  flex-direction: column;
  padding: 1rem 1.25rem;
  border-radius: var(--radius);
  border-color: rgba(var(--v-theme-on-surface), 0.12);
}

.stat-value {
  font-size: 1.75rem;
  font-weight: 800;
  color: rgb(var(--v-theme-primary));
}

.stat-label {
  color: rgb(var(--v-theme-muted));
  font-size: 0.9rem;
}

.section-title {
  font-size: 1.3rem;
  font-weight: 700;
  margin-top: 2.25rem;
}

.group-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 1.25rem;
  margin-top: 1rem;
}

.group-card {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  padding: 1.5rem;
  background: rgb(var(--v-theme-surface));
  border: 2px solid rgba(var(--v-theme-primary), 0.6);
  border-radius: var(--radius);
  transition:
    transform 0.2s ease,
    box-shadow 0.2s ease;
}

.group-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 24px rgba(0, 0, 0, 0.12);
}

.group-card h3 {
  font-size: 1.2rem;
  font-weight: 700;
  margin-top: 0.75rem;
}

.group-meta {
  color: rgb(var(--v-theme-muted));
  font-size: 0.95rem;
}

.group-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
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