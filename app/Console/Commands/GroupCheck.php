<?php namespace App\Console\Commands;

use App\Classes\SMFHelper;
use App\Models\User;
use Illuminate\Console\Command;

class GroupCheck extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'GroupCheck';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Check forum groups.';

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
        \DB::connection('forum')->table("smf_members")->update(['id_group' => 10, 'additional_groups' => '']);
        $users = User::where('facility', 'NOT LIKE', 'ZZN')->get();
        foreach ($users as $user) {
            SMFHelper::setPermissions($user->cid);
        }

        return 0;
    }
}
