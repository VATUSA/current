<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePoliciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('policies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ident');
            $table->unsignedSmallInteger('category');
            $table->text('title');
            $table->text('slug');
            $table->date('effective_date');
            $table->unsignedInteger('perms'); //Bitmap: All | WM | EC | FE | TA | DATM | ATM | USASTAFF
            $table->unsignedSmallInteger('order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('policies');
    }
}
