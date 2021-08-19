<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubcoordinatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subcoordinators', function (Blueprint $table) {
            $table->increments('subcoordinator_id');
			$table->string('city_municipality');
			$table->string('coordinator');
			$table->string('barangay');
			$table->string('sub_coordinator');
			$table->string('vin_number');
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
        Schema::dropIfExists('subcoordinators');
    }
}
