export default {
  namespaced: true,
  state: () => ({
    show: false,
    config: null,
  }),
  mutations: {
    /**
     * Exibe o snackbar
     * @param state
     * @param {Snackbar} snackbar
     */
    show(state, snackbar) {
      if (snackbar) {
        state.config = snackbar;
        state.show = true;
      }
    },
    /**
     * Oculta o snackbar
     * @param state
     */
    hide(state) {
      state.show = false;
    },
  },
  // actions: {},
  // getters: {},
};
