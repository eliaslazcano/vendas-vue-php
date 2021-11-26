export default {
  namespaced: true,
  state: () => ({
    alerts: [], //Array<Alert>
  }),
  mutations: {
    show(state, alert) {
      if (state.alerts.find(a => a.text === alert.text)) return;
      const id = Date.now();
      state.alerts.unshift({id, ...alert});
      setTimeout(() => {
        const index = state.alerts.findIndex(a => a.id === id);
        if (index === -1) return;
        state.alerts.splice(index, 1);
      }, alert.timeout);
    },
    clear(state) {
      state.alerts = [];
    },
  },
  // actions: {},
  // getters: {},
};
