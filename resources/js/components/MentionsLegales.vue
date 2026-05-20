<template>
  <div class="min-h-screen bg-[#F5F0E8]">
    <div class="bg-[#3D2B1F] py-16 px-6">
      <div class="max-w-3xl mx-auto text-center">
        <p class="text-[#C1813A] text-xs font-semibold tracking-[3px] uppercase mb-3">VITE & GOURMAND</p>
        <h1 class="text-[#F5F0E8] text-4xl md:text-5xl font-bold">Mentions légales</h1>
      </div>
    </div>

    <div class="max-w-3xl mx-auto px-6 py-16 space-y-12">
      <section v-for="section in sections" :key="section.title"
               class="bg-white rounded-3xl p-8 md:p-10 border border-[#E8C98A]">

        <h2 class="text-[#3D2B1F] text-xl font-bold mb-6 flex items-center gap-3">
          <span class="w-1.5 h-6 bg-[#C1813A] rounded-full inline-block"></span>
          {{ section.title }}
        </h2>

        <div class="prose prose-[#7A6E62] text-[15px] leading-relaxed">
          <template v-for="(content, i) in section.content" :key="i">
            <p v-if="typeof content === 'string'" class="mb-4">{{ content }}</p>
            <ul v-else-if="Array.isArray(content)" class="list-disc pl-5 mb-4 space-y-1">
              <li v-for="(item, j) in content" :key="j">{{ item }}</li>
            </ul>
          </template>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
const sections = [
  {
    title: "Éditeur du site",
    content: [
      "Vite & Gourmand",
      [
        "Julie Martin & José Dupont",
        "Bordeaux, France",
        "Email : contact@vitegourmand.fr"
      ]
    ]
  },
  {
    title: "Hébergement",
    content: [
      "Le site est hébergé par un prestataire tiers. Les coordonnées exactes de l’hébergeur peuvent être communiquées sur simple demande."
    ]
  },
  {
    title: "Propriété intellectuelle",
    content: [
      "L’ensemble des textes, logos, charte graphique et structure du site sont la propriété exclusive de Vite & Gourmand.",
      "Les photographies utilisées sur ce site proviennent de Unsplash et sont utilisées dans le respect de leur licence.",
      "Toute reproduction, même partielle, des éléments originaux (textes, design, logos) est strictement interdite sans autorisation écrite préalable."
    ]
  },
  {
    title: "Données personnelles (RGPD)",
    content: [
      "Conformément au Règlement Général sur la Protection des Données, vous disposez des droits suivants :",
      [
        "Droit d’accès, de rectification et de suppression de vos données",
        "Droit à la portabilité de vos données",
        "Droit d’opposition au traitement de vos données"
      ],
      "Les données collectées (nom, prénom, email, téléphone, adresse) sont utilisées uniquement pour la gestion des commandes et de la relation client. Elles ne sont jamais transmises à des tiers."
    ]
  },
  {
    title: "Cookies",
    content: [
      "Ce site utilise uniquement des cookies techniques nécessaires au fonctionnement de l’application (authentification de session). Aucun cookie de tracking ou publicitaire n’est déposé."
    ]
  },
  {
    title: "Responsabilité",
    content: [
      "Vite & Gourmand s’efforce de fournir des informations exactes et à jour. Toutefois, nous ne pouvons garantir l’exactitude permanente des informations diffusées.",
      "L’utilisation des informations du site se fait sous la seule responsabilité de l’utilisateur."
    ]
  },
  {
    title: "Droit applicable",
    content: [
      "Les présentes mentions légales sont régies par le droit français. En cas de litige, les tribunaux français seront seuls compétents."
    ]
  }
]
</script>