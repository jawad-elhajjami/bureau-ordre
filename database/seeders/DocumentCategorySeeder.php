<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['category_name' => 'Correspondance'],
            ['category_name' => 'Rapport'],
            ['category_name' => 'Décision'],
            ['category_name' => 'Note de service'],
            ['category_name' => 'Procès-verbal'],
            ['category_name' => 'Circulaire'],
            ['category_name' => 'Notification'],
            ['category_name' => 'Requête'],
            ['category_name' => 'Invitation'],
            ['category_name' => 'Facture'],
            ['category_name' => 'Reçu'],
            ['category_name' => 'Contrat'],
            ['category_name' => 'Accord'],
            ['category_name' => 'Documentation technique'],
            ['category_name' => 'Rapport financier'],
            ['category_name' => 'Planification'],
            ['category_name' => 'Évaluation'],
            ['category_name' => 'Formation'],
            ['category_name' => 'Projet'],
            ['category_name' => 'Autre']
        ];

        foreach ($categories as $category) {
            DocumentCategory::create($category);
        }
    }
}
