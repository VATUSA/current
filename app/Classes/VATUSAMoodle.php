<?php
/**
 * Interact with Moodle
 * @author Blake Nahin <b.nahin@vatusa.net>
 */

namespace App\Classes;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use MoodleRest;
use ReflectionClass;

class VATUSAMoodle extends MoodleRest
{
    /** @var int[] Role Mappings */
    protected $roleIds = [
        'TA'     => 1,
        'INS'    => 4,
        'STU'    => 5,
        'MTR'    => 9,
        'CBT'    => 10,
        'FACCBT' => 11
    ];

    /** @var int Category Contexts */
    public const CATEGORY_CONTEXT_VATUSA = 43;
    public const CATEGORY_CONTEXT_OBS = 44;
    public const CATEGORY_CONTEXT_S1 = 45;
    public const CATEGORY_CONTEXT_S2 = 115;
    public const CATEGORY_CONTEXT_S3 = 47;
    public const CATEGORY_CONTEXT_C1 = 48;

    /** @var int Category IDs */
    public const CATEGORY_ID_VATUSA = 2;
    public const CATEGORY_ID_OBS = 3;
    public const CATEGORY_ID_S1 = 4;
    public const CATEGORY_ID_S2 = 72;
    public const CATEGORY_ID_S3 = 6;
    public const CATEGORY_ID_C1 = 7;

    /** @var int Context Levels */
    public const CONTEXT_SYSTEM = 10;
    public const CONTEXT_USER = 30;
    public const CONTEXT_COURSECAT = 40;
    public const CONTEXT_COURSE = 50;
    public const CONTEXT_MODULE = 70;
    public const CONTEXT_BLOCK = 80;

    /**
     * VATUSAMoodle constructor.
     *
     * @param bool $isSSO
     *
     * @throws \Exception
     */
    public function __construct(bool $isSSO = false)
    {
        if (in_array(app()->environment(), ["livedev", "staging", "prod", "dev"])) {
            parent::__construct(config('services.moodle.url') . '/webservice/rest/server.php',
                $isSSO ? config('services.moodle.token_sso') : config('services.moodle.token'));
        }
    }

    /**
     * Get all Cohorts
     * @return mixed
     * @throws \Exception
     */
    public function getCohorts()
    {
        return $this->request("core_cohort_get_cohorts");
    }

    /**
     * Get members of all Cohorts.
     * @return array|mixed
     * @throws \Exception
     */
    public function getCohortMembers(): array
    {
        $members = [];
        foreach ($this->getCohorts() as $cohort) {
            $id = $cohort["id"];
            $members[] = $this->request("core_cohort_get_cohort_members", ["cohortids" => [0 => $id]])[0];
        }

        return $members;
    }

    /**
     * Get an array of all categories.
     * @return mixed
     * @throws \Exception
     */
    public function getCategories()
    {
        return $this->request("core_course_get_categories");
    }

    /**
     * Get single category
     *
     * @param int $id Category ID
     *
     * @return mixed
     * @throws \Exception
     */
    public function getCategory(int $id): array
    {
        return $this->request("core_course_get_categories",
            ["criteria" => [0 => ["key" => "id", "value" => $id]]]);
    }

    /**
     * Get Category Context or ID
     *
     * @param string|null $short   IDNumber
     * @param bool        $context Short is Context
     * @param bool        $full    Return full array
     *
     * @return mixed|null
     * @throws \Exception
     */
    public function getCategoryFromShort(?string $short, bool $context = false, bool $full = false)
    {
        if (is_null($short)) {
            return null;
        }

        foreach ($this->getCategories() as $category) {
            if ($category["idnumber"] === $short) {
                if ($full) {
                    return $context ? array_merge($category,
                        ["context" => $this->getContext($category["id"], "coursecat")]) : $category;
                }

                return $context ? $this->getContext($category["id"], "coursecat") : $category['id'];
            }
        }

        return null;
    }

    /**
     * Get All Subcategories of Parent
     *
     * @param int|null $parent        ID or Context
     * @param bool     $includeParent Include parent in return
     * @param bool     $context       Parent is Context
     * @param bool     $full          Return full array
     *
     * @return array
     * @throws \Exception
     */
    public function getAllSubcategories(
        ?int $parent,
        bool $includeParent = false,
        bool $context = false,
        bool $full = false
    ): array {
        if (is_null($parent)) {
            return [];
        }

        $categories = $this->request("core_course_get_categories",
            ["criteria" => [0 => ["key" => "parent", "value" => $parent]]]);
        if ($includeParent) {
            return $full ? array_merge($this->getCategory($parent), $categories) : array_merge([$parent],
                collect($categories)->pluck($context ? "context" : "id")->toArray());
        } else {
            return $full ? $categories :
                collect($categories)->pluck($context ? "context" : "id")->toArray();
        }
    }

    /**
     * Create top-level category
     *
     * @param string $id
     * @param string $name
     *
     * @return mixed
     * @throws \Exception
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
     * Delete category.
     *
     * @param int $id
     *
     * @return mixed
     * @throws \Exception
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
     * @throws \Exception
     */
    public function getUser(string $cid)
    {
        return $this->request("core_user_get_users", ['criteria' => [0 => ['key' => 'idnumber', 'value' => $cid]]]);
    }

    /**
     * Check if user exists in Moodle database.
     *
     * @param int $cid
     *
     * @return bool|int
     * @throws \Exception
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
     * Create user.
     *
     * @param \App\Models\User $user
     *
     * @return false|mixed
     * @throws \Exception
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
     * @param \App\Models\User $user
     * @param int              $id
     *
     * @return bool|null
     * @throws \Exception
     */
    public function updateUser(User $user, int $id): ?bool
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
     * Create Cohort
     *
     * @param string $id
     * @param string $name
     * @param string $type    Scope of Cohort
     * @param string $typeval Scope of Cohort - Identifier
     *
     * @return mixed
     * @throws \Exception
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
     * Assign user to Cohort.
     *
     * @param int    $uid     User ID
     * @param string $cnumber Cohort IDNumber
     *
     * @return mixed
     * @throws \Exception
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

    /**
     * Unassign Cohort
     *
     * @param int $uid
     * @param int $cid
     *
     * @return mixed
     * @throws \Exception
     */
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
        DB::connection('moodle')->table('cohort_members')->where('userid', $uid)->delete();
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
     * @throws \Exception
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
     *
     * Remove Role from User in Context
     *
     * @param int      $uid     User ID
     * @param int|null $cid     Context ID
     * @param string   $role    Role String
     * @param string   $context Context Type
     *
     * @return mixed
     * @throws \Exception
     */
    public function unassignRole(int $uid, ?int $cid, string $role, string $context)
    {
        return $this->request("core_role_unassign_roles", [
            "unassignments" => [
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
        return DB::connection('moodle')->table('role_assignments')->where('userid', $uid)->delete();
    }

    /**
     * Get Context ID for an instance
     *
     * @param int    $id   Instance ID
     * @param string $type Instance Type
     *
     * @return mixed
     */
    public function getContext(int $id, string $type)
    {
        $level = "CONTEXT_" . strtoupper($type);

        return DB::connection('moodle')->table('context')->where('instanceid', $id)->where('contextlevel',
            $this->getConstant($level))->pluck('id')->first();
    }

    /**
     * Get Courses
     *
     * @param int|null $catid
     *
     * @return mixed
     * @throws \Exception
     */
    public function getCoursesInCategory(int $catid = null)
    {
        $params = $catid ? ["field" => "category", "value" => $catid] : [];

        return $this->request("core_course_get_courses_by_field", $params)["courses"];
    }

    public function getAcademyCategoryIds()
    {
        return $this->getAllSubcategories(self::CATEGORY_ID_VATUSA, true);
    }

    public function getConstants()
    {
        return (new ReflectionClass(self::class))->getConstants();
    }

    /**
     * Get specific class constant
     *
     * @param string $constant
     *
     * @return int|null
     */
    public function getConstant(string $constant): ?int
    {
        return $this->getConstants()[$constant] ?? null;
    }

    public function getAcademyCategoryContexts()
    {
        return array_filter((new ReflectionClass(self::class))->getConstants(), function ($key) {
            return str_contains($key, "CATEGORY_CONTEXT");
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Enrol User in Course
     *
     * @param int      $uid User ID
     * @param int      $cid Course ID
     * @param int|null $rid Role ID
     *
     * @return mixed
     * @throws \Exception
     */
    public function enrolUser(int $uid, int $cid, ?int $rid = null)
    {
        if (is_null($rid)) {
            $rid = $this->roleIds['STU'];
        }

        return $this->request("enrol_manual_enrol_users",
            ["enrolments" => [0 => ["roleid" => $rid, "userid" => $uid, "courseid" => $cid]]]);
    }

    /**
     * Get quiz attempts
     *
     * @param int      $quizid The Quiz ID
     * @param int|null $cid    The user's CID
     * @param int|null $uid
     *
     * @return array
     */
    public function getQuizAttempts(int $quizid, ?int $cid, ?int $uid = null, $includeGrades = true): array
    {
        try {
            $userid = $uid ?? $this->getUserId($cid);
            if (!$userid) {
                return [];
            }

            $attempts = $this->request("mod_quiz_get_user_attempts", ["quizid" => $quizid, "userid" => $userid])['attempts'] ?? [];

            for ($i = 0; $i < count($attempts); $i++) {
                $review = $this->request("mod_quiz_get_attempt_review",
                        ["attemptid" => $attempts[$i]['id']]) ?? [];
                if (!empty($review)) {
                    $attempts[$i]['grade'] = round(floatval($review['grade']));
                }
            }

            return $attempts;
        } catch (Exception $e) {
            return [];
        }
    }

    public function getUserEnrolmentInfo(?int $uid, int $enrolmentId)
    {
        return DB::connection('moodle')->table('user_enrolments')
            ->where('userid', $uid)
            ->where('enrolid', $enrolmentId)
            ->first();
    }

    public function getUserEnrolmentTimestamp(?int $uid, int $enrolmentId)
    {
        $info = $this->getUserEnrolmentInfo($uid, $enrolmentId);

        return $info->timecreated ?? false;

    }


}