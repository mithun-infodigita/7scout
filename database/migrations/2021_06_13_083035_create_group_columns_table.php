<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_columns', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('column_id');
            $table->bigInteger('group_id');
            $table->string('name')->unique();
            $table->string('de')->nullable();
            $table->string('en')->nullable();
            $table->string('fr')->nullable();
            $table->string('it')->nullable();
            $table->boolean('show_in_table')->default(0);
            $table->boolean('detail_filter')->default(0);
            $table->integer('order_column')->nullable();
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
        Schema::dropIfExists('group_columns');
    }
}
