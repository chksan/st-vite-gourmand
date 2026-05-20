<template>
  <div class="min-h-screen bg-[#F5F0E8] flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full">
      <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10">
        <div class="text-center mb-10">
          <span class="text-4xl">🍽️</span>
          <h1 class="text-3xl font-bold text-[#3D2B1F] mt-4">Connexion</h1>
          <p class="text-[#7A6E62] mt-2">Accédez à votre espace</p>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-[#7A6E62] mb-2">Email</label>
            <input
                v-model="email"
                type="email"
                placeholder="votre@email.com"
                class="input"
                required
            />
            <p v-if="errors.email" class="text-red-600 text-sm mt-1">{{ errors.email[0] }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-[#7A6E62] mb-2">Mot de passe</label>
            <input
                v-model="password"
                type="password"
                placeholder="••••••••"
                class="input"
                required
            />
            <p v-if="errors.password" class="text-red-600 text-sm mt-1">{{ errors.password[0] }}</p>
          </div>

          <div class="text-right -mt-2">
            <router-link to="/forgot-password" class="text-sm text-[#C1813A] hover:underline">
              Mot de passe oublié ?
            </router-link>
          </div>

          <!-- Erreur générale -->
          <p v-if="errors.general" class="text-red-600 text-center bg-red-50 p-3 rounded-2xl">
            {{ errors.general }}
          </p>

          <button
              type="submit"
              :disabled="loading"
              class="w-full bg-[#C1813A] hover:bg-[#A76D2F] text-white py-4 rounded-2xl font-semibold text-lg transition disabled:opacity-70">
            {{ loading ? 'Connexion en cours...' : 'Se connecter' }}
          </button>
        </form>

        <p class="text-center mt-8 text-[#7A6E62]">
          Pas encore de compte ?
          <router-link to="/register" class="text-[#C1813A] hover:underline font-medium">Créer un compte</router-link>
        </p>
      </div>
    </div>
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
const loading = ref(false);
const errors = ref({});

const handleLogin = async () => {
  loading.value = true;
  errors.value = {};

  try {
    await login(email.value, password.value);
    router.push('/menus');
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors;
    } else if (error.response?.data?.message) {
      errors.value.general = error.response.data.message;
    } else {
      errors.value.general = "Identifiants incorrects.";
    }
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.input {
  width: 100%;
  padding: 16px;
  border: 1px solid #E8C98A;
  border-radius: 16px;
  font-size: 16px;
}
.input:focus {
  border-color: #C1813A;
  outline: none;
}
</style>