<x-mail::message>
# Votre compte employé a été créé 👋

Bonjour **{{ $employee->name }}**,

Un compte employé vient d'être créé pour vous sur l'application **Vite & Gourmand**.

<x-mail::table>
| | |
|---|---|
 **Identifiant (email)** | {{ $employee->email }} |
</x-mail::table>

<x-mail::panel>
🔒 **Mot de passe :** Pour des raisons de sécurité, votre mot de passe ne figure pas dans ce mail. Veuillez vous rapprocher de l'administrateur pour l'obtenir.
</x-mail::panel>

Une fois connecté, vous aurez accès à votre espace employé pour gérer les commandes, les menus et les avis clients.
<x-mail::button :url="config('app.url') . '/login'">
Accéder à l'application
</x-mail::button>

Bienvenue dans l'équipe,
**L'équipe Vite & Gourmand**
</x-mail::message>