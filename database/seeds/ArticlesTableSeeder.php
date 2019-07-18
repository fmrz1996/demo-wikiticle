<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ArticlesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('articles')->insert([
          'title' => 'Chéreng',
          'body' => 'Chéreng es una población y comuna francesa, en la región de Norte-Paso de Calais, departamento de Norte, en el distrito de Lille y cantón de Lannoy',
          'image' => 'https://upload.wikimedia.org/wikipedia/commons/e/e6/Chereng-01.jpg',
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }
}
