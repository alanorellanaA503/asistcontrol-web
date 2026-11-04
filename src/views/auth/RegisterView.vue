<script setup>
import { ref, computed } from 'vue'
import { RouterLink } from 'vue-router'
import { Eye, EyeOff, UserPlus } from 'lucide-vue-next'
import AuthLayout from '@/layouts/AuthLayout.vue'

const form = ref({
  fullName: '',
  carne: '',
  email: '',
  rol: null,
  password: '',
  confirm: '',
})

const showPassword = ref(false)
const error = ref('')
const sent = ref(false)

const roles = ['Estudiante', 'Docente']
const slides = [
  {
    src: '/images/auth/register-1.jpg',
    title: 'Registro diario',
    text: 'Marca presente, tarde o ausente en segundos desde la lista del grupo.',
  },
  {
    src: '/images/auth/register-2.jpg',
    title: 'Porcentajes automáticos',
    text: 'Consulta la asistencia de cada estudiante y del grupo sin hacer cuentas.',
  },
  {
    src: '/images/auth/register-3.jpg',
    title: 'Participación en clase',
    text: 'Lleva el control de todos tus grupos y estudiantes desde un solo lugar.',
  },
  {
    src: '/images/auth/register-4.jpg',
    title: 'Trabajo desde cualquier lugar',
    text: 'Usa AsistControl desde la computadora, la tableta o el celular.',
  },
  {
    src: '/images/auth/register-5.jpg',
    title: 'Historial claro',
    text: 'Revisa el historial de asistencia por fecha, grupo y materia.',
  },
  {
    src: '/images/auth/register-6.jpg',
    title: 'Equipo organizado',
    text: 'Grupos, estudiantes y asistencias listos para cada clase.',
  },
]
const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/

const passwordsMatch = computed(() => form.value.password === form.value.confirm)

function handleSubmit() {
  error.value = ''
  sent.value = false

  const f = form.value
  if (
    !f.fullName.trim() ||
    !f.carne.trim() ||
    !f.email.trim() ||
    !f.rol ||
    !f.password ||
    !f.confirm
  ) {
    error.value = 'Completa todos los campos.'
    return
  }
  if (!emailPattern.test(f.email)) {
    error.value = 'Ingresa un correo electrónico válido.'
    return
  }
  if (f.password.length < 8) {
    error.value = 'La contraseña debe tener al menos 8 caracteres.'
    return
  }
  if (!passwordsMatch.value) {
    error.value = 'Las contraseñas no coinciden.'
    return
  }

  // Solicitud simulada: esta versión no envía datos a ningún servidor
  sent.value = true
  form.value = { fullName: '', carne: '', email: '', rol: null, password: '', confirm: '' }
}
</script>

<template>
  <AuthLayout :slides="slides" align="top">
    <div class="auth-form">
      <h1 class="auth-title">Crear una cuenta</h1>
      <p class="auth-subtitle">Completa tus datos para solicitar acceso a AsistControl.</p>

      <v-alert v-if="error" type="error" variant="tonal" density="compact" class="mb-4">
        {{ error }}
      </v-alert>

      <v-alert v-if="sent" type="success" variant="tonal" density="compact" class="mb-4">
        Tu solicitud fue enviada. Te avisaremos cuando tu cuenta esté activa.
      </v-alert>

      <form novalidate @submit.prevent="handleSubmit">
        <v-text-field
          v-model="form.fullName"
          label="Nombre completo"
          placeholder="Ej. Carlos Hernández"
          variant="outlined"
          color="primary"
          autocomplete="name"
          class="mb-2"
        />

        <v-text-field
          v-model="form.carne"
          label="Carné o código institucional"
          placeholder="Ej. EST-001"
          variant="outlined"
          color="primary"
          class="mb-2"
        />

        <v-text-field
          v-model="form.email"
          label="Correo electrónico"
          placeholder="Ej. carlos@ejemplo.com"
          type="email"
          variant="outlined"
          color="primary"
          autocomplete="email"
          class="mb-2"
        />

        <v-select
          v-model="form.rol"
          :items="roles"
          label="Rol"
          variant="outlined"
          color="primary"
          class="mb-2"
        />

        <v-text-field
          v-model="form.password"
          :type="showPassword ? 'text' : 'password'"
          label="Contraseña"
          variant="outlined"
          color="primary"
          autocomplete="new-password"
          hint="Mínimo 8 caracteres"
          persistent-hint
          class="mb-2"
        >
          <template #append-inner>
            <button
              type="button"
              class="eye-btn"
              :aria-label="showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'"
              @click="showPassword = !showPassword"
            >
              <EyeOff v-if="showPassword" :size="18" />
              <Eye v-else :size="18" />
            </button>
          </template>
        </v-text-field>

        <v-text-field
          v-model="form.confirm"
          :type="showPassword ? 'text' : 'password'"
          label="Confirmar contraseña"
          variant="outlined"
          color="primary"
          autocomplete="new-password"
          class="mb-4"
        />

        <v-btn type="submit" color="primary" rounded="pill" size="large" block>
          <UserPlus :size="20" class="me-2" /> Solicitar registro
        </v-btn>
      </form>

      <p class="auth-links">
        ¿Ya tienes una cuenta?
        <RouterLink to="/login" class="link">Inicia sesión</RouterLink>
      </p>
    </div>
  </AuthLayout>
</template>

<style scoped>
.auth-title {
  font-size: clamp(1.9rem, 4vw, 2.6rem);
  font-weight: 800;
  line-height: 1.15;
  text-align: center;
  margin-bottom: 0.5rem;
}

.auth-subtitle {
  text-align: center;
  color: rgb(var(--v-theme-muted));
  margin-bottom: 1.5rem;
}

.eye-btn {
  display: flex;
  align-items: center;
  color: rgb(var(--v-theme-muted));
  cursor: pointer;
}

.auth-links {
  margin-top: 1.5rem;
  text-align: center;
  font-size: 0.95rem;
}

.link {
  color: rgb(var(--v-theme-primary));
  font-weight: 600;
  text-decoration: underline;
}
</style>
