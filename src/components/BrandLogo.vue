<script setup>
import { computed } from 'vue'
import { useTheme } from 'vuetify'
import logoDark from '@/assets/logos/logoNameDark.svg'
import logoLight from '@/assets/logos/logoNameLight.png'

defineProps({
  height: { type: Number, default: 44 },
})

const theme = useTheme()
// "Light" es la versión blanca para fondos oscuros; "Dark" es la negra para fondos claros
const src = computed(() => (theme.global.current.value.dark ? logoLight : logoDark))
</script>

<template>
  <img :src="src" alt="AsistControl" class="brand-logo" :style="{ height: height + 'px' }" />
</template>

<style scoped>
.brand-logo {
  width: auto;
}
</style>
