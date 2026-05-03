# 📊 Logique de Design : Dashboard (HabitFlow v2.0)

---

## 1. Objectif de la Page

Le Dashboard est le **centre de pilotage** de l'utilisateur. Il doit permettre de visualiser en un coup d'œil la progression, la régularité (streaks) et les performances hebdomadaires.

Conformément à l'**objectif O3 du PRD**, il transforme les données brutes en informations visuelles exploitables.

---

## 2. Structure Visuelle & Composants (Wireframe Logic)

### A. Barre de Résumé (KPIs)

Située en haut de page, elle affiche les compteurs clés :

- **Taux de complétion quotidien** : Pourcentage d'habitudes cochées aujourd'hui.
- **Meilleure Série (Best Streak)** : Record historique de l'utilisateur.
- **Habitude la plus performante** : Celle avec le taux de réussite le plus élevé.

---

### B. Graphiques de Progression (Chart.js)

- **Vue Hebdomadaire (Histogramme)** : Affiche le nombre d'habitudes complétées sur les 7 derniers jours pour visualiser la tendance.
- **Répartition par Catégorie (Graphique Circulaire)** : Montre l'équilibre entre Santé, Sport, Productivité, etc.

---

### C. Le Calendrier Visuel (Historique 30 jours)

Un *heatmap* ou calendrier compact où chaque jour est coloré selon le statut :

| Couleur | Signification |
|---|---|
| 🟢 Vert | Habitudes complétées |
| 🔴 Rouge | Objectifs non atteints |
| ⚪ Gris | Jours sans données ou non applicables |

---

## 3. Logique de Données (Backend-to-Frontend)

| Élément | Source de Données (MLD) | Logique de Calcul |
|---|---|---|
| Streaks | Table `STREAK` | Récupération de `current_streak` et `best_streak` |
| Graphique 7 jours | Table `HABIT_COMPLETION` | `COUNT` des entrées groupées par `completed_date` |
| Taux Global | Table `HABIT_COMPLETION` | `(Total complétées / Total possibles) * 100` |
| Statut Google | Table `CALENDAR_SYNCS` | Vérification si l'habitude est synchronisée (indicateur visuel) |

---

## 4. Expérience Utilisateur (UX) & Ergonomie

- **Isolation des données** : Le contrôleur utilise une *Policy* pour garantir que les statistiques affichées ne concernent que l'utilisateur authentifié *(Objectif O4 du PRD)*.
- **Performance** : Mise en cache des statistiques lourdes via **Laravel Cache** pour garantir un temps de réponse < 2 secondes.
- **Responsive Design** : Utilisation de **Tailwind CSS** avec une approche *mobile-first* pour permettre le suivi sur smartphone *(Exigence Ergonomie)*.

---

## 5. Intégrations Spécifiques v2.0

Le Dashboard inclut désormais deux nouveaux raccourcis :

- **★ État de Synchronisation** : Badge visuel à côté de chaque habitude confirmant la liaison avec Google Calendar.
- **★ Rappel Rapide** : Lien vers les paramètres pour ajuster l'heure de la Notification Email quotidienne sans quitter le tableau de bord.