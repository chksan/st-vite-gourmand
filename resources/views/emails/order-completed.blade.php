<x-mail::message>
# Merci pour votre confiance ! 🌟

Bonjour **{{ $order->user->name }}**,

Votre commande **{{ $order->menu->title }}** est désormais terminée. Nous espérons que cette prestation vous a pleinement satisfait.

<x-mail::panel>
⭐ Votre avis est précieux pour nous et pour nos futurs clients ! Connectez-vous à votre espace personnel et rendez-vous dans **Mes commandes** pour laisser une note et un commentaire.
</x-mail::panel>

<x-mail::button :url="config('app.url') . '/mon-espace'">
Laisser mon avis
</x-mail::button>

À bientôt,
**L'équipe Vite & Gourmand**
</x-mail::message>
