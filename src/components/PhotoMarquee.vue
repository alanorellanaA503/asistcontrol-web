<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { ChevronUp, ChevronDown } from 'lucide-vue-next'

const props = defineProps({
  // Cada diapositiva: { src, title, text }
  slides: { type: Array, required: true },
  // Milisegundos entre cada cambio automático
  interval: { type: Number, default: 5000 },
})

const current = ref(0)
const paused = ref(false)
const total = computed(() => props.slides.length)
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches
let timer = null

function go(step) {
  current.value = (current.value + step + total.value) % total.value
}

function goTo(index) {
  current.value = index
}

onMounted(() => {
  if (reduceMotion || total.value < 2) return
  timer = setInterval(() => {
    if (!paused.value) go(1)
  }, props.interval)
})

onBeforeUnmount(() => clearInterval(timer))
</script>

<template>
  <div class="marquee" @mouseenter="paused = true" @mouseleave="paused = false">
    <Transition name="slide-up" mode="out-in">
      <figure :key="current" class="marquee-card">
        <img :src="slides[current].src" alt="" />
        <figcaption>
          <h3>{{ slides[current].title }}</h3>
          <p>{{ slides[current].text }}</p>
        </figcaption>
      </figure>
    </Transition>

    <div class="marquee-controls" aria-label="Controles del carrusel">
      <button type="button" class="ctrl-btn" aria-label="Anterior" @click="go(-1)">
        <ChevronUp :size="18" />
      </button>

      <div class="dots">
        <button
          v-for="(_, i) in slides"
          :key="i"
          type="button"
          class="dot"
          :class="{ active: i === current }"
          :aria-label="`Ir a la diapositiva ${i + 1}`"
          @click="goTo(i)"
        />
      </div>

      <button type="button" class="ctrl-btn" aria-label="Siguiente" @click="go(1)">
        <ChevronDown :size="18" />
      </button>
    </div>
  </div>
</template>

<style scoped>
/* Ocupa todo el espacio de la columna, sin márgenes */
.marquee {
  position: absolute;
  inset: 0;
  overflow: hidden;
}

/* La tarjeta cubre todo el marquee */
.marquee-card {
  position: absolute;
  inset: 0;
  margin: 0;
}

.marquee-card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Texto sobre un degradado oscuro para que se lea en ambos modos */
.marquee-card figcaption {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  padding: 4rem 6rem 2.5rem 2.5rem;
  color: #ffffff;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.85), rgba(0, 0, 0, 0));
}

.marquee-card h3 {
  font-size: 1.5rem;
  font-weight: 800;
  margin-bottom: 0.35rem;
}

.marquee-card p {
  font-size: 1rem;
  color: rgba(255, 255, 255, 0.92);
  max-width: 480px;
}

/* Controles flotando sobre el borde derecho de la imagen */
.marquee-controls {
  position: absolute;
  top: 50%;
  right: 1.25rem;
  transform: translateY(-50%);
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
  padding: 0.6rem 0.4rem;
  border-radius: 999px;
  background: rgba(0, 0, 0, 0.25);
  backdrop-filter: blur(6px);
}

.ctrl-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: none;
  background: rgba(255, 255, 255, 0.92);
  color: #111111;
  cursor: pointer;
  transition:
    background 0.2s ease,
    color 0.2s ease;
}

.ctrl-btn:hover {
  background: rgb(var(--v-theme-primary));
  color: #ffffff;
}

.dots {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
}

.dot {
  width: 8px;
  height: 8px;
  border-radius: 999px;
  border: none;
  background: rgba(255, 255, 255, 0.5);
  cursor: pointer;
  transition:
    height 0.3s ease,
    background 0.3s ease;
}

.dot.active {
  height: 28px;
  background: rgb(var(--v-theme-primary));
}

/* Animación: la tarjeta sube y entra desde abajo */
.slide-up-enter-active,
.slide-up-leave-active {
  transition:
    transform 0.6s ease,
    opacity 0.6s ease;
}

.slide-up-enter-from {
  transform: translateY(40px);
  opacity: 0;
}

.slide-up-leave-to {
  transform: translateY(-40px);
  opacity: 0;
}

@media (prefers-reduced-motion: reduce) {
  .slide-up-enter-active,
  .slide-up-leave-active {
    transition: none;
  }
}
</style>
