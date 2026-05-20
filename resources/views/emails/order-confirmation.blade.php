<x-mail::message>
# Confirmation de votre commande ✅

Bonjour **{{ $order->user->name }}**,

Votre commande a bien été enregistrée. Voici le récapitulatif :

<x-mail::table>
| Détail | Information |
|--------|-------------|
| Menu | {{ $order->menu->title }} |
| Nombre de personnes | {{ $order->nb_personnes }} |
| Date de prestation | {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }} |
| Heure de livraison | {{ substr($order->delivery_time, 0, 5) }} |
| Adresse | {{ $order->delivery_address }} |
| Frais de livraison | {{ $order->delivery_fee }} € |
| **Total** | **{{ $order->total_price }} €** |
</x-mail::table>

@if($order->menu->conditions)
<x-mail::panel>
⚠️ **Conditions importantes :** {{ $order->menu->conditions }}
</x-mail::panel>
@endif

Vous pouvez suivre l'évolution de votre commande depuis votre espace personnel.

<x-mail::button :url="config('app.url') . '/mon-espace'">
Voir ma commande
</x-mail::button>

Merci pour votre confiance,
**L'équipe Vite & Gourmand**
</x-mail::message>