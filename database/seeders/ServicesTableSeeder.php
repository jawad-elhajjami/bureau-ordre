<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Service::create(['name' => 'Ressources Humaines']);
        Service::create(['name' => 'Administration']);
        Service::create(['name' => 'Comptabilité']);
        Service::create(['name' => 'Administration']);
        Service::create(['name' => 'Services Techniques']);
        Service::create(['name' => 'Bibliothèque']);
        Service::create(['name' => 'Informatique et Réseaux']);
        Service::create(['name' => 'Services Généraux']);
        Service::create(['name' => 'Affaires Estudiantines']);
        Service::create(['name' => 'Planification et Développement']);
        Service::create(['name' => 'Communication']);
        Service::create(['name' => 'Sécurité']);
        Service::create(['name' => 'Maintenance']);
        Service::create(['name' => 'Service Pédagogique']);
        Service::create(['name' => 'Service Médical']);
        Service::create(['name' => 'Logistique']);
    }
}
