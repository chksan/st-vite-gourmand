<template>
  <div class="min-h-screen bg-[#F5F0E8] flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full">
      <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10">
        <div class="text-center mb-10">
          <span class="text-4xl">🔒</span>
          <h1 class="text-3xl font-bold text-[#3D2B1F] mt-4">Réinitialiser le mot de passe</h1>
        </div>
        <div v-if="!form.token" class="text-center py-10">
          <p class="text-red-600">Lien invalide ou expiré.</p>
          <router-link to="/forgot-password" class="text-[#C1813A] hover:underline mt-4 inline-block">
            Demander un nouveau lien
          </router-link>
        </div>

        <div v-else>
          <div v-if="success" class="bg-green-50 text-green-700 p-5 rounded-2xl text-center mb-6">
            Mot de passe réinitialisé avec succès !
            <router-link to="/login" class="block mt-4 text-[#C1813A] underline">Se connecter</router-link>
          </div>

          <form v-else @submit.prevent="handleSubmit" class="space-y-6">
            <div>
              <label class="block text-sm font-medium text-[#7A6E62] mb-2">Email</label>
              <input v-model="form.email" type="email" class="input" readonly />
            </div>

            <div>
              <label class="block text-sm font-medium text-[#7A6E62] mb-2">Nouveau mot de passe</label>
              <input v-model="form.password" type="password" class="input" required />
            </div>

            <div>
              <label class="block text-sm font-medium text-[#7A6E62] mb-2">Confirmer le mot de passe</label>
              <input v-model="form.password_confirmation" type="password" class="input" required />
            </div>

            <p v-if="error" class="text-red-600 bg-red-50 p-4 rounded-2xl text-center">{{ error }}</p>

            <button
                type="submit"
                :disabled="loading"
                class="w-full bg-[#C1813A] hover:bg-[#A76D2F] text-white py-4 rounded-2xl font-semibold text-lg transition disabled:opacity-70">
              {{ loading ? 'Réinitialisation...' : 'Réinitialiser le mot de passe' }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();

const form = ref({
  token: route.query.token || '',
  email: route.query.email || '',
  password: '',
  password_confirmation: ''
});

const loading = ref(false);
const success = ref(false);
const error = ref('');

const hasValidToken = computed(() => !!form.value.token && !!form.value.email);

const handleSubmit = async () => {
  if (!hasValidToken.value) {
    error.value = "Lien invalide.";
    return;
  }

  loading.value = true;
  error.value = '';

  try {
    await axios.post('/v1/reset-password', form.value);
    success.value = true;

    setTimeout(() => {
      router.push('/login');
    }, 2000);
  } catch (e) {
    error.value = e.response?.data?.message ||
        Object.values(e.response?.data?.errors || {}).flat().join(' ') ||
        "Une erreur est survenue.";
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  if (!hasValidToken.value) {
    console.warn("Token non valide.");
  }
});
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