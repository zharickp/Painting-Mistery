<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function index(): View
    {
        $banners = Banner::orderBy('orden')->paginate(10);

        return view('admin.banners.index', compact('banners'));
    }

    public function create(): View
    {
        return view('admin.banners.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'imagen'        => 'required|image|mimes:jpg,jpeg,png,webp|max:8192',
            'titulo'        => 'required|string|max:150',
            'subtitulo'     => 'nullable|string|max:255',
            'boton_texto'   => 'nullable|string|max:50',
            'boton_enlace'  => 'nullable|string|max:255',
            'orden'         => 'nullable|integer|min:0',
            'activo'        => 'nullable|boolean',
            'publicar_en'   => 'nullable|date',
        ]);

        $nombreArchivo = time() . '_' . $request->file('imagen')->getClientOriginalName();
        $request->file('imagen')->move(public_path('images/banners'), $nombreArchivo);

        Banner::create([
            'imagen'       => '/images/banners/' . $nombreArchivo,
            'titulo'       => $request->titulo,
            'subtitulo'    => $request->subtitulo,
            'boton_texto'  => $request->boton_texto,
            'boton_enlace' => $request->boton_enlace,
            'orden'        => (int) ($request->orden ?? 0),
            'activo'       => $request->boolean('activo', true),
            'publicar_en'  => $request->publicar_en,
        ]);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner creado correctamente.');
    }

    public function edit(Banner $banner): View
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $request->validate([
            'imagen'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'titulo'        => 'required|string|max:150',
            'subtitulo'     => 'nullable|string|max:255',
            'boton_texto'   => 'nullable|string|max:50',
            'boton_enlace'  => 'nullable|string|max:255',
            'orden'         => 'nullable|integer|min:0',
            'activo'        => 'nullable|boolean',
            'publicar_en'   => 'nullable|date',
        ]);

        $rutaImagen = $banner->imagen;
        if ($request->hasFile('imagen')) {
            if ($banner->imagen && file_exists(public_path($banner->imagen))) {
                unlink(public_path($banner->imagen));
            }
            $nombreArchivo = time() . '_' . $request->file('imagen')->getClientOriginalName();
            $request->file('imagen')->move(public_path('images/banners'), $nombreArchivo);
            $rutaImagen = '/images/banners/' . $nombreArchivo;
        }

        $banner->update([
            'imagen'       => $rutaImagen,
            'titulo'       => $request->titulo,
            'subtitulo'    => $request->subtitulo,
            'boton_texto'  => $request->boton_texto,
            'boton_enlace' => $request->boton_enlace,
            'orden'        => (int) ($request->orden ?? 0),
            'activo'       => $request->boolean('activo', true),
            'publicar_en'  => $request->publicar_en,
        ]);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner actualizado correctamente.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        if ($banner->imagen && file_exists(public_path($banner->imagen))) {
            unlink(public_path($banner->imagen));
        }
        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner eliminado.');
    }

    public function toggleEstado(Banner $banner): RedirectResponse
    {
        $banner->update(['activo' => ! $banner->activo]);

        $mensaje = $banner->activo ? 'Banner activado.' : 'Banner desactivado.';

        return redirect()->route('admin.banners.index')
            ->with('success', $mensaje);
    }
}
