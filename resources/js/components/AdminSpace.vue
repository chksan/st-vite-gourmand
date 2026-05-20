<template>
  <div class="max-w-7xl mx-auto px-4 md:px-6 py-8 md:py-12">
    <h1 class="text-3xl md:text-4xl font-bold text-[#3D2B1F] mb-2">Espace Administrateur</h1>
    <p class="text-[#7A6E62] mb-10">Gestion des employés et statistiques</p>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
      <!-- Sidebar -->
      <div class="bg-white p-6 rounded-3xl shadow h-fit sticky top-6">
        <nav class="space-y-2">
          <button v-for="t in tabs" :key="t.id"
                  @click="currentTab = t.id"
                  :class="currentTab === t.id ? 'bg-[#3D2B1F] text-white' : 'hover:bg-[#F5F0E8]'"
                  class="w-full text-left px-5 py-4 rounded-2xl font-medium transition">
            {{ t.icon }} {{ t.label }}
          </button>
        </nav>

        <div class="mt-8 pt-6 border-t">
          <router-link to="/employe"
                       class="block w-full text-center py-3 border border-[#C1813A] text-[#C1813A] rounded-2xl hover:bg-[#C1813A] hover:text-white transition">
            → Espace Employé
          </router-link>
        </div>
      </div>

      <!-- Main Content -->
      <div class="lg:col-span-3 space-y-8">

        <!-- EMPLOYES -->
        <div v-if="currentTab === 'employees'" class="bg-white rounded-3xl shadow p-6 md:p-8">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <h2 class="text-2xl font-semibold text-[#3D2B1F]">Gestion des Employés</h2>
            <button @click="openCreateModal"
                    class="bg-[#C1813A] hover:bg-[#A76D2F] text-white px-6 py-3 rounded-2xl font-medium whitespace-nowrap transition">
              + Nouvel employé
            </button>
          </div>

          <div v-if="!employees.length" class="text-center py-16 text-[#7A6E62]">
            Aucun employé enregistré.
          </div>

          <div class="space-y-4">
            <div v-for="emp in employees" :key="emp.id"
                 class="border border-[#E8C98A] rounded-2xl p-5 hover:border-[#C1813A] transition flex flex-col sm:flex-row sm:items-center gap-4">

              <div class="flex-1 min-w-0">
                <p class="font-semibold text-[#3D2B1F] truncate">{{ emp.name }}</p>
                <p class="text-sm text-[#7A6E62] truncate">{{ emp.email }}</p>
                <p class="text-xs text-[#7A6E62]">Créé le {{ formatDate(emp.created_at) }}</p>
              </div>

              <div class="flex items-center gap-3 flex-wrap">
        <span :class="emp.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
              class="text-xs font-medium px-4 py-1.5 rounded-full whitespace-nowrap">
          {{ emp.is_active ? 'Actif' : 'Désactivé' }}
        </span>

                <button @click="toggleEmployee(emp)"
                        class="text-sm border border-gray-300 px-5 py-2 rounded-xl hover:bg-gray-50 whitespace-nowrap">
                  {{ emp.is_active ? 'Désactiver' : 'Réactiver' }}
                </button>

                <button @click="deleteEmployee(emp)"
                        class="text-sm border border-red-300 text-red-600 px-5 py-2 rounded-xl hover:bg-red-50 whitespace-nowrap">
                  Supprimer
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- STATISTIQUES -->
        <div v-if="currentTab === 'stats'" class="space-y-8">
          <!-- Filtres -->
          <div class="bg-white rounded-3xl shadow p-6">
            <h2 class="text-lg font-semibold mb-4 text-[#3D2B1F]">Filtres</h2>
            <div class="flex flex-wrap gap-4">
              <div>
                <label class="text-xs text-[#7A6E62] block mb-1">Menu</label>
                <select v-model="statsFilters.menu_id" @change="fetchStats" class="border border-[#E8C98A] rounded-2xl px-4 py-2 text-sm">
                  <option value="">Tous les menus</option>
                  <option v-for="m in statMenus" :key="m.menu_id" :value="m.menu_id">{{ m.menu_title }}</option>
                </select>
              </div>
              <div>
                <label class="text-xs text-[#7A6E62] block mb-1">Du</label>
                <input type="date" v-model="statsFilters.from" @change="fetchStats" class="border border-[#E8C98A] rounded-2xl px-4 py-2" />
              </div>
              <div>
                <label class="text-xs text-[#7A6E62] block mb-1">Au</label>
                <input type="date" v-model="statsFilters.to" @change="fetchStats" class="border border-[#E8C98A] rounded-2xl px-4 py-2" />
              </div>
              <div class="flex items-end">
                <button @click="resetFilters" class="border border-[#E8C98A] px-4 py-2 rounded-2xl text-sm hover:bg-[#F5F0E8]">
                  Réinitialiser
                </button>
              </div>
            </div>
          </div>

          <!-- Summary Cards -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-3xl shadow p-6 text-center">
              <p class="text-sm text-[#7A6E62]">Total commandes</p>
              <p class="text-4xl font-bold text-[#3D2B1F] mt-2">{{ totalOrders }}</p>
            </div>
            <div class="bg-white rounded-3xl shadow p-6 text-center">
              <p class="text-sm text-[#7A6E62]">Chiffre d'affaires</p>
              <p class="text-4xl font-bold text-[#3D2B1F] mt-2">{{ totalRevenue }} €</p>
            </div>
            <div class="bg-white rounded-3xl shadow p-6 text-center">
              <p class="text-sm text-[#7A6E62]">Panier moyen</p>
              <p class="text-4xl font-bold text-[#3D2B1F] mt-2">{{ averageOrder }} €</p>
            </div>
          </div>

          <!-- Revenue per menu -->
          <div class="bg-white rounded-3xl shadow p-8">
            <h3 class="text-lg font-semibold mb-6">Commandes par menu</h3>
            <div v-for="row in revenueStats" :key="row.menu_id" class="mb-5">
              <div class="flex justify-between text-sm mb-2">
                <span>{{ row.menu_title }}</span>
                <span>{{ row.total_orders }} commandes</span>
              </div>
              <div class="w-full bg-[#F5F0E8] rounded-full h-3">
                <div class="bg-[#C1813A] h-3 rounded-full" :style="{ width: barWidth(row.total_orders) + '%' }"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create Employee Modal -->
    <Teleport to="body">
      <div v-if="createModal.open" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8">
          <h3 class="text-xl font-bold mb-6">Créer un compte employé</h3>
          <div class="space-y-5">
            <div>
              <label class="block text-sm mb-2 text-[#7A6E62]">Nom complet</label>
              <input v-model="createModal.form.name" class="w-full p-4 border border-[#E8C98A] rounded-2xl" placeholder="Prénom Nom" />
            </div>
            <div>
              <label class="block text-sm mb-2 text-[#7A6E62]">Email</label>
              <input v-model="createModal.form.email" type="email" class="w-full p-4 border border-[#E8C98A] rounded-2xl" placeholder="employe@vite-gourmand.fr" />
            </div>
            <div>
              <label class="block text-sm mb-2 text-[#7A6E62]">Mot de passe</label>
              <input v-model="createModal.form.password" type="password" class="w-full p-4 border border-[#E8C98A] rounded-2xl" />
            </div>
          </div>
          <p v-if="createModal.error" class="text-red-600 text-sm mt-4">{{ createModal.error }}</p>
          <div class="flex gap-3 mt-8">
            <button @click="submitCreateEmployee" class="flex-1 bg-[#C1813A] text-white py-3 rounded-2xl font-semibold">Créer le compte</button>
            <button @click="createModal.open = false" class="flex-1 border py-3 rounded-2xl">Annuler</button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const tabs = [
  { id: 'employees', icon: '👥', label: 'Employés' },
  { id: 'stats', icon: '📊', label: 'Statistiques' },
];

const currentTab = ref('employees');
const employees = ref([]);
const createModal = ref({ open: false, form: { name: '', email: '', password: '' }, error: '' });
const revenueStats = ref([]);
const statMenus = ref([]);
const statsFilters = ref({ menu_id: '', from: '', to: '' });

const formatDate = (d) => d ? new Date(d).toLocaleDateString('fr-FR') : '—';

const fetchEmployees = async () => {
  const res = await axios.get('/v1/admin/employees');
  employees.value = res.data;
};

const openCreateModal = () => {
  createModal.value = { open: true, form: { name: '', email: '', password: '' }, error: '' };
};

const submitCreateEmployee = async () => {
  createModal.value.error = '';
  try {
    await axios.post('/v1/admin/employees', createModal.value.form);
    createModal.value.open = false;
    fetchEmployees();
  } catch (err) {
    createModal.value.error = err.response?.data?.message || 'Erreur lors de la création';
  }
};

const toggleEmployee = async (emp) => {
  if (!confirm(`Voulez-vous ${emp.is_active ? 'désactiver' : 'réactiver'} ${emp.name} ?`)) return;
  await axios.patch(`/v1/admin/employees/${emp.id}/toggle`);
  fetchEmployees();
};

const deleteEmployee = async (emp) => {
  if (!confirm(`Supprimer définitivement ${emp.name} ?`)) return;
  await axios.delete(`/v1/admin/employees/${emp.id}`);
  fetchEmployees();
};

const totalOrders = computed(() => revenueStats.value.reduce((sum, r) => sum + (r.total_orders || 0), 0));
const totalRevenue = computed(() => revenueStats.value.reduce((sum, r) => sum + (r.total_revenue || 0), 0).toFixed(2));
const averageOrder = computed(() => totalOrders.value > 0 ? (totalRevenue.value / totalOrders.value).toFixed(2) : '0.00');

const maxOrders = computed(() => Math.max(...revenueStats.value.map(r => r.total_orders || 0), 1));
const barWidth = (val) => Math.round(((val || 0) / maxOrders.value) * 100);

const fetchStats = async () => {
  const res = await axios.get('/v1/admin/stats/revenue-per-menu', { params: statsFilters.value });
  revenueStats.value = res.data;
};

const fetchStatMenus = async () => {
  const res = await axios.get('/v1/admin/stats/menus');
  statMenus.value = res.data;
};

const resetFilters = () => {
  statsFilters.value = { menu_id: '', from: '', to: '' };
  fetchStats();
};

onMounted(() => {
  fetchEmployees();
  fetchStats();
  fetchStatMenus();
});
</script>