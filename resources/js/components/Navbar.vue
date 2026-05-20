<template>
  <nav class="bg-[#3D2B1F] text-[#E8C98A] shadow-lg">
    <div class="max-w-7xl mx-auto px-4 md:px-6 py-4">
      <div class="flex items-center justify-between">
        <!-- Logo -->
        <router-link to="/" class="flex items-center gap-3">
          <span class="text-3xl">🍽️</span>
          <h1 class="text-xl md:text-2xl font-bold">Vite & Gourmand</h1>
        </router-link>

        <!-- Desktop Menu -->
        <div class="hidden md:flex items-center gap-8 text-sm">
          <router-link to="/" class="hover:text-[#C1813A] transition">Accueil</router-link>
          <router-link to="/menus" class="hover:text-[#C1813A] transition">Menus</router-link>
          <router-link to="/contact" class="hover:text-[#C1813A] transition">Contact</router-link>
        </div>

        <!-- User Section -->
        <div class="flex items-center gap-4">
          <div v-if="user" class="hidden md:flex items-center gap-6 text-sm">
            <span>{{ user.name }}</span>
            <router-link v-if="user.role === 'admin'" to="/admin" class="text-[#C1813A] hover:underline">Admin</router-link>
            <router-link v-else-if="user.role === 'employe'" to="/employe" class="text-[#C1813A] hover:underline">Employé</router-link>
            <router-link v-else to="/espace-utilisateur" class="text-[#C1813A] hover:underline">Mon espace</router-link>
            <button @click="logout" class="hover:text-red-400 transition">Déconnexion</button>
          </div>

          <div v-else class="hidden md:flex items-center gap-3">
            <router-link to="/login" class="px-5 py-2 border border-[#E8C98A] rounded-2xl hover:bg-[#E8C98A] hover:text-[#3D2B1F] transition text-sm">
              Connexion
            </router-link>
            <router-link to="/register" class="px-5 py-2 bg-[#C1813A] text-white rounded-2xl font-semibold hover:bg-[#A76D2F] transition text-sm">
              Inscription
            </router-link>
          </div>

          <!-- Mobile Menu Button -->
          <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-2xl p-2">
            ☰
          </button>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div v-if="mobileMenuOpen" class="md:hidden mt-4 pt-4 border-t border-[#E8C98A]/30">
        <div class="flex flex-col gap-4 text-sm">
          <router-link to="/" @click="mobileMenuOpen = false" class="hover:text-[#C1813A]">Accueil</router-link>
          <router-link to="/menus" @click="mobileMenuOpen = false" class="hover:text-[#C1813A]">Menus</router-link>
          <router-link to="/contact" @click="mobileMenuOpen = false" class="hover:text-[#C1813A]">Contact</router-link>

          <div v-if="user" class="pt-4 border-t border-[#E8C98A]/30 flex flex-col gap-3">
            <span class="text-[#C1813A]">{{ user.name }}</span>
            <router-link v-if="user.role === 'admin'" to="/admin" @click="mobileMenuOpen = false" class="text-[#C1813A]">Admin</router-link>
            <router-link v-else-if="user.role === 'employe'" to="/employe" @click="mobileMenuOpen = false" class="text-[#C1813A]">Employé</router-link>
            <router-link v-else to="/espace-utilisateur" @click="mobileMenuOpen = false" class="text-[#C1813A]">Mon espace</router-link>
            <button @click="handleLogout" class="text-left text-red-400">Déconnexion</button>
          </div>

          <div v-else class="flex flex-col gap-3 pt-4 border-t border-[#E8C98A]/30">
            <router-link to="/login" @click="mobileMenuOpen = false" class="text-center py-3 border border-[#E8C98A] rounded-2xl">Connexion</router-link>
            <router-link to="/register" @click="mobileMenuOpen = false" class="text-center py-3 bg-[#C1813A] text-white rounded-2xl">S'inscrire</router-link>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue';
import { useAuth } from '../Helpers/auth';
import { useRouter } from 'vue-router';

const { user, logout } = useAuth();
const router = useRouter();
const mobileMenuOpen = ref(false);

const handleLogout = async () => {
  await logout();
  mobileMenuOpen.value = false;
  router.push('/');
};
</script>