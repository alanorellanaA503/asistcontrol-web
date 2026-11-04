<script setup>
import { Mail, Phone, MapPin } from 'lucide-vue-next'
import logoFooter from '@/assets/logos/logoNameLight.png'
</script>

<template>
  <v-footer color="#000000" class="app-footer">
    <v-container>
      <v-row>
        <v-col cols="12" md="5">
          <img :src="logoFooter" alt="AsistControl" class="footer-logo" />
          <p class="footer-text">Gestión de asistencia académica para docentes.</p>
        </v-col>

        <v-col cols="6" md="3">
          <h4 class="footer-title">Navegación</h4>
          <ul class="footer-list">
            <li><a href="#quienes-somos">Quiénes somos</a></li>
            <li><a href="#valores">Valores</a></li>
            <li><a href="#equipo">Equipo</a></li>
          </ul>
        </v-col>

        <v-col cols="6" md="4">
          <h4 class="footer-title">Contacto</h4>
          <ul class="footer-list">
            <li><Mail :size="16" /> contacto@asistcontrol.com</li>
            <li><Phone :size="16" /> +503 0000-0000</li>
            <li><MapPin :size="16" /> El Salvador</li>
          </ul>
        </v-col>

        <v-col cols="12">
          <v-divider class="footer-divider" />
          <p class="footer-copy">© 2026 AsistControl · Proyecto académico ITCA-FEPADE</p>
        </v-col>
      </v-row>
    </v-container>
  </v-footer>
</template>

<style scoped>
.app-footer {
  /* Línea clave para el footer pegado abajo: empuja el footer al final de la pantalla */
  margin-top: auto;
  padding: 3rem 0 1.5rem;
  color: #d4d4d4;
}

.footer-logo {
  height: 40px;
  width: auto;
}

.footer-text {
  margin-top: 1rem;
  color: #b5b5b5;
}

.footer-title {
  font-size: 1rem;
  font-weight: 700;
  margin-bottom: 0.75rem;
  color: #ffffff;
}

.footer-list {
  list-style: none;
  display: grid;
  gap: 0.5rem;
}

.footer-list li {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.footer-list a:hover {
  color: #ff4b23;
}

.footer-divider {
  margin: 2rem 0 1.25rem;
  border-color: #2c2c2c;
}

.footer-copy {
  text-align: center;
  font-size: 0.85rem;
  color: #8a8a8a;
}

@media (max-width: 800px) {
  .footer-list li {
    justify-content: flex-start;
  }
}
</style>
