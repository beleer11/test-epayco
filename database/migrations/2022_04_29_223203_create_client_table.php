<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client', function (Blueprint $table) {
            $table->bigIncrements("id")->comment("id registro");
            $table->string("name", 30)->comment("Nombre cliente");
            $table->string("document_number", 20)->comment("Numero documento cliente")->unique();
            $table->string("age", 10)->comment("Edad cliente");
            $table->string("cel", 20)->comment("Telefono celular cliente");
            $table->string("email", 30)->comment("Email cliente");
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
        Schema::dropIfExists('client');
    }
}
