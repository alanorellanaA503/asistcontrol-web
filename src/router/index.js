import { createRouter, createWebHistory } from 'vue-router'
import store from '@/store'

const routes = [
  {
    path: '/',
    name: 'landing',
    component: () => import('@/views/LandingView.vue'),
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/auth/LoginView.vue'),
  },
  {
    path: '/solicitar-acceso',
    name: 'register',
    component: () => import('@/views/auth/RegisterView.vue'),
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('@/views/dashboard/DashboardView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/asistencia',
    name: 'attendance',
    component: () => import('@/views/attendance/AttendanceView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/estudiantes',
    name: 'students',
    component: () => import('@/views/students/StudentsView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/perfil',
    name: 'profile',
    component: () => import('@/views/profile/ProfileView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/',
  },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

// Protege las pantallas internas: sin sesión, va al login
router.beforeEach((to) => {
  if (to.meta.requiresAuth && !store.state.isAuthenticated) {
    return { name: 'login' }
  }
  if (to.name === 'login' && store.state.isAuthenticated) {
    return { name: 'dashboard' }
  }
})

export default router
