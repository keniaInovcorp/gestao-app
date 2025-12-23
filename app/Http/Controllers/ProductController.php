<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\VatRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     *
     * @return Response
     */
    public function index(): Response
    {
        try {
            $products = Product::with('vatRate')
                ->orderBy('reference')
                ->paginate(15);
        } catch (\Exception $e) {
            Log::error('Error loading products: ' . $e->getMessage());
            $products = new \Illuminate\Pagination\LengthAwarePaginator(
                [],
                0,
                15,
                1
            );
        }

        return Inertia::render('Configurations/Products/Index', [
            'products' => $products,
        ]);
    }

    /**
     * Show the form for creating a new product.
     *
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('Configurations/Products/Create', [
            'vatRates' => VatRate::where('status', 'active')->orderBy('percentage')->get(),
        ]);
    }

    /**
     * Store a newly created product in storage.
     *
     * @param StoreProductRequest $request
     * @return RedirectResponse
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('artigos', $filename, 'private');
            $data['photo'] = $path;
        } else {
            unset($data['photo']);
        }

        Product::create($data);

        return redirect()->route('products.index')
            ->with('success', 'Artigo criado com sucesso');
    }

    /**
     * Display the specified product.
     *
     * @param Product $product
     * @return Response
     */
    public function show(Product $product): Response
    {
        $product->load('vatRate');

        return Inertia::render('Configurations/Products/Show', [
            'product' => $product,
        ]);
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param Product $product
     * @return Response
     */
    public function edit(Product $product): Response
    {
        return Inertia::render('Configurations/Products/Edit', [
            'product' => $product->load('vatRate'),
            'vatRates' => VatRate::where('status', 'active')->orderBy('percentage')->get(),
        ]);
    }

    /**
     * Update the specified product in storage.
     *
     * @param UpdateProductRequest $request
     * @param Product $product
     * @return RedirectResponse
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($product->photo && Storage::disk('private')->exists($product->photo)) {
                Storage::disk('private')->delete($product->photo);
            }
            
            $photo = $request->file('photo');
            $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('artigos', $filename, 'private');
            $data['photo'] = $path;
        } else {
            unset($data['photo']);
        }

        $product->update($data);

        return redirect()->route('products.index')
            ->with('success', 'Artigo atualizado com sucesso');
    }

    /**
     * Remove the specified product from storage.
     *
     * @param Product $product
     * @return RedirectResponse
     */
    public function destroy(Product $product): RedirectResponse
    {
        if ($product->photo && Storage::disk('private')->exists($product->photo)) {
            Storage::disk('private')->delete($product->photo);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Artigo eliminado com sucesso');
    }

    /**
     * Serve product photo from private storage.
     *
     * @param Product $product
     * @return BinaryFileResponse
     */
    public function photo(Product $product): BinaryFileResponse
    {
        if (!$product->photo || !Storage::disk('private')->exists($product->photo)) {
            abort(404);
        }

        return response()->file(Storage::disk('private')->path($product->photo));
    }
}
