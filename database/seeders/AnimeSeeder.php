<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\File;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(database_path('animes.json'));
        $animes = json_decode($json, true);

        $appUrl = config('app.url') . '/storage/anime/';

        foreach ($animes as &$anime) {
            $anime['image'] = $appUrl . $anime['image'];
        }

        DB::table('animes')->insert($animes);
    }
}
