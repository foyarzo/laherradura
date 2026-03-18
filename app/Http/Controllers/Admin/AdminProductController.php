<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;

class AdminProductController extends Controller
{
    public function index()
    {
        // ✅ Cargar productos + relación categoría
        $products = Product::with('category')
            ->orderByDesc('id')
            ->paginate(15);

        // ✅ NECESARIO para que el CRUD/modal de categorías en productos/index.blade.php tenga data
        $categories = Category::orderBy('name')->get(['id', 'name', 'slug']);

        return view('admin.productos.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get(['id', 'name', 'slug']);

        return view('admin.productos.create', compact('categories'));
    }

    private function generateSku(string $name): string
    {
        $prefix = 'LHW';
        $base = strtoupper(Str::slug($name, ''));
        $base = substr($base, 0, 10) ?: 'PROD';

        $sku = $prefix . '-' . $base . '-' . strtoupper(Str::random(4));

        while (Product::where('sku', $sku)->exists()) {
            $sku = $prefix . '-' . $base . '-' . strtoupper(Str::random(4));
        }

        return $sku;
    }

    private function generateUniqueSlugFrom(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value);
        $base = $base ?: 'producto';

        $slug = $base;
        $i = 2;

        $query = Product::query();
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        while ($query->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    private function imageManager(): ImageManager
    {
        if (extension_loaded('gd')) {
            return new ImageManager(new GdDriver());
        }

        if (extension_loaded('imagick')) {
            return new ImageManager(new ImagickDriver());
        }

        throw new \RuntimeException(
            'Para convertir imágenes a WebP necesitas habilitar la extensión GD o Imagick en PHP.'
        );
    }

    private function storeImageAsWebp(\Illuminate\Http\UploadedFile $file): string
    {
        $manager = $this->imageManager();
        $image = $manager->read($file->getRealPath());

        $image->scaleDown(width: 1400);

        $filename = (string) Str::uuid() . '.webp';
        $destinationPath = public_path('product');

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $image->toWebp(85)->save($destinationPath . '/' . $filename);

        return 'product/' . $filename;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:160'],
            'category_id'  => ['nullable', 'integer', 'exists:categories,id'],
            'slug'         => ['nullable', 'string', 'max:180'],
            'description'  => ['nullable', 'string'],
            'price'        => ['required', 'integer', 'min:0'],
            'stock'        => ['required', 'integer', 'min:0'],
            'stock_bodega' => ['required', 'integer', 'min:0'],
            'sku'          => ['nullable', 'string', 'max:80', 'unique:products,sku'],
            'is_active'    => ['nullable', 'boolean'],
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'ai_image_path'=> ['nullable', 'string', 'max:255'], // por si lo usas
        ]);

        $data['is_active'] = (bool)($data['is_active'] ?? false);

        // ✅ Normaliza vacío a null
        $data['category_id'] = !empty($data['category_id']) ? (int)$data['category_id'] : null;

        if (empty($data['sku'])) {
            $data['sku'] = $this->generateSku($data['name']);
        }

        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlugFrom($data['name']);
        } else {
            $data['slug'] = $this->generateUniqueSlugFrom($data['slug']);
        }

        // ✅ Prioridad: imagen manual > imagen IA (si tú guardas esa ruta en tu lógica)
        if ($request->hasFile('image')) {
            $data['image'] = $this->storeImageAsWebp($request->file('image'));
        } elseif (!empty($data['ai_image_path'])) {
            // Si tu AI guarda en /storage/... y lo sirves por ruta, ajusta aquí si corresponde
            $data['image'] = $data['ai_image_path'];
        }

        unset($data['ai_image_path']);

        Product::create($data);

        return redirect()
            ->route('admin.productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get(['id', 'name', 'slug']);
        return view('admin.productos.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:160'],
            'category_id'  => ['nullable', 'integer', 'exists:categories,id'],
            'slug'         => ['nullable', 'string', 'max:180'],
            'description'  => ['nullable', 'string'],
            'price'        => ['required', 'integer', 'min:0'],
            'stock'        => ['required', 'integer', 'min:0'],
            'stock_bodega' => ['required', 'integer', 'min:0'],
            'sku'          => ['nullable', 'string', 'max:80', 'unique:products,sku,' . $product->id],
            'is_active'    => ['nullable', 'boolean'],
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'ai_image_path'=> ['nullable', 'string', 'max:255'],
        ]);

        $data['is_active'] = (bool)($data['is_active'] ?? false);
        $data['category_id'] = !empty($data['category_id']) ? (int)$data['category_id'] : null;

        if (empty($data['sku'])) {
            $data['sku'] = $product->sku ?: $this->generateSku($data['name']);
        }

        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlugFrom($data['name'], $product->id);
        } else {
            $data['slug'] = $this->generateUniqueSlugFrom($data['slug'], $product->id);
        }

        if ($request->hasFile('image')) {
            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }
            $data['image'] = $this->storeImageAsWebp($request->file('image'));
        } elseif (!empty($data['ai_image_path'])) {
            $data['image'] = $data['ai_image_path'];
        }

        unset($data['ai_image_path']);

        $product->update($data);

        return redirect()
            ->route('admin.productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product)
    {
        if ($product->image && file_exists(public_path($product->image))) {
            unlink(public_path($product->image));
        }

        $product->delete();

        return redirect()
            ->route('admin.productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}