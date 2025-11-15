# BoxManager - Guide de Démarrage

Application SaaS multi-tenant de gestion de centres de self-storage développée avec Laravel 11 + Vue.js 3 + Inertia.js.

## 🚀 Technologies

- **Backend**: Laravel 11.x
- **Frontend**: Vue.js 3 + Inertia.js
- **CSS**: Tailwind CSS
- **Build**: Vite
- **Base de données**: PostgreSQL (recommandé) / MySQL
- **Packages principaux**:
  - spatie/laravel-multitenancy (multi-tenancy)
  - spatie/laravel-permission (gestion des permissions)
  - spatie/laravel-medialibrary (gestion des médias)
  - laravel/sanctum (authentification API)

## 📋 Prérequis

- PHP 8.2+
- Composer 2.x
- Node.js 18+ et npm
- PostgreSQL 14+ ou MySQL 8.0+
- Redis (optionnel)

## 🔧 Installation

### 1. Cloner le projet

```bash
git clone <repository-url>
cd box
```

### 2. Installer les dépendances

```bash
# Dépendances PHP
composer install

# Dépendances JavaScript
npm install
```

### 3. Configuration de l'environnement

Le fichier `.env` est déjà créé. Modifiez les paramètres de base de données selon votre configuration :

**Pour PostgreSQL** (recommandé):
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=boxmanager
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

**Pour MySQL**:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=boxmanager
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Créer la base de données

**PostgreSQL**:
```bash
createdb boxmanager
# ou
psql -U postgres -c "CREATE DATABASE boxmanager;"
```

**MySQL**:
```bash
mysql -u root -p -e "CREATE DATABASE boxmanager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 5. Exécuter les migrations et seeders

```bash
# Exécuter les migrations
php artisan migrate

# Peupler la base de données avec des données de test
php artisan db:seed
```

Cela créera :
- 3 tenants (opérateurs de stockage)
- 5 sites à travers l'Europe
- ~10 bâtiments
- ~30 étages
- 100+ boxes de différentes tailles
- ~15 clients (individuels et entreprises)
- ~40 contrats actifs et en attente

### 6. Compiler les assets

**Mode développement** (avec hot reload):
```bash
npm run dev
```

**Mode production**:
```bash
npm run build
```

### 7. Lancer le serveur

```bash
php artisan serve
```

L'application sera accessible à : http://localhost:8000

## 📊 Données de Test

### Tenants créés

1. **BoxStore Paris**
   - Subdomain: `boxstore-paris`
   - Plan: Professional
   - Sites: Paris Centre, Montreuil

2. **StockSecure Belgium**
   - Subdomain: `stocksecure-be`
   - Plan: Starter
   - Sites: Bruxelles

3. **EuroStorage Group**
   - Subdomain: `eurostorage`
   - Custom domain: `eurostorage.com`
   - Plan: Enterprise
   - Sites: Berlin, Amsterdam

### Tailles de Box

| Taille | Dimensions | Volume | Prix/mois | Type |
|--------|-----------|---------|-----------|------|
| XS | 100x100x200cm | 1 m² | 49,99€ | Standard |
| S | 150x100x200cm | 1,5 m² | 69,99€ | Standard |
| M | 200x100x200cm | 2 m² | 89,99€ | Standard |
| L | 200x150x200cm | 3 m² | 119,99€ | Climatisée |
| XL | 300x150x250cm | 4,5 m² | 159,99€ | Climatisée |
| XXL | 300x200x250cm | 6 m² | 199,99€ | Premium |

## 🗂️ Structure du Projet

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DashboardController.php
│   │   │   ├── SiteController.php
│   │   │   └── BoxController.php
│   │   └── Middleware/
│   │       └── HandleInertiaRequests.php
│   └── Models/
│       ├── Tenant.php
│       ├── Site.php
│       ├── Building.php
│       ├── Floor.php
│       ├── Box.php
│       ├── Customer.php
│       ├── Contract.php
│       ├── Invoice.php
│       └── Payment.php
├── database/
│   ├── migrations/
│   │   ├── 2025_11_15_231553_create_tenants_table.php
│   │   ├── 2025_11_15_231603_create_sites_table.php
│   │   ├── 2025_11_15_231604_create_boxes_table.php
│   │   └── ... (autres migrations)
│   └── seeders/
│       ├── TenantSeeder.php
│       ├── SiteSeeder.php
│       ├── BoxSeeder.php
│       └── ... (autres seeders)
├── resources/
│   ├── js/
│   │   ├── Components/
│   │   │   ├── NavLink.vue
│   │   │   └── ResponsiveNavLink.vue
│   │   ├── Layouts/
│   │   │   └── AppLayout.vue
│   │   ├── Pages/
│   │   │   ├── Dashboard.vue
│   │   │   ├── Sites/Index.vue
│   │   │   └── Boxes/Index.vue
│   │   └── app.js
│   ├── css/
│   │   └── app.css
│   └── views/
│       └── app.blade.php
└── routes/
    └── web.php
```

## 🎯 Fonctionnalités Actuelles

### Backend
- ✅ Architecture multi-tenant
- ✅ Modèles Eloquent complets avec relations
- ✅ Migrations pour toutes les tables
- ✅ Controllers CRUD pour Sites et Boxes
- ✅ Seeders avec données de démonstration
- ✅ Auto-génération des numéros (contrats, factures, codes d'accès)
- ✅ Calcul automatique volume/surface des boxes

### Frontend
- ✅ Configuration Inertia.js + Vue.js 3
- ✅ Layout responsive avec navigation
- ✅ Page Dashboard avec statistiques
- ✅ Page Sites avec grille et cartes
- ✅ Page Boxes avec catalogue filtrable
- ✅ Design Tailwind CSS moderne
- ✅ Support mobile avec menu hamburger

## 🔜 Prochaines Étapes

1. **Authentification**
   - Système de login multi-tenant
   - Gestion des utilisateurs et rôles
   - Permissions par tenant

2. **Pages de Gestion**
   - Formulaires de création/édition Sites
   - Formulaires de création/édition Boxes
   - Gestion des clients
   - Gestion des contrats

3. **Fonctionnalités Métier**
   - Processus de réservation en ligne
   - Génération automatique de factures
   - Intégration paiement Stripe
   - Système de rappels et notifications

4. **Internationalisation**
   - Support multilingue (FR, EN, NL)
   - Gestion des devises
   - TVA par pays

5. **Optimisations**
   - Cache Redis
   - File d'attente pour emails
   - Upload et gestion des médias
   - Export PDF des documents

## 🐛 Dépannage

### Erreur de migration
```bash
# Réinitialiser la base de données
php artisan migrate:fresh --seed
```

### Erreur de compilation Vite
```bash
# Supprimer node_modules et réinstaller
rm -rf node_modules package-lock.json
npm install
npm run build
```

### Permission denied sur storage
```bash
chmod -R 775 storage bootstrap/cache
```

## 📚 Documentation

- [Laravel 11 Documentation](https://laravel.com/docs/11.x)
- [Vue.js 3 Documentation](https://vuejs.org/)
- [Inertia.js Documentation](https://inertiajs.com/)
- [Tailwind CSS Documentation](https://tailwindcss.com/)
- [Spatie Multi-tenancy](https://spatie.be/docs/laravel-multitenancy/v4/introduction)

## 📄 Licence

Propriétaire - Tous droits réservés

---

**Développé avec ❤️ pour la gestion moderne de centres de self-storage en Europe**
