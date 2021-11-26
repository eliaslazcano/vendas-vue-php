import Vue from 'vue';
import Vuex from 'vuex';
import vuexPersist from '@/plugins/vuex-persist';
import { alertStore, loaderStore, snackbarStore, dialogStore,sessionStore } from './modules';

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    dark: false,
  },
  mutations: {
    setDark(state, payload) {
      state.dark = payload;
    },
  },
  actions: {
  },
  modules: {
    alert: alertStore,
    loader: loaderStore,
    snackbar: snackbarStore,
    dialog: dialogStore,
    session: sessionStore,
  },
  plugins: [vuexPersist.plugin],
});
