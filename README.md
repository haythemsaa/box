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

### Phase 1 - MVP (Mois 1-4) - EN COURS
- ✅ Multi-tenancy basique (Spatie)
- ✅ Base de données complète (9 migrations + users)
- ✅ Modèles Eloquent avec relations
- ✅ Frontend Vue.js 3 + Inertia.js
- ✅ Gestion Sites (CRUD complet)
- ✅ Gestion Boxes (CRUD complet avec calculs auto)
- ✅ Gestion Clients (CRUD complet avec types dynamiques)
- ✅ Gestion Contrats (CRUD complet avec workflow)
- ✅ Dashboard avec statistiques
- ✅ Seeders avec données de test (+ UserSeeder)
- ✅ Authentification multi-tenant (Laravel Breeze + Inertia)
- ✅ Support multi-langue (FR, EN, NL avec vue-i18n)
- ⏳ Réservation en ligne
- ⏳ Paiement CB (Stripe)

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

Le fichier `.env` est déjà créé avec les valeurs par défaut. Modifiez-le selon votre configuration :

```bash
# Générer la clé d'application (déjà fait)
php artisan key:generate

# Configurer la base de données PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=boxmanager
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

5. **Créer la base de données**
```bash
# PostgreSQL (recommandé)
createdb boxmanager

# OU MySQL
mysql -u root -p -e "CREATE DATABASE boxmanager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

6. **Exécuter les migrations et seeders**
```bash
# Créer les tables
php artisan migrate

# Peupler avec des données de test
php artisan db:seed
```

Cela créera :
- 3 tenants de démonstration
- 5 sites dans 4 pays européens
- 100+ boxes de différentes tailles
- ~15 clients (particuliers et entreprises)
- ~40 contrats actifs et en attente

7. **Compiler les assets**
```bash
# Mode développement (avec hot reload)
npm run dev

# OU mode production
npm run build
```

8. **Démarrer le serveur**
```bash
php artisan serve
```

L'application sera accessible sur `http://localhost:8000`

### 🎯 Accès rapide

Une fois l'installation terminée, vous pouvez explorer :
- **Dashboard** : http://localhost:8000/dashboard
- **Sites** : http://localhost:8000/sites
- **Boxes** : http://localhost:8000/boxes

## 📚 Documentation

- **[Cahier des spécifications](docs/CAHIER_SPECIFICATIONS.md)** - Spécifications fonctionnelles complètes (77+ pages)
- **[Guide d'installation](README_SETUP.md)** - Instructions détaillées d'installation et configuration
- **[Résumé du projet](PROJECT_SUMMARY.md)** - Vue d'ensemble complète et métriques
- Architecture technique (À venir)
- Guide développeur (À venir)

### 📊 Fonctionnalités actuelles

#### Backend
- ✅ 10 migrations complètes avec contraintes et indexes (dont users multi-tenant)
- ✅ 10 modèles Eloquent avec relations bidirectionnelles (+ User)
- ✅ Auto-génération des numéros (contrats, factures, codes d'accès)
- ✅ Auto-calcul volume/surface des boxes
- ✅ Soft deletes sur toutes les entités
- ✅ 8 seeders avec données réalistes (+ UserSeeder)
- ✅ 5 controllers REST (Dashboard, Sites, Boxes, Customers, Contracts)
- ✅ Workflow automatique de statut des boxes selon contrats
- ✅ Laravel Breeze avec Inertia pour authentification
- ✅ Modèle User adapté pour multi-tenancy avec rôles

#### Frontend
- ✅ Configuration Inertia.js + Vue.js 3 + Vite
- ✅ Layout responsive avec navigation mobile complète
- ✅ Dashboard avec 4 KPI et activité récente
- ✅ CRUD Sites complet (Index, Create, Edit, Show)
- ✅ CRUD Boxes complet (Index, Create, Edit, Show)
- ✅ CRUD Customers complet (Index, Create, Edit, Show)
- ✅ CRUD Contracts complet (Index, Create, Edit, Show)
- ✅ Pages d'authentification Breeze (Login, Register, etc.)
- ✅ Pages Show détaillées avec relations et statistiques
- ✅ Sélecteurs hiérarchiques en cascade pour Boxes
- ✅ Formulaires dynamiques selon type de client
- ✅ Recherche et filtres avancés sur toutes les entités
- ✅ Formulaires avec validation temps réel
- ✅ Calculs automatiques (volume/surface)
- ✅ Support multi-langue complet (FR, EN, NL)
  - vue-i18n configuré et intégré
  - Sélecteur de langue dans la navigation
  - 3 fichiers de traduction complets (~180 clés chacun)
  - Stockage de la préférence utilisateur
- ✅ 23+ pages Vue.js complètes et fonctionnelles
- ✅ Design moderne et cohérent avec Tailwind CSS

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

### [0.7.0] - 2025-11-16 (Current)

**Ajouté**
- Authentification multi-tenant avec Laravel Breeze
  - Migration users adaptée pour multi-tenancy (tenant_id, role, is_active)
  - Modèle User avec relations tenant et scopes
  - UserSeeder créant 1 super admin + admin/manager/employees par tenant
  - Pages d'authentification (Login, Register, Password Reset, etc.)
  - Rôles utilisateur (super_admin, admin, manager, employee)
- Support multi-langue complet (FR, EN, NL)
  - Installation et configuration de vue-i18n@9
  - 3 fichiers de traduction complets (~180 clés chacun)
  - Sélecteur de langue dans la navigation avec drapeaux
  - Détection automatique de la langue du navigateur
  - Stockage de la préférence dans localStorage
  - Traductions pour toutes les pages et composants

**Amélioré**
- app.js configuré avec i18n global
- Navigation enrichie avec sélecteur de langue
- Architecture prête pour l'internationalisation

**Technique**
- Assets compilés: 328.70 KB (114.06 KB gzipped)
- 819 modules transformés
- +5 packages npm (vue-i18n + dépendances)

### [0.6.0] - 2025-11-16

**Ajouté**
- Pages Show détaillées pour toutes les entités
  - Customers/Show.vue avec informations client et liste des contrats
  - Boxes/Show.vue avec caractéristiques, équipements et localisation
  - Sites/Show.vue avec vue d'ensemble, statistiques et grille de boxes
- Navigation mobile complète avec tous les liens
- Affichage visuel des équipements des boxes (électricité, éclairage, climatisation)
- Grille interactive des boxes par site avec codes couleur de statut
- Statistiques en temps réel sur les pages Show (nombre de boxes, disponibilité)
- Liens croisés entre entités pour navigation fluide

**Amélioré**
- Navigation avec liens Clients et Contrats dans menu mobile
- Affichage des relations entre entités (contrats ↔ clients ↔ boxes)
- Design cohérent avec badges de statut colorés
- Formatage des dates et montants selon locale FR

### [0.5.0] - 2025-11-16

**Ajouté**
- Gestion complète des Contrats (CRUD)
  - ContractController avec toutes les opérations CRUD
  - Contracts/Index.vue avec recherche, filtres par statut et pagination
  - Contracts/Create.vue avec sélecteurs client/box et validation
  - Contracts/Show.vue avec vue détaillée, factures et paiements associés
  - Contracts/Edit.vue avec édition des montants et statut
- Workflow automatique box-contrat
  - Box devient "occupé" quand contrat passe à "actif"
  - Box redevient "disponible" quand contrat expire ou est annulé
- Recherche multi-critères (numéro de contrat, nom du client)
- Filtres par statut (brouillon, en attente, actif, expiré, annulé)
- Auto-remplissage du montant mensuel depuis le prix du box
- Affichage des factures récentes (10 dernières)
- Affichage des paiements récents (10 derniers)

**Amélioré**
- Navigation avec lien Contrats dans le menu
- Routes avec ContractController resource
- Logique métier pour gestion du cycle de vie des contrats
- Cohérence du design avec Tailwind CSS

### [0.4.0] - 2025-11-15

**Ajouté**
- Gestion complète des Clients (CRUD)
  - CustomerController avec recherche, filtres et pagination
  - Customers/Index.vue avec table interactive et filtres
  - Customers/Create.vue avec formulaire dynamique selon le type
  - Customers/Edit.vue avec pré-remplissage et validation
- Support clients particuliers ET entreprises
  - Champs dynamiques selon le type sélectionné
  - Validation conditionnelle côté serveur
  - Interface radio pour sélection du type
- Recherche multi-critères (nom, email, téléphone, entreprise)
- Filtres par type (particulier/entreprise) et statut
- Badges visuels pour type et statut
- Compteur de contrats par client

**Amélioré**
- Navigation avec lien Clients dans le menu
- Routes avec CustomerController resource
- Design cohérent avec le reste de l'application

### [0.3.0] - 2025-11-15

**Ajouté**
- Formulaires CRUD complets pour Sites (Create/Edit avec validation)
- Formulaires CRUD complets pour Boxes (Create/Edit avec hiérarchie)
- Calculs automatiques volume/surface en temps réel
- Sélecteurs hiérarchiques en cascade (Site → Bâtiment → Étage)
- Boutons de suppression avec confirmation
- Documentation complète du projet (PROJECT_SUMMARY.md)

**Amélioré**
- Design des formulaires avec Tailwind CSS
- Expérience utilisateur avec états de chargement
- Validation des formulaires côté client
- Navigation et breadcrumbs

### [0.2.0] - 2025-11-15

**Ajouté**
- 7 seeders complets avec données de test réalistes
- Configuration environnement (.env avec PostgreSQL)
- Guide d'installation détaillé (README_SETUP.md)
- Génération automatique de la clé application
- 3 tenants de démonstration (FR, BE, DE/NL)
- 100+ boxes avec prix et caractéristiques variés

**Données générées**
- 5 sites dans 4 pays européens
- ~10 bâtiments avec étages
- 100+ boxes (6 tailles: XS à XXL)
- ~15 clients (particuliers et entreprises)
- ~40 contrats actifs et en attente

### [0.1.0] - 2025-11-15

**Ajouté**
- Structure initiale du projet Laravel 11
- Configuration multi-tenancy avec Spatie
- 9 migrations de base de données complètes
- 9 modèles Eloquent avec relations
- Configuration Inertia.js + Vue.js 3 + Vite
- Layout responsive avec navigation
- Dashboard avec statistiques
- Pages Sites et Boxes (Index)
- 3 controllers (Dashboard, Sites, Boxes)
- Documentation fonctionnelle (77+ pages)

**Configuration**
- PostgreSQL comme base de données
- Tailwind CSS pour le styling
- Ziggy pour les routes
- Spatie packages (Multi-tenancy, Permissions, Media)

## 📄 Licence

Ce projet est propriétaire. Tous droits réservés.

---

**Version actuelle** : 0.6.0 (MVP Phase 1 - 90% complété)
**Date de dernière mise à jour** : 16 novembre 2025
**Prochaine étape** : Authentification multi-tenant + Réservation en ligne + Multi-langue
