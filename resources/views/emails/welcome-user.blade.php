<x-mail::message>
# Bienvenue chez Vite & Gourmand ! 🎉

Bonjour **{{ $user->name }}**,

Votre compte a bien été créé. Nous sommes ravis de vous accueillir !

vous pouvez dès à présent explorer nos menus et passer commande directement depuis votre espace personnel.

<x-mail::button :url="config('app.url') . '/menus'">
Découvrir nos menus
</x-mail::button>

Si vous avez la moindre question, n'hésitez pas à nous contacter via la page de contact.

À très bientôt,
**L'équipe Vite & Gourmand**
</x-mail::message>