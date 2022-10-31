<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultValuesToColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('columns', function (Blueprint $table) {
            $table->string('default_de_name')->nullable();
            $table->string('default_en_name')->nullable();
            $table->string('default_fr_name')->nullable();
            $table->string('default_it_name')->nullable();
            $table->boolean('default_show_in_frontend')->default(0);
            $table->boolean('default_show_in_table')->default(0);
            $table->boolean('default_show_in_table_detail')->default(0);
            $table->boolean('default_show_in_detail_page')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('columns', function (Blueprint $table) {
            $table->dropColumn('default_de_name');
            $table->dropColumn('default_en_name');
            $table->dropColumn('default_fr_name');
            $table->dropColumn('default_it_name');
            $table->dropColumn('default_show_in_frontend');
            $table->dropColumn('default_show_in_table');
            $table->dropColumn('default_show_in_table_detail');
            $table->dropColumn('default_show_in_detail_page');
        });
    }
}
