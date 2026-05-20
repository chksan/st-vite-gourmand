<template>
  <div class="min-h-screen bg-[#F5F0E8] flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full">
      <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10">
        <div class="text-center mb-10">
          <span class="text-4xl">🔑</span>
          <h1 class="text-3xl font-bold text-[#3D2B1F] mt-4">Mot de passe oublié</h1>
          <p class="text-[#7A6E62] mt-2">Nous allons vous envoyer un lien de réinitialisation</p>
        </div>

        <div v-if="success" class="bg-green-50 border border-green-200 text-green-700 p-5 rounded-2xl text-center mb-6">
          Un lien de réinitialisation a été envoyé à <strong>{{ email }}</strong>.<br>
          <span class="text-sm">Vérifiez vos spams si vous ne le voyez pas.</span>
        </div>

        <form v-else @submit.prevent="handleSubmit" class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-[#7A6E62] mb-2">Votre adresse email</label>
            <input
                v-model="email"
                type="email"
                placeholder="votre@email.com"
                class="input"
                required
                autofocus
            />
            <p v-if="error" class="text-red-600 text-sm mt-2">{{ error }}</p>
          </div>

          <button
              type="submit"
              :disabled="loading"
              class="w-full bg-[#C1813A] hover:bg-[#A76D2F] text-white py-4 rounded-2xl font-semibold text-lg transition disabled:opacity-70">
            {{ loading ? 'Envoi en cours...' : 'Envoyer le lien' }}
          </button>
        </form>

        <p class="text-center mt-8 text-[#7A6E62]">
          <router-link to="/login" class="text-[#C1813A] hover:underline font-medium">
            ← Retour à la connexion
          </router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const email = ref('');
const loading = ref(false);
const success = ref(false);
const error = ref('');

const handleSubmit = async () => {
  loading.value = true;
  error.value = '';

  try {
    await axios.post('/v1/forgot-password', {
      email: email.value
    });

    success.value = true;
  } catch (e) {
    error.value = e.response?.data?.message || "Impossible d'envoyer l'email. Vérifiez votre adresse.";
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