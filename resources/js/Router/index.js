import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from '../Helpers/auth';

const routes = [
    { path: '/', name: 'home', component: () => import('../components/Home.vue') },
    { path: '/login', name: 'login', component: () => import('../components/auth/Login.vue') },
    { path: '/register', name: 'register', component: () => import('../components/auth/Register.vue') },
    { path: '/menus', name: 'menus', component: () => import('../components/Menus.vue') },
    {
        path: '/menu/:id',
        name: 'menu-detail',
        component: () => import('../components/MenuDetails.vue')
    },
    {
        path: '/espace-utilisateur',
        name: 'user-space',
        component: () => import('../components/UserSpace.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/employe',
        name: 'employe-space',
        component: () => import('../components/EmployeSpace.vue'),
        meta: { requiresAuth: true, requiresRole: 'employe' }
    },
    {
        path: '/admin',
        name: 'admin-space',
        component: () => import('../components/AdminSpace.vue'),
        meta: { requiresAuth: true, requiresRole: 'admin' }
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const { user } = useAuth();

    if (to.meta.requiresAuth && !user.value) {
        next('/login');
    } else if (to.meta.requiresRole && user.value?.role !== to.meta.requiresRole) {
        next('/');
    } else {
        next();
    }
});

export default router;