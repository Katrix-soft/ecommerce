<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Family;
use Illuminate\Http\Request;

class FamilyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $families = Family::orderBy('id', 'desc')
        ->paginate(10);
        return view('admin.families.index', compact('families'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
        return view('admin.families.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        Family::create($request->all());

        session()->flash('swal', [
            'icon'=> 'success',
            'title' =>'Bien hecho',
            'text'=> 'Familia creada correctamente'
        ]);

        return redirect()->route('admin.families.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Family $family)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Family $family)
    {
     return view('admin.families.edit', compact('family'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Family $family)
    {
        $request->validate([
            'name'=> 'required'
        ]);
        $family->update($request->all());

        session()->flash('swal', [
            'icon'=> 'success',
            'title' =>'Bien hecho',
            'text'=> 'Familia actualizada correctamente'
        ]);

        return redirect()->route('admin.families.edit', $family);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Family $family)
{
    // Verificar si tiene categorías asociadas
    if ($family->categories()->count() > 0) {
        return redirect()->back()->with('swal', [
            'title' => 'Error',
            'text' => 'No podés eliminar esta familia porque tiene categorías asociadas.',
            'icon' => 'error'
        ]);
    }

    $family->delete();

    return redirect()->route('admin.families.index')->with('swal', [
        'title' => 'Eliminado!',
        'text' => 'La familia fue eliminada correctamente.',
        'icon' => 'success'
    ]);
}
}
