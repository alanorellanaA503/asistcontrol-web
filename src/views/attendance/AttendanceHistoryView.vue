<script setup>
import { computed, onMounted, ref } from 'vue'
import { ClipboardPlus, Eye, Search } from 'lucide-vue-next'
import AppLayout from '@/layouts/AppLayout.vue'
import { getAttendances } from '@/services/attendance'
import { getGroups } from '@/services/groups'

const groups = ref([])
const attendances = ref([])
const selectedGroupId = ref('all')
const search = ref('')
const selectedAttendance = ref(null)
const detailsOpen = ref(false)
const loadError = ref('')

const headers = [
  { title: 'Fecha', key: 'date', minWidth: 130 },
  { title: 'Materia', key: 'subject' },
  { title: 'Grupo', key: 'group' },
  { title: 'Presentes', key: 'presentCount', align: 'center' },
  { title: 'Tardanzas', key: 'lateCount', align: 'center' },
  { title: 'Ausentes', key: 'absentCount', align: 'center' },
  { title: 'Detalle', key: 'actions', align: 'center', sortable: false },
]

const groupFilterItems = computed(() => [
  { id: 'all', label: 'Todos los grupos' },
  ...groups.value.map((group) => ({
    id: group.id,
    label: `${group.subject} · ${group.group}`,
  })),
])

const attendanceRows = computed(() =>
  attendances.value.map((attendance) => ({
    ...attendance,
    recordKey: `${attendance.groupId}:${attendance.date}`,
    presentCount: attendance.students.filter((student) => student.status === 'present').length,
    lateCount: attendance.students.filter((student) => student.status === 'late').length,
    absentCount: attendance.students.filter((student) => student.status === 'absent').length,
  })),
)

const filteredAttendances = computed(() => {
  const query = search.value.trim().toLocaleLowerCase('es')

  return attendanceRows.value.filter((attendance) => {
    const matchesGroup =
      selectedGroupId.value === 'all' || attendance.groupId === selectedGroupId.value
    const searchableText = `${attendance.subject} ${attendance.group} ${attendance.date}`
      .toLocaleLowerCase('es')
    return matchesGroup && searchableText.includes(query)
  })
})

const detailHeaders = [
  { title: 'Estudiante', key: 'name' },
  { title: 'Carné', key: 'code' },
  { title: 'Estado', key: 'status' },
]

function formatDate(date) {
  return new Date(`${date}T00:00:00`).toLocaleDateString('es')
}

function statusLabel(status) {
  return { present: 'Presente', late: 'Tarde', absent: 'Ausente' }[status] ?? status
}

function statusColor(status) {
  return { present: 'success', late: 'warning', absent: 'error' }[status]
}

function openDetails(attendance) {
  selectedAttendance.value = attendance
  detailsOpen.value = true
}

function closeDetails() {
  detailsOpen.value = false
  selectedAttendance.value = null
}

onMounted(async () => {
  try {
    const [availableGroups, savedAttendances] = await Promise.all([
      getGroups(),
      Promise.resolve(getAttendances()),
    ])
    groups.value = availableGroups
    attendances.value = savedAttendances
  } catch (error) {
    loadError.value = error instanceof Error ? error.message : String(error)
  }
})
</script>

<template>
  <AppLayout>
    <h1 class="page-title">Asistencias guardadas</h1>
    <p class="page-subtitle">Consulta los registros y el detalle de cada estudiante.</p>

    <v-alert v-if="loadError" class="load-error" type="error" variant="tonal">
      No se pudo cargar el historial: {{ loadError }}
    </v-alert>

    <template v-else-if="attendances.length">
      <div class="filters">
        <v-select
          v-model="selectedGroupId"
          :items="groupFilterItems"
          item-title="label"
          item-value="id"
          label="Filtrar por grupo"
          variant="outlined"
          color="primary"
          density="comfortable"
          hide-details
        />
        <v-text-field
          v-model="search"
          label="Buscar materia, grupo o fecha"
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
          :items="filteredAttendances"
          item-value="recordKey"
          :items-per-page="10"
          no-data-text="No hay asistencias que coincidan con los filtros."
        >
          <template #[`item.date`]="{ item }">
            {{ formatDate(item.date) }}
          </template>
          <template #[`item.presentCount`]="{ item }">
            <span class="text-success font-weight-bold">{{ item.presentCount }}</span>
          </template>
          <template #[`item.lateCount`]="{ item }">
            <span class="text-warning font-weight-bold">{{ item.lateCount }}</span>
          </template>
          <template #[`item.absentCount`]="{ item }">
            <span class="text-error font-weight-bold">{{ item.absentCount }}</span>
          </template>
          <template #[`item.actions`]="{ item }">
            <v-btn
              variant="tonal"
              color="primary"
              size="small"
              rounded="pill"
              :aria-label="`Ver asistencia del ${formatDate(item.date)} para ${item.group}`"
              @click="openDetails(item)"
            >
              <Eye :size="16" class="me-1" /> Ver detalle
            </v-btn>
          </template>
        </v-data-table>
      </v-card>
    </template>

    <v-card v-else class="empty-state" variant="outlined">
      <ClipboardPlus :size="36" class="empty-icon" />
      <h2>Aún no hay asistencias guardadas</h2>
      <p>Registra una asistencia para que aparezca en este historial.</p>
      <v-btn :to="{ name: 'register-attendance' }" color="primary" rounded="pill">
        Nueva asistencia
      </v-btn>
    </v-card>

    <v-dialog v-model="detailsOpen" max-width="720" @after-leave="closeDetails">
      <v-card v-if="selectedAttendance" class="detail-card">
        <v-card-title class="detail-title">
          <div>
            <h2>{{ selectedAttendance.subject }}</h2>
            <p>{{ selectedAttendance.group }} · {{ formatDate(selectedAttendance.date) }}</p>
          </div>
          <v-btn icon="mdi-close" variant="text" aria-label="Cerrar detalle" @click="closeDetails" />
        </v-card-title>
        <v-card-text>
          <v-data-table
            :headers="detailHeaders"
            :items="selectedAttendance.students"
            item-value="id"
            :items-per-page="-1"
            hide-default-footer
          >
            <template #[`item.status`]="{ item }">
              <v-chip :color="statusColor(item.status)" size="small" variant="tonal">
                {{ statusLabel(item.status) }}
              </v-chip>
            </template>
          </v-data-table>
        </v-card-text>
      </v-card>
    </v-dialog>
  </AppLayout>
</template>

<style scoped>
.load-error {
  margin-top: 1.25rem;
}

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

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
  margin-top: 1.75rem;
  padding: 3rem 1.5rem;
  text-align: center;
  border: 1px dashed rgba(var(--v-theme-on-surface), 0.2);
  border-radius: var(--radius);
}

.empty-icon {
  color: rgb(var(--v-theme-primary));
}

.empty-state h2 {
  font-size: 1.25rem;
  font-weight: 700;
}

.empty-state p,
.detail-title p {
  color: rgb(var(--v-theme-muted));
}

.detail-title {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  white-space: normal;
}

.detail-title h2 {
  font-size: 1.25rem;
  font-weight: 700;
}

@media (max-width: 700px) {
  .filters {
    grid-template-columns: 1fr;
  }
}
</style>
