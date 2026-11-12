<script setup>
import { computed } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { useStore } from 'vuex'
import { House, ClipboardCheck, History, Users, UserRound, LogOut } from 'lucide-vue-next'
import BrandLogo from '@/components/BrandLogo.vue'
import ThemeToggle from '@/components/ThemeToggle.vue'
import AppFooter from '@/layouts/AppFooter.vue'

const router = useRouter()
const store = useStore()

// La notificación se cierra sola y limpia el store al terminar
const noticeOpen = computed({
  get: () => store.state.notice !== null,
  set: (open) => {
    if (!open) store.commit('CLEAR_NOTICE')
  },
})

const links = [
  { to: '/dashboard', label: 'Inicio', icon: House },
  { to: '/asistencia', label: 'Asistencia', icon: ClipboardCheck },
  { to: '/asistencias', label: 'Historial', icon: History },
  { to: '/estudiantes', label: 'Estudiantes', icon: Users },
  { to: '/perfil', label: 'Perfil', icon: UserRound },
]

function logout() {
  store.commit('LOGOUT')
  router.push('/login')
}
</script>

<template>
  <div class="app-page">
    <header class="app-header">
      <RouterLink to="/dashboard" class="brand" aria-label="Ir al inicio">
        <BrandLogo :height="36" />
      </RouterLink>

      <nav class="app-nav" aria-label="Navegación principal">
        <RouterLink
          v-for="item in links"
          :key="item.to"
          :to="item.to"
          class="nav-link"
          active-class="nav-active"
          :aria-label="item.label"
        >
          <component :is="item.icon" :size="20" />
          <span class="nav-label">{{ item.label }}</span>
        </RouterLink>
      </nav>

      <div class="header-actions">
        <ThemeToggle />
        <v-btn
          variant="outlined"
          color="secondary"
          rounded="pill"
          aria-label="Cerrar sesión"
          @click="logout"
        >
          <LogOut :size="18" />
          <span class="nav-label ms-2">Cerrar sesión</span>
        </v-btn>
      </div>
    </header>

    <main class="app-main">
      <slot />
    </main>

    <AppFooter />

    <v-snackbar
      v-model="noticeOpen"
      :color="store.state.notice?.color"
      :timeout="3500"
      location="bottom"
    >
      {{ store.state.notice?.text }}
    </v-snackbar>
  </div>
</template>

<style scoped>
.app-page {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

/* Navbar sticky: queda fija arriba al hacer scroll */
.app-header {
  position: sticky;
  top: 0;
  z-index: 100;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.75rem 1rem;
  padding: 0.75rem 4vw;
  background: rgb(var(--v-theme-background));
  border-bottom: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.app-nav {
  display: flex;
  gap: 0.5rem;
}

.nav-link {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.5rem 0.9rem;
  border-radius: 999px;
  font-weight: 600;
  color: rgb(var(--v-theme-on-surface));
  transition: background 0.2s ease;
}

.nav-link:hover {
  background: rgba(var(--v-theme-primary), 0.1);
}

.nav-active,
.nav-active:hover {
  background: rgb(var(--v-theme-primary));
  color: #ffffff;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.app-main {
  flex: 1;
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 2.5rem 4vw 3rem;
}

/* Móvil: la navbar pasa a una segunda fila con solo iconos */
@media (max-width: 800px) {
  .app-header {
    padding: 0.6rem 4vw;
  }

  .brand :deep(img) {
    height: 30px !important;
  }

  .app-nav {
    order: 3;
    width: 100%;
    justify-content: space-around;
  }

  .nav-link {
    flex: 1;
    justify-content: center;
    padding: 0.5rem;
  }

  .nav-label {
    display: none;
  }

  .app-main {
    padding: 1.5rem 4vw 2rem;
  }
}
</style>
