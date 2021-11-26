import VuexPersistence from 'vuex-persist';
const vuexPersist = new VuexPersistence({
    key: 'storage',
    storage: window.localStorage,
    modules: ['session'],
});
export default vuexPersist;
