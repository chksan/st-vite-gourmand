<template>
  <div class="max-w-md mx-auto mt-10">
    <h2 class="text-3xl font-bold text-center mb-6">Créer un compte</h2>
    <form @submit.prevent="handleRegister">
      <input v-model="form.name" type="text" placeholder="Nom complet" class="w-full p-3 border mb-4" required />
      <input v-model="form.email" type="email" placeholder="Email" class="w-full p-3 border mb-4" required />
      <input v-model="form.phone" type="tel" placeholder="Téléphone" class="w-full p-3 border mb-4" />
      <input v-model="form.password" type="password" placeholder="Mot de passe" class="w-full p-3 border mb-4" required />
      <input v-model="form.password_confirmation" type="password" placeholder="Confirmer le mot de passe" class="w-full p-3 border mb-6" required />
      <button type="submit" class="w-full bg-primary text-white py-3 rounded">S'inscrire</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuth } from '../../Helpers/auth';
import { useRouter } from 'vue-router';

const { register } = useAuth();
const router = useRouter();

const form = ref({
  name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: ''
});

const handleRegister = async () => {
  await register(form.value);
  router.push('/menus');
};
</script>