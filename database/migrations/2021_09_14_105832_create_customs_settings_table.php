<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomsSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customs_settings', function (Blueprint $table) {
            $table->id();
            $table->string('customs_tariff_number_eu');
            $table->string('customs_tariff_number_ch');
            $table->string('import_fees_with_preferential_origin_of_goods_eu');
            $table->string('import_fees_with_preferential_origin_of_goods_ch');
            $table->string('import_fees_without_preferential_origin_of_goods_eu');
            $table->string('import_fees_without_preferential_origin_of_goods_ch');
            $table->string('tax_unit_eu');
            $table->string('tax_unit_ch');
            $table->string('tax_by_value_eu');
            $table->string('tax_by_value_ch');
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
        Schema::dropIfExists('customs_settings');
    }
}
