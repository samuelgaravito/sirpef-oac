<?php 

namespace App\Http\Services\config;
use Illuminate\Http\{
    Request,
    JsonResponse
};

use App\Models\Auditoria;

class AuditService {




    static public function indexAuditorias()
    {
        $auditorias = Auditoria::all();

        return response()->json(['auditorias' => $auditorias]);
    }


    static public function createAuditoria()
    {
        return response()->json(['message' => 'Crear auditoria']);
    }


    static public function storeAuditoria(Request $request)
    {
        $auditoria = new Auditoria();

        $auditoria->descripcion = $request->input('descripcion');

        $auditoria->persona_id = $request->input('persona_id');

        $auditoria->save();

        return response()->json(['message' => 'Auditoria creada con éxito', 'auditoria' => $auditoria]);
    }


    static public function showAuditoria($id)
    {
        $auditoria = Auditoria::find($id);

        return response()->json(['auditoria' => $auditoria]);
    }


    static public function editAuditoria($id)
    {
        $auditoria = Auditoria::find($id);

        return response()->json(['auditoria' => $auditoria]);
    }


 /*    static public function updateAuditoria(Request $request, $id)
    {
        $auditoria = Auditoria::find($id);

        $auditoria->descripcion = $request->input('descripcion');

        $auditoria->persona_id = $request->input('persona_id');

        $auditoria->save();

        return response()->json(['message' => 'Auditoria actualizada con éxito', 'auditoria' => $auditoria]);
    }
 */

    static public function destroyAuditoria($id)
    {
        $auditoria = Auditoria::find($id);

        $auditoria->delete();

        return response()->json(['message' => 'Auditoria eliminada con éxito']);
    }
}