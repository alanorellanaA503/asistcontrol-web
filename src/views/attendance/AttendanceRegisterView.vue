<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { useStore } from 'vuex'
import { ArrowLeft, History, Save } from 'lucide-vue-next'
import AppLayout from '@/layouts/AppLayout.vue'
import { getGroupAttendance, getAttendances, saveAttendance } from '@/services/attendance'
import { getGroups } from '@/services/groups'

const route = useRoute()
const router = useRouter()
const store = useStore()

const groups = ref([])
const selectedGroupId = ref(null)
const attendanceDate = ref(getToday())
const students = ref([])
const isLoading = ref(false)
const isSaving = ref(false)
const loadError = ref('')

// Evita que una respuesta lenta sobrescriba la última selección
let requestId = 0

const statuses = [
  { title: 'Presente', value: 'present' },
  { title: 'Tarde', value: 'late' },
  { title: 'Ausente', value: 'absent' },
]

const headers = [
  { title: 'Estudiante', key: 'name' },
  { title: 'Carné', key: 'code' },
  { title: 'Estado', key: 'status', width: 200 },
]

const selectedGroup = computed(() =>
  groups.value.find((group) => group.id === selectedGroupId.value),
)

const canSave = computed(
  () =>
    Boolean(selectedGroup.value && attendanceDate.value && students.value.length) &&
    !isSaving.value,
)

function getToday() {
  const today = new Date()
  const month = String(today.getMonth() + 1).padStart(2, '0')
  const day = String(today.getDate()).padStart(2, '0')
  return `${today.getFullYear()}-${month}-${day}`
}

function errorText(error) {
  return error instanceof Error ? error.message : String(error)
}

async function loadStudents() {
  if (!selectedGroupId.value || !attendanceDate.value) return

  const currentRequest = ++requestId
  isLoading.value = true
  loadError.value = ''
  students.value = []

  try {
    const [{ students: groupStudents }, attendances] = await Promise.all([
      getGroupAttendance(selectedGroupId.value),
      Promise.resolve(getAttendances()),
    ])
    if (currentRequest !== requestId) return

    // Si ya hay asistencia guardada para este grupo y fecha, muestra sus estados
    const existing = attendances.find(
      (record) => record.groupId === selectedGroupId.value && record.date === attendanceDate.value,
    )

    students.value = groupStudents.map((student) => {
      const saved = existing?.students.find((s) => s.id === student.id)
      return {
        id: student.id,
        name: student.name,
        code: student.code,
        status: saved?.status ?? 'present',
      }
    })
  } catch (error) {
    if (currentRequest === requestId) loadError.value = errorText(error)
  } finally {
    if (currentRequest === requestId) isLoading.value = false
  }
}

function setStatus(studentId, status) {
  const student = students.value.find((s) => s.id === studentId)
  if (student) student.status = status
}

function submitAttendance() {
  if (!canSave.value) return

  isSaving.value = true
  try {
    saveAttendance({
      groupId: selectedGroup.value.id,
      group: selectedGroup.value.group,
      subject: selectedGroup.value.subject,
      date: attendanceDate.value,
      students: students.value.map(({ id, name, code, status }) => ({ id, name, code, status })),
    })

    store.commit('SHOW_NOTICE', { text: 'Asistencia guardada correctamente.', color: 'success' })

    // Regresa al resumen del grupo, que ya muestra los datos guardados
    router.push({ name: 'attendance', query: { grupo: selectedGroup.value.id } })
  } catch (error) {
    store.commit('SHOW_NOTICE', { text: errorText(error), color: 'error' })
    isSaving.value = false
  }
}

watch([selectedGroupId, attendanceDate], loadStudents)

onMounted(async () => {
  try {
    groups.value = await getGroups()
    const requestedGroup = Number(route.query.grupo)
    selectedGroupId.value = groups.value.some((group) => group.id === requestedGroup)
      ? requestedGroup
      : (groups.value[0]?.id ?? null)
  } catch (error) {
    loadError.value = errorText(error)
  }
})
</script>

<template>
  <AppLayout>
    <div class="page-heading">
      <div>
        <h1 class="page-title">Nueva asistencia</h1>
        <p class="page-subtitle">Selecciona el grupo, la fecha y el estado de cada estudiante.</p>
      </div>
      <RouterLink class="history-link" :to="{ name: 'attendance-history' }">
        <History :size="18" /> Historial
      </RouterLink>
    </div>

    <v-alert v-if="loadError" class="load-error" type="error" variant="tonal">
      No se pudo cargar la asistencia: {{ loadError }}
    </v-alert>

    <div class="filters">
      <v-select
        v-model="selectedGroupId"
        :items="groups"
        :item-title="(group) => `${group.subject} · ${group.group}`"
        item-value="id"
        label="Grupo"
        variant="outlined"
        color="primary"
        density="comfortable"
        hide-details
      />
      <v-text-field
        v-model="attendanceDate"
        type="date"
        label="Fecha"
        variant="outlined"
        color="primary"
        density="comfortable"
        hide-details
      />
    </div>

    <p v-if="selectedGroup" class="group-subtitle">
      {{ selectedGroup.subject }} · {{ selectedGroup.group }}
    </p>

    <v-card class="table-card" variant="outlined">
      <v-data-table
        :headers="headers"
        :items="students"
        :loading="isLoading"
        item-value="id"
        :items-per-page="-1"
        hide-default-footer
        no-data-text="No hay estudiantes en este grupo."
      >
        <template #[`item.status`]="{ item }">
          <v-select
            :model-value="item.status"
            :items="statuses"
            item-title="title"
            item-value="value"
            variant="outlined"
            density="compact"
            hide-details
            :aria-label="`Estado de ${item.name}`"
            @update:model-value="setStatus(item.id, $event)"
          />
        </template>
      </v-data-table>
    </v-card>

    <div class="actions">
      <v-btn
        :to="{ name: 'attendance', query: { grupo: selectedGroupId } }"
        variant="outlined"
        color="secondary"
        rounded="pill"
      >
        <ArrowLeft :size="18" class="me-2" /> Volver al grupo
      </v-btn>
      <v-btn
        color="primary"
        rounded="pill"
        :disabled="!canSave"
        :loading="isSaving"
        @click="submitAttendance"
      >
        <Save :size="18" class="me-2" /> Guardar asistencia
      </v-btn>
    </div>
  </AppLayout>
</template>

<style scoped>
.page-heading {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
}

.history-link {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  color: rgb(var(--v-theme-primary));
  font-weight: 700;
}

.load-error {
  margin-top: 1.25rem;
}

.filters {
  display: grid;
  grid-template-columns: minmax(220px, 1fr) minmax(180px, 260px);
  gap: 1rem;
  margin-top: 1.75rem;
}

.group-subtitle {
  margin-top: 1rem;
  color: rgb(var(--v-theme-muted));
  font-weight: 600;
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

.actions {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.75rem;
  margin-top: 1.5rem;
}

@media (max-width: 700px) {
  .page-heading,
  .actions {
    flex-direction: column;
    align-items: stretch;
  }

  .filters {
    grid-template-columns: 1fr;
  }
}
</style>
