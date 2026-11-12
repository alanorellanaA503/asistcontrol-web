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
    // Notificación global: { text, color } o null
    notice: null,
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

    SHOW_NOTICE(state, notice) {
      state.notice = notice
    },

    CLEAR_NOTICE(state) {
      state.notice = null
    },
  },
})

store.subscribe((_mutation, state) => {
  if (state.user) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(state.user))
  } else {
    localStorage.removeItem(STORAGE_KEY)
  }
})

export default store
