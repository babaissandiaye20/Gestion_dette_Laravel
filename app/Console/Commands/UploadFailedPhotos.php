<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Jobs\UploadUserPhotoToCloudinaryJob;

class UploadFailedPhotos extends Command
{
    /**
     * Le nom et la signature de la commande console.
     *
     * @var string
     */
    protected $signature = 'photos:upload-failed';

    /**
     * La description de la commande console.
     *
     * @var string
     */
    protected $description = 'Upload all failed photos (type = false) to Cloudinary';

    /**
     * Créer une nouvelle instance de la commande.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Exécuter la commande console.
     *
     * @return int
     */
    public function handle()
    {
        // Récupérer tous les utilisateurs avec des photos non uploadées (type = false)
        $users = User::where('type', false)->whereNotNull('photo')->get();

        if ($users->isEmpty()) {
            $this->info('No users with failed photo uploads.');
            return;
        }

        foreach ($users as $user) {
            // Dispatch un job pour chaque utilisateur ayant une photo échouée
            UploadUserPhotoToCloudinaryJob::dispatch($user);
            $this->info("Job dispatched for user ID: " . $user->id);
        }

        $this->info('Jobs dispatched for all users with failed photo uploads.');
    }
}

