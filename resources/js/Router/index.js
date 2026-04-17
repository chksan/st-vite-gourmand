import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/',
        name: 'home',
        title: 'Accueil',
        component: () => import('./components/Home.vue')
    },
    // We will add more routes later
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;