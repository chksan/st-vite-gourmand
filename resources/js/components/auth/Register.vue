<template>
  <div class="min-h-screen bg-[#F5F0E8] flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full">
      <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10">
        <div class="text-center mb-8">
          <span class="text-4xl">🍽️</span>
          <h1 class="text-3xl font-bold text-[#3D2B1F] mt-4">Créer un compte</h1>
          <p class="text-[#7A6E62] mt-2">Rejoignez Vite & Gourmand</p>
        </div>

        <form @submit.prevent="handleRegister" class="space-y-5">
          <!-- Prénom & Nom -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-[#7A6E62] mb-2">Prénom</label>
              <input
                  v-model="form.first_name"
                  type="text"
                  class="w-full px-5 py-4 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A] focus:outline-none text-base"
                  required
              />
              <p v-if="errors.first_name" class="text-red-600 text-sm mt-1">{{ errors.first_name[0] }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-[#7A6E62] mb-2">Nom</label>
              <input
                  v-model="form.last_name"
                  type="text"
                  class="w-full px-5 py-4 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A] focus:outline-none text-base"
                  required
              />
              <p v-if="errors.last_name" class="text-red-600 text-sm mt-1">{{ errors.last_name[0] }}</p>
            </div>
          </div>

          <!-- Email -->
          <div>
            <label class="block text-sm font-medium text-[#7A6E62] mb-2">Email</label>
            <input
                v-model="form.email"
                type="email"
                class="w-full px-5 py-4 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A] focus:outline-none text-base"
                required
            />
            <p v-if="errors.email" class="text-red-600 text-sm mt-1">{{ errors.email[0] }}</p>
          </div>

          <!-- Téléphone -->
          <div>
            <label class="block text-sm font-medium text-[#7A6E62] mb-2">Téléphone</label>
            <input
                v-model="form.phone"
                type="tel"
                class="w-full px-5 py-4 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A] focus:outline-none text-base"
                required
            />
            <p v-if="errors.phone" class="text-red-600 text-sm mt-1">{{ errors.phone[0] }}</p>
          </div>

          <!-- Adresse -->
          <div>
            <label class="block text-sm font-medium text-[#7A6E62] mb-2">Adresse complète</label>
            <input
                v-model="form.address"
                placeholder="Rue, numéro, code postal, ville"
                class="w-full px-5 py-4 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A] focus:outline-none text-base"
                required
            />
            <p v-if="errors.address" class="text-red-600 text-sm mt-1">{{ errors.address[0] }}</p>
          </div>

          <!-- Mot de passe -->
          <div>
            <label class="block text-sm font-medium text-[#7A6E62] mb-2">Mot de passe</label>
            <input
                v-model="form.password"
                type="password"
                class="w-full px-5 py-4 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A] focus:outline-none text-base"
                required
            />
            <p v-if="errors.password" class="text-red-600 text-sm mt-1">{{ errors.password[0] }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-[#7A6E62] mb-2">Confirmer le mot de passe</label>
            <input
                v-model="form.password_confirmation"
                type="password"
                class="w-full px-5 py-4 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A] focus:outline-none text-base"
                required
            />
          </div>

          <!-- Erreur générale -->
          <p v-if="errors.general" class="text-red-600 text-center bg-red-50 p-3 rounded-2xl">
            {{ errors.general }}
          </p>

          <button
              type="submit"
              :disabled="loading"
              class="w-full bg-[#C1813A] hover:bg-[#A76D2F] text-white py-4 rounded-2xl font-semibold text-lg transition disabled:opacity-70">
            {{ loading ? 'Création du compte en cours...' : 'Créer mon compte' }}
          </button>
        </form>      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuth } from '../../Helpers/auth';
import { useRouter } from 'vue-router';

const { register } = useAuth();
const router = useRouter();

const loading = ref(false);
const errors = ref({});

const form = ref({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  address: '',
  password: '',
  password_confirmation: ''
});

const handleRegister = async () => {
  loading.value = true;
  errors.value = {};

  const fullName = `${form.value.first_name.trim()} ${form.value.last_name.trim()}`.trim();

  const registerData = {
    name: fullName,
    first_name: form.value.first_name.trim(),
    last_name: form.value.last_name.trim(),
    email: form.value.email,
    phone: form.value.phone,
    address: form.value.address,
    password: form.value.password,
    password_confirmation: form.value.password_confirmation
  };

  try {
    await register(registerData);
    router.push('/menus');
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors;
    } else if (error.response?.data?.message) {
      errors.value.general = error.response.data.message;
    } else {
      errors.value.general = "Une erreur est survenue lors de l'inscription.";
    }
  } finally {
    loading.value = false;
  }
};
</script>