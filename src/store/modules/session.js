import router from '@/router';
import JwtHelper from '@/helpers/JwtHelper';

export default {
  namespaced: true,
  state: () => ({
    token: null,
  }),
  mutations: {
    setToken(state, payload) {
      state.token = payload;
    },
  },
  actions: {
    async signout({commit}) {
      commit('setToken', null);
      sessionStorage.clear();
      localStorage.clear();
      await router.push('/login');
    },
  },
  getters: {
    payload: state => state.token ? JwtHelper.getPayload(state.token) : null
  },
};
