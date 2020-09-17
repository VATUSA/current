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
        $facilities = \App\Facility::active()->get();
        foreach($facilities->pluck('id') as $fac) {
            $ins = [];
            $users = User::where('facility', $fac)->where('rating', '>=', Helper::ratingIntFromShort("I1"))
                ->where('rating', '<=', Helper::ratingIntFromShort("I3"))->get();
            if ($users) {
                foreach ($users as $user) {
                    $ins[] = $user->cid;
                }
            }
            $users = Role::where('facility', $fac)->where('role', 'INS')->get();
            if ($users) {
                foreach ($users as $user) {
                    $ins[] = $user->cid;
                }
            }
            $users = Role::where('facility', $fac)->where('role', 'MTR')->get();
            if ($users) {
                foreach ($users as $user) {
                    $ins[] = $user->cid;
                }
            }
            foreach ($ins as $cid) {
                for ($i = 0; $i < rand(2, 4); $i++) {
                    User::find($cid)->trainingRecordsIns()->save(factory(TrainingRecord::class)->make(['facility_id'=> $fac]));
                }
            }
        }
    }
}
