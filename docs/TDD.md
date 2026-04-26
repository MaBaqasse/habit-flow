# HabitFlow — Technical Design Document (TDD)
## Document de Conception Technique — Version 2.0

| Champ | Détail |
|---|---|
| **Projet** | HabitFlow – Habit Tracker |
| **Auteur** | BAQASSE Maroua – IRISI 1 |
| **Encadrante** | Prof. Sara Qassimi |
| **Date** | Avril 2026 |
| **Version** | 2.0 — Notifications Email + Calendrier Externe |
| **Stack Technique** | Laravel 12 (PHP 8.2), MySQL 8.0, Blade/Tailwind CSS, Laravel Mail, Google Calendar API v3 |

---

## 1. Vue d'Ensemble Architecturale

HabitFlow suit une architecture **Model-View-Controller (MVC)** standard Laravel avec une couche de services supplémentaire pour les intégrations externes (Google Calendar). L'application repose sur une base de données relationnelle MySQL avec 7 tables interconnectées par des relations rigoureuses.

### Principes de conception

- **Séparation des responsabilités** : logique métier isolée dans les Models, logique de contrôle dans les Controllers, logique de présentation dans les Views
- **Réutilisabilité** : services Laravel dédiés (`GoogleCalendarService`, `NotificationService`) pour les opérations complexes
- **Scalabilité** : utilisation des queues Laravel pour les tâches asynchrones (envoi d'emails non-bloquant)
- **Sécurité** : authentification OAuth 2.0 pour Google, encryption des tokens sensibles, authorization policies pour l'accès aux données
- **Maintenabilité** : code commenté selon PSR-12, variables d'environnement centralisées, migrations versionnées

---

## 2. Architecture Logique par Couche

### 2.1 Couche Présentation (Views)

La couche présentation est composée de templates Blade hébergés dans le répertoire `resources/views/`. Chaque vue correspond à une fonctionnalité clé de l'application :

- **Vue d'authentification** : Inscription, connexion, réinitialisation de mot de passe via Laravel Breeze
- **Vue tableau de bord** : Affichage synthétique des habitudes, taux de complétion, graphiques Chart.js
- **Vue gestion des habitudes** : CRUD complet (création, lecture, modification, suppression)
- **Vue suivi quotidien** : Interface pour marquer les habitudes du jour comme complétées
- **Vue historique** : Calendrier mensuel coloré avec status des jours
- **Vue statistiques** : Récapitulatif des performances (taux global, streak record, habitude la plus régulière)
- **Vue paramètres utilisateur** : Modification du profil, des préférences de notification, gestion du calendrier externe
- **Vue notification settings** : Configuration des horaires de rappel, activation/désactivation des types de notification

**Styling** : Tailwind CSS 4 pour l'interface responsive. Mobile-first avec breakpoints (sm, md, lg) pour adaptation mobile/tablette/desktop.

### 2.2 Couche Métier (Models)

Les models Eloquent représentent les entités métier et gèrent la logique métier au niveau de la base de données.

#### Model User
Représente un utilisateur du système. Gère l'authentification, les relations avec les habitudes et les paramètres de notification. Hérite des traits Laravel Authenticatable et HasApiTokens pour la sécurité.

**Relations** :
- `habits()` : relation one-to-many vers les habitudes de l'utilisateur
- `completions()` : relation one-to-many vers les complétions d'habitudes
- `notificationSettings()` : relation one-to-one vers les paramètres de notification
- `calendarSyncs()` : relation one-to-many vers les synchronisations Google Calendar

#### Model Habit
Représente une habitude créée par un utilisateur. Contient les métadonnées (nom, description, fréquence, couleur) et l'état (active/inactive).

**Relations** :
- `user()` : relation inverse vers l'utilisateur propriétaire
- `category()` : relation many-to-one vers une catégorie optionnelle
- `completions()` : relation one-to-many vers les jours de complétion
- `streak()` : relation one-to-one vers les données de streak
- `calendarSync()` : relation one-to-one vers la synchronisation Google Calendar

**Attributs calculés** : Pourcentage de complétion du mois, streak actuelle, jours restants.

#### Model Category
Représente une catégorie d'habitude (ex: Santé, Sport, Productivité). Permet une meilleure organisation et segmentation des habitudes.

**Relations** :
- `habits()` : relation one-to-many vers les habitudes de cette catégorie

#### Model HabitCompletion
Enregistre le marquage d'une habitude comme "complétée" pour une date donnée. Un enregistrement = une habitude cochée un jour spécifique.

**Relations** :
- `habit()` : relation inverse vers l'habitude complétée
- `user()` : relation inverse vers l'utilisateur qui a complété

**Contraintes d'unicité** : composite unique sur (habit_id, user_id, completed_date) pour éviter les doublons.

#### Model Streak
Gère les données de séries (streak courant et meilleur streak). Mis à jour automatiquement lors du marquage/démarquage d'une habitude.

**Relations** :
- `habit()` : relation inverse vers l'habitude associée

**Logique de mise à jour** : recalculée lors de chaque action de complétion/décomplétion via events ou observers Laravel.

#### Model NotificationSettings
Stocke les préférences de notification de chaque utilisateur : horaires, types actifs/inactifs.

**Relations** :
- `user()` : relation inverse vers l'utilisateur

#### Model CalendarSync
Établit la correspondance entre une habitude locale et un événement Google Calendar. Stocke les identifiants externes et le token OAuth.

**Relations** :
- `user()` : relation inverse vers l'utilisateur
- `habit()` : relation inverse vers l'habitude

---

### 2.3 Couche Contrôle (Controllers)

Les controllers orchestrent les requêtes HTTP, appliquent la logique métier via les models et les services, et retournent des réponses appropriées.

#### AuthController (ou resources authenficiées via Laravel Breeze)
Gère l'authentification (inscription, connexion, déconnexion) via Laravel Breeze. Aucune logique personnalisée requise au-delà de celle fournie par Breeze.

#### HabitController
Gère le CRUD des habitudes et la validation des inputs. Actions principales :
- `index()` : affiche la liste des habitudes avec filtrage optionnel par catégorie
- `create()` : formulaire de création
- `store()` : validation et enregistrement d'une nouvelle habitude
- `show()` : détails d'une habitude spécifique
- `edit()` : formulaire de modification
- `update()` : validation et mise à jour
- `destroy()` : suppression sécurisée avec cascade

Chaque action applique une authorization policy pour vérifier que l'utilisateur authentifié possède bien l'habitude.

#### HabitCompletionController
Gère le marquage/démarquage quotidien des habitudes. Actions principales :
- `toggle()` : marque/démarque une habitude pour la date actuelle
- `bulkToggle()` : marque plusieurs habitudes en une requête
- `history()` : retourne l'historique de complétion pour un calendrier mensuel

Déclenche automatiquement la recalculation des streaks et l'envoi de notifications via événements Laravel.

#### DashboardController
Agrège et affiche les données du tableau de bord principal : résumé du jour, statistiques hebdomadaires, graphiques.

Actions principales :
- `index()` : vue d'ensemble du tableau de bord avec les métriques principales

#### StreakController
Affiche les informations détaillées des streaks (courant et meilleur). Pas de modification directe ; les streaks sont mis à jour via `HabitCompletionController`.

#### StatisticsController
Calcule et affiche les statistiques d'utilisation : taux de réussite global, habitude la plus performante, evolution sur les 30 derniers jours.

#### NotificationSettingsController
Permet à l'utilisateur de configurer ses préférences de notification. Actions principales :
- `show()` : affiche les paramètres actuels
- `update()` : met à jour l'heure du rappel, les préférences de fréquence

Aucun envoi d'email directement ; les controllers déclenchent les jobs via la queue.

#### CalendarSyncController
Gère l'authentification OAuth 2.0 avec Google et la synchronisation des habitudes. Actions principales :
- `redirect()` : redirige l'utilisateur vers Google OAuth
- `callback()` : reçoit le code d'autorisation et stocke le token
- `sync()` : force la synchronisation manuelle d'une habitude
- `disconnect()` : révoque le token OAuth

---

### 2.4 Couche Métier Avancée (Services)

Les services encapsulent la logique métier complexe pour améliorer la testabilité et la réutilisabilité.

#### GoogleCalendarService
Service dédié à la gestion de la synchronisation Google Calendar. Responsabilités :
- Initialisation de la connexion à l'API Google Calendar via le token OAuth stocké
- Création d'événements récurrents à partir des habitudes (RRULE selon la fréquence)
- Mise à jour des événements lors de modifications d'habitudes
- Suppression des événements lors de suppressions d'habitudes
- Marquage des occurrences individuelles comme complétées
- Gestion des erreurs de synchronisation et retry automatique

Dépend du package `google/apiclient` et stocke les tokens de manière sécurisée (encrypted).

#### NotificationService
Service dédié à la gestion des notifications par email. Responsabilités :
- Envoi des rappels quotidiens à l'heure configurée par l'utilisateur
- Envoi des récapitulatifs hebdomadaires
- Envoi des alertes de streak en danger
- Gestion des préférences utilisateur (opt-in/opt-out)
- Queuing des jobs pour envoi asynchrone

#### StreakCalculationService
Encapsule la logique de calcul des streaks. Responsabilités :
- Calcul du streak courant basé sur l'historique de complétion
- Mise à jour du meilleur streak
- Détection des ruptures de streak
- Déclenchement des notifications d'alerte

#### StatisticsCalculationService
Calcule les statistiques d'utilisation. Responsabilités :
- Taux de complétion global et par période
- Identification de l'habitude la plus performante
- Génération des données pour les graphiques

---

### 2.5 Couche Données (Database)

#### Schema Principal

**Table users**
- Stocke les comptes utilisateurs avec identification unique par email
- Mots de passe hachés avec bcrypt
- Timestamps d'audit (created_at, updated_at)

**Table categories**
- Liste des catégories d'habitudes disponibles
- Chaque catégorie a un identifiant et un nom
- Code couleur en hexadécimal pour la présentation

**Table habits**
- Habitudes créées par les utilisateurs
- Associations : propriétaire (user_id), catégorie optionnelle (category_id)
- Fréquence : daily ou weekly
- Couleur personnalisée
- Drapeau d'activité (is_active) pour archivage logique

**Table habit_completions**
- Enregistrements de marquage quotidien
- Clé composite unique : (habit_id, user_id, completed_date)
- Note optionnelle pour chaque marquage
- Timestamps pour audit

**Table streaks**
- Données agrégées des séries
- Streak courant et meilleur streak
- Date de dernière complétion pour calcul continu
- Une ligne par habitude

**Table notification_settings**
- Préférences de notification par utilisateur
- Heure du rappel quotidien
- Activation/désactivation des types de notification
- Jour de la semaine pour le récapitulatif

**Table calendar_syncs**
- Correspondances entre habitudes locales et événements Google Calendar
- Stockage du token OAuth (encrypted)
- Horodatage de dernière synchronisation
- Gestion multi-user (plusieurs utilisateurs peuvent synchroniser différentes habitudes)

#### Relations Enforcées

- Cascade delete : suppression d'un utilisateur supprime ses habitudes, complétions, syncs
- Cascade delete : suppression d'une habitude supprime ses complétions et streak
- Intégrité référentielle : impossibilité de créer une completion sans habit valide

#### Indexation

- Index sur `user_id` pour les requêtes filtrées par utilisateur
- Index sur `completed_date` pour les requêtes d'historique
- Index composite sur (habit_id, completed_date) pour les calendriers
- Index sur `email` (UNIQUE) dans users pour recherche rapide d'authentification

---

## 3. Flux Utilisateur Détaillé

### 3.1 Flux d'Authentification

1. **Inscription** : L'utilisateur accède à la page d'inscription, fournit nom, email et mot de passe
2. **Validation** : Vérification que l'email n'existe pas et respect des critères de mot de passe
3. **Stockage** : Enregistrement dans `users` avec mot de passe hachés via bcrypt
4. **Redirection** : Redirection vers le dashboard avec session établie
5. **Connexion** : L'utilisateur saisit email/mot de passe, authentification via Laravel Breeze
6. **Déconnexion** : Invalidation de la session, redirection vers la page d'accueil

### 3.2 Flux de Gestion des Habitudes

**Créer une habitude** :
1. Utilisateur accède au formulaire de création
2. Renseigne : nom, description optionnelle, fréquence (daily/weekly), catégorie optionnelle, couleur
3. Validation côté serveur (nom requis, longueur max, unicité par user)
4. Enregistrement dans `habits` avec user_id et is_active=true
5. Affichage du succès et redirection vers la liste

**Modifier une habitude** :
1. Utilisateur ouvre le formulaire d'édition d'une habitude
2. Préremplissement des champs actuels
3. Modification des champs autorisés
4. Validation et enregistrement
5. Si synchronisée avec Google Calendar, mise à jour de l'événement via `GoogleCalendarService`

**Supprimer une habitude** :
1. Confirmation de suppression
2. Suppression de la ligne dans `habits` (cascade delete vers completions et streak)
3. Si synchronisée avec Google Calendar, suppression de l'événement et de ses occurrences

**Archiver une habitude** (soft delete logique) :
1. Bascule du drapeau `is_active` à false
2. L'habitude n'apparaît plus dans les vues normales
3. Accessible via section "archives"

### 3.3 Flux de Suivi Quotidien

**Marquer une habitude comme complétée (aujourd'hui)** :
1. Utilisateur accède à la vue "Aujourd'hui"
2. Clique sur le bouton de complétion pour chaque habitude
3. Enregistrement dans `habit_completions` avec completed_date=aujourd'hui
4. Recalcul automatique du streak via StreakCalculationService
5. Mise à jour immédiate de l'interface (feedback visuel)
6. Déclenchement de la mise à jour de Google Calendar (événement marqué comme réussi)

**Démarquer une habitude** :
1. Utilisateur clique à nouveau sur le bouton (toggle)
2. Suppression de la ligne dans `habit_completions`
3. Recalcul du streak (potentiellement rupture de série)
4. Déclenchement de la mise à jour Google Calendar

### 3.4 Flux de Synchronisation Google Calendar

**Connexion initiale** :
1. Utilisateur clique "Connecter Google Calendar"
2. Redirection vers `CalendarSyncController@redirect()` qui génère l'URL OAuth Google
3. Google affiche la demande de permission (scope: calendar)
4. Utilisateur accepte et Google redirige vers `CalendarSyncController@callback()` avec un code d'autorisation
5. Échange du code contre un token long-lived
6. Stockage du token dans `calendar_syncs` (encrypted)
7. Synchronisation initiale : création d'événements récurrents pour toutes les habitudes actives

**Synchronisation d'une habitude** :
1. Lors de la création d'une habitude (si utilisateur connecté à Google), création automatique d'un événement
2. Lors de la modification, mise à jour de l'événement (titre, description, RRULE)
3. Lors de la suppression, suppression de l'événement
4. Marquage d'une complétion : mise à jour de l'occurrence spécifique dans Google Calendar

**Déconnexion** :
1. Utilisateur clique "Déconnecter Google Calendar"
2. Révocation du token auprès de Google
3. Suppression des lignes dans `calendar_syncs`
4. Les événements restent dans le calendrier de l'utilisateur (non supprimés automatiquement)

### 3.5 Flux de Notifications

**Notification de rappel quotidien** :
1. Laravel Scheduler déclenche la commande de notification à l'heure configurée
2. Requête vers tous les utilisateurs ayant `email_reminder_enabled=true`
3. Pour chaque utilisateur, récupération des habitudes non complétées du jour
4. Création d'un job `SendReminderEmailJob` dans la queue
5. Worker traite le job et envoie l'email via Laravel Mail
6. Email contient la liste des habitudes + lien direct pour marquer

**Notification de récapitulatif hebdomadaire** :
1. Laravel Scheduler déclenche dimanche soir (ou jour configuré)
2. Calcul du taux de complétion de la semaine
3. Identification de la meilleure habitude
4. Création du job `SendWeeklySummaryEmailJob`
5. Envoi par email du récapitulatif avec graphique en texte et appel à l'action

**Alerte de rupture de streak** :
1. StreakCalculationService détecte qu'un streak actif risque d'être perdu (24h sans marquage + streak ≥7)
2. Création du job `SendStreakAlertEmailJob`
3. Envoi d'une alerte prioritaire avec le nombre de jours en jeu

---

## 4. Intégrations Externes

### 4.1 Google Calendar API v3

**Authentification** :
- OAuth 2.0 avec flux authorization_code
- Scope requis : `https://www.googleapis.com/auth/calendar`
- Token stocké encrypted dans `calendar_syncs` table
- Refresh automatique du token avant expiration

**Opérations principales** :
- **Create** : Création d'événement récurrent (RRULE: FREQ=DAILY pour daily, FREQ=WEEKLY pour weekly)
- **Update** : Modification du titre, description, couleur de l'événement
- **Delete** : Suppression complète incluant toutes les occurrences
- **Patch** : Marquage d'une occurrence individuelle comme complétée/annulée

**Gestion des erreurs** :
- Retry automatique sur erreurs transitoires (timeout, rate limiting)
- Logging des erreurs de synchronisation pour diagnostic
- Notification utilisateur en cas d'échec permanent

### 4.2 Email (SMTP)

**Configuration** :
- Pilote SMTP configurable via `.env` (Mailtrap pour tests, SendGrid/Gmail pour production)
- Adresse expéditeur configurable
- Format HTML via templates Blade dans `resources/mails/`

**Types de messages** :
- Rappel quotidien : liste des habitudes du jour
- Récapitulatif hebdomadaire : statistiques et appels à l'action
- Alerte de streak : urgence et motivation

**Queue & Scheduling** :
- Jobs enqueued via Laravel Queue (database driver par défaut)
- Workers lancés via supervisor ou cronjob
- Scheduler déclenche les jobs aux horaires configurés

---

## 5. Mécanismes de Sécurité

### 5.1 Authentification

- Laravel Breeze avec sessions HTTP
- Mots de passe bcrypt hachés avec salt aléatoire
- CSRF protection sur tous les formulaires
- Session timeout configurable

### 5.2 Autorisation

- Authorization Policies Laravel pour chaque resource
- Vérification que l'utilisateur authentifié possède bien l'habitude/complétion à modifier
- Impossible d'accéder aux données d'un autre utilisateur

### 5.3 Protection des Données Sensibles

- Tokens Google OAuth stockés encrypted via Laravel `encrypt()` function
- Variables d'environnement pour API keys et secrets (jamais en dur)
- Pas d'exposition de IDs internes dans les URLs (utiliser des UUIDs si besoin)

### 5.4 Injection SQL

- Requêtes via Eloquent ORM (parameterized queries)
- Pas de requêtes brutes SQL sauf si absolument nécessaire
- Sanitization automatique des inputs

---

## 6. Gestion des Performances

### 6.1 Requêtes Optimisées

- Eager loading via `with()` pour éviter N+1 queries
- Pagination des historiques (max 30 entrées par page)
- Indexes sur colonnes de filtrage fréquent (user_id, completed_date)

### 6.2 Cache

- Cache des statistiques lourdes (taux global, meilleur streak)
- Invalidation du cache lors de modifications d'habits ou complétions
- TTL configurable (ex: 1 heure)

### 6.3 Tâches Asynchrones

- Envoi d'emails non-bloquant via Queue
- Synchronisation Google Calendar en background
- Calcul des statistiques lourdes via jobs si nécessaire

### 6.4 Temps de Réponse Cible

- Vue de base (tableau de bord, liste) : < 1s
- Opérations de marquage/modification : < 500ms
- Intégrations externes (Google Calendar) : timeout configurable, retry automatique

---

## 7. Points de Monitoring et Logs

### 7.1 Logging

- Logs applicatifs via `Log::info()`, `Log::error()` dans laravel.log
- Niveau configurable (debug, info, warning, error)
- Logs des synchronisations Google Calendar (succès et erreurs)
- Logs des envois d'email (succès et bounces)

### 7.2 Monitoring Recommandé

- Taux d'erreur des synchronisations Google
- Temps d'exécution des jobs de notification
- Nombre de workers actifs pour la queue
- Taux de rejet d'email (bounces)
- Utilisation de la base de données (connexions, queries lentes)

### 7.3 Alerts

- Alerte si > 5% des jobs de queue en erreur dans l'heure
- Alerte si token Google expire sans refresh automatique
- Alerte si délai d'envoi d'email > 5 minutes

---

## 8. Plan de Déploiement

### 8.1 Environnement de Production

Récommandé via **Laravel Cloud** ou VPS classique :

- Serveur : Linux (Ubuntu 22.04 LTS)
- Web Server : Nginx
- PHP : 8.2
- Database : MySQL 8.0
- Queue Worker : Supervisor pour maintien de workers persistants
- Cache : Redis (optionnel, améliore performances)
- Email : SendGrid ou Amazon SES (ou Gmail pour petite échelle)
- CDN : Cloudflare pour assets statiques (optionnel)

### 8.2 Variables d'Environnement Essentielles

- `APP_KEY` : clé d'application pour encryption
- `DB_*` : paramètres de connexion MySQL
- `MAIL_DRIVER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`
- `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`
- `QUEUE_CONNECTION` : database ou redis
- `LOG_LEVEL` : debug/info/warning/error

### 8.3 Migrations et Seeders

- Exécuter `php artisan migrate` pour créer les tables
- Exécuter `php artisan db:seed` pour insérer les catégories de base

---

## 9. Considérations de Scalabilité Future

### 9.1 Multi-Tenancy

Si l'application s'étend à de multiples organisations, considérer l'implémentation d'une architecture multi-tenant avec colonnes de isolation ou databases séparées par tenant.

### 9.2 Intégrations Supplémentaires

- Outlook Calendar, Apple Calendar, iCal
- Intégrations fitness (Apple HealthKit, Google Fit) pour sync des données
- Notifications SMS ou push mobile
- Partage social d'habitudes

### 9.3 Optimisations Avancées

- Mise en cache au niveau de la base (query caching)
- Elasticsearch pour recherche full-text d'habitudes
- Graphql API pour clients mobiles
- Offline-first mobile app avec sync local

---

## 10. Conclusion

Le Technical Design Document de HabitFlow v2.0 établit une architecture robuste et extensible pour un système de suivi d'habitudes intégré. L'approche modulaire avec services dédiés (Google Calendar, Notifications) permet une maintenance facile et une évolution future sans refactorisations majeures. La couche de base de données relationnelle avec indexes et contraintes assure la performance et l'intégrité des données. Les mécanismes de sécurité (authentification, autorisation, encryption) protègent les données utilisateur sensibles. Enfin, les tâches asynchrones et le caching garantissent une expérience utilisateur fluide même sous charge élevée.