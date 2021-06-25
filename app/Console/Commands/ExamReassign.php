<?php namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Actions;
use App\Classes\ExamHelper;
use App\Models\Exam;
use App\Models\ExamAssignment;
use App\Models\ExamReassignment;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ExamReassign extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ExamReassign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reassign exams';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (ExamReassignment::whereDate('reassign_date', '<=', Carbon::now())->count() > 0) {
            foreach (ExamReassignment::whereDate('reassign_date', "<=", Carbon::now())->get() as $ex) {
                $exam = Exam::find($ex->exam_id);
                if ($exam) {
                    ExamHelper::assign($ex->cid, $ex->exam_id, $ex->instructor_id, $exam->retake_period, true);
                }
                $ex->delete();
            }
        }
        if (ExamAssignment::whereDate('expire_date', '>=', Carbon::now())->count() > 0) {
            foreach (ExamAssignment::whereDate('expire_date', '<=', Carbon::now())->get() as $ex) {
                if ($ex->exam()) {
                    $log = new Actions();
                    $log->to = $ex->cid;
                    $log->log = "Exam (" . $ex->exam()->facility_id . ") " . $ex->exam()->name . " has expired and been unassigned.";
                    $log->save();
                }
                $ex->delete();
            }
        }
        \DB::raw("DELETE FROM `solo_certs` WHERE `expires` < NOW()");

        return 0;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
        ];
    }

}
