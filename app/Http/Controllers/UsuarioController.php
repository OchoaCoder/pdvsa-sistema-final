<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    public function index()
    {
        // 1. Verificamos nivel: Si no es 2 (Auditor), lo mandamos al home.
        // Cambiamos withErrors por with('info') para que el layout lo maneje mejor.
        if (Auth::user()->nivel_acceso != 2) {
            return redirect()->to('/home')->with('info', 'Acceso denegado: Esta función es exclusiva para Auditores (Nivel 2).');
        }

        $usuarios = User::all();
        return view('usuarios.index', compact('usuarios'));
    }

    public function store(Request $request)
    {
        // 2. Doble seguridad de nivel
        if (Auth::user()->nivel_acceso != 2) {
            return abort(403, 'No tienes permisos de Auditor para registrar usuarios.');
        }

        $request->validate([
            'cedula' => 'required|unique:usuario,usuario',
            'usuario_nombre' => 'required',
            'password' => 'required|min:4',
            'nivel_acceso' => 'required'
        ], [
            'cedula.unique' => 'Error: Esta cédula ya está registrada en el sistema.',
        ]);

        try {
            // Limpiamos la cédula de espacios por si acaso
            $cedula = trim($request->cedula);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            User::create([
                'usuario'      => $cedula,
                'password'     => Hash::make($request->password),
                'nivel_acceso' => $request->nivel_acceso,
                'id_empleado'  => $cedula, 
            ]);

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return redirect()->route('usuarios.index')->with('success', '¡Excelente Jose! Usuario creado correctamente.');

        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return redirect()->back()->withErrors('Error al guardar: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        // 3. Triple seguridad de nivel para eliminar
        if (Auth::user()->nivel_acceso != 2) {
            return redirect()->back()->withErrors('Solo un Auditor puede eliminar registros.');
        }

        if (Auth::id() == $id) {
            return redirect()->back()->withErrors('No puedes eliminar tu propia sesión activa.');
        }

        User::destroy($id);
        return redirect()->route('usuarios.index')->with('success', 'El acceso ha sido eliminado.');
    }
}