<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(database_path('banner.json'));
        $animes = json_decode($json, true);

        $appUrl = config('app.url') . '/storage/banner/';

        foreach ($animes as &$anime) {
            $anime['image'] = $appUrl . $anime['image'];
        }

        DB::table('banners')->insert($animes);
    }
}
