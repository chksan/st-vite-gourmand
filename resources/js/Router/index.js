import {createRouter, createWebHistory} from 'vue-router';
import {useAuth} from '../Helpers/auth';

const routes = [
    {path: '/', name: 'home', component: () => import('../components/Home.vue')},
    {path: '/login', name: 'login', component: () => import('../components/auth/Login.vue')},
    {path: '/register', name: 'register', component: () => import('../components/auth/Register.vue')},
    {path: '/menus', name: 'menus', component: () => import('../components/Menus.vue')},
    {path: '/menu/:id', name: 'menu-detail', component: () => import('../components/MenuDetails.vue')},
    {
        path: '/commande',
        name: 'commande',
        component: () => import('../components/Order.vue'),
        meta: {
            requiresAuth: true,
        }
    },
    {
        path: '/espace-utilisateur',
        name: 'user-space',
        component: () => import('../components/UserSpace.vue'),
        meta: {requiresAuth: true}
    },

    {
        path: '/contact',
        name: 'contact',
        component: () => import('../components/Contact.vue') || {template: '<div>Contact page coming soon</div>'}
    },

    {path: '/:pathMatch(.*)*', redirect: '/'}
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to) => {
    const {user, getMe} = useAuth();
    if (!user.value) {
        await getMe();
    }

    if (to.meta.requiresAuth && !user.value) {
        return '/login';
    }

    return true;
});

export default router;