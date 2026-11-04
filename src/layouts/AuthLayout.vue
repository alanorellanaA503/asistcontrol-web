<script setup>
import { ArrowLeft } from 'lucide-vue-next'
import BrandLogo from '@/components/BrandLogo.vue'
import ThemeToggle from '@/components/ThemeToggle.vue'
import LadybugFrame from '@/components/LadybugFrame.vue'
import PhotoMarquee from '@/components/PhotoMarquee.vue'
import AppFooter from '@/layouts/AppFooter.vue'

// "ladybug": fotos dentro de la mariquita (login)
// "slides": tarjetas con foto y texto en carrusel (registro)
// "align": "center" o "top" para la posición del formulario
defineProps({
  slides: { type: Array, default: () => [] },
  ladybug: { type: Array, default: null },
  align: { type: String, default: 'center' },
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

    <main class="auth-main" :class="{ 'has-media': slides.length || ladybug }">
      <aside v-if="ladybug" class="auth-media auth-media-ladybug">
        <LadybugFrame :images="ladybug" :width="180" />
      </aside>

      <aside v-else-if="slides.length" class="auth-media auth-media-marquee">
        <PhotoMarquee :slides="slides" :interval="5000" />
      </aside>

      <section class="auth-content" :class="`align-${align}`">
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
  justify-content: center;
  align-items: center;
  gap: 1.25rem;
  padding: 2rem;
  background: rgba(var(--v-theme-on-surface), 0.06);
}

.auth-media-ladybug {
  min-height: 420px;
}

.auth-media-marquee {
  position: relative;
  min-height: 560px;
  padding: 0;
  overflow: hidden;
}

.auth-content {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem 4vw;
}

/* Formulario pegado arriba, con espacio respecto al borde */
.auth-content.align-top {
  align-items: flex-start;
  padding-top: 3rem;
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
