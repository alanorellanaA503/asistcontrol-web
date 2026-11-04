<script setup>
import { RouterLink } from 'vue-router'
import heroUrl from '@/assets/images/Hero_AsistC.svg'
import BrandLogo from '@/components/BrandLogo.vue'
import ThemeToggle from '@/components/ThemeToggle.vue'
import AppFooter from '@/layouts/AppFooter.vue'
import {
  CalendarCheck,
  BarChart3,
  Smartphone,
  Eye,
  ShieldCheck,
  Heart,
  Users,
  UserRound,
  LogIn,
  UserPlus,
} from 'lucide-vue-next'

const features = [
  {
    icon: CalendarCheck,
    title: 'Registro diario',
    text: 'Marca presente, tarde o ausente en pocos clics desde la lista del grupo.',
  },
  {
    icon: BarChart3,
    title: 'Porcentajes automáticos',
    text: 'El sistema calcula la asistencia de cada estudiante y del grupo.',
  },
  {
    icon: Smartphone,
    title: 'Desde cualquier dispositivo',
    text: 'Diseño adaptable para usarlo en computadora, tableta o celular.',
  },
]

const values = [
  {
    icon: Eye,
    title: 'Claridad',
    text: 'Información visible y fácil de entender para el docente.',
  },
  {
    icon: ShieldCheck,
    title: 'Confiabilidad',
    text: 'Registros ordenados y consistentes en cada clase.',
  },
  { icon: Heart, title: 'Cercanía', text: 'Una herramienta pensada para la realidad del aula.' },
  {
    icon: Users,
    title: 'Trabajo en equipo',
    text: 'Construido de forma colaborativa, paso a paso.',
  },
]

// Reemplaza con los nombres y roles reales
const team = [
  { name: 'Anderson Daniel Durán Caceres', role: 'Scrum Master' },
  { name: 'Alan Antonio Orellana Arguera', role: 'Product Owner y Frontend' },
  { name: 'Adrián Josue Chicas González', role: 'Developer Full Stack' },
  { name: 'Michael Steven Jiménez Navas', role: 'Developer Backend' },
  { name: 'Alessandra Giselle Córdova Burgos', role: 'Developer Frontend' },
]
</script>

<template>
  <div class="landing">
    <header class="navbar">
      <RouterLink to="/" class="brand">
        <BrandLogo :height="44" />
      </RouterLink>

      <nav class="nav-links">
        <a href="#quienes-somos">Quiénes somos</a>
        <a href="#valores">Valores</a>
        <a href="#equipo">Equipo</a>
      </nav>

      <div class="nav-actions">
        <ThemeToggle />
        <v-btn variant="outlined" color="secondary" rounded="pill" to="/login">
          <LogIn :size="18" class="me-2" /> Iniciar sesión
        </v-btn>
        <v-btn color="primary" rounded="pill" to="/solicitar-acceso">
          <UserPlus :size="18" class="me-2" /> Solicitar acceso
        </v-btn>
      </div>
    </header>

    <section class="hero">
      <div class="hero-text">
        <h1>Tu asistencia, más organizada.</h1>
        <p class="hero-subtitle">
          Registra, administra y consulta la asistencia académica desde un solo lugar.
        </p>
        <div class="hero-actions">
          <v-btn color="primary" rounded="pill" size="large" to="/solicitar-acceso">
            <UserPlus :size="20" class="me-2" /> Solicitar acceso
          </v-btn>
          <v-btn variant="outlined" color="secondary" rounded="pill" size="large" to="/login">
            <LogIn :size="20" class="me-2" /> Iniciar sesión
          </v-btn>
        </div>
      </div>

      <div class="hero-media">
        <img :src="heroUrl" alt="Ilustración de un aula con docente y estudiantes" />
      </div>
    </section>

    <section id="quienes-somos" class="section">
      <h2>¿Quiénes somos?</h2>
      <p class="section-intro">
        AsistControl es una aplicación web para docentes que necesitan llevar el control de
        asistencia de sus grupos de forma ordenada, rápida y confiable.
      </p>

      <div class="cards">
        <article v-for="item in features" :key="item.title" class="card">
          <div class="icon-circle">
            <component :is="item.icon" :size="28" />
          </div>
          <h3>{{ item.title }}</h3>
          <p>{{ item.text }}</p>
        </article>
      </div>
    </section>

    <section id="valores" class="section section-alt">
      <h2>Nuestros valores</h2>
      <div class="cards cards-4">
        <article v-for="item in values" :key="item.title" class="card">
          <div class="icon-circle">
            <component :is="item.icon" :size="28" />
          </div>
          <h3>{{ item.title }}</h3>
          <p>{{ item.text }}</p>
        </article>
      </div>
    </section>

    <section id="equipo" class="section">
      <h2>Nuestro equipo</h2>
      <div class="team-grid">
        <article v-for="member in team" :key="member.name" class="card">
          <div class="avatar">
            <UserRound :size="36" />
          </div>
          <h3>{{ member.name }}</h3>
          <p>{{ member.role }}</p>
        </article>
      </div>
    </section>

    <AppFooter />
  </div>
</template>

<style scoped>
.landing {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

/* Navbar */
.navbar {
  position: sticky;
  top: 0;
  z-index: 100;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
  padding: 1.25rem 4vw;
  background: rgb(var(--v-theme-background));
  border-bottom: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.nav-links {
  display: flex;
  gap: 1.5rem;
  font-weight: 600;
}

.nav-links a:hover {
  color: rgb(var(--v-theme-primary));
}

.nav-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex-wrap: wrap;
}

/* Hero */
.hero {
  display: grid;
  grid-template-columns: 1fr 1fr;
  align-items: center;
  gap: 2rem;
  padding: 3rem 4vw 4rem;
}

.hero h1 {
  font-size: clamp(2.2rem, 5vw, 3.75rem);
  line-height: 1.1;
  font-weight: 800;
  margin-bottom: 1rem;
}

.hero-subtitle {
  font-size: 1.15rem;
  color: rgb(var(--v-theme-primary));
  margin-bottom: 2rem;
}

.hero-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}

.hero-media img {
  width: 100%;
  max-width: 560px;
  margin: 0 auto;
}

/* Secciones */
.section {
  padding: 4rem 4vw;
}

.section-alt {
  background: rgb(var(--v-theme-surface));
}

.section h2 {
  font-size: clamp(1.75rem, 3.5vw, 2.5rem);
  font-weight: 800;
  text-align: center;
  margin-bottom: 0.75rem;
}

.section-intro {
  text-align: center;
  max-width: 680px;
  margin: 0 auto 2.5rem;
  color: rgb(var(--v-theme-muted));
}

.cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
  gap: 1.5rem;
  max-width: 1100px;
  margin: 0 auto;
}

.card {
  background: rgb(var(--v-theme-surface));
  border: 1px solid rgba(var(--v-theme-on-surface), 0.1);
  border-radius: var(--radius);
  padding: 1.75rem;
  text-align: center;
  transition:
    transform 0.2s ease,
    box-shadow 0.2s ease;
}
/* Equipo: 3 por fila en escritorio, la última fila centrada */
.team-grid {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 1.5rem;
  max-width: 1000px;
  margin: 0 auto;
}

.team-grid .card {
  flex: 0 0 calc((100% - 3rem) / 3);
  display: flex;
  flex-direction: column;
  align-items: center;
}

@media (max-width: 800px) {
  .team-grid .card {
    flex-basis: calc((100% - 1.5rem) / 2);
  }

  .hero-actions {
    flex-direction: column;
    align-items: stretch;
    width: 100%;
    max-width: 360px;
    margin: 0 auto;
  }

  .hero-actions :deep(.v-btn) {
    width: 100%;
  }
}

@media (max-width: 520px) {
  .team-grid .card {
    flex-basis: 100%;
  }
}

.card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 24px rgba(0, 0, 0, 0.12);
}

.card h3 {
  font-size: 1.15rem;
  margin: 0.75rem 0 0.5rem;
}

.card p {
  color: rgb(var(--v-theme-muted));
  font-size: 0.95rem;
}

.icon-circle,
.avatar {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: rgba(var(--v-theme-primary), 0.14);
  color: rgb(var(--v-theme-primary));
}

.card .avatar {
  margin: 0 auto;
}

/* Responsive */
@media (max-width: 800px) {
  .hero {
    grid-template-columns: 1fr;
    text-align: center;
    padding-top: 2rem;
  }

  .hero-actions {
    justify-content: center;
  }

  .hero-media {
    order: -1;
  }

  .nav-links {
    display: none;
  }
}

/* Móvil: navbar en dos filas, con marca arriba y acciones abajo */
@media (max-width: 800px) {
  .navbar {
    flex-direction: column;
    align-items: stretch;
    gap: 0.75rem;
    padding: 1rem 4vw;
  }

  .navbar > a,
  .brand {
    align-self: center;
  }

  .nav-actions {
    display: grid;
    grid-template-columns: auto 1fr 1fr;
    gap: 0.5rem;
    align-items: center;
  }

  .nav-actions :deep(.v-btn) {
    width: 100%;
    font-size: 0.75rem;
    padding: 0 0.5rem;
    letter-spacing: 0;
  }
}
</style>
