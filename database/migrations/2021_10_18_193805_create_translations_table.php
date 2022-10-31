<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->longText('de')->nullable();
            $table->longText('en')->nullable();
            $table->longText('fr')->nullable();
            $table->longText('it')->nullable();
            $table->string('translation_type_de')->nullable();
            $table->string('translation_type_en')->nullable();
            $table->string('translation_type_fr')->nullable();
            $table->string('translation_type_it')->nullable();
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
        Schema::dropIfExists('translations');
    }
}
