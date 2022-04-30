<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Middleware\TrimStrings;
use App\Models\Client;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{

    /**
     * @description Crear billetera electronica.
     * @param Request $request
     * @return JsonResponse
     */
    public function createWallet(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            //se crea nuevo registro
            $wallet = new Wallet();

            $wallet->saldo = 0;
            $wallet->card_number = $request->input("card_number");
            $wallet->client_id = $request->input("client_id");

            $wallet->save();

            DB::commit();

            return response()->json([
                "success"   => true,
                "cod_error" => "00",
                "message_error"   => "Se creo la billetera virtual con el numero ". $request->input("card_number") . ", exitosamente."
            ]);
        }catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "success"   => false,
                "cod_error" => $e->getCode(),
                "message_error"   => $e->getMessage()
            ]);
        }

    }

    /**
     * @description Recargar billetera electronica y generar el registro de la transaccion.
     * @param Request $request
     * @return JsonResponse
     */
    public function rechargeWallet(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            //consulta el cliente con los parametros recibidos
            $client = Client::withoutTrashed()
                ->where("document_number", $request->input("document_number"))
                ->where("cel", $request->input("cel"))
                ->first();

            //consulta la billetera del cliente
            $wallet = Wallet::where("client_id", $client->id)->first();

            //actualiza el nuevo saldo
            $wallet->saldo = bcadd($request->input("value"), $wallet->saldo, 2);

            //actualiza los datos
            $wallet->update();

            //generamos el registro de la transaccion
            $transaction = new Transaction();

            $transaction->value = $request->input("value");
            $transaction->state = "Aprobada";
            $transaction->type  = "Recarga de tarjeta";
            $transaction->session_id = $client->id;
            $transaction->wallet_id = $wallet->id;

            $transaction->save();

            DB::commit();

            return response()->json([
                "success"   => true,
                "cod_error" => "00",
                "message_error"   => "Se recargo exitosamente la billetera virtual numero ". $wallet->card_number
            ]);

        }catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "success"   => false,
                "cod_error" => $e->getCode(),
                "message_error"   => $e->getMessage()
            ]);
        }
    }

    /**
     * @description Consultar saldo en la billetera
     * @param Request $request
     * @return JsonResponse
     */
    public function checkBalance(Request $request): JsonResponse
    {
        $balance = Client::withoutTrashed()
            ->join("wallet as w", "client.id", "=", "w.client_id")
            ->where("document_number", $request->input("document_number"))
            ->where("cel", $request->input("cel"))
            ->first();

        return response()->json([
            "success"   => true,
            "cod_error" => "00",
            "message_error"   => "Su saldo es de " . $balance->saldo
        ]);
    }

}
