<template>
  <div class="max-w-md mx-auto mt-12 p-8 bg-white rounded-xl shadow">
    <h2 class="text-3xl font-bold text-center mb-8">Créer un compte</h2>

    <form @submit.prevent="handleRegister" class="space-y-6">
      <input v-model="form.name" type="text" placeholder="Nom complet" class="w-full p-4 border rounded-2xl" required />

      <input v-model="form.email" type="email" placeholder="Adresse email" class="w-full p-4 border rounded-2xl" required />

      <div>
        <label class="block mb-2">Adresse</label>
        <input v-model="form.street" placeholder="Rue et numéro" class="w-full p-4 border rounded-2xl" required />
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block mb-2">Code postal</label>
          <input v-model="form.postal_code" placeholder="33000" class="w-full p-4 border rounded-2xl" required />
        </div>
        <div>
          <label class="block mb-2">Ville</label>
          <input v-model="form.city" placeholder="Bordeaux" class="w-full p-4 border rounded-2xl" required />
        </div>
      </div>

      <input v-model="form.phone" type="tel" placeholder="Numéro de téléphone Mobile" class="w-full p-4 border rounded-2xl" required />

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
  street: '',
  postal_code: '',
  city: '',
  password: '',
  password_confirmation: ''
});

const handleRegister = async () => {
  const fullAddress = `${form.value.street}, ${form.value.postal_code} ${form.value.city}`.trim();

  const registerData = {
    name: form.value.name,
    email: form.value.email,
    phone: form.value.phone,
    address: fullAddress,
    password: form.value.password,
    password_confirmation: form.value.password_confirmation
  };

  await register(registerData);
  router.push('/menus');
};
</script>