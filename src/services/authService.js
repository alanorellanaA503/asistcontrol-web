
import api from './api'

export async function iniciarSesion(username, password) {
  const response = await api.post('/api/auth/login', {
    username,
    password,
  })

  return response.data
}

export async function obtenerPerfil() {
  const response = await api.get('/api/auth/me')

  return response.data
}
