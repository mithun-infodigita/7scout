<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToBooleanOnGroupColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('group_columns', function (Blueprint $table) {
            $table->boolean('show_in_table')->nullable()->change();
            $table->boolean('show_in_table_detail')->nullable()->change();
            $table->boolean('show_in_detail_page')->nullable()->change();
            // $table->integer('order_column')->nullable();
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
            $table->dropColumn('default_de_name');
        });
    }
}
