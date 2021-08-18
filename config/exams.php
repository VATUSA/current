<?php
return [
    'BASIC' => [
        'id' => env('EXAMS_BASIC', 0),
        'legacyId' => env('EXAMS_BASIC_LEGACY', 7),
        'passingPercent' => env('EXAMS_BASIC_PASSING', 80),
        'enrolId' => env('EXAMS_BASIC_ENROLID', 0),
        'rating' => 2
    ],
    'S2'    => [
        'id' => env('EXAMS_S2', 0),
        'legacyId' => env('EXAMS_S2_LEGACY', 8),
        'passingPercent' => env('EXAMS_S2_PASSING', 80),
        'enrolId' => env('EXAMS_S2_ENROLID', 0),
        'courseId' => env('EXAMS_S2_COURSEID', 0),
        'rating' => 3
    ],
    'S3'    => [
        'id' => env('EXAMS_S3', 0),
        'legacyId' => env('EXAMS_S3_LEGACY', 9),
        'passingPercent' => env('EXAMS_S3_PASSING', 80),
        'enrolId' => env('EXAMS_S3_ENROLID', 0),
        'courseId' => env('EXAMS_S3_COURSEID', 0),
        'rating' => 4
    ],
    'C1'   => [
        'id' => env('EXAMS_C1', 0),
        'legacyId' => env('EXAMS_C1_LEGACY', 10),
        'passingPercent' => env('EXAMS_C1_PASSING', 80),
        'enrolId' => env('EXAMS_C1_ENROLID', 0),
        'courseId' => env('EXAMS_C1_COURSEID', 0),
        'rating' => 5
    ],
];