<?php

namespace App\Console\Commands;

use App\Classes\Helper;
use App\Classes\RoleHelper;
use App\Classes\VATUSAMoodle;
use App\Models\Facility;
use App\Models\Role;
use App\Models\User;
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

                return 0;
            }

            $this->sync($user);

            return 0;
        }

        //Syncronize Users
        $users = User::all();
        foreach ($users as $user) {
            if ($this->moodle->getUserId($user->cid)) {
                $this->sync($user);
            }
        }

        return 0;
    }

    /**
     * Synchronize Roles
     *
     * @param \App\Models\User $user
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
        $this->moodle->assignRole($id, VATUSAMoodle::CATEGORY_CONTEXT_VATUSA, "STU", "coursecat");
        $this->moodle->assignRole($id, $this->moodle->getCategoryFromShort($user->facility, true), "STU", "coursecat");

        //Assign Category Permissions
        if (RoleHelper::isVATUSAStaff() || RoleHelper::hasRole($user->cid, $user->facility, "TA", true)) {
            $this->moodle->assignRole($id, VATUSAMoodle::CATEGORY_CONTEXT_VATUSA, "INS", "coursecat");
        }
        if (RoleHelper::isVATUSAStaff() || RoleHelper::isFacilitySeniorStaff($user->cid, $user->facility, true)) {
            $this->moodle->assignRole($id, $this->moodle->getCategoryFromShort($user->facility, true), "TA",
                "coursecat");
        }
        if (RoleHelper::isVATUSAStaff() || RoleHelper::hasRole($user->cid, "ZAE", "CBT", true)) {
            $this->moodle->assignRole($id, VATUSAMoodle::CATEGORY_CONTEXT_VATUSA, "CBT", "coursecat");
        }
        if ($user->flag_homecontroller && (
                $user->rating >= Helper::ratingIntFromShort("I1")
                && $user->rating < Helper::ratingIntFromShort("SUP")
                || $user->rating == Helper::ratingIntFromShort("ADM")
                || RoleHelper::isVATUSAStaff()
                || RoleHelper::hasRole($user->cid, $user->facility, "INS", true))) {
            $this->moodle->assignRole($id, VATUSAMoodle::CATEGORY_CONTEXT_VATUSA, "INS", "coursecat");
            $this->moodle->assignRole($id, $this->moodle->getCategoryFromShort($user->facility, true), "INS",
                "coursecat");
        }
        if (RoleHelper::isVATUSAStaff() || RoleHelper::hasRole($user->cid, $user->facility, "MTR", true)) {
            for ($i = 1; $i <= $user->rating; $i++) {
                $category = "CATEGORY_CONTEXT_" . Helper::ratingShortFromInt($i);
                $this->moodle->assignRole($id, $this->moodle->getConstant($category), "MTR", "course");
            }
        }

        //Enrol User in Courses within Academy and ARTCC
        $vatusaCategories = $this->moodle->getAcademyCategoryIds();
        $artccCategoryParent = $this->moodle->getCategoryFromShort("ZAB");
        $artccCategories = $this->moodle->getAllSubcategories($artccCategoryParent, true);

        $allCategories = array_merge($vatusaCategories, $artccCategories);
        foreach ($allCategories as $category) {
            $courses = $this->moodle->getCoursesInCategory($category);
            foreach ($courses as $course) {
                $this->moodle->enrolUser($id, $course["id"]);
            }
        }

    }
}
