<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class OrderController extends Controller
{
    #[OA\Get(
        path: "/orders",
        tags: ["Orders"],
        summary: "List all orders for the authenticated user",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of orders",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: "#/components/schemas/Order"))
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function index(Request $request)
    {
        $user = $request->user();
        \Log::info('Listing orders for user', ['user_id' => $user->id]);

        // FIX: Restricted order listing to the authenticated user's own orders
        $orders = Order::where('user_id', $user->id)
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    #[OA\Get(
        path: "/orders/{id}",
        tags: ["Orders"],
        summary: "Get a single order by ID",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Order ID", schema: new OA\Schema(type: "integer", example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Order detail",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "data", ref: "#/components/schemas/Order"),
                ])
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Order not found"),
        ]
    )]
    public function show(Request $request, $id)
    {
        $user = $request->user();
        \Log::info('Showing order details', ['user_id' => $user->id, 'order_id' => $id]);

        // FIX: Restricted order detail viewing to the owner of the order
        $order = Order::where('user_id', $user->id)
            ->with('items')
            ->findOrFail($id);

        return response()->json([
            'data' => $order,
        ]);
    }
}
