<script setup>
import { ref } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useStore } from 'vuex'
import { Eye, EyeOff, LogIn } from 'lucide-vue-next'
import AuthLayout from '@/layouts/AuthLayout.vue'

const router = useRouter()
const store = useStore()

const identifier = ref('')
const password = ref('')
const showPassword = ref(false)
const error = ref('')

function handleSubmit() {
  error.value = ''

  if (!identifier.value.trim() || !password.value) {
    error.value = 'Completa tu correo o carné y tu contraseña.'
    return
  }

  // Inicio de sesión simulado: esta versión no tiene backend
  store.commit('SET_USER', { name: identifier.value.trim(), role: 'Docente' })
  router.push('/dashboard')
}
</script>

<template>
  <AuthLayout :images="['/images/auth/login-aula.jpg']">
    <div class="auth-form">
      <h1 class="auth-title">¡Bienvenido de nuevo!</h1>
      <p class="auth-subtitle">Ingresa tus credenciales para continuar.</p>

      <v-alert v-if="error" type="error" variant="tonal" density="compact" class="mb-4">
        {{ error }}
      </v-alert>

      <form novalidate @submit.prevent="handleSubmit">
        <v-text-field
          v-model="identifier"
          label="Correo electrónico o carné"
          placeholder="Ej. ejemplo@gmail.com"
          variant="outlined"
          color="primary"
          autocomplete="username"
          class="mb-3"
        />

        <v-text-field
          v-model="password"
          :type="showPassword ? 'text' : 'password'"
          label="Contraseña"
          variant="outlined"
          color="primary"
          autocomplete="current-password"
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

        <v-btn type="submit" color="primary" rounded="pill" size="large" block class="mt-2">
          <LogIn :size="20" class="me-2" /> Iniciar sesión
        </v-btn>
      </form>

      <div class="auth-links">
        <v-btn variant="text" color="primary" size="small">¿Olvidaste tu contraseña?</v-btn>
        <p>
          ¿No tienes cuenta?
          <RouterLink to="/solicitar-acceso" class="link">Solicita acceso</RouterLink>
        </p>
      </div>
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
  margin-bottom: 2rem;
}

.eye-btn {
  display: flex;
  align-items: center;
  color: rgb(var(--v-theme-muted));
  cursor: pointer;
}

.auth-links {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
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
