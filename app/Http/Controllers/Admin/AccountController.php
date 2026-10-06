<?php

// app/Http/Controllers/Admin/AccountController.php
// "Tu cuenta" del ADMIN: actualizar su información y cambiar la contraseña.
// La cuenta del administrador general NO se puede eliminar (el sistema quedaría sin admin).

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $request->merge(['email' => Str::lower((string) $request->input('email'))]);

        $data = $request->validate([
            'names'    => ['required', 'string', 'max:100', "regex:/^[\pL\s.'-]+$/u"],
            'surnames' => ['required', 'string', 'max:100', "regex:/^[\pL\s.'-]+$/u"],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'    => ['nullable', 'digits:9'],
        ], [
            'names.regex'    => 'Los nombres solo pueden contener letras.',
            'surnames.regex' => 'Los apellidos solo pueden contener letras.',
            'email.unique'   => 'Ese correo ya está registrado.',
            'phone.digits'   => 'El teléfono debe tener exactamente 9 dígitos.',
        ]);

        $user->update($data);

        return back()->with('success', 'Tus datos se actualizaron correctamente.');
    }

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
}