<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class rolesController extends Controller
{
    //mostrar los roles en la bd
    public function index(): View
    {
        $roles = Role::withCount('users')
            ->orderBy('name')
            ->paginate(10);
        
        return view('roles.index', compact('roles'));
    }

    //mostrar formulario de creación de rol
    public function create(): View
    {
        return view('roles.create');
    }

    //agregar un nuevo rol / insertar
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:roles,name'],
        ]);

        Role::create($validated);

        return redirect()->route('roles.index')
            ->with('success', 'Rol creado exitosamente.');
    }

    //Mostrar un rol
    public function show(Role $role): View
    {
        $role->load('users');
        
        return view('roles.show', compact('role'));
    }

    //abrir formulario de editar rol
    public function edit(Role $role): View
    {
        return view('roles.edit', compact('role'));
    }

    //Actualizar / editar rol
    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required', 
                'string', 
                'max:50', 
                Rule::unique('roles', 'name')->ignore($role->id)
            ],
        ]);

        $role->update($validated);

        return redirect()->route('roles.index')
            ->with('success', 'Rol actualizado exitosamente.');
    }

    //eliminar rol
    public function destroy(Role $role): RedirectResponse
    {
        // Verificar si tiene usuarios asignados
        if ($role->users()->exists()) {
            return redirect()->route('roles.index')
                ->with('error', 'No se puede eliminar un rol que tiene usuarios asignados.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Rol eliminado exitosamente.');
    }

    // obtener roles por api
    public function getRoles()
    {
        $roles = Role::orderBy('name')
            ->get(['id', 'name'])
            ->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => Role::getRoles()[$role->name] ?? $role->name,
                ];
            });

        return response()->json($roles);
    }

    // obtener usuarios por rol
    public function getUsersByRole(Role $role)
    {
        $users = $role->users()
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ];
            });

        return response()->json($users);
    }
}
