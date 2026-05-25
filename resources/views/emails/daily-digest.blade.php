<x-mail::message>
# Bonjour {{ $userName }} ! ☀️

C'est une nouvelle journée pour devenir la meilleure version de vous-même. Voici vos défis pour aujourd'hui :

## 📋 Vos habitudes du jour
@foreach($habits as $habit)
* **{{ $habit->name }}** * 🕒 Heure cible : {{ \Carbon\Carbon::parse($habit->target_time)->format('H:i') }}
  * 🔥 Streak actuel : {{ $habit->streak->current_streak ?? 0 }} jours
@endforeach

<x-mail::button :url="url('/dashboard')" color="success">
Valider mes habitudes
</x-mail::button>

> "La discipline est le pont entre les objectifs et l'accomplissement."

Bonne chance pour votre journée !

Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>