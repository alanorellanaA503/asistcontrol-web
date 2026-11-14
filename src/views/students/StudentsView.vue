
<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useStore } from 'vuex'
import { useRoute, useRouter } from 'vue-router'
import { Search, Plus, Pencil, Trash2 } from 'lucide-vue-next'
import AppLayout from '@/layouts/AppLayout.vue'
import { getGroups } from '@/services/groups'
import {
  getAllStudents,
  saveStudent,
  deleteStudent,
} from '@/store/modules/students'

const store = useStore()
const route = useRoute()
const router = useRouter()

const groups = ref([])
const students = ref([])
const selectedGroupId = ref(null)

const search = ref('')
const viewMode = ref('grupo')
const loading = ref(false)
const pageError = ref('')

const dialogOpen = ref(false)
const editingId = ref(null)
const formError = ref('')
const deleteTarget = ref(null)

const form = ref({
  name: '',
  code: '',
  groupId: null,
})

const groupStudents = computed(() =>
  students.value.filter(
    (s) => s.groupId === Number(selectedGroupId.value)
  )
)

function getGroupName(groupId) {
  const group = groups.value.find(
    (g) => Number(g.id) === Number(groupId)
  )

  if (!group) return `Grupo ${groupId}`

  return `${group.subject} · ${group.group}`
}

const visibleStudents = computed(() => {
  const source = viewMode.value === 'guardados'
    ? students.value
    : groupStudents.value

  const filter = (search.value || '').trim().toLowerCase()

  return source
    .filter((student) => {
      if (!filter) return true

      return (
        student.name.toLowerCase().includes(filter) ||
        student.code.toLowerCase().includes(filter)
      )
    })
    .map((student) => ({
      ...student,
      groupName: getGroupName(student.groupId),
    }))
})

const headers = computed(() => {
  const columns = [
    { title: 'Estudiante', key: 'name' },
    { title: 'Carné', key: 'code' },
  ]

  if (viewMode.value === 'guardados') {
    columns.push({ title: 'Grupo', key: 'groupName' })
  }

  columns.push({
    title: 'Acciones',
    key: 'actions',
    align: 'end',
    sortable: false,
  })

  return columns
})

const formTitle = computed(() =>
  editingId.value === null
    ? 'Agregar estudiante'
    : 'Editar estudiante'
)

async function loadStudents() {
  loading.value = true
  pageError.value = ''

  try {
    students.value = await getAllStudents()
  } catch (error) {
    pageError.value = error.message
  } finally {
    loading.value = false
  }
}

function openCreate() {
  editingId.value = null
  formError.value = ''

  form.value = {
    name: '',
    code: '',
    groupId: selectedGroupId.value,
  }

  dialogOpen.value = true
}

function openEdit(student) {
  editingId.value = student.id
  formError.value = ''

  form.value = {
    name: student.name,
    code: student.code,
    groupId: student.groupId,
  }

  dialogOpen.value = true
}

async function submitForm() {
  formError.value = ''

  const name = form.value.name.trim()
  const code = form.value.code.trim().toUpperCase()
  const groupId = Number(form.value.groupId)

  if (!name || !code) {
    formError.value = 'Completa el nombre y el carné.'
    return
  }

  if (!groups.value.some((g) => Number(g.id) === groupId)) {
    formError.value = 'Selecciona un grupo válido.'
    return
  }

  const isEditing = editingId.value !== null

  try {
    await saveStudent({
      id: editingId.value,
      name,
      code,
      groupId,
    })

    await loadStudents()

    selectedGroupId.value = groupId
    search.value = ''
    dialogOpen.value = false

    store.commit('SHOW_NOTICE', {
      text: isEditing
        ? 'Estudiante actualizado correctamente.'
        : 'Estudiante agregado correctamente.',
      color: 'success',
    })
  } catch (error) {
    formError.value = error.message
  }
}

async function confirmDelete() {
  if (!deleteTarget.value) return

  try {
    await deleteStudent(deleteTarget.value.id)

    deleteTarget.value = null

    await loadStudents()

    store.commit('SHOW_NOTICE', {
      text: 'Estudiante eliminado correctamente.',
      color: 'success',
    })
  } catch (error) {
    store.commit('SHOW_NOTICE', {
      text: error.message,
      color: 'error',
    })
  }
}

watch(selectedGroupId, (id) => {
  search.value = ''

  if (id != null && viewMode.value === 'grupo') {
    router.replace({
      name: 'students',
      query: { grupo: id },
    })
  }
})

watch(viewMode, () => {
  search.value = ''
})

onMounted(async () => {
  try {
    groups.value = await getGroups()

    const fromUrl = Number(route.query.grupo)

    selectedGroupId.value = groups.value.some(
      (g) => Number(g.id) === fromUrl
    )
      ? fromUrl
      : (groups.value[0]?.id ?? null)

    await loadStudents()
  } catch (error) {
    pageError.value = error.message
  }
})
</script>

<template>
  <AppLayout>
    <h1 class="page-title">Estudiantes</h1>

    <p class="page-subtitle">
      Gestión de estudiantes por grupo.
    </p>

    <v-alert
      v-if="pageError"
      type="error"
      variant="tonal"
      class="mt-4"
    >
      {{ pageError }}
    </v-alert>

    <div class="toolbar">
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
        label="Buscar por nombre o carné"
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

      <v-btn
        color="primary"
        rounded="pill"
        :disabled="groups.length === 0"
        @click="openCreate"
      >
        <Plus :size="18" class="me-2" />
        Agregar estudiante
      </v-btn>
    </div>

    <div class="view-options">
      <v-btn-toggle
        v-model="viewMode"
        color="primary"
        variant="outlined"
        mandatory
        divided
      >
        <v-btn value="grupo">
          Estudiantes del grupo ({{ groupStudents.length }})
        </v-btn>

        <v-btn value="guardados">
          Ver guardados ({{ students.length }})
        </v-btn>
      </v-btn-toggle>
    </div>

    <v-card class="table-card" variant="outlined">
      <v-data-table
        :headers="headers"
        :items="visibleStudents"
        :loading="loading"
        item-value="id"
        :items-per-page="-1"
        hide-default-footer
        no-data-text="No hay estudiantes para mostrar."
      >
        <template #[`item.actions`]="{ item }">
          <div class="row-actions">
            <v-btn
              icon
              variant="text"
              color="primary"
              size="small"
              :aria-label="`Editar a ${item.name}`"
              @click="openEdit(item)"
            >
              <Pencil :size="18" />
            </v-btn>

            <v-btn
              icon
              variant="text"
              color="error"
              size="small"
              :aria-label="`Eliminar a ${item.name}`"
              @click="deleteTarget = item"
            >
              <Trash2 :size="18" />
            </v-btn>
          </div>
        </template>
      </v-data-table>
    </v-card>

    <v-dialog v-model="dialogOpen" max-width="480">
      <v-card class="dialog-card">
        <v-card-title class="dialog-title">
          {{ formTitle }}
        </v-card-title>

        <v-card-text>
          <v-alert
            v-if="formError"
            type="error"
            variant="tonal"
            density="compact"
            class="mb-4"
          >
            {{ formError }}
          </v-alert>

          <v-text-field
            v-model="form.name"
            label="Nombre completo"
            variant="outlined"
            color="primary"
          />

          <v-text-field
            v-model="form.code"
            label="Carné"
            variant="outlined"
            color="primary"
          />

          <v-select
            v-model="form.groupId"
            :items="groups"
            :item-title="(g) => `${g.subject} · ${g.group}`"
            item-value="id"
            label="Grupo"
            variant="outlined"
            color="primary"
          />
        </v-card-text>

        <v-card-actions class="dialog-actions">
          <v-btn
            variant="outlined"
            color="secondary"
            rounded="pill"
            @click="dialogOpen = false"
          >
            Cancelar
          </v-btn>

          <v-btn
            color="primary"
            rounded="pill"
            @click="submitForm"
          >
            Guardar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog
      :model-value="deleteTarget !== null"
      max-width="420"
      @update:model-value="deleteTarget = null"
    >
      <v-card class="dialog-card">
        <v-card-title class="dialog-title">
          Eliminar estudiante
        </v-card-title>

        <v-card-text>
          ¿Deseas eliminar a
          <strong>{{ deleteTarget?.name }}</strong>?
          Esta acción no se puede deshacer.
        </v-card-text>

        <v-card-actions class="dialog-actions">
          <v-btn
            variant="outlined"
            color="secondary"
            rounded="pill"
            @click="deleteTarget = null"
          >
            Cancelar
          </v-btn>

          <v-btn
            color="error"
            rounded="pill"
            @click="confirmDelete"
          >
            Eliminar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </AppLayout>
</template>

<style scoped>
.toolbar {
  display: grid;
  grid-template-columns: minmax(200px, 300px) minmax(200px, 1fr) auto;
  align-items: center;
  gap: 1rem;
  margin-top: 1.75rem;
}

.view-options {
  margin-top: 1.5rem;
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
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

.row-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.25rem;
}

.dialog-card {
  border-radius: var(--radius);
  padding: 0.5rem;
}

.dialog-title {
  font-weight: 800;
  font-size: 1.25rem;
}

.dialog-actions {
  justify-content: flex-end;
  gap: 0.5rem;
}

@media (max-width: 800px) {
  .toolbar {
    grid-template-columns: 1fr;
  }

  .view-options :deep(.v-btn-toggle) {
    max-width: 100%;
    flex-wrap: wrap;
    height: auto;
  }
}
</style>
