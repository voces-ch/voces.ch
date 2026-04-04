<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SignatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Signature::factory(100)->create();
    }
}
