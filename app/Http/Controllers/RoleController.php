<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::paginate(10);
        return view('roles.index', compact('roles'), ['tittle' => 'Manajemen Roles | Portal Supplier']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('roles.create', ['tittle' => 'Tambah Role | Portal Supplier']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles|max:255',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama role harus diisi',
            'name.unique' => 'Nama role sudah ada',
            'description.max' => 'Deskripsi maksimal 500 karakter',
        ]);

        Role::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => 'Role berhasil ditambahkan']);
        }

        return redirect()->route('roles.index')->with('success', 'Role berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        return view('roles.show', compact('role'), ['tittle' => 'Detail Role | Portal Supplier']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        if (request()->ajax()) {
            return response()->json(['role' => $role]);
        }

        return view('roles.edit', compact('role'), ['tittle' => 'Edit Role | Portal Supplier']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id . '|max:255',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama role harus diisi',
            'name.unique' => 'Nama role sudah ada',
            'description.max' => 'Deskripsi maksimal 500 karakter',
        ]);

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => 'Role berhasil diupdate']);
        }

        return redirect()->route('roles.index')->with('success', 'Role berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Check if role is being used by users
        if ($role->users()->exists()) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Role tidak dapat dihapus karena masih digunakan oleh users'], 422);
            }
            return redirect()->route('roles.index')->with('error', 'Role tidak dapat dihapus karena masih digunakan oleh users');
        }

        $role->delete();

        if (request()->ajax()) {
            return response()->json(['success' => 'Role berhasil dihapus']);
        }

        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus');
    }
}
