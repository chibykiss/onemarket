<?php

namespace Database\Seeders;

use App\Models\Shop;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ShopTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Shop::factory(10)->create();
        $faker = Faker::create();
        foreach (Shop::all() as $shop) {
            $shop->plaza_name = $faker->company();
            $shop->save();
        }
    }
}
