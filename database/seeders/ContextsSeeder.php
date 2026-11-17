<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContextsSeeder extends Seeder
{
    /**
     * Seed the public content of every landing page.
     *
     * The copy lives in config/trifecta-content.php so the seeder and the
     * runtime fallback (Context::defaults()) can never drift apart. Running
     * this again updates every key in place.
     */
    public function run(): void
    {
        $contexts = config('trifecta-content', []);

        foreach ($contexts as $key => $details) {
            DB::table('contexts')->updateOrInsert(
                ['context_key' => $key],
                [
                    'details' => $details,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
