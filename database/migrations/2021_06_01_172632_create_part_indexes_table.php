<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartIndexesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_indexes', function (Blueprint $table) {
            $table->id();
            $table->text('status')->nullable();
            $table->string('name');
            $table->text('table_name')->nullable();
            $table->text('model_name')->nullable();
            $table->timestamp('last_upload')->nullable();
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
        Schema::dropIfExists('part_indexes');
    }
}
