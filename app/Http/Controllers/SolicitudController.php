<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\Cdt;
use App\Models\Beneficio;
use App\Models\Empleado;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf; 
use App\Exports\SolicitudesExport;
use Maatwebsite\Excel\Facades\Excel;

class SolicitudController extends Controller
{
    /**
     * Listado de solicitudes con Gráficos y Filtros
     */
    public function index(Request $request)
    {
        $usuario = Auth::user();
        $query = Solicitud::with(['beneficio', 'estatus', 'usuario']); // Añadimos usuario para saber quién solicita
        
        // 1. Filtro de Privacidad: Nivel 1 y 2 solo ven lo suyo (o de su dpto si quisieras)
        // Nivel 3 (Admin) ve todo.
        if ($usuario->nivel_acceso < 3) {
            $query->where('id_usuario', $usuario->id);
        }

        // 2. Filtro de Rango de Fechas
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('fecha_solicitud', [$request->fecha_inicio, $request->fecha_fin]);
        }

        $solicitudes = $query->orderBy('fecha_solicitud', 'desc')->get();

        // 3. Estadísticas
        $total = $solicitudes->count();
        $pendientes = $solicitudes->where('id_estatusSol', 1)->count();
        $aprobadas = $solicitudes->where('id_estatusSol', 2)->count();
        $rechazadas = $solicitudes->where('id_estatusSol', 3)->count();

        return view('solicitudes.index', compact(
            'solicitudes', 'total', 'pendientes', 'aprobadas', 'rechazadas'
        ));
    }

    /**
     * NUEVA FUNCIÓN: Aprobar o Rechazar Solicitud (Solo Nivel 3)
     */
    public function actualizarEstatus(Request $request, $id)
    {
        // Solo administradores nivel 3 pueden auditar
        if (Auth::user()->nivel_acceso < 3) {
            return back()->with('error', 'No tienes autorización para realizar esta acción.');
        }

        $request->validate([
            'id_estatusSol' => 'required|in:2,3', // 2: Aprobado, 3: Rechazado
            'monto' => 'nullable|numeric|min:0',
        ]);

        $solicitud = Solicitud::findOrFail($id);
        
        $solicitud->update([
            'id_estatusSol' => $request->id_estatusSol,
            'monto' => $request->monto ?? $solicitud->monto,
            // Aquí podrías guardar quién aprobó si añades la columna 'auditado_por'
        ]);

        $statusName = $request->id_estatusSol == 2 ? 'APROBADA' : 'RECHAZADA';
        
        return back()->with('success', "Solicitud #{$id} marcada como {$statusName}.");
    }

    public function exportarExcel(Request $request)
    {
        if (Auth::user()->nivel_acceso < 2) {
            abort(403, 'No tienes permisos para descargar reportes.');
        }

        $fecha_inicio = $request->query('fecha_inicio');
        $fecha_fin = $request->query('fecha_fin');

        return Excel::download(
            new SolicitudesExport($fecha_inicio, $fecha_fin), 
            'Reporte_PDVSA_'.now()->format('d_m_Y').'.xlsx'
        );
    }

    public function create()
    {
        $centros = Cdt::where('activo', 1)->get();
        $beneficios = Beneficio::where('activo', 1)->get();
        
        $usuario = Auth::user();
        $empleado = Empleado::where('codigo', $usuario->id_empleado)->first();

        return view('solicitudes.create', compact('centros', 'beneficios', 'empleado'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_cdt' => 'required',
            'id_beneficio' => 'required',
            'descripcion' => 'required|min:10',
        ]);

        $usuario = Auth::user();
        $empleado = Empleado::where('codigo', $usuario->id_empleado)->first();

        Solicitud::create([
            'id_usuario'      => $usuario->id,
            'id_cdt'          => $request->id_cdt,
            'id_dept'         => $empleado->id_dept ?? 1,
            'id_cargo'        => $empleado->id_cargo ?? 1,
            'id_beneficio'    => $request->id_beneficio,
            'id_estatusSol'   => 1, // Por defecto Pendiente
            'descripcion'     => $request->descripcion,
            'monto'           => 0, 
            'fecha_solicitud' => now(),
        ]);

        return redirect()->route('solicitudes.index')->with('success', '¡Solicitud enviada a revisión!');
    }

    public function descargarPDF($id)
    {
        $solicitud = Solicitud::with(['beneficio', 'estatus'])
                              ->where('id_solicitud', $id)
                              ->firstOrFail();

        if (Auth::user()->nivel_acceso < 3 && $solicitud->id_usuario != Auth::id()) {
            abort(403);
        }

        $pdf = Pdf::loadView('solicitudes.pdf', compact('solicitud'));
        return $pdf->stream('Comprobante_PDVSA_'.$id.'.pdf');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['La contraseña actual es incorrecta.'],
            ]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', '¡Contraseña actualizada!');
    }
}