<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CursoRequest;
use App\Http\Resources\CursoResource;
use App\Models\Curso;
use Illuminate\Http\JsonResponse;

class CursoController extends Controller
{
    private const RELATIONS = ['materia', 'profesor.usuario', 'periodo'];

    public function index()
    {
        return CursoResource::collection(Curso::with(self::RELATIONS)->orderBy('id_curso')->paginate(15));
    }

    public function store(CursoRequest $request): JsonResponse
    {
        $curso = Curso::create($request->validated())->load(self::RELATIONS);

        return (new CursoResource($curso))->response()->setStatusCode(201);
    }

    public function show(Curso $curso): CursoResource
    {
        return new CursoResource($curso->load(self::RELATIONS));
    }

    public function update(CursoRequest $request, Curso $curso): CursoResource
    {
        $curso->update($request->validated());

        return new CursoResource($curso->refresh()->load(self::RELATIONS));
    }

    public function destroy(Curso $curso): JsonResponse
    {
        $curso->delete();

        return response()->json(['message' => 'Curso eliminado correctamente.']);
    }
}
