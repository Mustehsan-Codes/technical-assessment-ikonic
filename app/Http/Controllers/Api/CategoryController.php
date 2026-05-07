<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use OpenApi\Attributes as OA;

class CategoryController extends Controller
{
    #[OA\Get(
        path: "/categories",
        tags: ["Categories"],
        summary: "List all categories",
        responses: [
            new OA\Response(
                response: 200,
                description: "Category list",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "data", type: "array", items: new OA\Items(ref: "#/components/schemas/Category")),
                ])
            ),
        ]
    )]
    public function index()
    {
        $categories = Category::all();

        return response()->json([
            'data' => $categories,
        ]);
    }

    #[OA\Get(
        path: "/categories/{id}",
        tags: ["Categories"],
        summary: "Get a single category by ID",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "Category ID", schema: new OA\Schema(type: "integer", example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Category detail",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "data", ref: "#/components/schemas/Category"),
                ])
            ),
            new OA\Response(response: 404, description: "Category not found"),
        ]
    )]
    public function show($id)
    {
        $category = Category::findOrFail($id);

        return response()->json([
            'data' => $category,
        ]);
    }
}
