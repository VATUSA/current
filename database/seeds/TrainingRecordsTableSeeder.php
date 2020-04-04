<?php

use App\TrainingRecord;
use Illuminate\Database\Seeder;

class TrainingRecordsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TrainingRecord::truncate();
        factory(TrainingRecord::class, 35)->create();
    }
}
