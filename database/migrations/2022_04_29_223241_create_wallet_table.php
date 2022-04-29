<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet', function (Blueprint $table) {
            $table->bigIncrements("id")->comment("id registro");
            $table->bigInteger("saldo")->comment("Saldo tarjeta");
            $table->string("card_number", 30)->comment("Numero tarjeta");
            $table->unsignedBigInteger("client_id")->comment("Id del cliente propietario de la tarjeta");
            $table->softDeletes();
            $table->timestamps();
            /*Foreign*/
            $table->foreign('client_id')
                ->references('id')->on('client')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("SET FOREIGN_KEY_CHECKS=0");
        Schema::dropIfExists('wallet');
        DB::statement("SET FOREIGN_KEY_CHECKS=1");
    }
}
