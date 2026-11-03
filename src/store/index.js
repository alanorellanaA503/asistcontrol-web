import { createStore } from "vuex";

export default createStore({
  state: {
    user: null,
    isAuthenticated: false,
  },

  mutations: {
    SET_USER(state, user) {
      state.user = user;
      state.isAuthenticated = !!user;
    },

    LOGOUT(state) {
      state.user = null;
      state.isAuthenticated = false;
    },
  },
});
