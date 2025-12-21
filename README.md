# 🩺 Doctolib Watcher

> ⚠️ **Projet en cours de développement**  
> Cette application est actuellement en travaux et n'est pas encore fonctionnelle. Les fonctionnalités décrites ci-dessous sont en cours d'implémentation.

Application Laravel pour surveiller automatiquement les créneaux disponibles chez vos praticiens sur Doctolib.

## 🎯 Fonctionnalités

- Surveillance automatique des disponibilités Doctolib
- Notifications par email dès qu'un créneau se libère
- Interface en ligne de commande simple

## 🚀 Installation

```bash
# Installer les dépendances
composer setup

# Configurer l'environnement
cp .env.example .env
php artisan key:generate

# Configurer la base de données
php artisan migrate

# Lancer l'application
composer run dev
```

## ⚙️ Configuration

Dans votre fichier `.env`, configurez :

```env
# Email pour recevoir les notifications
DOCTOLIB_EMAIL=votre-email@example.com

# Configuration mail (SMTP, Mailgun, etc.)
MAIL_MAILER=smtp
MAIL_HOST=...
```

## 📝 Utilisation

### Ajouter un praticien à surveiller

```bash
php artisan watcher:add
```

L'assistant vous guidera pour :
1. Rechercher et sélectionner un praticien
2. Choisir le motif de consultation
3. Définir la période de surveillance (dates début/fin)

### Vérifier manuellement les disponibilités

```bash
php artisan watcher:check
```

### Surveillance automatique

Le système vérifie automatiquement les disponibilités toutes les minutes via le scheduler Laravel.

Pour activer la surveillance automatique :

```bash
php artisan schedule:work
```

## 🧪 Tests

```bash
php artisan test
```

## 📄 Licence

MIT