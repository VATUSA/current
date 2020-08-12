<?php

use App\Classes\Helper;
use App\Role;
use App\TrainingRecord;
use App\User;
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
        $ins = [];
        $users = User::where('facility', 'ZSE')->where('rating', '>=', Helper::ratingIntFromShort("I1"))
            ->where('rating', '<=', Helper::ratingIntFromShort("I3"))->get();
        if ($users) {
            foreach ($users as $user) {
                $ins[] = $user->cid;
            }
        }
        $users = Role::where('facility', 'ZSE')->where('role', 'INS')->get();
        if ($users) {
            foreach ($users as $user) {
                $ins[] = $user->cid;
            }
        }
        $users = Role::where('facility', 'ZSE')->where('role', 'MTR')->get();
        if ($users) {
            foreach ($users as $user) {
                $ins[] = $user->cid;
            }
        }
        foreach ($ins as $cid) {
            for ($i = 0; $i < rand(2, 15); $i++) {
                User::find($cid)->trainingRecordsIns()->save(factory(TrainingRecord::class)->make());
            }
        }
    }
}
