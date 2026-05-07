<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "ShopApp E-Commerce API",
    version: "1.0.0",
    description: "REST API for the ShopApp e-commerce platform. Provides endpoints for authentication, product browsing, cart management, checkout, and order history.",
    contact: new OA\Contact(name: "API Support", email: "support@shopapp.test")
)]
#[OA\Server(url: "http://localhost:8000/api", description: "Local Development Server")]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "Sanctum Token",
    description: "Enter your Bearer token obtained from /login or /register"
)]
#[OA\Tag(name: "Authentication", description: "User registration, login, and logout")]
#[OA\Tag(name: "Products", description: "Browse and search products")]
#[OA\Tag(name: "Categories", description: "Product categories")]
#[OA\Tag(name: "Cart", description: "Shopping cart management")]
#[OA\Tag(name: "Checkout", description: "Order placement and payment")]
#[OA\Tag(name: "Orders", description: "Order history")]

// --- Schemas ---

#[OA\Schema(
    schema: "User",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "User 1"),
        new OA\Property(property: "email", type: "string", format: "email", example: "user1@ikonicdev.com"),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time"),
    ]
)]
#[OA\Schema(
    schema: "Product",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "Wireless Bluetooth Headphones"),
        new OA\Property(property: "slug", type: "string", example: "wireless-bluetooth-headphones"),
        new OA\Property(property: "description", type: "string", example: "High quality headphones"),
        new OA\Property(property: "price", type: "string", example: "79.99"),
        new OA\Property(property: "stock", type: "integer", example: 50),
        new OA\Property(property: "image_url", type: "string", nullable: true),
        new OA\Property(property: "category", type: "string", example: "Electronics"),
        new OA\Property(property: "category_id", type: "integer", example: 1),
    ]
)]
#[OA\Schema(
    schema: "ProductDetail",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "Wireless Bluetooth Headphones"),
        new OA\Property(property: "slug", type: "string", example: "wireless-bluetooth-headphones"),
        new OA\Property(property: "description", type: "string"),
        new OA\Property(property: "price", type: "string", example: "79.99"),
        new OA\Property(property: "stock", type: "integer", example: 50),
        new OA\Property(property: "image_url", type: "string", nullable: true),
        new OA\Property(property: "category_id", type: "integer", example: 1),
        new OA\Property(property: "is_active", type: "boolean", example: true),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time"),
    ]
)]
#[OA\Schema(
    schema: "Category",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "Electronics"),
        new OA\Property(property: "slug", type: "string", example: "electronics"),
        new OA\Property(property: "description", type: "string", example: "Electronic gadgets and devices"),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time"),
    ]
)]
#[OA\Schema(
    schema: "CartItem",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "cart_id", type: "integer", example: 1),
        new OA\Property(property: "product_id", type: "integer", example: 1),
        new OA\Property(property: "quantity", type: "integer", example: 2),
        new OA\Property(property: "price", type: "string", example: "79.99"),
        new OA\Property(property: "product", ref: "#/components/schemas/ProductDetail"),
    ]
)]
#[OA\Schema(
    schema: "Order",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "user_id", type: "integer", example: 1),
        new OA\Property(property: "total", type: "string", example: "159.98"),
        new OA\Property(property: "status", type: "string", enum: ["pending", "paid", "failed", "refunded"], example: "paid"),
        new OA\Property(property: "payment_method", type: "string", example: "credit_card"),
        new OA\Property(property: "shipping_address", type: "string", example: "123 Main St"),
        new OA\Property(property: "billing_address", type: "string", nullable: true),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time"),
        new OA\Property(property: "items", type: "array", items: new OA\Items(ref: "#/components/schemas/OrderItem")),
    ]
)]
#[OA\Schema(
    schema: "OrderItem",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "order_id", type: "integer", example: 1),
        new OA\Property(property: "product_id", type: "integer", example: 1),
        new OA\Property(property: "product_name", type: "string", example: "Wireless Bluetooth Headphones"),
        new OA\Property(property: "quantity", type: "integer", example: 2),
        new OA\Property(property: "price", type: "string", example: "79.99"),
    ]
)]
abstract class Controller
{
    //
}
