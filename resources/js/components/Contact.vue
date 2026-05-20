<template>
  <div class="min-h-screen bg-[#F5F0E8] flex items-center py-12">
    <div class="max-w-3xl mx-auto px-4 w-full">
      <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
        <!-- Top Banner -->
        <div class="bg-[#3D2B1F] text-[#E8C98A] px-8 py-10 text-center">
          <div class="flex justify-center mb-4">
            <span class="text-5xl">📬</span>
          </div>
          <h1 class="text-4xl font-bold">Contactez Vite & Gourmand</h1>
          <p class="text-[#E8C98A]/80 mt-3 text-lg">Nous sommes à votre écoute pour vos événements</p>
        </div>

        <div class="p-8 md:p-12">
          <div v-if="success" class="text-center py-16">
            <div class="mx-auto w-20 h-20 bg-[#4A6741]/10 rounded-2xl flex items-center justify-center text-5xl mb-6">🎉</div>
            <h2 class="text-3xl font-semibold text-[#3D2B1F]">Merci pour votre message !</h2>
          </div>

          <form v-else @submit.prevent="submit" class="space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-[#7A6E62] mb-2">Nom complet</label>
                <input
                    v-model="form.name"
                    type="text"
                    class="w-full px-6 py-4 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A] focus:outline-none"
                    placeholder="Jean Dupont"
                    required
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-[#7A6E62] mb-2">Téléphone</label>
                <input
                    v-model="form.phone"
                    type="tel"
                    class="w-full px-6 py-4 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A] focus:outline-none"
                    placeholder="06 XX XX XX XX"
                >
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-[#7A6E62] mb-2">Email</label>
              <input
                  v-model="form.email"
                  type="email"
                  class="w-full px-6 py-4 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A] focus:outline-none"
                  placeholder="jean@example.fr"
                  required
              >
            </div>

            <div>
              <label class="block text-sm font-medium text-[#7A6E62] mb-2">Objet de votre demande</label>
              <input
                  v-model="form.subject"
                  type="text"
                  class="w-full px-6 py-4 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A] focus:outline-none"
                  placeholder="Devis mariage • Menu de Noël • Autre"
                  required
              >
            </div>

            <div>
              <label class="block text-sm font-medium text-[#7A6E62] mb-2">Message</label>
              <textarea
                  v-model="form.message"
                  rows="6"
                  class="w-full px-6 py-4 border border-[#E8C98A] rounded-2xl focus:border-[#C1813A] focus:outline-none resize-y"
                  placeholder="Bonjour, je souhaiterais un devis pour un événement de 40 personnes le 20 décembre..."
                  required
              ></textarea>
            </div>

            <button
                type="submit"
                :disabled="loading"
                class="w-full bg-[#C1813A] hover:bg-[#A76D2F] text-white py-5 rounded-2xl font-semibold text-lg transition-all active:scale-[0.985]">
              {{ loading ? 'Envoi en cours...' : 'Envoyer mon message' }}
            </button>
          </form>
        </div>
      </div>

      <p class="text-center text-[#7A6E62] text-sm mt-8">
        📍 Bordeaux • Réponse sous 24h en semaine
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const form = ref({
  name: '',
  email: '',
  phone: '',
  subject: '',
  message: ''
});

const loading = ref(false);
const success = ref(false);

const submit = async () => {
  loading.value = true;
  try {
    await axios.post('/v1/contact', form.value);
    success.value = true;
    form.value = { name: '', email: '', phone: '', subject: '', message: '' };
  } catch (err) {
    alert("Erreur lors de l'envoi du message.");
  } finally {
    loading.value = false;
  }
};
</script>