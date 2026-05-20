<template>
  <div class="min-h-screen bg-[#F5F0E8]">
    <div class="bg-[#3D2B1F] py-12 px-6">
      <div class="max-w-3xl mx-auto">
        <p class="text-[#C1813A] text-xs font-semibold tracking-[3px] uppercase mb-3">Vite & Gourmand</p>
        <h1 class="text-[#F5F0E8] text-4xl font-bold">Conditions Générales de Vente</h1>
      </div>
    </div>

    <div class="max-w-3xl mx-auto px-6 py-12 space-y-10">

      <div class="bg-[#FAEDE9] border border-[#8B4A3C] rounded-2xl p-6 flex gap-4">
        <span class="text-[#8B4A3C] text-xl">⚠</span>
        <p class="text-[#8B4A3C] text-sm leading-relaxed">
          Toute commande passée sur ce site implique l'acceptation pleine et entière des présentes Conditions Générales de Vente.
          Veuillez les lire attentivement avant de commander.
        </p>
      </div>

      <section v-for="section in sections" :key="section.title"
               class="bg-white rounded-2xl p-8 border border-[#E8C98A]">
        <h2 class="text-[#3D2B1F] text-lg font-bold mb-4 flex items-center gap-3">
          <span class="w-1 h-5 bg-[#C1813A] rounded-full inline-block"></span>
          {{ section.title }}
        </h2>
        <div class="space-y-3">
          <p v-for="(line, i) in section.lines" :key="i"
             class="text-sm leading-relaxed"
             :class="line.highlight
              ? 'bg-[#FAEDE9] border border-[#8B4A3C] text-[#8B4A3C] rounded-xl px-4 py-3 font-semibold'
              : line.bold
                ? 'text-[#3D2B1F] font-semibold'
                : 'text-[#7A6E62]'">
            {{ line.text }}
          </p>
        </div>
      </section>

      <p class="text-center text-[#7A6E62] text-xs pb-4">
        Dernière mise à jour : {{ lastUpdated }}
      </p>

    </div>
  </div>
</template>

<script setup>
const lastUpdated = new Date().toLocaleDateString('fr-FR', { year: 'numeric', month: 'long', day: 'numeric' })

const sections = [
  {
    title: 'Article 1 — Objet',
    lines: [
      { text: "Les présentes CGV définissent les droits et obligations des parties dans le cadre de la vente de prestations traiteur proposées par Vite & Gourmand (Julie et José), ci-après dénommé « le Prestataire »." },
      { text: "Elles s'appliquent à toute commande passée via l'application web vitegourmand.fr." },
    ]
  },
  {
    title: 'Article 2 — Commandes et délais',
    lines: [
      { text: "Chaque menu précise ses propres conditions de commande (délai minimal avant la date de prestation). Ce délai est clairement indiqué sur la page de chaque menu et doit impérativement être respecté." },
      { text: "⚠ Toute commande passée en dehors de ce délai ne pourra pas être garantie et pourra être annulée par le Prestataire.", highlight: true },
      { text: "La commande est considérée comme validée après réception du mail de confirmation envoyé automatiquement à l'adresse email fournie lors de la commande." },
    ]
  },
  {
    title: 'Article 3 — Prix et livraison',
    lines: [
      { text: "Les prix affichés sont en euros TTC, pour le nombre minimum de personnes indiqué sur chaque menu." },
      { text: "Une réduction de 10% est automatiquement appliquée pour toute commande portant sur 5 personnes de plus que le minimum indiqué dans le menu.", bold: true },
      { text: "La livraison est incluse pour toute prestation réalisée dans la ville de Bordeaux." },
      { text: "Pour toute livraison hors Bordeaux : frais fixes de 5,00 € + 0,59 € par kilomètre parcouru.", bold: true },
      { text: "Le détail du prix (menu + livraison) est affiché avant toute validation de commande." },
    ]
  },
  {
    title: 'Article 4 — Annulation et modification',
    lines: [
      { text: "L'annulation ou la modification d'une commande est possible tant qu'un employé n'a pas passé la commande en statut « Accepté »." },
      { text: "Une fois la commande acceptée, aucune annulation ni modification ne sera possible sans prise de contact préalable avec le Prestataire." },
      { text: "Le Prestataire se réserve le droit d'annuler une commande après avoir contacté le client par téléphone ou email, en précisant le motif d'annulation." },
    ]
  },
  {
    title: 'Article 5 — Matériel prêté',
    lines: [
      { text: "Dans le cadre de certaines prestations, du matériel peut être prêté au client (plats, ustensiles, équipements de service, etc.)." },
      { text: "⚠ Le client dispose de 10 jours ouvrés à compter de la fin de la prestation pour restituer le matériel prêté. Passé ce délai, des frais de 600,00 € seront facturés.", highlight: true },
      { text: "Le client sera notifié par email lorsque la commande passe en statut « En attente du retour de matériel ». Pour organiser la restitution, le client doit prendre contact avec le Prestataire." },
    ]
  },
  {
    title: 'Article 6 — Responsabilité',
    lines: [
      { text: "Le Prestataire s'engage à réaliser les prestations commandées dans les règles de l'art et en conformité avec les normes d'hygiène alimentaire en vigueur." },
      { text: "Le client est responsable de s'assurer que les informations fournies lors de la commande (adresse, date, heure, nombre de personnes) sont exactes." },
      { text: "Les informations relatives aux allergènes sont indiquées sur chaque plat. Il appartient au client de vérifier la composition des menus avant commande." },
    ]
  },
  {
    title: 'Article 7 — Données personnelles',
    lines: [
      { text: "Les données personnelles collectées lors de la commande (nom, prénom, email, téléphone, adresse) sont utilisées exclusivement dans le cadre de la gestion de la prestation. Conformément au RGPD, vous disposez d'un droit d'accès, de modification et de suppression de vos données. Pour toute demande : contact@vitegourmand.fr." },
    ]
  },
  {
    title: 'Article 8 — Litiges',
    lines: [
      { text: "En cas de litige, une solution amiable sera recherchée en priorité. À défaut, les tribunaux compétents de Bordeaux seront saisis. Les présentes CGV sont soumises au droit français." },
    ]
  },
]
</script>