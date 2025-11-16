# TODO - BoxManager MVP Phase 1

**Version actuelle** : 0.6.0 (90% complété)
**Dernière mise à jour** : 16 novembre 2025

## ✅ Fonctionnalités complétées (90%)

### Backend
- ✅ Architecture multi-tenant avec Spatie
- ✅ 9 migrations de base de données complètes
- ✅ 9 modèles Eloquent avec relations
- ✅ 5 controllers REST complets (Dashboard, Sites, Boxes, Customers, Contracts)
- ✅ Auto-génération des numéros (contrats, factures, codes d'accès)
- ✅ Workflow automatique de statut des boxes
- ✅ 7 seeders avec données réalistes
- ✅ Soft deletes sur toutes les entités

### Frontend
- ✅ 17 pages Vue.js complètes (Index, Create, Edit, Show pour chaque entité + Dashboard)
- ✅ Layout responsive avec navigation mobile
- ✅ Formulaires avec validation temps réel
- ✅ Recherche et filtres avancés
- ✅ Design moderne Tailwind CSS
- ✅ Calculs automatiques (volume, surface)
- ✅ Relations et liens croisés entre entités

## 🔄 Fonctionnalités restantes (10%)

### 1. Authentification Multi-Tenant (4%)

**Priorité** : HAUTE
**Estimation** : 3-5 heures
**Dépendances** : Laravel Breeze déjà installé

#### Tâches
- [ ] Configurer Laravel Breeze avec Inertia Vue
  ```bash
  php artisan breeze:install vue --dark
  ```
- [ ] Adapter User model pour multi-tenancy
  - Ajouter `tenant_id` à la table users
  - Relations avec Tenant model
  - Middleware tenant scope
- [ ] Créer migrations pour users
  - Migration users table avec tenant_id
  - Migration password_reset_tokens
  - Migration sessions
- [ ] Adapter les pages Breeze au design existant
  - Login.vue (style Tailwind cohérent)
  - Register.vue (avec sélection tenant)
  - ForgotPassword.vue
  - ResetPassword.vue
- [ ] Middleware d'authentification
  - EnsureTenantUser (vérifier user appartient au tenant actuel)
  - RequireAuth sur routes protégées
- [ ] Système de permissions (Spatie Permission déjà installé)
  - Rôles : super_admin, admin, manager, employee
  - Permissions par module (sites, boxes, customers, contracts)
- [ ] Seeder pour utilisateurs
  - 1 super admin
  - 3 admins (1 par tenant)
  - 5 employés de test

#### Fichiers à créer/modifier
```
app/Models/User.php (modifier)
database/migrations/2025_11_16_create_users_table.php
database/migrations/2025_11_16_create_password_reset_tokens_table.php
database/migrations/2025_11_16_create_sessions_table.php
database/seeders/UserSeeder.php
app/Http/Middleware/EnsureTenantUser.php
resources/js/Pages/Auth/Login.vue
resources/js/Pages/Auth/Register.vue
resources/js/Pages/Auth/ForgotPassword.vue
resources/js/Pages/Auth/ResetPassword.vue
routes/auth.php
```

---

### 2. Support Multi-Langue (FR, EN, NL) (3%)

**Priorité** : MOYENNE
**Estimation** : 4-6 heures
**Dépendances** : Aucune

#### Tâches
- [ ] Installer vue-i18n
  ```bash
  npm install vue-i18n@9
  ```
- [ ] Créer fichiers de traduction
  - `resources/js/lang/fr.json` (par défaut)
  - `resources/js/lang/en.json`
  - `resources/js/lang/nl.json`
- [ ] Configurer i18n dans app.js
  - Initialiser vue-i18n
  - Détecter langue du navigateur
  - Stocker préférence dans localStorage
- [ ] Ajouter sélecteur de langue dans navigation
  - Dropdown dans AppLayout.vue
  - Drapeaux FR / EN / NL
  - Sauvegarde de la préférence
- [ ] Traduire toutes les chaînes de texte
  - Remplacer textes hardcodés par $t('key')
  - Dashboard (4 labels KPI + titres)
  - Sites (tous les labels de formulaire)
  - Boxes (tous les labels de formulaire)
  - Customers (tous les labels de formulaire)
  - Contracts (tous les labels de formulaire)
  - Messages de validation
  - Messages de succès/erreur
- [ ] Traduction côté serveur (Laravel)
  - `lang/fr/`, `lang/en/`, `lang/nl/`
  - Messages de validation
  - Emails transactionnels

#### Fichiers à créer
```
resources/js/lang/fr.json (~200 lignes)
resources/js/lang/en.json (~200 lignes)
resources/js/lang/nl.json (~200 lignes)
resources/js/i18n.js
resources/js/Components/LanguageSelector.vue
lang/fr/validation.php
lang/en/validation.php
lang/nl/validation.php
```

#### Exemple de structure de traduction
```json
{
  "nav": {
    "dashboard": "Tableau de bord",
    "sites": "Sites",
    "boxes": "Boxes",
    "customers": "Clients",
    "contracts": "Contrats"
  },
  "dashboard": {
    "total_sites": "Sites totaux",
    "total_boxes": "Boxes totaux",
    "available_boxes": "Boxes disponibles",
    "active_contracts": "Contrats actifs"
  },
  "common": {
    "create": "Créer",
    "edit": "Modifier",
    "delete": "Supprimer",
    "cancel": "Annuler",
    "save": "Enregistrer",
    "search": "Rechercher",
    "filter": "Filtrer",
    "actions": "Actions"
  }
  // ... ~200 clés
}
```

---

### 3. Réservation en Ligne (2%)

**Priorité** : MOYENNE
**Estimation** : 6-8 heures
**Dépendances** : Authentification complétée

#### Tâches
- [ ] Créer page publique de réservation
  - Route publique `/reserve`
  - Sélection du site (liste avec cartes)
  - Filtres par taille de box (XS à XXL)
  - Filtres par équipements
  - Affichage prix en temps réel
- [ ] Formulaire de réservation
  - Informations client (préremplit si connecté)
  - Sélection dates de location
  - Calcul automatique du prix total
  - Récapitulatif avant confirmation
- [ ] Workflow de réservation
  - Vérification disponibilité en temps réel
  - Création contrat avec status "pending"
  - Box marqué comme "reserved" temporairement
  - Email de confirmation au client
  - Email de notification au gestionnaire
- [ ] Page de confirmation
  - Récapitulatif de la réservation
  - Prochaines étapes (paiement, signature)
  - Téléchargement PDF de réservation

#### Fichiers à créer
```
app/Http/Controllers/ReservationController.php
resources/js/Pages/Public/Reserve.vue
resources/js/Pages/Public/ReserveConfirm.vue
app/Mail/ReservationConfirmed.php
app/Mail/ReservationNotification.php
routes/public.php
```

---

### 4. Intégration Paiement Stripe (1%)

**Priorité** : BASSE (peut être déplacé en Phase 2)
**Estimation** : 8-12 heures
**Dépendances** : Réservation en ligne complétée

#### Tâches
- [ ] Installer Laravel Cashier
  ```bash
  composer require laravel/cashier
  ```
- [ ] Configurer Stripe
  - Clés API dans .env
  - Webhooks Stripe
  - Mode test/production
- [ ] Créer page de paiement
  - Intégration Stripe Elements
  - Formulaire de carte bancaire
  - Validation 3D Secure
- [ ] Workflow de paiement
  - Création PaymentIntent
  - Traitement paiement
  - Gestion des erreurs
  - Création du Contract si paiement réussi
  - Génération facture PDF
- [ ] Webhooks Stripe
  - payment_intent.succeeded
  - payment_intent.payment_failed
  - charge.refunded
- [ ] Tableau de bord paiements
  - Liste des transactions
  - Statuts des paiements
  - Export comptable

#### Fichiers à créer
```
config/cashier.php
app/Http/Controllers/PaymentController.php
app/Http/Controllers/WebhookController.php
resources/js/Pages/Payment/Checkout.vue
resources/js/Pages/Payment/Success.vue
resources/js/Pages/Payment/Failed.vue
database/migrations/2025_11_16_add_stripe_columns.php
```

---

## 📊 Priorisation recommandée

### Sprint 1 (Semaine 1) - Essentiel
1. **Authentification multi-tenant** (4%)
   - Sans cela, l'application n'est pas sécurisée
   - Bloquant pour la mise en production

### Sprint 2 (Semaine 2) - Important
2. **Support multi-langue** (3%)
   - Requis pour marché européen
   - Améliore considérablement UX

### Sprint 3 (Semaine 3-4) - Nice to have
3. **Réservation en ligne** (2%)
   - Fonctionnalité business importante
   - Peut être utilisée sans paiement (réservation + paiement sur place)

### Phase 2
4. **Paiement Stripe** (1%)
   - Peut être déplacé en Phase 2
   - Nécessite plus de temps de développement
   - Alternatives possibles (paiement sur place, virement)

---

## 🎯 Pour atteindre 100% de MVP Phase 1

**Minimum viable** (95%) :
- Authentification ✅
- Multi-langue ✅

**Complet** (100%) :
- Authentification ✅
- Multi-langue ✅
- Réservation en ligne ✅

**Au-delà** (Phase 2) :
- Paiement Stripe
- Facturation automatique récurrente
- Signature électronique
- CRM complet
- Application mobile

---

## 📝 Notes d'implémentation

### Considérations de sécurité
- CSRF protection sur tous les formulaires
- XSS prevention (échapper toutes les sorties)
- Rate limiting sur login (max 5 tentatives / 15min)
- Password hashing avec bcrypt
- 2FA optionnel (Phase 2)

### Performance
- Cache des traductions (15min)
- Eager loading partout (déjà en place)
- Queue pour emails (configuration Redis)
- CDN pour assets statiques (Phase 2)

### Tests
- Feature tests pour authentification
- Browser tests pour réservation (Dusk)
- Tests unitaires pour calculs de prix

---

## 🚀 Commandes utiles

```bash
# Authentification
php artisan breeze:install vue
php artisan migrate
npm install && npm run build

# Multi-langue
npm install vue-i18n@9
npm run build

# Tests
php artisan test
php artisan dusk

# Cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Production
php artisan optimize
npm run build
php artisan migrate --force
```

---

**Prêt pour la production ?**
- ✅ Backend complet
- ✅ Frontend complet
- ⏳ Authentification (Sprint 1)
- ⏳ Multi-langue (Sprint 2)
- 📅 Réservation (Sprint 3)

**Objectif** : MVP Phase 1 à 100% en 2-3 semaines maximum.
