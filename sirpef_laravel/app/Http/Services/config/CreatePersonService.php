<?php 

namespace App\Http\Services\config;

use Illuminate\Http\{
    Request,
    JsonResponse
};
use App\Models\Persona;
use App\Models\EventoPersona; // Asegúrate de importar el modelo EventoPersona
use App\Models\Evento; // Asegúrate de importar el modelo Evento
use Illuminate\Support\Facades\Auth;

class CreatePersonService {

    static public function create(Request $request)
    {
        // Verifica si el usuario está autenticado
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'No autorizado'], 401); // 401 Unauthorized
        }

        // Accede a la configuración del usuario autenticado
        $configUser  = $user->configUser ; // Obtiene el ConfigUser  relacionado
        if (!$configUser ) {
            return response()->json(['message' => 'Configuración de usuario no encontrada'], 404); // 404 Not Found
        }

        // Obtiene el valor del campo evento_activo
        $eventoActivo = $configUser ->evento_activo;

        // Valida que el evento esté activo
        $evento = Evento::find($eventoActivo);
        if (!$evento || $evento->estatus !== true) {
            return response()->json(['message' => 'El evento no está activo'], 400); // 400 Bad Request
        }

        // Valida los datos de entrada
        $validatedData = $request->validate([
            'nombre_completo' => 'required',
            'sexo' => 'required',
            'fecha_nacimiento' => 'required',
            'cedula' => 'required|unique:tbl_personas,cedula',
            'unidad_adscrita' => 'required',
            'tipo_empleado_id' => 'required',
            'centro' => 'required',
            'telefono' => 'required',
            'parroquia' => 'required',
            'direccion' => 'required',
        ]);
    
        // Crea un nuevo registro de Persona
        $registro = new Persona;
        $registro->nombre_completo = $validatedData['nombre_completo'];
        $registro->cedula = $validatedData['cedula'];
        $registro->direccion = $validatedData['direccion'];
        $registro->telefono = $validatedData['telefono'];
        $registro->fecha_nacimiento = $validatedData['fecha_nacimiento'];
        $registro->sexo = $validatedData['sexo'];
        $registro->tipo_empleado_id = $validatedData['tipo_empleado_id'];
        $registro->parroquia_id = $validatedData['parroquia'];
        $registro->centro_id = $validatedData['centro'];
        $registro->ministerio_id = $validatedData['unidad_adscrita'];

        // Guarda el registro de Persona
        $registro->save();

        // Guarda la relación en la tabla EventoPersona
        $eventoPersona = new EventoPersona();
        $eventoPersona->evento_id = $eventoActivo; // Asigna el evento activo
        $eventoPersona->persona_id = $registro->id; // Asigna el ID de la persona creada
        $eventoPersona->cantidad = 1; // Asigna un valor por defecto o según tu lógica
        $eventoPersona->estatus = 'activo'; // Asigna un estatus por defecto o según tu lógica

        // Guarda el registro en la tabla EventoPersona
        $eventoPersona->save();
    
        return response()->json(['message' => 'Registro creado exitosamente'], 200);
    }
}