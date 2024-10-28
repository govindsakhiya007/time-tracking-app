<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Project;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Project::create(['name' => 'Project A', 'department_id' => 1]);
        Project::create(['name' => 'Project B', 'department_id' => 1]);
        Project::create(['name' => 'Project C', 'department_id' => 2]);
    }
}
