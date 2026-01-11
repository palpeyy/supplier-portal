<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

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
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'pic' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
        ], [
            'nama.required' => 'Nama supplier harus diisi',
            'nama.max' => 'Nama supplier maksimal 255 karakter',
            'pic.max' => 'PIC maksimal 255 karakter',
            'telephone.max' => 'Telephone maksimal 255 karakter',
            'contact_person.max' => 'Contact person maksimal 255 karakter',
        ]);

        Supplier::create([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'pic' => $request->pic,
            'telephone' => $request->telephone,
            'contact_person' => $request->contact_person,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => 'Supplier berhasil ditambahkan']);
        }

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil ditambahkan');
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
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'pic' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
        ], [
            'nama.required' => 'Nama supplier harus diisi',
            'nama.max' => 'Nama supplier maksimal 255 karakter',
            'pic.max' => 'PIC maksimal 255 karakter',
            'telephone.max' => 'Telephone maksimal 255 karakter',
            'contact_person.max' => 'Contact person maksimal 255 karakter',
        ]);

        $supplier->update([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'pic' => $request->pic,
            'telephone' => $request->telephone,
            'contact_person' => $request->contact_person,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => 'Supplier berhasil diupdate']);
        }

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        if (request()->ajax()) {
            return response()->json(['success' => 'Supplier berhasil dihapus']);
        }

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil dihapus');
    }
}
