<?php

namespace App\Console\Commands;

use App\Classes\VATUSAMoodle;
use App\Models\ExamResults;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class ProcessOBSList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'obslist:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve Basic ATC exam results for listed members';

    private $moodle;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->moodle = new VATUSAMoodle();
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $file = Storage::get('obslist.csv');
        $this->info('Processing OBS List');
        $result = array(
            'data'          => [],
            'numPassed'     => 0,
            'numFailed'     => 0,
            'numNotFound'   => 0,
            'numProcessed'  => 0,
            'numNotTaken'   => 0,
            'numInProgress' => 0
        );
        $export = "";

        foreach (explode("\r\n", $file) as $line) {
            $cid = str_getcsv($line)[0];
            if (!$cid) {
                continue;
            }
            $this->info('Processing CID: ' . $cid);

            $user = User::find($cid);
            if (!$user) {
                $this->info("$cid not in database");
                $result['data'][] = [$cid, 'N/A', 'N/A', 'Not in DB'];
                $result['numNotFound']++;
                continue;
            }
            $result['numProcessed']++;

            $passingResult = ExamResults::where('cid', $cid)->where('exam_id',
                config('exams.BASIC.legacyId'))->where('passed', 1)->first();
            $failingResult = ExamResults::where('cid', $cid)->where('exam_id',
                config('exams.BASIC.legacyId'))->where('passed', 0)->first();
            if ($passingResult) {
                $result['data'][] = [
                    $cid,
                    $user->lname,
                    $user->fname,
                    'Passed',
                    $passingResult->date->format('Y-m-d')
                ];
                $result['numPassed']++;
                $this->info("$cid passed");
                continue;
            }

            $academyAttempts = $this->moodle->getQuizAttempts(config('exams.BASIC.id'), $cid);
            if (!$academyAttempts || !is_array($academyAttempts) || empty($academyAttempts)) {
                if ($failingResult) {
                    $result['data'][] = [
                        $cid,
                        $user->lname,
                        $user->fname,
                        'Failed',
                        $failingResult->date->format('Y-m-d')
                    ];
                    $result['numFailed']++;
                    $this->info("$cid failed");
                } else {
                    $result['data'][] = [$cid, $user->lname, $user->fname, 'Not Taken'];
                    $result['numNotTaken']++;
                    $this->info("$cid not taken");
                }

                continue;
            }
            $numFinishedAttempts = 0;
            $hasPassed = false;
            $lastDate = null;
            foreach ($academyAttempts as $attempt) {
                if ($attempt['state'] === 'finished') {
                    $numFinishedAttempts++;
                    $hasPassed = $attempt['grade'] >= 80;
                    $lastDate = $attempt['timefinish'];
                    if ($hasPassed) {
                        break;
                    }
                }
            }
            if ($numFinishedAttempts !== count($academyAttempts) && !$hasPassed) {
                $result['data'][] = [
                    $cid,
                    $user->lname,
                    $user->fname,
                    'In Progress',
                    Carbon::createFromTimestampUTC($lastDate)->format('Y-m-d')
                ];
                $this->info("$cid in progress");
            } else {
                if ($hasPassed) {
                    $result['data'][] = [
                        $cid,
                        $user->lname,
                        $user->fname,
                        'Passed',
                        Carbon::createFromTimestampUTC($lastDate)->format('Y-m-d')
                    ];
                    $this->info("$cid passed");
                } else {
                    $result['data'][] = [
                        $cid,
                        $user->lname,
                        $user->fname,
                        'Failed',
                        Carbon::createFromTimestampUTC($lastDate)->format('Y-m-d')
                    ];
                    $this->info("$cid failed");
                }
            }
        }

        $export = "Total Members," . $result['numProcessed'] . "\r\n";
        $export .= "Total Non-members," . $result['numNotFound'] . "\r\n";
        $export .= "Total Taken," . ($result['numPassed'] + $result['numFailed']) . "\r\n";
        $export .= "Total Not Taken," . $result['numNotTaken'] . "\r\n";
        $export .= "Total Passed," . $result['numPassed'] . "\r\n";
        $export .= "Total Failed," . $result['numFailed'] . "\r\n";
        $export .= "Total In Progress," . $result['numInProgress'] . "\r\n\r\n";

        foreach ($result['data'] as $data) {
            $export .= implode(',', $data) . "\r\n";
        }

        Storage::put('oblist-processed.csv', $export);

        return 0;
    }
}
