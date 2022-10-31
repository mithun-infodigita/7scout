<?php

use App\Models\Import\Import;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGlobalSearchToFacetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('facets', function (Blueprint $table) {
            $table->boolean('global_facet')->default(0);
            $table->integer('order_column')->nullable();
            $table->text('item_sort')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('facets', function (Blueprint $table) {
            $table->dropColumn('global_facet');
            $table->dropColumn('order_column');
            $table->dropColumn('item_sort');
        });
    }
}
