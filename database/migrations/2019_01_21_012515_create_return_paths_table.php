<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReturnPathsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('return_paths', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order');
            $table->string('facility_id');
            $table->string('url');
            $table->timestamps();
        });

        /** Import Existing  */
        $facilities = \App\Models\Facility::where('active', true)->get();
        foreach ($facilities as $facility) {
            if ($facility->uls_return) {
                $facility->returnPaths()->create([
                    'order' => 1,
                    'url'   => $facility->uls_return
                ]);
            }
            if ($facility->uls_devreturn) {
                $facility->returnPaths()->create([
                    'order' => 2,
                    'url'   => $facility->uls_devreturn
                ]);
            }
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('return_paths');
    }
}
