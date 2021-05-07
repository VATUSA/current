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
            $table->unsignedInteger('category');
            $table->text('title');
            $table->text('slug');
            $table->date('effective_date')->nullable();
            $table->string('perms'); //Bitmap: All | WM | EC | FE | TA | DATM | ATM | USASTAFF
            $table->boolean('visible');
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
