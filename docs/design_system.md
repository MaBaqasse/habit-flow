# Design System : "Clarity & Flow"

Ce système de design privilégie la **lisibilité**, l’**espacement généreux** et une **hiérarchie visuelle douce** pour réduire la charge cognitive dans la gestion des tâches quotidiennes.

---

## 1. Palette de Couleurs
Le système utilise une base blanche épurée avec des accents de couleurs vives pour différencier les catégories d'actions.

* **Couleur Primaire (Action) :** `#4A90E2` (Bleu ciel vibrant) – Utilisé pour les boutons d'action principaux (FAB), les liens et les sélections.
* **Couleurs de Statut/Catégorie :**
    * **Habitudes/Santé :** `#FF5E5E` (Rouge corail)
    * **Succès/Validation :** `#2ECC71` (Vert émeraude)
    * **Focus/Important :** `#F5A623` (Orange ambre)
* **Neutres :**
    * **Fond :** `#FFFFFF` (Blanc pur)
    * **Texte Principal :** `#2D3436` (Gris anthracite foncé)
    * **Texte Secondaire/Labels :** `#636E72` (Gris moyen)
    * **Bordures/Lignes de séparation :** `#F0F0F0` (Gris très clair)

---

## 2. Typographie
Le design utilise une police **Sans-Serif moderne** (type *Inter* ou *SF Pro*) avec des graisses contrastées.

* **H1 (Titres de section) :** Bold, 24px, `#2D3436`.
* **H2 (Sous-titres/Dates) :** Semibold, 18px, Majuscules, Espacement des lettres légèrement augmenté.
* **Body (Tâches/Descriptions) :** Regular, 16px, `#2D3436`.
* **Caption :** Light/Regular, 13px, `#636E72`.

---

## 3. Composants d'Interface (UI)

### A. Listes de Tâches (Cards)
* **Structure :** Une ligne simple avec une icône de checkbox à gauche.
* **Interactivité :** * *Swipe à gauche* pour révéler des actions rapides (Éditer, Déplacer, Supprimer).
    * *Cocher* déclenche une animation de barré (strikethrough) avec un changement de couleur du texte vers le gris clair.
* **Groupement :** Les tâches sont regroupées par blocs temporels (Aujourd'hui, Demain, Cette semaine, Plus tard).

### B. Le Bouton d'Action Flottant (FAB)
* **Style :** Cercle bleu avec un icône `+` blanc centré.
* **Position :** Toujours situé en bas à droite de l'écran avec une ombre portée légère (`box-shadow: 0 4px 10px rgba(74, 144, 226, 0.3)`).

### C. Suivi des Habitudes (Habit Tracker)
* **Visualisation :** Utilisation de cercles de progression ou de calendriers mensuels minimalistes.
* **Statistiques :** Affichage de "Streaks" (séries) avec des chiffres en gras et des icônes colorées pour renforcer la gamification.

---

## 4. Iconographie et Illustrations
* **Style d'icônes :** Linéaire (Outline), trait fin (1.5pt), coins arrondis.
* **Illustrations :** Style "Flat Design" moderne avec des personnages aux formes fluides et des couleurs vives. Elles sont utilisées pour remplir les espaces vides (Empty States) afin de maintenir l'engagement de l'utilisateur.

---

## 5. Mise en page (Layout) & Espacement
* **Grille :** Utilisation d'une grille à 4 colonnes pour le mobile.
* **Marges :** Marges latérales de `20px`.
* **Border-Radius :** Coins très arrondis pour tous les éléments interactifs (`12px` à `20px`) pour un aspect "soft" et amical.
* **Hiérarchie :** Utilisation massive de l'espace blanc (white space) pour séparer les jours et les catégories sans utiliser de lignes de division lourdes.

---

## 6. Micro-interactions
* **Feedback visuel :** Lors de l'appui sur une tâche, une légère réduction de la taille de l'élément (échelle 0.98) simule une pression physique.
* **Transitions :** Transitions fluides entre les vues (Slide-in pour les détails, Fade-in pour les nouveaux éléments).

---

> **Note pour l'implémentation :** Pour répliquer ce design, assurez-vous que l'interface reste "aérée". Chaque élément doit avoir de la place pour respirer. L'aspect minimaliste ne doit pas sacrifier la clarté : utilisez le gras pour les titres et le gris clair pour les éléments moins importants.
