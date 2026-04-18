<template>
  <div class="max-w-7xl mx-auto px-6 py-8">
    <h1 class="text-4xl font-bold text-primary mb-8">Nos Menus</h1>

    <div class="bg-white p-6 rounded-xl shadow mb-8 grid grid-cols-1 md:grid-cols-5 gap-4">
      <div>
        <label class="block text-sm mb-1">Prix maximum</label>
        <input v-model="filters.max_price" type="range" min="0" max="200" class="w-full" />
        <span class="text-sm">{{ filters.max_price }} €</span>
      </div>
      <div>
        <label class="block text-sm mb-1">Thème</label>
        <select v-model="filters.theme" class="w-full p-3 border rounded-lg">
          <option value="">Tous</option>
          <option value="Noel">Noël</option>
          <option value="Paques">Pâques</option>
          <option value="Classique">Classique</option>
          <option value="Evenement">Événement</option>
        </select>
      </div>
      <div>
        <label class="block text-sm mb-1">Régime</label>
        <select v-model="filters.regime" class="w-full p-3 border rounded-lg">
          <option value="">Tous</option>
          <option value="classique">Classique</option>
          <option value="vegetarien">Végétarien</option>
          <option value="vegan">Vegan</option>
          <option value="sans_gluten">Sans gluten</option>
        </select>
      </div>
      <div>
        <label class="block text-sm mb-1">Min. personnes</label>
        <input v-model="filters.min_personnes" type="number" class="w-full p-3 border rounded-lg" placeholder="Ex: 4" />
      </div>
      <div class="flex items-end">
        <button @click="fetchMenus" class="w-full bg-primary text-white py-3 rounded-lg">Filtrer</button>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <div v-for="menu in menus" :key="menu.id" class="bg-white rounded-2xl shadow overflow-hidden">
        <img :src="menu.images && menu.images[0] ? '/storage/' + menu.images[0] : '/images/placeholder.jpg'"
             class="w-full h-48 object-cover">
        <div class="p-6">
          <h3 class="font-bold text-xl">{{ menu.title }}</h3>
          <p class="text-gray-600 line-clamp-2 mt-2">{{ menu.description }}</p>

          <div class="flex justify-between mt-4 text-sm">
            <span class="bg-gray-100 px-3 py-1 rounded-full">{{ menu.theme }}</span>
            <span class="bg-gray-100 px-3 py-1 rounded-full">{{ menu.regime }}</span>
          </div>

          <div class="mt-6 flex justify-between items-end">
            <div>
              <span class="text-2xl font-bold">{{ menu.price }} €</span>
              <span class="text-sm text-gray-500"> / {{ menu.min_personnes }} pers.</span>
            </div>
            <button @click="viewDetail(menu)"
                    class="bg-accent text-white px-6 py-2 rounded-lg font-medium">
              Voir le détail
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const menus = ref([]);
const filters = ref({
  max_price: 200,
  theme: '',
  regime: '',
  min_personnes: ''
});

const router = useRouter();

const fetchMenus = async () => {
  const res = await axios.get('/v1/menus', { params: filters.value });
  menus.value = res.data;
};

const viewDetail = (menu) => {
  router.push(`/menu/${menu.id}`);
};

onMounted(fetchMenus);
</script>