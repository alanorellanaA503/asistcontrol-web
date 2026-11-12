<script setup>
import { ref, computed, watch } from 'vue'
import { useStore } from 'vuex'
import { UserRound, Pencil, Save, X, LogOut } from 'lucide-vue-next'
import { useRouter } from 'vue-router'
import AppLayout from '@/layouts/AppLayout.vue'

const store = useStore()
const router = useRouter()

// Campos del perfil. Se copian desde el store para poder cancelar cambios.
const emptyForm = () => ({
  name: store.state.user?.name ?? '',
  role: store.state.user?.role ?? '',
  email: store.state.user?.email ?? '',
  institutionalId: store.state.user?.institutionalId ?? '',
})

const form = ref(emptyForm())
const editing = ref(false)
const error = ref('')
const saved = ref(false)

const roles = ['Docente', 'Estudiante']
const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/

const initials = computed(
  () =>
    form.value.name
      .split(' ')
      .filter(Boolean)
      .slice(0, 2)
      .map((word) => word[0].toUpperCase())
      .join('') || '?',
)

// Si cambia el usuario en el store (por ejemplo, al iniciar sesión), refrescamos el formulario
watch(
  () => store.state.user,
  () => {
    if (!editing.value) form.value = emptyForm()
  },
)

function startEdit() {
  error.value = ''
  saved.value = false
  form.value = emptyForm()
  editing.value = true
}

function cancelEdit() {
  error.value = ''
  form.value = emptyForm()
  editing.value = false
}

function saveProfile() {
  error.value = ''
  saved.value = false

  const f = form.value
  if (!f.name.trim()) {
    error.value = 'El nombre no puede estar vacío.'
    return
  }
  if (f.email && !emailPattern.test(f.email)) {
    error.value = 'Ingresa un correo electrónico válido.'
    return
  }

  // Actualiza la sesión; el store la guarda en localStorage automáticamente
  store.commit('SET_USER', {
    ...store.state.user,
    name: f.name.trim(),
    role: f.role,
    email: f.email.trim(),
    institutionalId: f.institutionalId.trim(),
  })

  editing.value = false
  saved.value = true
}

function logout() {
  store.commit('LOGOUT')
  router.push('/login')
}
</script>

<template>
  <AppLayout>
    <h1 class="page-title">Mi perfil</h1>
    <p class="page-subtitle">Información de su cuenta.</p>

    <v-alert v-if="saved" type="success" variant="tonal" density="compact" class="mt-6">
      Perfil actualizado correctamente.
    </v-alert>

    <v-alert v-if="error" type="error" variant="tonal" density="compact" class="mt-6">
      {{ error }}
    </v-alert>

    <div class="profile-card">
      <div class="profile-header">
        <div class="avatar">{{ initials }}</div>
        <div>
          <p class="header-name">{{ store.state.user?.name ?? '—' }}</p>
          <p class="header-role">{{ store.state.user?.role ?? '—' }}</p>
        </div>
      </div>

      <form v-if="editing" novalidate class="profile-form" @submit.prevent="saveProfile">
        <v-text-field
          v-model="form.name"
          label="Nombre completo"
          variant="outlined"
          color="primary"
          autocomplete="name"
        />

        <v-text-field
          v-model="form.institutionalId"
          label="Carné o código institucional"
          variant="outlined"
          color="primary"
        />

        <v-text-field
          v-model="form.email"
          label="Correo electrónico"
          type="email"
          variant="outlined"
          color="primary"
          autocomplete="email"
        />

        <v-select
          v-model="form.role"
          :items="roles"
          label="Rol"
          variant="outlined"
          color="primary"
        />

        <div class="form-actions">
          <v-btn variant="outlined" color="secondary" rounded="pill" @click="cancelEdit">
            <X :size="18" class="me-2" /> Cancelar
          </v-btn>
          <v-btn type="submit" color="primary" rounded="pill">
            <Save :size="18" class="me-2" /> Guardar cambios
          </v-btn>
        </div>
      </form>

      <dl v-else class="profile-data">
        <div>
          <dt>Nombre completo</dt>
          <dd>{{ store.state.user?.name ?? '—' }}</dd>
        </div>
        <div>
          <dt>Carné o código institucional</dt>
          <dd>{{ store.state.user?.institutionalId || '—' }}</dd>
        </div>
        <div>
          <dt>Correo electrónico</dt>
          <dd>{{ store.state.user?.email || '—' }}</dd>
        </div>
        <div>
          <dt>Rol asignado</dt>
          <dd>{{ store.state.user?.role ?? '—' }}</dd>
        </div>

        <div class="profile-actions">
          <v-btn color="primary" rounded="pill" @click="startEdit">
            <Pencil :size="18" class="me-2" /> Editar perfil
          </v-btn>
          <v-btn variant="outlined" color="secondary" rounded="pill" @click="logout">
            <LogOut :size="18" class="me-2" /> Cerrar sesión
          </v-btn>
        </div>
      </dl>
    </div>
  </AppLayout>
</template>

<style scoped>
/* CONTENEDOR PRINCIPAL */
.profile-card {
  /* Ocupa el 100% del ancho disponible, pero con un máximo de 900px 
     para que no se vea desproporcionado en pantallas muy grandes */
  width: 100%;
  max-width: 900px;
  margin-top: 2rem;
  padding: 2rem;
  background: rgb(var(--v-theme-surface));
  border: 1px solid rgba(var(--v-theme-on-surface), 0.1);
  border-radius: var(--radius);
  /* Asegura que no se desborde de su contenedor padre */
  box-sizing: border-box;
}

.profile-header {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  padding-bottom: 1.5rem;
  margin-bottom: 1.5rem;
  border-bottom: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.avatar {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background: rgba(var(--v-theme-primary), 0.14);
  color: rgb(var(--v-theme-primary));
  font-size: 1.5rem;
  font-weight: 800;
}

.header-name {
  font-size: 1.2rem;
  font-weight: 700;
}

.header-role {
  color: rgb(var(--v-theme-muted));
}

/* DATOS DEL PERFIL (GRID RESPONSIVO) */
.profile-data {
  display: grid;
  gap: 1.5rem;
  /* Por defecto en móviles es 1 columna. 
     En pantallas de 600px o más, se dividen en 2 columnas */
  grid-template-columns: 1fr;
}

/* Ajuste específico para pantallas más grandes */
@media (min-width: 600px) {
  .profile-data {
    grid-template-columns: repeat(2, 1fr);
  }

  /* Hacemos que los botones de acción ocupen todo el ancho de la fila del grid */
  .profile-actions {
    grid-column: 1 / -1;
  }
}

.profile-data dt {
  color: rgb(var(--v-theme-muted));
  font-size: 0.9rem;
  margin-bottom: 0.25rem;
}

.profile-data dd {
  font-size: 1.05rem;
  font-weight: 600;
  margin: 0;
}

/* FORMULARIO (También en 2 columnas en escritorio) */
.profile-form {
  display: grid;
  gap: 1rem;
  grid-template-columns: 1fr;
}

@media (min-width: 600px) {
  .profile-form {
    grid-template-columns: repeat(2, 1fr);
  }

  .form-actions {
    grid-column: 1 / -1;
  }
}

/* ACCIONES Y BOTONES */
.profile-actions,
.form-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  margin-top: 1rem;
}

/* RESPONSIVE PARA MÓVILES (Ajustes finales) */
@media (max-width: 600px) {
  .profile-card {
    padding: 1.25rem;
    /* Opcional: si quieres que en móvil ocupe el 100% del padding de la app */
    width: 100%;
  }

  .profile-actions .v-btn,
  .form-actions .v-btn {
    flex: 1; /* Hace que los botones se estiren y ocupen el mismo ancho */
    min-width: 140px; /* Evita que se hagan demasiado pequeños */
  }
}
</style>
