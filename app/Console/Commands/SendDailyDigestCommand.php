<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Mail\DailyDigestMail;
use Illuminate\Support\Facades\Mail;

class SendDailyDigestCommand extends Command
{
    /**
     * La signature définit comment appeler la commande dans le terminal.
     * C'est le namespace "email:send-daily-digest" que Laravel cherchait.
     *
     * @var string
     */
    protected $signature = 'email:send-daily-digest {--user=}';

    /**
     * La description de la commande qui s'affiche avec php artisan list.
     *
     * @var string
     */
    protected $description = 'Envoie manuellement le Daily Digest à un utilisateur spécifique pour test';

    /**
     * Exécute la commande de console.
     */
    public function handle()
    {
        // 1. Récupérer l'ID passé en option (--user=11)
        $userId = $this->option('user');

        if (!$userId) {
            $this->error('Erreur : Veuillez spécifier un ID utilisateur. Exemple: --user=11');
            return Command::FAILURE;
        }

        // 2. Chercher l'utilisateur en base de données
        $user = User::find($userId);

        if (!$user) {
            $this->error("Erreur : L'utilisateur avec l'ID {$userId} n'existe pas en base de données.");
            return Command::FAILURE;
        }

        // 3. Récupérer ses habitudes actives
        $habits = $user->habits()->where('is_active', true)->get();

        $this->info("🔄 Préparation de l'envoi pour l'utilisateur : {$user->email}...");
        
        // 4. Envoi synchrone via ->send() au lieu de ->queue() pour le test immédiat
        try {
            Mail::to($user->email)->send(new DailyDigestMail($user, $habits));
            $this->info("✅ Email envoyé avec succès ! Vérifie ton tableau de bord Mailtrap.");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("❌ Échec de l'envoi de l'email : " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
