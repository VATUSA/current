<?php

namespace App\Console\Commands;

use App\Classes\Helper;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CachePromotionEligibility extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promos:cacheeligible';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache promotion eligibility.';

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
     * @return void
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function handle()
    {
        foreach (Facility::active()->get() as $fac) {
            $count = 0;
            foreach ($fac->members as $member) {
                if ($member->rating >= Helper::ratingIntFromShort("C1")) {
                    continue;
                }
                if ($member->promotionEligible()) {
                    $count++;
                }
            }
            Cache::set("promotionEligible-$fac->id", $count);
            sleep(3);
        }
    }
}
