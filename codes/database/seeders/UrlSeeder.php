<?php

namespace Database\Seeders;

use App\Models\Domain;
use App\Models\Url;
use Illuminate\Database\Seeder;

class UrlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure domains are created first
        $domains = Domain::all();

        foreach ($domains as $domain) {
            Url::factory()->count(50)->create([
                'domain_id' => $domain->id,
            ]);
        }
    }
}
