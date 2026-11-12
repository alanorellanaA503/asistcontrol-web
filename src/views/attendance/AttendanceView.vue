<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Search, Plus, Download } from 'lucide-vue-next'
import AppLayout from '@/layouts/AppLayout.vue'
import { getGroups } from '@/services/groups'
import { getGroupAttendance, getGroupAverage } from '@/services/attendance'

const route = useRoute()
const router = useRouter()

const groups = ref([])
const selectedGroupId = ref(null)
const students = ref([])
const totalClasses = ref(0)
const search = ref('')

// Menos de este porcentaje se marca en rojo
const MIN_PERCENTAGE = 70

const headers = [
  { title: 'Estudiante', key: 'name' },
  { title: 'Carné', key: 'code' },
  { title: 'Presente', key: 'present', align: 'center' },
  { title: 'Tarde', key: 'late', align: 'center' },
  { title: 'Ausente', key: 'absent', align: 'center' },
  { title: 'Asistencia', key: 'percentage', align: 'center' },
]

const selectedGroup = computed(() => groups.value.find((g) => g.id === selectedGroupId.value))
const groupAverage = computed(() => getGroupAverage(students.value))

async function loadAttendance(groupId) {
  const data = await getGroupAttendance(groupId)
  students.value = data.students
  totalClasses.value = data.totalClasses
}

function percentageClass(value) {
  return value < MIN_PERCENTAGE ? 'text-error' : 'text-success'
}

function goToNewAttendance() {
  router.push({ name: 'register-attendance', query: { grupo: selectedGroupId.value } })
}

onMounted(async () => {
  groups.value = await getGroups()
  // Si viene desde el dashboard, trae el grupo en la URL: /asistencia?grupo=1
  const fromUrl = Number(route.query.grupo)
  selectedGroupId.value = groups.value.some((g) => g.id === fromUrl) ? fromUrl : groups.value[0]?.id
})

watch(selectedGroupId, (id) => {
  if (!id) return
  search.value = ''
  loadAttendance(id)
  router.replace({ query: { grupo: id } })
})
</script>

<template>
  <AppLayout>
    <h1 class="page-title">Asistencia del grupo</h1>
    <p v-if="selectedGroup" class="page-subtitle">
      {{ selectedGroup.subject }} · {{ selectedGroup.group }} · {{ totalClasses }} clases
      registradas
    </p>

    <div class="filters">
      <v-select
        v-model="selectedGroupId"
        :items="groups"
        :item-title="(g) => `${g.subject} · ${g.group}`"
        item-value="id"
        label="Grupo"
        variant="outlined"
        color="primary"
        density="comfortable"
        hide-details
      />
      <v-text-field
        v-model="search"
        label="Buscar estudiante o carné"
        variant="outlined"
        color="primary"
        density="comfortable"
        hide-details
        clearable
      >
        <template #prepend-inner>
          <Search :size="18" />
        </template>
      </v-text-field>
    </div>

    <v-card class="table-card" variant="outlined">
      <v-data-table
        :headers="headers"
        :items="students"
        :search="search"
        item-value="id"
        :items-per-page="-1"
        hide-default-footer
        no-data-text="No hay estudiantes en este grupo."
      >
        <template #[`header.present`]="{ column }">
          <span class="text-success font-weight-bold">{{ column.title }}</span>
        </template>
        <template #[`header.late`]="{ column }">
          <span class="text-warning font-weight-bold">{{ column.title }}</span>
        </template>
        <template #[`header.absent`]="{ column }">
          <span class="text-error font-weight-bold">{{ column.title }}</span>
        </template>
        <template #[`item.percentage`]="{ item }">
          <span class="font-weight-bold" :class="percentageClass(item.percentage)">
            {{ item.percentage }}%
          </span>
        </template>
      </v-data-table>
    </v-card>

    <div class="summary-row">
      <v-card class="average-card" variant="outlined">
        <span class="average-label">Promedio del grupo:</span>
        <span class="average-value" :class="percentageClass(groupAverage)">
          {{ groupAverage }}%
        </span>
      </v-card>

      <div class="actions">
        <v-btn color="primary" rounded="pill" size="large" @click="goToNewAttendance">
          Nueva asistencia <Plus :size="18" class="ms-1" />
        </v-btn>
        <v-btn variant="tonal" rounded="pill" disabled>
          <Download :size="18" class="me-2" /> Exportar reporte (próximamente)
        </v-btn>
      </div>
    </div>

    <p class="note">
      La tarde cuenta como media falta en el porcentaje. Asistencia menor a {{ MIN_PERCENTAGE }}% se
      marca en rojo.
    </p>
  </AppLayout>
</template>

<style scoped>
.filters {
  display: grid;
  grid-template-columns: minmax(220px, 320px) minmax(220px, 1fr);
  gap: 1rem;
  margin-top: 1.75rem;
}

.table-card {
  margin-top: 1.25rem;
  border: 2px solid rgba(var(--v-theme-primary), 0.6);
  border-radius: var(--radius);
  overflow: hidden;
}

.table-card :deep(th) {
  font-weight: 700 !important;
  background: rgba(var(--v-theme-primary), 0.06);
}

.summary-row {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-top: 1.5rem;
}

.average-card {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  padding: 1rem 1.5rem;
  border: 2px solid rgba(var(--v-theme-primary), 0.6);
  border-radius: var(--radius);
}

.average-label {
  font-weight: 700;
}

.average-value {
  font-size: 1.6rem;
  font-weight: 800;
}

.actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.note {
  margin-top: 1rem;
  font-size: 0.85rem;
  color: rgb(var(--v-theme-muted));
}

@media (max-width: 700px) {
  .filters {
    grid-template-columns: 1fr;
  }

  .average-card,
  .actions,
  .actions .v-btn {
    width: 100%;
  }
}
</style>