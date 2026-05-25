<x-mail::message>
# Votre bilan de la semaine 📊

Félicitations **{{ $userName }}**, vous avez terminé une autre semaine avec **{{ config('app.name') }}**. Voici vos statistiques globales :

<x-mail::panel>
### Statistiques Clés
* **Habitudes complétées :** {{ $stats['completed_count'] }}
* **Taux de réussite :** {{ $stats['success_rate'] }}%
* **Meilleur Streak de la semaine :** {{ $stats['best_streak'] }} jours
</x-mail::panel>

## 💡 Insight de la semaine
{{ $stats['insight_message'] }}

<x-mail::button :url="url('/habits')">
Voir mes statistiques détaillées
</x-mail::button>

Prêt pour une nouvelle semaine encore plus productive ?

À bientôt,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
