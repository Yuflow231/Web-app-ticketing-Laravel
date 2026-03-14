<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Ticket;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Supprimer tous les fichiers uploadés précédemment
        $foldersToClean = ['contracts', 'attachments'];
        foreach ($foldersToClean as $folder) {
            if (Storage::disk('public')->exists($folder)) {
                $files = Storage::disk('public')->files($folder);
                Storage::disk('public')->delete($files);
                $this->command->info("Dossier '{$folder}' cleared (" . count($files) . " file(s) deleted).");
            }
        }

        // Créer un administrateur
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'System',
            'email' => 'admin@example.com',
            'password_hashed' => Hash::make('password'),
            'role' => 'Administrator',
            'join_date' => now(),
            'language' => 'fr',
        ]);

        // Créer des utilisateurs de test
        $users = [];
        $users[] = User::create([
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'jean.dupont@example.com',
            'password_hashed' => Hash::make('password'),
            'role' => 'Guest',
            'join_date' => now(),
            'language' => 'fr',
        ]);

        $users[] = User::create([
            'first_name' => 'Marie',
            'last_name' => 'Martin',
            'email' => 'marie.martin@example.com',
            'password_hashed' => Hash::make('password'),
            'role' => 'Guest',
            'join_date' => now(),
            'language' => 'fr',
        ]);

        $users[] = User::create([
            'first_name' => 'Pierre',
            'last_name' => 'Bernard',
            'email' => 'pierre.bernard@example.com',
            'password_hashed' => Hash::make('password'),
            'role' => 'Guest',
            'join_date' => now(),
            'language' => 'fr',
        ]);

        // Créer des projets
        $project1 = Project::create([
            'name' => 'Site Web E-commerce',
            'status' => 'In Progress',
            'progress_percent' => 45,
            'creation_date' => now()->subDays(30),
            'description' => 'Développement d\'un site e-commerce avec Laravel',
            'estimated_time' => 120.00,
            'spent_time' => 54.00,
        ]);

        $project2 = Project::create([
            'name' => 'Application Mobile',
            'status' => 'New',
            'progress_percent' => 10,
            'creation_date' => now()->subDays(10),
            'description' => 'Application mobile de gestion de tâches',
            'estimated_time' => 80.00,
            'spent_time' => 8.00,
        ]);

        $project3 = Project::create([
            'name' => 'Refonte CRM',
            'status' => 'On Hold',
            'progress_percent' => 60,
            'creation_date' => now()->subDays(60),
            'description' => 'Refonte complète du système CRM',
            'estimated_time' => 200.00,
            'spent_time' => 120.00,
        ]);

        // Attacher les utilisateurs aux projets
        $project1->teamMembers()->attach($admin->id, ['role' => 'Owner']);
        $project1->teamMembers()->attach($users[0]->id, ['role' => 'Maintainer']);
        $project1->teamMembers()->attach($users[1]->id, ['role' => 'Maintainer']);

        $project2->teamMembers()->attach($admin->id, ['role' => 'Owner']);
        $project2->teamMembers()->attach($users[2]->id, ['role' => 'Maintainer']);

        $project3->teamMembers()->attach($users[0]->id, ['role' => 'Owner']);
        $project3->teamMembers()->attach($users[1]->id, ['role' => 'Maintainer']);

        // Créer des tickets pour le projet 1
        $ticket1 = Ticket::create([
            'name' => 'Mise en place de l\'authentification',
            'project_id' => $project1->id,
            'status' => 'Completed',
            'priority' => 'High',
            'type' => 'Billed',
            'description' => 'Implémenter le système d\'authentification utilisateur',
            'estimated_time' => 8.00,
            'spent_time' => 10.00,
        ]);

        $ticket2 = Ticket::create([
            'name' => 'Design de la page d\'accueil',
            'project_id' => $project1->id,
            'status' => 'In Progress',
            'priority' => 'Medium',
            'type' => 'Billed',
            'description' => 'Créer le design responsive de la page d\'accueil',
            'estimated_time' => 12.00,
            'spent_time' => 6.00,
        ]);

        $ticket3 = Ticket::create([
            'name' => 'Intégration du paiement',
            'project_id' => $project1->id,
            'status' => 'New',
            'priority' => 'High',
            'type' => 'Billed',
            'description' => 'Intégrer Stripe pour les paiements',
            'estimated_time' => 16.00,
            'spent_time' => 0.00,
        ]);

        // Créer des tickets pour le projet 2
        $ticket4 = Ticket::create([
            'name' => 'Maquette UI/UX',
            'project_id' => $project2->id,
            'status' => 'In Progress',
            'priority' => 'High',
            'type' => 'Included',
            'description' => 'Créer les maquettes de l\'application',
            'estimated_time' => 20.00,
            'spent_time' => 8.00,
        ]);

        // Attacher les travailleurs aux tickets
        $ticket1->workers()->attach($users[0]->id, ['role' => 'Ticket Creator']);
        $ticket1->workers()->attach($users[1]->id, ['role' => 'Helper']);

        $ticket2->workers()->attach($users[1]->id, ['role' => 'Ticket Creator']);

        $ticket3->workers()->attach($admin->id, ['role' => 'Ticket Creator']);
        $ticket3->workers()->attach($users[0]->id, ['role' => 'Helper']);

        $ticket4->workers()->attach($users[2]->id, ['role' => 'Ticket Creator']);

        $this->command->info('Database populated successfully.!');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('Users: jean.dupont@example.com / password');
        $this->command->info('       marie.martin@example.com / password');
        $this->command->info('       pierre.bernard@example.com / password');
    }
}
