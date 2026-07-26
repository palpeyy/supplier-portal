<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::latest()->paginate(10);
        return view('suppliers.index', compact('suppliers'), ['tittle' => 'Manajemen Supplier | Portal Supplier']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('suppliers.create', ['tittle' => 'Tambah Supplier | Portal Supplier']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_code' => 'required|string|max:20|unique:suppliers,supplier_code',
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'pic' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ], [
            'supplier_code.required' => 'Kode supplier harus diisi',
            'supplier_code.max' => 'Kode supplier maksimal 20 karakter',
            'supplier_code.unique' => 'Kode supplier sudah digunakan',
            'nama.required' => 'Nama supplier harus diisi',
            'nama.max' => 'Nama supplier maksimal 255 karakter',
            'pic.max' => 'PIC maksimal 255 karakter',
            'telephone.max' => 'Telephone maksimal 255 karakter',
            'contact_person.max' => 'Contact person maksimal 255 karakter',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
        ]);

        try {
            // Create supplier
            $supplier = Supplier::create([
                'supplier_code' => $request->supplier_code,
                'nama' => $request->nama,
                'alamat' => $request->alamat,
                'pic' => $request->pic,
                'telephone' => $request->telephone,
                'contact_person' => $request->contact_person,
            ]);

            // Create user for supplier with role_id = 2 (Supplier role)
            $user = User::create([
                'name' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => 2, // Supplier role ID
                'supplier_id' => $supplier->id,
            ]);

            Log::info('Supplier created with user account', [
                'supplier_id' => $supplier->id,
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            if ($request->ajax()) {
                return response()->json(['success' => 'Supplier berhasil ditambahkan dan akun login telah dibuat']);
            }

            return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil ditambahkan dan akun login telah dibuat. Email: ' . $request->email);
        } catch (\Exception $e) {
            Log::error('Error creating supplier and user', [
                'error' => $e->getMessage(),
            ]);

            if ($request->ajax()) {
                return response()->json(['error' => 'Gagal membuat supplier: ' . $e->getMessage()], 500);
            }

            return redirect()->route('suppliers.index')->with('error', 'Gagal membuat supplier: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'), ['tittle' => 'Detail Supplier | Portal Supplier']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        $supplier->load('users');
        if (request()->ajax()) {
            return response()->json(['supplier' => $supplier]);
        }

        return view('suppliers.edit', compact('supplier'), ['tittle' => 'Edit Supplier | Portal Supplier']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'supplier_code' => 'required|string|max:20|unique:suppliers,supplier_code,' . $supplier->id,
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'pic' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
        ], [
            'supplier_code.required' => 'Kode supplier harus diisi',
            'supplier_code.max' => 'Kode supplier maksimal 20 karakter',
            'supplier_code.unique' => 'Kode supplier sudah digunakan',
            'nama.required' => 'Nama supplier harus diisi',
            'nama.max' => 'Nama supplier maksimal 255 karakter',
            'pic.max' => 'PIC maksimal 255 karakter',
            'telephone.max' => 'Telephone maksimal 255 karakter',
            'contact_person.max' => 'Contact person maksimal 255 karakter',
            'password.min' => 'Password minimal 6 karakter',
        ]);

        try {
            // Update supplier
            $supplier->update([
                'supplier_code' => $request->supplier_code,
                'nama' => $request->nama,
                'alamat' => $request->alamat,
                'pic' => $request->pic,
                'telephone' => $request->telephone,
                'contact_person' => $request->contact_person,
            ]);

            // Update user password if provided
            if ($request->filled('password')) {
                $user = $supplier->users()->first();
                if ($user) {
                    $user->update([
                        'password' => Hash::make($request->password),
                    ]);
                    Log::info('Supplier user password updated', [
                        'supplier_id' => $supplier->id,
                        'user_id' => $user->id,
                    ]);
                }
            }

            if ($request->ajax()) {
                return response()->json(['success' => 'Supplier berhasil diupdate']);
            }

            return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil diupdate');
        } catch (\Exception $e) {
            Log::error('Error updating supplier', [
                'supplier_id' => $supplier->id,
                'error' => $e->getMessage(),
            ]);

            if ($request->ajax()) {
                return response()->json(['error' => 'Gagal update supplier: ' . $e->getMessage()], 500);
            }

            return redirect()->route('suppliers.index')->with('error', 'Gagal update supplier: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        try {
            // Delete associated users
            $supplier->users()->delete();

            // Delete supplier
            $supplier->delete();

            Log::info('Supplier deleted with associated users', [
                'supplier_id' => $supplier->id,
            ]);

            if (request()->ajax()) {
                return response()->json(['success' => 'Supplier dan akun loginnya berhasil dihapus']);
            }

            return redirect()->route('suppliers.index')->with('success', 'Supplier dan akun loginnya berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting supplier', [
                'supplier_id' => $supplier->id,
                'error' => $e->getMessage(),
            ]);

            if (request()->ajax()) {
                return response()->json(['error' => 'Gagal menghapus supplier: ' . $e->getMessage()], 500);
            }

            return redirect()->route('suppliers.index')->with('error', 'Gagal menghapus supplier: ' . $e->getMessage());
        }
    }
}
