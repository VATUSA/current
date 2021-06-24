<?php namespace App\Console\Commands;

use App\Classes\ExamHelper;
use App\Exam;
use App\ExamReassignment;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ULSTokens extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ULSTokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ULSTokens';

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
        \DB::raw("DELETE FROM `uls_tokens` WHERE `date` <= DATE_SUB(NOW(), INTERVAL 30 SECOND)");

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
