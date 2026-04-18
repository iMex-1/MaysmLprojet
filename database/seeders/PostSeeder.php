<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::all();
        
        if ($users->isEmpty()) {
            $this->command->error('Aucun utilisateur trouvé. Veuillez d\'abord créer des utilisateurs.');
            return;
        }

        // Créer des tags
        $tags = [
            \App\Models\Tag::create(['name' => 'Laravel']),
            \App\Models\Tag::create(['name' => 'PHP']),
            \App\Models\Tag::create(['name' => 'Web Development']),
            \App\Models\Tag::create(['name' => 'Tutorial']),
        ];

        // Créer des posts pour chaque utilisateur
        foreach ($users as $user) {
            for ($i = 1; $i <= 3; $i++) {
                $post = \App\Models\Post::create([
                    'user_id' => $user->id,
                    'title' => "Post $i de {$user->name}",
                    'content' => "Ceci est le contenu du post $i créé par {$user->name}. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
                ]);

                // Attacher des tags aléatoires
                $post->tags()->attach($tags[array_rand($tags->toArray())]->id);
                $post->tags()->attach($tags[array_rand($tags->toArray())]->id);

                // Créer des commentaires
                foreach ($users as $commenter) {
                    if (rand(0, 1)) {
                        \App\Models\Comment::create([
                            'post_id' => $post->id,
                            'user_id' => $commenter->id,
                            'content' => "Commentaire de {$commenter->name} sur ce post.",
                        ]);
                    }
                }
            }
        }

        $this->command->info('Posts, tags et commentaires créés avec succès!');
    }
}
