# Vite & Gourmand

**Traiteur d'exception à Bordeaux** — Site web réalisé pour l'ECF Développeur Web & Web Mobile.

---

## 🚀 Présentation du Projet

Vite & Gourmand est une plateforme de réservation de menus traiteur haut de gamme basée à Bordeaux. Julie et José proposent des prestations gourmandes sur mesure pour tous types d'événements (Noël, Pâques, mariages, entreprises, etc.).

---

## 🛠 Technologies utilisées

- **Backend** : Laravel 13
- **Frontend** : Vue 3 + Vite + Tailwind CSS
- **Base de données relationnelle** : MySQL
- **Base de données non relationnelle** : MongoDB (statistiques administrateur)
- **Email** : Resend
- **Authentification** : Laravel Session (Auth natif Laravel)
- **Déploiement** : Railway (backend + base de données) + Cloudflare (domaine)

---

## 📋 Installation en local

> Les étapes suivantes supposent un environnement **Laragon** (Windows).

### 1. Cloner le repository

```bash
git clone https://github.com/chksan/st-vite-gourmand.git
cd st-vite-gourmand
```

### 2. Installation des dépendances

```bash
composer install
npm install
```

### 3. Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Voici les variables essentielles à configurer dans votre `.env` :

```env
APP_NAME="Vite & Gourmand"
APP_ENV=local
APP_KEY=                        # généré via php artisan key:generate
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr

# ====================================
# COMPANY INFO
# ====================================
MAIL_COMPANY_EMAIL="julie@vitegourmand.fr"
MAIL_COMPANY_NAME="Vite & Gourmand"

# ====================================
# BASE DE DONNÉES MySQL
# ====================================
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vite_gourmand
DB_USERNAME=root
DB_PASSWORD=

# ====================================
# MongoDB (stats admin)
# ====================================
MONGODB_HOST=127.0.0.1
MONGODB_PORT=27017
MONGODB_DATABASE=vite_gourmand_stats

# ====================================
# EMAIL (Resend) https://resend.com/
# ====================================
MAIL_MAILER=resend
RESEND_API_KEY="re_xxxxxxxxxxxxxxxxxxxxxxxxxx"
MAIL_FROM_ADDRESS="contact@vite-gourmand.fr?"
MAIL_FROM_NAME="Vite & Gourmand"

SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database
BCRYPT_ROUNDS=12
```

### 4. Base de données

Exécutez les étapes dans cet ordre :

```bash
# 1. Importer la structure de la base de données
mysql -u root -p vite_gourmand < database/create_db.sql

# 2. Lancer les migrations Laravel
php artisan migrate

# 3. Importer les données de test
mysql -u root -p vite_gourmand < database/seed_db.sql
```

> MongoDB doit être installé et démarré séparément avant de lancer l'application.

### 5. Lancement

```bash
# Terminal 2
npm run dev
```

L'application est accessible sur `http://localhost:5173` ou sur le vhost créer par Laragon (Dans mon cas vite-gourmand.test).

---

## 🚢 Déploiement

L'application est déployée sur **Railway** (hébergement + base de données MySQL & Mongodb).  
Le nom de domaine est géré via **Cloudflare** (DNS + proxy).

URL de production : `https://vite-gourmand.chksan.dev`

---

## 🔑 Identifiants de test

| Rôle           | Email                 | Mot de passe |
|----------------|-----------------------|--------------|
| Administrateur | julie@vitegourmand.fr | password     |
| Employé        | jose@vitegourmand.fr  | password     |
| Client         | client@test.fr        | password     |

> ⚠️ Le compte Administrateur ne peut pas être créé depuis l'application. Il est seeded directement en base de données.

---

## 📁 Structure du projet

```text
st-vite-gourmand/
├─ app/                  # Logique backend Laravel (Models, Controllers, Middleware)
├─ bootstrap/            # Initialisation du framework
├─ config/               # Fichiers de configuration Laravel
├─ database/             # Migrations, create_db.sql, seed_db.sql
├─ public/               # Assets publics
├─ resources/
│  ├─ js/                # Composants Vue 3
│  └─ css/               # Styles Tailwind CSS
├─ routes/               # Routes API et web (api.php)
├─ storage/              # Uploads (images menus) et logs
└─ .env.example          # Template de configuration d'environnement
```

---

## 🌿 Gestion des branches Git

```
main                  ← branche de production (stable)
└── develop           ← branche de développement
```

Chaque fonctionnalité est développée sur une branche dédiée issue de `develop`.  
Après validation, merge vers `develop`, puis vers `main` une fois la version stable.

---

## ✨ Fonctionnalités principales

- Authentification complète + réinitialisation de mot de passe
- Catalogue de menus avec filtres (prix, thème, régime, personnes) et allergènes
- Système de commande avec calcul de frais de livraison (5€ + 0.59€/km hors Bordeaux)
- Réduction de 10% pour les commandes de 5 personnes de plus que le minimum
- Gestion du stock en temps réel
- Upload d'images pour les menus
- Espace Employé (commandes, menus, plats, horaires, avis)
- Espace Administrateur (statistiques MongoDB, chiffre d'affaires, gestion employés)
- Emails transactionnels via Resend (bienvenue, confirmation commande, réinitialisation, avis)
- Conformité RGPD et accessibilité RGAA

---

## 🔒 Sécurité

- Mots de passe hashés via bcrypt (12 rounds)
- Sessions sécurisées via base de données
- Middleware de rôles (utilisateur / employé / admin)
- Validation stricte de toutes les entrées côté serveur
- Protection CSRF native Laravel

---

## 📄 Documents fournis

- Charte Graphique (PDF)
- Maquettes Desktop + Mobile — 3 bureautiques & 3 mobiles (PDF)
- Mentions Légales + CGV
- Manuel d'utilisation avec identifiants (PDF)
- Fichiers SQL (`create_db.sql` + `seed_db.sql`)
- Documentation technique (MCD, diagrammes d'utilisation et de séquence)
- Documentation de gestion de projet

---

**Projet ECF Développeur Web & Web Mobile**  
Mai 2026