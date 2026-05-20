<template>
  <div class="max-w-6xl mx-auto px-6 py-12">
    <button @click="goBack" class="flex items-center gap-2 text-[#C1813A] hover:underline mb-8 font-medium">
      ← Retour aux menus
    </button>

    <div v-if="menu" class="grid grid-cols-1 lg:grid-cols-2 gap-12">
      <div>
        <img
            v-if="menu.images?.length"
            :src="`/storage/${menu.images[0]}`"
            class="w-full rounded-3xl shadow-xl object-cover"
            alt="Menu {{ menu.title }}"
        >
        <img v-else src="/images/default-menu.jpg" class="w-full rounded-3xl shadow-xl" alt="Menu">
      </div>

      <div>
        <div class="flex gap-3 mb-6">
          <span class="px-5 py-1.5 bg-[#C1813A] text-white text-sm font-semibold rounded-full">{{ menu.theme }}</span>
          <span class="px-5 py-1.5 bg-[#E8C98A] text-[#3D2B1F] text-sm font-semibold rounded-full">{{ menu.regime }}</span>
        </div>

        <h1 class="text-4xl font-bold text-[#3D2B1F]">{{ menu.title }}</h1>
        <p class="text-[#7A6E62] mt-4 text-lg">{{ menu.description }}</p>

        <div class="mt-6 inline-flex items-center gap-2 px-5 py-2 bg-[#F5F0E8] rounded-2xl">
          <span class="text-sm font-medium text-[#3D2B1F]">Stock restant :</span>
          <span :class="menu.stock <= 5 ? 'text-red-600 font-bold' : 'text-[#C1813A] font-bold'" class="text-xl">
            {{ menu.stock }} places
          </span>
          <span v-if="menu.stock <= 5" class="text-red-600 text-sm">⚠️ Dernières places</span>
        </div>

        <div class="mt-12">
          <h3 class="font-semibold text-xl mb-6 text-[#3D2B1F]">Composition du menu</h3>
          <div v-for="plat in menu.plats" :key="plat.id" class="mb-8 p-6 bg-[#F9F6F1] rounded-2xl">
            <div class="flex justify-between items-start">
              <div>
                <strong class="text-lg">{{ plat.title }}</strong>
                <span class="ml-3 text-xs uppercase text-[#7A6E62]">({{ plat.type }})</span>
              </div>
              <div v-if="plat.allergens?.length" class="flex flex-wrap gap-2">
                <span v-for="a in plat.allergens" :key="a.id" class="text-xs px-3 py-1 bg-[#8B4A3C]/10 text-[#8B4A3C] rounded-full">
                  ⚠ {{ a.name }}
                </span>
              </div>
            </div>
            <p class="text-[#7A6E62] mt-3">{{ plat.description }}</p>
          </div>
        </div>

        <div class="mt-8 p-6 bg-red-50 border border-[#8B4A3C]/30 rounded-2xl">
          <h4 class="font-semibold text-[#8B4A3C]">⚠️ Conditions importantes</h4>
          <p class="text-[#8B4A3C] mt-2">{{ menu.conditions }}</p>
        </div>

        <div class="mt-12 flex flex-col sm:flex-row justify-between items-end gap-6">
          <div>
            <span class="text-5xl font-bold text-[#C1813A]">{{ menu.price }} €</span>
            <span class="text-[#7A6E62]"> / personne</span>
          </div>
          <button
              @click="goToOrder"
              :disabled="menu.stock <= 0"
              class="bg-[#C1813A] hover:bg-[#A76D2F] disabled:bg-gray-400 text-white px-12 py-5 rounded-2xl text-xl font-semibold transition">
            {{ menu.stock > 0 ? 'Commander ce menu' : 'Stock épuisé' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();
const menu = ref(null);

const fetchMenu = async () => {
  const res = await axios.get(`/v1/menus/${route.params.id}`);
  menu.value = res.data;
};

const goBack = () => router.push('/menus');
const goToOrder = () => {
  if (menu.value.stock > 0) {
    router.push(`/commande?menu_id=${menu.value.id}`);
  }
};

onMounted(fetchMenu);
</script>