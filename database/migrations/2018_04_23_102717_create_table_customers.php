<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('reg_id')->nullable();
            $table->enum('network',['internal','external']);
            $table->string('country')->default('IR');
            $table->string('name')->nullable();
            $table->integer('device_count')->default('1');
            $table->string('image')->nullable();
            $table->enum('active',['0','1'])->default("1");
            $table->text('description')->nullable();
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
        Schema::dropIfExists('customers');
    }
}
