<script setup>
import { computed } from 'vue'
import { useTheme } from 'vuetify'
import { Sun, Moon } from 'lucide-vue-next'

const theme = useTheme()
const isDark = computed(() => theme.global.current.value.dark)

function toggleTheme() {
  const next = isDark.value ? 'light' : 'dark'
  theme.global.name.value = next
  localStorage.setItem('theme', next)
}
</script>

<template>
  <v-btn
    icon
    variant="text"
    color="primary"
    :aria-label="isDark ? 'Activar modo claro' : 'Activar modo oscuro'"
    @click="toggleTheme"
  >
    <Sun v-if="isDark" :size="20" />
    <Moon v-else :size="20" />
  </v-btn>
</template>
