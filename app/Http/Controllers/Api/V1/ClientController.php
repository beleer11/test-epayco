<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * @description Consultar todos o un cliente/s con excepcion los que tienen borrado logico.
     *
     * @param $id
     * @return JsonResponse
     */
    public function getClient($id = null): JsonResponse
    {
        try {
            $client = Client::withoutTrashed();

            if(!is_null($id)){
                $client = $client->where("id", $id);
            }

            return response()->json([
                "success"   => true,
                "cod_error" => "00",
                "message_error"   => $client->get()
            ]);
        } catch (\Exception $e){
            return response()->json([
                "success"   => false,
                "cod_error" => $e->getCode(),
                "message_error"   => $e->getMessage()
            ]);
        }
    }

    /**
     * @description Guardar los nuevos clientes del sistema.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function clientRegistration(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            //se crea nuevo registro
            $client = new Client();

            $client->name               = $request->input("name");
            $client->document_number    = $request->input("document_number");
            $client->age                = $request->input("age");
            $client->cel                = $request->input("cel");
            $client->email              = $request->input("email");

            $client->save();

            DB::commit();

            return response()->json([
                "success"   => true,
                "cod_error" => "00",
                "message_error"   => "Se creo el usuario ". $request->input("name") . ", exitosamente."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "success"   => false,
                "cod_error" => $e->getCode(),
                "message_error"   => $e->getMessage()
            ]);
        }

    }
}
