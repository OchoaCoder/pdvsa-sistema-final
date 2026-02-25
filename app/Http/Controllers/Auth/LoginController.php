<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // 1. Validamos la entrada
        $credentials = $request->validate([
            'usuario'  => 'required|string',
            'password' => 'required|string',
        ]);

        // 2. Intentamos autenticar (sin filtrar por activo todavía)
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // 3. Verificamos el Estatus (1=Activo, 2=Inactivo, 3=Reposo, 4=Vacaciones, 5=Suspendido)
            if ($user->activo != 1) {
                $mensaje = $this->getMensajeEstatus($user->activo);
                
                Auth::logout(); // Cerramos la sesión si no está activo
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors(['usuario' => $mensaje]);
            }

            // 4. Si es Activo, regeneramos sesión y entra
            $request->session()->regenerate();
            return redirect()->intended('solicitudes');
        }

        // Si las credenciales están mal
        return back()->withErrors([
            'usuario' => 'El indicador o la contraseña son incorrectos.',
        ]);
    }

    /**
     * Función auxiliar para los mensajes de PDVSA
     */
    private function getMensajeEstatus($estatus)
    {
        return match ($estatus) {
            2 => 'Su usuario se encuentra INACTIVO. Contacte a su supervisor.',
            3 => 'Acceso denegado: Usted se encuentra actualmente en REPOSO MÉDICO.',
            4 => 'Acceso restringido: Usted se encuentra disfrutando de VACACIONES.',
            5 => 'USUARIO SUSPENDIDO: Diríjase a la oficina de Talento Humano.',
            default => 'Su estatus actual no le permite acceder al sistema.',
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}