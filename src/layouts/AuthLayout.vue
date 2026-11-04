<script setup>
import { ArrowLeft } from 'lucide-vue-next'
import BrandLogo from '@/components/BrandLogo.vue'
import ThemeToggle from '@/components/ThemeToggle.vue'
import AppFooter from '@/layouts/AppFooter.vue'

// Si se pasan imágenes, aparecen en una columna a la izquierda (escritorio)
defineProps({
  images: { type: Array, default: () => [] },
})
</script>

<template>
  <div class="auth-page">
    <header class="auth-topbar">
      <v-btn icon variant="flat" color="primary" size="small" to="/" aria-label="Volver al inicio">
        <ArrowLeft :size="18" />
      </v-btn>

      <RouterLink to="/">
        <BrandLogo :height="40" />
      </RouterLink>

      <ThemeToggle />
    </header>

    <main class="auth-main" :class="{ 'has-media': images.length }">
      <aside v-if="images.length" class="auth-media">
        <img v-for="src in images" :key="src" :src="src" alt="" />
      </aside>

      <section class="auth-content">
        <div class="auth-card">
          <slot />
        </div>
      </section>
    </main>

    <AppFooter />
  </div>
</template>

<style scoped>
.auth-page {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.auth-topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 1rem 4vw;
}

.auth-main {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 1.5rem 4vw 3rem;
}

.auth-main.has-media {
  display: grid;
  grid-template-columns: 1fr 1fr;
  align-items: stretch;
  padding: 0;
}

.auth-media {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
  padding: 2rem;
  background: rgba(var(--v-theme-on-surface), 0.06);
}

.auth-media img {
  width: 100%;
  height: auto;
  border-radius: var(--radius);
  object-fit: cover;
}

.auth-content {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem 4vw;
}

.auth-card {
  width: 100%;
  max-width: 420px;
}

@media (max-width: 800px) {
  .auth-main.has-media {
    grid-template-columns: 1fr;
  }

  .auth-media {
    display: none;
  }
}
</style>
