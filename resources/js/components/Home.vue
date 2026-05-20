<template>
  <div class="min-h-screen bg-[#F5F0E8]">
    <!-- HERO -->
    <div class="relative h-screen flex items-center justify-center bg-cover bg-center"
         :style="{ backgroundImage: `url('https://images.unsplash.com/photo-1600891964599-f61ba0e24092?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D')` }">
      <div class="absolute inset-0 bg-black/60"></div>
      <div class="relative z-10 text-center px-6 max-w-4xl">
        <p class="uppercase tracking-[5px] text-[#C1813A] text-sm font-medium mb-6">
          Traiteur à Bordeaux depuis 25 ans
        </p>
        <h1 class="text-6xl md:text-7xl lg:text-8xl font-bold text-white leading-none mb-8">
          L'art de recevoir<br>avec passion
        </h1>
        <p class="text-xl text-white/90 max-w-2xl mx-auto mb-12">
          Des expériences culinaires raffinées et sur mesure pour vos événements les plus précieux.
        </p>
        <div class="flex flex-col sm:flex-row gap-5 justify-center">
          <router-link to="/menus"
                       class="bg-[#C1813A] hover:bg-[#A76D2F] text-white px-12 py-5 rounded-2xl font-semibold text-lg transition">
            Découvrir nos menus
          </router-link>
          <router-link to="/contact"
                       class="border-2 border-white text-white hover:bg-white hover:text-[#3D2B1F] px-12 py-5 rounded-2xl font-semibold text-lg transition">
            Nous contacter
          </router-link>
        </div>
      </div>
    </div>

    <!-- EXPERTISE -->
    <div class="max-w-6xl mx-auto px-6 py-20">
      <div class="text-center mb-16">
        <h2 class="text-4xl font-bold text-[#3D2B1F]">Pourquoi nous choisir ?</h2>
        <p class="text-[#7A6E62] mt-4 text-lg">Des prestations d'exception pour des moments inoubliables</p>
      </div>
      <div class="grid md:grid-cols-3 gap-10">
        <div class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all group">
          <div class="h-64 bg-cover bg-center" style="background-image: url('/images/savoir.jpg')"></div>
          <div class="p-8 text-center">
            <h3 class="font-bold text-2xl mb-3 text-[#3D2B1F]">Cuisine d'auteur</h3>
            <p class="text-[#7A6E62]">Produits d'exception et savoir-faire artisanal</p>
          </div>
        </div>
        <div class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all group">
          <div class="h-64 bg-cover bg-center" style="background-image: url('/images/restaurant.jpg')"></div>
          <div class="p-8 text-center">
            <h3 class="font-bold text-2xl mb-3 text-[#3D2B1F]">Service &amp; Logistique</h3>
            <p class="text-[#7A6E62]">Présentation élégante et livraison impeccable</p>
          </div>
        </div>
        <div class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all group">
          <div class="h-64 bg-cover bg-center" style="background-image: url('/images/mesure.jpg')"></div>
          <div class="p-8 text-center">
            <h3 class="font-bold text-2xl mb-3 text-[#3D2B1F]">Sur Mesure</h3>
            <p class="text-[#7A6E62]">Chaque événement est unique et traité avec passion</p>
          </div>
        </div>
      </div>
    </div>

    <!-- AVIS CLIENTS -->
    <div class="bg-white py-20">
      <div class="max-w-6xl mx-auto px-6">
        <h2 class="text-4xl font-bold text-center text-[#3D2B1F] mb-12">
          Ils nous ont fait confiance
        </h2>

        <div v-if="reviews.length" class="relative">
          <div class="overflow-hidden">
            <div class="flex transition-transform duration-700 ease-in-out"
                 :style="{ transform: `translateX(-${currentSlide * (100 / reviewsPerView)}%)` }">

              <div v-for="(review, index) in reviews" :key="review.id"
                   class="min-w-full md:min-w-1/3 px-4">

                <div class="bg-[#F9F6F1] p-8 md:p-10 rounded-3xl h-full flex flex-col relative">
                  <!-- Étoiles -->
                  <div class="text-[#C1813A] text-4xl mb-6">★★★★★</div>

                  <!-- Commentaire -->
                  <p class="italic text-[#7A6E62] leading-relaxed flex-1 text-[15.5px] mb-8">
                    "{{ review.comment }}"
                  </p>

                  <!-- Auteur + Date -->
                  <div class="flex justify-between items-end mt-auto pt-6 border-t border-[#E8C98A]/60">
                    <div>
                      <p class="font-semibold text-[#3D2B1F]">{{ review.user?.display_name }}</p>
                    </div>
                    <div class="text-right">
                      <p class="text-xs text-[#7A6E62]">
                        {{ formatDate(review.created_at) }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Navigation -->
          <button v-if="reviews.length > 3" @click="prevSlide"
                  class="absolute left-4 top-1/2 -translate-y-1/2 bg-white shadow-xl p-4 rounded-full hover:bg-[#F5F0E8] transition text-2xl z-10">
            ←
          </button>
          <button v-if="reviews.length > 3" @click="nextSlide"
                  class="absolute right-4 top-1/2 -translate-y-1/2 bg-white shadow-xl p-4 rounded-full hover:bg-[#F5F0E8] transition text-2xl z-10">
            →
          </button>
        </div>

        <div v-else class="text-center py-16 text-[#7A6E62]">
          Aucun avis pour le moment...
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="bg-[#3D2B1F] text-[#E8C98A] py-16">
      <div class="max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-4 gap-10">

          <div>
            <div class="flex items-center gap-4 mb-6">
              <span class="text-4xl">🍽️</span>
              <div>
                <p class="font-bold text-2xl">Vite & Gourmand</p>
                <p class="text-xs text-[#E8C98A]/50">Traiteur d'exception à Bordeaux</p>
              </div>
            </div>
            <p class="text-sm text-[#E8C98A]/70">
              Des prestations gourmandes sur mesure<br>
              pour vos événements les plus précieux.
            </p>
          </div>

          <div>
            <p class="uppercase text-xs tracking-widest text-[#C1813A] mb-4">Horaires d'ouverture</p>
            <div v-if="horaires.length" class="text-sm space-y-1 text-[#E8C98A]/70">
              <div v-for="h in horaires" :key="h.id" class="flex justify-between">
                <span class="capitalize">{{ h.day }}</span>
                <span v-if="h.is_closed" class="text-red-400">Fermé</span>
                <span v-else>{{ h.opening_time }} — {{ h.closing_time }}</span>
              </div>
            </div>
          </div>

          <div>
            <p class="uppercase text-xs tracking-widest text-[#C1813A] mb-4">Contact</p>
            <div class="text-sm space-y-2 text-[#E8C98A]/70">
              <p>contact@vitegourmand.fr</p>
              <p>06 12 34 56 78</p>
              <p>12 Rue des Vignes<br>33000 Bordeaux</p>
            </div>
          </div>

          <div>
            <p class="uppercase text-xs tracking-widest text-[#C1813A] mb-4">Informations légales</p>
            <div class="flex flex-col gap-2 text-sm">
              <router-link to="/mentions-legales" class="hover:text-white transition">
                Mentions légales
              </router-link>
              <router-link to="/cgv" class="hover:text-white transition">
                Conditions Générales de Vente
              </router-link>
            </div>
          </div>
        </div>

        <div class="border-t border-[#E8C98A]/20 mt-12 pt-8 text-center text-xs text-[#E8C98A]/50">
          © 2026 Vite & Gourmand — Tous droits réservés
        </div>
      </div>
    </footer>  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';

const horaires = ref([]);
const reviews = ref([]);

const currentSlide = ref(0);
const reviewsPerView = ref(3);
let autoSlideInterval = null;

const fetchHomeData = async () => {
  try {
    const res = await axios.get('/v1/informations');
    horaires.value = res.data.horaires || [];
    reviews.value = res.data.reviews || [];
  } catch (e) {
    console.log("Impossible de charger les données home");
  }
};

const nextSlide = () => {
  const max = Math.max(0, reviews.value.length - reviewsPerView.value);
  currentSlide.value = currentSlide.value >= max ? 0 : currentSlide.value + 1;
};

const prevSlide = () => {
  const max = Math.max(0, reviews.value.length - reviewsPerView.value);
  currentSlide.value = currentSlide.value <= 0 ? max : currentSlide.value - 1;
};

const startAutoSlide = () => {
  if (autoSlideInterval) clearInterval(autoSlideInterval);
  if (reviews.value.length > 3) {
    autoSlideInterval = setInterval(nextSlide, 5000);
  }
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  });
};

watch(reviews, (newVal) => {
  if (newVal.length > 0) startAutoSlide();
}, { immediate: true });

onMounted(() => {
  fetchHomeData();
});

onUnmounted(() => {
  if (autoSlideInterval) clearInterval(autoSlideInterval);
});
</script>