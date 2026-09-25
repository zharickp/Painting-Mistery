<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResenaSitio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResenaSitioController extends Controller
{
    public function index(): View
    {
        $resenas = ResenaSitio::orderByRaw("CASE estado WHEN 'pendiente' THEN 0 WHEN 'aprobada' THEN 1 ELSE 2 END")
            ->orderByDesc('created_at')->paginate(20);

        return view('admin.resenas-sitio', compact('resenas'));
    }

    public function update(Request $request, ResenaSitio $resena): RedirectResponse
    {
        $request->validate(['estado' => ['required', 'in:aprobada,rechazada,pendiente']]);
        $resena->update(['estado' => $request->estado]);

        return back()->with('success', 'Reseña actualizada.');
    }
}
