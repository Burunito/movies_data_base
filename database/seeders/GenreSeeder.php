<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = [
            'Public',
            'School',
            'Japan',
            'Asian',
            'Stop time',
            'Blonde',
            'Redhair',
            'Latina',
            'Ass',
            'Sam alike',
            'Ale alike',
            'Latina',
            'Boobs',
            'Webcam',
            'Taxi',
            'Masturbate',
            'Group',
            'Train',
            'Glory hole',
            'Favorites',
            'Beach',
            'Dildo',
            'Casting',
            'Casual',
            'Amateur',
            'Sex mex',
            'Fat',
            'Slim',
            'Perfect',
            'Skinny',
        ];

        $now = Carbon::now();

        $data = array_map(function ($title) use ($now) {
            return [
                'title' => $title,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $genres);

        DB::table('genres')->insert($data);
    }
}
