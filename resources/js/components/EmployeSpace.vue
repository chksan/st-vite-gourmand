<template>
  <div class="max-w-7xl mx-auto px-4 md:px-6 py-8 md:py-12">
    <h1 class="text-3xl md:text-4xl font-bold text-[#3D2B1F] mb-8">Espace Employé</h1>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
      <div class="bg-white p-6 rounded-3xl shadow h-fit sticky top-6">
        <nav class="space-y-2">
          <button v-for="tab in tabs" :key="tab.id"
                  @click="currentTab = tab.id"
                  :class="currentTab === tab.id ? 'bg-[#3D2B1F] text-white' : 'hover:bg-[#F5F0E8]'"
                  class="w-full text-left px-5 py-4 rounded-2xl font-medium transition">
            {{ tab.icon }} {{ tab.label }}
          </button>
        </nav>
      </div>

      <div class="lg:col-span-3 bg-white rounded-3xl shadow p-6 md:p-8 min-h-[700px]">
        <div v-if="currentTab === 'orders'">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-semibold text-[#3D2B1F]">Gestion des Commandes</h2>
          </div>

          <div class="flex flex-wrap gap-3 mb-6">
            <select v-model="orderFilters.status" @change="fetchOrders"
                    class="border border-[#E8C98A] rounded-2xl px-4 py-2 text-sm focus:border-[#C1813A]">
              <option value="">Tous les statuts</option>
              <option v-for="(label, val) in statusMap" :key="val" :value="val">{{ label }}</option>
            </select>
            <input v-model="orderFilters.client" @input="fetchOrders"
                   placeholder="Rechercher un client…"
                   class="border border-[#E8C98A] rounded-2xl px-4 py-2 text-sm w-full md:w-56" />
          </div>

          <div class="space-y-4">
            <div v-for="order in orders" :key="order.id"
                 class="border border-[#E8C98A] rounded-2xl p-5 hover:border-[#C1813A] transition-colors">
              <div class="flex justify-between items-start">
                <div>
                  <p class="font-semibold">{{ order.menu?.title }}</p>
                  <p class="text-sm text-[#7A6E62]">
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
                        class="text-sm text-[#C1813A] hover:underline">Voir détails</button>
                <button v-if="order.status !== 'completed' && order.status !== 'cancelled'"
                        @click="openStatusModal(order)"
                        class="text-sm bg-[#C1813A] text-white px-4 py-2 rounded-xl">
                  Changer statut
                </button>
              </div>
            </div>
            <p v-if="!orders.length" class="text-[#7A6E62] text-center py-10">Aucune commande trouvée.</p>
          </div>

          <div v-if="ordersMeta.last_page > 1" class="flex justify-center gap-2 mt-6">
            <button v-for="p in ordersMeta.last_page" :key="p"
                    @click="fetchOrders(p)"
                    :class="p === ordersMeta.current_page ? 'bg-[#3D2B1F] text-white' : 'bg-[#F5F0E8]'"
                    class="w-9 h-9 rounded-lg text-sm font-medium">{{ p }}</button>
          </div>
        </div>

        <!-- MENUS -->
        <div v-if="currentTab === 'menus'">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-semibold text-[#3D2B1F]">Gestion des Menus</h2>
            <button @click="openMenuForm()"
                    class="bg-[#C1813A] hover:bg-[#A76D2F] text-white px-5 py-2.5 rounded-2xl text-sm font-medium">
              + Nouveau menu
            </button>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="menu in menus" :key="menu.id" class="border border-[#E8C98A] rounded-2xl p-5">
              <div class="flex justify-between items-start">
                <div>
                  <p class="font-semibold text-lg">{{ menu.title }}</p>
                  <p class="text-sm text-[#7A6E62] mt-1">{{ menu.theme }} · {{ menu.regime }}</p>
                  <p class="text-sm font-medium text-[#C1813A] mt-1">{{ menu.price }} € / {{ menu.min_personnes }} pers.</p>
                  <p class="text-xs text-[#7A6E62] mt-1">Stock : {{ menu.stock }}</p>
                </div>
                <div class="flex flex-col gap-2">
                  <button @click="openMenuForm(menu)" class="text-xs border border-gray-300 px-3 py-1 rounded-lg hover:bg-gray-50">Modifier</button>
                  <button @click="confirmDeleteMenu(menu)" class="text-xs border border-red-300 text-red-600 px-3 py-1 rounded-lg hover:bg-red-50">Supprimer</button>
                </div>
              </div>
              <div v-if="menu.plats?.length" class="mt-3 flex flex-wrap gap-1">
                <span v-for="plat in menu.plats" :key="plat.id" class="text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded">{{ plat.title }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- PLATS -->
        <div v-if="currentTab === 'plats'">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-semibold text-[#3D2B1F]">Plats & Allergènes</h2>
            <button @click="openPlatForm()" class="bg-[#C1813A] hover:bg-[#A76D2F] text-white px-5 py-2.5 rounded-2xl text-sm font-medium">+ Nouveau plat</button>
          </div>

          <div class="mb-6 p-4 bg-red-50 rounded-2xl">
            <div class="flex items-center justify-between mb-3">
              <p class="font-semibold text-red-700 text-sm">Allergènes disponibles</p>
              <button @click="showAllergenForm = !showAllergenForm" class="text-xs bg-red-600 text-white px-3 py-1 rounded-lg">+ Ajouter</button>
            </div>
            <div v-if="showAllergenForm" class="flex gap-2 mb-3">
              <input v-model="newAllergenName" placeholder="Nom de l'allergène" class="border rounded-lg px-3 py-1.5 text-sm flex-1" />
              <button @click="storeAllergen" class="bg-red-600 text-white px-4 py-1.5 rounded-lg text-sm">Créer</button>
            </div>
            <div class="flex flex-wrap gap-2">
              <span v-for="a in allergens" :key="a.id" class="text-xs bg-white border border-red-200 text-red-700 px-3 py-1 rounded-full flex items-center gap-1">
                {{ a.name }}
                <button @click="deleteAllergen(a)" class="hover:text-red-900">×</button>
              </span>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="plat in plats" :key="plat.id" class="border border-[#E8C98A] rounded-2xl p-4">
              <div class="flex justify-between">
                <div>
                  <p class="font-medium">{{ plat.title }}</p>
                  <p class="text-sm text-[#7A6E62] capitalize">{{ plat.type }}</p>
                </div>
                <div class="flex gap-2">
                  <button @click="openPlatForm(plat)" class="text-xs border px-3 py-1 rounded-lg hover:bg-gray-50">Modifier</button>
                  <button @click="deletePlat(plat)" class="text-xs border border-red-300 text-red-600 px-3 py-1 rounded-lg hover:bg-red-50">Supprimer</button>
                </div>
              </div>
              <div v-if="plat.allergens?.length" class="flex flex-wrap gap-1 mt-2">
                <span v-for="a in plat.allergens" :key="a.id" class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded">{{ a.name }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- HORAIRES -->
        <div v-if="currentTab === 'horaires'">
          <h2 class="text-2xl font-semibold mb-6 text-[#3D2B1F]">Gestion des Horaires</h2>

          <div class="space-y-4">
            <div v-for="h in horaires" :key="h.id"
                 class="border border-[#E8C98A] rounded-2xl p-5 md:p-6 flex flex-col md:flex-row md:items-center gap-4">

              <div class="w-full md:w-32">
                <p class="font-medium capitalize text-[#3D2B1F]">{{ h.day }}</p>
              </div>

              <div class="flex-1 flex flex-col sm:flex-row sm:items-center gap-3">
                <label class="flex items-center gap-2 text-sm cursor-pointer whitespace-nowrap">
                  <input type="checkbox" v-model="h.is_closed" class="accent-[#C1813A] w-5 h-5" />
                  Fermé toute la journée
                </label>

                <template v-if="!h.is_closed">
                  <div class="flex items-center gap-3 flex-1">
                    <input type="time" v-model="h.opening_time"
                           class="border border-[#E8C98A] rounded-xl px-4 py-3 text-sm w-full sm:w-auto" />
                    <span class="text-[#7A6E62] hidden sm:inline">→</span>
                    <input type="time" v-model="h.closing_time"
                           class="border border-[#E8C98A] rounded-xl px-4 py-3 text-sm w-full sm:w-auto" />
                  </div>
                </template>
              </div>

              <button @click="saveHoraire(h)"
                      class="mt-2 md:mt-0 bg-[#C1813A] hover:bg-[#A76D2F] text-white px-6 py-3 rounded-2xl text-sm font-medium whitespace-nowrap transition">
                Enregistrer
              </button>
            </div>
          </div>
        </div>

        <!-- REVIEWS -->
        <div v-if="currentTab === 'reviews'">
          <h2 class="text-2xl font-semibold mb-6 text-[#3D2B1F]">Avis à valider</h2>
          <div v-if="reviews.length === 0" class="text-[#7A6E62] text-center py-10">Aucun avis en attente de validation.</div>
          <div class="space-y-4">
            <div v-for="review in reviews" :key="review.id" class="border border-[#E8C98A] rounded-2xl p-6">
              <div class="flex justify-between items-start">
                <div>
                  <p class="font-semibold">{{ review.user?.name }}</p>
                  <p class="text-xs text-[#7A6E62] mb-1">Commande : {{ review.order?.menu?.title }}</p>
                  <div class="flex gap-0.5 mb-2">
                    <span v-for="i in 5" :key="i" :class="i <= review.rating ? 'text-[#C1813A]' : 'text-gray-200'" class="text-lg">★</span>
                  </div>
                  <p class="text-sm text-[#7A6E62]">{{ review.comment }}</p>
                </div>
              </div>
              <div class="flex gap-3 mt-4">
                <button @click="validateReview(review)" class="bg-green-600 text-white px-5 py-2 rounded-xl text-sm font-medium hover:bg-green-700">✓ Valider</button>
                <button @click="rejectReview(review)" class="border border-red-300 text-red-600 px-5 py-2 rounded-xl text-sm font-medium hover:bg-red-50">✗ Refuser</button>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>




    <Teleport to="body">
      <div v-if="selectedOrder" class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden">
          <div class="bg-[#3D2B1F] text-[#E8C98A] px-8 py-6 flex justify-between items-center">
            <h3 class="text-xl font-bold">Détail de la commande</h3>
            <button @click="selectedOrder = null"
                    class="text-3xl leading-none hover:text-[#C1813A] transition">×</button>
          </div>

          <div class="p-8 space-y-8">
            <div>
          <span class="inline-block px-4 py-1 bg-[#C1813A] text-white text-sm font-semibold rounded-full mb-3">
            {{ selectedOrder.menu?.theme }}
          </span>
              <h2 class="text-2xl font-bold text-[#3D2B1F]">{{ selectedOrder.menu?.title }}</h2>
              <p class="text-[#7A6E62] mt-2">{{ selectedOrder.menu?.description }}</p>
            </div>
            <div class="grid grid-cols-2 gap-6 text-sm">
              <div>
                <p class="text-[#7A6E62] mb-1">Client</p>
                <p class="font-medium">{{ selectedOrder.user?.name }}</p>
              </div>
              <div>
                <p class="text-[#7A6E62] mb-1">Téléphone</p>
                <p class="font-medium">{{ selectedOrder.user?.gsm }}</p>
              </div>
              <div class="col-span-2">
                <p class="text-[#7A6E62] mb-1">Adresse de livraison</p>
                <p class="font-medium">{{ selectedOrder.adresse_livraison }}</p>
              </div>
              <div>
                <p class="text-[#7A6E62] mb-1">Date & Heure</p>
                <p class="font-medium">{{ formatDate(selectedOrder.date_prestation) }} à {{ selectedOrder.heure_livraison }}</p>
              </div>
              <div>
                <p class="text-[#7A6E62] mb-1">Nombre de personnes</p>
                <p class="font-medium">{{ selectedOrder.nb_personnes }} personnes</p>
              </div>
            </div>
            <div class="bg-[#F9F6F1] rounded-2xl p-6">
              <div class="flex justify-between items-end">
                <div>
                  <span class="text-4xl font-bold text-[#C1813A]">{{ selectedOrder.prix_menu }} €</span>
                  <span class="text-[#7A6E62]"> (menu)</span>
                </div>
                <div class="text-right">
                  <span class="text-[#7A6E62]">Livraison</span><br>
                  <span class="font-medium">{{ selectedOrder.prix_livraison }} €</span>
                </div>
              </div>

              <div class="border-t border-[#E8C98A] mt-6 pt-6 flex justify-between text-xl font-bold text-[#3D2B1F]">
                <span>Total</span>
                <span class="text-[#C1813A]">
              {{ (parseFloat(selectedOrder.prix_menu || 0) + parseFloat(selectedOrder.prix_livraison || 0)).toFixed(2) }} €
            </span>
              </div>
            </div>

            <div>
              <p class="text-[#7A6E62] mb-2 text-sm">Statut actuel</p>
              <span :class="statusClass(selectedOrder.status)"
                    class="inline-block px-5 py-2 text-sm font-semibold rounded-2xl">
            {{ statusLabel(selectedOrder.status) }}
          </span>
            </div>

            <div v-if="selectedOrder.status_histories?.length" class="pt-6 border-t border-[#E8C98A]">
              <p class="font-semibold mb-4 text-[#3D2B1F]">Historique des statuts</p>
              <div class="space-y-4">
                <div v-for="h in selectedOrder.status_histories" :key="h.id"
                     class="flex gap-4 text-sm">
                  <div class="w-2 h-2 mt-2 rounded-full bg-[#C1813A]"></div>
                  <div class="flex-1">
                    <div class="flex justify-between">
                      <span class="font-medium">{{ statusLabel(h.status) }}</span>
                      <span class="text-xs text-[#7A6E62]">{{ formatDate(h.created_at, true) }}</span>
                    </div>
                    <p v-if="h.comment" class="text-[#7A6E62] text-xs mt-1">{{ h.comment }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Status Modal -->
    <Teleport to="body">
      <div v-if="statusModal.open" class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8">
          <div class="flex justify-between items-center mb-8">
            <h3 class="text-xl font-bold text-[#3D2B1F]">Mettre à jour le statut</h3>
            <button @click="statusModal.open = false" class="text-3xl text-[#7A6E62] hover:text-[#3D2B1F]">×</button>
          </div>

          <div class="space-y-6">
            <div>
              <label class="block text-sm font-medium text-[#7A6E62] mb-3">Nouveau statut</label>
              <select v-model="statusModal.status"
                      class="w-full p-4 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A] text-base">
                <option v-for="(label, val) in statusMap" :key="val" :value="val">{{ label }}</option>
              </select>
            </div>

            <template v-if="statusModal.status === 'cancelled'">
              <div>
                <label class="block text-sm font-medium text-[#7A6E62] mb-3">Mode de contact client *</label>
                <select v-model="statusModal.contactMode"
                        class="w-full p-4 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A]">
                  <option value="">Sélectionner…</option>
                  <option value="gsm">Appel GSM</option>
                  <option value="email">Email</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-[#7A6E62] mb-3">Motif d'annulation *</label>
                <textarea v-model="statusModal.cancelReason" rows="4"
                          class="w-full p-4 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A]"
                          placeholder="Expliquez la raison de l'annulation..."></textarea>
              </div>
            </template>
          </div>

          <div class="flex gap-3 mt-10">
            <button @click="submitStatus"
                    class="flex-1 bg-[#C1813A] hover:bg-[#A76D2F] text-white py-4 rounded-2xl font-semibold transition">
              Confirmer
            </button>
            <button @click="statusModal.open = false"
                    class="flex-1 border border-[#E8C98A] py-4 rounded-2xl font-medium">
              Annuler
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Menu Modal -->
    <Teleport to="body">
      <div v-if="menuModal.open" class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full p-8 max-h-[92vh] overflow-y-auto">
          <div class="flex justify-between items-center mb-8">
            <h3 class="text-2xl font-bold text-[#3D2B1F]">
              {{ menuModal.isEdit ? 'Modifier le menu' : 'Créer un nouveau menu' }}
            </h3>
            <button @click="closeMenuModal" class="text-3xl text-[#7A6E62]">×</button>
          </div>

          <div class="space-y-8">
            <!-- Upload Image -->
            <div>
              <label class="block text-sm font-medium text-[#7A6E62] mb-3">Image du menu</label>
              <div
                  @dragover.prevent="dragOver"
                  @dragleave.prevent="dragLeave"
                  @drop.prevent="dropFile"
                  @click="triggerFileInput"
                  class="border-2 border-dashed border-[#E8C98A] rounded-2xl p-8 text-center cursor-pointer hover:border-[#C1813A] transition"
                  :class="{ 'bg-[#F5F0E8] border-[#C1813A]' : isDragging }">

                <input type="file" ref="fileInput" @change="handleFileSelect" accept="image/jpeg,image/png,image/webp" class="hidden" />

                <div v-if="menuModal.form.imagePreview">
                  <img :src="menuModal.form.imagePreview" class="mx-auto max-h-56 rounded-2xl shadow" alt="preview" />
                  <button @click.stop="removeImage" class="mt-4 text-red-600 hover:text-red-700 text-sm">Supprimer l'image</button>
                </div>
                <div v-else>
                  <p class="text-5xl mb-4">📸</p>
                  <p class="font-medium">Glissez une image ici ou cliquez pour sélectionner</p>
                  <p class="text-xs text-[#7A6E62] mt-1">JPG, PNG, WebP — Max 5 Mo</p>
                </div>
              </div>
            </div>

            <!-- Champs du menu -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-[#7A6E62] mb-2">Titre du menu</label>
                <input v-model="menuModal.form.title" class="w-full p-4 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A]" required />
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-[#7A6E62] mb-2">Description</label>
                <textarea v-model="menuModal.form.description" rows="3" class="w-full p-4 border border-[#E8C98A] rounded-2xl"></textarea>
              </div>

              <div>
                <label class="block text-sm font-medium text-[#7A6E62] mb-2">Stock disponible <span class="text-red-600">*</span></label>
                <input
                    v-model.number="menuModal.form.stock"
                    type="number"
                    min="0"
                    class="w-full p-4 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A] text-lg font-semibold"
                    required
                />
                <p class="text-xs text-[#7A6E62] mt-1">Nombre de commandes possibles pour ce menu</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-[#7A6E62] mb-2">Thème</label>
                <select v-model="menuModal.form.theme" class="w-full p-4 border border-[#E8C98A] rounded-2xl">
                  <option value="noel">Noël</option>
                  <option value="paques">Pâques</option>
                  <option value="classique">Classique</option>
                  <option value="evenement">Événement</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-[#7A6E62] mb-2">Régime</label>
                <select v-model="menuModal.form.regime" class="w-full p-4 border border-[#E8C98A] rounded-2xl">
                  <option value="classique">Classique</option>
                  <option value="vegetarien">Végétarien</option>
                  <option value="vegan">Vegan</option>
                  <option value="sans_gluten">Sans gluten</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-[#7A6E62] mb-2">Personnes minimum</label>
                <input v-model.number="menuModal.form.min_personnes" type="number" min="1" class="w-full p-4 border border-[#E8C98A] rounded-2xl" />
              </div>

              <div>
                <label class="block text-sm font-medium text-[#7A6E62] mb-2">Prix (€)</label>
                <input v-model.number="menuModal.form.price" type="number" step="0.01" class="w-full p-4 border border-[#E8C98A] rounded-2xl" />
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-[#7A6E62] mb-2">Conditions importantes</label>
                <textarea v-model="menuModal.form.conditions" rows="3" class="w-full p-4 border border-[#E8C98A] rounded-2xl"></textarea>
              </div>
            </div>
          </div>

          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-[#7A6E62] mb-3">Plats du menu</label>
            <div class="border border-[#E8C98A] rounded-2xl p-5 max-h-60 overflow-y-auto grid grid-cols-2 gap-3">
              <label v-for="plat in plats" :key="plat.id" class="flex items-center gap-2 cursor-pointer text-sm">
                <input type="checkbox" :value="plat.id" v-model="menuModal.form.plat_ids" class="accent-[#C1813A]" />
                <span>{{ plat.title }} <span class="text-[#7A6E62] capitalize">({{ plat.type }})</span></span>
              </label>
            </div>
          </div>

          <div class="flex gap-3 mt-10">
            <button @click="submitMenu" class="flex-1 bg-[#C1813A] hover:bg-[#A76D2F] text-white py-4 rounded-2xl font-semibold transition">
              {{ menuModal.isEdit ? 'Enregistrer les modifications' : 'Créer le menu' }}
            </button>
            <button @click="closeMenuModal" class="flex-1 border border-[#E8C98A] py-4 rounded-2xl">Annuler</button>
          </div>
        </div>
      </div>
    </Teleport>
    <!-- Plat Modal -->
    <Teleport to="body">
      <div v-if="platModal.open" class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8">
          <div class="flex justify-between items-center mb-8">
            <h3 class="text-xl font-bold text-[#3D2B1F]">
              {{ platModal.isEdit ? 'Modifier le plat' : 'Nouveau plat' }}
            </h3>
            <button @click="platModal.open = false" class="text-3xl text-[#7A6E62]">×</button>
          </div>

          <div class="space-y-6">
            <div>
              <label class="block text-sm font-medium text-[#7A6E62] mb-2">Type de plat</label>
              <select v-model="platModal.form.type" class="w-full p-4 border border-[#E8C98A] rounded-2xl">
                <option value="entree">Entrée</option>
                <option value="plat">Plat principal</option>
                <option value="dessert">Dessert</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-[#7A6E62] mb-2">Titre</label>
              <input v-model="platModal.form.title" class="w-full p-4 border border-[#E8C98A] rounded-2xl" />
            </div>

            <div>
              <label class="block text-sm font-medium text-[#7A6E62] mb-2">Description</label>
              <textarea v-model="platModal.form.description" rows="3" class="w-full p-4 border border-[#E8C98A] rounded-2xl"></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium text-[#7A6E62] mb-3">Allergènes</label>
              <div class="border border-[#E8C98A] rounded-2xl p-5 max-h-60 overflow-y-auto grid grid-cols-2 gap-3">
                <label v-for="a in allergens" :key="a.id" class="flex items-center gap-2 cursor-pointer text-sm">
                  <input type="checkbox" :value="a.id" v-model="platModal.form.allergen_ids" class="accent-[#C1813A]" />
                  {{ a.name }}
                </label>
              </div>
            </div>
          </div>

          <div class="flex gap-3 mt-10">
            <button @click="submitPlat"
                    class="flex-1 bg-[#C1813A] hover:bg-[#A76D2F] text-white py-4 rounded-2xl font-semibold">
              {{ platModal.isEdit ? 'Enregistrer' : 'Créer le plat' }}
            </button>
            <button @click="platModal.open = false"
                    class="flex-1 border border-[#E8C98A] py-4 rounded-2xl">Annuler</button>
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
const menuModal = ref({
  open: false,
  isEdit: false,
  form: {
    title: '',
    description: '',
    theme: 'classique',
    regime: 'classique',
    min_personnes: 2,
    price: '',
    stock: 10,
    conditions: '',
    image: null,
    imagePreview: null
  }
});

const isDragging = ref(false);
const fileInput = ref(null);

const fetchMenus = async () => {
  const res = await axios.get('/v1/employe/menus');
  menus.value = res.data;
};

const openMenuForm = (menu = null) => {
  if (menu) {
    menuModal.value = {
      open: true,
      isEdit: true,
      form: {
        ...menu,
        plat_ids: menu.plats?.map(p => p.id) || [],
        stock: menu.stock || 10,
        image: null,
        imagePreview: menu.images?.[0] ? `/storage/${menu.images[0]}` : null
      }
    };
  } else {
    menuModal.value = {
      open: true,
      isEdit: false,
      form: {
        title: '', description: '', theme: 'classique', regime: 'classique',
        min_personnes: 2, price: '', stock: 10, conditions: '',
        image: null, imagePreview: null
      }
    };
  }
};

const closeMenuModal = () => {
  menuModal.value.open = false;
  menuModal.value.form.image = null;
  menuModal.value.form.imagePreview = null;
};

const triggerFileInput = () => fileInput.value?.click();

const handleFileSelect = (e) => {
  const file = e.target.files[0];
  if (file) processImage(file);
};

const dragOver = () => isDragging.value = true;
const dragLeave = () => isDragging.value = false;

const dropFile = (e) => {
  isDragging.value = false;
  const file = e.dataTransfer.files[0];
  if (file) processImage(file);
};

const processImage = (file) => {
  if (!file.type.startsWith('image/')) return alert("Veuillez sélectionner une image");
  if (file.size > 5 * 1024 * 1024) return alert("L'image ne doit pas dépasser 5 Mo");

  menuModal.value.form.image = file;
  menuModal.value.form.imagePreview = URL.createObjectURL(file);
};

const removeImage = () => {
  menuModal.value.form.image = null;
  menuModal.value.form.imagePreview = null;
};

const submitMenu = async () => {
  const formData = new FormData();
  formData.append('title', menuModal.value.form.title || '');
  formData.append('description', menuModal.value.form.description || '');
  formData.append('theme', menuModal.value.form.theme || 'classique');
  formData.append('regime', menuModal.value.form.regime || 'classique');
  formData.append('min_personnes', menuModal.value.form.min_personnes || 2);
  formData.append('price', menuModal.value.form.price || 0);
  formData.append('stock', menuModal.value.form.stock || 10);
  formData.append('conditions', menuModal.value.form.conditions || '');


  const platIds = menuModal.value.form.plat_ids || [];
  platIds.forEach(id => formData.append('plat_ids[]', id));

  if (menuModal.value.form.image) {
    formData.append('image', menuModal.value.form.image);
  }

  try {
    let res;

    if (menuModal.value.isEdit) {
      res = await axios.post(`/v1/employe/menus/${menuModal.value.form.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
    } else {
      res = await axios.post('/v1/employe/menus', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
    }

    alert('Menu enregistré avec succès !');
    closeMenuModal();
    fetchMenus();

  } catch (e) {
    console.error(e.response?.data);
    if (e.response?.data?.errors) {
      alert("Erreurs : " + Object.values(e.response.data.errors).flat().join('\n'));
    } else {
      alert("Erreur lors de l'enregistrement");
    }
  }
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

