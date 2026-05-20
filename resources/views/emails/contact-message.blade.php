<x-mail::message>
# Nouveau message de contact 📬

Vous avez reçu un nouveau message via le formulaire de contact.

<x-mail::table>
| | |
|---|---|
| **Expéditeur** | {{ $senderEmail }} |
| **Objet** | {{ $title }} |
</x-mail::table>

**Message :**

<x-mail::panel>
{{ $description }}
</x-mail::panel>
**L'application Vite & Gourmand**
</x-mail::message>