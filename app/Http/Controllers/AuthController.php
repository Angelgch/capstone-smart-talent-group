<?php

// app/Http/Controllers/AuthController.php   (REEMPLAZA el anterior)
// Registro y login reales contra la BD. Responde JSON porque /auth/login.js usa fetch (la página no se recarga).

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /* ------------------------------------------------------------------
       LOGIN: valida, intenta autenticar y devuelve a dónde redirigir según el rol
       ------------------------------------------------------------------ */
    public function login(Request $request): JsonResponse
    {
        // El correo siempre en minúsculas
        $request->merge(['email' => Str::lower((string) $request->input('email'))]);

        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'Ingrese su correo electrónico.',
            'email.email'       => 'Ingrese un correo electrónico válido.',
            'password.required' => 'Ingrese su contraseña.',
        ]);

        if (! Auth::attempt($credentials)) {
            $msg = 'Credenciales incorrectas. Verifique correo y contraseña.';
            return response()->json(['message' => $msg, 'errors' => ['email' => [$msg]]], 422);
        }

        $request->session()->regenerate(); // protege contra "session fixation"

        return response()->json(['redirect' => $this->homeFor(Auth::user())]);
    }

    /* ------------------------------------------------------------------
       REGISTRO: el RUC debe ser de una empresa YA registrada. El backend busca esa empresa
       y guarda su id en el usuario (company_id). El rol siempre es 'user'.
       ------------------------------------------------------------------ */
    public function register(Request $request): JsonResponse
    {
        $request->merge(['email' => Str::lower((string) $request->input('email'))]);

        $data = $request->validate([
            'ruc'      => ['required', 'digits:11', 'exists:companies,ruc'],   // la empresa tiene que existir
            'dni'      => ['required', 'digits:8', 'unique:users,dni'],
            'names'    => ['required', 'string', 'max:100', "regex:/^[\pL\s.'-]+$/u"],
            'surnames' => ['required', 'string', 'max:100', "regex:/^[\pL\s.'-]+$/u"],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['required', 'digits:9'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],        // usa password_confirmation
            'terms'    => ['accepted'],
        ], [
            'ruc.required'       => 'El RUC es obligatorio.',
            'ruc.digits'         => 'El RUC debe tener exactamente 11 dígitos.',
            'ruc.exists'         => 'No hay una empresa registrada con ese RUC. Contacta al administrador.',
            'dni.required'       => 'El DNI es obligatorio.',
            'dni.digits'         => 'El DNI debe tener exactamente 8 dígitos.',
            'dni.unique'         => 'Este DNI ya está registrado.',
            'names.required'     => 'Los nombres son obligatorios.',
            'names.regex'        => 'Los nombres solo pueden contener letras.',
            'surnames.required'  => 'Los apellidos son obligatorios.',
            'surnames.regex'     => 'Los apellidos solo pueden contener letras.',
            'email.required'     => 'El correo electrónico es obligatorio.',
            'email.email'        => 'Ingrese un correo electrónico válido.',
            'email.unique'       => 'Este correo ya está registrado.',
            'phone.required'     => 'El teléfono es obligatorio.',
            'phone.digits'       => 'El teléfono debe tener exactamente 9 dígitos.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'terms.accepted'     => 'Debe aceptar los términos y condiciones.',
        ]);

        $user = User::create([
            'company_id'        => Company::where('ruc', $data['ruc'])->value('id'),  // lo decide el servidor
            'dni'               => $data['dni'],
            'names'             => $data['names'],
            'surnames'          => $data['surnames'],
            'email'             => $data['email'],
            'phone'             => $data['phone'],
            'password'          => $data['password'],   // se encripta sola (cast "hashed" del modelo)
            'terms_accepted_at' => now(),
        ]);                                              // role = 'user' por defecto de la BD

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json(['redirect' => $this->homeFor($user)], 201);
    }

    // A dónde va cada rol al entrar. Ruta relativa (false) para no depender de APP_URL.
    private function homeFor(User $user): string
    {
        return route($user->isAdmin() ? 'admin.dashboard' : 'user.dashboard', [], false);
    }
}
