<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'
import { LogOut } from 'lucide-vue-next'
import BrandLogo from '@/components/BrandLogo.vue'
import ThemeToggle from '@/components/ThemeToggle.vue'
import AppFooter from '@/layouts/AppFooter.vue'

const router = useRouter()
const store = useStore()

const userName = computed(() => store.state.user?.name ?? 'docente')

function logout() {
  store.commit('LOGOUT')
  router.push('/login')
}
</script>

<template>
  <div class="dashboard-page">
    <header class="dash-topbar">
      <BrandLogo :height="40" />
      <div class="dash-actions">
        <ThemeToggle />
        <v-btn variant="outlined" color="secondary" rounded="pill" @click="logout">
          <LogOut :size="18" class="me-2" /> Cerrar sesión
        </v-btn>
      </div>
    </header>

    <main class="dash-main">
      <h1>¡Bienvenido, {{ userName }}!</h1>
      <p>Panel en construcción. Aquí se integrarán los grupos, la asistencia y el perfil.</p>
    </main>

    <AppFooter />
  </div>
</template>

<style scoped>
.dashboard-page {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.dash-topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
  padding: 1rem 4vw;
}

.dash-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.dash-main {
  flex: 1;
  padding: 3rem 4vw;
}

.dash-main h1 {
  font-size: clamp(1.8rem, 4vw, 2.5rem);
  font-weight: 800;
  margin-bottom: 0.5rem;
}
</style>
