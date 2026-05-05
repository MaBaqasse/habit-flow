# Logique de Design : Dashboard

## 1. Objectif de la Page
Le Dashboard évolue pour devenir un centre d'analyse de la performance globale de l'utilisateur. Il permet de piloter l'activité quotidienne tout en mettant en avant les records de régularité (Top Streaks) et en offrant un accès rapide aux statistiques détaillées par habitude.

## 2. Structure Visuelle & Composants (Wireframe Logic)

### A. Barre de Résumé des Stats Globales (KPIs)
Située en haut de page, elle affiche les compteurs consolidés de l'utilisateur :
*   **Taux de complétion du jour (%)** : Pourcentage global d'habitudes cochées aujourd'hui par rapport au total des habitudes actives. Affiche également le nombre exact de complétions (ex: "3/5 habitudes").
*   **Habitudes Actuelles** : Nombre total d'habitudes correspondant au filtre de catégorie séléctionné.
*   **Catégorie Active** : Rappel textuel de la catégorie séléctionnée pour le filtrage (ou "Toutes").

### B. Top 3 Streaks
Un nouveau panneau affichant les 3 habitudes possédant la `current_streak` la plus élevée, récupérée depuis la table **STREAK**. Cela encourage la compétition avec soi-même.

### C. Composants Interactifs & Filtrage
*   **Filtre par Catégorie** : Menu déroulant (dropdown) alimenté par la table **CATEGORIE** pour filtrer dynamiquement l'affichage de la liste.
*   **Cartes d'Habitudes Personnalisées** :
    *   **Suppression des Streaks Individuels** : Les blocs "Série actuelle" et "Meilleure série" sont retirés de la vue principale pour alléger l'interface.
    *   **Bouton Check-in Coloré** : Le bouton adopte dynamiquement la couleur définie par l'utilisateur pour l'habitude (`$habit->color`) au lieu du violet standard.
    *   **Bouton "Voir Statistiques"** : Remplace le bouton "Modifier". Il redirige vers la page de détails ou de statistiques de l'habitude spécifique.

## 3. Logique de Données (Backend-to-Frontend)

| Élément | Source de Données (MLD) | Logique de Calcul |
| :--- | :--- | :--- |
| **Taux Global** | **HABIT_COMPLETION** | `(Complétées aujourd'hui / Total actives) * 100`. |
| **Top 3 Streaks** | **STREAK** | `Habit::with('streak')->orderByDesc('current_streak')->take(3)`. |
| **Filtre** | **CATEGORIE** | Filtrage de la collection par `category_id`. |
| **Bouton Check-in** | **HABIT** | Application de `$habit->color` dans le style CSS du bouton. |

## 4. Expérience Utilisateur (UX) & Ergonomie
*   **Cohérence Visuelle** : L'utilisation de la couleur de l'habitude pour le bouton Check-in renforce le lien cognitif entre l'action et l'objectif.
*   **Focus sur la Data** : Le remplacement du bouton "Modifier" par "Voir Statistique" oriente l'utilisateur vers l'analyse de ses progrès plutôt que vers la gestion administrative.
*   **Responsive Design** : Maintien de l'approche mobile-first avec Tailwind CSS.