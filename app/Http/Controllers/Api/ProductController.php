<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    #[OA\Get(
        path: "/products",
        tags: ["Products"],
        summary: "List all active products",
        parameters: [
            new OA\Parameter(name: "category_id", in: "query", required: false, description: "Filter by category ID", schema: new OA\Schema(type: "integer", example: 1)),
            new OA\Parameter(name: "search", in: "query", required: false, description: "Search by product name", schema: new OA\Schema(type: "string", example: "headphones")),
            new OA\Parameter(name: "page", in: "query", required: false, description: "Page number", schema: new OA\Schema(type: "integer", example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Paginated product list",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "data", type: "array", items: new OA\Items(ref: "#/components/schemas/Product")),
                    new OA\Property(property: "meta", type: "object", properties: [
                        new OA\Property(property: "current_page", type: "integer", example: 1),
                        new OA\Property(property: "last_page", type: "integer", example: 2),
                        new OA\Property(property: "per_page", type: "integer", example: 15),
                        new OA\Property(property: "total", type: "integer", example: 22),
                    ]),
                ])
            ),
        ]
    )]
    public function index(Request $request)
    {
        \Log::info('Listing products', ['category_id' => $request->category_id, 'search' => $request->search]);
        // FIX: Eager load 'category' to prevent N+1 query issues during response mapping
        $query = Product::active()->with('category');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(15);

        $result = [];
        foreach ($products as $product) {
            $result[] = [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'price' => $product->price,
                'stock' => $product->stock,
                'image_url' => $product->image_url,
                'category' => $product->category->name,
                'category_id' => $product->category_id,
            ];
        }

        return response()->json([
            'data' => $result,
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    #[OA\Get(
        path: "/products/{id}",
        tags: ["Products"],
        summary: "Get a single product by ID",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Product ID", schema: new OA\Schema(type: "integer", example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: "Product detail", content: new OA\JsonContent(ref: "#/components/schemas/ProductDetail")),
            new OA\Response(response: 404, description: "Product not found"),
        ]
    )]
    public function show($id)
    {
        $product = Product::findOrFail($id);

        return response()->json($product);
    }
}
