<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('role', 'supplier')->paginate(10);
        $roles = Role::all();
        $suppliers = Supplier::all();
        return view('users.index', compact('users', 'roles', 'suppliers'), ['tittle' => 'Manajemen Users | Portal Supplier']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'), ['tittle' => 'Tambah User | Portal Supplier']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $role = Role::find($request->role_id);
        $isSupplierRole = $role && strtolower($role->name) === 'supplier';
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ];
        
        $messages = [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak sesuai',
            'role_id.required' => 'Role harus dipilih',
            'role_id.exists' => 'Role tidak valid',
        ];
        
        if ($isSupplierRole) {
            $rules['supplier_id'] = 'required|exists:suppliers,id';
            $messages['supplier_id.required'] = 'Supplier harus dipilih untuk role Supplier';
            $messages['supplier_id.exists'] = 'Supplier tidak valid';
        }
        
        $request->validate($rules, $messages);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ];
        
        if ($isSupplierRole && $request->supplier_id) {
            $data['supplier_id'] = $request->supplier_id;
        }

        User::create($data);

        if ($request->ajax()) {
            return response()->json(['success' => 'User berhasil ditambahkan']);
        }

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'), ['tittle' => 'Detail User | Portal Supplier']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $suppliers = Supplier::all();

        // Return JSON for AJAX
        if (request()->ajax()) {
            return response()->json([
                'user' => $user,
                'roles' => $roles,
                'suppliers' => $suppliers
            ]);
        }

        return view('users.edit', compact('user', 'roles', 'suppliers'), ['tittle' => 'Edit User | Portal Supplier']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $role = Role::find($request->role_id);
        $isSupplierRole = $role && strtolower($role->name) === 'supplier';
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|min:8|confirmed',
        ];
        
        $messages = [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.unique' => 'Email sudah terdaftar',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak sesuai',
            'role_id.required' => 'Role harus dipilih',
            'role_id.exists' => 'Role tidak valid',
        ];
        
        if ($isSupplierRole) {
            $rules['supplier_id'] = 'required|exists:suppliers,id';
            $messages['supplier_id.required'] = 'Supplier harus dipilih untuk role Supplier';
            $messages['supplier_id.exists'] = 'Supplier tidak valid';
        }
        
        $request->validate($rules, $messages);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ];
        
        if ($isSupplierRole && $request->supplier_id) {
            $data['supplier_id'] = $request->supplier_id;
        } else {
            $data['supplier_id'] = null;
        }

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->ajax()) {
            return response()->json(['success' => 'User berhasil diupdate']);
        }

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        if (request()->ajax()) {
            return response()->json(['success' => 'User berhasil dihapus']);
        }

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus');
    }
}
