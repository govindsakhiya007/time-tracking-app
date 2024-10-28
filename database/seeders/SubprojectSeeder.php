<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Subproject;

class SubprojectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Subproject::create(['name' => 'Subproject 1', 'project_id' => 1]);
        Subproject::create(['name' => 'Subproject 2', 'project_id' => 1]);
        Subproject::create(['name' => 'Subproject 3', 'project_id' => 2]);
    }
}
