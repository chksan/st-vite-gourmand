<template>
  <div class="max-w-md mx-auto mt-12 p-8 bg-white rounded-xl shadow">
    <h2 class="text-3xl font-bold text-center mb-8 text-primary">Connexion</h2>

    <form @submit.prevent="handleLogin" class="space-y-6">
      <div>
        <input
            v-model="email"
            type="email"
            placeholder="Email"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary"
            required
        />
      </div>

      <div>
        <input
            v-model="password"
            type="password"
            placeholder="Mot de passe"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary"
            required
        />
      </div>

      <button
          type="submit"
          class="w-full bg-primary hover:bg-red-700 text-white font-medium py-3.5 rounded-lg transition"
      >
        Se connecter
      </button>
    </form>

    <p class="text-center mt-6 text-gray-600">
      Pas encore de compte ?
      <router-link to="/register" class="text-accent underline">Créer un compte</router-link>
    </p>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuth } from '../../Helpers/auth';
import { useRouter } from 'vue-router';

const { login } = useAuth();
const router = useRouter();

const email = ref('');
const password = ref('');

const handleLogin = async () => {
  try {
    await login(email.value, password.value);
    router.push('/menus');
  } catch (error) {
    alert('Connexion échouée : ' + (error.response?.data?.message || 'Erreur inconnue'));
  }
};
</script>