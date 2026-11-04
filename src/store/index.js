import { createStore } from 'vuex'

const STORAGE_KEY = 'asistcontrol-session'

function loadSession() {
  try {
    return JSON.parse(localStorage.getItem(STORAGE_KEY))
  } catch {
    return null
  }
}

const savedUser = loadSession()

const store = createStore({
  state: {
    user: savedUser,
    isAuthenticated: !!savedUser,
  },

  mutations: {
    SET_USER(state, user) {
      state.user = user
      state.isAuthenticated = !!user
    },

    LOGOUT(state) {
      state.user = null
      state.isAuthenticated = false
    },
  },
})

// Guarda o borra la sesión cada vez que cambia el usuario
store.subscribe((_mutation, state) => {
  if (state.user) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(state.user))
  } else {
    localStorage.removeItem(STORAGE_KEY)
  }
})

export default store
