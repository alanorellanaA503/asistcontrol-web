<script setup>
import { computed } from 'vue'

const props = defineProps({
  images: { type: Array, default: () => [] },
  width: { type: Number, default: 180 },
})

// Id único para que el clipPath no choque si hay más de una mariquita
const uid = `ladybug-${Math.random().toString(36).slice(2, 8)}`

// Cada foto ocupa una franja vertical dentro del cuerpo
const slices = computed(() => {
  const n = props.images.length || 1
  const bodyTop = 70
  const bodyHeight = 250
  const h = bodyHeight / n
  return props.images.map((src, i) => ({
    src,
    y: bodyTop + i * h,
    height: h,
  }))
})
</script>

<template>
  <svg
    :width="width"
    :height="width * 2"
    viewBox="0 0 160 320"
    class="ladybug"
    role="img"
    aria-label="Mariquita decorativa"
  >
    <defs>
      <clipPath :id="uid">
        <ellipse cx="80" cy="200" rx="66" ry="120" />
      </clipPath>
    </defs>

    <!-- Antenas -->
    <path
      d="M68 40 Q56 12 42 8"
      stroke="#161616"
      stroke-width="4"
      fill="none"
      stroke-linecap="round"
    />
    <path
      d="M92 40 Q104 12 118 8"
      stroke="#161616"
      stroke-width="4"
      fill="none"
      stroke-linecap="round"
    />

    <!-- Patas -->
    <g stroke="#161616" stroke-width="4" stroke-linecap="round">
      <line x1="30" y1="110" x2="10" y2="95" />
      <line x1="30" y1="170" x2="6" y2="170" />
      <line x1="30" y1="230" x2="10" y2="245" />
      <line x1="130" y1="110" x2="150" y2="95" />
      <line x1="130" y1="170" x2="154" y2="170" />
      <line x1="130" y1="230" x2="150" y2="245" />
    </g>

    <!-- Cabeza -->
    <circle cx="80" cy="50" r="30" fill="#161616" />
    <circle cx="70" cy="46" r="4" fill="#ffffff" />
    <circle cx="90" cy="46" r="4" fill="#ffffff" />

    <!-- Cuerpo: fondo rojo y fotos recortadas dentro del óvalo -->
    <g :clip-path="`url(#${uid})`">
      <rect x="0" y="70" width="160" height="260" fill="#ff4b23" />
      <image
        v-for="slice in slices"
        :key="slice.src"
        :href="slice.src"
        x="14"
        :y="slice.y"
        width="132"
        :height="slice.height"
        preserveAspectRatio="xMidYMid slice"
      />
    </g>

    <!-- Línea central y contorno -->
    <line x1="80" y1="72" x2="80" y2="320" stroke="#161616" stroke-width="4" />
    <ellipse cx="80" cy="200" rx="66" ry="120" fill="none" stroke="#161616" stroke-width="4" />
  </svg>
</template>

<style scoped>
.ladybug {
  display: block;
  max-width: 100%;
  height: auto;
  margin: 0 auto;
}
</style>
