import Vue from 'vue';
import VueRouter from 'vue-router';
import store from '@/store';
import Home from '../views/Home.vue';
import Login from '../views/Login.vue';

Vue.use(VueRouter);

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  }
];

const router = new VueRouter({routes});
router.beforeEach((to, from, next) => {
  if (store.state.session.token) {
    if (to.name === 'Login') next('/');
    else next();
  }
  else {
    if (to.name === 'Login') next();
    else next({name: 'Login'});
  }
});
export default router;
