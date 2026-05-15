<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Cover;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class CoverController extends Controller
{
    public function index()
    {
        $covers = Cover::orderBy('order')->get();
        return view('admin.covers.index', compact('covers'));
    }

    public function create()
    {
        return view('admin.covers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'image' => 'required|image|max:1024',
            'title' => 'required|string|max:255',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'is_active' => 'required|boolean',
        ]);
        $data['image_path'] = Storage::disk('public')->put('covers', $data['image']);
        $cover = Cover::create($data);
        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Portada creada',
            'text' => 'La portada se ha creado correctamente',
        ]);
        return redirect()->route('admin.covers.edit', $cover);
    }

    public function show(Cover $cover)
    {
        //
    }

    public function edit(Cover $cover)
    {
        return view('admin.covers.edit', compact('cover'));
    }

    public function update(Request $request, Cover $cover)
    {
        $data = $request->validate([
            'image' => 'nullable|image|max:1024',
            'title' => 'required|string|max:255',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'is_active' => 'required|boolean',
        ]);
        if (isset($data['image'])) {
            Storage::disk('public')->delete($cover->image_path);
            $data['image_path'] = Storage::disk('public')->put('covers', $data['image']);
        }
        $cover->update($data);
        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Portada actualizada',
            'text' => 'La portada se ha actualizado correctamente',
        ]);
        return redirect()->route('admin.covers.edit', $cover);
    }

    public function destroy(Cover $cover)
    {
        //
    }
}
