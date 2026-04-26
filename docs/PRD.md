# HabitFlow — Habit Tracker
## Product Requirements Document (PRD) — Version 2.0

> **★ Version enrichie suite aux recommandations de Prof. Sara Qassimi**
> Cette version intègre les notifications par email et l'intégration Google Calendar.

| Champ | Détail |
|---|---|
| **Projet** | HabitFlow – Habit Tracker |
| **Auteur** | BAQASSE Maroua – IRISI 1 |
| **Module** | Développement Web Back-End |
| **Encadrante** | Prof. Sara Qassimi |
| **Date** | Avril 2026 |
| **Version** | 2.0 — Notifications Email + Calendrier Externe |
| **Stack** | Laravel 11 (PHP 8.2+), MySQL, Blade/Tailwind CSS, Laravel Mail, Google Calendar API |

---

## 1. Résumé Exécutif

HabitFlow est une application web développée avec **Laravel** permettant aux utilisateurs de créer, suivre et analyser leurs habitudes quotidiennes. En plus du tableau de bord visuel, du système de streaks et des statistiques de progression, cette version intègre désormais un **système de notifications par email** pour rappeler les habitudes non complétées, ainsi qu'une **synchronisation avec Google Calendar** pour afficher les habitudes directement dans l'agenda de l'utilisateur.

---

## 2. Objectifs du Projet

| N° | Objectif | Priorité | Version |
|---|---|---|---|
| O1 | Permettre la création et la gestion d'habitudes personnalisées | Haute | v1.0 |
| O2 | Suivre quotidiennement la complétion des habitudes | Haute | v1.0 |
| O3 | Visualiser la progression via des statistiques et graphiques | Haute | v1.0 |
| O4 | Gérer l'authentification et l'isolation des données par utilisateur | Haute | v1.0 |
| **O5** | **★ Envoyer des notifications email de rappel quotidien** | **Haute** | **★ v2.0** |
| **O6** | **★ Synchroniser les habitudes avec Google Calendar** | **Haute** | **★ v2.0** |

---

## 3. Périmètre Fonctionnel

### Inclus dans la version 2.0

- Inscription, connexion et déconnexion (authentification Laravel Breeze)
- Création, modification et suppression d'habitudes (nom, description, fréquence, catégorie, couleur)
- Marquage quotidien des habitudes comme « complétées »
- Calcul et affichage des streaks (jours consécutifs réussis)
- Tableau de bord avec taux de complétion quotidien et hebdomadaire
- Historique des habitudes sur les 30 derniers jours (calendrier visuel)
- Statistiques : taux de réussite global, meilleure série, habitude la plus régulière
- Catégorisation des habitudes (Santé, Sport, Productivité, Bien-être…)
- **★ Notifications email quotidiennes** pour rappeler les habitudes non encore complétées (Laravel Mail + SMTP)
- **★ Choix de l'heure** de réception du rappel par l'utilisateur
- **★ Email récapitulatif hebdomadaire** avec le taux de complétion de la semaine
- **★ Intégration Google Calendar** : synchronisation automatique des habitudes comme événements récurrents
- **★ Bouton « Ajouter à Google Calendar »** sur chaque habitude
- **★ Mise à jour automatique** dans Google Calendar si l'habitude est modifiée ou supprimée

### Hors périmètre

- ✗ Application mobile native
- ✗ Partage social entre utilisateurs
- ✗ Intégration avec d'autres calendriers (Outlook, Apple Calendar) — prévu v3.0
- ✗ Notifications SMS ou push mobile

---

## 4. Fonctionnalités Principales

| ID | Fonctionnalité | Priorité | Version | Description |
|---|---|---|---|---|
| F1 | Authentification | Haute | v1.0 | Inscription avec nom, email, mot de passe. Connexion sécurisée. Profil modifiable. |
| F2 | Gestion des Habitudes | Haute | v1.0 | CRUD complet. Fréquence (quotidien/hebdo). Choix couleur. Activation/désactivation. |
| F3 | Suivi Quotidien | Haute | v1.0 | Page « Aujourd'hui » avec bouton check/uncheck. Barre de complétion en temps réel. |
| F4 | Streaks | Haute | v1.0 | Calcul automatique de la série courante et de la meilleure série. Indicateur visuel. |
| F5 | Tableau de Bord | Haute | v1.0 | Vue d'ensemble : taux hebdomadaire, graphique en barres des 7 derniers jours. |
| F6 | Historique & Calendrier | Haute | v1.0 | Calendrier mensuel coloré (vert = fait, rouge = raté, gris = non applicable). |
| F7 | Statistiques | Moyenne | v1.0 | Taux de réussite global, habitude la plus performante, graphique circulaire. |
| F8 | Paramètres Utilisateur | Basse | v1.0 | Modification du nom, email, mot de passe. Suppression du compte. |
| **F9** | **★ Notifications Email** | **Haute** | **★ v2.0** | Rappel quotidien à l'heure choisie. Récapitulatif hebdomadaire. Alerte streak. Géré via Laravel Mail + Queue. |
| **F10** | **★ Intégration Google Calendar** | **Haute** | **★ v2.0** | Synchronisation OAuth 2.0. Habitudes comme événements récurrents. Mise à jour automatique. |

---

## 5. Détail des Nouvelles Fonctionnalités (v2.0)

### ★ F9 — Notifications Email

Les notifications email sont gérées via **Laravel Mail** combiné au système de **queues Laravel** (jobs planifiés). Chaque utilisateur configure ses préférences dans son profil.

| Type de notification | Déclencheur | Contenu |
|---|---|---|
| Rappel quotidien | Chaque jour à l'heure choisie | Liste des habitudes non encore cochées pour la journée |
| Récapitulatif hebdomadaire | Chaque dimanche soir (paramétrable) | Taux de complétion, meilleur streak, habitude la plus régulière |
| Alerte streak | Quand un streak de 7+ jours risque d'être perdu | Rappel urgent : « Votre série de X jours est en danger ! » |

**Implémentation technique :**
- Laravel Mail : classes `ReminderMail`, `WeeklySummaryMail`
- Laravel Queue (database driver) : jobs planifiés avec Laravel Scheduler (cron)
- Table `notification_settings` : préférences utilisateur (heure, type, activé/désactivé)
- Configuration SMTP dans `.env` (Mailtrap pour les tests, SendGrid/Gmail pour la prod)

---

### ★ F10 — Intégration Google Calendar

L'intégration utilise l'**API Google Calendar v3** via le package **google/apiclient**. L'utilisateur connecte son compte Google une seule fois via **OAuth 2.0**.

| Action utilisateur | Résultat dans Google Calendar |
|---|---|
| Créer une habitude | Création d'un événement récurrent (RRULE selon la fréquence daily/weekly) |
| Cocher une habitude | L'occurrence du jour est marquée comme complétée |
| Modifier une habitude | Mise à jour de l'événement récurrent |
| Supprimer une habitude | Suppression de l'événement et de toutes ses occurrences |
| Déconnecter Google | Révocation du token OAuth, suppression des données de synchronisation |

**Implémentation technique :**
- Package : `google/apiclient` installé via Composer
- Authentification OAuth 2.0 : redirection vers Google, stockage du token dans `google_tokens`
- `GoogleCalendarService` : service Laravel dédié dans `app/Services/`
- Table `calendar_syncs` : correspondance entre `habit_id` et `google_event_id`
- Scope requis : `https://www.googleapis.com/auth/calendar`

---

## 6. Conception Base de Données — MLD

La base de données repose sur **5 tables principales** (inchangées) auxquelles s'ajoutent **2 nouvelles tables** pour la v2.0.

### Table : USER

| Attribut | Type | Contraintes / Notes |
|---|---|---|
| id | INT | PK, AUTO_INCREMENT |
| name | VARCHAR(100) | NOT NULL |
| email | VARCHAR(150) | UNIQUE, NOT NULL |
| password | VARCHAR(255) | NOT NULL (bcrypt) |
| created_at / updated_at | TIMESTAMP | Auto Laravel |

### Table : CATEGORIE

| Attribut | Type | Contraintes / Notes |
|---|---|---|
| id | INT | PK, AUTO_INCREMENT |
| name | VARCHAR(80) | NOT NULL (Santé, Sport...) |
| color | VARCHAR(7) | Code hex (#4F46E5) |

### Table : HABIT

| Attribut | Type | Contraintes / Notes |
|---|---|---|
| id | INT | PK, AUTO_INCREMENT |
| user_id | INT | FK → users(id) ON DELETE CASCADE |
| category_id | INT | FK → categories(id) NULLABLE |
| name | VARCHAR(150) | NOT NULL |
| description | TEXT | NULLABLE |
| frequency | ENUM('daily','weekly') | DEFAULT 'daily' |
| color | VARCHAR(7) | Couleur personnalisée |
| is_active | BOOLEAN | DEFAULT TRUE |
| created_at / updated_at | TIMESTAMP | Auto Laravel |

### Table : HABIT_COMPLETION

| Attribut | Type | Contraintes / Notes |
|---|---|---|
| id | INT | PK, AUTO_INCREMENT |
| habit_id | INT | FK → habits(id) ON DELETE CASCADE |
| user_id | INT | FK → users(id) ON DELETE CASCADE |
| completed_date | DATE | NOT NULL |
| note | TEXT | NULLABLE — commentaire optionnel |
| created_at | TIMESTAMP | Auto Laravel |

### Table : STREAK

| Attribut | Type | Contraintes / Notes |
|---|---|---|
| id | INT | PK, AUTO_INCREMENT |
| habit_id | INT | FK → habits(id) ON DELETE CASCADE |
| current_streak | INT | DEFAULT 0 |
| best_streak | INT | DEFAULT 0 |
| last_completed_date | DATE | NULLABLE |
| updated_at | TIMESTAMP | Auto Laravel |

### ★ Table : NOTIFICATION_SETTINGS (v2.0)

| Attribut | Type | Contraintes / Notes |
|---|---|---|
| id | INT | PK, AUTO_INCREMENT |
| user_id | INT | FK → users(id) ON DELETE CASCADE |
| email_reminder_enabled | BOOLEAN | DEFAULT TRUE |
| reminder_time | TIME | Heure du rappel (ex: 08:00:00) |
| weekly_summary_enabled | BOOLEAN | DEFAULT TRUE |
| weekly_summary_day | ENUM(0..6) | 0=dim, 1=lun… DEFAULT 0 |
| streak_alert_enabled | BOOLEAN | DEFAULT TRUE |
| created_at / updated_at | TIMESTAMP | Auto Laravel |

### ★ Table : CALENDAR_SYNCS (v2.0)

| Attribut | Type | Contraintes / Notes |
|---|---|---|
| id | INT | PK, AUTO_INCREMENT |
| user_id | INT | FK → users(id) ON DELETE CASCADE |
| habit_id | INT | FK → habits(id) ON DELETE CASCADE |
| google_event_id | VARCHAR(255) | ID de l'événement dans Google Calendar |
| google_token | TEXT | Token OAuth 2.0 chiffré (encrypt()) |
| synced_at | TIMESTAMP | Dernière synchronisation |
| created_at / updated_at | TIMESTAMP | Auto Laravel |

### Relations entre tables

```
USER (1) ──── (N) HABIT                      [user_id]
CATEGORIE (1) ──── (N) HABIT                 [category_id]
HABIT (1) ──── (N) HABIT_COMPLETION          [habit_id]
HABIT (1) ──── (1) STREAK                    [habit_id]
USER (1) ──── (N) HABIT_COMPLETION           [user_id]
★ USER (1) ──── (1) NOTIFICATION_SETTINGS   [user_id]
★ USER (1) ──── (N) CALENDAR_SYNCS          [user_id]
★ HABIT (1) ──── (1) CALENDAR_SYNCS         [habit_id]
```

---

## 7. Exigences Non-Fonctionnelles

### Sécurité
- Mots de passe hashés avec bcrypt (Laravel par défaut)
- Protection CSRF sur tous les formulaires
- Requêtes SQL via Eloquent ORM (pas d'injection SQL)
- Autorisation : chaque utilisateur n'accède qu'à ses propres données (Policy Laravel)
- **★ Token Google OAuth 2.0 stocké chiffré dans la base de données (`encrypt()`)**
- **★ Scope Google Calendar limité au minimum nécessaire**

### Performance
- Temps de réponse < 2 secondes pour toutes les pages
- Pagination sur l'historique (max 30 entrées par page)
- Mise en cache des statistiques lourdes (Laravel Cache)
- **★ Envoi des emails en arrière-plan via Laravel Queue (non bloquant)**

### Maintenabilité
- Architecture MVC stricte (Laravel conventions)
- Migrations de base de données versionnées
- Code commenté et structuré (PSR-12)
- Variables d'environnement dans `.env` (pas de credentials en dur)
- **★ `GoogleCalendarService` isolé dans `app/Services/` pour faciliter la maintenance**

### Ergonomie
- Interface responsive (mobile-first avec Tailwind CSS)
- Retours visuels instantanés (succès, erreur, confirmation)
- Navigation intuitive : max 2 clics pour marquer une habitude
- **★ Page de gestion des notifications : activer/désactiver en un clic**
- **★ Bouton « Synchroniser avec Google Calendar » visible sur chaque habitude**

---

## 8. Stack Technique

| Couche | Technologie | Notes |
|---|---|---|
| Back-End | Laravel 11 (PHP 8.2+) | Framework MVC, Eloquent ORM, Blade |
| Base de données | MySQL 8.0 | Via XAMPP ou Laravel Sail |
| Front-End | Blade + Tailwind CSS 3 | Templates serveur + utilitaires CSS |
| Graphiques | Chart.js (CDN) | Histogrammes et graphiques circulaires |
| Auth | Laravel Breeze | Scaffolding d'authentification complet |
| **★ Email** | **Laravel Mail + SMTP** | **Mailable classes, Queue jobs, Laravel Scheduler** |
| **★ Calendrier** | **Google Calendar API v3** | **Package google/apiclient, OAuth 2.0** |
| **★ Queue** | **Laravel Queue (database driver)** | **Jobs asynchrones pour l'envoi d'emails** |
| Dev Tools | VS Code + Artisan CLI | Commandes make:model, make:mail, make:job... |
| Versioning | Git + GitHub | Branches main/develop |

---

## 9. Plan de Développement

| Phase | Intitulé | Durée | Livrables |
|---|---|---|---|
| Phase 1 | Setup & Auth | Jour 1–2 | Installation Laravel, Breeze, migration users, layout de base |
| Phase 2 | Gestion Habitudes | Jour 3–4 | CRUD habits, migrations, Seeders catégories, interface Blade |
| Phase 3 | Suivi Quotidien | Jour 5–6 | Page Today, habit_completions, barre de progression, streaks |
| Phase 4 | Dashboard & Stats | Jour 7–8 | Dashboard, Chart.js, statistiques, calendrier mensuel |
| **★ Phase 5** | **Notifications Email** | **Jour 9–10** | **Laravel Mail, Mailable classes, Queue jobs, Scheduler, page préférences** |
| **★ Phase 6** | **Google Calendar** | **Jour 11–12** | **OAuth 2.0, google/apiclient, GoogleCalendarService, CALENDAR_SYNCS** |
| Phase 7 | Finitions & Tests | Jour 13–14 | Responsive, tests manuels, démo complète |

---

> **★ PRD v2.0** — Enrichi suite aux recommandations de **Prof. Sara Qassimi**.
> Les éléments marqués **★ v2.0** correspondent aux deux nouvelles fonctionnalités :
> **F9 — Notifications Email** et **F10 — Intégration Google Calendar**.
> Le MLD conserve les 5 tables originales et en ajoute 2 : **NOTIFICATION_SETTINGS** et **CALENDAR_SYNCS**.