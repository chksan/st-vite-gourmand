<template>
  <div class="max-w-md mx-auto mt-12 p-8 bg-white rounded-xl shadow">
    <h2 class="text-3xl font-bold text-center mb-8">Créer un compte</h2>

    <form @submit.prevent="handleRegister" class="space-y-6">
      <input v-model="form.name" type="text" placeholder="Nom complet" class="w-full p-4 border rounded-2xl" required />

      <input v-model="form.email" type="email" placeholder="Adresse email" class="w-full p-4 border rounded-2xl" required />

      <textarea
          v-model="form.address"
          placeholder="Adresse postale complète (rue, code postal, ville)"
          rows="3"
          class="w-full p-4 border rounded-2xl resize-none"
          required>
      </textarea>

      <input v-model="form.phone" type="tel" placeholder="Numéro de GSM" class="w-full p-4 border rounded-2xl" required />

      <input v-model="form.password" type="password" placeholder="Mot de passe" class="w-full p-4 border rounded-2xl" required />
      <input v-model="form.password_confirmation" type="password" placeholder="Confirmer le mot de passe" class="w-full p-4 border rounded-2xl" required />

      <button type="submit" class="w-full bg-primary text-white py-4 rounded-2xl text-lg font-semibold">
        Créer mon compte
      </button>
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
  address: '',
  password: '',
  password_confirmation: ''
});

const handleRegister = async () => {
  await register(form.value);
  router.push('/menus');
};
</script>