<?php

// app/Http/Controllers/User/AccountController.php
// "Tu cuenta" del USUARIO: actualizar su información, cambiar la contraseña y eliminar su cuenta.
// Todo se hace sobre el usuario con sesión iniciada (nunca se recibe un id desde el formulario).

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    // Información de la cuenta. El DNI, la empresa y el rol NO se pueden cambiar desde aquí.
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $request->merge(['email' => Str::lower((string) $request->input('email'))]);

        $data = $request->validate([
            'names'    => ['required', 'string', 'max:100', "regex:/^[\pL\s.'-]+$/u"],
            'surnames' => ['required', 'string', 'max:100', "regex:/^[\pL\s.'-]+$/u"],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'    => ['required', 'digits:9'],
        ], [
            'names.regex'    => 'Los nombres solo pueden contener letras.',
            'surnames.regex' => 'Los apellidos solo pueden contener letras.',
            'email.unique'   => 'Ese correo ya está registrado.',
            'phone.digits'   => 'El teléfono debe tener exactamente 9 dígitos.',
        ]);

        $user->update($data);

        return back()->with('success', 'Tus datos se actualizaron correctamente.');
    }

    // Cambiar contraseña: exige la actual
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'La contraseña actual es incorrecta.',
            'password.min'                      => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'                => 'Las contraseñas nuevas no coinciden.',
        ]);

        $request->user()->update(['password' => $request->password]); // se encripta sola (cast "hashed")

        return back()->with('success', 'Tu contraseña se actualizó correctamente.');
    }

    // Eliminar cuenta: pide la contraseña y borra también SUS solicitudes y sus archivos
    public function destroy(Request $request)
    {
        $request->validate(['password' => ['required', 'current_password']], [
            'password.required'         => 'Ingresa tu contraseña para eliminar tu cuenta.',
            'password.current_password' => 'La contraseña es incorrecta. Tu cuenta no se eliminó.',
        ]);

        $user = $request->user();

        $requestIds = $user->verificationRequests()->pluck('id');
        $paths = Document::whereIn('request_id', $requestIds)->pluck('file_path');

        DB::transaction(function () use ($user) {
            $user->verificationRequests()->delete(); // servicios y documentos caen por cascada
            $user->delete();
        });

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        foreach ($paths as $path) Storage::disk('local')->delete($path);

        return redirect()->route('login');
    }
}