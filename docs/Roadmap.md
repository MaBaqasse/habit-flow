# HabitFlow v2.0 - Roadmap de Développement Détaillé

**Dernier mis à jour** : 26 avril 2026  
**Durée totale estimée** : 10-14 jours (v2.0) + 8-10 jours (v1.0 foundation)  
**Statut** : En planification

---

## 📋 Table des Matières

1. [Vue d'ensemble](#vue-densemble)
2. [Timeline et Jalons](#timeline-et-jalons)
3. [Phases Détaillées](#phases-détaillées)
4. [Matrice de Dépendances](#matrice-de-dépendances)
5. [Risques et Mitigations](#risques-et-mitigations)
6. [Ressources et Outils](#ressources-et-outils)
7. [Critères de Réussite](#critères-de-réussite)

---

## 🎯 Vue d'ensemble

### Stack Technique
- **Backend** : Laravel 12 (PHP 8.2)
- **Database** : MySQL 8.0
- **Frontend** : Blade Templates + Tailwind CSS v4
- **APIs Externes** : Google Calendar API v3 (OAuth 2.0)
- **Email** : Laravel Mail
- **Testing** : Pest 3 + PHPUnit 11
- **Code Quality** : Laravel Pint v1

### Livrables Clés
- ✅ v1.0 : Application complète de suivi d'habitudes
- ✨ v2.0 : Notifications email + Intégration Google Calendar

### Audience
- Utilisateurs individuels (MVP single-user)
- Focus : Productivité personnelle, suivi quotidien

---

## 📅 Timeline et Jalons

```
SEMAINE 1 (v1.0 Foundation)
├─ J1 : Phase 1-2 (Setup + Auth)
├─ J2-3 : Phase 3 (Modèle Habitudes)
├─ J4-5 : Phase 4 (Suivi & Streaks)
├─ J6 : Phase 5 (Dashboard)
└─ J7 : Phase 6 (Historique & Stats)

SEMAINE 2 (v2.0 Nouvelles Features)
├─ J8 : Phase 7 (Infrastructure OAuth)
├─ J9-10 : Phase 8 (Google Calendar OAuth)
├─ J11-12 : Phase 9 (Email Notifications)
├─ J13 : Phase 10 (Email Templates & Scheduling)
└─ J14 : Phase 11-12 (QA & Deployment)
```

### Jalons Critiques
| Jalon | Date | Phase | Status |
|-------|------|-------|--------|
| Setup & Auth fonctionnels | J2 | 1-2 | Critique |
| Modèle habitudes validé | J3 | 3 | Critique |
| v1.0 MVP déployable | J7 | 1-6 | Critique |
| OAuth Google configuré | J8 | 7 | Critique |
| Notifications en production | J13 | 9 |Majeur |
| v2.0 Release Ready | J14 | 11-12 | Critique |

---

## 🔄 Phases Détaillées

---

### ⚙️ **PHASE 1 : Setup & Configuration**

**Durée** : 1 jour  
**Objectifs** :
- Initialiser le projet Laravel 12
- Configurer la base de données MySQL
- Installer et configurer les dépendances de base
- Mettre en place Git et le versioning
- Configurer l'environnement de développement

**Tasks** :
- [ ] Initialiser le projet Laravel 12 avec `php artisan`
- [ ] Configurer `.env` (DB, APP_NAME, APP_DEBUG, etc.)
- [ ] Créer la base de données MySQL (habit_flow_dev)
- [ ] Configurer `config/database.php` pour MySQL 8.0
- [ ] Installer Laravel Pint et ajouter pre-commit hook
- [ ] Configurer VSCode / PHPStorm (xdebug, intellisense)
- [ ] Initialiser repository Git avec `.gitignore` approprié
- [ ] Installer et tester Pest pour les tests
- [ ] Ajouter scripts npm (`dev`, `build`, `test`)
- [ ] Valider que `php artisan serve` fonctionne

**Livrables** :
- Projet Laravel 12 clonable et fonctionnel
- Base de données créée et testée
- Environnement de développement prêt
- Git repository initialisé

**Validation & Tests** :
- [ ] `php artisan serve` démarre sans erreur
- [ ] Accès à http://localhost:8000 affiche la page d'accueil
- [ ] `php artisan tinker` se connecte à la DB
- [ ] `php artisan test` exécute sans erreur
- [ ] `vendor/bin/pint --test` passe

**Dépendances** : Aucune (phase d'amorçage)

**Risques** :
- Version PHP incompatible → Valider PHP 8.2 dès le départ
- DB non accessible → Tester la connexion immédiatement

---

### 🔐 **PHASE 2 : Authentification & User Management**

**Durée** : 1 jour  
**Objectifs** :
- Implémenter l'authentification utilisateur
- Configurer Laravel Breeze
- Mettre en place User model et migrations
- Ajouter middleware de protection des routes

**Tasks** :
- [ ] Installer Laravel Breeze avec Blade + Tailwind CSS
- [ ] Générer migrations pour `users` table
- [ ] Personnaliser User model (ajouter propriétés métier)
- [ ] Configurer `config/auth.php` pour guards/providers
- [ ] Créer routes d'auth protégées (login, register, logout)
- [ ] Implémenter middleware de vérification utilisateur
- [ ] Ajouter tests Pest pour l'authentification
- [ ] Configurer session storage et CSRF protection
- [ ] Ajouter validations form requests pour registration
- [ ] Styliser formulaires auth avec Tailwind CSS

**Livrables** :
- Système d'authentification complet
- Formulaires login/register stylisés
- Tests d'authentification (Pest)
- User model avec validations

**Validation & Tests** :
- [ ] Inscription crée un utilisateur dans la DB
- [ ] Login fonctionne avec identifiants corrects
- [ ] Logout détruit la session
- [ ] Routes protégées redirigent vers login si non-auth
- [ ] Tests Pest pour login/logout/register passent
- [ ] CSRF token présent sur formulaires

**Dépendances** : Phase 1

**Risques** :
- Sessions qui ne persistent pas → Vérifier `config/session.php`
- CSRF failures → Valider tokens dans tous les formulaires

---

### 📝 **PHASE 3 : Modèle d'Habitudes & CRUD Core**

**Durée** : 2 jours  
**Objectifs** :
- Créer le modèle Habit avec relations User
- Implémenter CRUD (Create, Read, Update, Delete)
- Mettre en place factory et seeder pour tests
- Créer l'UI principale de gestion des habitudes

**Tasks** :

**Jour 1 - Modèle & Migration**
- [ ] Créer migration `create_habits_table` avec colonnes :
  - `id`, `user_id`, `name`, `description`, `category`
  - `goal_frequency` (daily/weekly/monthly)
  - `icon`, `color`, `is_active`, `created_at`, `updated_at`
- [ ] Créer model `Habit` avec :
  - Relation `belongsTo(User)`
  - Relation `hasMany(HabitLog)` (phase suivante)
  - Accessor pour status courant
  - Scopes (`active()`, `byCategory()`)
- [ ] Créer HabitFactory avec données réalistes
- [ ] Créer HabitSeeder pour développement
- [ ] Implémenter Policy pour autorisation (user owns habit)

**Jour 2 - CRUD & UI**
- [ ] Créer HabitController (index, show, create, store, edit, update, destroy)
- [ ] Implémenter Form Requests (StoreHabitRequest, UpdateHabitRequest)
- [ ] Créer views Blade :
  - `habits/index.blade.php` (liste avec filtres)
  - `habits/show.blade.php` (détails habitude)
  - `habits/create.blade.php` (formulaire création)
  - `habits/edit.blade.php` (formulaire modification)
- [ ] Ajouter routes CRUD dans `routes/web.php`
- [ ] Implémenter validations (name required, goal_frequency enum, etc.)
- [ ] Styliser UI avec Tailwind CSS (cards, forms, buttons)
- [ ] Ajouter tests Pest pour CRUD

**Livrables** :
- Habit model avec relations
- CRUD fonctionnel (create, read, update, delete)
- UI habitudes intuitive
- Tests unitaires et feature pour CRUD
- Factory et Seeder pour développement

**Validation & Tests** :
- [ ] Créer une habitude via formulaire → enregistrée en DB
- [ ] Lister les habitudes affiche seulement celles de l'utilisateur
- [ ] Éditer habitude met à jour les champs
- [ ] Supprimer habitude la retire de la DB et des listes
- [ ] Tests Pest couvrent tous les cas CRUD
- [ ] Authorization : utilisateur A ne peut pas modifier habitude de B
- [ ] Validations fonctionnent (erreurs affichées côté frontend)

**Dépendances** : Phase 2

**Risques** :
- Authorization bypass → Tester avec multiples utilisateurs
- Validations incomplètes → Couvrir tous les scénarios en Pest

---

### 📊 **PHASE 4 : Suivi Quotidien & Streaks System**

**Durée** : 2 jours  
**Objectifs** :
- Créer système de suivi quotidien (check-ins)
- Implémenter calcul des streaks (séries consécutives)
- Ajouter UI pour log quotidien
- Implémenter statistiques basiques de streak

**Tasks** :

**Jour 1 - Modèle & Logique Streaks**
- [ ] Créer migration `create_habit_logs_table` :
  - `id`, `habit_id`, `logged_date`, `completed_at`, `notes`
  - `streak_count`, `notes`
- [ ] Créer model `HabitLog` avec :
  - Relation `belongsTo(Habit)`
  - Scope pour jour courant, semaine, mois
  - Méthode pour calculer streak courant
- [ ] Créer logique Streak Calculator (service class) :
  - Calculer streak depuis logs ordonnés
  - Gérer les interruptions
  - Stocker le streak en DB
- [ ] Implémenter mutation pour auto-update des streaks
- [ ] Tests unitaires pour calculs de streaks

**Jour 2 - API & UI Check-in**
- [ ] Créer HabitLogController (store pour check-in)
- [ ] Créer route POST `/habits/{id}/log` (check-in)
- [ ] Créer composant UI "Check-in" (Blade partial) :
  - Bouton check-in quotidien
  - Affichage du streak courant
  - Historique des 7 derniers jours (mini-calendar)
- [ ] Intégrer check-in dans `habits/show.blade.php`
- [ ] Ajouter feedback visuel (notification de succès)
- [ ] Créer HabitLog seeder avec données réalistes
- [ ] Tests Pest pour logique check-in et streaks
- [ ] Styliser composants avec Tailwind CSS

**Livrables** :
- HabitLog model avec streak calculations
- Service Streak Calculator
- UI de check-in quotidien
- Affichage des streaks courants
- Tests couvrant logique de streaks
- Seeders pour données de test

**Validation & Tests** :
- [ ] Check-in crée un HabitLog pour le jour courant
- [ ] Streak augmente après chaque check-in consecutif
- [ ] Streak reset après une journée d'absence
- [ ] Affichage du streak courant correct en UI
- [ ] Impossible de log 2x le même jour
- [ ] Tests Pest pour tous les scénarios de streaks
- [ ] Performance acceptable pour calcul de streaks

**Dépendances** : Phase 3

**Risques** :
- Calcul des streaks complexe → Tester exhaustivement edge cases
- Timezone issues pour "jour courant" → Utiliser UTC + transformer côté frontend

---

### 🎨 **PHASE 5 : Tableau de Bord & Visualisations**

**Durée** : 1.5 jours  
**Objectifs** :
- Créer dashboard principal
- Afficher statistiques et progrès
- Implémenter visualisations (graphiques simples)
- Optimiser les queries (éviter N+1)

**Tasks** :

**Jour 1 - Dashboard Layout & Stats**
- [ ] Créer DashboardController avec `index()` method
- [ ] Query optimisée pour charger :
  - All habits de l'user avec leur streak courant
  - Stats du jour (% habits complétés)
  - Stats de la semaine
  - Top 3 habitudes par streak
- [ ] Implémenter eager loading pour éviter N+1 queries
- [ ] Créer view `dashboard/index.blade.php` avec :
  - Résumé quotidien (X/Y habitudes complétées)
  - Grille des habitudes avec badges de streak
  - Vue semaine (mini-calendar)
  - Top performers
- [ ] Styliser dashboard avec Tailwind CSS (grid layout, cards)
- [ ] Ajouter dropdown de filtres (par catégorie, active/inactive)

**Jour 2 - Visualisations & Performance**
- [ ] Implémenter mini chart :
  - Barres simples pour complétions par jour (semaine)
  - Utiliser Chart.js ou Alpine.js + SVG
  - Responsive et mobile-friendly
- [ ] Ajouter widget "Streak Streaks" (plus hauts streaks actuels)
- [ ] Implémenter widget "Recent Activity"
- [ ] Optimiser requêtes SQL (profile avec Debugbar si besoin)
- [ ] Tester avec 100+ habitudes pour perfs
- [ ] Créer tests Pest pour dashboard queries
- [ ] Responsive design pour mobile

**Livrables** :
- Dashboard page fonctionnelle
- Statistiques et visualisations claires
- Queries optimisées (pas de N+1)
- Tests de performance validés
- Design responsive

**Validation & Tests** :
- [ ] Dashboard charge en < 500ms
- [ ] Stats quotidiennes et hebdomadaires exactes
- [ ] Filtres fonctionnent correctement
- [ ] Pas de N+1 queries (vérifier logs DB)
- [ ] Responsive sur mobile (< 768px)
- [ ] Tests Pest pour chaque stat calculée

**Dépendances** : Phase 4

**Risques** :
- N+1 queries → Implémenter eager loading dès le départ
- Performance dashboard → Limiter data chargées et ajouter pagination si besoin

---

### 📈 **PHASE 6 : Historique, Statistiques Avancées & Export**

**Durée** : 1.5 jours  
**Objectifs** :
- Créer pages d'historique détaillé
- Implémenter statistiques avancées (trends, insights)
- Ajouter export de données (CSV)
- Créer rapports complets

**Tasks** :

**Jour 1 - Historique & Rapports**
- [ ] Créer HistoryController
- [ ] Implémenter views :
  - `history/daily.blade.php` → Journal quotidien avec détails
  - `history/habits.blade.php` → Historique par habitude
  - `history/calendar.blade.php` → Calendrier d'année (heatmap style)
- [ ] Créer queries pour historique :
  - Logs filtrés par date, habitude, statut
  - Pagination pour larges datasets
- [ ] Implémenter filtres et tri
- [ ] Styliser pages historique avec Tailwind CSS
- [ ] Ajouter breadcrumbs et navigation

**Jour 2 - Statistiques Avancées & Export**
- [ ] Créer StatisticsService pour calculs :
  - Tendance de completion (% sur 30j, 90j, etc.)
  - Habitudes en hausse/baisse
  - Productivity score
  - Best/worst days of week
- [ ] Créer page `/statistics` affichant :
  - Graphiques de tendances (Chart.js ou Livewire)
  - Insights textuels ("Vous êtes plus productif le lundi")
  - Comparaisons mois vs mois
- [ ] Implémenter export CSV :
  - Route `/habits/export` → Télécharge CSV avec historique complet
  - Inclure dates, streaks, notes
- [ ] Tests Pest pour statistiques
- [ ] Performance test avec gros datasets

**Livrables** :
- Pages historique et statistiques
- Service de calcul statistiques
- Export CSV fonctionnel
- Tests pour tous les calculs
- UI claire et responsive

**Validation & Tests** :
- [ ] Historique affiche tous les logs de l'utilisateur
- [ ] Statistiques matchent les données réelles
- [ ] Export CSV valide et complet
- [ ] Filtres et tris fonctionnent correctement
- [ ] Performance acceptable (< 1s) même avec 1000+ logs
- [ ] Tests Pest pour statistiques complexes

**Dépendances** : Phase 5

**Risques** :
- Calculs statistiques complexes → Implémenter et tester séparément en service
- Performance avec gros datasets → Ajouter indexes sur `habit_logs(user_id, logged_date)`

---

## 🚀 PHASES v2.0 (NOUVELLES FEATURES)

---

### 🔧 **PHASE 7 : Infrastructure & Configuration Google OAuth 2.0**

**Durée** : 1 jour  
**Objectifs** :
- Configurer Google Cloud Console et OAuth 2.0
- Installer et configurer Laravel packages Google
- Implémenter logique de stockage des tokens
- Créer système de refresh automatique des tokens

**Tasks** :
- [ ] Créer Google Cloud Project (console.cloud.google.com)
- [ ] Activer Google Calendar API v3
- [ ] Créer OAuth 2.0 Client ID (Web application)
- [ ] Ajouter Redirect URI : `http://localhost:8000/auth/google/callback`
- [ ] Installer `google/apiclient` et `laravel-socialite` via Composer
- [ ] Créer migration `add_google_auth_to_users` :
  - `google_id`, `google_access_token`, `google_refresh_token`
  - `google_token_expires_at`, `google_calendar_sync_enabled`
- [ ] Configurer `config/services.php` pour Google :
  - CLIENT_ID, CLIENT_SECRET depuis .env
- [ ] Créer GoogleAuthService (logique OAuth) :
  - Échanger code pour token
  - Refresh tokens automatiquement
  - Stocker tokens en DB chiffrés
- [ ] Implémenter token encryption/decryption
- [ ] Tests unitaires pour OAuth logic
- [ ] Documenter setup en README

**Livrables** :
- Google Cloud Project configuré
- Laravel OAuth 2.0 infrastructure
- Token storage et refresh system
- Service de gestion Google Auth
- Tests unitaires pour auth flow

**Validation & Tests** :
- [ ] Google Cloud Project créé et APIs activées
- [ ] OAuth credentials en .env
- [ ] Service peut générer URLs OAuth
- [ ] Tokens stockés et chiffrés en DB
- [ ] Tests unitaires pour OAuth flow
- [ ] Configuration Redirect URI correcte

**Dépendances** : Phase 2

**Risques** :
- OAuth credentials compromises → Utiliser encrypted .env
- Token refresh logic buggée → Tester exhaustivement
- API limits Google → Documenter et implémenter throttling

---

### 📅 **PHASE 8 : Intégration Google Calendar OAuth & Sync**

**Durée** : 2.5 jours  
**Objectifs** :
- Implémenter bouton "Connect Google Calendar"
- Créer logique de synchronisation habitudes ↔ Google Calendar
- Gérer les event créations/mises à jour bidirectionnelles
- Implémenter UI pour configuration sync

**Tasks** :

**Jour 1 - OAuth Login & Token Management**
- [ ] Créer route GET `/auth/google` → Redirige vers Google OAuth
- [ ] Créer route GET `/auth/google/callback` → Traite callback
- [ ] Implémenter GoogleAuthController :
  - Récupère code depuis query
  - Échange code pour access/refresh tokens
  - Stocke tokens en DB (chiffrés)
  - Crée ou met à jour User avec google_id
  - Redirige vers /settings
- [ ] Créer UI bouton "Connect Google Calendar" dans settings
- [ ] Implémenter déconnexion Google (revoke token + DB cleanup)
- [ ] Tests Pest pour OAuth flow complet
- [ ] Gestion des erreurs OAuth (invalid code, access denied, etc.)

**Jour 2 - Calendar Sync Architecture**
- [ ] Créer GoogleCalendarService :
  - Lister calendriers de l'utilisateur
  - Créer événement sur Google Calendar
  - Mettre à jour événement existant
  - Supprimer événement
  - Lister événements (pour reconciliation)
- [ ] Créer model CalendarEvent (optionnel, pour tracking synced events) :
  - `id`, `user_id`, `habit_id`, `google_event_id`, `google_calendar_id`
  - Timestamp de dernière synchro
- [ ] Implémenter logique :
  - Quand habit est créée → Option de créer event Google Calendar
  - Quand habit est checked-in → Crée/met à jour event Google Calendar
  - Quand habit est supprimée → Supprime event Google Calendar
- [ ] Ajouter toggle "Sync to Google Calendar" par habit
- [ ] Tester avec account Google réelle

**Jour 3 - UI & Configuration**
- [ ] Créer page settings `/settings/calendar` :
  - Status connexion Google (connected/disconnected)
  - Bouton "Connect Google Calendar" / "Disconnect"
  - Toggle "Auto-sync habits to Google Calendar"
  - Sélection du calendrier destination (dropdown)
  - Historique dernière synchro
- [ ] Ajouter checkbox par habit : "Sync this to Google Calendar"
- [ ] Styliser UI avec Tailwind CSS
- [ ] Ajouter messages de feedback (success/error)
- [ ] Implémenter background job pour syncs asynchrones
- [ ] Tests Pest pour toute la logique sync

**Livrables** :
- OAuth 2.0 login complet avec Google
- GoogleCalendarService fonctionnel
- Sync bidirectionnelle habitudes ↔ Google Calendar
- UI settings pour configuration
- Background jobs pour syncs asynchrones
- Tests couvrant tous les scénarios

**Validation & Tests** :
- [ ] Connexion Google Calendar crée tokens en DB
- [ ] Créer habitude → Option de syncer vers Google
- [ ] Check-in habitude → Crée ou met à jour event Google Calendar
- [ ] Événements Google Calendar visibles dans app Google Calendar
- [ ] Déconnexion Google → Tokens supprimés, syncs arrêtées
- [ ] Tests Pest pour OAuth, sync logic, error handling
- [ ] Gestion des erreurs (token expiré, API errors, etc.)

**Dépendances** : Phase 7

**Risques** :
- Token expiration durant opération → Implémenter refresh logic
- API rate limits Google → Implémenter throttling et queue
- Data sync conflicts → Documenter stratégie (last-write-wins)
- Permissions insuffisantes → Tester scopes OAuth corrects

---

### 📧 **PHASE 9 : Email Notifications Service**

**Durée** : 2 jours  
**Objectifs** :
- Implémenter system de notifications email
- Créer jobs pour envois quotidiens/hebdomadaires
- Implémenter logique de scheduling et preferences utilisateur
- Configurer SMTP et templates email

**Tasks** :

**Jour 1 - Email Infrastructure & Jobs**
- [ ] Configurer SMTP dans `.env` :
  - MAIL_MAILER, MAIL_HOST, MAIL_PORT
  - MAIL_USERNAME, MAIL_PASSWORD
  - Utiliser Mailtrap ou Mailgun pour dev/test
- [ ] Créer migration `create_notification_preferences_table` :
  - `id`, `user_id`, `daily_email_enabled`, `weekly_email_enabled`
  - `daily_email_time`, `weekly_email_day`, `weekly_email_time`
  - `email_digest_format` (summary, detailed, minimal)
- [ ] Créer NotificationPreference model
- [ ] Implémenter Mailable classes :
  - `DailyDigestMail` → Summary du jour (habits completés, streaks)
  - `WeeklyReportMail` → Stats semaine, insights
- [ ] Créer Jobs :
  - `SendDailyDigestEmailJob` → Pour chaque user
  - `SendWeeklyReportEmailJob` → Samedi soir (configurable)
- [ ] Configurer Laravel Scheduler dans `routes/console.php` :
  - Schedule daily emails 7am (configurable par user)
  - Schedule weekly emails samedi 18h (configurable)
- [ ] Implémenter queue pour traiter emails en arrière-plan
- [ ] Tests unitaires pour jobs et email content

**Jour 2 - Email Templates & User Preferences**
- [ ] Créer templates email Blade :
  - `emails/daily-digest.blade.php` → HTML + texte
  - `emails/weekly-report.blade.php`
  - Inclure logos, links vers app, footer avec preferences
- [ ] Implémenter NotificationPreferenceController :
  - Page settings `/settings/notifications`
  - Toggles pour activer/désactiver emails
  - Sélection horaires et fréquences
  - Sélection format digest (summary/detailed)
- [ ] Ajouter préférences par défaut dans user creation
- [ ] Implémenter opt-out tokens (unsubscribe links) dans emails
- [ ] Créer commande artisan pour test emails :
  - `php artisan email:send-daily-digest --user=ID`
  - Utile pour testing
- [ ] Tester avec Mailtrap/Mailgun
- [ ] Tests Pest pour email generation et sending

**Livrables** :
- Infrastructure SMTP configurée
- Email Mailables pour daily/weekly digests
- Jobs asynchrones pour envois
- Scheduler configuré
- UI preferences utilisateur
- Email templates stylisées
- Tests unitaires et feature

**Validation & Tests** :
- [ ] Emails envoyés à la bonne heure
- [ ] Contenu emails correct et personnalisé
- [ ] Préférences utilisateur respectées
- [ ] Opt-out links fonctionnent
- [ ] Pas d'emails envoyés aux users qui ont désactivé
- [ ] Tests Pest pour toute la logique emails
- [ ] Performance OK même avec 1000+ users

**Dépendances** : Phase 6

**Risques** :
- Emails non envoyés (config SMTP) → Tester imédiatement avec Mailtrap
- Scheduling qui ne déclenche pas → Tester `schedule:work` en dev
- Email content spam-like → Tester deliverability avec test emails

---

### 🎨 **PHASE 10 : Email Templates, Styling & Advanced Features**

**Durée** : 1.5 jours  
**Objectifs** :
- Polir et styliser templates email
- Implémenter email preview
- Ajouter features avancées (unsubscribe, preferences link)
- Tester la délivrabilité

**Tasks** :

**Jour 1 - Email Polish & Styling**
- [ ] Refactor email templates avec :
  - Header avec logo HabitFlow
  - Sections claires pour chaque partie (daily summary, streaks, stats)
  - Color scheme cohérent avec app (Tailwind colors)
  - Footer avec links (app, settings, unsubscribe)
  - Mobile-responsive design (inline styles pour email compatibility)
- [ ] Ajouter images/icons pertinents (emoji ou SVG inlined)
- [ ] Implémenter template inheritance pour DRY (layout)
- [ ] Créer email preview page `/settings/email-preview` :
  - Affiche aperçu des daily/weekly emails
  - Simule les données de l'user actuel
  - Permet d'envoyer email test
- [ ] Tester templates dans multiple email clients (Gmail, Outlook, Apple Mail)
- [ ] Optmiser pour inbox delivery (score SPF/DKIM)

**Jour 2 - Advanced Features & Quality Assurance**
- [ ] Implémenter unsubscribe logic :
  - Token unique par email
  - Link `{app_url}/unsubscribe/{token}` dans footer
  - Désactive les emails pour l'utilisateur
  - Redirect vers page confirmation
- [ ] Ajouter link "Update Preferences" pointant vers `/settings/notifications`
- [ ] Implémenter fallback text pour emails (plain text alternative)
- [ ] Configurer `Reply-To` header pour support emails
- [ ] Implémenter email tracking optionnel (opens, clicks) si désiré
- [ ] Tester deliverability :
  - Mail tester tools (MXToolbox, etc.)
  - Test avec Gmail, Outlook, Yahoo
  - Vérifier pas de spam folder
- [ ] Ajouter logs/monitoring pour email sends
- [ ] Tests Pest pour tous les cas d'usage

**Livrables** :
- Email templates polies et responsive
- Email preview en app
- Unsubscribe system fonctionnel
- Tests de délivrabilité
- Monitoring email sends
- Documentation email setup

**Validation & Tests** :
- [ ] Emails affichent correctement dans Gmail, Outlook, Apple Mail
- [ ] Links dans emails fonctionnent
- [ ] Unsubscribe désactive emails correctement
- [ ] Pas d'emails envoyés après unsubscribe
- [ ] Email preview affiche contenu correct
- [ ] Deliverability score bon (SPF/DKIM configured)
- [ ] Logs montrent statut de chaque email envoyé

**Dépendances** : Phase 9

**Risques** :
- Emails en spam folder → Configurer SPF/DKIM correctement
- Rendering issues → Tester dans multiple clients

---

### ✅ **PHASE 11 : Quality Assurance & Testing Complet**

**Durée** : 1.5 jours  
**Objectifs** :
- Effectuer testing exhaustif v1.0 + v2.0
- Implémenter integration tests complètes
- Valider performance globale
- Bug fixes et polish

**Tasks** :

**Jour 1 - Functional & Integration Testing**
- [ ] Créer scenario tests complets (Pest) :
  - User signup → Create habits → Check-ins → View stats
  - Google Calendar connect → Sync habits → Verify events created
  - Email preferences → Receive daily email → Unsubscribe
- [ ] Test Coverage :
  - Couvrir tous les controllers (80%+ coverage)
  - Tous les models et scopes
  - Services complexes (streak calc, stats, emails)
  - Authorization (users can't access other users' data)
- [ ] Edge case testing :
  - Timezone edge cases (midnight transitions)
  - Leap years, DST changes
  - Large datasets (100+ habits, 1000+ logs)
  - Concurrent requests
- [ ] Browser testing (Dusk si utilisé) :
  - Manual browser testing des flows critiques
  - Responsive design mobile/tablet/desktop
- [ ] Accesibility audit (WCAG 2.1 AA basics)

**Jour 2 - Performance & Polish**
- [ ] Performance testing :
  - Dashboard chargement < 500ms
  - History page avec 1000 logs < 1s
  - Google Calendar sync < 5s
  - Email generation scalable
- [ ] Load testing optionnel (Apache Bench, Locust)
- [ ] Database query optimization :
  - Review slow queries (Laravel Debugbar)
  - Add indexes où nécessaire
  - Verify no N+1 queries
- [ ] Security audit :
  - CSRF protection OK
  - Authorization checks exhaustive
  - Input validation complète
  - SQL injection prevention
  - XSS prevention
- [ ] Code quality :
  - Run `vendor/bin/pint --check` → Fix issues
  - PHP static analysis (optional : phpstan)
  - Review code style consistency
- [ ] User feedback & bug fixes :
  - Test avec real users si possible
  - Document/fix bugs trouvés
  - UI/UX polish basé sur feedback

**Livrables** :
- Comprehensive test suite (80%+ coverage)
- Performance validated
- Security audit passed
- Code quality validated
- Bug list with fixes
- Deployment checklist

**Validation & Tests** :
- [ ] All Pest tests pass (100%)
- [ ] Code coverage 80%+
- [ ] No warnings from Pint
- [ ] Performance benchmarks met
- [ ] Security audit cleared
- [ ] Responsive design validated
- [ ] Accessibility basics OK

**Dépendances** : Phase 8, Phase 10

**Risques** :
- Tests qui prennent trop de temps → Optimiser test suite
- Performance issues découvertes tard → Implémenter plus tôt dans le cycle

---

### 🚀 **PHASE 12 : Deployment & Monitoring**

**Durée** : 1 jour  
**Objectifs** :
- Préparer l'application pour production
- Configurer serveur/hosting
- Implémenter monitoring et logging
- Deployment automatisé
- Post-deployment validation

**Tasks** :

**Pre-Deployment**
- [ ] Créer `.env.production` avec variables de production
- [ ] Configuration sécurité :
  - `APP_DEBUG=false`
  - `APP_ENV=production`
  - Generate APP_KEY si pas fait
  - Configure DB production (credentials sécurisées)
- [ ] Configure mail production (SendGrid, Mailgun, AWS SES)
- [ ] Configure Google OAuth production (ajouter production URLs)
- [ ] Prepare deployment checklist

**Deployment (Laravel Cloud recommandé)**
- [ ] Si utilisant Laravel Cloud :
  - Create project sur cloud.laravel.com
  - Deploy branch via GitHub
  - Configure environment variables
  - Run migrations
- [ ] Alternative (Heroku/DigitalOcean/etc) :
  - Setup server (PHP 8.2, MySQL 8.0, Nginx)
  - Configure SSL/TLS (Let's Encrypt)
  - Setup CI/CD pipeline (GitHub Actions)
  - Deploy application
  - Run migrations en production

**Post-Deployment**
- [ ] Verify application accessible
- [ ] Test login, core flows sur production
- [ ] Verify email sending works
- [ ] Test Google Calendar sync
- [ ] Monitor error logs
- [ ] Setup monitoring/alerting :
  - Error tracking (Sentry optionnel)
  - Log aggregation (Papertrail optionnel)
  - Uptime monitoring
- [ ] Performance monitoring production
- [ ] Backup strategy (DB + user files)
- [ ] Disaster recovery plan

**Livrables** :
- Production-ready application
- Deployed on hosting
- Monitoring configured
- Backup/recovery plan
- Documentation

**Validation & Tests** :
- [ ] App accessible en production
- [ ] Signup → Login → Create habit → Check-in fonctionne
- [ ] Email envoyés depuis production
- [ ] Google Calendar sync fonctionne
- [ ] Error logs nettoyés de warnings
- [ ] Performance acceptable en production
- [ ] Backups configurés et testés

**Dépendances** : Phase 11

**Risques** :
- Production deployment issues → Avoir rollback plan
- Database migrations failure → Test migrations sur prod-like DB d'abord
- Performance issues production → Load test avant deployment

---

## 🔗 Matrice de Dépendances

```
Phase 1: Setup & Config
    ↓
Phase 2: Auth
    ├─→ Phase 3: Habits CRUD
    │       ├─→ Phase 4: Suivi & Streaks
    │       │       ├─→ Phase 5: Dashboard
    │       │       │       └─→ Phase 6: Historique & Stats
    │       │       │           └─→ Phase 9: Notifications
    │       │       └─→ Phase 8: Google Calendar Sync
    │
    └─→ Phase 7: Google OAuth Setup
            ├─→ Phase 8: Google Calendar Integration
            └─→ Phase 10: Email Templates
                ├─→ Phase 11: QA & Testing
                │   └─→ Phase 12: Deployment
```

### Chemins Critiques
1. **Path v1.0** : Phase 1 → 2 → 3 → 4 → 5 → 6 (7 jours min)
2. **Path v2.0** : Phase 7 → 8 → 9 → 10 (6 jours min)
3. **Final** : Phase 11 → 12 (2 jours)

### Opportunités de Parallélisation
- Phase 7 peut commencer après Phase 2 (en parallèle avec Phase 3-6)
- Phase 9-10 peuvent commencer après Phase 6 (indépendant)
- Phase 11 commence quand Phase 8 et 10 sont complètes

---

## ⚠️ Risques et Mitigations

| Risque | Impact | Probabilité | Mitigation |
|--------|--------|-------------|-----------|
| **Timezone/Date bugs** | Streak logic cassée | Haute | Tests exhaustifs pour timezones, utiliser UTC en DB |
| **N+1 queries** | Performance dégradée | Haute | Code review, utilize eager loading, profiling |
| **Google OAuth tokens expirent** | Sync fails silently | Moyenne | Implémenter auto-refresh, monitoring |
| **Email delivery problèmes** | Users ne reçoivent pas emails | Moyenne | Tester SPF/DKIM, utiliser service fiable (SendGrid) |
| **Data sync conflicts (Google ↔ App)** | Duplication/perte données | Moyenne | Documenter stratégie (last-write-wins), versionning |
| **Performance dégradée avec gros datasets** | Dashboard lent | Moyenne | Indexes DB, caching, pagination |
| **API rate limits Google Calendar** | Sync échoue sous charge | Basse | Implémenter queue + throttling, monitoring |
| **Security vulnerabilities** | Data breach/exploit | Basse | Security audit, OWASP compliance, input validation |
| **Deployment failure** | Downtime production | Basse | Rollback plan, pre-prod testing, CI/CD |

---

## 📦 Ressources et Outils Requis

### Développement
- **Editor** : VSCode / PHPStorm avec Laravel extensions
- **Local Environment** : Docker Compose (PHP 8.2, MySQL 8.0, Redis optionnel)
- **Database** : MySQL 8.0
- **Version Control** : Git + GitHub

### Testing & Quality
- **Testing Framework** : Pest 3 + PHPUnit 11
- **Code Formatter** : Laravel Pint v1
- **Database Testing** : Factory + Seeding
- **Performance Profiling** : Laravel Debugbar, xdebug
- **Load Testing** : Apache Bench ou Locust (optionnel)

### External Services
- **Email** : SMTP (dev: Mailtrap, prod: SendGrid/Mailgun/AWS SES)
- **Google APIs** : Google Cloud Console (free tier adequate for MVP)
- **Hosting** : Laravel Cloud (recommandé) ou alternatives (Heroku, DO, AWS)
- **Monitoring** : Sentry (errors), Papertrail (logs) - optionnel

### Dependencies à Installer
```php
// Backend
- laravel/framework ^12.0
- laravel/socialite ^5.0 (pour Google OAuth)
- google/apiclient ^2.0 (pour Google Calendar API)
- laravel/mail ^12.0 (inclus)
- pestphp/pest ^3.0
- phpunit/phpunit ^11.0

// Frontend
- tailwindcss ^4.0
- laravel-vite-plugin

// Optional
- laraveldaily/laravel-charts
- spatie/laravel-query-builder (pour queries complexes)
```

### Documentation Nécessaire
- README.md (setup + deployment)
- API docs (si API exposée)
- Email template docs
- Google OAuth setup guide
- Deployment guide

---

## ✨ Critères de Réussite

### v1.0 Success Criteria
- ✅ Users peuvent s'authentifier (login/signup/logout)
- ✅ CRUD complet pour habitudes (create, read, update, delete)
- ✅ Check-in quotidien fonctionne
- ✅ Streaks calculés correctement
- ✅ Dashboard affiche stats correctes
- ✅ Historique navigable et filtrable
- ✅ Statistiques claires et exactes
- ✅ UI responsive (mobile + desktop)
- ✅ All tests pass (80%+ coverage)
- ✅ No N+1 queries
- ✅ Performance < 500ms pour pages principales

### v2.0 Success Criteria
- ✅ Google Calendar OAuth complète (login + token management)
- ✅ Habits synchées vers Google Calendar
- ✅ Google Calendar events visibles
- ✅ Daily email digests envoyés à l'heure configurée
- ✅ Weekly reports fonctionnels
- ✅ Email preferences rispectées
- ✅ Unsubscribe fonctionne
- ✅ Email deliverability > 95%
- ✅ All integration tests pass
- ✅ Security audit cleared
- ✅ Performance tests passed
- ✅ Deployed et accessible en production

### Déploiement Success Criteria
- ✅ Application accessible en production
- ✅ Tous les flows testés en production
- ✅ Monitoring configuré
- ✅ Backups actifs
- ✅ Rollback plan documenté
- ✅ SLA défini (uptime, response time)
- ✅ Support/issue tracking setup

---

## 📞 Contacts & Escalation

### Points de Décision Clés
1. **Phase 2** : Confirmer auth method (Breeze ok ?)
2. **Phase 3** : Confirmer DB schema habitudes
3. **Phase 7** : Confirmer Google OAuth scopes
4. **Phase 9** : Confirmer email service (SendGrid ok ?)
5. **Phase 12** : Confirmer hosting (Laravel Cloud ok ?)

### Communication Cadence
- **Daily standups** : 10 min (blockers, progress)
- **Weekly reviews** : Phase completion + next priorities
- **Retros** : Post-deployment + retrospective

---

## 📝 Changelog

| Date | Version | Changes |
|------|---------|---------|
| 26-04-2026 | v1.0 | Initial roadmap créé pour v1.0 + v2.0 |

---

**Prochaine étape** : Valider ce roadmap en equipe, puis lancer Phase 1 (Setup & Config).

