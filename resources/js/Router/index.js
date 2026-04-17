import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    { path: '/', name: 'home', component: () => import('../components/Home.vue') },
    { path: '/login', name: 'login', component: () => import('../components/auth/Login.vue') },
    { path: '/register', name: 'register', component: () => import('../components/auth/Register.vue.vue') },
    //{ path: '/menus', name: 'menus', component: () => import('./components/Menus.vue') },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;