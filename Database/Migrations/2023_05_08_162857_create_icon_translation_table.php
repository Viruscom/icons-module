<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIconTranslationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('icon_translation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('icon_id');
            $table->string('locale')->index();
            $table->string('title');
            $table->text('short_description')->nullable()->default(null);
            $table->boolean('external_url')->default(false);
            $table->text('url')->nullable()->default(null);
            $table->timestamps();

            $table->unique(['icon_id', 'locale']);
            $table->foreign('icon_id')->references('id')->on('icons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('icon_translation');
    }
}
