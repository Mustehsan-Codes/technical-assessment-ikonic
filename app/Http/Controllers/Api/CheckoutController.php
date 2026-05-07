<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CheckoutController extends Controller
{
    #[OA\Post(
        path: "/checkout",
        tags: ["Checkout"],
        summary: "Place an order from the active cart",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["shipping_address", "payment_method"],
                properties: [
                    new OA\Property(property: "shipping_address", type: "string", example: "123 Main Street, City, State 12345"),
                    new OA\Property(property: "billing_address", type: "string", nullable: true, example: "456 Billing Ave, City, State 67890"),
                    new OA\Property(property: "payment_method", type: "string", enum: ["credit_card", "debit_card", "paypal"], example: "credit_card"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Order placed successfully",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "message", type: "string", example: "Order placed successfully"),
                    new OA\Property(property: "order", ref: "#/components/schemas/Order"),
                ])
            ),
            new OA\Response(response: 400, description: "Cart is empty"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function process(Request $request)
    {
        $user = $request->user();

        $cart = Cart::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('items.product')
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'message' => 'Cart is empty',
            ], 400);
        }

        $total = $cart->getTotal();

        $order = Order::create([
            'user_id' => $user->id,
            'total' => $total,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'shipping_address' => $request->shipping_address,
            'billing_address' => $request->billing_address,
        ]);

        foreach ($cart->items as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'product_name' => $cartItem->product->name,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->price,
            ]);
        }

        $cart->status = 'checked_out';
        $cart->save();

        $order->load('items');

        return response()->json([
            'message' => 'Order placed successfully',
            'order' => $order,
        ], 201);
    }

    #[OA\Get(
        path: "/checkout/pay/{orderId}",
        tags: ["Checkout"],
        summary: "Process payment for an order (simulated)",
        description: "Simulates a payment gateway. Has ~80% success rate. Updates order status to 'paid' on success.",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "orderId", in: "path", required: true, description: "Order ID to process payment for", schema: new OA\Schema(type: "integer", example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Payment result",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "message", type: "string", example: "Payment successful"),
                    new OA\Property(property: "order", ref: "#/components/schemas/Order"),
                ])
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Order not found"),
        ]
    )]
    public function paymentProcess(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        $paymentSuccess = rand(0, 10) > 2;

        if ($paymentSuccess) {
            $order->status = 'paid';
            $order->save();
        }

        return response()->json([
            'message' => $paymentSuccess ? 'Payment successful' : 'Payment failed',
            'order' => $order,
        ]);
    }
}
