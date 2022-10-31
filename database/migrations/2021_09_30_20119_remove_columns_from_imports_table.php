<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveColumnsFromImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('imports', function (Blueprint $table) {
            $table->dropColumn('previous_import_id');
            $table->dropColumn('type');
            $table->dropColumn('part_import_file');
            $table->dropColumn('group_mapping');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('imports', function (Blueprint $table) {
            $table->bigInteger('previous_import_id')->nullable();
            $table->string('type');
            $table->string('part_import_file')->nullable();
            $table->longText('group_mapping')->nullable();
        });
    }
}
