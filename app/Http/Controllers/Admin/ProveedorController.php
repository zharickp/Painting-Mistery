<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProveedorController extends Controller
{
    public function index(Request $request): View
    {
        $buscar = trim((string) $request->query('buscar', ''));

        $proveedores = Proveedor::withCount(['compras' => fn ($q) => $q->where('estado', 'registrada')])
            ->when($buscar !== '', function ($q) use ($buscar) {
                $like = '%' . mb_strtolower(addcslashes($buscar, '\\%_')) . '%';
                $q->where(fn ($s) => $s->whereRaw('lower(nombre) like ?', [$like])
                    ->orWhereRaw("lower(coalesce(nit, '')) like ?", [$like])
                    ->orWhereRaw("lower(coalesce(persona_contacto, '')) like ?", [$like]));
            })
            ->orderByDesc('estado')
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('admin.proveedores.index', compact('proveedores', 'buscar'));
    }

    public function create(): View
    {
        return view('admin.proveedores.form', ['proveedor' => new Proveedor(['estado' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $proveedor = Proveedor::create($this->validar($request) + ['estado' => true]);

        return redirect()->route('admin.proveedores.index')
            ->with('success', "Proveedor \"{$proveedor->nombre}\" registrado.");
    }

    public function edit(Proveedor $proveedor): View
    {
        return view('admin.proveedores.form', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor): RedirectResponse
    {
        $proveedor->update($this->validar($request, $proveedor));

        return redirect()->route('admin.proveedores.index')
            ->with('success', "Proveedor \"{$proveedor->nombre}\" actualizado.");
    }

    /** Los proveedores no se borran (tienen compras asociadas): se desactivan. */
    public function toggle(Proveedor $proveedor): RedirectResponse
    {
        $proveedor->update(['estado' => ! $proveedor->estado]);

        return back()->with('success', $proveedor->estado
            ? "Proveedor \"{$proveedor->nombre}\" activado."
            : "Proveedor \"{$proveedor->nombre}\" desactivado: ya no aparece al registrar compras.");
    }

    private function validar(Request $request, ?Proveedor $proveedor = null): array
    {
        return $request->validate([
            'nombre'           => ['required', 'string', 'max:120'],
            'nit'              => ['nullable', 'string', 'max:30', Rule::unique('proveedor', 'nit')->ignore($proveedor?->id)],
            'persona_contacto' => ['nullable', 'string', 'max:120'],
            'telefono'         => ['nullable', 'string', 'max:40'],
            'correo'           => ['nullable', 'email', 'max:120'],
            'direccion'        => ['nullable', 'string', 'max:200'],
        ], [
            'nit.unique' => 'Ya hay un proveedor registrado con ese NIT.',
        ]);
    }
}
