# BoxManager - Plateforme SaaS Multi-Tenant de Gestion de Self-Stockage

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-green.svg)](https://vuejs.org)
[![PHP](https://img.shields.io/badge/PHP-8.3+-blue.svg)](https://www.php.net)

## 📋 Vue d'ensemble

BoxManager est une plateforme SaaS multi-tenant complète destinée à la gestion de centres de self-stockage (garde-meubles, box de stockage) à travers l'Europe. La solution permet aux opérateurs de gérer l'intégralité de leurs opérations via une interface web moderne et mobile.

### Proposition de valeur

- **Multi-tenant** : Architecture permettant de servir des centaines d'opérateurs
- **Multi-sites** : Gestion centralisée de plusieurs centres depuis une seule interface
- **Multi-pays** : Support natif de 27 pays européens avec spécificités locales
- **Complet** : De la réservation en ligne à la facturation automatique
- **Évolutif** : Infrastructure pensée pour scaler de 1 à 1000+ centres

## 🎯 Objectifs Business

### Phase 1 - MVP (Mois 1-4)
- ✅ Multi-tenancy basique
- ✅ Gestion 1 site par tenant
- 🔄 Gestion box (CRUD simple)
- ⏳ Réservation en ligne simple
- ⏳ Espace client basique
- ⏳ Paiement CB (Stripe)
- ⏳ 3 langues : FR, EN, NL

### Phase 2 - Fonctionnalités avancées (Mois 5-8)
- Multi-sites illimités
- Facturation automatique récurrente
- Prélèvement SEPA
- Signature électronique
- CRM complet
- Application mobile client

### Phase 3 - Scale & Intelligence (Mois 9-12)
- Tarification dynamique (ML)
- API publique v1
- Marketplace partenaires
- IoT et vidéosurveillance

## 🏗️ Architecture Technique

### Stack technologique

**Backend**
- Laravel 11.x
- PHP 8.3+
- PostgreSQL 15+ / MySQL 8+
- Redis 7+

**Frontend**
- Vue.js 3 (Composition API)
- Inertia.js 2.0
- Tailwind CSS 3
- TypeScript 5
- Vite 5

**Packages Laravel principaux**
- spatie/laravel-multitenancy: ^4.0
- spatie/laravel-permission: ^6.23
- spatie/laravel-medialibrary: ^11.17
- inertiajs/inertia-laravel: ^2.0
- laravel/sanctum: ^4.2
- tightenco/ziggy: ^2.6

## 📊 Structure de la base de données

```
Platform
├── tenants (Opérateurs)
│   └── sites (Centres de stockage)
│       └── buildings (Bâtiments)
│           └── floors (Étages)
│               └── boxes (Unités de stockage)
│
├── customers (Clients finaux)
├── contracts (Contrats de location)
├── invoices (Factures)
└── payments (Paiements)
```

## 🚀 Installation

### Prérequis

- PHP 8.3 ou supérieur
- Composer 2.x
- Node.js 18+ et NPM
- PostgreSQL 15+ ou MySQL 8+
- Redis 7+

### Étapes d'installation

1. **Cloner le dépôt**
```bash
git clone <repository-url>
cd box
```

2. **Installer les dépendances PHP**
```bash
composer install
```

3. **Installer les dépendances NPM**
```bash
npm install
```

4. **Configurer l'environnement**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configurer la base de données**

Éditer le fichier `.env` :
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=boxmanager
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. **Exécuter les migrations**
```bash
php artisan migrate
```

7. **Compiler les assets**
```bash
npm run dev
```

8. **Démarrer le serveur**
```bash
php artisan serve
```

L'application sera accessible sur `http://localhost:8000`

## 📚 Documentation

- **[Cahier des spécifications](docs/CAHIER_SPECIFICATIONS.md)** - Spécifications fonctionnelles détaillées
- Architecture technique (À venir)
- Guide développeur (À venir)

## 🔒 Sécurité

### Mesures de sécurité implémentées

- CSRF protection
- XSS prevention
- SQL injection prevention (Eloquent ORM)
- Rate limiting
- Encryption at rest (AES-256)
- Encryption in transit (TLS 1.3)
- RGPD by design

## 📝 Changelog

### [0.1.0] - 2025-11-15

**Ajouté**
- Structure initiale du projet Laravel 11
- Configuration multi-tenancy avec Spatie
- Migrations de base de données (tenants, sites, buildings, floors, boxes)
- Configuration Inertia.js + Vue.js 3
- Documentation initiale

## 📄 Licence

Ce projet est propriétaire. Tous droits réservés.

---

**Version actuelle** : 0.1.0 (MVP en développement)
**Date de dernière mise à jour** : 15 novembre 2025
