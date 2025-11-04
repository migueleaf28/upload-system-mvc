<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = Group::latest()->get();
        return view('admin.groups.index', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.groups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:groups'],
            'description' => ['nullable', 'string'],
            'storage_quota' => ['nullable', 'integer', 'min:1'],
        ], [
            'name.required' => 'El nombre del grupo es obligatorio.',
            'name.unique' => 'Ya existe un grupo con este nombre.',
            'storage_quota.integer' => 'La cuota de almacenamiento debe ser tener un digito.',
            'storage_quota.min' => 'La cuota de almacenamiento debe ser al menos 1 MB.',
        ]);

        $storageQuota = $request->storage_quota ? $request->storage_quota * 1048576 : null;

        Group::create([
            'name' => $request->name,
            'description' => $request->description,
            'storage_quota' => $storageQuota,
        ]);

        return redirect()->route('admin.groups.index')
            ->with('success', 'Grupo creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        return view('admin.groups.show', compact('group'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group)
    {
        $availableUsers = User::whereDoesntHave('groups', function($query) use ($group) {
            $query->where('groups.id', $group->id);
        })->get();
        
        return view('admin.groups.edit', compact('group', 'availableUsers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
        $request->validate([
            'name' => [
                'required', 
                'string', 
                'max:255', 
                Rule::unique('groups')->ignore($group->id)
            ],
            'description' => ['nullable', 'string'],
            'storage_quota' => ['nullable', 'integer', 'min:1'],
        ], [
            'name.required' => 'El nombre del grupo es obligatorio.',
            'name.unique' => 'Ya existe un grupo con este nombre.',
            'storage_quota.integer' => 'La cuota de almacenamiento debe ser un número.',
            'storage_quota.min' => 'La cuota de almacenamiento debe ser al menos 1 MB.',
        ]);

        $storageQuota = $request->storage_quota ? $request->storage_quota * 1048576 : null;

        $group->update([
            'name' => $request->name,
            'description' => $request->description,
            'storage_quota' => $storageQuota,
        ]);

        return redirect()->route('admin.groups.index')
            ->with('success', 'Grupo actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        if ($group->users()->exists()) {
            return redirect()->route('admin.groups.index')
                ->with('error', 'No se puede eliminar el grupo porque tiene usuarios asociados.');
        }

        $group->delete();

        return redirect()->route('admin.groups.index')
            ->with('success', 'Grupo eliminado exitosamente.');
    }

    public function addUser(Request $request, Group $group)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        if ($group->users()->where('user_id', $request->user_id)->exists()) {
            return redirect()->back()->with('error', 'El usuario ya está en este grupo.');
        }

        $group->users()->attach($request->user_id);

        return redirect()->back()->with('success', 'Usuario agregado al grupo exitosamente.');
    }

    public function removeUser(Group $group, User $user)
    {
        $group->users()->detach($user->id);

        return redirect()->back()->with('success', 'Usuario removido del grupo exitosamente.');
    }
}