<template>
  <div class="max-w-7xl mx-auto px-6 py-12">
    <h1 class="text-4xl font-bold text-[#3D2B1F] mb-10">Nos Menus</h1>

    <!-- Filtres -->
    <div class="bg-white p-8 rounded-3xl shadow mb-12 grid grid-cols-1 md:grid-cols-5 gap-6">
      <div>
        <label class="block text-xs font-medium text-[#7A6E62] mb-2">Prix maximum</label>
        <input v-model="filters.max_price" type="range" min="0" max="200" class="w-full accent-[#C1813A]" />
        <span class="text-sm font-medium text-[#C1813A]">{{ filters.max_price }} €</span>
      </div>
      <div>
        <label class="block text-xs font-medium text-[#7A6E62] mb-2">Thème</label>
        <select v-model="filters.theme" class="w-full p-3 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A] focus:outline-none">
          <option value="">Tous</option>
          <option value="Noel">Noël</option>
          <option value="Paques">Pâques</option>
          <option value="Classique">Classique</option>
          <option value="Evenement">Événement</option>
        </select>
      </div>
      <div>
        <label class="block text-xs font-medium text-[#7A6E62] mb-2">Régime</label>
        <select v-model="filters.regime" class="w-full p-3 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A] focus:outline-none">
          <option value="">Tous</option>
          <option value="classique">Classique</option>
          <option value="vegetarien">Végétarien</option>
          <option value="vegan">Vegan</option>
          <option value="sans_gluten">Sans gluten</option>
        </select>
      </div>
      <div>
        <label class="block text-xs font-medium text-[#7A6E62] mb-2">Min. personnes</label>
        <input v-model="filters.min_personnes" type="number" class="w-full p-3 border border-[#E8C98A] rounded-2xl" placeholder="Ex: 4" />
      </div>
      <div class="flex items-end">
        <button @click="fetchMenus"
                class="w-full bg-[#3D2B1F] hover:bg-[#2C2119] text-white py-3.5 rounded-2xl font-semibold transition">
          Filtrer
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <div v-for="menu in menus" :key="menu.id"
           class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300">
        <img :src="menu.images?.[0] ? `/storage/${menu.images[0]}` : '/images/placeholder.jpg'"
             class="w-full h-56 object-cover">
        <div class="p-6">
          <div class="flex gap-2 mb-4">
            <span class="px-4 py-1 text-xs font-semibold rounded-full bg-[#C1813A] text-white">{{ menu.theme }}</span>
            <span class="px-4 py-1 text-xs font-semibold rounded-full bg-[#E8C98A] text-[#3D2B1F]">{{ menu.regime }}</span>
          </div>
          <h3 class="font-bold text-xl mb-2 text-[#3D2B1F]">{{ menu.title }}</h3>
          <p class="text-[#7A6E62] line-clamp-2 mb-6">{{ menu.description }}</p>

          <div class="flex justify-between items-end">
            <div>
              <span class="text-3xl font-bold text-[#C1813A]">{{ menu.price }} €</span>
              <span class="text-sm text-[#7A6E62]"> / {{ menu.min_personnes }} pers.</span>
            </div>
            <button @click="viewDetail(menu)"
                    class="bg-[#C1813A] text-white px-6 py-3 rounded-2xl font-semibold hover:bg-[#A76D2F] transition">
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
const filters = ref({ max_price: 200, theme: '', regime: '', min_personnes: '' });
const router = useRouter();

const fetchMenus = async () => {
  const res = await axios.get('/v1/menus', { params: filters.value });
  menus.value = res.data;
};

const viewDetail = (menu) => router.push(`/menu/${menu.id}`);

onMounted(fetchMenus);
</script>