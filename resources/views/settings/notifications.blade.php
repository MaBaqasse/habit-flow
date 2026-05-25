<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Paramètres de notification</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('settings.notifications.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="email_reminder_enabled" value="1" class="form-checkbox" {{ $settings->email_reminder_enabled ? 'checked' : '' }}>
                            <span class="ml-2">Activer le rappel quotidien par e-mail</span>
                        </label>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Heure du rappel quotidien</label>
                        <input type="time" name="reminder_time" value="{{ \Carbon\Carbon::parse($settings->reminder_time)->format('H:i') }}" class="mt-1 block w-48">
                    </div>

                    <div class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="weekly_summary_enabled" value="1" class="form-checkbox" {{ $settings->weekly_summary_enabled ? 'checked' : '' }}>
                            <span class="ml-2">Activer le bilan hebdomadaire</span>
                        </label>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Jour du bilan hebdomadaire</label>
                        <select name="weekly_summary_day" class="mt-1 block w-48">
                            @foreach([0=>'Dimanche',1=>'Lundi',2=>'Mardi',3=>'Mercredi',4=>'Jeudi',5=>'Vendredi',6=>'Samedi'] as $k => $label)
                                <option value="{{ $k }}" {{ $settings->weekly_summary_day == $k ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Heure du bilan hebdomadaire</label>
                        <input type="time" 
                            name="weekly_summary_time" 
                            id="weekly_summary_time" 
                            class="form-control" 
                            value="{{ old('weekly_summary_time', \Carbon\Carbon::parse($settings->weekly_summary_time)->format('H:i')) }}">
                    </div>

                    <div class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="streak_alert_enabled" value="1" class="form-checkbox" {{ $settings->streak_alert_enabled ? 'checked' : '' }}>
                            <span class="ml-2">Activer les alertes de streak</span>
                        </label>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700">Format du digest e-mail</label>
                        <select name="email_digest_format" class="mt-1 block w-48">
                            <option value="summary" {{ $settings->email_digest_format === 'summary' ? 'selected' : '' }}>Résumé</option>
                            <option value="detailed" {{ $settings->email_digest_format === 'detailed' ? 'selected' : '' }}>Détaillé</option>
                            <option value="minimal" {{ $settings->email_digest_format === 'minimal' ? 'selected' : '' }}>Minimal</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded">Enregistrer</button>
                        <a href="{{ route('dashboard') }}" class="text-sm text-gray-600">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
