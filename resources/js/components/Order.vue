<template>
  <div class="max-w-5xl mx-auto px-6 py-12">
    <h1 class="text-4xl font-bold text-[#3D2B1F] mb-2">Finaliser votre commande</h1>
    <p class="text-[#7A6E62] mb-10">Veuillez vérifier les informations ci-dessous</p>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
      <!-- Formulaire -->
      <div class="bg-white p-8 rounded-3xl shadow">
        <form @submit.prevent="submitOrder" class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-[#7A6E62] mb-2">Nom complet</label>
            <input v-model="form.name" class="w-full p-4 border border-[#E8C98A] rounded-2xl bg-gray-50" readonly />
          </div>

          <div>
            <label class="block text-sm font-medium text-[#7A6E62] mb-2">Adresse de livraison complète</label>
            <textarea
                v-model="form.delivery_address"
                rows="3"
                class="w-full p-4 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A] focus:outline-none"
                placeholder="45 Avenue de la République, 33100 Bordeaux"
                required>
            </textarea>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-[#7A6E62] mb-2">Date de prestation</label>
              <input v-model="form.delivery_date" type="date" class="w-full p-4 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A]" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-[#7A6E62] mb-2">Heure souhaitée</label>
              <input v-model="form.delivery_time" type="time" class="w-full p-4 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A]" required />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-[#7A6E62] mb-2">
              Nombre de personnes (minimum {{ menu?.min_personnes || 2 }})
            </label>
            <input
                v-model="form.nb_personnes"
                type="number"
                :min="menu?.min_personnes || 2"
                class="w-full p-4 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A]"
                required
            />
          </div>

          <button
              type="submit"
              class="w-full bg-[#C1813A] hover:bg-[#A76D2F] text-white py-5 text-xl font-semibold rounded-2xl transition">
            Valider la commande — {{ totalPrice.toFixed(2) }} €
          </button>
        </form>
      </div>

      <!-- Récapitulatif -->
      <div class="bg-[#F9F6F1] p-8 rounded-3xl">
        <h2 class="font-semibold text-xl mb-6 text-[#3D2B1F]">Récapitulatif</h2>

        <div v-if="menu" class="space-y-6">
          <div>
            <p class="text-2xl font-medium text-[#3D2B1F]">{{ menu.title }}</p>
            <p class="text-[#7A6E62] mt-1">{{ menu.description }}</p>
          </div>

          <div class="border-t border-b border-[#E8C98A] py-6">
            <div class="flex justify-between text-lg">
              <span>Menu ({{ form.nb_personnes }} personnes)</span>
              <span class="font-semibold">{{ (menu.price * (form.nb_personnes / menu.min_personnes)).toFixed(2) }} €</span>
            </div>
            <div v-if="form.nb_personnes >= menu.min_personnes + 5" class="text-[#4A6741] text-sm mt-2">
              ✓ Réduction de 10% appliquée
            </div>
          </div>

          <div class="flex justify-between text-lg">
            <span>Frais de livraison</span>
            <span class="font-semibold">5,00 €</span>
          </div>

          <div class="bg-white p-4 rounded-2xl text-sm border border-[#E8C98A]">
            <p class="font-medium text-[#3D2B1F]">Informations livraison :</p>
            <p class="text-[#7A6E62] mt-1">
              • 5 € de base<br>
              • + 0,59 € par km hors Bordeaux
            </p>
          </div>

          <div class="pt-6 border-t border-[#E8C98A] flex justify-between text-2xl font-bold text-[#3D2B1F]">
            <span>Total</span>
            <span>{{ totalPrice.toFixed(2) }} €</span>
          </div>
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
  menu_id: null
});

const totalPrice = computed(() => {
  if (!menu.value) return 0;
  let total = menu.value.price * (form.value.nb_personnes / menu.value.min_personnes);
  if (form.value.nb_personnes >= menu.value.min_personnes + 5) total *= 0.9;
  return total + 5;
});

const loadMenu = async () => {
  const menuId = route.query.menu_id;
  if (!menuId) return;
  form.value.menu_id = menuId;
  const res = await axios.get(`/v1/menus/${menuId}`);
  menu.value = res.data;
};

const submitOrder = async () => {
  try {
    await axios.post('/v1/commande', form.value);
    alert('✅ Commande enregistrée avec succès !');
    router.push('/espace-utilisateur');
  } catch (error) {
    alert('Erreur : ' + (error.response?.data?.message || 'Veuillez réessayer'));
  }
};

onMounted(loadMenu);
</script>