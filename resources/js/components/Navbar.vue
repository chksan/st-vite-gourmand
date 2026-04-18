<template>
  <nav class="bg-dark text-white shadow">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <h1 class="text-2xl font-bold text-primary">Vite & Gourmand</h1>
      </div>

      <div class="flex items-center gap-8">
        <router-link to="/" class="hover:text-accent">Accueil</router-link>
        <router-link to="/menus" class="hover:text-accent">Menus</router-link>
        <router-link to="/contact" class="hover:text-accent">Contact</router-link>

        <div v-if="user" class="flex items-center gap-6">
          <span class="text-sm">{{ user.name }}</span>

          <router-link v-if="user.role === 'admin'" to="/admin" class="text-accent">Admin</router-link>
          <router-link v-else-if="user.role === 'employe'" to="/employe" class="text-accent">Employé</router-link>
          <router-link v-else to="/espace-utilisateur" class="text-accent">Mon espace</router-link>

          <button @click="logout" class="text-sm hover:text-red-400">Déconnexion</button>
        </div>

        <div v-else class="flex items-center gap-4">
          <router-link to="/login" class="px-5 py-2 border border-white rounded hover:bg-white hover:text-dark">Connexion</router-link>
          <router-link to="/register" class="px-5 py-2 bg-primary rounded">Inscription</router-link>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { useAuth } from '../Helpers/auth';
import { useRouter } from 'vue-router';

const { user, logout } = useAuth();
const router = useRouter();

const handleLogout = async () => {
  await logout();
  router.push('/');
};
</script>