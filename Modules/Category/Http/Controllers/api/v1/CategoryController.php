<?php

namespace Module\Category\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Module\Category\Http\Requests\v1\StoreRequest;
use Module\Category\Http\Requests\v1\UpdateRequest;
use Module\Category\Http\Resources\v1\CategoryCollection;
use Module\Category\Http\Resources\v1\CategoryResource;
use Module\Category\Models\Category;
use Module\Category\Repository\v1\CategoryRepository;
use Module\Category\Services\v1\CategoryService;
use Module\Share\Contracts\Response\ResponseGenerator;

class CategoryController extends Controller implements ResponseGenerator
{
    // resolve \Module\Post\Repository\v1\PostRepository
    public function repo()
    {
        return resolve(CategoryRepository::class);
    }

    // resolve \Module\Post\Services\PostService
    public function service()
    {
        return resolve(CategoryService::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Module\Category\Http\Resources\v1\CategoryCollection
     */
    public function index()
    {
        $categories = $this->repo()->take(Category::query());

        return new CategoryCollection($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Module\Category\Http\Requests\v1\StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $category = $this->service()->store($request);

        return $this->res('success', Response::HTTP_CREATED, "Successfully create category", $category);
    }

    /**
     * Display the specified resource.
     *
     * @param \Module\Category\Models\Category $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return $this->res('success', Response::HTTP_OK, null, new CategoryResource($category));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Category $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Module\Category\Http\Requests\v1\UpdateRequest $request
     * @param \Module\Category\Models\Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, Category $category)
    {
        $this->service()->update($category, $request);

        return $this->res('success', Response::HTTP_NO_CONTENT, null, null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Category $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
    }

    // manage response
    public function res($status, $code, $message, $data = null): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data ?? null
        ], $code);
    }
}
