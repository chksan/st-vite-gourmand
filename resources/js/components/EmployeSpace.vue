<template>
  <div class="max-w-7xl mx-auto px-6 py-10">
    <h1 class="text-4xl font-bold mb-8">Espace Employé</h1>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
      <div class="bg-white p-6 rounded-3xl shadow h-fit">
        <nav class="space-y-2">
          <button @click="currentTab = 'orders'"
                  :class="{ 'bg-primary text-white': currentTab === 'orders' }"
                  class="w-full text-left px-5 py-4 rounded-2xl font-medium">📋 Commandes</button>
          <button @click="currentTab = 'menus'"
                  :class="{ 'bg-primary text-white': currentTab === 'menus' }"
                  class="w-full text-left px-5 py-4 rounded-2xl font-medium">🍽️ Menus</button>
          <button @click="currentTab = 'plats'"
                  :class="{ 'bg-primary text-white': currentTab === 'plats' }"
                  class="w-full text-left px-5 py-4 rounded-2xl font-medium">🍲 Plats</button>
          <button @click="currentTab = 'horaires'"
                  :class="{ 'bg-primary text-white': currentTab === 'horaires' }"
                  class="w-full text-left px-5 py-4 rounded-2xl font-medium">🕒 Horaires</button>
          <button @click="currentTab = 'reviews'"
                  :class="{ 'bg-primary text-white': currentTab === 'reviews' }"
                  class="w-full text-left px-5 py-4 rounded-2xl font-medium">⭐ Avis à valider</button>
        </nav>
      </div>

      <div class="lg:col-span-3 bg-white rounded-3xl shadow p-8 min-h-[600px]">

        <div v-if="currentTab === 'orders'">
          <h2 class="text-2xl font-semibold mb-6">Gestion des Commandes</h2>
          <div class="space-y-6">
            <div v-for="order in orders" :key="order.id" class="border rounded-2xl p-6">
              <div class="flex justify-between">
                <div>
                  <p class="font-medium">{{ order.menu.title }}</p>
                  <p class="text-sm text-gray-500">{{ order.user.name }} - {{ order.nb_personnes }} personnes</p>
                </div>
                <select v-model="order.status" @change="updateStatus(order)" class="border rounded-xl px-4 py-2 text-sm">
                  <option value="pending">En attente</option>
                  <option value="accepted">Accepté</option>
                  <option value="preparing">En préparation</option>
                  <option value="delivering">En cours de livraison</option>
                  <option value="delivered">Livré</option>
                  <option value="waiting_material">Attente matériel</option>
                  <option value="completed">Terminée</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="h-96 flex items-center justify-center text-gray-400">
          <div class="text-center">
            <p class="text-2xl">Section {{ currentTab.charAt(0).toUpperCase() + currentTab.slice(1) }}</p>
            <p class="mt-2">À implémenter</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const currentTab = ref('orders');
const orders = ref([]);

const fetchOrders = async () => {
  const res = await axios.get('/v1/employe/orders');
  orders.value = res.data.data || res.data;
};

const updateStatus = async (order) => {
  await axios.post(`/v1/employe/orders/${order.id}/status`, {
    status: order.status
  });
};

onMounted(fetchOrders);
</script>