<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ✅ LISTAR (JSON) — para poblar el modal por AJAX si quieres
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        // Solo JSON (evitamos View not found)
        return response()->json([
            'ok' => true,
            'categories' => Category::query()
                ->orderBy('name')
                ->get(['id', 'name', 'slug']),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ✅ CREAR (modal)
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:categories,name'],
            'slug' => ['nullable', 'string', 'max:180'],
        ]);

        // Si mandas slug manual desde UI, lo normalizamos.
        // Si no mandas, tu Model puede generarlo solo (igual lo aseguramos).
        $slug = null;
        if (!empty($validated['slug'])) {
            $slug = $this->uniqueSlugFrom($validated['slug']);
        }

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => $slug, // si es null, el Model lo puede completar en boot()
        ])->fresh();

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'category' => $category->only(['id', 'name', 'slug']),
            ], 201);
        }

        return redirect()->back()->with('success', 'Categoría creada correctamente.');
    }

    /*
    |--------------------------------------------------------------------------
    | ✅ ACTUALIZAR (modal)
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('categories', 'name')->ignore($category->id),
            ],
            'slug' => ['nullable', 'string', 'max:180'],
        ]);

        $payload = ['name' => $validated['name']];

        // Si mandas slug manual lo hacemos único.
        // Si no mandas slug, tu Model decide si lo recalcula o no.
        if (array_key_exists('slug', $validated) && !empty($validated['slug'])) {
            $payload['slug'] = $this->uniqueSlugFrom($validated['slug'], $category->id);
        } elseif (array_key_exists('slug', $validated) && empty($validated['slug'])) {
            // Si mandan slug vacío explícito, lo dejamos null para que el Model lo regenere si aplica
            $payload['slug'] = null;
        }

        $category->update($payload);
        $category = $category->fresh();

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'category' => $category->only(['id', 'name', 'slug']),
            ]);
        }

        return redirect()->back()->with('success', 'Categoría actualizada correctamente.');
    }

    /*
    |--------------------------------------------------------------------------
    | ✅ ELIMINAR (modal)
    |--------------------------------------------------------------------------
    */
    public function destroy(Request $request, Category $category)
    {
        if ($category->products()->exists()) {
            $msg = 'No puedes eliminar una categoría que tiene productos asociados.';

            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => $msg,
                ], 422);
            }

            return redirect()->back()->with('error', $msg);
        }

        $category->delete();

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->back()->with('success', 'Categoría eliminada correctamente.');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */
    private function uniqueSlugFrom(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'categoria';
        $slug = $base;
        $i = 2;

        $query = Category::query();
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        while ($query->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}