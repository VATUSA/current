<?php

namespace App\Console\Commands;

use App\Classes\Helper;
use App\Classes\RoleHelper;
use App\Classes\VATUSAMoodle;
use App\Facility;
use App\Role;
use App\User;
use Illuminate\Console\Command;

class MoodleSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'moodle:sync 
                            {user? : CID of a single user to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Moodle Roles and Cohorts';

    /** @var \App\Classes\VATUSAMoodle instance */
    private $moodle;

    /**
     * Create a new command instance.
     *
     * @param \App\Classes\VATUSAMoodle $moodle
     */
    public function __construct(VATUSAMoodle $moodle)
    {
        parent::__construct();
        $this->moodle = $moodle;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->argument('user')) {
            $user = User::find($this->argument('user'));
            if (!$user) {
                $this->error("Invalid CID");
                exit;
            }

            $this->sync($user);
            exit;
        }

        //Syncronize Users
        $users = User::all();
        foreach ($users as $user) {
            $this->sync($user);
        }
    }

    /**
     * Synchronize Roles
     *
     * @param \App\User $user
     */
    private function sync(User $user)
    {
        //Update or Create
        if ($id = $this->moodle->getUserId($user->cid)) {
            //Update Information
            $this->moodle->updateUser($user, $id);
        } else {
            //Create User
            $id = $this->moodle->createUser($user)[0]["id"];
        }

        //Assign Cohorts
        $this->moodle->clearUserCohorts($id);
        $this->moodle->assignCohort($id, Helper::ratingShortFromInt($user->rating));
        $this->moodle->assignCohort($id, $user->facility);

        //Clear Roles
        $this->moodle->clearUserRoles($id);

        //Assign Student Role
        $this->moodle->assignRole($id, VATUSAMoodle::CATEGORY_VATUSA, "STU", "coursecat");
        $this->moodle->assignRole($id, $this->moodle->getCategoryFromShort($user->facility), "STU", "coursecat");

        //Assign Category Permissions
        if (RoleHelper::hasRole($user->cid, $user->facility, "TA")) {
            $this->moodle->assignRole($id, $this->moodle->getCategoryFromShort($user->facility), "TA", "coursecat");
            $this->moodle->assignRole($id, VATUSAMoodle::CATEGORY_VATUSA, "INS", "coursecat");
        }
        if ($user->flag_homecontroller && (
                $user->rating >= Helper::ratingIntFromShort("I1")
                && $user->rating < Helper::ratingIntFromShort("SUP")
                || $user->rating == Helper::ratingIntFromShort("ADM")
                || RoleHelper::hasRole($user->cid, $user->facility, "INS"))) {
            $this->moodle->assignRole($id, VATUSAMoodle::CATEGORY_VATUSA, "INS", "coursecat");
            $this->moodle->assignRole($id, $this->moodle->getCategoryFromShort($user->facility), "INS",
                "coursecat");
        }
        if (RoleHelper::hasRole($user->cid, $user->facility, "MTR")) {
            for ($i = 1; $i <= $user->rating; $i++) {
                $category = "CATEGORY_" . Helper::ratingShortFromInt($i);
                $this->moodle->assignRole($id, VATUSAMoodle::$$category ?? null, "MTR", "course");
            }
        }

        //TODO: Enrol User in Academy and ARTCC



    }
}
