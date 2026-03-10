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
        $query = Solicitud::with(['beneficio', 'estatus', 'usuario']); 
        
        // 1. Filtro de Privacidad
        if ($usuario->nivel_acceso == 1) {
            $query->where('id_usuario', $usuario->id);
        }

        // 2. Filtro de Rango de Fechas (Para la tabla y el gráfico)
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
     * EXCEL DINÁMICO (Paso 2): Recibe filtros de la vista
     */
    public function exportarExcel(Request $request)
    {
        // Seguridad: Nivel 2 y 3 pueden exportar
        if (Auth::user()->nivel_acceso < 2) {
            abort(403, 'No tienes permisos para descargar reportes.');
        }

        // Capturamos las fechas enviadas por el botón de la vista
        $fecha_inicio = $request->query('fecha_inicio');
        $fecha_fin = $request->query('fecha_fin');

        return Excel::download(
            new SolicitudesExport($fecha_inicio, $fecha_fin), 
            'Reporte_PDVSA_'.now()->format('d_m_Y').'.xlsx'
        );
    }

    /**
     * Guardar nueva solicitud con doble validación (Antiduplicados)
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_cdt' => 'required',
            'id_beneficio' => 'required',
            'descripcion' => 'required|min:10',
        ]);

        $usuario = Auth::user();

        // VALIDACIÓN A: No permitir si ya hay una PENDIENTE del mismo beneficio
        $existePendiente = Solicitud::where('id_usuario', $usuario->id)
                                    ->where('id_beneficio', $request->id_beneficio)
                                    ->where('id_estatusSol', 1)
                                    ->exists();

        if ($existePendiente) {
            return back()->with('error', 'Ya tienes una solicitud pendiente para este beneficio.');
        }

        // VALIDACIÓN B: No permitir repetir el mismo beneficio el mismo DÍA
        $existeHoy = Solicitud::where('id_usuario', $usuario->id)
                                ->where('id_beneficio', $request->id_beneficio)
                                ->whereDate('fecha_solicitud', now()->toDateString())
                                ->exists();

        if ($existeHoy) {
            return back()->with('error', 'Límite alcanzado: Solo puedes enviar una solicitud de este tipo por día.');
        }

        $empleado = Empleado::where('codigo', $usuario->id_empleado)->first();

        Solicitud::create([
            'id_usuario'      => $usuario->id,
            'id_cdt'          => $request->id_cdt,
            'id_dept'         => $empleado->id_dept ?? 1,
            'id_cargo'        => $empleado->id_cargo ?? 1,
            'id_beneficio'    => $request->id_beneficio,
            'id_estatusSol'   => 1, 
            'descripcion'     => $request->descripcion,
            'monto'           => 0, 
            'fecha_solicitud' => now(),
        ]);

        return redirect()->route('solicitudes.index')->with('success', '¡Solicitud enviada a revisión!');
    }

    /**
     * Aprobar o Rechazar (Solo Nivel 3)
     */
    public function actualizarEstatus(Request $request, $id)
    {
        if (Auth::user()->nivel_acceso < 3) {
            return back()->with('error', 'No tienes autorización para realizar esta acción.');
        }

        $request->validate([
            'id_estatusSol' => 'required|in:2,3', 
            'monto' => 'nullable|numeric|min:0',
        ]);

        $solicitud = Solicitud::findOrFail($id);
        
        $solicitud->update([
            'id_estatusSol' => $request->id_estatusSol,
            'monto' => $request->monto ?? $solicitud->monto,
        ]);

        $statusName = $request->id_estatusSol == 2 ? 'APROBADA' : 'RECHAZADA';
        
        return back()->with('success', "Solicitud #{$id} marcada como {$statusName}.");
    }

    public function create()
    {
        $centros = Cdt::where('activo', 1)->get();
        $beneficios = Beneficio::where('activo', 1)->get();
        
        $usuario = Auth::user();
        $empleado = Empleado::where('codigo', $usuario->id_empleado)->first();

        return view('solicitudes.create', compact('centros', 'beneficios', 'empleado'));
    }

    public function descargarPDF($id)
    {
        $solicitud = Solicitud::with(['beneficio', 'estatus'])
                              ->where('id_solicitud', $id)
                              ->firstOrFail();

        if (Auth::user()->nivel_acceso == 1 && $solicitud->id_usuario != Auth::id()) {
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