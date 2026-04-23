<template>
  <div class="max-w-6xl mx-auto px-6 py-10">
    <h1 class="text-4xl font-bold mb-8">Mon Espace Utilisateur</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="bg-white p-6 rounded-3xl shadow h-fit">
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

          <div class="flex flex-wrap gap-2 mb-8">
            <button @click="setFilter(null)"
                    :class="{ 'bg-primary text-white shadow': activeFilter === null }"
                    class="px-6 py-3 rounded-2xl text-sm font-medium transition">
              Toutes les commandes
            </button>
            <button @click="setFilter('pending')"
                    :class="{ 'bg-primary text-white shadow': activeFilter === 'pending' }"
                    class="px-6 py-3 rounded-2xl text-sm font-medium transition">
              En cours
            </button>
            <button @click="setFilter('completed')"
                    :class="{ 'bg-primary text-white shadow': activeFilter === 'completed' }"
                    class="px-6 py-3 rounded-2xl text-sm font-medium transition">
              Terminées
            </button>
            <button @click="setFilter('cancelled')"
                    :class="{ 'bg-primary text-white shadow': activeFilter === 'cancelled' }"
                    class="px-6 py-3 rounded-2xl text-sm font-medium transition">
              Annulées
            </button>
          </div>

          <div v-if="orders.data && orders.data.length === 0" class="text-gray-500 py-16 text-center">
            Aucune commande trouvée.
          </div>

          <div v-else class="space-y-6">
            <div v-for="order in orders.data" :key="order.id" class="border rounded-2xl p-6 hover:shadow transition">
              <div class="flex justify-between">
                <div>
                  <p class="font-medium">{{ order.menu.title }}</p>
                  <p class="text-sm text-gray-500">{{ order.nb_personnes }} personnes • {{ order.total_price }} €</p>
                </div>
                <span :class="statusClass(order.status)" class="px-5 py-1 text-sm font-medium rounded-full">
                  {{ statusLabel(order.status) }}
                </span>
              </div>
              <p class="text-xs text-gray-400 mt-3">{{ order.delivery_date }} à {{ order.delivery_time }}</p>

              <button v-if="order.status === 'pending'"
                      @click="cancelOrder(order.id)"
                      class="mt-4 text-red-600 hover:underline text-sm">
                Annuler la commande
              </button>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="orders.last_page > 1" class="flex justify-center gap-3 mt-10">
            <button @click="goToPage(orders.current_page - 1)"
                    :disabled="orders.current_page === 1"
                    class="px-6 py-3 border rounded-2xl disabled:opacity-50">← Précédent</button>

            <span class="px-6 py-3">Page {{ orders.current_page }} / {{ orders.last_page }}</span>

            <button @click="goToPage(orders.current_page + 1)"
                    :disabled="orders.current_page === orders.last_page"
                    class="px-6 py-3 border rounded-2xl disabled:opacity-50">Suivant →</button>
          </div>
        </div>

        <div v-if="tab === 'profile'" class="bg-white rounded-3xl shadow p-8">
          <h2 class="text-2xl font-semibold mb-6">Mes informations</h2>
          <p class="text-gray-500">Fonctionnalité à venir...</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const tab = ref('orders');
const orders = ref({ data: [], current_page: 1, last_page: 1 });
const activeFilter = ref(null);

const fetchOrders = async (page = 1) => {
  const params = { page: page };
  if (activeFilter.value) params.status = activeFilter.value;

  const res = await axios.get('/v1/orders', { params });
  orders.value = res.data;
};

const setFilter = (filter) => {
  activeFilter.value = filter;
  fetchOrders(1);
};

const goToPage = (page) => {
  if (page < 1 || page > orders.value.last_page) return;
  fetchOrders(page);
};

const statusLabel = (status) => {
  const map = { pending: 'En attente', accepted: 'Accepté', preparing: 'En préparation', delivering: 'En livraison', delivered: 'Livré', completed: 'Terminée', cancelled: 'Annulée' };
  return map[status] || status;
};

const statusClass = (status) => {
  const map = { pending: 'bg-yellow-100 text-yellow-700', accepted: 'bg-blue-100 text-blue-700', completed: 'bg-green-100 text-green-700', cancelled: 'bg-red-100 text-red-700' };
  return map[status] || 'bg-gray-100 text-gray-700';
};

const cancelOrder = async (id) => {
  if (confirm('Annuler cette commande ?')) {
    await axios.post(`/v1/orders/${id}/cancel`);
    fetchOrders(orders.value.current_page);
  }
};

onMounted(() => fetchOrders());
</script>