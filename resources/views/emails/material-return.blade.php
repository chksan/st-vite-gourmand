<x-mail::message>
# Retour de matériel requis ⚠️

Bonjour **{{ $order->user->name }}**,

Votre commande **{{ $order->menu->title }}** a bien été livrée.

Du matériel vous a été prêté lors de cette prestation.
Merci de le restituer dans un délai de **10 jours ouvrés**.

<x-mail::panel>
⚠️ Sans retour de votre part dans ce délai, des frais de **600 €** vous seront facturés, conformément aux conditions générales de vente.
</x-mail::panel>

<x-mail::button :url="config('app.url') . '/contact'">
Nous contacter
</x-mail::button>

Merci de votre compréhension,
**L'équipe Vite & Gourmand**
</x-mail::message>