<template>
  <div class="max-w-7xl mx-auto px-6 py-10">
    <h1 class="text-4xl font-bold mb-8">Espace Employé</h1>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
      <div class="bg-white p-6 rounded-3xl shadow h-fit sticky top-6">
        <nav class="space-y-2">
          <button v-for="tab in tabs" :key="tab.id"
                  @click="currentTab = tab.id"
                  :class="currentTab === tab.id ? 'bg-primary text-white' : 'hover:bg-gray-100'"
                  class="w-full text-left px-5 py-4 rounded-2xl font-medium transition-colors">
            {{ tab.icon }} {{ tab.label }}
          </button>
        </nav>
      </div>

      <div class="lg:col-span-3 bg-white rounded-3xl shadow p-8 min-h-[700px]">

        <div v-if="currentTab === 'orders'">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-semibold">Gestion des Commandes</h2>
          </div>

          <div class="flex flex-wrap gap-3 mb-6">
            <select v-model="orderFilters.status" @change="fetchOrders"
                    class="border rounded-xl px-4 py-2 text-sm">
              <option value="">Tous les statuts</option>
              <option v-for="(label, val) in statusMap" :key="val" :value="val">{{ label }}</option>
            </select>
            <input v-model="orderFilters.client" @input="fetchOrders"
                   placeholder="Rechercher un client…"
                   class="border rounded-xl px-4 py-2 text-sm w-56" />
          </div>

          <div class="space-y-4">
            <div v-for="order in orders" :key="order.id"
                 class="border rounded-2xl p-5 hover:border-primary/40 transition-colors">
              <div class="flex justify-between items-start">
                <div>
                  <p class="font-semibold">{{ order.menu?.title }}</p>
                  <p class="text-sm text-gray-500">
                    {{ order.user?.name }} · {{ order.nb_personnes }} pers. ·
                    {{ formatDate(order.date_prestation) }}
                  </p>
                </div>
                <span :class="statusClass(order.status)"
                      class="px-4 py-1 text-xs font-semibold rounded-full">
                  {{ statusLabel(order.status) }}
                </span>
              </div>

              <div class="flex gap-3 mt-4">
                <button @click="openOrderDetail(order)"
                        class="text-sm text-primary hover:underline">
                  Voir détails
                </button>
                <button v-if="order.status !== 'completed' && order.status !== 'cancelled'"
                        @click="openStatusModal(order)"
                        class="text-sm bg-primary text-white px-4 py-1 rounded-lg">
                  Changer statut
                </button>
              </div>
            </div>
            <p v-if="!orders.length" class="text-gray-400 text-center py-10">Aucune commande trouvée.</p>
          </div>

          <div v-if="ordersMeta.last_page > 1" class="flex justify-center gap-2 mt-6">
            <button v-for="p in ordersMeta.last_page" :key="p"
                    @click="fetchOrders(p)"
                    :class="p === ordersMeta.current_page ? 'bg-primary text-white' : 'bg-gray-100'"
                    class="w-9 h-9 rounded-lg text-sm font-medium">{{ p }}</button>
          </div>
        </div>

        <div v-if="currentTab === 'menus'">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-semibold">Gestion des Menus</h2>
            <button @click="openMenuForm()" class="bg-primary text-white px-5 py-2 rounded-xl text-sm font-medium">
              + Nouveau menu
            </button>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="menu in menus" :key="menu.id"
                 class="border rounded-2xl p-5">
              <div class="flex justify-between items-start">
                <div>
                  <p class="font-semibold text-lg">{{ menu.title }}</p>
                  <p class="text-sm text-gray-500 mt-1">{{ menu.theme }} · {{ menu.regime }}</p>
                  <p class="text-sm font-medium text-primary mt-1">{{ menu.price }} € / {{ menu.min_personnes }} pers.</p>
                  <p class="text-xs text-gray-400 mt-1">Stock : {{ menu.stock }}</p>
                </div>
                <div class="flex flex-col gap-2">
                  <button @click="openMenuForm(menu)"
                          class="text-xs border border-gray-300 px-3 py-1 rounded-lg hover:bg-gray-50">
                    Modifier
                  </button>
                  <button @click="confirmDeleteMenu(menu)"
                          class="text-xs border border-red-300 text-red-600 px-3 py-1 rounded-lg hover:bg-red-50">
                    Supprimer
                  </button>
                </div>
              </div>

              <div v-if="menu.plats?.length" class="mt-3 flex flex-wrap gap-1">
                <span v-for="plat in menu.plats" :key="plat.id"
                      class="text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded">
                  {{ plat.title }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <div v-if="currentTab === 'plats'">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-semibold">Plats & Allergènes</h2>
            <button @click="openPlatForm()" class="bg-primary text-white px-5 py-2 rounded-xl text-sm font-medium">
              + Nouveau plat
            </button>
          </div>

          <div class="mb-6 p-4 bg-red-50 rounded-2xl">
            <div class="flex items-center justify-between mb-3">
              <p class="font-semibold text-red-700 text-sm">Allergènes disponibles</p>
              <button @click="showAllergenForm = !showAllergenForm"
                      class="text-xs bg-red-600 text-white px-3 py-1 rounded-lg">+ Ajouter</button>
            </div>
            <div v-if="showAllergenForm" class="flex gap-2 mb-3">
              <input v-model="newAllergenName" placeholder="Nom de l'allergène"
                     class="border rounded-lg px-3 py-1.5 text-sm flex-1" />
              <button @click="storeAllergen" class="bg-red-600 text-white px-4 py-1.5 rounded-lg text-sm">
                Créer
              </button>
            </div>
            <div class="flex flex-wrap gap-2">
              <span v-for="a in allergens" :key="a.id"
                    class="text-xs bg-white border border-red-200 text-red-700 px-3 py-1 rounded-full flex items-center gap-1">
                {{ a.name }}
                <button @click="deleteAllergen(a)" class="hover:text-red-900">×</button>
              </span>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="plat in plats" :key="plat.id"
                 class="border rounded-2xl p-4">
              <div class="flex justify-between">
                <div>
                  <p class="font-medium">{{ plat.title }}</p>
                  <p class="text-sm text-gray-500 capitalize">{{ plat.type }}</p>
                </div>
                <div class="flex gap-2">
                  <button @click="openPlatForm(plat)" class="text-xs border px-3 py-1 rounded-lg hover:bg-gray-50">
                    Modifier
                  </button>
                  <button @click="deletePlat(plat)" class="text-xs border border-red-300 text-red-600 px-3 py-1 rounded-lg hover:bg-red-50">
                    Supprimer
                  </button>
                </div>
              </div>
              <div v-if="plat.allergens?.length" class="flex flex-wrap gap-1 mt-2">
                <span v-for="a in plat.allergens" :key="a.id"
                      class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded">
                  {{ a.name }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <div v-if="currentTab === 'horaires'">
          <h2 class="text-2xl font-semibold mb-6">Gestion des Horaires</h2>
          <div class="space-y-3">
            <div v-for="h in horaires" :key="h.id"
                 class="flex items-center gap-4 border rounded-2xl p-4">
              <p class="w-28 font-medium capitalize">{{ h.day }}</p>

              <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" v-model="h.is_closed" class="rounded" />
                Fermé
              </label>

              <template v-if="!h.is_closed">
                <input type="time" v-model="h.opening_time"
                       class="border rounded-lg px-3 py-1.5 text-sm" />
                <span class="text-gray-400">→</span>
                <input type="time" v-model="h.closing_time"
                       class="border rounded-lg px-3 py-1.5 text-sm" />
              </template>
              <span v-else class="text-gray-400 text-sm italic">Fermé ce jour</span>

              <button @click="saveHoraire(h)"
                      class="ml-auto bg-primary text-white px-4 py-1.5 rounded-xl text-sm">
                Enregistrer
              </button>
            </div>
          </div>
        </div>

        <div v-if="currentTab === 'reviews'">
          <h2 class="text-2xl font-semibold mb-6">Avis à valider</h2>
          <div v-if="reviews.length === 0" class="text-gray-400 text-center py-10">
            Aucun avis en attente de validation.
          </div>
          <div class="space-y-4">
            <div v-for="review in reviews" :key="review.id"
                 class="border rounded-2xl p-6">
              <div class="flex justify-between items-start">
                <div>
                  <p class="font-semibold">{{ review.user?.name }}</p>
                  <p class="text-xs text-gray-400 mb-1">
                    Commande : {{ review.order?.menu?.title }}
                  </p>
                  <div class="flex gap-0.5 mb-2">
                    <span v-for="i in 5" :key="i"
                          :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-200'"
                          class="text-lg">★</span>
                  </div>
                  <p class="text-sm text-gray-700">{{ review.comment }}</p>
                </div>
              </div>
              <div class="flex gap-3 mt-4">
                <button @click="validateReview(review)"
                        class="bg-green-600 text-white px-5 py-2 rounded-xl text-sm font-medium hover:bg-green-700">
                  ✓ Valider
                </button>
                <button @click="rejectReview(review)"
                        class="border border-red-300 text-red-600 px-5 py-2 rounded-xl text-sm font-medium hover:bg-red-50">
                  ✗ Refuser
                </button>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <Teleport to="body">
      <div v-if="selectedOrder" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-xl max-w-lg w-full p-8 max-h-[90vh] overflow-y-auto">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold">Détail de la commande</h3>
            <button @click="selectedOrder = null" class="text-gray-400 hover:text-gray-700 text-2xl">×</button>
          </div>
          <div class="space-y-3 text-sm">
            <div class="flex justify-between"><span class="text-gray-500">Client</span><span class="font-medium">{{ selectedOrder.user?.name }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Email</span><span>{{ selectedOrder.user?.email }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">GSM</span><span>{{ selectedOrder.user?.gsm }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Menu</span><span class="font-medium">{{ selectedOrder.menu?.title }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Personnes</span><span>{{ selectedOrder.nb_personnes }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Date prestation</span><span>{{ formatDate(selectedOrder.date_prestation) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Heure livraison</span><span>{{ selectedOrder.heure_livraison }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Lieu</span><span>{{ selectedOrder.adresse_livraison }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Prix menu</span><span>{{ selectedOrder.prix_menu }} €</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Livraison</span><span>{{ selectedOrder.prix_livraison }} €</span></div>
            <div class="flex justify-between font-semibold border-t pt-2"><span>Total</span><span>{{ (parseFloat(selectedOrder.prix_menu || 0) + parseFloat(selectedOrder.prix_livraison || 0)).toFixed(2) }} €</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Statut</span>
              <span :class="statusClass(selectedOrder.status)" class="px-3 py-0.5 rounded-full text-xs font-semibold">{{ statusLabel(selectedOrder.status) }}</span>
            </div>
          </div>

          <!-- Historique statuts -->
          <div v-if="selectedOrder.status_histories?.length" class="mt-6">
            <p class="font-semibold mb-3 text-sm">Historique</p>
            <div class="space-y-2">
              <div v-for="h in selectedOrder.status_histories" :key="h.id"
                   class="text-xs flex justify-between text-gray-600 border-l-2 border-primary/30 pl-3">
                <span>{{ statusLabel(h.status) }} — {{ h.comment }}</span>
                <span class="text-gray-400 ml-4 whitespace-nowrap">{{ formatDate(h.created_at, true) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Teleport>

    <Teleport to="body">
      <div v-if="statusModal.open" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-xl max-w-md w-full p-8">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold">Mettre à jour le statut</h3>
            <button @click="statusModal.open = false" class="text-gray-400 text-2xl">×</button>
          </div>

          <div class="space-y-4">
            <div>
              <label class="text-sm font-medium block mb-1">Nouveau statut</label>
              <select v-model="statusModal.status" class="w-full border rounded-xl px-4 py-2">
                <option v-for="(label, val) in statusMap" :key="val" :value="val">{{ label }}</option>
              </select>
            </div>

            <!-- Champs annulation -->
            <template v-if="statusModal.status === 'cancelled'">
              <div>
                <label class="text-sm font-medium block mb-1">Mode de contact client *</label>
                <select v-model="statusModal.contactMode" class="w-full border rounded-xl px-4 py-2">
                  <option value="">Sélectionner…</option>
                  <option value="gsm">Appel GSM</option>
                  <option value="email">Email</option>
                </select>
              </div>
              <div>
                <label class="text-sm font-medium block mb-1">Motif d'annulation *</label>
                <textarea v-model="statusModal.cancelReason" rows="3"
                          class="w-full border rounded-xl px-4 py-2 text-sm"
                          placeholder="Expliquez la raison de l'annulation…"></textarea>
              </div>
            </template>
          </div>

          <div class="flex gap-3 mt-6">
            <button @click="submitStatus"
                    class="flex-1 bg-primary text-white py-3 rounded-2xl font-medium">
              Confirmer
            </button>
            <button @click="statusModal.open = false"
                    class="flex-1 border py-3 rounded-2xl text-sm">
              Annuler
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <Teleport to="body">
      <div v-if="menuModal.open" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-xl max-w-2xl w-full p-8 max-h-[90vh] overflow-y-auto">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold">{{ menuModal.isEdit ? 'Modifier le menu' : 'Créer un menu' }}</h3>
            <button @click="menuModal.open = false" class="text-gray-400 text-2xl">×</button>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
              <label class="label">Titre</label>
              <input v-model="menuModal.form.title" class="input" />
            </div>
            <div class="col-span-2">
              <label class="label">Description</label>
              <textarea v-model="menuModal.form.description" rows="3" class="input"></textarea>
            </div>
            <div>
              <label class="label">Thème</label>
              <select v-model="menuModal.form.theme" class="input">
                <option value="noel">Noël</option>
                <option value="paques">Pâques</option>
                <option value="classique">Classique</option>
                <option value="evenement">Événement</option>
              </select>
            </div>
            <div>
              <label class="label">Régime</label>
              <select v-model="menuModal.form.regime" class="input">
                <option value="classique">Classique</option>
                <option value="vegetarien">Végétarien</option>
                <option value="vegan">Vegan</option>
                <option value="sans_gluten">Sans gluten</option>
              </select>
            </div>
            <div>
              <label class="label">Personnes minimum</label>
              <input v-model.number="menuModal.form.min_personnes" type="number" min="1" class="input" />
            </div>
            <div>
              <label class="label">Prix (€)</label>
              <input v-model.number="menuModal.form.price" type="number" step="0.01" min="0" class="input" />
            </div>
            <div>
              <label class="label">Stock</label>
              <input v-model.number="menuModal.form.stock" type="number" min="0" class="input" />
            </div>
            <div class="col-span-2">
              <label class="label">Conditions</label>
              <textarea v-model="menuModal.form.conditions" rows="2" class="input"
                        placeholder="Ex : commander 7 jours avant la prestation…"></textarea>
            </div>

            <!-- Sélection des plats -->
            <div class="col-span-2">
              <label class="label">Plats associés</label>
              <div class="grid grid-cols-2 gap-2 border rounded-xl p-3 max-h-40 overflow-y-auto">
                <label v-for="plat in plats" :key="plat.id" class="flex items-center gap-2 text-sm cursor-pointer">
                  <input type="checkbox" :value="plat.id" v-model="menuModal.form.plat_ids" />
                  <span class="capitalize">[{{ plat.type }}]</span> {{ plat.title }}
                </label>
              </div>
            </div>
          </div>

          <div class="flex gap-3 mt-6">
            <button @click="submitMenu" class="flex-1 bg-primary text-white py-3 rounded-2xl font-medium">
              {{ menuModal.isEdit ? 'Enregistrer' : 'Créer' }}
            </button>
            <button @click="menuModal.open = false" class="flex-1 border py-3 rounded-2xl text-sm">Annuler</button>
          </div>
        </div>
      </div>
    </Teleport>

    <Teleport to="body">
      <div v-if="platModal.open" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-xl max-w-md w-full p-8">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold">{{ platModal.isEdit ? 'Modifier le plat' : 'Nouveau plat' }}</h3>
            <button @click="platModal.open = false" class="text-gray-400 text-2xl">×</button>
          </div>

          <div class="space-y-4">
            <div>
              <label class="label">Type</label>
              <select v-model="platModal.form.type" class="input">
                <option value="entree">Entrée</option>
                <option value="plat">Plat</option>
                <option value="dessert">Dessert</option>
              </select>
            </div>
            <div>
              <label class="label">Titre</label>
              <input v-model="platModal.form.title" class="input" />
            </div>
            <div>
              <label class="label">Description</label>
              <textarea v-model="platModal.form.description" rows="2" class="input"></textarea>
            </div>
            <div>
              <label class="label">Allergènes</label>
              <div class="flex flex-wrap gap-2 border rounded-xl p-3">
                <label v-for="a in allergens" :key="a.id" class="flex items-center gap-1 text-sm cursor-pointer">
                  <input type="checkbox" :value="a.id" v-model="platModal.form.allergen_ids" />
                  {{ a.name }}
                </label>
              </div>
            </div>
          </div>

          <div class="flex gap-3 mt-6">
            <button @click="submitPlat" class="flex-1 bg-primary text-white py-3 rounded-2xl font-medium">
              {{ platModal.isEdit ? 'Enregistrer' : 'Créer' }}
            </button>
            <button @click="platModal.open = false" class="flex-1 border py-3 rounded-2xl text-sm">Annuler</button>
          </div>
        </div>
      </div>
    </Teleport>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const tabs = [
  { id: 'orders',   icon: '📋', label: 'Commandes' },
  { id: 'menus',    icon: '🍽️', label: 'Menus' },
  { id: 'plats',    icon: '🍲', label: 'Plats & Allergènes' },
  { id: 'horaires', icon: '🕒', label: 'Horaires' },
  { id: 'reviews',  icon: '⭐', label: 'Avis à valider' },
];
const currentTab = ref('orders');

const statusMap = {
  pending:          'En attente',
  accepted:         'Acceptée',
  preparing:        'En préparation',
  delivering:       'En cours de livraison',
  delivered:        'Livrée',
  waiting_material: 'Attente retour matériel',
  completed:        'Terminée',
  cancelled:        'Annulée',
};
const statusLabel  = (s) => statusMap[s] || s;
const statusClass  = (s) => ({
  pending:          'bg-yellow-100 text-yellow-700',
  accepted:         'bg-blue-100 text-blue-700',
  preparing:        'bg-orange-100 text-orange-700',
  delivering:       'bg-purple-100 text-purple-700',
  delivered:        'bg-green-100 text-green-700',
  waiting_material: 'bg-amber-100 text-amber-700',
  completed:        'bg-emerald-100 text-emerald-700',
  cancelled:        'bg-red-100 text-red-700',
}[s] || 'bg-gray-100 text-gray-700');

const formatDate = (d, withTime = false) => {
  if (!d) return '—';
  const date = new Date(d);
  if (withTime) return date.toLocaleString('fr-FR');
  return date.toLocaleDateString('fr-FR');
};

const orders      = ref([]);
const ordersMeta  = ref({ last_page: 1, current_page: 1 });
const orderFilters = ref({ status: '', client: '' });
const selectedOrder = ref(null);

const fetchOrders = async (page = 1) => {
  const params = new URLSearchParams({ page, ...orderFilters.value });
  const res = await axios.get(`/v1/employe/orders?${params}`);
  orders.value     = res.data.data || res.data;
  ordersMeta.value = res.data.meta || { last_page: 1, current_page: 1 };
};

const openOrderDetail = async (order) => {
  const res = await axios.get(`/v1/employe/orders/${order.id}`);
  selectedOrder.value = res.data;
};

const statusModal = ref({ open: false, order: null, status: '', contactMode: '', cancelReason: '' });

const openStatusModal = (order) => {
  statusModal.value = { open: true, order, status: order.status, contactMode: '', cancelReason: '' };
};

const submitStatus = async () => {
  const { order, status, contactMode, cancelReason } = statusModal.value;
  if (status === 'cancelled' && (!contactMode || !cancelReason.trim())) {
    alert('Veuillez renseigner le mode de contact et le motif d\'annulation.');
    return;
  }
  await axios.put(`/v1/employe/orders/${order.id}/status`, {
    status,
    contact_mode:  contactMode || undefined,
    cancel_reason: cancelReason || undefined,
  });
  statusModal.value.open = false;
  fetchOrders(ordersMeta.value.current_page);
};

const menus = ref([]);
const menuModal = ref({ open: false, isEdit: false, form: {} });

const fetchMenus = async () => {
  const res = await axios.get('/v1/employe/menus');
  menus.value = res.data;
};

const openMenuForm = (menu = null) => {
  menuModal.value = {
    open: true,
    isEdit: !!menu,
    form: menu
        ? { ...menu, plat_ids: menu.plats?.map(p => p.id) || [] }
        : { title: '', description: '', theme: 'classique', regime: 'classique',
          min_personnes: 2, price: '', stock: 10, conditions: '', plat_ids: [] },
  };
};

const submitMenu = async () => {
  const { isEdit, form } = menuModal.value;
  if (isEdit) {
    await axios.put(`/v1/employe/menus/${form.id}`, form);
  } else {
    await axios.post('/v1/employe/menus', form);
  }
  menuModal.value.open = false;
  fetchMenus();
};

const confirmDeleteMenu = async (menu) => {
  if (!confirm(`Supprimer le menu « ${menu.title} » ?`)) return;
  await axios.delete(`/v1/employe/menus/${menu.id}`);
  fetchMenus();
};

const plats      = ref([]);
const allergens  = ref([]);
const platModal  = ref({ open: false, isEdit: false, form: {} });
const showAllergenForm = ref(false);
const newAllergenName  = ref('');

const fetchPlats = async () => {
  const res = await axios.get('/v1/employe/plats');
  plats.value = res.data;
};

const fetchAllergens = async () => {
  const res = await axios.get('/v1/employe/allergens');
  allergens.value = res.data;
};

const openPlatForm = (plat = null) => {
  platModal.value = {
    open: true,
    isEdit: !!plat,
    form: plat
        ? { ...plat, allergen_ids: plat.allergens?.map(a => a.id) || [] }
        : { type: 'plat', title: '', description: '', allergen_ids: [] },
  };
};

const submitPlat = async () => {
  const { isEdit, form } = platModal.value;
  if (isEdit) {
    await axios.put(`/v1/employe/plats/${form.id}`, form);
  } else {
    await axios.post('/v1/employe/plats', form);
  }
  platModal.value.open = false;
  fetchPlats();
};

const deletePlat = async (plat) => {
  if (!confirm(`Supprimer le plat « ${plat.title} » ?`)) return;
  await axios.delete(`/v1/employe/plats/${plat.id}`);
  fetchPlats();
};

const storeAllergen = async () => {
  if (!newAllergenName.value.trim()) return;
  await axios.post('/v1/employe/allergens', { name: newAllergenName.value });
  newAllergenName.value = '';
  fetchAllergens();
};

const deleteAllergen = async (a) => {
  if (!confirm(`Supprimer l'allergène « ${a.name} » ?`)) return;
  await axios.delete(`/v1/employe/allergens/${a.id}`);
  fetchAllergens();
};

const horaires = ref([]);

const fetchHoraires = async () => {
  const res = await axios.get('/v1/employe/horaires');
  horaires.value = res.data;
};

const saveHoraire = async (h) => {
  await axios.put(`/v1/employe/horaires/${h.id}`, {
    opening_time: h.opening_time,
    closing_time: h.closing_time,
    is_closed: h.is_closed,
  });
};

const reviews = ref([]);

const fetchReviews = async () => {
  const res = await axios.get('/v1/employe/reviews');
  reviews.value = res.data;
};

const validateReview = async (review) => {
  await axios.post(`/v1/employe/reviews/${review.id}/validate`);
  fetchReviews();
};

const rejectReview = async (review) => {
  await axios.post(`/v1/employe/reviews/${review.id}/reject`);
  fetchReviews();
};

onMounted(() => {
  fetchOrders();
  fetchMenus();
  fetchPlats();
  fetchAllergens();
  fetchHoraires();
  fetchReviews();
});
</script>

