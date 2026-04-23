<template>
  <div class="max-w-6xl mx-auto px-6 py-10">
    <h1 class="text-4xl font-bold mb-8">Mon Espace Utilisateur</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="bg-white p-6 rounded-3xl shadow">
        <nav class="space-y-2">
          <button @click="tab = 'orders'"
                  :class="{ 'bg-primary text-white': tab === 'orders' }"
                  class="w-full text-left px-5 py-4 rounded-2xl font-medium">
            📋 Mes commandes
          </button>
          <button @click="tab = 'profile'"
                  :class="{ 'bg-primary text-white': tab === 'profile' }"
                  class="w-full text-left px-5 py-4 rounded-2xl font-medium">
            👤 Mes informations
          </button>
        </nav>
      </div>

      <div class="lg:col-span-2">
        <div v-if="tab === 'orders'" class="bg-white rounded-3xl shadow p-8">
          <h2 class="text-2xl font-semibold mb-6">Mes commandes</h2>

          <div v-if="orders.length === 0" class="text-gray-500 py-12 text-center">
            Vous n'avez pas encore passé de commande.
          </div>

          <div v-else class="space-y-6">
            <div v-for="order in orders" :key="order.id" class="border rounded-2xl p-6">
              <div class="flex justify-between items-start">
                <div>
                  <p class="font-medium text-lg">{{ order.menu.title }}</p>
                  <p class="text-sm text-gray-500">
                    {{ order.nb_personnes }} personnes • {{ order.total_price }} €
                  </p>
                  <p class="text-xs text-gray-400 mt-1">{{ order.delivery_date }} à {{ order.delivery_time }}</p>
                </div>

                <span :class="statusClass(order.status)" class="px-5 py-1 text-sm font-medium rounded-full whitespace-nowrap">
                  {{ statusLabel(order.status) }}
                </span>
              </div>

              <button
                  v-if="order.status === 'pending'"
                  @click="cancelOrder(order.id)"
                  class="mt-5 text-red-600 hover:text-red-700 text-sm font-medium flex items-center gap-1">
                ✕ Annuler la commande
              </button>
            </div>
          </div>
        </div>

        <!-- Profile tab (placeholder for now) -->
        <div v-if="tab === 'profile'" class="bg-white rounded-3xl shadow p-8">
          <h2 class="text-2xl font-semibold mb-6">Mes informations personnelles</h2>
          <p class="text-gray-500">Fonctionnalité en cours de développement...</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const tab = ref('orders');
const orders = ref([]);

const fetchOrders = async () => {
  const res = await axios.get('/v1/orders');
  orders.value = res.data;
};

// Labels
const statusLabel = (status) => {
  const labels = {
    pending: 'En attente',
    accepted: 'Accepté',
    preparing: 'En préparation',
    delivering: 'En cours de livraison',
    delivered: 'Livré',
    waiting_material: 'En attente du retour de matériel',
    completed: 'Terminée',
    cancelled: 'Annulée'
  };
  return labels[status] || status;
};

// Color for status badge
const statusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-700',
    accepted: 'bg-blue-100 text-blue-700',
    preparing: 'bg-orange-100 text-orange-700',
    delivering: 'bg-purple-100 text-purple-700',
    delivered: 'bg-green-100 text-green-700',
    waiting_material: 'bg-amber-100 text-amber-700',
    completed: 'bg-emerald-100 text-emerald-700',
    cancelled: 'bg-red-100 text-red-700'
  };
  return classes[status] || 'bg-gray-100 text-gray-700';
};

const cancelOrder = async (id) => {
  if (confirm('Voulez-vous vraiment annuler cette commande ?')) {
    await axios.post(`/v1/orders/${id}/cancel`);
    fetchOrders();
  }
};

onMounted(fetchOrders);
</script>