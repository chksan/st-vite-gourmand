<template>
  <div class="max-w-4xl mx-auto px-6 py-8">
    <h1 class="text-4xl font-bold mb-8">Passer commande</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
      <div class="bg-white p-8 rounded-3xl shadow">
        <form @submit.prevent="submitOrder" class="space-y-6">
          <div>
            <label class="block mb-2">Nom complet</label>
            <input v-model="form.name" class="w-full p-4 border rounded-2xl" readonly />
          </div>

          <div>
            <label class="block mb-2">Adresse de livraison</label>
            <input v-model="form.delivery_address" class="w-full p-4 border rounded-2xl" required />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block mb-2">Date</label>
              <input v-model="form.delivery_date" type="date" class="w-full p-4 border rounded-2xl" required />
            </div>
            <div>
              <label class="block mb-2">Heure</label>
              <input v-model="form.delivery_time" type="time" class="w-full p-4 border rounded-2xl" required />
            </div>
          </div>

          <div>
            <label class="block mb-2">Nombre de personnes (min {{ menu?.min_personnes || 2 }})</label>
            <input v-model="form.nb_personnes" type="number" :min="menu?.min_personnes || 2" class="w-full p-4 border rounded-2xl" required />
          </div>

          <div>
            <label class="block mb-2">Distance (km) — si hors Bordeaux</label>
            <input v-model="form.distance_km" type="number" step="0.1" min="0" class="w-full p-4 border rounded-2xl" placeholder="Ex: 12.5" />
          </div>

          <button type="submit" class="w-full bg-accent py-5 text-xl font-semibold rounded-3xl text-white">
            Valider la commande — {{ totalPrice.toFixed(2) }} €
          </button>
        </form>
      </div>

      <div class="bg-gray-50 p-8 rounded-3xl">
        <h2 class="font-semibold mb-6">Récapitulatif</h2>
        <div v-if="menu" class="space-y-4">
          <p class="text-2xl">{{ menu.title }}</p>
          <p class="text-4xl font-bold">{{ totalPrice.toFixed(2) }} €</p>
          <p class="text-sm text-gray-500">
            Livraison : {{ deliveryFee.toFixed(2) }} €
            <span v-if="form.distance_km > 0 && !isInBordeaux" class="text-red-600">(+ {{ (0.59 * form.distance_km).toFixed(2) }} € / km)</span>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuth } from '../Helpers/auth';

const route = useRoute();
const router = useRouter();
const { user } = useAuth();

const menu = ref(null);
const form = ref({
  name: user.value?.name || '',
  delivery_address: '',
  delivery_date: '',
  delivery_time: '',
  nb_personnes: 4,
  menu_id: null,
  distance_km: 0
});

const isInBordeaux = computed(() =>
    form.value.delivery_address.toLowerCase().includes('bordeaux')
);

const deliveryFee = computed(() => {
  const base = 5.00;
  return isInBordeaux.value
      ? base
      : base + (0.59 * (form.value.distance_km || 0));
});

const totalPrice = computed(() => {
  if (!menu.value) return 0;
  let total = menu.value.price * (form.value.nb_personnes / menu.value.min_personnes);
  if (form.value.nb_personnes >= menu.value.min_personnes + 5) total *= 0.9;
  return total + deliveryFee.value;
});

const loadMenu = async () => {
  const menuId = route.query.menu_id;
  if (!menuId) return;
  form.value.menu_id = menuId;
  const res = await axios.get(`/v1/menus/${menuId}`);
  menu.value = res.data;
};

const submitOrder = async () => {
  await axios.post('/v1/commande', form.value);
  alert('Commande passée avec succès !');
  router.push('/espace-utilisateur');
};

onMounted(loadMenu);
</script>