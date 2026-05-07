<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CartController extends Controller
{
    #[OA\Get(
        path: "/cart",
        tags: ["Cart"],
        summary: "Get the current user's active cart",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Cart data with items and total",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "data", type: "object", nullable: true),
                    new OA\Property(property: "items", type: "array", items: new OA\Items(ref: "#/components/schemas/CartItem")),
                    new OA\Property(property: "total", type: "number", format: "float", example: 159.98),
                ])
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function index(Request $request)
    {
        $user = $request->user();
        \Log::info('Fetching cart for user', ['user_id' => $user->id]);

        // FIX: Added 'user_id' check to prevent IDOR where users could access each other's active carts
        $cart = Cart::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('items.product')
            ->first();

        if (!$cart) {
            return response()->json([
                'data' => null,
                'items' => [],
                'total' => 0,
            ]);
        }

        return response()->json([
            'data' => $cart,
            'items' => $cart->items,
            'total' => $cart->getTotal(),
        ]);
    }

    #[OA\Post(
        path: "/cart/items",
        tags: ["Cart"],
        summary: "Add an item to the cart",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["product_id", "quantity"],
                properties: [
                    new OA\Property(property: "product_id", type: "integer", example: 1),
                    new OA\Property(property: "quantity", type: "integer", minimum: 1, example: 2),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Item added to cart",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "message", type: "string", example: "Item added to cart"),
                    new OA\Property(property: "cart", type: "object"),
                    new OA\Property(property: "total", type: "number", format: "float", example: 159.98),
                ])
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function addItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();
        $product = Product::findOrFail($request->product_id);

        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id, 'status' => 'active']
        );

        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existingItem) {
            $existingItem->quantity += $request->quantity;
            $existingItem->save();
            $item = $existingItem;
        } else {
            $item = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price,
            ]);
        }

        $cart->load('items.product');

        return response()->json([
            'message' => 'Item added to cart',
            'cart' => $cart,
            'total' => $cart->getTotal(),
        ], 201);
    }

    #[OA\Post(
        path: "/cart/items/{itemId}",
        tags: ["Cart"],
        summary: "Update cart item quantity",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "itemId", in: "path", required: true, description: "Cart item ID", schema: new OA\Schema(type: "integer", example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["quantity"],
                properties: [
                    new OA\Property(property: "quantity", type: "integer", minimum: 1, example: 3),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Cart updated",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "message", type: "string", example: "Cart updated"),
                    new OA\Property(property: "cart", type: "object"),
                    new OA\Property(property: "total", type: "number", format: "float", example: 239.97),
                ])
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Cart item not found"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function updateItem(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();
        \Log::info('Updating cart item', ['user_id' => $user->id, 'item_id' => $itemId, 'quantity' => $request->quantity]);

        // FIX: Ensure the cart item belongs to the authenticated user's cart (preventing unauthorized item modification)
        $item = CartItem::whereHas('cart', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->findOrFail($itemId);

        $item->quantity = $request->quantity;
        $item->save();

        $cart = $item->cart->load('items.product');

        return response()->json([
            'message' => 'Cart updated',
            'cart' => $cart,
            'total' => $cart->getTotal(),
        ]);
    }

    #[OA\Delete(
        path: "/cart/items/{itemId}",
        tags: ["Cart"],
        summary: "Remove an item from the cart",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "itemId", in: "path", required: true, description: "Cart item ID", schema: new OA\Schema(type: "integer", example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Item removed",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "message", type: "string", example: "Item removed from cart"),
                ])
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Cart item not found"),
        ]
    )]
    public function removeItem($itemId)
    {
        $user = auth()->user();
        \Log::info('Removing cart item', ['user_id' => $user->id, 'item_id' => $itemId]);

        // FIX: Ensure the cart item belongs to the authenticated user's cart (preventing unauthorized item deletion)
        $item = CartItem::whereHas('cart', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->findOrFail($itemId);

        $item->delete();

        return response()->json([
            'message' => 'Item removed from cart',
        ]);
    }
}
