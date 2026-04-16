<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth; 
use App\Http\Services\record\ObtenerCedulaService;
use App\Http\Services\record\UserInfoService;
use App\Http\Services\record\RegistroMultipleService;
use App\Http\Services\record\RegistroIndividualService;
use App\Http\Services\record\RegistroIndividualFeDeVidaService;
use App\Http\Services\record\ObtenerCedulaGrupalService;
use App\Http\Services\record\EditRegisterService;
use App\Http\Services\record\DeletedVoteService;
use App\Http\Services\record\ObtenerCedulaAutorizadoService;
use App\Http\Services\record\ObtenerCedulaFeDeVidaService;

use ClickSend\Configuration;
use ClickSend\Api\SMSApi;
use ClickSend\Model\SmsMessage;
use ClickSend\Model\SmsMessageCollection;
use GuzzleHttp\Client as ClientSms;
use ElephantIO\Client;

use App\Models\Persona;
use App\Models\Ente;
use App\Models\Genero;
use App\Models\Centro;
use App\Models\Registro;
use App\Models\UnidadAdscrita;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Parroquia;
use App\Models\Auditoria;

use Illuminate\Http\{
    Request,
    JsonResponse
};



class RegistroController extends Controller
{


//------------------------Individual-----------------------------------//

public function obtenerPorCedula($cedula)
{
    return ObtenerCedulaService::obtenerPorCedula($cedula);    
}

public function ObtenerCedulaFeDeVida($cedula)
{
    return ObtenerCedulaFeDeVidaService::ObtenerCedulaFeDeVida($cedula);    
}


public function registervote(Request $request)
{
    return RegistroIndividualService::registervote($request);    
}

//--------------------------------------------------------------------//

//------------------------Individual  fe de vida-----------------------------------//

public function registervoteFedeVida(Request $request)
{
    return RegistroIndividualFeDeVidaService::registervoteFedeVida($request);    
}
//--------------------------------------------------------------------//



//------------------------grupal y autorizado--------------------------//

public function obtenerPorCedulaGrupal($cedula)
{
    return ObtenerCedulaGrupalService::obtenerPorCedulaGrupal($cedula);    
}

public function obtenerCedulaAutorizado($cedula)
{
    return ObtenerCedulaAutorizadoService::obtenerCedulaAutorizado($cedula);    
}


public function registerMultipleVotes(Request $request)
{
    return RegistroMultipleService::registerMultipleVotes($request);    
}

//--------------------------------------------------------------------//





//--------------------------------------------------------------------//

public function DeleteVote($id)
{
    return DeletedVoteService::DeleteVote($id); 
}


public function editRegistro(Request $request, $personaId)
{
    return EditRegisterService::editRegistro($request,$personaId); 
}


public function getUserInfo($message)
{
    return UserInfoService::getUserInfo($message); 
}
//--------------------------------------------------------------------//




static public function registerMultipleVotesAutorizado(Request $request)
{
    $validatedData = $request->validate([
        'vote' => 'required',
        'descripcion' => 'nullable',
        'hora_voto' => 'nullable',
        'persona_ids' => 'required|array',
        'persona_ids.*' => 'integer',
    ]);

    $errors = [];
    $successful = [];
    $alreadyRegistered = [];
    $notFound = [];
    $infoMessages = []; // Para mensajes informativos

    foreach ($validatedData['persona_ids'] as $persona_id) {
        if ($persona_id === null) {
            $errors[] = "Persona ID cannot be null";
            continue;
        }

        $persona = Persona::find($persona_id);
        if (!$persona) {
            $notFound[] = $persona_id;
            continue;
        }

        $eventoPersona = $persona->eventos()->where('evento_id', auth()->user()->configUser ->evento_activo)->withPivot('id', 'estatus')->first();
        if (!$eventoPersona) {
            $errors[] = "La persona no está asociada al evento activo";
            continue;
        }

        // Validar el estatus
        if ($eventoPersona->pivot->estatus === 'rechazado') {
            $infoMessages[] = "No se puede registrar el voto para la persona autorizada por $persona->nombre_completo porque su solicitud fue rechazada.";
            continue; // No registrar el voto
        } elseif (is_null($eventoPersona->pivot->estatus)) {
            $infoMessages[] = "No se puede registrar el voto para la persona autorizada por $persona->nombre_completo porque su solicitud es null.";
            continue; // No registrar el voto
        }

        // Verificar si ya existe un registro
        $exists = Registro::where('evento_persona_id', $eventoPersona->pivot->id)->exists();
        if ($exists) {
            $alreadyRegistered[] = $persona_id;
            continue; // No registrar el voto si ya existe
        }

        // Intentar registrar el voto
        try {
            $registro = new Registro;
            $registro->voto = $validatedData['vote'];
            $registro->descripcion = $validatedData['descripcion'];
            $registro->hora_voto = $validatedData['hora_voto'] ?? now();
            $registro->evento_persona_id = $eventoPersona->pivot->id;
            $registro->save();

            $successful[] = $persona_id; // Agregar a la lista de exitosos
        } catch (\Throwable $th) {
            $errors[] = "Error al registrar la persona ID $persona->nombre_completo: " . $th->getMessage();
        }
    }

    // Preparar la respuesta
    $response = [];
    if (!empty($errors)) {
        $response['errors'] = $errors;
    }
    if (!empty($infoMessages)) {
        $response['info'] = $infoMessages;
    }

    $registeredCount = count($successful);
    $alreadyRegisteredCount = count($alreadyRegistered);
    $notFoundCount = count($notFound);

    $message = "Se registraron $registeredCount personas con éxito.";

    // Agregar información adicional al mensaje
    if ($alreadyRegisteredCount > 0) {
        $message .= " $alreadyRegisteredCount personas ya estaban registradas.";
    }
    if ($notFoundCount > 0) {
        $message .= " $notFoundCount personas no fueron encontradas.";
    }

    $response['msg'] = $message;

    return response()->json($response, !empty($errors) ? 400 : 201);
}
    
    
    
    /* public function sendSms($to_phone_number, $message)
        {
            $config = Configuration::getDefaultConfiguration()
            ->setUsername('samelgaravito@gmail.com')
            ->setPassword('B44C8DB8-BDF9-FE3F-F7D0-E93C9B087159');
    
            $apiInstance = new SMSApi(new ClientSms(),$config);
    
            $msg = new SmsMessage();
            $msg->setSource("SOURCE");
            $msg->setBody($message);
            $msg->setTo($to_phone_number);
    
            $sms_messages = new SmsMessageCollection();
            $sms_messages->setMessages([$msg]);
    
            try {
                $result = $apiInstance->smsSendPost($sms_messages);
                return $result;
            } catch (Exception $e) {
                return ['error' => 'Exception when calling SMSApi->smsSendPost: '.$e->getMessage()];
            }
        }
     */
    














































}   
