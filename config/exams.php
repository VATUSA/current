<?php
return [
    'BASIC' => [
        'id' => env('EXAMS_BASIC', 0),
        'legacyId' => env('EXAMS_BASIC_LEGACY', 7),
        'numQuestions' => env('EXAMS_BASIC_QUESTIONS', 30),
        'enrollPeriod' => env('EXAMS_BASIC_PERIOD', 30),
        'passingPercent' => env('EXAMS_BASIC_PASSING', 80),
        'enrolId' => env('EXAMS_BASIC_ENROLID', 0),
        'courseId' => env('EXAMS_BASIC_COURSEID', 0),
        'rating' => 2
    ],
    'S2'    => [
        'id' => env('EXAMS_S2', 0),
        'legacyId' => env('EXAMS_S2_LEGACY', 8),
        'numQuestions' => env('EXAMS_S2_QUESTIONS', 30),
        'enrollPeriod' => env('EXAMS_S2_PERIOD', 30),
        'passingPercent' => env('EXAMS_S2_PASSING', 80),
        'enrolId' => env('EXAMS_S2_ENROLID', 0),
        'courseId' => env('EXAMS_S2_COURSEID', 0),
        'rating' => 3
    ],
    'S3'    => [
        'id' => env('EXAMS_S3', 0),
        'legacyId' => env('EXAMS_S3_LEGACY', 9),
        'numQuestions' => env('EXAMS_S3_QUESTIONS', 30),
        'enrollPeriod' => env('EXAMS_S3_PERIOD', 30),
        'passingPercent' => env('EXAMS_S3_PASSING', 80),
        'enrolId' => env('EXAMS_S3_ENROLID', 0),
        'courseId' => env('EXAMS_S3_COURSEID', 0),
        'rating' => 4
    ],
    'C1'   => [
        'id' => env('EXAMS_C1', 0),
        'legacyId' => env('EXAMS_C1_LEGACY', 10),
        'numQuestions' => env('EXAMS_C1_QUESTIONS', 30),
        'enrollPeriod' => env('EXAMS_C1_PERIOD', 30),
        'passingPercent' => env('EXAMS_C1_PASSING', 80),
        'enrolId' => env('EXAMS_C1_ENROLID', 0),
        'courseId' => env('EXAMS_C1_COURSEID', 0),
        'rating' => 5
    ],
];