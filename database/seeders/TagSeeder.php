<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = [
            "implementations",
            "welcome",
            "introduction",
            "codebaseShow",
            "ipsum",
            "qui",
            "cupiditate",
            "et",
            "quia",
            "deserunt"
        ];

        foreach ($tags as $tag) {
            DB::table('tags')->insert([
                'name' => $tag,
            ]);
        }
    }
}
