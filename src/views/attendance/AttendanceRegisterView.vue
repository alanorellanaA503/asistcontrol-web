<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import { ArrowLeft, History, Save } from 'lucide-vue-next'
import AppLayout from '@/layouts/AppLayout.vue'
import { getGroupAttendance, getAttendances, saveAttendance } from '@/services/attendance'
import { getGroups } from '@/services/groups'

const route = useRoute()
const groups = ref([])
const selectedGroupId = ref(null)
const attendanceDate = ref(getToday())
const students = ref([])
const isLoading = ref(false)
const loadError = ref('')
const snackbar = ref({ show: false, text: '', color: 'success' })

const statuses = [
  { title: 'Presente', value: 'present', color: 'success' },
  { title: 'Tarde', value: 'late', color: 'warning' },
  { title: 'Ausente', value: 'absent', color: 'error' },
]

const headers = [
  { title: 'Estudiante', key: 'name' },
  { title: 'Carné', key: 'code' },
  { title: 'Estado', key: 'status', width: 200 },
]

const selectedGroup = computed(() => groups.value.find((group) => group.id === selectedGroupId.value))
const canSave = computed(() => Boolean(selectedGroup.value && attendanceDate.value && students.value.length))

function getToday() {
  const today = new Date()
  const month = String(today.getMonth() + 1).padStart(2, '0')
  const day = String(today.getDate()).padStart(2, '0')
  return `${today.getFullYear()}-${month}-${day}`
}

function showError(error) {
  loadError.value = error instanceof Error ? error.message : String(error)
}

async function loadStudents() {
  if (!selectedGroupId.value || !attendanceDate.value) return

  isLoading.value = true
  loadError.value = ''
  students.value = []

  try {
    const [{ students: groupStudents }, attendances] = await Promise.all([
      getGroupAttendance(selectedGroupId.value),
      Promise.resolve(getAttendances()),
    ])
    const existing = attendances.find(
      (record) => record.groupId === selectedGroupId.value && record.date === attendanceDate.value,
    )

    students.value = groupStudents.map((student) => {
      const savedStudent = existing?.students.find((saved) => saved.id === student.id)
      return { ...student, status: savedStudent?.status ?? 'present' }
    })
  } catch (error) {
    showError(error)
  } finally {
    isLoading.value = false
  }
}

watch([selectedGroupId, attendanceDate], () => {
  loadStudents()
})

onMounted(async () => {
  try {
    groups.value = await getGroups()
    const requestedGroup = Number(route.query.grupo)
    selectedGroupId.value = groups.value.some((group) => group.id === requestedGroup)
      ? requestedGroup
      : groups.value[0]?.id ?? null
  } catch (error) {
    showError(error)
  }
})

function submitAttendance() {
  if (!canSave.value) return

  try {
    saveAttendance({
      groupId: selectedGroup.value.id,
      group: selectedGroup.value.group,
      subject: selectedGroup.value.subject,
      date: attendanceDate.value,
      students: students.value.map(({ id, name, code, status }) => ({ id, name, code, status })),
    })
    snackbar.value = { show: true, text: 'La asistencia se guardó correctamente.', color: 'success' }
  } catch (error) {
    snackbar.value = {
      show: true,
      text: error instanceof Error ? error.message : String(error),
      color: 'error',
    }
  }
}
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
            v-model="item.status"
            :items="statuses"
            item-title="title"
            item-value="value"
            variant="outlined"
            density="compact"
            hide-details
            :aria-label="`Estado de ${item.name}`"
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
      <v-btn color="primary" rounded="pill" :disabled="!canSave || isLoading" @click="submitAttendance">
        <Save :size="18" class="me-2" /> Guardar asistencia
      </v-btn>
    </div>

    <v-snackbar v-model="snackbar.show" :color="snackbar.color" timeout="3500" location="bottom">
      {{ snackbar.text }}
    </v-snackbar>
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
