<?php
/**
 * Interact with Moodle
 * @author Blake Nahin <b.nahin@vatusa.net>
 */

namespace App\Classes;

use App\User;
use Illuminate\Support\Facades\DB;
use MoodleRest;

class VATUSAMoodle extends MoodleRest
{

    /**
     * List of Cohorts
     * @var array|mixed
     */
    protected $cohorts = [];

    /**
     * List of members for each Cohort (ID)
     * @var array|mixed
     */
    protected $cohortMembers = [];

    /**
     * List of all Categories
     * @var array|mixed
     */
    protected $categories = [];

    protected $roleIds = [
        'TA'  => 1,
        'INS' => 4,
        'STU' => 5,
        'MTR' => 9
    ];

    public const CATEGORY_VATUSA = 43;
    public const CATEGORY_OBS = 44;
    public const CATEGORY_S1 = 45;
    public const CATEGORY_S2 = 115;
    public const CATEGORY_S3 = 47;
    public const CATEGORY_C1 = 48;

    public const CONTEXT_SYSTEM = 10;
    public const CONTEXT_USER = 30;
    public const CONTEXT_COURSECAT = 40;
    public const CONTEXT_COURSE = 50;
    public const CONTEXT_MODULE = 70;
    public const CONTEXT_BLOCK = 80;

    /**
     * VATUSAMoodle constructor.
     */
    public function __construct()
    {
        parent::__construct(config('services.moodle.url') . '/webservice/rest/server.php',
            config('services.moodle.token'));

        $this->cohorts = $this->getCohorts();
        $this->cohortMembers = $this->getCohortMembers();
        $this->categories = $this->getCategories();
    }

    /**
     * Get all Cohorts
     * @return mixed
     */
    public function getCohorts()
    {
        return $this->request("core_cohort_get_cohorts");
    }

    /**
     * Get members of all Cohorts.
     * @return array|mixed
     */
    public function getCohortMembers(): array
    {
        $members = [];
        foreach ($this->cohorts as $cohort) {
            $id = $cohort["id"];
            $members[] = $this->request("core_cohort_get_cohort_members", ["cohortids" => [0 => $id]])[0];
        }

        return $members;
    }

    /**
     * Create top-level category
     *
     * @param string $id
     * @param string $name
     *
     * @return mixed
     */
    public function createCategory(string $id, string $name)
    {
        return $this->request("core_course_create_categories", [
            'categories' => [
                0 => [
                    'name'     => $name,
                    'idnumber' => $id,
                ]
            ]
        ], self::METHOD_POST);
    }

    /**
     * Get an array of all categories.
     * @return mixed
     */
    public function getCategories()
    {
        return $this->request("core_course_get_categories");
    }

    public function getCategoryFromShort($short)
    {
        foreach ($this->categories as $category) {
            if ($category["idnumber"] === $short) {
                return $this->getContext($category["id"], "coursecat");
            }
        }

        return null;
    }

    /**
     * Delete category.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function deleteCategory(int $id)
    {
        return $this->request("core_course_delete_categories", [
            'categories' => [
                0 => [
                    'id'        => $id,
                    'recursive' => 1
                ]
            ]
        ], self::METHOD_POST);
    }

    /**
     * Get User Information
     *
     * @param string $cid
     *
     * @return mixed
     */
    public function getUser(string $cid)
    {
        return $this->request("core_user_get_users", ['criteria' => [0 => ['key' => 'idnumber', 'value' => $cid]]]);
    }

    /**
     * Create Cohort
     *
     * @param string $id
     * @param string $name
     * @param string $type    Scope of Cohort
     * @param string $typeval Scope of Cohort - Identifier
     *
     * @return mixed
     */
    public function createCohort(string $id, string $name, string $type = 'system', string $typeval = '')
    {
        return $this->request("core_cohort_create_cohorts",
            [
                'cohorts' => [
                    0 => [
                        'categorytype' =>
                            [
                                'type'  => $type,
                                'value' => $typeval,
                            ],
                        'idnumber'     => $id,
                        'name'         => $name
                    ]
                ]
            ]);
    }

    /**
     * Create user.
     *
     * @param \App\User $user
     *
     * @return false|mixed
     */
    public function createUser(User $user)
    {
        if (!$user) {
            return false;
        }

        return $this->request("core_user_create_users", [
            'users' => [
                0 => [
                    'createpassword' => 0,
                    'username'       => $user->cid,
                    'password'       => env('APP_KEY'),
                    'auth'           => 'manual',
                    'firstname'      => $user->fname,
                    'lastname'       => $user->lname,
                    'email'          => $user->email,
                    'maildisplay'    => 0,
                    'idnumber'       => $user->cid,
                    'mailformat'     => 1,
                ]
            ]
        ], self::METHOD_POST);
    }

    /**
     * Update user.
     *
     * @param \App\User $user
     * @param int       $id
     *
     * @return false|mixed
     */
    public function updateUser(User $user, int $id)
    {
        if (!$user) {
            return false;
        }

        return $this->request("core_user_update_users", [
            'users' => [
                0 => [
                    'id'        => $id,
                    'firstname' => $user->fname,
                    'lastname'  => $user->lname,
                    'email'     => $user->email
                ]
            ]
        ], self::METHOD_POST);
    }

    /**
     * Check if user exists in Moodle database.
     *
     * @param int $cid
     *
     * @return bool|int
     */
    public function getUserId(int $cid)
    {
        $user = $this->getUser($cid)["users"][0] ?? [];

        if (empty($user)) {
            return false;
        }

        return $user["id"];
    }

    /**
     * Assign user to Cohort.
     *
     * @param int    $uid     User ID
     * @param string $cnumber Cohort IDNumber
     *
     * @return mixed
     */
    public function assignCohort(int $uid, string $cnumber)
    {
        return $this->request("core_cohort_add_cohort_members", [
            "members" => [
                0 => [
                    "cohorttype" => [
                        'type'  => 'idnumber',
                        'value' => $cnumber
                    ],
                    "usertype"   => [
                        'type'  => 'id',
                        'value' => $uid
                    ]
                ]
            ]
        ]);
    }

    public function removeCohort(int $uid, int $cid)
    {
        return $this->request("core_cohort_delete_cohort_members",
            ["members" => [0 => ["cohortid" => $cid, "userid" => $uid]]]);
    }

    /**
     * Remove user from all Cohorts.
     *
     * @param int $uid User ID
     */
    public function clearUserCohorts(int $uid)
    {
        foreach ($this->cohortMembers as $cohortMember) {
            $id = $cohortMember["cohortid"];
            if (in_array($uid, $cohortMember["userids"])) {
                $this->removeCohort($uid, $id);
            }
        }
    }

    /**
     * Assign Role to User in Context
     *
     * @param int      $uid     User ID
     * @param int|null $cid     Context ID
     * @param string   $role    Role String
     * @param string   $context Context Type
     *
     * @return mixed
     */
    public function assignRole(int $uid, ?int $cid, string $role, string $context)
    {
        return $this->request("core_role_assign_roles", [
            "assignments" => [
                0 => [
                    "roleid"       => $this->roleIds[$role],
                    "userid"       => $uid,
                    "contextid"    => $cid,
                    "contextlevel" => $context
                ]
            ]
        ]);
    }

    /**
     * Clear User's roles
     *
     * @param int $uid User ID
     *
     * @return int
     */
    public function clearUserRoles(int $uid): int
    {
        //There is no way to do this other than through the database directly. Ugh.
        return DB::connection('moodle')->table('role_assignments')->where('userid', $uid)->delete();
    }

    /**
     * Get Context ID for an instance
     * @param int    $id Instance ID
     * @param string $type Instance Type
     *
     * @return mixed
     */
    public function getContext(int $id, string $type)
    {
        $level = "CONTEXT_" . strtoupper($type);

        return DB::connection('moodle')->table('context')->where('instanceid', $id)->where('contextlevel',
            self::$$level)->pluck('id')->first();
    }


}