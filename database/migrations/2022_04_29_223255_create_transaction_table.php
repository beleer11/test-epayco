<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->double("value")->comment("Valor de la transaccion");
            $table->string("state", 20)->comment("Aprobado, Pendiente, Rechazado");
            $table->string("type", 20)->comment("Compra, Retiro efectivo, Recarga de tarjeta");
            $table->unsignedBigInteger("wallet_id")->comment("Id de la tarjeta propietario de la tarjeta");
            $table->bigInteger("session_id")->comment("Id de la sesion del cliente");
            $table->string("token", 6)->comment("Token para verificar el pago, solo para pagos")->nullable();
            $table->softDeletes();
            $table->timestamps();
            /*Foreign*/
            $table->foreign('wallet_id')
                ->references('id')->on('wallet')
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
        Schema::dropIfExists('transaction');
    }
}
