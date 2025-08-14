<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class BasePlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $basePlans = [
            [
                "id"            => "1",
                "name"          => "Car Wash",

            ],
            [
                "id"            => "2",
                "name"          => "Deep Clean",

            ],
            [
                "id"            => "3",
                "name"          => "Shine and Coat",

            ],
            [
                "id"            => "4",
                "name"          => "Luxury Car Care",
                "status"        => "1"
            ],
            [
                "id"            => "5",
                "name"          => "Special Car Care",

            ],
            [
                "id"            => "6",
                "name"          => "Bike and Moped",
            ],





        ];

        DB::table('base_plans')->insert($basePlans);
    }
}
