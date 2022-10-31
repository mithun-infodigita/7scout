<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalNamesToGroupColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_columns', function (Blueprint $table) {
            $table->renameColumn('de', 'de_table');
            $table->renameColumn('en', 'en_table');
            $table->renameColumn('fr', 'fr_table');
            $table->renameColumn('it', 'it_table');

            $table->string('de_detail')->nullable();
            $table->string('en_detail')->nullable();
            $table->string('fr_detail')->nullable();
            $table->string('it_detail')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_columns', function (Blueprint $table) {
            $table->dropColumn('de_detail');
            $table->dropColumn('en_detail');
            $table->dropColumn('fr_detail');
            $table->dropColumn('it_detail');
        });
    }
}
