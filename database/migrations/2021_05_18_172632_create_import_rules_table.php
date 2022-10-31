<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('import_id');
            $table->bigInteger('source_reference_column_id')->nullable();
            $table->string('reference_compare_type')->nullable();
            $table->string('map_reference_script')->nullable();
            $table->bigInteger('map_file_id')->nullable();
            $table->bigInteger('map_column_id')->nullable();
            $table->string('map_value_script')->nullable();
            $table->string('order_column');
            $table->string('status');
            $table->softDeletes();
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
        Schema::dropIfExists('import_rules');
    }
}
