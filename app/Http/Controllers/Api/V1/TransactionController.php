<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\EmailService;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * @description Pago con billetera
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function payment(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            //consulta que no tenga mas pagos pendientes
            $pendingPay = Transaction::withoutTrashed()
                ->join("wallet as w", "transaction.wallet_id", "=", "w.id")
                ->where("w.card_number", $request->input("card_number"))
                ->where("transaction.state", "=", "Pendiente")
                ->exists();

            if($pendingPay){
                DB::rollBack();
                return response()->json([
                    "success"   => false,
                    "cod_error" => "200",
                    "message_error"   => "Señor cliente, usted ya tiene un proceso de pago pendiente. Por favor revise su correo electronico e ingrese el token enviado."
                ]);
            }

            //consulta el saldo de la billetera
            $data = Wallet::withoutTrashed()
                ->join("client as c", "wallet.client_id", "=", "c.id")
                ->select(
                    "wallet.id as id_wallet",
                    "wallet.saldo",
                    "c.id as id_client",
                    "c.email",
                    "c.name"
                )
                ->where("wallet.card_number", $request->input("card_number"))
                ->first();

            if($data->saldo <= 0 || is_null($data)){
                return response()->json([
                    "success"   => false,
                    "cod_error" => "200",
                    "message_error"   => "La billetera no tiene saldo o no existe"
                ]);
            }

            //genera token para enviar al correo
            $token = bin2hex(random_bytes((6 - (6 % 2)) / 2));

            //genera variable de sesion para validar el user
            session(['client_id' => $data->id_client]);

            //enviamos al correo la informacion para confirmar el pago
            $mailSend = new EmailService();
            $bodyHTML = 'Hola, tu token es <b>'. $token .'</b>. Por favor ingresa tu token en la pagina de pagos.';

            //Servicio para enviar el correo
            $mailSend->sendMail(
                "Pagina de pagos Epayco.",
                $data->name, $data->email,
                "Verificación de token",
                $bodyHTML
            );

            //se registra la transacción para estar y queda en estado pendiente. Por ahora no se le descuenta nada al cliente hasta que confirme su pago
            $transaction = new Transaction();

            $transaction->value = $request->input("pay_value");
            $transaction->state = "Pendiente";
            $transaction->type  = "Pagos";
            $transaction->session_id = session("client_id");
            $transaction->token = $token;
            $transaction->wallet_id = $data->id_wallet;

            $transaction->save();
            DB::commit();

            return response()->json([
                "success"   => true,
                "cod_error" => "00",
                "session_id" => session("client_id"),
                "message_error"   => "Se realizo la solicitud del pago exitosamente, para confirmar el pago revise el correo electronico: " . $data->email ." e ingrese el token enviado."
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
     * @description Confirma el pago realizado por el cliente.
     * @param  Request  $request
     * @return void
     */
    public function paymentConfirm(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            //verifica la informacion guardada para continuar con el proceso de pago
            $verifyData = Transaction::withoutTrashed()
                ->join("wallet as w", "transaction.wallet_id", "=", "w.id")
                ->select(
                    "w.id as wallet_id",
                    "transaction.id as transaction_id",
                    "w.saldo as saldo_actual",
                    "transaction.value as saldo_descontar"
                )
                ->where("w.card_number", $request->input("card_number"))
                ->where("transaction.token", $request->input("token"))
                ->where("transaction.state", "Pendiente")
                ->where("transaction.session_id", $request->input("session_id"))
                ->where("transaction.type", "Pagos")
                ->first();

            if(is_null($verifyData)){
                return response()->json([
                    "success"   => false,
                    "cod_error" => "200",
                    "message_error"   => "El token es incorrecto, por favor verifique e intente nuevamente"
                ]);
            }

            //actualiza el saldo de la billetera
            $wallet = Wallet::find($verifyData->wallet_id);

            $wallet->saldo = bcsub($verifyData->saldo_actual, $verifyData->saldo_descontar, 2);

            $wallet->update();

            //actualiza el registro para ponerlo aprobado
            $updateTransaction = Transaction::find($verifyData->transaction_id);

            $updateTransaction->state = "Aprobada";

            $updateTransaction->update();

            DB::commit();

            return response()->json([
                "success"   => true,
                "cod_error" => "00",
                "message_error"   => "El pago se realizo correctamente, gracias por utilizar nuestros servicios"
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

}
