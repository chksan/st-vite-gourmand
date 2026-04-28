<template>
  <div class="max-w-6xl mx-auto px-6 py-8">
    <button @click="goBack" class="mb-6 text-primary flex items-center gap-2 hover:underline">
      ← Retour aux menus
    </button>

    <div v-if="menu" class="grid grid-cols-1 lg:grid-cols-2 gap-12">
      <!-- Left: Image -->
      <div>
        <img
            v-if="menu.images && menu.images.length"
            :src="`/storage/${menu.images[0]}`"
            class="w-full rounded-3xl shadow-xl"
            alt="Image du menu"
        >
      </div>

      <!-- Right: Details -->
      <div>
        <div class="flex gap-3 mb-4">
          <span class="px-4 py-1 bg-primary text-white text-sm rounded-full">{{ menu.theme }}</span>
          <span class="px-4 py-1 bg-gray-200 text-sm rounded-full">{{ menu.regime }}</span>
        </div>

        <h1 class="text-4xl font-bold">{{ menu.title }}</h1>
        <p class="text-gray-600 mt-3 text-lg leading-relaxed">{{ menu.description }}</p>

        <!-- Plats with Allergens -->
        <div class="mt-10">
          <h3 class="font-semibold mb-5 text-xl">Composition du menu</h3>

          <div v-for="plat in menu.plats" :key="plat.id" class="mb-6 p-5 bg-gray-50 rounded-2xl">
            <div class="flex justify-between items-start">
              <div>
                <strong class="text-lg">{{ plat.title }}</strong>
                <span class="ml-3 uppercase text-xs font-medium text-gray-500">({{ plat.type }})</span>
              </div>

              <!-- Allergens -->
              <div v-if="plat.allergens && plat.allergens.length" class="flex flex-wrap gap-2 justify-end">
                <span
                    v-for="allergen in plat.allergens"
                    :key="allergen.id"
                    class="text-xs px-3 py-1 bg-red-100 text-red-700 rounded-full font-medium">
                  {{ allergen.name }}
                </span>
              </div>
            </div>
            <p class="text-sm text-gray-600 mt-2">{{ plat.description }}</p>
          </div>
        </div>

        <!-- Important Conditions -->
        <div class="mt-8 p-6 bg-red-50 border border-red-200 rounded-2xl">
          <h4 class="font-semibold text-red-700 mb-2">⚠️ Conditions importantes</h4>
          <p class="text-red-700 leading-relaxed">{{ menu.conditions }}</p>
        </div>

        <!-- Price & Button -->
        <div class="mt-10 flex justify-between items-end">
          <div>
            <span class="text-5xl font-bold">{{ menu.price }} €</span>
            <span class="text-xl text-gray-500"> / {{ menu.min_personnes }} personnes minimum</span>
          </div>

          <button
              @click="goToOrder"
              class="bg-accent hover:bg-orange-600 text-white px-10 py-4 rounded-2xl text-xl font-semibold transition">
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