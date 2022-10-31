<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('producer_id');
            $table->bigInteger('previous_import_id')->nullable();
            $table->string('type');
            $table->string('part_import_file')->nullable();
            $table->string('language');
            $table->string('status');
            $table->text('notification')->nullable();
            $table->longText('one_to_one')->nullable();
            $table->longText('group_mapping')->nullable();
            $table->longText('category_mapping')->nullable();
            $table->longText('price_mapping')->nullable();
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
        Schema::dropIfExists('imports');
    }
}
