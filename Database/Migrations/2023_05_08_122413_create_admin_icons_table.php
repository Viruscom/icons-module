<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminIconsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_icons', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('icon_set_id')->default(0);
            $table->string('module');
            $table->string('model');
            $table->integer('model_id');
            $table->integer('main_position');
            $table->integer('position');
            $table->string('filename')->nullable()->default(null);
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('admin_icons');
    }
}
