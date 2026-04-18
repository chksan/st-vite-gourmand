<template>
  <div class="max-w-6xl mx-auto px-6 py-8">
    <button @click="goBack" class="mb-6 text-primary flex items-center gap-2 hover:underline">
      ← Retour aux menus
    </button>

    <div v-if="menu" class="grid grid-cols-1 lg:grid-cols-2 gap-12">
      <div>
        <img
            v-if="menu.images && menu.images.length"
            :src="`/storage/${menu.images[0]}`"
            class="w-full rounded-3xl shadow-xl"
        >
      </div>

      <div>
        <div class="flex gap-3">
          <span class="px-4 py-1 bg-primary text-white text-sm rounded-full">{{ menu.theme }}</span>
          <span class="px-4 py-1 bg-gray-200 text-sm rounded-full">{{ menu.regime }}</span>
        </div>

        <h1 class="text-4xl font-bold mt-4">{{ menu.title }}</h1>
        <p class="text-gray-600 mt-2 text-lg">{{ menu.description }}</p>

        <div class="mt-8">
          <h3 class="font-semibold mb-4">Plats inclus :</h3>
          <div v-for="plat in menu.plats" :key="plat.id" class="mb-4 p-4 bg-gray-50 rounded-2xl">
            <strong>{{ plat.title }}</strong>
            <span class="text-xs uppercase ml-2 text-gray-500">({{ plat.type }})</span>
            <p class="text-sm text-gray-600">{{ plat.description }}</p>
          </div>
        </div>

        <div class="mt-8 p-6 bg-red-50 border border-red-200 rounded-2xl">
          <h4 class="font-semibold text-red-700">Conditions importantes :</h4>
          <p class="text-red-700">{{ menu.conditions }}</p>
        </div>

        <div class="mt-10 flex justify-between items-end">
          <div>
            <span class="text-5xl font-bold">{{ menu.price }} €</span>
            <span class="text-xl text-gray-500"> pour {{ menu.min_personnes }} personnes minimum</span>
          </div>

          <button
              @click="goToOrder"
              class="bg-accent hover:bg-orange-600 text-white px-10 py-4 rounded-2xl text-xl font-semibold transition"
          >
            Commander ce menu
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
const goToOrder = () => router.push(`/commande?menu_id=${menu.value.id}`);

onMounted(fetchMenu);
</script>