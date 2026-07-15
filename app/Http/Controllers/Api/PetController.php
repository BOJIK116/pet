<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pet\PetStoreRequest;
use App\Http\Requests\Pet\PetUpdateRequest;
use App\Http\Resources\PetResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class PetController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $pets = $request->user()
            ->pets()
            ->latest()
            ->paginate(15);

        return PetResource::collection($pets);
    }

    public function store(PetStoreRequest $request): JsonResponse
    {
        $pet = $request->user()
            ->pets()
            ->create($request->validated());

        return (new PetResource($pet))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Request $request, int $pet): PetResource
    {
        $pet = $request->user()
            ->pets()
            ->findOrFail($pet);

        return new PetResource($pet);
    }

    public function update(
        PetUpdateRequest $request,
        int $pet
    ): PetResource {
        $pet = $request->user()
            ->pets()
            ->findOrFail($pet);

        $pet->update($request->validated());

        return new PetResource($pet->refresh());
    }

    public function destroy(Request $request, int $pet): Response
    {
        $pet = $request->user()
            ->pets()
            ->findOrFail($pet);

        $pet->delete();

        return response()->noContent();
    }
}