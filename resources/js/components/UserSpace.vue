<template>
  <div class="max-w-6xl mx-auto px-6 py-12">
    <h1 class="text-4xl font-bold text-[#3D2B1F] mb-10">Mon Espace Utilisateur</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

      <!-- Sidebar -->
      <div class="bg-white p-6 rounded-3xl shadow h-fit sticky top-6">
        <nav class="space-y-2">
          <button @click="tab = 'orders'"
                  :class="{ 'bg-[#3D2B1F] text-white': tab === 'orders' }"
                  class="w-full text-left px-5 py-4 rounded-2xl font-medium transition">
            📋 Mes commandes
          </button>
          <button @click="tab = 'profile'"
                  :class="{ 'bg-[#3D2B1F] text-white': tab === 'profile' }"
                  class="w-full text-left px-5 py-4 rounded-2xl font-medium transition">
            👤 Mes informations
          </button>
        </nav>
      </div>

      <!-- Contenu principal -->
      <div class="lg:col-span-2 space-y-6">

        <!-- MES COMMANDES -->
        <div v-if="tab === 'orders'" class="bg-white rounded-3xl shadow p-8">
          <h2 class="text-2xl font-semibold mb-6 text-[#3D2B1F]">Mes commandes</h2>

          <div class="flex flex-wrap gap-2 mb-8">
            <button v-for="f in filters" :key="f.value"
                    @click="setFilter(f.value)"
                    :class="activeFilter === f.value ? 'bg-[#3D2B1F] text-white' : 'bg-[#F5F0E8] hover:bg-[#E8C98A]'"
                    class="px-5 py-2 rounded-2xl text-sm font-medium transition">
              {{ f.label }}
            </button>
          </div>

          <div v-if="!orders.data?.length" class="py-16 text-center text-[#7A6E62]">
            Aucune commande trouvée.
          </div>

          <div class="space-y-6">
            <div v-for="order in orders.data" :key="order.id"
                 class="border border-[#E8C98A] rounded-2xl p-6 hover:border-[#C1813A] transition">

              <div class="flex justify-between items-start">
                <div>
                  <p class="font-semibold text-lg">{{ order.menu?.title }}</p>
                  <p class="text-sm text-[#7A6E62] mt-1">
                    {{ order.nb_personnes }} personnes ·
                    {{ formatDate(order.delivery_date) }} à {{ order.delivery_time }}
                  </p>
                  <p class="text-sm text-[#7A6E62]">{{ order.delivery_address }}</p>
                </div>
                <span :class="statusClass(order.status)" class="px-4 py-1 text-xs font-semibold rounded-full shrink-0">
                  {{ statusLabel(order.status) }}
                </span>
              </div>

              <div class="mt-3 flex gap-4 text-sm text-[#7A6E62]">
                <span>Total : <strong class="text-[#3D2B1F]">{{ order.total_price }} €</strong></span>
                <span v-if="parseFloat(order.delivery_fee) > 0" class="text-[#7A6E62]">
                  (dont {{ order.delivery_fee }} € livraison)
                </span>
              </div>

              <div class="mt-5 flex flex-wrap gap-3">
                <button v-if="canEdit(order)" @click="openEditModal(order)"
                        class="text-sm border border-[#C1813A] text-[#C1813A] px-4 py-2 rounded-xl hover:bg-[#C1813A]/5">
                  ✏️ Modifier
                </button>
                <button v-if="canEdit(order)" @click="cancelOrder(order)"
                        class="text-sm border border-red-300 text-red-600 px-4 py-2 rounded-xl hover:bg-red-50">
                  ✕ Annuler
                </button>
                <button v-if="canTrack(order)" @click="openTracking(order)"
                        class="text-sm border border-[#7A6E62] px-4 py-2 rounded-xl hover:bg-[#F5F0E8]">
                  🔍 Suivre la commande
                </button>
                <button v-if="order.status === 'completed' && !order.review"
                        @click="openReviewModal(order)"
                        class="text-sm bg-[#C1813A] text-white px-4 py-2 rounded-xl hover:bg-[#A76D2F]">
                  ⭐ Laisser un avis
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- MON PROFIL -->
        <div v-if="tab === 'profile'" class="bg-white rounded-3xl shadow p-8">
          <h2 class="text-2xl font-semibold mb-6 text-[#3D2B1F]">Mes informations personnelles</h2>

          <div class="max-w-md space-y-5">
            <!-- Prénom & Nom -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-[#7A6E62] mb-2">Prénom</label>
                <input
                    v-model="profileForm.first_name"
                    type="text"
                    class="w-full px-5 py-4 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A] focus:outline-none text-base"
                />
                <p v-if="profileErrors.first_name" class="text-red-600 text-sm mt-1">{{
                    profileErrors.first_name[0]
                  }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-[#7A6E62] mb-2">Nom</label>
                <input
                    v-model="profileForm.last_name"
                    type="text"
                    class="w-full px-5 py-4 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A] focus:outline-none text-base"
                />
                <p v-if="profileErrors.last_name" class="text-red-600 text-sm mt-1">{{ profileErrors.last_name[0] }}</p>
              </div>
            </div>

            <!-- Email -->
            <div>
              <label class="block text-sm font-medium text-[#7A6E62] mb-2">Email</label>
              <input
                  v-model="profileForm.email"
                  type="email"
                  class="w-full px-5 py-4 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A] focus:outline-none text-base"
              />
              <p v-if="profileErrors.email" class="text-red-600 text-sm mt-1">{{ profileErrors.email[0] }}</p>
            </div>

            <!-- Téléphone -->
            <div>
              <label class="block text-sm font-medium text-[#7A6E62] mb-2">Numéro de Téléphone</label>
              <input
                  v-model="profileForm.gsm"
                  type="tel"
                  class="w-full px-5 py-4 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A] focus:outline-none text-base"
              />
              <p v-if="profileErrors.gsm" class="text-red-600 text-sm mt-1">{{ profileErrors.gsm[0] }}</p>
            </div>

            <!-- Adresse -->
            <div>
              <label class="block text-sm font-medium text-[#7A6E62] mb-2">Adresse postale</label>
              <textarea
                  v-model="profileForm.address"
                  rows="3"
                  class="w-full px-5 py-4 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A] focus:outline-none text-base"
              ></textarea>
              <p v-if="profileErrors.address" class="text-red-600 text-sm mt-1">{{ profileErrors.address[0] }}</p>
            </div>

            <button
                @click="updateProfile"
                :disabled="profileLoading"
                class="w-full bg-[#C1813A] hover:bg-[#A76D2F] text-white py-4 rounded-2xl font-semibold transition disabled:opacity-70">
              {{ profileLoading ? 'Enregistrement en cours...' : 'Enregistrer les modifications' }}
            </button>

            <p v-if="profileSuccess" class="text-green-600 text-center font-medium">{{ profileSuccess }}</p>
            <p v-if="profileErrors.general" class="text-red-600 text-center">{{ profileErrors.general }}</p>
          </div>
        </div>
      </div>
    </div>
    <Teleport to="body">
      <div v-if="trackingModal.open" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold">Suivi de commande</h3>
            <button @click="trackingModal.open = false" class="text-3xl text-[#7A6E62]">×</button>
          </div>
          <p class="text-[#7A6E62] mb-6">{{ trackingModal.order?.menu?.title }}</p>

          <div class="relative pl-6">
            <div class="absolute left-2 top-0 bottom-0 w-0.5 bg-[#E8C98A]"></div>
            <div v-for="(h, i) in trackingModal.history" :key="h.id" class="relative mb-6">
              <div class="absolute -left-4 top-1 w-3 h-3 rounded-full border-2"
                   :class="i === 0 ? 'bg-[#C1813A] border-[#C1813A]' : 'bg-white border-[#E8C98A]'"></div>
              <p class="font-medium">{{ statusLabel(h.status) }}</p>
              <p v-if="h.comment" class="text-sm text-[#7A6E62]">{{ h.comment }}</p>
              <p class="text-xs text-[#7A6E62]">{{ formatDate(h.created_at, true) }}</p>
            </div>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Edit Modal -->
    <Teleport to="body">
      <div v-if="editModal.open" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-8">
          <h3 class="text-xl font-bold mb-6">Modifier la commande</h3>
          <div class="space-y-4">
            <div>
              <label class="block text-sm mb-2">Nombre de personnes</label>
              <input v-model.number="editModal.form.nb_personnes" type="number"
                     class="w-full p-4 border border-[#E8C98A] rounded-2xl"/>
            </div>
            <div>
              <label class="block text-sm mb-2">Date de prestation</label>
              <input v-model="editModal.form.delivery_date" type="date"
                     class="w-full p-4 border border-[#E8C98A] rounded-2xl"/>
            </div>
            <div>
              <label class="block text-sm mb-2">Heure de livraison</label>
              <input v-model="editModal.form.delivery_time" type="time"
                     class="w-full p-4 border border-[#E8C98A] rounded-2xl"/>
            </div>
            <div>
              <label class="block text-sm mb-2">Adresse de livraison</label>
              <input v-model="editModal.form.delivery_address" class="w-full p-4 border border-[#E8C98A] rounded-2xl"/>
            </div>
          </div>
          <div class="flex gap-3 mt-8">
            <button @click="submitEdit" class="flex-1 bg-[#C1813A] text-white py-3 rounded-2xl font-semibold">
              Enregistrer
            </button>
            <button @click="editModal.open = false" class="flex-1 border py-3 rounded-2xl">Annuler</button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Review Modal -->
    <Teleport to="body">
      <div v-if="reviewModal.open" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8">
          <div class="flex justify-between mb-6">
            <h3 class="text-xl font-bold">Laisser un avis</h3>
            <button @click="reviewModal.open = false" class="text-3xl text-[#7A6E62]">×</button>
          </div>
          <div class="flex gap-1 text-5xl mb-6">
            <button v-for="star in 5" :key="star" @click="reviewModal.form.rating = star"
                    :class="star <= reviewModal.form.rating ? 'text-[#C1813A]' : 'text-gray-200'">
              ★
            </button>
          </div>
          <textarea v-model="reviewModal.form.comment" rows="5"
                    class="w-full p-4 border border-[#E8C98A] rounded-2xl"
                    placeholder="Votre commentaire..."></textarea>
          <div class="flex gap-3 mt-6">
            <button @click="submitReview" :disabled="reviewModal.form.rating === 0"
                    class="flex-1 bg-[#C1813A] text-white py-3 rounded-2xl font-semibold">Envoyer l'avis
            </button>
            <button @click="reviewModal.open = false" class="flex-1 border py-3 rounded-2xl">Annuler</button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import {ref, onMounted} from 'vue';
import {useAuth} from '../Helpers/auth';

const {user} = useAuth();
const tab = ref('orders');

const statusLabel = (s) => ({
  pending: 'En attente', accepted: 'Acceptée', preparing: 'En préparation',
  delivering: 'En cours de livraison', delivered: 'Livrée', completed: 'Terminée',
  cancelled: 'Annulée'
}[s] || s);

const statusClass = (s) => ({
  pending: 'bg-yellow-100 text-yellow-700',
  accepted: 'bg-blue-100 text-blue-700',
  preparing: 'bg-orange-100 text-orange-700',
  delivering: 'bg-purple-100 text-purple-700',
  delivered: 'bg-green-100 text-green-700',
  completed: 'bg-emerald-100 text-emerald-700',
  cancelled: 'bg-red-100 text-red-700'
}[s] || 'bg-gray-100 text-gray-700');

const formatDate = (d, withTime = false) => {
  if (!d) return '—';
  const date = new Date(d);
  return withTime ? date.toLocaleString('fr-FR') : date.toLocaleDateString('fr-FR');
};

const orders = ref({data: [], current_page: 1, last_page: 1});
const activeFilter = ref(null);

const filters = [
  {label: 'Toutes', value: null},
  {label: 'En cours', value: 'pending'},
  {label: 'Terminées', value: 'completed'},
  {label: 'Annulées', value: 'cancelled'},
];

const fetchOrders = async (page = 1) => {
  const params = {page};
  if (activeFilter.value) params.status = activeFilter.value;
  const res = await axios.get('/v1/orders', {params});
  orders.value = res.data;
};

const setFilter = (f) => {
  activeFilter.value = f;
  fetchOrders(1);
};

const canEdit = (order) => ['pending'].includes(order.status);
const canTrack = (order) => !['pending', 'cancelled'].includes(order.status);

const cancelOrder = async (order) => {
  if (!confirm(`Annuler la commande « ${order.menu?.title} » ?`)) return;
  await axios.post(`/v1/orders/${order.id}/cancel`);
  fetchOrders(orders.value.current_page);
};

// Modals
const trackingModal = ref({open: false, order: null, history: []});
const openTracking = async (order) => {
  const res = await axios.get(`/v1/orders/${order.id}/tracking`);
  trackingModal.value = {open: true, order, history: res.data};
};

const editModal = ref({open: false, order: null, form: {}});
const openEditModal = (order) => {
  editModal.value = {
    open: true,
    order,
    form: {
      nb_personnes: order.nb_personnes,
      delivery_date: order.delivery_date?.split('T')[0] ?? '',
      delivery_time: order.delivery_time?.slice(0, 5) ?? '',
      delivery_address: order.delivery_address,
    }
  };
};

const submitEdit = async () => {
  const {order, form} = editModal.value;
  await axios.put(`/v1/orders/${order.id}`, form);
  editModal.value.open = false;
  fetchOrders(orders.value.current_page);
};

const reviewModal = ref({open: false, order: null, form: {rating: 0, comment: ''}});
const openReviewModal = (order) => {
  reviewModal.value = {open: true, order, form: {rating: 0, comment: ''}};
};

const submitReview = async () => {
  const {order, form} = reviewModal.value;
  if (form.rating === 0) return;
  await axios.post(`/v1/orders/${order.id}/review`, form);
  reviewModal.value.open = false;
  fetchOrders(orders.value.current_page);
};

const profileForm = ref({
  first_name: '',
  last_name: '',
  email: '',
  gsm: '',
  address: ''
});

const profileErrors = ref({});
const profileSuccess = ref('');
const profileLoading = ref(false);

const updateProfile = async () => {
  profileSuccess.value = '';
  profileErrors.value = {};
  profileLoading.value = true;

  try {
    const fullName = `${profileForm.value.first_name} ${profileForm.value.last_name}`.trim();
    const res = await axios.post('/v1/profile', {
      name: fullName,
      email: profileForm.value.email,
      phone: profileForm.value.gsm,
      address: profileForm.value.address
    });
    profileSuccess.value = '✅ Informations mises à jour avec succès !';

    if (user.value && res.data.user) {
      Object.assign(user.value, res.data.user);
    }
  } catch (err) {
    if (err.response?.data?.errors) {
      profileErrors.value = err.response.data.errors;
    } else if (err.response?.data?.message) {
      profileErrors.value.general = err.response.data.message;
    } else {
      profileErrors.value.general = "Une erreur est survenue lors de la mise à jour.";
    }
  } finally {
    profileLoading.value = false;
  }
};

onMounted(() => {
  if (user.value?.name) {
    const parts = user.value.name.trim().split(' ');
    profileForm.value.first_name = parts[0] || '';
    profileForm.value.last_name = parts.slice(1).join(' ') || '';
    profileForm.value.email = user.value.email || '';
    profileForm.value.gsm = user.value.phone || '';
    profileForm.value.address = user.value.address || '';
  }
  fetchOrders();
});
</script>