import {createRouter, createWebHistory} from 'vue-router';
import {useAuth} from '../Helpers/auth';

const routes = [{path: '/', name: 'home', component: () => import('../components/Home.vue')}, {
    path: '/login',
    name: 'login',
    component: () => import('../components/auth/Login.vue')
}, {path: '/register', name: 'register', component: () => import('../components/auth/Register.vue')}, {
    path: '/menus',
    name: 'menus',
    component: () => import('../components/Menus.vue')
}, {path: '/menu/:id', name: 'menu-detail', component: () => import('../components/MenuDetails.vue')}, {
    path: '/commande', name: 'commande', component: () => import('../components/Order.vue'), meta: {
        requiresAuth: true,
    }
},

    {
        path: '/espace-utilisateur',
        name: 'user-space',
        component: () => import('../components/UserSpace.vue'),
        meta: {requiresAuth: true}
    }, {
        path: '/employe',
        name: 'employe-space',
        component: () => import('../components/EmployeSpace.vue'),
        meta: {requiresAuth: true, requiresRole: 'employe'}
    }, {
        path: '/admin',
        name: 'admin-space',
        component: () => import('../components/AdminSpace.vue'),
        meta: {requiresAuth: true, requiresRole: 'admin'}
    }, {
        name: 'forgot-password',
        path: '/forgot-password',
        component: () => import('../components/auth/ForgotPassword.vue')
    },
    {
        name: 'reset-password',
        path: '/reset-password',
        component: () => import('../components/auth/ResetPassword.vue')
    },
    { path: '/mentions-legales', component: () => import('../components/MentionsLegales.vue') },
    { path: '/cgv', component: () => import('../components/CGV.vue') },
    {
        path: '/contact', name: 'contact', component: () => import('../components/Contact.vue'),
    },

    {path: '/:pathMatch(.*)*', redirect: '/'}];

const router = createRouter({
    history: createWebHistory(), routes,
});

router.beforeEach(async (to) => {
    const {user, getMe} = useAuth();
    if (!user.value) {
        await getMe();
    }

    if (to.meta.requiresAuth && !user.value) {
        return '/login';
    }

    if (to.meta.requiresRole && user.value) {
        const role = user.value.role;
        const required = to.meta.requiresRole;

        if (role === 'admin') return true;

        if (role !== required) return '/';
    }

    return true;
});

export default router;