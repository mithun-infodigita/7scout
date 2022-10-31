<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailsToGroupColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_columns', function (Blueprint $table) {
            // $table->integer('show_in_table')->default(0);
            $table->integer('left_side_filter')->default(0);
            // $table->integer('detail_filter')->default(0);
            $table->integer('show_in_table_detail')->default(0);
            $table->integer('show_in_detail_page')->default(0);
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
            // $table->dropColumn('show_in_table');
            $table->dropColumn('left_side_filter');
            // $table->dropColumn('detail_filter');
            $table->dropColumn('show_in_table_detail');
            $table->dropColumn('show_in_detail_page');
        });
    }
}
