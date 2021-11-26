export default {
  namespaced: true,
  state: () => ({
    text: false,
  }),
  mutations: {
    show(state, payload) {
      state.text = payload;
    },
    close(state) {
      state.text = false;
    }
  },
  // actions: {},
  // getters: {},
};
